<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_fix_items_quantity_unit_item_cost_price extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220125202328_fix_items_quantity_unit_item_cost_price.sql'));
	    }

	    public function down() 
			{
	    }

	}