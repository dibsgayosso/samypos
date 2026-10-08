<?php
require __DIR__.'/supervisor_receiving.php';
require APPPATH.'models/Owner_dashboard.php';
require APPPATH.'models/Register.php';
class OwnerEmployee {
    public $module=TRUE,$owner_locations=array(1,2,3),$difference=FALSE;
    function has_module_permission($module,$person) { return $this->module; }
    function get_authenticated_location_ids($person) { return array(1,2,3,4); }
    function has_module_action_permission($module,$action,$person,$location) { return $action==='view_register_difference' ? $this->difference : in_array($location,$this->owner_locations); }
    function get_info($person) { return (object)array('first_name'=>'Caja','last_name'=>'Uno'); }
}
class OwnerQuery {
    public $queries=array(),$current=array('where'=>array()),$ready=TRUE;
    function select($value,$escape=TRUE) { $this->current['select']=$value; return $this; }
    function from($table) { $this->current['table']=$table; return $this; }
    function join($table,$condition,$type='') { return $this; }
    function where($key,$value) { $this->current['where'][$key]=$value; return $this; }
    function group_by($field) { return $this; }
    function order_by($key,$order) { $this->current['order'][]=$key; return $this; }
    function limit($number) { return $this; }
    function table_exists($table) { return $this->ready; }
    function get() {
        $query=$this->current; $this->queries[]=$query; $this->current=array('where'=>array());
        $rows=array();
        switch ($query['table']) {
            case 'sales': $rows=array(array('last_sale'=>time()-120)); break;
            case 'receivings': $rows=array(array('operations'=>2,'amount'=>150)); break;
            case 'receiving_requests': $rows=array(array('operations'=>1,'amount'=>99)); break;
            case 'expenses': $rows=array(array('category'=>'Luz <script>','operations'=>1,'amount'=>58),array('category'=>'Sin categoría','operations'=>2,'amount'=>42)); break;
            case 'customers': $rows=array(array('debt'=>500,'in_favor'=>40,'customers'=>2)); break;
            case 'register_log': $rows=array(array('register_log_id'=>9,'employee_id_close'=>12,'register_name'=>'Caja 1','shift_end'=>'2026-10-06 22:00:00')); break;
        }
        return new class($rows) {
            private $rows; function __construct($rows) {$this->rows=$rows;}
            function row_array() { return $this->rows[0] ?? array(); } function result_array() { return $this->rows; } function num_rows() { return count($this->rows); }
        };
    }
}
class OwnerRegisterSpy extends Register {
    public $calls=0;
    function get_register_log($id) { $this->calls++; return array((object)array('difference'=>-50),(object)array('difference'=>20)); }
}
$model=new Owner_dashboard(); $model->db=new OwnerQuery(); $model->Employee=new OwnerEmployee();
$model->Location=new class { function get_info($id) { return (object)array('location_id'=>$id,'name'=>'Sucursal <script>'.$id,'timezone'=>$id===1?'America/Mexico_City':'America/Tijuana','deleted'=>$id===3?1:0); } };
$model->Register=new OwnerRegisterSpy(); $model->Register->db=$model->db; $model->Register->Employee=$model->Employee;
$loaded=array(); $original_zone=date_default_timezone_get();
$loader=function($id) use (&$loaded) { $loaded[]=$id; return array('sales_total'=>$id===1?200:-50,'sales_count'=>1,'rows'=>array(array('label'=>'Transferencia: BBVA','operations'=>1,'total'=>$id===1?200:-50))); };
$dashboard=$model->snapshot(10,$loader);
check($loaded===array(1,2),'Query only authorized, active branches');
check(date_default_timezone_get()===$original_zone,'Restore timezone after multi-branch queries');
check($dashboard['totals']['sales_total']===150 && $dashboard['totals']['sales_count']===2,'Sales consolidated without duplicating combined operations');
check($dashboard['payments'][0]['label']==='Transferencia: BBVA' && $dashboard['payments'][0]['total']===150,'Preserve custom payment labels and returns in consolidation');
check($dashboard['totals']['receiving_total']==300 && $dashboard['totals']['pending_total']==198,'Pending merchandise must be separate from applied entries');
check($dashboard['totals']['credit_balance']==1000 && $dashboard['totals']['credit_in_favor']==80,'Customer credits must not mask debt with balances in favor');
check($dashboard['totals']['expenses_total']==200 && $dashboard['totals']['expenses_count']===6,'Expense categories summed once, with taxes included');
check($model->Register->calls===0,'Never read differences without the existing permission');
foreach ($model->db->queries as $query) {
    $w=$query['where']; $table=$query['table'];
    check(isset($w['location_id']) || isset($w['expenses.location_id']),'Every financial query must be branch scoped');
    if ($table==='sales') check($w['deleted']===0 && $w['suspended']===0 && $w['store_account_payment']===0 && $w['total >=']===0,'Latest sale excludes deleted, suspended, debt payments and returns');
    if ($table==='receivings') check($w['total_quantity_received >']===0 && $w['suspended']===0 && $w['store_account_payment']===0,'Entries require real applied merchandise');
    if ($table==='expenses' || $table==='receivings') check(isset($w[($table==='expenses'?'expense_date':'receiving_time').' <']),'Day range is exclusive of next midnight');
}
$model->Employee->difference=TRUE;
$dashboard=$model->snapshot(10,$loader);
check($dashboard['branches'][0]['last_close']['difference']===-30.0,'Use native cut calculation across payment methods');
$cut_queries=array_values(array_filter($model->db->queries,function($q){return $q['table']==='register_log';}));
check($cut_queries[0]['order']===array('register_log.shift_end','register_log.register_log_id'),'Latest cut ordered by actual closing time then ID');
$model->Employee->module=FALSE; $loaded=array();
$empty=$model->snapshot(10,$loader); check(!$empty['branches'] && !$loaded,'No reports permission must expose no branches');
$model->Employee->module=TRUE;
try { $model->snapshot(10,function(){throw new RuntimeException('fixture failure');}); } catch (RuntimeException $error) {}
check(date_default_timezone_get()===$original_zone,'Restore timezone even if data loading fails');
$model->db->ready=FALSE;
$no_migration=$model->snapshot(10,$loader); check(!$no_migration['receiving_ready'],'Missing authorization migration shown explicitly');
function html_escape($s) {return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function to_currency($value) {return '$'.number_format($value,2);}
$owner_dashboard=$dashboard;
ob_start(); require APPPATH.'views/owner_panel.php'; $html=ob_get_clean();
check(strpos($html,'<script>')===FALSE && strpos($html,'&lt;script&gt;')!==FALSE,'Escape branch and expense names');
check(strpos($html,'Faltante')!==FALSE && strpos($html,'Transferencia: BBVA')!==FALSE,'Render cut warning and exact payment names');
ob_start(); require APPPATH.'language/spanish/common_lang.php'; $output=ob_get_clean();
check($output==='' && $lang['common_authorize_receivings']==='Autorizar recepciones de mercancía' && $lang['common_view_owner_dashboard']==='Ver panel del propietario','Spanish permissions must load as PHP without stray output');
echo "Owner dashboard permissions, consolidation, balances, cuts and rendering tests passed\n";
