<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_fix_expenses_collation extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220112153040_fix_expenses_collation.sql'));
	    }

	    public function down() 
			{
	    }

	}