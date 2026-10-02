<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_add_approved_and_assigned_sales_item_kits extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230217195841_add_approved_and_assigned_sales_item_kits.sql'));
	    }

	    public function down() 
			{
	    }

	}