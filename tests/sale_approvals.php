<?php
error_reporting(E_ALL & ~E_DEPRECATED);
define('BASEPATH',__DIR__);
define('APPPATH',dirname(__DIR__).'/application/');
class CI_Model {}
class MY_Model extends CI_Model {}
function check($condition,$message) { if (!$condition) throw new RuntimeException($message); }
function log_message($level,$message) {}
function get_all_language_values_for_key($key,$file) { throw new RuntimeException('Approval checks must never switch the active application language'); }
require APPPATH.'models/Sale_approvals.php';
require APPPATH.'models/Sale.php';
class ConfigStub { public $value='["Transferencia","common_cash","common_giftcard"]'; function item($key) { return $this->value; } }
class EmployeeStub {
    public $allowed=TRUE,$location=1,$person=10;
    function has_module_permission($m,$p) { return $this->allowed; }
    function has_module_action_permission($m,$a,$p) { return $this->allowed; }
    function get_logged_in_employee_info() { return (object)array('person_id'=>$this->person); }
    function get_logged_in_employee_current_location_id() { return $this->location; }
    function get_logged_in_employee_current_register_id() { return 3; }
}
class CartStub {
    public $payments=array(),$sale_id=NULL,$employee_id,$location_id,$register_id,$suspended,$cart_id='sale';
    function get_payments() { return $this->payments; }
    function get_items() { return array((object)array()); }
    function get_mode() { return 'sale'; }
}
$service=new Sale_approvals();$service->config=new ConfigStub();$service->Employee=new EmployeeStub();
$cart=new CartStub();
$payment=function($method,$amount) { return (object)array('payment_type'=>$method,'payment_amount'=>$amount); };
$cart->payments=array($payment('Tarjeta',10)); check(!$service->required($cart),'Unselected method must complete normally');
$cart->payments[]= $payment('Transferencia',10); check($service->required($cart),'Mixed payment must require approval');
$cart->payments=array($payment('Transferencia',0)); check(!$service->required($cart),'Zero payment must not trigger approval');
$cart->payments=array($payment('Efectivo',-10));check($service->required($cart),'Translated standard method and return must require approval');
$cart->payments=array($payment('Gift Card:123',10));check($service->required($cart),'Gift card identifier must match method');
$service->config->value='[]';check(!$service->required($cart),'Empty setting must preserve current behavior');
$service->config->value='["Transferencia"]'; $cart->payments=array($payment('Transferencia',10));
class ResultStub { private $row; function __construct($r){$this->row=$r;} function row_array(){return $this->row;} }
class DBStub {
    public $row=array('id'=>1,'employee_id'=>11,'location_id'=>1,'status'=>'pending'),$stock=0,$writes=0,$begin=0,$commits=0,$healthy=TRUE;
    private $backup;
    function table_exists($t){return TRUE;}
    function dbprefix($t){return 'phppos_'.$t;}
    function trans_begin(){$this->begin++;$this->backup=array($this->row,$this->stock);}
    function trans_rollback(){list($this->row,$this->stock)=$this->backup;}
    function trans_commit(){$this->commits++;}
    function trans_status(){return $this->healthy;}
    function query($sql,$args){check(strpos($sql,'FOR UPDATE')!==FALSE,'Must lock request');return new ResultStub($this->row['status']==='pending' && $this->row['id']==$args[0] && $this->row['location_id']==$args[1] ? $this->row : array());}
    function where($f,$v){return $this;}
    function update($t,$data){$this->writes++;$this->row=array_merge($this->row,$data);return TRUE;}
}
class ApplyStub { public $db,$fail=FALSE,$throws=FALSE,$calls=0; function apply_sale_request($row){$this->calls++;$this->db->stock+=5;if($this->throws)throw new RuntimeException('Failure');return $this->fail ? -1 : 123;} }
$service->db=new DBStub();$service->Sale=new ApplyStub();$service->Sale->db=$service->db;
$service->load=new class {function model($name){}};
$service->Employee->allowed=FALSE;check(!$service->decide(1,10,FALSE) && !$service->db->begin,'No permission must do no writes');
$service->Employee->allowed=TRUE;check(!$service->decide(1,12,FALSE) && !$service->db->begin,'Cannot impersonate supervisor');
$service->Employee->location=2;check(!$service->decide(1,10,FALSE) && !$service->Sale->calls,'Cross branch denied');$service->Employee->location=1;
$service->db->row['employee_id']=10;check(!$service->decide(1,10,FALSE),'No self approval');check(!$service->decide(1,10,TRUE,'reason'),'No self rejection');$service->db->row['employee_id']=11;
check(!$service->decide(1,10,TRUE,''),'Rejection requires reason');
$service->Sale->fail=TRUE;check(!$service->decide(1,10,FALSE) && !$service->db->stock && $service->db->row['status']==='pending','Failed sale must rollback');
$service->Sale->fail=FALSE;$service->Sale->throws=TRUE;check(!$service->decide(1,10,FALSE) && !$service->db->stock,'Thrown failure must rollback');$service->Sale->throws=FALSE;
check($service->decide(1,10,FALSE),'Supervisor must authorize');check($service->db->stock===5 && $service->db->row['sale_id']===123 && $service->db->row['authorized_by']===10,'Approval applies and audits sale');
$calls=$service->Sale->calls;check(!$service->decide(1,10,FALSE) && $calls===$service->Sale->calls,'Repeated approval must not duplicate sale');
$service->db=new DBStub();$service->Sale->db=$service->db;
check($service->decide(1,10,TRUE,'No llegó el pago') && $service->db->stock===0 && $service->db->row['status']==='rejected','Rejection must not apply sale');
$sale=(new ReflectionClass('Sale'))->newInstanceWithoutConstructor();$sale->load=$service->load;$sale->Sale_approvals=$service;
check($sale->save($cart,FALSE)===-1,'Direct API/model save cannot bypass policy');
$cart->suspended=1;check($sale->save($cart,FALSE)===-1,'Suspended flag cannot bypass policy');
check($sale->save_sale_data(array())===FALSE,'Raw import cannot bypass policy');
echo "Sale approval regression tests passed\n";
class StageDBStub {
    public $writes=array();
    function table_exists($table){return TRUE;}
    function get_where($table,$filters){return new ResultStub(array());}
    function insert($table,$data){$this->writes[]=array($table,$data);return TRUE;}
    function insert_id(){return 77;}
}
class LineStub {public $name='Producto',$quantity=2;function get_total(){return 10;}}
class StageCartStub extends CartStub {
    public $customer_id=12;
    function get_items(){return array(new LineStub());}
    function get_total(){return 10;}
    function get_amount_due(){return 0;}
}
$service->session=new class {function userdata($key){return NULL;}};
$service->db=new StageDBStub();$stageCart=new StageCartStub();$stageCart->payments=array($payment('Transferencia',10));
check($service->stage($stageCart,'submission')===77,'New sale must enter approval queue');
check(count($service->db->writes)===1 && $service->db->writes[0][0]==='sale_requests','Staging must write only the queue');
$queued=$service->db->writes[0][1];$payload=unserialize(base64_decode($queued['cart_payload']));
check($queued['employee_id']===10 && $queued['location_id']===1 && $payload->register_id===3 && $payload->cart_id===NULL,'Submission must bind actual branch, cashier and register without overwriting supervisor cart');
check($stageCart->cart_id==='sale' && $stageCart->employee_id===NULL,'Submission must not mutate original cart');
$stageCart->sale_id=100;check(!$service->stage($stageCart,'other') && count($service->db->writes)===1,'Saved sale cannot be applied as a duplicate');
echo "Sale staging isolation tests passed\n";
