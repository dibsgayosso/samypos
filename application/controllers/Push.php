<?php
require_once 'Secure_area.php';
class Push extends Secure_area {
 public function __construct() { parent::__construct(); $this->load->model('Receiving_push'); }
 private function admin() { return $this->Employee->has_module_permission('config',$this->session->userdata('person_id')); }
 private function supervisor() {
  $person=$this->session->userdata('person_id');
  if (!$this->Employee->has_module_permission('receivings',$person)) return FALSE;
  foreach ($this->Employee->get_authenticated_location_ids($person) as $location) if ($this->Employee->has_module_action_permission('receivings','authorize_receivings',$person,$location)) return TRUE;
  return FALSE;
 }
 private function reply($data,$code=200) { $this->output->set_status_header($code)->set_header('Cache-Control: no-store')->set_content_type('application/json')->set_output(json_encode($data)); }
 public function config() {
  if (!$this->supervisor() && !$this->admin()) { $this->reply(array('error'=>'Sin permiso.'),403); return; }
  $token=$this->session->userdata('push_csrf');
  if (!$token) { $token=bin2hex(random_bytes(32)); $this->session->set_userdata('push_csrf',$token); }
  $settings=$this->Receiving_push->settings();
  $this->reply(array('publicKey'=>$settings['public_key'] ?? NULL,'csrf'=>$token,'admin'=>$this->admin(),'supervisor'=>$this->supervisor()));
 }
 public function action($action='') {
  $data=json_decode($this->input->raw_input_stream,TRUE); $token=$this->session->userdata('push_csrf');
  if ($this->input->method()!=='post' || !$token || !is_array($data) || !is_string($data['csrf'] ?? NULL) || !hash_equals($token,$data['csrf'])) { $this->reply(array('error'=>'Sesión vencida. Recarga la página.'),403); return; }
  try {
   if ($action==='setup') {
    if (!$this->admin()) { $this->reply(array('error'=>'Solo administración puede activar el servicio.'),403); return; }
    $this->Receiving_push->initialize();
   } else {
    if (!$this->supervisor() || !$this->Receiving_push->ready() || !$this->Receiving_push->settings()) { $this->reply(array('error'=>'Servicio sin activar o sin permiso de supervisor.'),403); return; }
    $person=$this->session->userdata('person_id'); $subscription=$data['subscription'] ?? array();
    if ($action==='subscribe') $this->Receiving_push->subscribe($person,$subscription);
    elseif (in_array($action,array('unsubscribe','test'),TRUE)) {
     $endpoint=$subscription['endpoint'] ?? '';
     if (!is_string($endpoint)) throw new RuntimeException('Suscripción inválida.');
     $sub=$this->db->get_where('webpush_subscriptions',array('endpoint_hash'=>hash('sha256',$endpoint),'person_id'=>$person,'enabled'=>1))->row_array();
     if (!$sub) throw new RuntimeException('Activa este teléfono primero.');
     if ($action==='unsubscribe') $this->db->where('id',$sub['id'])->update('webpush_subscriptions',array('enabled'=>0));
     else {
      $report=$this->Receiving_push->send($sub,array('title'=>'SAMYPOS','body'=>'Los avisos de supervisión están activados.','url'=>site_url('home'),'tag'=>'samypos-test'));
      if (!$report->isSuccess()) throw new RuntimeException('El servicio no aceptó el aviso. Reactiva este teléfono.');
     }
    } else throw new RuntimeException('Acción inválida.');
   }
   $this->reply(array('ok'=>TRUE));
  } catch (Throwable $error) { $this->reply(array('error'=>$error instanceof RuntimeException?$error->getMessage():'No se pudo completar la operación. Revisa la configuración.'),400); }
 }
}
