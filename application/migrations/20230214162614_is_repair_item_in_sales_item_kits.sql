-- is_repair_item_in_sales_item_kits --

ALTER TABLE `phppos_sales_item_kits` ADD `is_repair_item` INT(11) NULL DEFAULT '0' AFTER `supplier_id`;