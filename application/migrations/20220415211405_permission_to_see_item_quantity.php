<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_permission_to_see_item_quantity extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220415211405_permission_to_see_item_quantity.sql'));
	    }

	    public function down() 
			{
	    }

	}