-- 2fa_install --
ALTER TABLE `phppos_employees` ADD `secret_key_2fa` VARCHAR(255) NULL DEFAULT NULL AFTER `allowed_ip_address`;
