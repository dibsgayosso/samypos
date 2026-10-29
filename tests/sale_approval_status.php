<?php
error_reporting(E_ALL & ~E_DEPRECATED);
define('BASEPATH',__DIR__);define('APPPATH',dirname(__DIR__).'/application/');
class CI_Model {}
function log_message($a,$b){}
function check($ok,$message){if(!$ok)throw new RuntimeException($message);}
function html_escape($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function to_currency($v){return '$'.number_format($v,2);}
function site_url($v){return '/'.$v;}
require APPPATH.'models/Sale_approvals.php';
require dirname(__DIR__).'/system/database/DB_driver.php';
require dirname(__DIR__).'/system/database/DB_query_builder.php';
class StatusDB extends CI_DB_query_builder {
    public $sql=array();protected $_escape_char='`';
    protected function _escape_str($v){return str_replace("'","''",$v);}
    function table_exists($t){return TRUE;}
    function get($table='',$limit=NULL,$offset=NULL){$this->sql[]=$this->get_compiled_select();return new class {function result_array(){return array();}};}
}
$db=new StatusDB(array('dbprefix'=>'phppos_'));
$employee=new class {public $db,$authorized=FALSE;
    function has_module_permission($m,$p){$this->db->select('module_id')->from('permissions')->get();return TRUE;}
    function has_module_action_permission($m,$a,$p){$this->db->select('action_id')->from('permissions_actions')->get();return $this->authorized;}
    function get_logged_in_employee_info(){return (object)array('person_id'=>10);}
    function get_logged_in_employee_current_location_id(){return 2;}
};$employee->db=$db;
$model=new Sale_approvals();$model->db=$db;$model->Employee=$employee;
foreach(array(FALSE,TRUE) as $authorized){
    $employee->authorized=$authorized;$model->listing();$sql=end($db->sql);
    check(strpos($sql,'FROM `phppos_sale_requests`')!==FALSE,'Permission queries must not erase the requests table');
    check(strpos($sql,'`location_id` = 2')!==FALSE,'Listing must stay scoped to current branch');
    check((strpos($sql,'`employee_id` = 10')!==FALSE)===!$authorized,'Cashier must see only their own requests');
    check(strpos($sql,'authorized_at')!==FALSE,'Approval date must be available');
}
class ViewLoader {function view($v){}}
class ViewFixture {
    public $load;
    function render($requests){ob_start();include APPPATH.'views/sale_approvals.php';return ob_get_clean();}
}
$view=new ViewFixture();$view->load=new ViewLoader();
$base=array('id'=>7,'employee_id'=>10,'sale_time'=>'2026-10-28 22:00:00','total'=>300,'sale_id'=>123,'rejection_reason'=>'<bad>','authorized_at'=>'2026-10-28 22:05:00');
foreach(array('pending','authorized','rejected') as $status){
    $html=$view->render(array(array_merge($base,array('status'=>$status))));
    check((strpos($html,'Imprimir venta')!==FALSE)===($status==='authorized'),'Only approved sales may have a print action');
    if($status==='rejected')check(strpos($html,'&lt;bad&gt;')!==FALSE,'Rejection text must be escaped');
}
check(strpos($view->render(array()),'No hay solicitudes')!==FALSE,'Empty state must render a page');
$source=file_get_contents(APPPATH.'controllers/Sales.php');
$start=strpos($source,'public function approval_receipt($id)');$end=strpos($source,'public function approval_request($id)',$start);
function show_error($message,$code){throw new RuntimeException('Denied', $code);}
eval('class PrintFixture {public $load,$Sale_approvals,$Sale,$Employee,$printed=NULL; function receipt($id){$this->printed=$id;} '.substr($source,$start,$end-$start).'}');
$f=new PrintFixture();$f->load=new class {function model($n){}};
$f->Employee=$employee;$f->Sale_approvals=new class {public $row;function detail($id){return $this->row;}};
$f->Sale=new class {public $row=array('deleted'=>0,'suspended'=>0,'location_id'=>2);function get_info($id){return new class($this->row){private $row;function __construct($r){$this->row=$r;}function row_array(){return $this->row;}};}};
foreach(array(FALSE,array('status'=>'pending','sale_id'=>123),array('status'=>'rejected','sale_id'=>123),array('status'=>'authorized','sale_id'=>0)) as $row){
    $f->Sale_approvals->row=$row;try{$f->approval_receipt(7);throw new Exception('Unapproved print allowed');}catch(RuntimeException $e){check($e->getCode()===403,'Unapproved receipt must be denied');}
}
$f->Sale_approvals->row=array('status'=>'authorized','sale_id'=>123);$f->approval_receipt(7);check($f->printed===123,'Approved receipt must open native sale print');
$f->Sale->row['location_id']=3;try{$f->approval_receipt(7);throw new Exception('Cross branch print allowed');}catch(RuntimeException $e){check($e->getCode()===403,'Cross branch print denied');}
echo "Sale status SQL, permissions, rendering and approved-only printing passed\n";
