<?php
class MY_Session_database_driver extends CI_Session_database_driver {

	public function gc($maxlifetime)
	{
		parent::gc($maxlifetime);
		$this->cleanup_expired_files();
		$this->cleanup_expired_open_suspended_sales();
	}
	
	function cleanup_expired_files()
	{
		$return = TRUE;
		$cur_timezone = date_default_timezone_get();
		
		$CI =& get_instance();
		date_default_timezone_set('America/New_York');
		if ($CI->db->table_exists('app_files') && $CI->db->field_exists('expires','app_files'))
		{		
			$return = $CI->db->delete('app_files', 'expires < '.$CI->db->escape(date('Y-m-d H:i:s')).' and expires IS NOT NULL');
		}
		
		date_default_timezone_set($cur_timezone);
		return $return;
	}
	
	function cleanup_expired_open_suspended_sales()
	{
		$return = TRUE;
		$cur_timezone = date_default_timezone_get();
		
		$CI =& get_instance();
		date_default_timezone_set('America/New_York');
		if ($CI->db->table_exists('open_suspended_sales') && $CI->db->field_exists('expires','open_suspended_sales'))
		{		
			$return = $CI->db->delete('open_suspended_sales', 'expires < '.$CI->db->escape(date('Y-m-d H:i:s')).' and expires IS NOT NULL');
		}
		
		date_default_timezone_set($cur_timezone);
		return $return;
	}
	
}