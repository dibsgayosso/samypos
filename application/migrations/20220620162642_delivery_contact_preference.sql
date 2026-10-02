-- delivery_contact_preference --
ALTER TABLE `phppos_sales_deliveries` ADD `contact_preference` VARCHAR(255) NULL DEFAULT NULL AFTER `category_id`;
