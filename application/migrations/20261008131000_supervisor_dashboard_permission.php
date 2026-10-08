<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_supervisor_dashboard_permission extends MY_Migration
{
    public function up()
    {
        $sql="INSERT IGNORE INTO ".$this->db->dbprefix('modules_actions').
            " (action_id,module_id,action_name_key,sort) VALUES
            ('view_supervisor_dashboard','reports','common_view_supervisor_dashboard',261)";
        if ($this->db->query($sql) === FALSE) throw new RuntimeException('No se pudo registrar el permiso del panel del supervisor.');
    }
    public function down() { /* Preserve explicit employee grants. */ }
}
