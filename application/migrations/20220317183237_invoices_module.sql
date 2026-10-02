-- invoices_module --
CREATE TABLE phppos_terms (
	term_id INT(11) NOT NULL AUTO_INCREMENT,
	name VARCHAR(255),
	description TEXT,
	days_due INT(11) DEFAULT '30',
	deleted INT(1) DEFAULT '0',
	PRIMARY KEY (term_id) USING BTREE
) ENGINE = INNODB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;


CREATE TABLE phppos_customer_invoices (
	invoice_id INT(11) NOT NULL AUTO_INCREMENT,
	location_id INT(11) NOT NULL,
	customer_id INT(11),
	customer_po VARCHAR(255) COLLATE utf8_unicode_ci,
	term_id INT(11),
	invoice_date date,
	due_date date,
	total DECIMAL(23,10),
	balance DECIMAL(23,10),
	last_paid date,
	deleted INT(1) DEFAULT '0',
	PRIMARY KEY (invoice_id) USING BTREE,
    CONSTRAINT `phppos_customer_invoices_ibfk_1` FOREIGN KEY (`term_id`) REFERENCES `phppos_terms` (`term_id`),	
    CONSTRAINT `phppos_customer_invoices_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `phppos_customers` (`person_id`),
	CONSTRAINT `phppos_customer_invoices_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`)
	
) ENGINE = INNODB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;


CREATE TABLE phppos_customer_invoice_details (
	invoice_details_id INT(11) NOT NULL AUTO_INCREMENT,
	invoice_id INT(11) NOT NULL,
	line_id INT(11) NULL,
	sale_id INT(11) NULL,
	description TEXT,
	total DECIMAL(23,10),
	account VARCHAR(255) COLLATE utf8_unicode_ci,
	PRIMARY KEY (invoice_details_id) USING BTREE,
    CONSTRAINT `phppos_customer_invoice_details_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `phppos_sales` (`sale_id`),
    CONSTRAINT `phppos_customer_invoice_details_ibfk_2` FOREIGN KEY (`invoice_id`) REFERENCES `phppos_customer_invoices` (`invoice_id`)	
) ENGINE = INNODB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;


CREATE TABLE phppos_customer_invoice_payments (
	payment_id INT(11) NOT NULL AUTO_INCREMENT,
	invoice_id INT(11) NULL,
    `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,	
    `payment_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `payment_amount` decimal(23,10) NOT NULL,
    `auth_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `ref_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `cc_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `acq_ref_data` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `process_data` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `entry_method` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `aid` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `tvr` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `iad` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `tsi` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `arc` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `cvm` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `tran_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `application_label` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `truncated_card` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `card_issuer` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
	PRIMARY KEY (payment_id) USING BTREE,
    CONSTRAINT `phppos_customer_invoice_payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `phppos_customer_invoices` (`invoice_id`)	
) ENGINE = INNODB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;



CREATE TABLE phppos_supplier_invoices (
	invoice_id INT(11) NOT NULL AUTO_INCREMENT,
	location_id INT(11) NOT NULL,
	supplier_id INT(11),
	supplier_po VARCHAR(255) COLLATE utf8_unicode_ci,
	term_id INT(11),
	invoice_date date,
	due_date date,
	total DECIMAL(23,10),
	balance DECIMAL(23,10),
	last_paid date,
	deleted INT(1) DEFAULT '0',
	PRIMARY KEY (invoice_id) USING BTREE,
    CONSTRAINT `phppos_supplier_invoices_ibfk_1` FOREIGN KEY (`term_id`) REFERENCES `phppos_terms` (`term_id`),
    CONSTRAINT `phppos_supplier_invoices_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `phppos_suppliers` (`person_id`),
	CONSTRAINT `phppos_supplier_invoices_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `phppos_locations` (`location_id`)	
	
) ENGINE = INNODB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;


CREATE TABLE phppos_supplier_invoice_details (
	invoice_details_id INT(11) NOT NULL AUTO_INCREMENT,
	invoice_id INT(11) NOT NULL,
	line_id INT(11) NULL,
	receiving_id INT(11) NULL,
	description TEXT,
	total DECIMAL(23,10),
	account VARCHAR(255) COLLATE utf8_unicode_ci,
	PRIMARY KEY (invoice_details_id) USING BTREE,
    CONSTRAINT `phppos_supplier_invoice_details_ibfk_1` FOREIGN KEY (`receiving_id`) REFERENCES `phppos_receivings` (`receiving_id`),
    CONSTRAINT `phppos_supplier_invoice_details_ibfk_2` FOREIGN KEY (`invoice_id`) REFERENCES `phppos_supplier_invoices` (`invoice_id`)	
) ENGINE = INNODB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;


CREATE TABLE phppos_supplier_invoice_payments (
	payment_id INT(11) NOT NULL AUTO_INCREMENT,
	invoice_id INT(11) NULL,
    `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,	
    `payment_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `payment_amount` decimal(23,10) NOT NULL,
    `auth_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `ref_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `cc_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `acq_ref_data` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `process_data` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `entry_method` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `aid` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `tvr` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `iad` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `tsi` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `arc` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `cvm` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `tran_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `application_label` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `truncated_card` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
    `card_issuer` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
	PRIMARY KEY (payment_id) USING BTREE,
    CONSTRAINT `phppos_supplier_invoice_payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `phppos_supplier_invoices` (`invoice_id`)	
) ENGINE = INNODB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;



