<?php
define('BASEPATH',__DIR__); class CI_Model {} class PHPPOSCartSale {}
function lang($key){return $key;}
require dirname(__DIR__).'/application/models/Supervisor_dashboard.php';
$source=file_get_contents(dirname(__DIR__).'/application/controllers/Home.php');
$start=strpos($source,'        private function get_today_payment_breakdown');
$end=strpos($source,'private function get_today_location_sales',$start);
$method=substr($source,$start,$end-$start);
eval('class BreakdownFixture { public $db,$Sale; public function run($merchandise){return $this->get_today_payment_breakdown(1,$merchandise);} '.$method.'}');
$f=new BreakdownFixture();
$f->db=new class {
 public $filters=array();
 function select($v){$this->filters=array();return $this;}function from($v){return $this;}
 function where($k,$v){$this->filters[$k]=$v;return $this;}
 function get(){ $rows=array(array('sale_id'=>1,'total'=>100));if(!isset($this->filters['store_account_payment']))$rows[]=array('sale_id'=>2,'total'=>250);
 return new class($rows){public $rows;function __construct($r){$this->rows=$r;}function result_array(){return $this->rows;}};}
};
$f->Sale=new class {
 function get_payment_options($cart){return array('Transferencia','Efectivo');}
 function _get_all_sale_payments($ids,$ordered){$r=array();foreach($ids as $id)$r[$id]=array(array('payment_type'=>'Transferencia','payment_amount'=>$id===1?100:200),array('payment_type'=>'Efectivo','payment_amount'=>$id===1?0:50));return $r;}
 function get_payment_data_grouped_by_sale($p,$totals){return $p;}
};
$transfers=Supervisor_dashboard::transfer_payments($f->run(FALSE));
if($transfers['total']!==300.0 || $transfers['operations']!==2)throw new Exception('Credit transfer missing or cash included');
$owner=Supervisor_dashboard::transfer_payments($f->run(TRUE));
if($owner['total']!==100.0)throw new Exception('Merchandise-only totals changed');
$detail=substr($source,strpos($source,'public function supervisor_transfers'),strpos($source,'public function my_receiving_requests')-strpos($source,'public function supervisor_transfers'));
if(strpos($detail,"where('store_account_payment',0)")!==FALSE || strpos($source,'get_today_payment_breakdown($id,FALSE)')===FALSE)throw new Exception('Supervisor still excludes credit payments');
echo "Credit transfer inclusion and merchandise-only regression checks passed\n";
