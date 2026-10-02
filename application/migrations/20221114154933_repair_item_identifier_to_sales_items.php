<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_repair_item_identifier_to_sales_items extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20221114154933_repair_item_identifier_to_sales_items.sql'));
	    }

	    public function down() 
			{
	    }

	}