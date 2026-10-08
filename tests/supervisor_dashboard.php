<?php
require __DIR__.'/supervisor_receiving.php';
function html_escape($s) { return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8'); }
function to_currency($n) { return '$'.number_format($n,2); }
function site_url($path) { return '/index.php/'.$path; }
class SupervisorPanelFixture extends Supervisor_dashboard {
    public $seen=array();
    function ready() { return TRUE; }
    function activity($id) {
        $this->seen[]=array($id,date_default_timezone_get());
        return array('last_sale'=>date('Y-m-d H:i:s',time()-300),'expenses'=>array('amount'=>150,'tax'=>24,'operations'=>2));
    }
    function pending($id) { return $id===1 ? array(array('receiving_id'=>19,'total'=>1500,'employee_id'=>10,'receiving_time'=>'2026-10-08 08:00:00')) : array(); }
}
$model=new SupervisorPanelFixture();
$model->Employee=new class {
    public $allowed=TRUE, $authorize=TRUE;
    function has_module_permission($module,$person) { return $this->allowed; }
    function get_authenticated_location_ids($person) { return array(1,2,3,4); }
    function has_module_action_permission($module,$action,$person,$location) { return (($module==='reports' && $action==='view_supervisor_dashboard') || ($this->authorize && $module==='receivings' && $action==='authorize_receivings')) && in_array($location,array(1,2,3),TRUE); }
    function get_info($id) { return (object)array('first_name'=>'Empleado','last_name'=>'<script>'); }
};
$model->Location=new class { function get_info($id) { return (object)array('location_id'=>$id,'deleted'=>$id===3,'timezone'=>$id===1?'America/Mexico_City':'America/Tijuana','name'=>'Sucursal <script>'.$id); } };
$zone=date_default_timezone_get(); $loaded=array();
$loader=function($id) use (&$loaded) { $loaded[]=$id; return array('sales_count'=>5,'sales_total'=>2000,'rows'=>array(
    array('label'=>'Tarjeta','total'=>1500,'operations'=>3),array('label'=>'Transferencia BBVA','total'=>500,'operations'=>2),
    array('label'=>'Crédito <script>','total'=>0,'operations'=>0))); };
$supervisor_dashboard=$model->snapshot(1,$loader);
check($loaded===array(1,2),'Only authorized active branches may load payments');
check($model->seen===array(array(1,'America/Mexico_City'),array(2,'America/Tijuana')),'Use each branch local timezone');
check(date_default_timezone_get()===$zone,'Restore timezone after snapshots');
try { $model->snapshot(1,function(){throw new RuntimeException('fixture');}); } catch (RuntimeException $e) {}
check(date_default_timezone_get()===$zone,'Restore timezone after snapshot failure');
$renderer=new class {
    public $Employee;
    function render($supervisor_dashboard) { ob_start(); require dirname(__DIR__).'/application/views/supervisor_panel.php'; return ob_get_clean(); }
};
$renderer->Employee=$model->Employee;
$html=$renderer->render($supervisor_dashboard);
check(strpos($html,'<script>')===FALSE && strpos($html,'&lt;script&gt;')!==FALSE,'Escape branch, payment and employee labels');
check(substr_count($html,'class="sv-branch"')===2 && strpos($html,'Revisar →')!==FALSE,'Render every authorized branch and review action');
check(strpos($html,'stroke-dasharray="282.743')!==FALSE,'Chart uses only transfer amounts');
check($supervisor_dashboard['branches'][0]['payments']['total']===500.0 && !isset($supervisor_dashboard['branches'][0]['payments']['sales_total']),'Never expose total sales in supervisor snapshot');
check(strpos($html,'Tarjeta')===FALSE && strpos($html,'Crédito')===FALSE && strpos($html,'Transferencia BBVA')!==FALSE,'Hide non-transfer methods');
$model->Employee->authorize=FALSE;
$read_only=$model->snapshot(1,$loader);
check(count($read_only['branches'])===2 && strpos($renderer->render($read_only),'Revisar →')===FALSE,'Panel permission works independently without approval access');
$model->Employee->allowed=FALSE; $loaded=array();
check(!$model->snapshot(1,$loader)['branches'] && !$loaded,'No reports module must expose no branches');
check(strpos($renderer->render(array('error'=>TRUE)),'No se pudo cargar')!==FALSE,'Visible error fallback');
if (isset($argv[1])) file_put_contents($argv[1],'<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><body style="font-family:Arial;background:#f1f5f9;margin:24px">'.$html.'</body>');
echo "Supervisor branch permissions, timezones, charts and rendering tests passed\n";
