-- add_approved_and_assigned_sales_item_kits --
ALTER TABLE `phppos_sales_item_kits` ADD `approved_by` INT(10) NULL AFTER `supplier_id`, ADD `assigned_to` INT(10) NULL AFTER `approved_by`;