<?php
function lang($key){return $key==='common_store_account'?'Línea de crédito':$key;}
function get_all_language_values_for_key($key,$module){return array('Línea de crédito','Store Account');}
$source=file_get_contents(dirname(__DIR__).'/application/controllers/Sales.php');
$start=strpos($source,'    private function customer_phone_missing()');
$end=strpos($source,"\tfunction select_customer()",$start);
$methods=substr($source,$start,$end-$start);
eval('class PhoneGuardFixture { public $cart,$Customer,$reloads=array(); public function _reload($data,$ajax){$this->reloads[]=array($data,$ajax);} public function check($ajax=TRUE,$type=NULL,$amount=0){return $this->require_customer_phone($ajax,$type,$amount);} '.$methods.'}');
$f=new PhoneGuardFixture();
$f->cart=new class {public $customer_id=10,$mode='sale',$payments=array();function get_mode(){return $this->mode;}function get_payment_amount($label){return isset($this->payments[$label])?$this->payments[$label]:0;}};
$f->Customer=new class {public $phone=NULL,$calls=0;function get_info($id,$cache){if($cache)throw new Exception('Cached profile');$this->calls++;return (object)array('phone_number'=>$this->phone);}};
foreach(array(NULL,'','   ') as $phone){$f->Customer->phone=$phone;if($f->check(TRUE,'Línea de crédito',100)!==FALSE)throw new Exception('Missing phone passed credit payment');}
if(!$f->reloads[0][0]['customer_phone_required'])throw new Exception('Missing popup flag');
foreach(array('Efectivo','Transferencia','Tarjeta de crédito') as $type)if(!$f->check(TRUE,$type,100))throw new Exception('Non-credit payment blocked');
$f->cart->payments=array('Store Account'=>50,'Efectivo'=>50);
if($f->check(FALSE)!==FALSE || end($f->reloads)[1]!==FALSE)throw new Exception('Mixed credit completion escaped guard');
$f->Customer->phone='55 1234 5678';if(!$f->check(FALSE))throw new Exception('Updated phone still blocked');
$f->Customer->phone=NULL;$f->cart->mode='store_account_payment';if(!$f->check())throw new Exception('Credit installment blocked');
$f->cart->mode='return';$f->cart->payments=array('Store Account'=>-100);if(!$f->check())throw new Exception('Credit refund blocked');
$f->cart->mode='sale';$f->cart->payments=array();$f->cart->customer_id=NULL;if(!$f->check())throw new Exception('Anonymous cash sale blocked');
foreach(array('function add_payment()','function complete()') as $method){$section=substr($source,strpos($source,$method),260);if(strpos($section,'require_customer_phone')===FALSE)throw new Exception('Missing server guard');}
$view=file_get_contents(dirname(__DIR__).'/application/views/sales/register.php');
if(strpos($view,'Contacte a Dibs para actualizar la información del cliente')===FALSE || strpos($view,'bootbox.alert')===FALSE)throw new Exception('Missing seller popup');
echo "Credit phone guard passed: blank profiles, mixed payments, fresh updates, installments, returns, and ordinary payments.\n";
