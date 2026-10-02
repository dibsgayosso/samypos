-- edit_suspended_sale_data --
INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('edit_suspended_sale_data', 'sales', 'sales_edit_suspended_sale_data', '300');
 
INSERT INTO phppos_permissions_actions (module_id, person_id, action_id)
SELECT DISTINCT phppos_permissions.module_id, phppos_permissions.person_id, action_id
FROM phppos_permissions
INNER JOIN phppos_modules_actions ON phppos_permissions.module_id = phppos_modules_actions.module_id
WHERE phppos_permissions.module_id = 'sales' AND
action_id = 'edit_suspended_sale_data'
ORDER BY module_id, person_id;
