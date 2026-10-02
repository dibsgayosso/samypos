-- add_send_sms_via_whatsapp_to_location --
ALTER TABLE `phppos_locations` ADD `send_sms_via_whatsapp` INT(1) NOT NULL DEFAULT 0 AFTER `additional_appointment_note`;
