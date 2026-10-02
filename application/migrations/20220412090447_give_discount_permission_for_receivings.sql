-- give_discount_permission_for_receivings --
INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`)     VALUES ('give_discount', 'receivings', 'module_give_discount', 308);

INSERT INTO phppos_permissions_actions (module_id, person_id, action_id)
SELECT DISTINCT phppos_permissions.module_id, phppos_permissions.person_id, action_id
FROM phppos_permissions
INNER JOIN phppos_modules_actions ON phppos_permissions.module_id = phppos_modules_actions.module_id
WHERE phppos_permissions.module_id = 'receivings' AND
action_id = 'give_discount'
ORDER BY module_id, person_id;