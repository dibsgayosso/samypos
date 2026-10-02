<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_add_approved_by_and_assigned_to_to_sale_items extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220831182922_add_approved_by_and_assigned_to_to_sale_items.sql'));
	    }

	    public function down() 
			{
	    }

	}