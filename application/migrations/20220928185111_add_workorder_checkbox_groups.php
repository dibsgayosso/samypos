<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_add_workorder_checkbox_groups extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220928185111_add_workorder_checkbox_groups.sql'));
	    }

	    public function down() 
			{
	    }

	}