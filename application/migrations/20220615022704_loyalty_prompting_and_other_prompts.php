<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_loyalty_prompting_and_other_prompts extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220615022704_loyalty_prompting_and_other_prompts.sql'));
	    }

	    public function down() 
			{
	    }

	}