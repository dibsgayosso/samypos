-- permission_to_see_item_quantity --
INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('see_item_quantity', 'items', 'items_see_item_quantity', 64);
INSERT INTO phppos_permissions_actions (module_id, person_id, action_id)
SELECT DISTINCT phppos_permissions.module_id, phppos_permissions.person_id, action_id
from phppos_permissions
inner join phppos_modules_actions on phppos_permissions.module_id = phppos_modules_actions.module_id
WHERE phppos_permissions.module_id = 'items' and
action_id = 'see_item_quantity'
order by module_id, person_id;