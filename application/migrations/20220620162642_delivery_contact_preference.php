<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_delivery_contact_preference extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220620162642_delivery_contact_preference.sql'));
	    }

	    public function down() 
			{
	    }

	}