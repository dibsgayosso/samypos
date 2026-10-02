-- add_customer_subscription_id_to_sales_table --
ALTER TABLE phppos_sales ADD COLUMN customer_subscription_id INT(11) NULL DEFAULT NULL;

ALTER TABLE `phppos_sales`
	ADD CONSTRAINT `phppos_sales_ibfk_12` FOREIGN KEY (`customer_subscription_id`) REFERENCES `phppos_customer_subscriptions` (`id`);
