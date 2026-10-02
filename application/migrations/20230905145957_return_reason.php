<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_return_reason extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230905145957_return_reason.sql'));
	    }

	    public function down() 
			{
	    }

	}