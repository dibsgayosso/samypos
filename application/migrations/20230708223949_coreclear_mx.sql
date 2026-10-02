-- coreclear_mx --
ALTER TABLE phppos_locations 
ADD `coreclear_mx_merchant_id` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
ADD `coreclear_user` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
ADD `coreclear_password` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
ADD `coreclear_consumer_key` TEXT COLLATE utf8_unicode_ci,
ADD `coreclear_secret_key` TEXT COLLATE utf8_unicode_ci,
ADD `coreclear_authorization_key` TEXT COLLATE utf8_unicode_ci,
ADD `coreclear_sandbox` TINYINT(1) DEFAULT 0,
ADD `coreclear_allow_cards_on_file` TINYINT(1) DEFAULT 0,
ADD `coreclear_authorization_key_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP