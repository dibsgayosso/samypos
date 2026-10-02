-- add_permission_delete_log_activity --
INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('delete_log_activity', 'work_orders', 'work_orders_delete_log_activity', '244');
 


INSERT INTO phppos_permissions_actions (module_id, person_id, action_id)
SELECT DISTINCT phppos_permissions.module_id, phppos_permissions.person_id, action_id
FROM phppos_permissions
INNER JOIN phppos_modules_actions ON phppos_permissions.module_id = phppos_modules_actions.module_id
WHERE phppos_permissions.module_id = 'work_orders' AND
action_id = 'delete_log_activity'
ORDER BY module_id, person_id;
