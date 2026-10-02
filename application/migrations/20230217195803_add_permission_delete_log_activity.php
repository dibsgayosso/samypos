<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_add_permission_delete_log_activity extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230217195803_add_permission_delete_log_activity.sql'));
	    }

	    public function down() 
			{
	    }

	}