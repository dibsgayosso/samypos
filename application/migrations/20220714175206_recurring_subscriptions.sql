-- recurring_subscriptions --
ALTER TABLE phppos_items 
ADD COLUMN is_recurring INT(1) DEFAULT '0',
ADD COLUMN startup_cost DECIMAL (23,10) DEFAULT '0',
ADD COLUMN prorated INT(1) DEFAULT '0',
ADD COLUMN `interval` VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN weekday INT(1) NULL DEFAULT NULL,
ADD COLUMN day_number INT(10) NULL DEFAULT NULL,
ADD COLUMN month INT(10) NULL DEFAULT NULL,
ADD COLUMN day VARCHAR(255) NULL DEFAULT NULL;


CREATE TABLE `phppos_customer_subscriptions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sale_id` INT(10) NULL DEFAULT NULL,
  `location_id` INT(10) NOT NULL,
  `item_id` INT(10) NOT NULL,
  `variation_id` INT(10) NULL DEFAULT NULL,
  `startup_cost` DECIMAL (23,10) DEFAULT '0',
  `recurring_charge_amount` DECIMAL (23,10) DEFAULT '0',
  `customer_id` INT(10) NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `interval` VARCHAR(255) NULL DEFAULT NULL,
  `weekday` INT(1) NULL DEFAULT NULL,
  `day_number` INT(10) NULL DEFAULT NULL,
  `month` INT(10) NULL DEFAULT NULL,
  `day` VARCHAR(255) NULL DEFAULT NULL,
  `next_payment_date` DATE NULL DEFAULT NULL,
  `next_retry_date` DATE NULL DEFAULT NULL,
  `retries_attempted` int(10) DEFAULT 0,
  `card_on_file_token` VARCHAR(255) NULL DEFAULT NULL,
  `card_on_file_masked` VARCHAR(255) NULL DEFAULT NULL,
  `card_on_file_expiration_date` DATE NULL DEFAULT NULL,
  `deleted` INT(1) DEFAULT '0',
  CONSTRAINT `phppos_customer_subscriptions_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `phppos_sales` (`sale_id`),
  CONSTRAINT `phppos_customer_subscriptions_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `phppos_items` (`item_id`),
  CONSTRAINT `phppos_customer_subscriptions_ibfk_3` FOREIGN KEY (`variation_id`) REFERENCES `phppos_item_variations` (`id`),
  CONSTRAINT `phppos_customer_subscriptions_ibfk_4` FOREIGN KEY (`customer_id`) REFERENCES `phppos_customers` (`person_id`),
  CONSTRAINT `phppos_customer_subscriptions_ibfk_5` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
