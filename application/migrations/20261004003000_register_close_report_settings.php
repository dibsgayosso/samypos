<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_register_close_report_settings extends MY_Migration
{
    public function up()
    {
        $this->execute_sql(realpath(dirname(__FILE__).'/20261004003000_register_close_report_settings.sql'));
    }
    public function down()
    {
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('locations')." DROP COLUMN register_close_report_enabled, DROP COLUMN register_close_report_email, DROP COLUMN register_close_report_cc, DROP COLUMN register_close_report_bcc");
    }
}
