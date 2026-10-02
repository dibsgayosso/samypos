<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_work_order_authorization_signature extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220507153513_work_order_authorization_signature.sql'));
	    }

	    public function down() 
			{
	    }

	}