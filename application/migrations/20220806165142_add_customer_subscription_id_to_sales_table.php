<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_add_customer_subscription_id_to_sales_table extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220806165142_add_customer_subscription_id_to_sales_table.sql'));
	    }

	    public function down() 
			{
	    }

	}