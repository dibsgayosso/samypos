CREATE TABLE `phppos_open_suspended_sales` (
  `sale_id` INT(11) NOT NULL,
  `employee_id` INT(11) NOT NULL,
  `register_id` INT(11) NOT NULL,
  `expires` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`sale_id`),
  KEY `phppos_open_suspended_sales_ibfk_1` (`sale_id`),
  KEY `phppos_open_suspended_sales_ibfk_2` (`employee_id`),
  KEY `phppos_open_suspended_sales_ibfk_3` (`register_id`),
  CONSTRAINT `phppos_open_suspended_sales_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `phppos_sales` (`sale_id`),
  CONSTRAINT `phppos_open_suspended_sales_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `phppos_employees` (`person_id`),
  CONSTRAINT `phppos_open_suspended_sales_ibfk_3` FOREIGN KEY (`register_id`) REFERENCES `phppos_registers` (`register_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;