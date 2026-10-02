-- zatca_integration --

CREATE TABLE `phppos_zatca_config` (
  `location_id` int(10) NOT NULL,
  `csr_common_name` text COLLATE utf8_unicode_ci NOT NULL,
  `csr_serial_number` text COLLATE utf8_unicode_ci NOT NULL,
  `csr_organization_identifier` text COLLATE utf8_unicode_ci NOT NULL,
  `csr_organization_unit_name` text COLLATE utf8_unicode_ci NOT NULL,
  `csr_organization_name` text COLLATE utf8_unicode_ci NOT NULL,
  `csr_country_name` text COLLATE utf8_unicode_ci NOT NULL,
  `csr_invoice_type` text COLLATE utf8_unicode_ci NOT NULL,
  `csr_location_address` text COLLATE utf8_unicode_ci NOT NULL,
  `csr_industry_business_category` text COLLATE utf8_unicode_ci NOT NULL,
  `seller_party_postal_street_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `seller_party_postal_building_number` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `seller_party_postal_code` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `seller_party_postal_city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `seller_party_postal_district` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `seller_party_postal_plot_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `seller_party_postal_country` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `seller_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `seller_scheme_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `seller_tax_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `csr` text COLLATE utf8_unicode_ci NOT NULL,
  `csr_private_key` text COLLATE utf8_unicode_ci NOT NULL,
  `private_key` text COLLATE utf8_unicode_ci NOT NULL,
  `cert` text COLLATE utf8_unicode_ci NOT NULL,
  `compliance_csid` text COLLATE utf8_unicode_ci NOT NULL,
  `production_csid` text COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `location_id` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `phppos_zatca_invoices` (
  `invoice_id` int(10) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) NOT NULL,
  `sale_id` int(10) NOT NULL,
  `PIH` text COLLATE utf8_unicode_ci NOT NULL,
  `hash` text COLLATE utf8_unicode_ci NOT NULL,
  `qr_code` text COLLATE utf8_unicode_ci NOT NULL,
  `invoice_data` text COLLATE utf8_unicode_ci NOT NULL,
  `invoice_type_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `invoice_subtype` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `invoice_xml` text COLLATE utf8_unicode_ci NOT NULL,
  `invoice_xml_sign` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `validate` tinyint(1) NOT NULL DEFAULT 0,
  `invoice_request` text COLLATE utf8_unicode_ci NOT NULL,
  `clearance_response` text COLLATE utf8_unicode_ci NOT NULL,
  `reporting_response` text COLLATE utf8_unicode_ci NOT NULL,
  `reported` tinyint(4) NOT NULL,
  `check_compliance` tinyint(4) NOT NULL,
  `issue_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (invoice_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `phppos_customers_zatca` (
  `customer_id` int(10) NOT NULL,
  `buyer_party_postal_street_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `buyer_party_postal_building_number` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `buyer_party_postal_code` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `buyer_party_postal_city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `buyer_party_postal_district` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `buyer_party_postal_plot_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `buyer_party_postal_country` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `buyer_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `buyer_scheme_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `buyer_tax_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;