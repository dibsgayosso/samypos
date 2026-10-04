<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_view_register_difference_permission extends MY_Migration
{
    public function up()
    {
        $this->execute_sql(realpath(dirname(__FILE__).'/20261003224500_view_register_difference_permission.sql'));
    }

    public function down()
    {
        $this->db->where('module_id', 'reports');
        $this->db->where('action_id', 'view_register_difference');
        $this->db->delete('permissions_actions');

        $this->db->where('module_id', 'reports');
        $this->db->where('action_id', 'view_register_difference');
        $this->db->delete('modules_actions');
    }
}
