<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_19_1_version extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220615022703_19_1_version.sql'));
	    }

	    public function down() 
			{
	    }

	}