<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_enable_tips_per_register extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220507153515_enable_tips_per_register.sql'));
	    }

	    public function down() 
			{
	    }

	}