<?php
function remote_addr_set()
{
	if ($ip = get_real_ip_address())
	{
		$_SERVER['REMOTE_ADDR'] = $ip;
	}
}
	
function clear_expired_session_data()
{
	$CI =& get_instance();
	//use $_SESSION because we need to see expire
	$sess_data = isset($_SESSION) ? $_SESSION : array();
	foreach($sess_data as $sess_key => $sess_data_row)
	{
		if (is_array($sess_data_row) && isset($sess_data_row['expire']))
		{
			if ($sess_data_row['expire'] <= time())
			{
				$CI->session->unset_userdata($sess_key);
			}
		}
	}		
	
}
function setup_mysql()
{
	$CI =& get_instance();

	//Makes sure we have a simple mode that doesn't have strict restrictions
	$CI->db->query('SET SESSION sql_mode=""');	
	
	//Fixes bug https://bugs.mysql.com/bug.php?id=79675
	$CI->db->query('SET SESSION optimizer_switch="index_merge_intersection=off"');
}

function setup_shopify()
{
	define('SHOPIFY_API_KEY',get_config_key_shared('shopify_public'));
	define('SHOPIFY_API_SECRET_KEY',get_config_key_shared('shopify_private'));
	
	define('SHOPIFY_PRICE',19);
}

//Loads configuration from database into global CI config
function load_config()
{	
	$CI =& get_instance();
	
	if ($CI->db->table_exists('app_config'))
	{
		foreach($CI->Appconfig->get_all()->result() as $app_config)
		{
			$CI->config->set_item($app_config->key,$app_config->value);
		
			if ($app_config->key == 'number_of_items_per_page' && $CI->agent->is_mobile())
			{
				$CI->config->set_item($app_config->key,20);			
			}
		}
	
		if($CI->session->userdata('language'))
		{
			$CI->lang->switch_to($CI->session->userdata('language'));
		}
		elseif($CI->Employee->is_logged_in() && $CI->Employee->get_logged_in_employee_info()->language)
		{
			$CI->lang->switch_to($CI->Employee->get_logged_in_employee_info()->language);
		}
		else if ($CI->config->item('language'))
		{
			$CI->lang->switch_to($CI->config->item('language'));
		}	
		date_default_timezone_set('America/New_York');
		define('BUILT_ON_DATE', date(get_date_format(). ' '.get_time_format(), BUILD_TIMESTAMP). ' EST'); 
	
		if ($CI->db->table_exists('locations'))
		{
			if ($CI->Location->get_info_for_key('timezone'))
			{
				date_default_timezone_set($CI->Location->get_info_for_key('timezone'));
			}
			else
			{
				$timezone = $CI->Location->get_info_for_key('timezone',1);
				if (!$timezone)
				{
					$timezone = 'America/New_York';
				}
		
				date_default_timezone_set($timezone);
			}
		}
	}
	
	
	$branding = $CI->config->item('branding');
	
	$CI->config->set_item('branding_code',$branding['code']);			
	$CI->config->set_item('branding_name',$branding['name']);			
	$CI->config->set_item('branding_short_name',$branding['short_name']);			
	
	$domain = $branding['domain'];
	$staging_domain = $branding['staging_domain'];
	
	$quickbooks_dev_client = $branding['quickbooks_dev_client'];
	$quickbooks_dev_secret = $branding['quickbooks_dev_secret'];
	$quickbooks_prod_client = $branding['quickbooks_prod_client'];
	$quickbooks_prod_secret = $branding['quickbooks_prod_secret'];
	
	
	/*
	|--------------------------------------------------------------------------
	| Custom Constants
	|--------------------------------------------------------------------------
	*/
	// Data Service URI
	define('DATA_SERVICE_REDIRECT_URI_1', "https://$staging_domain/qbonline_redirect.php");
	define('DATA_SERVICE_REDIRECT_URI_2', "https://$domain/qbonline_redirect.php");
	// Default Country Code (US)
	define('US_CODE', 'US');


	$clientID = (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? $quickbooks_dev_client : $quickbooks_prod_client;
	$clientSecret = (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? $quickbooks_dev_secret : $quickbooks_prod_secret;


	// Inventory Synch Needed value 1 be used to set the flag for syncing the current quantity of item in db in inventory table with QB.
	define('QUICKBOOKS_CLIENT_ID', $clientID);
	define('QUICKBOOKS_CLIENT_SECRET', $clientSecret);
				
				
	if (is_on_phppos_host())
	{
		$CI->config->set_item('ig_api_bearer_token',get_config_key_shared('ig_api_bearer_token'));			
		$CI->config->set_item('wgp_integration_pkey',get_config_key_shared('wgp_integration_pkey'));			
		$CI->config->set_item('p4_api_bearer_token',get_config_key_shared('p4_api_bearer_token'));			
				
	}
}
?>