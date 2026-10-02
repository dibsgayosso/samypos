<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_give_discount_permission_for_receivings extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220412090447_give_discount_permission_for_receivings.sql'));
	    }

	    public function down() 
			{
	    }

	}