<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_credit_report_schedule extends MY_Migration {
 public function up() {
  $s=$this->db->dbprefix('credit_report_schedules'); $j=$this->db->dbprefix('credit_report_jobs');
  $this->checked_query("CREATE TABLE IF NOT EXISTS `$s` (person_id INT PRIMARY KEY, enabled TINYINT NOT NULL DEFAULT 0, weekday TINYINT NOT NULL DEFAULT 1, send_time CHAR(5) NOT NULL DEFAULT '08:00', interval_weeks TINYINT NOT NULL DEFAULT 1, timezone VARCHAR(80) NOT NULL, next_run DATETIME NOT NULL, revision INT NOT NULL DEFAULT 1) ENGINE=InnoDB");
  $this->checked_query("CREATE TABLE IF NOT EXISTS `$j` (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, person_id INT NOT NULL, scheduled_for DATETIME NOT NULL, revision INT NOT NULL, recipient VARCHAR(254) NOT NULL, payload LONGTEXT NULL, status VARCHAR(20) NOT NULL DEFAULT 'pending', attempts INT NOT NULL DEFAULT 0, next_attempt DATETIME NOT NULL, sent_at DATETIME NULL, last_error VARCHAR(255) NULL, UNIQUE KEY occurrence (person_id,scheduled_for,revision), KEY delivery (status,next_attempt)) ENGINE=InnoDB");
 }
 private function checked_query($sql) {
        if ($this->db->query($sql) === FALSE) throw new RuntimeException('No se pudo aplicar la migración 20261007180000_credit_report_schedule. Revisa el registro SQL del servidor.');
    }
    public function down() { /* Retain delivery and report history. */ }
}
