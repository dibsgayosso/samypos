-- work_order_authorization_template --
ALTER TABLE `phppos_locations` ADD `blockchyp_work_order_pre_auth` TEXT NOT NULL DEFAULT '';
ALTER TABLE `phppos_locations` ADD `blockchyp_work_order_post_auth` TEXT NOT NULL DEFAULT '';