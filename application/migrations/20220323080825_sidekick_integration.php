<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_sidekick_integration extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220323080825_sidekick_integration.sql'));
	    }

	    public function down() 
			{
	    }

	}