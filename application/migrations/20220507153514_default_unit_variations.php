<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_default_unit_variations extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220507153514_default_unit_variations.sql'));
	    }

	    public function down() 
			{
	    }

	}