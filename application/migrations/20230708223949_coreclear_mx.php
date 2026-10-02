<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_coreclear_mx extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230708223949_coreclear_mx.sql'));
	    }

	    public function down() 
			{
	    }

	}