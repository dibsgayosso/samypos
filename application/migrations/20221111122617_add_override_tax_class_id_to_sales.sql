-- add_override_tax_class_id_to_sales --
ALTER TABLE `phppos_sales` ADD `override_tax_class_id` INT NULL DEFAULT NULL AFTER `customer_subscription_id`;
