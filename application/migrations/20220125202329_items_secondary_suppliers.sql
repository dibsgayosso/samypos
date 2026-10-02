-- items_secondary_suppliers --
CREATE TABLE `phppos_items_secondary_suppliers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `item_id` int(10) NOT NULL,
  `supplier_id` int(10) NOT NULL,
  `cost_price` decimal(23,10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `phppos_items_secondary_suppliers_ibfk_2` (`supplier_id`),
  CONSTRAINT `phppos_items_secondary_suppliers_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`),
  CONSTRAINT `phppos_items_secondary_suppliers_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `phppos_suppliers` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `phppos_receivings_items` ADD COLUMN `supplier_id` INT NULL AFTER `items_quantity_units_id`, ADD FOREIGN KEY (`supplier_id`) REFERENCES `phppos_suppliers`(`person_id`);

ALTER TABLE `phppos_items_secondary_suppliers` ADD COLUMN `unit_price` DECIMAL(23,10) NULL AFTER `cost_price`;

ALTER TABLE `phppos_item_variations` ADD COLUMN `supplier_id` INT NULL AFTER `ecommerce_inventory_item_id`, ADD FOREIGN KEY (`supplier_id`) REFERENCES `phppos_suppliers`(`person_id`);

ALTER TABLE `phppos_sales_items` ADD COLUMN `supplier_id` INT NULL AFTER `receipt_line_sort_order`, ADD FOREIGN KEY (`supplier_id`) REFERENCES `phppos_suppliers`(`person_id`);

ALTER TABLE `phppos_sales_item_kits` ADD COLUMN `supplier_id` INT NULL AFTER `receipt_line_sort_order`, ADD FOREIGN KEY (`supplier_id`) REFERENCES `phppos_suppliers`(`person_id`);