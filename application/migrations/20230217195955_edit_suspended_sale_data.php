<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_edit_suspended_sale_data extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230217195955_edit_suspended_sale_data.sql'));
	    }

	    public function down() 
			{
	    }

	}