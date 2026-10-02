-- add_device_location_and_status_to_sales_items_notes --
ALTER TABLE `phppos_sales_items_notes` ADD COLUMN `device_location` VARCHAR(255) NULL AFTER `images`;

ALTER TABLE `phppos_sales_items_notes` 
ADD `status` INT(10) NULL AFTER `device_location`, 
ADD	CONSTRAINT `phppos_sales_items_notes_ibfk_4` FOREIGN KEY (`status`) REFERENCES `phppos_workorder_statuses` (`id`);