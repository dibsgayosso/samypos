-- update_item_being_repaired --
update phppos_sales_items SET is_repair_item = 1 WHERE `line`=0 and sale_id IN(select sale_id FROM phppos_sales_work_orders);