<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_permission_to_add_remove_amounts_from_cash_drawer extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220506221239_permission_to_add_remove_amounts_from_cash_drawer.sql'));
	    }

	    public function down() 
			{
	    }

	}