-- Permiso para visualizar la diferencia de los cortes de caja.
-- Por seguridad NO se asigna automáticamente a ningún empleado.
INSERT IGNORE INTO `phppos_modules_actions`
(`action_id`, `module_id`, `action_name_key`, `sort`)
VALUES
('view_register_difference', 'reports', 'common_view_register_difference', 233);
