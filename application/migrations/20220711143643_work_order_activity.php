<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_work_order_activity extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220711143643_work_order_activity.sql'));
	    }

	    public function down() 
			{
	    }

	}