<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_coreclear_merchant_id extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220324034537_coreclear_merchant_id.sql'));
	    }

	    public function down() 
			{
	    }

	}