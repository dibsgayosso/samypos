<?php
// Compile the real model queries with CodeIgniter's actual prefixed query builder.
require __DIR__.'/owner_dashboard.php';
require dirname(__DIR__).'/system/database/DB_driver.php';
require dirname(__DIR__).'/system/database/DB_query_builder.php';
class OwnerCompiledDb extends CI_DB_query_builder
{
    public $sql=array();
    protected $_escape_char='`';
    protected function _escape_str($value) { return str_replace("'","''",$value); }
    public function table_exists($table) { return FALSE; }
    public function get($table='', $limit=NULL, $offset=NULL) {
        $this->sql[]=$this->get_compiled_select();
        return new class {
            function row_array() { return array('last_sale'=>NULL,'operations'=>0,'amount'=>0,'debt'=>0,'in_favor'=>0,'customers'=>0); }
            function result_array() { return array(); }
        };
    }
}
$db=new OwnerCompiledDb(array('dbprefix'=>'phppos_'));
$model->db=$db;
$model->Employee->difference=FALSE;
$model->snapshot(10,$loader);
$expenses=array_values(array_filter($db->sql,function($sql){ return strpos($sql,'FROM `phppos_expenses`')!==FALSE; }));
check(count($expenses)===2,'Compile expense queries for both authorized branches');
foreach ($expenses as $sql) {
    check(strpos($sql,'COALESCE(phppos_expenses_categories.name,')!==FALSE,'Raw aggregate must use the real prefixed category table');
    check(strpos($sql,'`phppos_expenses_categories`.`id`=`phppos_expenses`.`category_id`')!==FALSE,'Join must use the same actual tables');
}
echo "Real CodeIgniter SQL prefix regression passed\n";
