ALTER TABLE `phppos_sales` ADD COLUMN `location_folio` INT(10) UNSIGNED DEFAULT NULL AFTER `sale_id`;

SET @row_number := 0;
SET @current_location := NULL;
UPDATE `phppos_sales` s
JOIN (
    SELECT sale_id, location_id,
        (@row_number := IF(@current_location = location_id, @row_number + 1, 1)) AS location_folio,
        (@current_location := location_id) AS dummy
    FROM `phppos_sales` s2
    JOIN (SELECT @row_number := 0, @current_location := NULL) vars
    ORDER BY s2.location_id, s2.sale_id
) seq ON seq.sale_id = s.sale_id
SET s.location_folio = seq.location_folio
WHERE s.location_folio IS NULL;

CREATE INDEX `phppos_sales_location_folio` ON `phppos_sales` (`location_id`,`location_folio`);
