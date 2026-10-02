<?php
require_once(APPPATH . "libraries/blockchyp/vendor/autoload.php");
require_once (APPPATH."traits/subscriptionProcessingTrait.php");

use \BlockChyp\BlockChyp;

class Cron extends MY_Controller
{
	use subscriptionProcessingTrait;

	function __construct()
    {
		//TODO add protection since can be run via http
        parent::__construct();
    }
	
    function process_recurring_payments($base_url = '', $db_override = '')
    {
		if (!is_cli())
		{
			die('must be cli');
		}
		
		$this->load->model('Customer_subscription');
        ignore_user_abort(TRUE);
        set_time_limit(0);
        ini_set('max_input_time', '-1');
        session_write_close();
        
        //Cron's always run on current server path; but if we are between migrations we should run the cron on the previous folder passing along any arguements
        if (defined('SHOULD_BE_ON_OLD') && SHOULD_BE_ON_OLD) {
            global $argc, $argv;
            $prev_folder = isset($_SERVER['CI_PREV_FOLDER']) ? $_SERVER['CI_PREV_FOLDER'] : 'PHP-Point-Of-Sale-Prev';
            system('php ' . FCPATH . "$prev_folder/index.php cron process_recurring_payments " . $argv[3] . $prev_folder . '/ ' . $argv[4]);
            exit();
        }
		
		date_default_timezone_set($this->Location->get_info_for_key('timezone',1));
		
		
		$subs_to_process = $this->Customer_subscription->get_subs_to_process();
			
		foreach($subs_to_process as $sub)
		{
			$this->process_sub($sub);
		}    
    }
	
	function get_accounts()
	{
		error_reporting(E_ALL);
		set_time_limit(0);
		$db_host = 'php-pos-db.phppointofsale.com';
		$db_user= $this->db->username;
		$db_password = $this->db->password;

		$exclude_dbs = array('horde',$db_user.'_forums',$db_user.'_site','roundcube', 'pos', 'bntennis_site', 'mysql', 'information_schema', 'performance_schema');

		$conn = mysqli_connect($db_host, $db_user, $db_password);
		$show_db_query = mysqli_query($conn, 'SHOW databases');
		$accounts = array();
		while ($row = mysqli_fetch_assoc($show_db_query)) 
		{
		 	if (!in_array($row['Database'], $exclude_dbs))
			{
				$accounts[] = str_replace($db_user.'_','',$row['Database']);
			}
		}

		$db_host = 'php-pos-db-2.phppointofsale.com';

		$conn = mysqli_connect($db_host, $db_user, $db_password);
		$show_db_query = mysqli_query($conn, 'SHOW databases');
		while ($row = mysqli_fetch_assoc($show_db_query)) 
		{
		 	if (!in_array($row['Database'], $exclude_dbs))
			{
				$accounts[] = str_replace($db_user.'_','',$row['Database']);
			}
		}
		return $accounts;
		
	}
	
	
	function run_all_recurring_payments($cron_key)
	{
		if ($cron_key!= getenv('CRON_KEY'))
		{
			die('cannot acccess key key does not match');
		}
		
		$this->load->helper('command');
		$db_user= $this->db->username;
		$account = str_replace($db_user.'_','',$this->db->database);
		$domain = $this->config->item('branding')['domain'];
		$phppos_url = "https://".$account.'.'.$domain.'/';
		
		$command = 'php '.FCPATH."index.php cron do_run_all_recurring_payments $phppos_url $account";
		run_command_in_background($command);
	}
	function do_run_all_recurring_payments($base_url = '', $db_override = '')
	{
		if (!is_cli())
		{
			die('must be cli');
		}
		
		ini_set('display_errors', 1);
		error_reporting(E_ALL);
		set_time_limit(0);
		$accounts = $this->get_accounts();
		
		foreach($accounts as $account)
		{
			echo date('m/d/Y h:i:s ').": Running Recurring payments for account $account"."\n"; 
	
			$domain = $this->config->item('branding')['domain'];
			$phppos_url = "https://".$account.'.'.$domain.'/';
			exec('php '.FCPATH."index.php cron process_recurring_payments $phppos_url $account");	
		}
	}
	
	
	function run_all_reports_mailer($cron_key)
	{
		if ($cron_key!= getenv('CRON_KEY'))
		{
			die('cannot acccess key key does not match');
		}
		
		$this->load->helper('command');
		$db_user= $this->db->username;
		$account = str_replace($db_user.'_','',$this->db->database);
		$domain = $this->config->item('branding')['domain'];
		$phppos_url = "https://".$account.'.'.$domain.'/';
		
		$command = 'php '.FCPATH."index.php cron do_run_all_reports_mailer $phppos_url $account";
		run_command_in_background($command);
	}
	
	function do_run_all_reports_mailer($base_url = '', $db_override = '')
	{
		if (!is_cli())
		{
			die('must be cli');
		}
		
		ini_set('display_errors', 1);
		error_reporting(E_ALL);
		set_time_limit(0);
		$accounts = $this->get_accounts();		
		
		foreach($accounts as $account)
		{
			echo date('m/d/Y h:i:s ').": Running E-Mailer for account $account"."\n"; 
	
			$domain = $this->config->item('branding')['domain'];
			$phppos_url = "https://".$account.'.'.$domain.'/';
			exec('php '.FCPATH."index.php reportsmailer cron $phppos_url $account");	
		}
	}
	
	function run_ecommerce_cron($cron_key)
	{
		if ($cron_key!= getenv('CRON_KEY'))
		{
			die('cannot acccess key key does not match');
		}
		
		$this->load->helper('command');
		$base_url = base_url();
		$db_override = str_replace($this->db->username.'_','',$this->db->database);
		$command = 'php '.FCPATH."index.php ecommerce cron $base_url $db_override";
		run_command_in_background($command);
	}
	
	function run_quickbooks_cron($cron_key)
	{
		if ($cron_key!= getenv('CRON_KEY'))
		{
			die('cannot acccess key key does not match');
		}
		
		$this->load->helper('command');
		$base_url = base_url();
		$db_override = str_replace($this->db->username.'_','',$this->db->database);
		$command = 'php '.FCPATH."index.php quickbooks cron $base_url $db_override";
		run_command_in_background($command);
	}
	
	function run_zatca_cron($cron_key)
	{
		//TODO
		// if ($cron_key!= getenv('CRON_KEY'))
		// {
		// 	die('cannot acccess key key does not match');
		// }

		$this->load->helper('command');
		$base_url = base_url();
		$db_override = str_replace($this->db->username.'_','',$this->db->database);
		$command = 'php '.FCPATH."index.php zatca cron $base_url $db_override";
		shell_exec($command);
	}
}
?>
