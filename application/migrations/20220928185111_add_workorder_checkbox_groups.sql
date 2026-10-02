-- add_workorder_checkbox_groups --
CREATE TABLE `phppos_workorder_checkbox_groups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sort_order` int(10) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `sort_index` (`deleted`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `phppos_workorder_checkboxes` 
ADD `group_id` INT(10) NULL AFTER `last_modified`, 
ADD CONSTRAINT `phppos_workorder_checkboxes_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `phppos_workorder_checkbox_groups`(`id`);