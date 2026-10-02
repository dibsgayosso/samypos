-- add_approved_by_and_assigned_to_to_sale_items --
ALTER TABLE `phppos_sales_items` 
ADD `approved_by` INT(10) NULL AFTER `supplier_id`, 
ADD CONSTRAINT `phppos_sales_items_ibfk_8` FOREIGN KEY (`approved_by`) REFERENCES `phppos_employees`(`person_id`),
ADD `assigned_to` INT(10) NULL AFTER `approved_by`, 
ADD CONSTRAINT `phppos_sales_items_ibfk_9` FOREIGN KEY (`assigned_to`) REFERENCES `phppos_employees`(`person_id`);
