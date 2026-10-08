<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_repair_location_report_settings extends MY_Migration
{
    public function up()
    {
        $columns=array(
            'register_close_report_enabled'=>'TINYINT(1) NOT NULL DEFAULT 0',
            'register_close_report_email'=>'VARCHAR(255) NULL DEFAULT NULL',
            'register_close_report_cc'=>'VARCHAR(255) NULL DEFAULT NULL',
            'register_close_report_bcc'=>'VARCHAR(255) NULL DEFAULT NULL'
        );
        foreach ($columns as $name=>$definition) {
            if (!$this->db->field_exists($name,'locations')) {
                $table=$this->db->dbprefix('locations');
                if ($this->db->query("ALTER TABLE `$table` ADD COLUMN `$name` $definition")===FALSE)
                    throw new RuntimeException('No se pudo crear el campo de sucursal '.$name.'.');
            }
        }
    }
    public function down() { /* Preserve branch report settings. */ }
}
