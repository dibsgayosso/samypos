<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_add_device_location_and_status_to_sales_items_notes extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220926023350_add_device_location_and_status_to_sales_items_notes.sql'));
	    }

	    public function down() 
			{
	    }

	}