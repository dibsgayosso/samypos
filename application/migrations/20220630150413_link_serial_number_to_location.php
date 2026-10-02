<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_link_serial_number_to_location extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220630150413_link_serial_number_to_location.sql'));
	    }

	    public function down() 
			{
	    }

	}