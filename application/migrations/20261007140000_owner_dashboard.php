<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_owner_dashboard extends MY_Migration
{
    public function up()
    {
        $this->checked_query("INSERT IGNORE INTO ".$this->db->dbprefix('modules_actions')." (action_id,module_id,action_name_key,sort) VALUES ('view_owner_dashboard','reports','common_view_owner_dashboard',260)");
    }
    private function checked_query($sql) {
        if ($this->db->query($sql) === FALSE) throw new RuntimeException('No se pudo aplicar la migración 20261007140000_owner_dashboard. Revisa el registro SQL del servidor.');
    }
    public function down() { /* Preserve explicit grants; no automatic access changes. */ }
}
