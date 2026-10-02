-- repair_item_identifier_to_sales_items --
ALTER TABLE `phppos_sales_items` ADD `is_repair_item` INT NOT NULL DEFAULT '0' AFTER `assigned_to`;