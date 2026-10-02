<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_shopify_item_level_inventory_policy extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230217195873_shopify_item_level_inventory_policy.sql'));
	    }

	    public function down() 
			{
	    }

	}