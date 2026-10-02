<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_19_0_version extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220112153039_19_0_version.sql'));
	    }

	    public function down() 
			{
	    }

	}