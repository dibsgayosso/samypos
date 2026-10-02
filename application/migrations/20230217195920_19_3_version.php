<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_19_3_version extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230217195920_19_3_version.sql'));
	    }

	    public function down() 
			{
	    }

	}