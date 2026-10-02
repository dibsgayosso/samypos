<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_price_rules_day_of_week extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230905145892_price_rules_day_of_week.sql'));
	    }

	    public function down() 
			{
	    }

	}