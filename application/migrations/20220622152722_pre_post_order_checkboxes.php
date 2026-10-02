<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_pre_post_order_checkboxes extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220622152722_pre_post_order_checkboxes.sql'));
	    }

	    public function down() 
			{
	    }

	}