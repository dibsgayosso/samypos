<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_tax_cap extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230905145977_tax_cap.sql'));
	    }

	    public function down() 
			{
	    }

	}