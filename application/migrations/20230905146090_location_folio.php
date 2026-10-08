<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_Location_folio extends MY_Migration
{
    public function up()
    {
        $table = $this->db->dbprefix('sales');
        if (!$this->db->field_exists('location_folio', 'sales')) {
            $this->run_query("ALTER TABLE `$table` ADD COLUMN `location_folio` INT(10) UNSIGNED DEFAULT NULL AFTER `sale_id`");
        }

        // Retain assigned folios when resuming a partially applied migration.
        $this->run_query('SET @row_number := 0');
        $this->run_query('SET @current_location := NULL');
        $this->run_query("UPDATE `$table` s
            JOIN (
                SELECT sale_id, location_id,
                    (@row_number := IF(@current_location = location_id, @row_number + 1, 1)) AS location_folio,
                    (@current_location := location_id) AS dummy
                FROM `$table` s2
                JOIN (SELECT @row_number := 0, @current_location := NULL) vars
                ORDER BY s2.location_id, s2.sale_id
            ) seq ON seq.sale_id = s.sale_id
            SET s.location_folio = seq.location_folio
            WHERE s.location_folio IS NULL");

        $index = $table.'_location_folio';
        $existing = $this->db->query("SHOW INDEX FROM `$table` WHERE Key_name = ".$this->db->escape($index));
        if ($existing === FALSE) throw new RuntimeException('No se pudo comprobar el índice de folios.');
        if ($existing->num_rows() === 0) {
            $this->run_query("CREATE INDEX `$index` ON `$table` (`location_id`,`location_folio`)");
        }
    }

    private function run_query($sql)
    {
        if ($this->db->query($sql) === FALSE) {
            throw new RuntimeException('No se pudo completar la migración de folios. Revisa el registro del servidor.');
        }
    }

    public function down() {}
}
