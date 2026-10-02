<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_is_repair_item_in_sales_item_kits extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230214162614_is_repair_item_in_sales_item_kits.sql'));
	    }

	    public function down() 
			{
	    }

	}