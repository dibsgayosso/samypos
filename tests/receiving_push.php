<?php
require __DIR__.'/receiving_notifications.php';
require APPPATH.'models/Receiving_push.php';
function push_encoded($data) { return rtrim(strtr(base64_encode($data),'+/','-_'),'='); }
$service=new Receiving_push(); $service->runtime();
$keys=\Minishlink\WebPush\VAPID::createVapidKeys();
check(strlen(base64_decode(strtr($keys['publicKey'],'-_','+/')))===65,'Bundled VAPID runtime creates a valid public key');
$data=array('endpoint'=>'https://fcm.googleapis.com/push/test','keys'=>array('p256dh'=>$keys['publicKey'],'auth'=>push_encoded(random_bytes(16))));
check(Receiving_push::valid_subscription($data),'Valid browser subscription');
foreach (array('http://fcm.googleapis.com/x','https://localhost/x','https://127.0.0.1/x','https://fcm.googleapis.com.evil.test/x','https://user@fcm.googleapis.com/x','https://fcm.googleapis.com:8080/x','https://fcm.googleapis.com/x#fragment') as $url) {
 $bad=$data; $bad['endpoint']=$url; check(!Receiving_push::valid_subscription($bad),'Reject unsafe push destination');
}
foreach (array('https://web.push.apple.com/test','https://updates.push.services.mozilla.com/wpush/v2/test','https://wns2-bl2p.notify.windows.com/test') as $url) { $valid=$data; $valid['endpoint']=$url; check(Receiving_push::valid_subscription($valid),'Allow browser push service'); }
$bad=$data; $bad['keys']['auth']='bad'; check(!Receiving_push::valid_subscription($bad),'Reject invalid auth token');
$bad=$data; $bad['keys']['p256dh']=push_encoded(str_repeat('x',65)); check(!Receiving_push::valid_subscription($bad),'Reject malformed public key');
$service->Employee=new RecipientStub();
$request=array('employee_id'=>11,'location_id'=>1);
check($service->eligible($service->Employee->get_info(20),$request),'Authorized supervisor eligible without email');
check(!$service->eligible($service->Employee->get_info(11),$request),'Capturist excluded');
$service->Employee->allowed=FALSE; check(!$service->eligible($service->Employee->get_info(20),$request),'Revoked permission excluded');
$service->Employee->allowed=TRUE; $service->Employee->locations=array(2); check(!$service->eligible($service->Employee->get_info(20),$request),'Wrong branch excluded');
class PushQueueDB extends QueueDBStub {
 public $sub;
 function __construct($data) { $this->row['subscription_id']=7; $this->sub=array('id'=>7,'person_id'=>20,'enabled'=>1,'endpoint'=>$data['endpoint'],'public_key'=>$data['keys']['p256dh'],'auth_token'=>$data['keys']['auth']); }
 function get($table=NULL) { return parent::get(); }
 function get_where($table,$filter) { return new ResultStub($table==='webpush_subscriptions' ? ($this->sub['enabled']?$this->sub:array()) : $this->request); }
 function update($table,$fields) { if ($table==='webpush_subscriptions') { $this->sub=array_merge($this->sub,$fields); return TRUE; } return parent::update($table,$fields); }
}
class PushServiceSpy extends Receiving_push {
 public $success=FALSE,$expired=FALSE,$calls=0;
 function settings() { return array('id'=>1); }
 function send($sub,$payload) {
  $this->calls++; check(strpos($payload['url'],'home/receiving_request/')!==FALSE,'Push opens review only');
  return new class($this->success,$this->expired) {
   private $ok,$expired; function __construct($ok,$expired) { $this->ok=$ok; $this->expired=$expired; }
   function isSuccess() { return $this->ok; } function isSubscriptionExpired() { return $this->expired; }
  };
 }
}
function push_spy($data) { $s=new PushServiceSpy(); $s->db=new PushQueueDB($data); $s->Employee=new RecipientStub(); return $s; }
$s=push_spy($data); check($s->deliver()===0 && $s->db->row['status']==='pending' && $s->db->row['attempts']===1,'Push failure retries');
$s->success=TRUE; check($s->deliver()===1 && $s->db->row['status']==='sent','Accepted push marked sent');
$calls=$s->calls; check($s->deliver()===0 && $s->calls===$calls,'No normal duplicate send');
$s=push_spy($data); $s->expired=TRUE; check($s->deliver()===0 && !$s->db->sub['enabled'] && $s->db->row['status']==='failed','Expired device disabled');
$s=push_spy($data); $s->db->request['status']='authorized'; check($s->deliver()===0 && $s->calls===0 && $s->db->row['status']==='canceled','Stale request canceled');
$s=push_spy($data); $s->db->request['employee_id']=20; check($s->deliver()===0 && $s->calls===0,'Self approval push excluded at delivery');
echo "Web push runtime, destination validation, eligibility and queue tests passed\n";
// Exercise the shipped encryption/VAPID stack against an in-memory HTTP transport.
$history=array();
$handler=\GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler(array(new \GuzzleHttp\Psr7\Response(201))));
$handler->push(\GuzzleHttp\Middleware::history($history));
$client=new \Minishlink\WebPush\WebPush(array('VAPID'=>array('subject'=>'https://example.test/','publicKey'=>$keys['publicKey'],'privateKey'=>$keys['privateKey'])),array('TTL'=>3600),10,array('handler'=>$handler,'allow_redirects'=>FALSE),new \Psr\Log\NullLogger());
$report=$client->sendOneNotification(\Minishlink\WebPush\Subscription::create(array_merge($data,array('contentEncoding'=>'aes128gcm'))),'Test notification');
check($report->isSuccess() && count($history)===1,'Bundled encrypted send works without external network');
check($history[0]['request']->getHeaderLine('Content-Encoding')==='aes128gcm','Modern encrypted payload for Safari and browsers');
check(strpos($history[0]['request']->getHeaderLine('Authorization'),'vapid ')===0,'VAPID signed authorization header');
check((string)$history[0]['request']->getBody()!=='Test notification','Notification body is encrypted');
echo "Encrypted push and VAPID transport test passed\n";
