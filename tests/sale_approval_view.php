<?php
// Models first loaded inside a view exist on the controller, not the Loader.
error_reporting(E_ALL);
set_error_handler(function($severity,$message){throw new RuntimeException($message);});
$ci=new stdClass();
function &get_instance(){global $ci;return $ci;}
$ci->load=new class {
    function model($name){$ci=&get_instance();$ci->$name=new class {function required($cart){return $cart->approval;}};}
};
$view=file_get_contents(dirname(__DIR__).'/application/views/sales/register.php');
preg_match('/^<\?php(.*?)\?>/s',$view,$match);
class LoaderFixture {
    function render_prelude($code,$cart){eval($code);return $sale_requires_approval;}
}
$loader=new LoaderFixture();
foreach(array(FALSE,TRUE) as $expected){
    unset($ci->Sale_approvals);
    $cart=(object)array('approval'=>$expected);
    if ($loader->render_prelude($match[1],$cart)!==$expected)throw new RuntimeException('Incorrect approval button state');
    if (isset($loader->Sale_approvals))throw new RuntimeException('Fixture must not expose dynamically loaded model on view');
}
if(strpos($view,'$this->Sale_approvals')!==FALSE)throw new RuntimeException('View still accesses missing Loader property');
echo "Approval view first-load and button state regression passed\n";
