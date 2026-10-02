<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_terms_and_conditions_coreclear extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220415211404_terms_and_conditions_coreclear.sql'));
	    }

	    public function down() 
			{
	    }

	}