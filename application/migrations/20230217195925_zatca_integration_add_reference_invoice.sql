-- zatca_integration_add_reference_invoice --
ALTER TABLE `phppos_sales` ADD `ref_sale_id` INT(10) NULL AFTER `return_sale_id`, ADD `ref_sale_desc` TEXT NULL AFTER `ref_sale_id`;

ALTER TABLE `phppos_sales` ADD FOREIGN KEY (`ref_sale_id`) REFERENCES `phppos_sales`(`sale_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;