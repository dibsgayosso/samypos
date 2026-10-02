<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_create_function_alphanum extends MY_Migration 
	{

	    public function up() 
		{
				$this->db->query('DROP FUNCTION IF EXISTS alphanumplus');
				 
				$this->db->query(" 
				CREATE FUNCTION alphanumplus( str CHAR(255) ) RETURNS CHAR(255) DETERMINISTIC
				BEGIN 
				  DECLARE i, len SMALLINT DEFAULT 1; 
				  DECLARE ret CHAR(255) DEFAULT ''; 
				  DECLARE c CHAR(1);
				  IF str IS NOT NULL THEN 
				    SET len = CHAR_LENGTH( str ); 
				    REPEAT 
				      BEGIN 
				        SET c = MID( str, i, 1 ); 
				        IF c REGEXP '[[:alnum:]\+]' THEN 
				          SET ret=CONCAT(ret,c); 
				        END IF; 
				        SET i = i + 1; 
				      END; 
				    UNTIL i > len END REPEAT; 
				  ELSE
				    SET ret='';
				  END IF;
				  RETURN ret; 
				END");
	    }

	    public function down() 
		{
	    }

	}