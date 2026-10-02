<?php
class Yaml extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();	
	}
	
	function index()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Max-Age: 1000");
		header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
		header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
		$this->load->view('api_yaml',array('domain' => $this->config->item('branding')['domain'],'name' => $this->config->item('branding')['name'],'short_name' => $this->config->item('branding')['short_name']));
	}
	
}