<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_zatca_integration extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230217195928_zatca_integration.sql'));
	    }

	    public function down() 
			{
	    }

	}