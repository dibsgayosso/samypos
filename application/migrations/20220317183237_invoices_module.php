<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_invoices_module extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220317183237_invoices_module.sql'));
	    }

	    public function down() 
			{
	    }

	}