<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_add_override_tax_class_id_to_sales extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20221111122617_add_override_tax_class_id_to_sales.sql'));
	    }

	    public function down() 
			{
	    }

	}