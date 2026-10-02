<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_update_item_being_repaired extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20221115200805_update_item_being_repaired.sql'));
	    }

	    public function down() 
			{
	    }

	}