-- work_order_activity --

CREATE TABLE `phppos_work_order_log` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`work_order_id` int(10) NOT NULL,
`activity_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`employee_id` int(10) NOT NULL,
`activity_text` TEXT NOT NULL,
CONSTRAINT `phppos_work_order_log_ibfk_1` FOREIGN KEY (`work_order_id`) REFERENCES `phppos_sales_work_orders` (`id`),
CONSTRAINT `phppos_work_order_log_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `phppos_employees` (`person_id`),
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
