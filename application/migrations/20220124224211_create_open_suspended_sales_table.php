<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_create_open_suspended_sales_table extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220124224211_create_open_suspended_sales_table.sql'));
	    }

	    public function down() 
			{
	    }

	}