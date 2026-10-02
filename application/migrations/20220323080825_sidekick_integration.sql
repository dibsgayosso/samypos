-- sidekick_integration --
ALTER TABLE `phppos_locations` 
ADD `sidekick_api_key` TEXT NULL, 
ADD `sidekick_auto_review` INT(1) DEFAULT 0 NULL; 

INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) 
VALUES ('export_to_sidekick', 'customers', 'customers_export_to_sidekick', 46);

INSERT INTO phppos_permissions_actions (module_id, person_id, action_id)
SELECT DISTINCT phppos_permissions.module_id, phppos_permissions.person_id, action_id
from phppos_permissions
inner join phppos_modules_actions on phppos_permissions.module_id = phppos_modules_actions.module_id
WHERE phppos_permissions.module_id = 'customers' and
action_id = 'export_to_sidekick'
order by module_id, person_id;