<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_items_secondary_suppliers extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220125202329_items_secondary_suppliers.sql'));
	    }

	    public function down() 
			{
	    }

	}