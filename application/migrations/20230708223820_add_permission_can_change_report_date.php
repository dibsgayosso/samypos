<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_add_permission_can_change_report_date extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230708223820_add_permission_can_change_report_date.sql'));
	    }

	    public function down() 
			{
	    }

	}