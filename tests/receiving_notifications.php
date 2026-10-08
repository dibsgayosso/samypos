<?php
require __DIR__.'/supervisor_receiving.php';
require APPPATH.'models/Receiving_notifications.php';
function html_escape($text) { return htmlspecialchars((string)$text,ENT_QUOTES,'UTF-8'); }
function to_currency($amount) { return '$'.number_format($amount,2); }
class RecipientStub extends EmployeeStub {
    public $locations=array(1), $recipient_id=20;
    function get_info($id) { return (object)array('person_id'=>$id,'email'=>'supervisor@example.test','deleted'=>0,'inactive'=>0,'first_name'=>'Supervisor','last_name'=>'Prueba'); }
    function get_authenticated_location_ids($id) { return $this->locations; }
    function has_module_permission($module,$id) { return $this->allowed; }
}
class QueueDBStub {
    public $row=array('id'=>1,'request_id'=>8,'recipient_id'=>20,'review_url'=>'https://example.test/index.php/home/receiving_request/8','status'=>'pending','attempts'=>0);
    public $request=array('id'=>8,'employee_id'=>11,'location_id'=>1,'status'=>'pending','total'=>50);
    private $filters=array(); public $affected=0;
    function table_exists($table) { return TRUE; }
    function where($field,$value) { $this->filters[$field]=$value; return $this; }
    function from($table) { return $this; }
    function order_by($field) { return $this; }
    function limit($count) { return $this; }
    function update($table,$fields) {
        $ok=TRUE;
        foreach ($this->filters as $field=>$value) if (strpos($field,'<')===FALSE && isset($this->row[$field]) && $this->row[$field]!=$value) $ok=FALSE;
        $this->filters=array(); $this->affected=$ok ? 1 : 0;
        if ($ok) $this->row=array_merge($this->row,$fields);
        return TRUE;
    }
    function affected_rows() { return $this->affected; }
    function get() { $pending=$this->row['status']==='pending'; $this->filters=array(); return new class($pending ? array($this->row) : array()) {
        private $rows; function __construct($rows) { $this->rows=$rows; } function result_array() { return $this->rows; }
    }; }
    function get_where($table,$filter) { return new ResultStub($this->request); }
}
class EmailSpy {
    public $success=FALSE,$calls=0,$recipient='';
    function clear($attachments) {}
    function initialize($options) {}
    function from($email,$name) {}
    function to($email) { $this->recipient=$email; }
    function subject($subject) {}
    function message($body) { check(strpos($body,'El enlace no autoriza automáticamente')!==FALSE,'Email must direct to authenticated review, not approval'); }
    function send() { $this->calls++; return $this->success; }
}
function notification_service() {
    $service=new Receiving_notifications(); $service->db=new QueueDBStub(); $service->Employee=new RecipientStub(); $service->email=new EmailSpy();
    $service->load=new class { function library($name) {} };
    $service->config=new class { function item($name) { return $name==='smtp_user' ? 'noreply@example.test' : 'SAMYPOS'; } };
    $service->Location=new class { function get_info($id) { return (object)array('name'=>'Sucursal de prueba'); } };
    return $service;
}
$service=notification_service();
check($service->deliver(1)===0 && $service->db->row['status']==='pending' && $service->db->row['attempts']===1,'SMTP failure must queue a retry');
check(strtotime($service->db->row['next_attempt_at'].' UTC')>time(),'Retry must use a future UTC timestamp');
$service->email->success=TRUE;
check($service->deliver(1)===1 && $service->db->row['status']==='sent','Successful send must mark the queue sent');
check($service->email->recipient==='supervisor@example.test','Delivery must use the current authorized supervisor email');
$calls=$service->email->calls;
check($service->deliver(1)===0 && $service->email->calls===$calls,'Sent notification must not be redelivered by the normal worker');
$service=notification_service(); $service->db->request['employee_id']=20;
check($service->deliver(1)===0 && $service->email->calls===0 && $service->db->row['status']==='canceled','Requester must not receive their own supervisor approval notification');
$service=notification_service(); $service->Employee->allowed=FALSE;
check($service->deliver(1)===0 && $service->email->calls===0,'Revoked supervisor permission must prevent email delivery');
$service=notification_service(); $service->Employee->locations=array(2);
check($service->deliver(1)===0 && $service->email->calls===0,'Another branch must not receive the notification');
$service=notification_service(); $service->db->request['status']='authorized';
check($service->deliver(1)===0 && $service->email->calls===0,'Already-decided request must cancel a stale email');
echo "Receiving notification eligibility and retry tests passed\n";
