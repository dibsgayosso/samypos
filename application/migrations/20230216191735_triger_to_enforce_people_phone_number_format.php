<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_triger_to_enforce_people_phone_number_format extends MY_Migration 
	{

	    public function up() 
		{
				$this->db->query(" 
				 					CREATE TRIGGER enforce_people_phone_format_on_update
				 					 BEFORE UPDATE ON phppos_people 
				 					 FOR EACH ROW BEGIN  
				 					      SET NEW.phone_number = alphanumplus(NEW.phone_number);
				 					  END;");

				 				$this->db->query(" 
				 					CREATE TRIGGER enforce_people_phone_format_on_insert
				 					 BEFORE INSERT ON phppos_people 
				 					 FOR EACH ROW BEGIN  
				 					      SET NEW.phone_number = alphanumplus(NEW.phone_number);
				 					END;");	    
		}

	    public function down() 
			{
	    }

	}