<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_19_2_version extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230201211600_19_2_version.sql'));
	    }

	    public function down() 
			{
	    }

	}