-- store_all_credit_card_transactions_charged --
 CREATE TABLE `phppos_credit_card_transactions_unconfirmed` (
   `id` INT(11) NOT NULL AUTO_INCREMENT,
   `time_of_charge` timestamp NOT NULL,
   `register_id_of_charge` INT(11) NULL DEFAULT NULL,
   `transaction_charge_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `amount` DECIMAL (23,10),
   `cart_data` longblob NOT NULL,
   PRIMARY KEY (`id`),
   CONSTRAINT `phppos_credit_card_transactions_charged_ibfk_1` FOREIGN KEY (`register_id_of_charge`) REFERENCES `phppos_registers` (`register_id`),
   KEY `phppos_cctc_transaction_charge_id_index` (`transaction_charge_id`)
 ) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;