<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_add_send_sms_via_whatsapp_to_location extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/'.'20220415211403_add_send_sms_via_whatsapp_to_location.sql'));
	    }

	    public function down() 
			{
	    }

	}