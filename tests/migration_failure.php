<?php
define('BASEPATH',__DIR__);
function log_message($level,$message) {}
function verify($ok,$message) { if (!$ok) throw new RuntimeException($message); }
class MigrationTestDb {
    public $fail=TRUE, $queries=array();
    function table_exists($table) { return $table!=='app_config'; }
    function dbprefix($table) { return 'phppos_'.$table; }
    function query($sql) { $this->queries[]=$sql; return !$this->fail; }
}
$GLOBALS['migration_db']=new MigrationTestDb();
class CI_Migration {
    protected $_migration_type='timestamp', $_migration_version='20261008063000', $_error_string='';
    protected static $version='20261007123000';
    public $db,$input;
    function __construct($config=array()) {
        $this->db=$GLOBALS['migration_db'];
        $this->input=new class { function is_cli_request() { return FALSE; } };
    }
    protected function _get_version() { return self::$version; }
    protected function _update_version($version) { self::$version=$version; }
    protected function _get_migration_name($name) { return substr($name,15); }
    public function find_migrations() {
        $root=dirname(__DIR__).'/application/migrations/';
        return array('20261007140000'=>$root.'20261007140000_owner_dashboard.php',
            '20261007180000'=>$root.'20261007180000_credit_report_schedule.php');
    }
}
require dirname(__DIR__).'/application/libraries/MY_Migration.php';
$runner=new MY_Migration();
verify($runner->version('20261007180000')===FALSE,'SQL failure must stop the migration runner');
verify($runner->get_version()==='20261007123000','Failed migration must never advance the recorded version');
verify(count($GLOBALS['migration_db']->queries)===1,'No later migration may execute after failure');
$GLOBALS['migration_db']->fail=FALSE;
verify((string)$runner->version('20261007180000')==='20261007180000','Successful retry must advance normally');
require dirname(__DIR__).'/application/migrations/20261008063000_repair_supervision_schema.php';
$repair=new Migration_repair_supervision_schema();
$repair->up(); $repair->up();
verify((string)$runner->get_version()==='20261007180000','Direct idempotent repair must not rewind migration version');
$GLOBALS['migration_db']->fail=TRUE;
$failed=FALSE;
try { $repair->up(); } catch (RuntimeException $error) { $failed=TRUE; }
verify($failed,'Repair must report DDL failures');
echo "Migration failure, retry and additive repair tests passed\n";
