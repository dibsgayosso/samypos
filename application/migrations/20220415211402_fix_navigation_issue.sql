-- fix_navigation_issue --
UPDATE phppos_modules SET sort = '72' WHERE module_id = 'work_orders'; 
UPDATE phppos_modules SET sort = '74' WHERE module_id = 'expenses'; 