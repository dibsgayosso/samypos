<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_receiving_push extends MY_Migration {
 public function up() {
  $settings=$this->db->dbprefix('webpush_settings');
  $subs=$this->db->dbprefix('webpush_subscriptions');
  $queue=$this->db->dbprefix('webpush_queue');
  $this->checked_query("CREATE TABLE IF NOT EXISTS `$settings` (id INT PRIMARY KEY, public_key VARCHAR(128) NOT NULL, private_key VARCHAR(128) NOT NULL, subject VARCHAR(512) NOT NULL) ENGINE=InnoDB");
  $this->checked_query("CREATE TABLE IF NOT EXISTS `$subs` (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, person_id INT NOT NULL, endpoint TEXT NOT NULL, endpoint_hash CHAR(64) NOT NULL, public_key VARCHAR(128) NOT NULL, auth_token VARCHAR(64) NOT NULL, enabled TINYINT NOT NULL DEFAULT 1, UNIQUE KEY endpoint (endpoint_hash), KEY employee (person_id,enabled)) ENGINE=InnoDB");
  $this->checked_query("CREATE TABLE IF NOT EXISTS `$queue` (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, request_id BIGINT UNSIGNED NOT NULL, subscription_id BIGINT UNSIGNED NOT NULL, review_url TEXT NOT NULL, status VARCHAR(20) NOT NULL DEFAULT 'pending', attempts INT NOT NULL DEFAULT 0, next_attempt_at DATETIME NOT NULL, claimed_at DATETIME NULL, sent_at DATETIME NULL, last_error VARCHAR(255) NULL, UNIQUE KEY recipient_request (request_id,subscription_id), KEY delivery (status,next_attempt_at)) ENGINE=InnoDB");
 }
 private function checked_query($sql) {
        if ($this->db->query($sql) === FALSE) throw new RuntimeException('No se pudo aplicar la migración 20261007123000_receiving_push. Revisa el registro SQL del servidor.');
    }
    public function down() { /* Preserve keys, subscriptions and audit history. */ }
}
