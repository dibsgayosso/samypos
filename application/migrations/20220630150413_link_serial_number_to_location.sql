-- link_serial_number_to_location --
ALTER TABLE phppos_items_serial_numbers ADD COLUMN serial_location_id INT(11) NULL DEFAULT NULL;
ALTER TABLE phppos_items_serial_numbers ADD CONSTRAINT phppos_items_serial_numbers_ibfk_3 FOREIGN KEY (serial_location_id) REFERENCES phppos_locations(location_id);