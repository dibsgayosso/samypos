-- fix_items_quantity_unit_item_cost_price --
-- fix_items_quantity_unit_item_cost_price --
update phppos_sales_items SET item_cost_price = (subtotal-profit)/quantity_purchased WHERE unit_quantity IS NOT NULL and items_quantity_units_id IS NOT NULL and (item_unit_price-item_cost_price)*quantity_purchased < 0;