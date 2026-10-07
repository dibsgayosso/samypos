<?php
// Service regression tests without loading an application/server database.
error_reporting(E_ALL & ~E_DEPRECATED);
define('BASEPATH', __DIR__);
define('APPPATH', dirname(__DIR__).'/application/');
class CI_Model {}
class MY_Model extends CI_Model {}
function check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
function log_message($level,$message) {}
require APPPATH.'models/Receiving.php';
require APPPATH.'models/Supervisor_dashboard.php';
class EmployeeStub {
    public $allowed=TRUE;
    function has_module_action_permission($module,$action,$employee) { return $this->allowed; }
    function get_logged_in_employee_current_location_id() { return 1; }
    function get_logged_in_employee_info() { return (object)array('person_id'=>10); }
}
class CartStub {
    public $mode='receive', $suspended=0, $receiving_id=NULL, $supervisor_applying=TRUE;
    public $items=array();
    function get_mode() { return $this->mode; }
    function get_items() { return $this->items; }
}
$receiving=(new ReflectionClass('Receiving'))->newInstanceWithoutConstructor();
$receiving->Employee=new EmployeeStub();
$receiving->Item=new class { function get_store_account_item_id() { return 999; } };
$cart=new CartStub();
check($receiving->save($cart,FALSE)===-1,'Direct completed receipt must be denied, even with an injected approval property');
$cart->suspended=1; $cart->items=array((object)array('quantity_received'=>5,'item_id'=>1));
check($receiving->save($cart,FALSE)===-1,'Partial draft must not apply inventory');
$cart->mode='store_account_payment';
check($receiving->save($cart,FALSE)===-1,'Mode spoofing must not authorize merchandise');
$receiving->Employee->allowed=FALSE;
check($receiving->apply_supervisor_request(array('location_id'=>1))===-1,'Employee cannot invoke the application service');
$receiving->Employee->allowed=TRUE;
check($receiving->apply_supervisor_request(array('location_id'=>2))===-1,'Application must remain scoped to the authorized branch');

class ResultStub {
    private $row;
    function __construct($row) { $this->row=$row; }
    function row_array() { return $this->row; }
}
class DBStub {
    public $row=array('id'=>1,'location_id'=>1,'status'=>'pending','supervisor_revision'=>1,'employee_id'=>11);
    public $stock=0, $calls=0, $begin=0, $commits=0, $rollbacks=0, $healthy=TRUE;
    private $backup;
    function table_exists($table) { return TRUE; }
    function dbprefix($table) { return 'phppos_'.$table; }
    function trans_begin() { $this->begin++; $this->backup=array($this->row,$this->stock); }
    function trans_rollback() { $this->rollbacks++; list($this->row,$this->stock)=$this->backup; }
    function trans_commit() { $this->commits++; }
    function trans_status() { return $this->healthy; }
    function query($sql,$params) {
        check(strpos($sql,'FOR UPDATE')!==FALSE,'Approval must lock the request');
        return new ResultStub($params[0]==$this->row['id'] && $params[1]==$this->row['location_id']
            && $params[2]==$this->row['supervisor_revision'] && $this->row['status']==='pending' ? $this->row : array());
    }
    function where($field,$value) { return $this; }
    function update($table,$fields) { $this->row=array_merge($this->row,$fields); return TRUE; }
}
class ApplyStub {
    public $db, $fail=FALSE;
    function apply_supervisor_request($request) { $this->db->calls++; $this->db->stock+=5; return $this->fail ? -1 : 123; }
}
$service=new Supervisor_dashboard(); $service->Employee=new EmployeeStub(); $service->db=new DBStub();
$service->Receiving=new ApplyStub(); $service->Receiving->db=$service->db;
$service->load=new class { function model($name) {} };
$service->Employee->allowed=FALSE;
check(!$service->authorize(1,1,1,10) && $service->db->begin===0,'Unauthorized approval must do no writes');
$service->Employee->allowed=TRUE;
check(!$service->authorize(1,1,2,10) && $service->db->begin===0,'Cross-branch approval must do no writes');
check(!$service->authorize(1,2,1,10) && $service->db->calls===0,'Stale revision must not apply merchandise');
$service->db->row['employee_id']=10;
check(!$service->authorize(1,1,1,10) && $service->db->calls===0,'The requesting employee must never approve their own receipt');
$service->db->row['employee_id']=11;
$service->Receiving->fail=TRUE;
check(!$service->authorize(1,1,1,10) && $service->db->stock===0 && $service->db->row['status']==='pending','Failure must roll back application and keep the request pending');
$service->Receiving->fail=FALSE;
check($service->authorize(1,1,1,10),'Valid approval must apply');
check($service->db->stock===5 && $service->db->row['receiving_id']===123 && $service->db->row['authorized_by']===10,'Approval must link the receipt and audit its supervisor');
$calls=$service->db->calls;
check(!$service->authorize(1,1,1,10) && $service->db->calls===$calls && $service->db->stock===5,'A repeated approval must never apply inventory twice');
class StageDBStub {
    public $writes=array();
    function table_exists($table) { return TRUE; }
    function get_where($table,$where) { return new ResultStub(array()); }
    function insert($table,$fields) { $this->writes[]=array($table,$fields); return TRUE; }
    function insert_id() { return 7; }
}
class StageCartStub extends CartStub {
    public $supplier_id=2;
    function get_total() { return 50; }
}
$stage=new Supervisor_dashboard(); $stage->db=new StageDBStub(); $stage->Employee=new EmployeeStub();
$cart=new StageCartStub();
class StageLineStub {
    public $name='Producto', $quantity=5, $unit_price=10, $quantity_unit_quantity=1, $serialnumber='', $expire_date=NULL;
    function get_total() { return 50; }
}
$cart->items=array(new StageLineStub());
check($stage->stage($cart,'unique-key')===7,'Submission must create a pending request');
check(count($stage->db->writes)===1 && $stage->db->writes[0][0]==='receiving_requests','Submitting merchandise must write only the pending queue, never inventory or supplier balances');
check($stage->db->writes[0][1]['employee_id']===10 && $stage->db->writes[0][1]['location_id']===1,'Pending request must bind the actual employee and branch');
$policy=new Supervisor_dashboard();
$approved=array('status'=>'authorized','employee_id'=>11,'location_id'=>1,'receiving_id'=>123);
check($policy->can_print_request($approved,11,1,FALSE),'Requester must be able to print after approval');
check(!$policy->can_print_request(array_merge($approved,array('status'=>'pending')),11,1,TRUE),'Even a supervisor cannot print a pending request');
check(!$policy->can_print_request(array_merge($approved,array('status'=>'rejected')),11,1,TRUE),'Rejected merchandise must never have an authorized receipt');
check(!$policy->can_print_request($approved,11,2,TRUE),'Receipt cannot be printed through a different branch');
check(!$policy->can_print_request($approved,12,1,FALSE),'Another employee cannot print a receipt owned by someone else');
check($policy->can_print_request($approved,12,1,TRUE),'Supervisor may print an authorized receipt in their branch');
echo "Supervisor authorization regression tests passed\n";
