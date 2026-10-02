<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_locations_additional_appointment_note extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220329144711_locations_additional_appointment_note.sql'));
	    }

	    public function down() 
			{
	    }

	}