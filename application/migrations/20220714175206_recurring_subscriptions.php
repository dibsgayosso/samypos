<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_recurring_subscriptions extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220714175206_recurring_subscriptions.sql'));
	    }

	    public function down() 
			{
	    }

	}