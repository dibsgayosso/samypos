-- permission_to_add_remove_amounts_from_cash_drawer --
INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('add_remove_amounts_from_cash_drawer', 'sales', 'common_add_remove_amounts_from_cash_drawer', 505);
INSERT INTO phppos_permissions_actions (module_id, person_id, action_id)
SELECT DISTINCT phppos_permissions.module_id, phppos_permissions.person_id, action_id
from phppos_permissions
inner join phppos_modules_actions on phppos_permissions.module_id = phppos_modules_actions.module_id
WHERE phppos_permissions.module_id = 'sales' and
action_id = 'add_remove_amounts_from_cash_drawer'
order by module_id, person_id;