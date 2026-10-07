<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_receiving_supervisor extends MY_Migration
{
    public function up()
    {
        $this->load->dbforge();
        if (!$this->db->field_exists('supervisor_status', 'receivings')) {
            $this->dbforge->add_column('receivings', array(
                'supervisor_status' => array('type'=>'VARCHAR','constraint'=>20,'null'=>TRUE),
                'supervisor_revision' => array('type'=>'INT','default'=>0)
            ));
        }
        $table = $this->db->dbprefix('receiving_authorizations');
        $this->db->query("CREATE TABLE IF NOT EXISTS `$table` (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            receiving_id INT NOT NULL, revision INT NOT NULL, employee_id INT NOT NULL,
            authorized_at DATETIME NOT NULL, UNIQUE KEY receipt_revision (receiving_id, revision)
        ) ENGINE=InnoDB");
        $this->db->query("INSERT IGNORE INTO ".$this->db->dbprefix('modules_actions')."
            (action_id,module_id,action_name_key,sort) VALUES
            ('authorize_receivings','receivings','common_authorize_receivings',250)");
    }
    public function down() { /* Preserve authorization audit history. */ }
}
