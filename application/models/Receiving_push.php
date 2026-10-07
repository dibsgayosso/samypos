<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Receiving_push extends CI_Model {
 public function ready() { return $this->db->table_exists('webpush_queue') && $this->db->table_exists('webpush_settings') && $this->db->table_exists('webpush_subscriptions'); }
 public function settings() { return $this->ready() ? $this->db->get_where('webpush_settings',array('id'=>1))->row_array() : array(); }
 public function runtime() {
  if (PHP_VERSION_ID<80200) throw new RuntimeException('Se requiere PHP 8.2 o posterior.');
  foreach (array('curl','mbstring','openssl','phar','zlib') as $extension) if (!extension_loaded($extension)) throw new RuntimeException('Falta la extensión PHP '.$extension.'.');
  require_once 'phar://'.APPPATH.'libraries/webpush/webpush-deps.phar/vendor/autoload.php';
 }
 public function initialize() {
  if (!$this->ready()) throw new RuntimeException('Aplica la migración de notificaciones push.');
  $subject=base_url();
  if (parse_url($subject,PHP_URL_SCHEME)!=='https') throw new RuntimeException('Configura la URL del sistema con HTTPS.');
  $this->runtime(); $keys=\Minishlink\WebPush\VAPID::createVapidKeys();
  $table=$this->db->dbprefix('webpush_settings');
  $this->db->query("INSERT IGNORE INTO `$table` (id,public_key,private_key,subject) VALUES (1,?,?,?)",array($keys['publicKey'],$keys['privateKey'],$subject));
 }
 public static function valid_subscription($data) {
  if (!is_array($data) || empty($data['endpoint']) || !is_string($data['endpoint']) || !is_array($data['keys'] ?? NULL) || strlen($data['endpoint'])>4096) return FALSE;
  $url=parse_url($data['endpoint']); $host=strtolower($url['host'] ?? '');
  $allowed=in_array($host,array('fcm.googleapis.com','updates.push.services.mozilla.com'),TRUE) || preg_match('/^[a-z0-9.-]+\.push\.apple\.com$/D',$host) || preg_match('/^[a-z0-9.-]+\.notify\.windows\.com$/D',$host);
  if (!$allowed || ($url['scheme'] ?? '')!=='https' || isset($url['user']) || isset($url['pass']) || isset($url['fragment']) || (isset($url['port']) && $url['port']!==443)) return FALSE;
  foreach (array('p256dh'=>65,'auth'=>16) as $key=>$length) {
   $encoded=$data['keys'][$key] ?? '';
   if (!is_string($encoded) || !preg_match('/^[A-Za-z0-9_-]+={0,2}$/D',$encoded)) return FALSE;
   $decoded=base64_decode(strtr($encoded,'-_','+/'),TRUE);
   if (strlen((string)$decoded)!==$length || ($key==='p256dh' && ord($decoded[0])!==4)) return FALSE;
  }
  return TRUE;
 }
 public function subscribe($person,$data) {
  if (!self::valid_subscription($data)) throw new RuntimeException('Suscripción del navegador inválida.');
  $table=$this->db->dbprefix('webpush_subscriptions');
  $this->db->query("INSERT INTO `$table` (person_id,endpoint,endpoint_hash,public_key,auth_token,enabled) VALUES (?,?,?,?,?,1) ON DUPLICATE KEY UPDATE person_id=VALUES(person_id),public_key=VALUES(public_key),auth_token=VALUES(auth_token),enabled=1",array($person,$data['endpoint'],hash('sha256',$data['endpoint']),$data['keys']['p256dh'],$data['keys']['auth']));
 }
 public function eligible($employee,$request) {
  return $employee && !$employee->deleted && !$employee->inactive && (int)$employee->person_id!==(int)$request['employee_id']
   && in_array($request['location_id'],$this->Employee->get_authenticated_location_ids($employee->person_id))
   && $this->Employee->has_module_permission('receivings',$employee->person_id)
   && $this->Employee->has_module_action_permission('receivings','authorize_receivings',$employee->person_id,$request['location_id']);
 }
 public function enqueue($id) {
  if (!$this->ready() || !$this->settings()) return 0;
  $request=$this->db->get_where('receiving_requests',array('id'=>$id,'status'=>'pending'))->row_array();
  if (!$request) return 0; $count=0;
  foreach ($this->db->get_where('webpush_subscriptions',array('enabled'=>1))->result_array() as $sub) {
   if (!$this->eligible($this->Employee->get_info($sub['person_id']),$request)) continue;
   $table=$this->db->dbprefix('webpush_queue');
   $this->db->query("INSERT IGNORE INTO `$table` (request_id,subscription_id,review_url,next_attempt_at) VALUES (?,?,?,?)",array($id,$sub['id'],site_url('home/receiving_request/'.$id),gmdate('Y-m-d H:i:s'))); $count++;
  }
  return $count;
 }
 public function summary($id) {
  if (!$this->ready() || !$this->settings()) return 'El servicio push aún no está activado.';
  $rows=$this->db->get_where('webpush_queue',array('request_id'=>$id))->result_array();
  if (!$rows) return 'Sin teléfonos activados de supervisores autorizados para esta sucursal.';
  foreach ($rows as $row) if ($row['status']==='sent') return 'Aviso aceptado por el servicio push; la entrega depende del teléfono.';
  foreach ($rows as $row) if (in_array($row['status'],array('pending','sending'))) return 'Aviso push en cola de envío.';
  return 'Avisos cancelados o no entregados; revisar permisos y suscripciones.';
 }
 public function send($sub,$payload) {
  $this->runtime(); $settings=$this->settings();
  if (!$settings) throw new RuntimeException('Servicio push sin activar.');
  $data=array('endpoint'=>$sub['endpoint'],'keys'=>array('p256dh'=>$sub['public_key'],'auth'=>$sub['auth_token']));
  if (!self::valid_subscription($data)) throw new RuntimeException('Suscripción inválida.');
  $client=new \Minishlink\WebPush\WebPush(array('VAPID'=>array('subject'=>$settings['subject'],'publicKey'=>$settings['public_key'],'privateKey'=>$settings['private_key'])),array('TTL'=>3600),10,array('allow_redirects'=>FALSE),new \Psr\Log\NullLogger());
  return $client->sendOneNotification(\Minishlink\WebPush\Subscription::create(array_merge($data,array('contentEncoding'=>'aes128gcm'))),json_encode($payload,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
 }
 public function deliver($limit=20) {
  if (!$this->ready() || !$this->settings()) return 0;
  $now=gmdate('Y-m-d H:i:s'); $sent=0;
  $this->db->where('status','sending')->where('claimed_at <',gmdate('Y-m-d H:i:s',time()-300))->update('webpush_queue',array('status'=>'pending'));
  $rows=$this->db->where('status','pending')->where('next_attempt_at <=',$now)->order_by('id')->limit(max(1,min(50,(int)$limit)))->get('webpush_queue')->result_array();
  foreach ($rows as $row) {
   $this->db->where('id',$row['id'])->where('status','pending')->update('webpush_queue',array('status'=>'sending','claimed_at'=>$now));
   if ($this->db->affected_rows()!==1) continue;
   $request=$this->db->get_where('receiving_requests',array('id'=>$row['request_id']))->row_array();
   $sub=$this->db->get_where('webpush_subscriptions',array('id'=>$row['subscription_id'],'enabled'=>1))->row_array();
   if (!$request || $request['status']!=='pending' || !$sub || !$this->eligible($this->Employee->get_info($sub['person_id']),$request)) {
    $this->db->where('id',$row['id'])->update('webpush_queue',array('status'=>'canceled')); continue;
   }
   $ok=FALSE; $expired=FALSE;
   try {
    $report=$this->send($sub,array('title'=>'SAMYPOS · Mercancía pendiente','body'=>'Hay una recepción por revisar.','url'=>$row['review_url'],'tag'=>'receiving-'.$request['id']));
    $ok=$report->isSuccess(); $expired=$report->isSubscriptionExpired();
   } catch (Throwable $error) { log_message('error','Web push delivery failed; check runtime and configuration.'); }
   if ($expired) $this->db->where('id',$sub['id'])->update('webpush_subscriptions',array('enabled'=>0));
   $attempts=(int)$row['attempts']+1;
   $this->db->where('id',$row['id'])->update('webpush_queue',array('status'=>$ok?'sent':(($expired || $attempts>=10)?'failed':'pending'),'attempts'=>$attempts,'sent_at'=>$ok?gmdate('Y-m-d H:i:s'):NULL,'last_error'=>$ok?NULL:($expired?'Suscripción vencida; reactivar teléfono.':'No se aceptó el push; revisar configuración.'),'next_attempt_at'=>gmdate('Y-m-d H:i:s',time()+min(3600,60*pow(2,min(6,$attempts-1))))));
   if ($ok) $sent++;
  }
  return $sent;
 }
}
