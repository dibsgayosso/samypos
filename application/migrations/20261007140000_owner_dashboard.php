<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_owner_dashboard extends MY_Migration
{
    public function up()
    {
        $this->db->query("INSERT IGNORE INTO ".$this->db->dbprefix('modules_actions')." (action_id,module_id,action_name_key,sort) VALUES ('view_owner_dashboard','reports','common_view_owner_dashboard',260)");
    }
    public function down() { /* Preserve explicit grants; no automatic access changes. */ }
}
