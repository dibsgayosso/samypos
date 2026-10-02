-- add_title_in_customers_suppliers --
ALTER TABLE `phppos_people` 
	ADD COLUMN `title` VARCHAR(255)  COLLATE utf8_unicode_ci; 