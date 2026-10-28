<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_sale_approvals extends MY_Migration
{
    public function up()
    {
        $table = $this->db->dbprefix('sale_requests');
        $this->checked_query("CREATE TABLE IF NOT EXISTS `$table` (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            location_id INT NOT NULL, employee_id INT NOT NULL, customer_id INT NULL,
            sale_time DATETIME NOT NULL, total DECIMAL(23,10) NOT NULL,
            supervisor_revision INT NOT NULL DEFAULT 1, status VARCHAR(20) NOT NULL DEFAULT 'pending',
            cart_payload LONGTEXT NOT NULL, detail_json LONGTEXT NOT NULL, context_json LONGTEXT NOT NULL,
            authorized_by INT NULL, authorized_at DATETIME NULL, sale_id INT NULL,
            source_sale_id INT NULL, rejection_reason TEXT NULL, UNIQUE KEY source_receipt (source_sale_id),
            submission_key VARCHAR(64) NOT NULL, UNIQUE KEY submission (submission_key),
            KEY pending_branch (location_id,status,id)
        ) ENGINE=InnoDB");
        $this->checked_query("INSERT IGNORE INTO ".$this->db->dbprefix('modules_actions')."
            (action_id,module_id,action_name_key,sort) VALUES
            ('authorize_sales','sales','common_authorize_sales',250)");
    }
    private function checked_query($sql) {
        if ($this->db->query($sql) === FALSE) throw new RuntimeException('No se pudo aplicar la migración 20261028010000_sale_approvals. Revisa el registro SQL del servidor.');
    }
    public function down() { /* Preserve authorization audit history. */ }
}
