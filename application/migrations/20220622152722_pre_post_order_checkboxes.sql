-- pre_post_order_checkboxes --
CREATE TABLE `phppos_workorder_checkboxes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `last_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
--
-- Indexes for table `phppos_workorder_checkboxes`
--

ALTER TABLE `phppos_workorder_checkboxes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

ALTER TABLE `phppos_workorder_checkboxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

CREATE TABLE `phppos_workorder_checkboxes_states` (
  `checkbox_id` int(10) NOT NULL,
  `workorder_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Indexes for table `phppos_workorder_checkboxes_states`
--
ALTER TABLE `phppos_workorder_checkboxes_states`
  ADD PRIMARY KEY (`checkbox_id`,`workorder_id`) USING BTREE,
  ADD KEY `workorder_id` (`workorder_id`);

--
-- Constraints for table `phppos_workorder_checkboxes_states`
--
ALTER TABLE `phppos_workorder_checkboxes_states`
  ADD CONSTRAINT `phppos_workorder_checkboxes_states_ibfk_1` FOREIGN KEY (`workorder_id`) REFERENCES `phppos_sales_work_orders` (`id`),
  ADD CONSTRAINT `phppos_workorder_checkboxes_states_ibfk_2` FOREIGN KEY (`checkbox_id`) REFERENCES `phppos_workorder_checkboxes` (`id`);
COMMIT;
