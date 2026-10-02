<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_work_order_template extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220330155452_work_order_template.sql'));
	    }

	    public function down() 
			{
	    }

	}