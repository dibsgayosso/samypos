<?php


class Squarehooks extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();	
		
	}
	
	function index()
	{
		if ($this->_validate_web_hook() && is_on_phppos_host())
		{			
			$data = json_decode(file_get_contents('php://input'), TRUE);
			$code = $data['data']['object']['device_code']['code'];
			$device_id = $data['data']['object']['device_code']['device_id'];
			
			$site_db = $this->load->database('site', TRUE);
			$site_db->from('square_devices');
			$site_db->where('code',$code);
			
			$row = $site_db->get()->row_array();
			
			$this->load->helper('cloud');
			switch_database($row['database_name']);
			
			$this->db->where('register_id', $row['register_id']);
			$this->db->update('registers',array('emv_terminal_id' => $device_id));
			
		}
	}
	
	function _validate_web_hook()
	{
		$headers = getallheaders();
		$headers = array_change_key_case($headers, CASE_UPPER);
		$signature = isset($headers["X-SQUARE-HMACSHA256-SIGNATURE"]) ? $headers["X-SQUARE-HMACSHA256-SIGNATURE"] : NULL;
		
		$body = file_get_contents('php://input');		
		$url = current_url();		
		$signature_key =  getenv('SQUARE_HOOKS_SIGNATURE_KEY');
		
		$hash = hash_hmac("sha256", $url.$body,$signature_key, true);
		
		if(base64_encode($hash) == $signature)
		{
			return TRUE;
		}
		
		return FALSE;
	}

}



?>