<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_repair_supervision_schema extends MY_Migration
{
    public function up()
    {
        // Reapply only additive, idempotent feature migrations. Never rewind the version.
        $migrations=array(
            '20261007053000_receiving_supervisor'=>'Migration_receiving_supervisor',
            '20261007060000_receiving_notifications'=>'Migration_receiving_notifications',
            '20261007123000_receiving_push'=>'Migration_receiving_push',
            '20261007140000_owner_dashboard'=>'Migration_owner_dashboard',
            '20261007180000_credit_report_schedule'=>'Migration_credit_report_schedule'
        );
        foreach ($migrations as $file=>$class) {
            require_once __DIR__.'/'.$file.'.php';
            $migration=new $class();
            $migration->up();
        }
        // table_exists() uses a list cached before DDL by migration constructors.
        unset($this->db->data_cache['table_names']);
        foreach (array('receiving_requests','receiving_notification_queue','webpush_settings',
            'webpush_subscriptions','webpush_queue','credit_report_schedules','credit_report_jobs') as $table) {
            if (!$this->db->table_exists($table)) throw new RuntimeException('Falta la tabla '.$table.'.');
        }
    }
    public function down() { /* Preserve inventory authorization and notification history. */ }
}
