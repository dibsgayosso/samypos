<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_covert_customer_phone_to_alphanumeric extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230216191705_covert_customer_phone_to_alphanumeric.sql'));
	    }

	    public function down() 
			{
	    }

	}