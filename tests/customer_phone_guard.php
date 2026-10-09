<?php
$source=file_get_contents(dirname(__DIR__).'/application/controllers/Sales.php');
$start=strpos($source,'    private function customer_phone_missing()');
$end=strpos($source,"\tfunction select_customer()",$start);
$methods=substr($source,$start,$end-$start);
eval('class PhoneGuardFixture { public $cart,$Customer,$reloads=array(); public function _reload($data,$ajax){$this->reloads[]=array($data,$ajax);} public function check($ajax=TRUE){return $this->require_customer_phone($ajax);} '.$methods.'}');
$f=new PhoneGuardFixture();$f->cart=(object)array('customer_id'=>10);
$f->Customer=new class { public $phone=NULL,$calls=0; function get_info($id,$cache){if($cache)throw new Exception('Cached profile');$this->calls++;return (object)array('phone_number'=>$this->phone);} };
foreach(array(NULL,'','   ') as $phone){$f->Customer->phone=$phone;if($f->check()!==FALSE)throw new Exception('Missing phone passed');}
if(!$f->reloads[0][0]['customer_phone_required'])throw new Exception('Missing popup flag');
$f->Customer->phone='55 1234 5678';if(!$f->check(FALSE))throw new Exception('Updated phone still blocked');
$f->cart->customer_id=NULL;if(!$f->check())throw new Exception('Anonymous sale blocked');
foreach(array("function add_payment()","function complete()") as $method){$section=substr($source,strpos($source,$method),150);if(strpos($section,'require_customer_phone')===FALSE)throw new Exception('Missing server guard');}
echo "Customer phone guard tests passed\n";
