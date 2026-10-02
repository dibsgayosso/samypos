<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_zatca_integration_add_reference_invoice extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230217195925_zatca_integration_add_reference_invoice.sql'));
	    }

	    public function down() 
			{
	    }

	}