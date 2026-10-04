ALTER TABLE `phppos_locations`
ADD COLUMN `register_close_report_enabled` TINYINT(1) NOT NULL DEFAULT 0,
ADD COLUMN `register_close_report_email` VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `register_close_report_cc` VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `register_close_report_bcc` VARCHAR(255) NULL DEFAULT NULL;