<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_store_all_credit_card_transactions_charged extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230211131512_store_all_credit_card_transactions_charged.sql'));
	    }

	    public function down() 
			{
	    }

	}