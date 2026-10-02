<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_fix_navigation_issue extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220415211402_fix_navigation_issue.sql'));
	    }

	    public function down() 
			{
	    }

	}