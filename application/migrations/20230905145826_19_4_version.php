<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_19_4_version extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230905145826_19_4_version.sql'));
	    }

	    public function down() 
			{
	    }

	}