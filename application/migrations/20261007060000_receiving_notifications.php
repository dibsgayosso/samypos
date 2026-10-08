<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migration_receiving_notifications extends MY_Migration
{
    public function up()
    {
        $queue=$this->db->dbprefix('receiving_notification_queue');
        $this->checked_query("CREATE TABLE IF NOT EXISTS `$queue` (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, request_id BIGINT UNSIGNED NOT NULL,
            recipient_id INT NOT NULL, review_url TEXT NOT NULL, status VARCHAR(20) NOT NULL DEFAULT 'pending',
            attempts INT NOT NULL DEFAULT 0, next_attempt_at DATETIME NOT NULL, claimed_at DATETIME NULL,
            sent_at DATETIME NULL, last_error VARCHAR(255) NULL,
            UNIQUE KEY recipient_request (request_id,recipient_id), KEY delivery (status,next_attempt_at)
        ) ENGINE=InnoDB");
    }
    private function checked_query($sql) {
        if ($this->db->query($sql) === FALSE) throw new RuntimeException('No se pudo aplicar la migración 20261007060000_receiving_notifications. Revisa el registro SQL del servidor.');
    }
    public function down() { /* Preserve notification delivery history. */ }
}
