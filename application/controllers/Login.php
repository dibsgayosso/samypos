<?php
require_once (APPPATH."libraries/google2fa/vendor/autoload.php");
require_once (APPPATH."libraries/bacon-qr-code/vendor/autoload.php");
require_once (APPPATH."libraries/php-saml/vendor/autoload.php");
require_once (APPPATH."libraries/oidc/vendor/autoload.php");

use PragmaRX\Google2FAQRCode\Google2FA;
use Jumbojett\OpenIDConnectClient;

class Login extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->lang->load('login');
		$this->load->helper('cloud');
	}
	
	function index()
	{
		$data = array();
		$this->load->helper('demo');
		$data['username'] = is_on_demo_host() ? 'admin' : '';
		$data['password'] = is_on_demo_host() ? 'pointofsale' : '';
		if ($this->agent->browser() == 'Internet Explorer' && $this->agent->version() < 11)
		{
			$data['ie_browser_warning'] = TRUE;
		}
		else
		{
			$data['ie_browser_warning'] = FALSE;
		}
		
		$this->load->helper('update');
		if(!is_on_phppos_host() && (APPLICATION_VERSION!=$this->config->item('version') || ($this->migration->get_migration_version() != $this->migration->get_version())))
		{
			redirect('migrate/start');
		}
		
		if($this->Employee->is_logged_in())
		{
			redirect('home');
		}
		else
		{
			$this->form_validation->set_rules('username', 'lang:login_username', 'required|callback_employee_location_check|callback_login_check');
			$this->form_validation->set_message('required', lang('login_invalid_username_and_password'));
    	   $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			if($this->form_validation->run() == FALSE)
			{
				//Only set the username when we have a non false value (not '' or FALSE)
				if ($this->input->post('username'))
				{					
					$data['username'] = $this->input->post('username');
				}
				
				$this->load->helper('update');
				if (is_on_phppos_host())
				{
					$site_db = $this->load->database('site', TRUE);
					if (!is_on_demo_host())
					{
						$data['announcement'] = get_cloud_announcement($site_db);
					}
					
					if (is_subscription_cancelled($site_db) || is_subscription_failed($site_db) || is_in_trial($site_db))
					{
						$data['cloud_customer_info'] = get_cloud_customer_info($site_db);
						
						if (is_in_trial($site_db))
						{
							if (is_trial_over($site_db))
							{
								$data['trial_over']  = TRUE;
								$this->load->view('login/trial_over',$data);
							}
							else
							{
								$data['trial_on']  = TRUE;
								$this->load->view('login/login',$data);
							}
						}
						elseif (is_subscription_failed($site_db))
						{
							$data['subscription_payment_failed']  = TRUE;
							$this->load->view('login/login',$data);							
						}
						elseif (is_subscription_cancelled_within_grace_period($site_db))
						{
							$data['subscription_cancelled_within_5_days']  = TRUE;
							$this->load->view('login/login',$data);
						}
						else
						{
							$this->load->view('login/subscription_cancelled', $data);
						}
					}
					else
					{
						$this->load->view('login/login', $data);
					}
				}
				else
				{
					$this->load->view('login/login',$data);
				}
				
				if (isset($site_db) && $site_db)
				{
					$site_db->close();
				}			
			}
			else
			{
				
				$logged_in_employee_info=$this->Employee->get_logged_in_employee_info();
				
				if ($logged_in_employee_info->force_password_change)
				{
					$this->Employee->logout(false);
					$data['username'] = $logged_in_employee_info->username;
					//Create key on the fly
					$data['key'] = $this->generate_reset_key($logged_in_employee_info->person_id);
					$data['force_password_change'] = TRUE;
					$this->load->view('login/reset_password_enter_password', $data);			
				}
				else
				{
					if($this->config->item('allow_employees_to_use_2fa') && $logged_in_employee_info->secret_key_2fa){
						$this->Employee->logout(false);
						$data['username'] = $logged_in_employee_info->username;
						$this->load->view('login/2fa_verify', $data);
					}else{

						session_regenerate_id();
						$number_of_locations = count($this->Employee->get_authenticated_location_ids($logged_in_employee_info->person_id));
						
						if ($this->input->get('continue'))
						{
							$continue = rtrim($this->input->get('continue'),'?');
							redirect('/'.$continue);	
						}
						else
						{
							redirect('home/index/'.($number_of_locations > 1 ? '1' : '0'));
						}
					}
				}
			}
		}
	}
		
	function login_check($username)
	{
		$this->load->helper('update');
		if (is_on_phppos_host())
		{
			$site_db = $this->load->database('site', TRUE);
		
			if (is_subscription_cancelled($site_db))
			{
				//If we are not cancelled within 5 days; block login
				if (!is_subscription_cancelled_within_grace_period($site_db))
				{
					$this->form_validation->set_message('login_check', lang('login_invalid_username_and_password'));
					return false;
				}
			}
			if (isset($site_db) && $site_db)
			{
				$site_db->close();
			}
		}
		$password = $this->input->post("password");	
		
		if(!$this->Employee->login($username,$password))
		{
			if ($this->Employee->login_failed_time_period($username,$password))
			{
				$this->form_validation->set_message('login_check', lang('login_you_are_not_allowed_to_login_at_this_time'));
			}
			else
			{
				$this->form_validation->set_message('login_check', lang('login_invalid_username_and_password'));
			}
			
			return false;
		}
		return true;		
	}
	
	function employee_location_check($username)
	{		
		$employee_id = $this->Employee->get_employee_id($username);
		
		if ($employee_id)
		{
			$employee_location_count = count($this->Employee->get_authenticated_location_ids($employee_id));

			if ($employee_location_count < 1)
			{
				$this->form_validation->set_message('employee_location_check', lang('login_employee_is_not_assigned_to_any_locations'));
				return false;
			}
		}
		
		//Didn't find an employee, we can pass validation
		return true;
	}
		
	function can_fast_switch()
	{
		$allowed = $this->_can_fast_switch_user($this->input->post('username'));
		
		echo json_encode(array('allowed' => $allowed));
	}
	
	function _can_fast_switch_user($username)
	{
		$emp_id = $this->Employee->get_employee_id($username);
		
		if ($emp_id)
		{
			$emp_info = $this->Employee->get_info($emp_id);
			return !$emp_info->always_require_password && $this->config->item('fast_user_switching');
		}
		
		return false;
	}
	
	private function reset_sold_by_employee($previous_logged_in_employee_id)
	{
		$cart = PHPPOSCartSale::get_instance('sale');
		
		if ($this->config->item('default_sales_person') != 'not_set' && $cart->sold_by_employee_id == $previous_logged_in_employee_id)
		{
			$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
			$cart->sold_by_employee_id = $employee_id;
			$cart->save();
		}
		elseif($this->config->item('default_sales_person') == 'not_set')
		{
			$cart->sold_by_employee_id = NULL;
			$cart->save();
		}
	}
	
	function switch_user($reload = 0)
	{
		if ($this->Employee->is_logged_in())
		{
			require_once (APPPATH."models/cart/PHPPOSCartSale.php");
		
			$previous_logged_in_employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
			$username = $this->input->post('username_or_account_number');
		
			if (!$username)
			{
				$username = $this->input->post('username');
			}
		
			if($username && $this->_can_fast_switch_user($username))
			{
				if (!$this->Employee->login_no_password($username))
				{
					echo json_encode(array('success'=>false,'message'=>lang('login_invalid_username_and_password')));
				}
				else
				{
					
					$default_register = $this->Employee->getDefaultRegister($this->Employee->get_logged_in_employee_info()->person_id,$this->Employee->get_logged_in_employee_current_location_id());
					
					if ($default_register)
					{
						$this->Employee->set_employee_current_register_id($default_register['register_id']);
					}
					
					if ($this->config->item('reset_location_when_switching_employee'))
					{
						//Unset location in case the user doesn't have access to currently set location
						$this->session->unset_userdata('employee_current_location_id');							
					}
					$this->reset_sold_by_employee($previous_logged_in_employee_id);
					$emp_info = $this->Employee->get_logged_in_employee_info();
					$name = $emp_info->first_name. ' '.$emp_info->last_name;
					$avatar = $emp_info->image_id ?  secure_app_file_url($emp_info->image_id) : base_url('assets/assets/images/avatar-default.jpg');
					$is_clocked_in_or_timeclock_disabled = $this->Employee->get_logged_in_employee_info()->not_required_to_clock_in || $this->Employee->is_clocked_in() || !$this->config->item('timeclock');
					echo json_encode(array('success'=>true,'reload' => $reload,'name' => $name,'avatar' => $avatar,'is_clocked_in_or_timeclock_disabled' => $is_clocked_in_or_timeclock_disabled));
				}
			}
			elseif($username)
			{
				if(!$this->Employee->login($username,$this->input->post('password')))
				{
					echo json_encode(array('success'=>false,'message'=>lang('login_invalid_username_and_password')));
				}
				else
				{
					
					$default_register = $this->Employee->getDefaultRegister($this->Employee->get_logged_in_employee_info()->person_id,$this->Employee->get_logged_in_employee_current_location_id());
					
					if ($default_register)
					{
						$this->Employee->set_employee_current_register_id($default_register['register_id']);
					}
					
					if ($this->config->item('reset_location_when_switching_employee'))
					{
						//Unset location in case the user doesn't have access to currently set location
						$this->session->unset_userdata('employee_current_location_id');							
					}
				
					$this->reset_sold_by_employee($previous_logged_in_employee_id);
					$is_clocked_in_or_timeclock_disabled = $this->Employee->get_logged_in_employee_info()->not_required_to_clock_in || $this->Employee->is_clocked_in() || !$this->config->item('timeclock');
				
					$emp_info = $this->Employee->get_logged_in_employee_info();
					$name = $emp_info->first_name. ' '.$emp_info->last_name;
					$avatar = $emp_info->image_id ?  secure_app_file_url($emp_info->image_id) : base_url('assets/assets/images/avatar-default.jpg');

				
					echo json_encode(array('success'=>true,'reload' => $reload, 'name'=> $name,'avatar' => $avatar,'is_clocked_in_or_timeclock_disabled' => $is_clocked_in_or_timeclock_disabled));
				}
			}
			else
			{
				foreach($this->Employee->get_all()->result_array() as $row)
				{
					$employees[$row['username']] = $row['first_name'] .' '. $row['last_name'];
				}
				$data['employees']=$employees;
				$data['reload'] = $reload;
				$this->load->view('login/switch_user',$data);
			}
		}
	}
			
	function reset_password()
	{
		$this->load->view('login/reset_password');
	}
	
	function do_reset_password_notify()
	{	
		if($this->input->post('username_or_email'))
		{
			$employee = $this->Employee->get_employee_by_username_or_email($this->input->post('username_or_email'));
			if ($employee)
			{
				$data = array();
				$data['employee'] = $employee;
			   $data['reset_key'] = $this->generate_reset_key($employee->person_id);
			
				$this->load->library('email');
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
				$this->email->from($this->config->item('branding')['no_reply_email'], $this->config->item('company'));
				$this->email->to($employee->email); 
				
				if($this->Location->get_info_for_key('cc_email'))
				{
					$this->email->cc($this->Location->get_info_for_key('cc_email'));
				}
				
				if($this->Location->get_info_for_key('bcc_email'))
				{
					$this->email->bcc($this->Location->get_info_for_key('bcc_email'));
				}
				
				$this->email->subject(lang('login_reset_password'));
				$this->email->message($this->load->view("login/reset_password_email",$data, true));	
				$this->email->send();
			
				$data['success']=lang('login_password_reset_has_been_sent');
				$this->load->view('login/reset_password',$data);
			}
			else 
			{
				$data['error']=lang('login_username_or_email_does_not_exist');
				$this->load->view('login/reset_password',$data);
			}
		}
		else
		{
			$data['error']= lang('common_field_cannot_be_empty');
			$this->load->view('login/reset_password',$data);
		}
	}
	
	function reset_password_enter_password($key=false)
	{
		if ($key)
		{
			$data = array();
			$reset_info = $this->get_reset_info($key);
			
			if ($reset_info)
			{
				$employee_id = $reset_info->employee_id;
				$expire = strtotime($reset_info->expire);
						 
				if ($employee_id && $expire && $expire > time())
				{
					$employee = $this->Employee->get_info($employee_id);
					$data['username'] = $employee->username;
					$data['key'] = $key;
					$this->load->view('login/reset_password_enter_password', $data);			
				}
			}
		}
	}
	
	function get_reset_info($key)
	{
		$this->db->from('employees_reset_password');
		$this->db->where('key',$key);
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		
		return FALSE;
	}
	
	function generate_reset_key($employee_id)
	{
		if (function_exists('openssl_random_pseudo_bytes'))
		{
			$key = bin2hex(openssl_random_pseudo_bytes(16));
		}
		else
		{
			$key = md5(rand());
		}
		if($this->db->insert('employees_reset_password',
		array(
			'employee_id' => $employee_id, 
			'key' => $key, 
			'expire' => date('Y-m-d H:i:s', strtotime("+3 day")))))
		{
			return $key;
		}
		
		return FALSE;
	}
	
	function delete_reset_key($key)
	{
		return $this->db->delete('employees_reset_password', array('key' => $key)); 
	}
	
	function do_reset_password($key=false)
	{
		if ($key)
		{
			$reset_info = $this->get_reset_info($key);
			
			if ($reset_info)
			{
				$employee_id = $reset_info->employee_id;
				$expire = strtotime($reset_info->expire);
				
				if ($employee_id && $expire && $expire > time())
				{
					$password = $this->input->post('password');
					$confirm_password = $this->input->post('confirm_password');
			
					if (($password == $confirm_password) && strlen($password) >=8)
					{
						if ($this->Employee->update_employee_password($employee_id, md5($password)))
						{
							$this->delete_reset_key($key);
							$this->load->view('login/do_reset_password');	
						}
					}
					else
					{
						$data = array();
						$employee = $this->Employee->get_info($employee_id);
						$data['username'] = $employee->username;
						$data['key'] = $key;
						$data['force_password_change'] = $this->input->post('force_password_change') ? TRUE : FALSE;
						$data['error_message'] = lang('login_passwords_must_match_and_be_at_least_8_characters');
						$this->load->view('login/reset_password_enter_password', $data);
					}
				}
			}
		}
	}
	
	function is_update_available()
	{
		session_write_close();
		$this->load->helper('update');
		echo json_encode(is_phppos_update_available());
	}
	
	function auth_only() {
		$data = array();

		$this->load->helper('update');

		if($this->Employee->is_logged_in())
		{
			$return = array("success" => true);
		}
		else
		{
			$this->form_validation->set_rules('username', 'lang:login_username', 'required|callback_employee_location_check|callback_login_check');
			$this->form_validation->set_message('required', lang('login_invalid_username_and_password'));

			$this->form_validation->set_error_delimiters('', '');
			if($this->form_validation->run() == FALSE)
			{
				//Only set the username when we have a non false value (not '' or FALSE)
				if ($this->input->post('username'))
				{
					$data['username'] = $this->input->post('username');
				}

				$return = array("success" => false,'error_message'=>validation_errors());
			}
			else
			{
				$return = array("success" => true);
			}
		}

		if($return['success']){
			$return['currentLocationId'] = $this->Employee->get_logged_in_employee_current_location_id();
			$return['currentLocationName'] = $this->Location->get_info_for_key('name');
			$return['authenticatedLocations'] = $this->Employee->get_authenticated_locations($this->Employee->get_logged_in_employee_info()->person_id);
		}

		echo json_encode($return);
	}

	function do_verify_2fa()
	{
		$username = $this->input->post('username');
		$security_code = $this->input->post('security_code');

		$employee_id = $this->Employee->get_employee_id($username);
		$secret_key = $this->Employee->get_info($employee_id)->secret_key_2fa;

		if ($secret_key)
		{
			$google2fa = new Google2FA();

			$isValid  = $google2fa->verifyKey($secret_key, $security_code);
			if($isValid){
				$this->session->set_userdata('person_id', $employee_id);
				$number_of_locations = count($this->Employee->get_authenticated_location_ids($employee_id));
				$allowed_modules=$this->Module->get_allowed_modules($employee_id)->result_array();

				//when the Price Check user logs in, open the app directly to the Price Check screen
				if(count($allowed_modules) == 1 && $allowed_modules[0]['module_id'] == 'price_check'){
					redirect('price_check');
				}
				else{
					if ($this->input->get('continue'))
					{
						$continue = rtrim($this->input->get('continue'),'?');
						redirect('/'.$continue);
					}
					else
					{
						redirect('home/index/'.($number_of_locations > 1 ? '1' : '0'));
					}
				}
			}
			else{
				$data['username'] = $username;
				$data['error_message'] = lang('common_code_invalid');
				$this->load->view('login/2fa_verify', $data);
			}
		}
	}
	
	function samlmetadata()
	{
		try {
						
		    $settings = new \OneLogin\Saml2\Settings($this->_saml_get_settings(), true);
		    $metadata = $settings->getSPMetadata();
		    $errors = $settings->validateMetadata($metadata);
		    if (empty($errors)) {
		        header('Content-Type: text/xml');
		        echo $metadata;
		    } else {
		        throw new OneLogin_Saml2_Error(
		            'Invalid SP metadata: '.implode(', ', $errors),
		            OneLogin_Saml2_Error::METADATA_SP_INVALID
		        );
		    }
		} catch (Exception $e) {
		    echo $e->getMessage();
		}
	}
		
	function samlassertionconsumerservice()
	{
		$auth = new \OneLogin\Saml2\Auth($this->_saml_get_settings());
		
		if (isset($_GET['sso'])) 
		{
		    $auth->login();
		}
		else if (isset($_GET['slo'])) 
		{
		    $returnTo = null;
		    $parameters = array();
		    $nameId = null;
		    $sessionIndex = null;
		    $nameIdFormat = null;
		    $samlNameIdNameQualifier = null;
		    $samlNameIdSPNameQualifier = null;

		    if (isset($_SESSION['samlNameId'])) {
		        $nameId = $_SESSION['samlNameId'];
		    }
		    if (isset($_SESSION['samlNameIdFormat'])) {
		        $nameIdFormat = $_SESSION['samlNameIdFormat'];
		    }
		    if (isset($_SESSION['samlNameIdNameQualifier'])) {
		        $samlNameIdNameQualifier = $_SESSION['samlNameIdNameQualifier'];
		    }
		    if (isset($_SESSION['samlNameIdSPNameQualifier'])) {
		        $samlNameIdSPNameQualifier = $_SESSION['samlNameIdSPNameQualifier'];
		    }
		    if (isset($_SESSION['samlSessionIndex'])) {
		        $sessionIndex = $_SESSION['samlSessionIndex'];
		    }

		    $auth->logout($returnTo, $parameters, $nameId, $sessionIndex, false, $nameIdFormat, $samlNameIdNameQualifier, $samlNameIdSPNameQualifier);
			$this->Employee->logout(true);

		} 
		else if (isset($_GET['acs'])) 
		{
		    $auth->processResponse(NULL);

		    $errors = $auth->getErrors();
		    if (!$auth->isAuthenticated()) 
			{
				$this->Employee->logout(true);
		    }

		    $_SESSION['samlUserdata'] = $auth->getAttributes();
		    $_SESSION['samlNameId'] = $auth->getNameId();
		    $_SESSION['samlNameIdFormat'] = $auth->getNameIdFormat();
		    $_SESSION['samlNameIdNameQualifier'] = $auth->getNameIdNameQualifier();
		    $_SESSION['samlNameIdSPNameQualifier'] = $auth->getNameIdSPNameQualifier();
		    $_SESSION['samlSessionIndex'] = $auth->getSessionIndex();
			
			if (!$this->Employee->employee_email_exists($_SESSION['samlNameId']))
			{
				$first_name = 'Unknown';
				$last_name = 'Unknown';
				$email = $_SESSION['samlNameId'];
				
				
				if (isset($_SESSION['samlUserdata'][$this->config->item('saml_first_name_field')][0]))
				{
					$first_name = $_SESSION['samlUserdata'][$this->config->item('saml_first_name_field')][0];
				}
				
				if (isset($_SESSION['samlUserdata'][$this->config->item('saml_last_name_field')][0]))
				{
					$last_name = $_SESSION['samlUserdata'][$this->config->item('saml_last_name_field')][0];
				}
				
				if (isset($_SESSION['samlUserdata'][$this->config->item('saml_email_field')][0]))
				{
					$email = $_SESSION['samlUserdata'][$this->config->item('saml_email_field')][0];
				}
				$groups = array();
				@$groups = $_SESSION['samlUserdata'][$this->config->item('saml_groups_field') ? $this->config->item('saml_groups_field') : 'groups'];
				@$locations = $_SESSION['samlUserdata'][$this->config->item('saml_locations_field') ? $this->config->item('saml_locations_field') : 'locations'];
				
				$new_sso_employee = array('email' => $email,'username' => $_SESSION['samlNameId'], 'first_name' => $first_name, 'last_name' => $last_name,'locations' => array());
				foreach($groups as $group)
				{
					$permission_template_result = $this->Permission_template->search($group);
					if($permission_template_result->num_rows() > 0)
					{
						$new_sso_employee['template_id'] = $permission_template_result->row()->id;
						break;
					}
				}
				
				
				foreach($locations as $location)
				{
					$this->load->model('Location');
					$location_result = $this->Location->search($location);
					if($location_result->num_rows() > 0)
					{
						$new_sso_employee['locations'][] = $location_result->row()->location_id;
					}
				}
				
				
				$this->Employee->create_sso_employee($new_sso_employee);
			}
			$this->Employee->login_no_password($_SESSION['samlNameId']);
			
			$number_of_locations = count($this->Employee->get_authenticated_location_ids($logged_in_employee_info->person_id));

			if ($this->input->get('continue'))
			{
				$continue = rtrim($this->input->get('continue'),'?');
				redirect('/'.$continue);
			}
			else
			{
				redirect('home/index/'.($number_of_locations > 1 ? '1' : '0'));
			}
				
		} 
		else if (isset($_GET['sls'])) 
		{
		    $auth->processSLO(false, NULL);
		    $errors = $auth->getErrors();
		    if (empty($errors)) 
			{
				$this->Employee->logout(true);
		    }
		}
	}
	
	function _saml_get_settings()
	{
		
	    $settings= array (
			'strict' => FALSE,
	        'sp' => array (
	            'entityId' => site_url('login/samlmetadata'),
	            'assertionConsumerService' => array (
	                'url' => site_url('login/samlassertionconsumerservice?acs'),
	            ),
	            'singleLogoutService' => array (
	                'url' => site_url('login/samlassertionconsumerservice?sls'),
	            ),
	            'NameIDFormat' => $this->config->item('saml_name_id_format') ? $this->config->item('saml_name_id_format') : 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
	        ),
	        'idp' => array (
	            'entityId' => $this->config->item('saml_idp_entity_id'),
	            'singleSignOnService' => array (
	                'url' => $this->config->item('saml_single_sign_on_service'),
	            ),
	            'singleLogoutService' => array (
	                'url' => $this->config->item('saml_single_logout_service'),
	            ),
	            'x509cert' => $this->config->item('saml_x509_cert')	,
	        ),
	    );		
		
		
	return $settings; 
	}
	
	function oidc()
	{		
		if ($this->config->item('oidc_client_id') && $this->config->item('oidc_secret'))
		{
			$oidc = new OpenIDConnectClient($this->config->item('oidc_host'),
			$this->config->item('oidc_client_id'),
			$this->config->item('oidc_secret'));
		}
		else
		{
			$oidc = new OpenIDConnectClient($this->config->item('oidc_host'));
			$oidc->register();
		}
		
		if ($this->config->item('oidc_cert_url'))
		{
			$tmpFilename = tempnam(ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir(), 'oidc_auth_cert');
			file_put_contents($tmpFilename,file_get_contents($this->config->item('oidc_cert_url')));
			$oidc->setCertPath($tmpFilename);
		}
		
		$oidc->setVerifyHost(false);
		$oidc->setVerifyPeer(false);
		
		
		$oidc->setHttpUpgradeInsecureRequests(false);
		$scopes = array('openid','profile','email');
		
		if ($this->config->item('oidc_additional_scopes'))
		{
			foreach(explode(',',$this->config->item('oidc_additional_scopes')) as $add_scope)
			{
				$scopes[] = trim($add_scope);
			}
		}
		
		$oidc->addScope($scopes);
		$oidc->authenticate();
		
		if ($this->config->item('oidc_cert_url'))
		{
			unlink($tmpFilename);
		}
		
		if ($oidc->requestUserInfo('email'))
		{
			$first_name = $oidc->requestUserInfo('given_name');
			$last_name = $oidc->requestUserInfo('family_name');
			$email = $oidc->requestUserInfo('email');
			$username = $oidc->requestUserInfo($this->config->item('oidc_username_field') ? $this->config->item('oidc_username_field') : 'email');
			@$groups = $oidc->requestUserInfo($this->config->item('oidc_groups_field'));
			@$locations = $oidc->requestUserInfo($this->config->item('oidc_locations_field'));
				
				
			if (!$this->Employee->employee_username_exists($username))
			{
				$employee_data = array('email' => $email,'username' =>$username, 'first_name' => $first_name, 'last_name' => $last_name,'locations' => array());

				//http://schemas.microsoft.com/ws/2008/06/identity/claims/groups
				foreach(@$groups as $group)
				{
					$permission_template_result = $this->Permission_template->search($group);
					if($permission_template_result->num_rows() > 0)
					{
						$employee_data['template_id'] = $permission_template_result->row()->id;
						break;
					}
				}
				
				foreach($locations as $location)
				{
					$this->load->model('Location');
					$location_result = $this->Location->search($location);
					if($location_result->num_rows() > 0)
					{
						$employee_data['locations'][] = $location_result->row()->location_id;
					}
				}
				
				
				$this->Employee->create_sso_employee($employee_data);
			}
			$this->Employee->login_no_password($username);
			
			$number_of_locations = count($this->Employee->get_authenticated_location_ids($logged_in_employee_info->person_id));

			if ($this->input->get('continue'))
			{
				$continue = rtrim($this->input->get('continue'),'?');
				redirect('/'.$continue);
			}
			else
			{
				redirect('home/index/'.($number_of_locations > 1 ? '1' : '0'));
			}
			
		}
		
	}
}
?>