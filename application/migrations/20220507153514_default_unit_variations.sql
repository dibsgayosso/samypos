-- default_unit_variations --
ALTER TABLE phppos_items_quantity_units ADD COLUMN default_for_sale INT(1) DEFAULT 0;
ALTER TABLE `phppos_items_quantity_units` ADD INDEX `default_for_sale` (`default_for_sale`);

ALTER TABLE phppos_items_quantity_units ADD COLUMN default_for_recv INT(1) DEFAULT 0;
ALTER TABLE `phppos_items_quantity_units` ADD INDEX `default_for_recv` (`default_for_recv`);
