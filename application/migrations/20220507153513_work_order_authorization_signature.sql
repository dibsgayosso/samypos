-- work_order_authorization_signature --
ALTER TABLE `phppos_sales_work_orders` ADD `pre_auth_signature_file_id` INT (11) NULL DEFAULT NULL;
ALTER TABLE `phppos_sales_work_orders` ADD `post_auth_signature_file_id` INT (11) NULL DEFAULT NULL;

ALTER TABLE `phppos_sales_work_orders` ADD CONSTRAINT phppos_sales_work_orders_ibfk_4 FOREIGN KEY (`pre_auth_signature_file_id`) REFERENCES phppos_app_files (`file_id`);
ALTER TABLE `phppos_sales_work_orders` ADD CONSTRAINT phppos_sales_work_orders_ibfk_5 FOREIGN KEY (`post_auth_signature_file_id`) REFERENCES phppos_app_files (`file_id`);