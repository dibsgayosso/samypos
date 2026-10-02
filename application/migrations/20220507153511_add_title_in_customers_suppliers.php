<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_add_title_in_customers_suppliers extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220507153511_add_title_in_customers_suppliers.sql'));
	    }

	    public function down() 
			{
	    }

	}