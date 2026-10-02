<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_square_tokens extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20230708223891_square_tokens.sql'));
	    }

	    public function down() 
			{
	    }

	}