INSERT INTO `phppos_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `icon`, `module_id`) VALUES
('module_invoices', 'module_invoices_desc', 102, 'ti-receipt', 'invoices');

INSERT INTO `phppos_permissions` (`module_id`, `person_id`) (SELECT 'invoices', person_id FROM phppos_permissions WHERE module_id = 'sales');

INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('add', 'invoices', 'invoices_add', 240);

INSERT INTO phppos_permissions_actions (module_id, person_id, action_id)
SELECT DISTINCT phppos_permissions.module_id, phppos_permissions.person_id, action_id
from phppos_permissions
inner join phppos_modules_actions on phppos_permissions.module_id = phppos_modules_actions.module_id
WHERE phppos_permissions.module_id = 'invoices' and
action_id = 'add'
order by module_id, person_id;

INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('edit', 'invoices', 'invoices_edit', 245);

INSERT INTO phppos_permissions_actions (module_id, person_id, action_id)
SELECT DISTINCT phppos_permissions.module_id, phppos_permissions.person_id, action_id
from phppos_permissions
inner join phppos_modules_actions on phppos_permissions.module_id = phppos_modules_actions.module_id
WHERE phppos_permissions.module_id = 'invoices' and
action_id = 'edit'
order by module_id, person_id;


INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('delete', 'invoices', 'invoices_delete', 250);

INSERT INTO phppos_permissions_actions (module_id, person_id, action_id)
SELECT DISTINCT phppos_permissions.module_id, phppos_permissions.person_id, action_id
from phppos_permissions
inner join phppos_modules_actions on phppos_permissions.module_id = phppos_modules_actions.module_id
WHERE phppos_permissions.module_id = 'invoices' and
action_id = 'delete'
order by module_id, person_id;

INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('search', 'invoices', 'invoices_search', 255);

INSERT INTO phppos_permissions_actions (module_id, person_id, action_id)
SELECT DISTINCT phppos_permissions.module_id, phppos_permissions.person_id, action_id
from phppos_permissions
inner join phppos_modules_actions on phppos_permissions.module_id = phppos_modules_actions.module_id
WHERE phppos_permissions.module_id = 'invoices' and
action_id = 'search'
order by module_id, person_id;


INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('view_invoices_reports', 'reports', 'reports_invoices_reports', 265);
INSERT INTO phppos_permissions_actions (module_id, person_id, action_id)
SELECT DISTINCT phppos_permissions.module_id, phppos_permissions.person_id, action_id
from phppos_permissions
inner join phppos_modules_actions on phppos_permissions.module_id = phppos_modules_actions.module_id
WHERE phppos_permissions.module_id = 'reports' and
action_id = 'view_invoices_reports'
order by module_id, person_id;


-- default_terms_for_suppliers_and_customers --
ALTER TABLE phppos_customers ADD COLUMN default_term_id INT(11) NULL DEFAULT NULL;
ALTER TABLE phppos_customers ADD CONSTRAINT `phppos_customers_ibfk_5` FOREIGN KEY (`default_term_id`) REFERENCES `phppos_terms` (`term_id`);	


ALTER TABLE phppos_suppliers ADD COLUMN default_term_id INT(11) NULL DEFAULT NULL;
ALTER TABLE phppos_customers ADD CONSTRAINT `phppos_suppliers_ibfk_3` FOREIGN KEY (`default_term_id`) REFERENCES `phppos_terms` (`term_id`);	