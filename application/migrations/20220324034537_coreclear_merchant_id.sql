-- coreclear_merchant_id --
ALTER TABLE phppos_locations 
ADD `coreclear_merchant_id` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL;