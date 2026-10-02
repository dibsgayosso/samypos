<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_enable_manage_title extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20221101225423_enable_manage_title.sql'));
	    }

	    public function down() 
			{
	    }

	}