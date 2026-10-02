<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_work_order_authorization_template extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220507153512_work_order_authorization_template.sql'));
	    }

	    public function down() 
			{
	    }

	}