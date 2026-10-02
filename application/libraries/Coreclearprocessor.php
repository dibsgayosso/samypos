<?php
require_once ("Creditcardprocessor.php");
class Coreclearprocessor extends Creditcardprocessor
{
	private $CI;
	private $declined_index;

	function __construct($controller)
	{
		parent::__construct($controller);
		$this->CI =& get_instance();
		$this->declined_index = 0;

		$this->CI->load->model('Location');
		$this->CI->load->model('Register');
		$this->CI->load->model('Employee');
		$this->CI->load->model('Customer');
	}
	
	public function start_cc_processing()
	{
		return false;
	}
	//this only does card NOT present
	public function finish_cc_processing()
	{
		$payment_type = lang('common_credit');
		$cc_full_charge_amount = to_currency_no_money($this->controller->cart->get_payment_amount($payment_type));			
		$cur_location_info = $this->CI->Location->get_info($this->CI->Employee->get_logged_in_employee_current_location_id());
		
		$test_mode = $cur_location_info->coreclear_sandbox;
		$merchant_id = $cur_location_info->coreclear_mx_merchant_id;
		$authorization_key = $cur_location_info->coreclear_authorization_key;
		$authorization_key_created = $cur_location_info->coreclear_authorization_key_created;
		$coreclear_consumer_key = $cur_location_info->coreclear_consumer_key;
		$coreclear_secret_key = $cur_location_info->coreclear_secret_key;
		
		$authorization_key_created_timestamp = strtotime($authorization_key_created);
		$current_timestamp = time();

		//When processing cards the value of the config field "Token Create Date" is > 8 hours old from the current time then we need to get a new token before processing the credit cards
		if($current_timestamp-$authorization_key_created_timestamp > 28800){
			$get_coreclear_authorization_key = $this->CI->Location->get_coreclear_authorization_key($test_mode,$merchant_id,$coreclear_consumer_key,$coreclear_secret_key);
			$authorization_key = $get_coreclear_authorization_key['jwtToken'];
			$authorization_key_created = $get_coreclear_authorization_key['coreclear_authorization_key_created'];
			
			$authorization_key_update_data = array(
				'coreclear_authorization_key' => $authorization_key,
				'coreclear_authorization_key_created' => $authorization_key_created,
			);
			$this->CI->Location->save($authorization_key_update_data,$this->CI->Employee->get_logged_in_employee_current_location_id());
		}

		$amount = $this->controller->cart->get_payment_amount($payment_type);

		if($this->controller->cart->customer_id){
			$customer_info = $this->CI->Customer->get_info($this->controller->cart->customer_id);
		}
		
		$suspended = 0;

		
			$card_number = $this->controller->input->post('cc_number');
			$exp_date = $this->controller->input->post('cc_exp_date');
			$cvc = $this->controller->input->post('cc_cvc') ?: $this->controller->input->post('cvv');

			if($test_mode){
				$uri = "https://sandbox.api.mxmerchant.com";
			}
			else{
				$uri = "https://api.mxmerchant.com";
			}

			$method = "checkout/v3/payment?echo=true";
			$endpoint = $uri.'/'.$method;

			$expiryMonth = explode('/',$exp_date)[0];
			$expiryyear = explode('/',$exp_date)[1];

			if($customer_info){
				$avsStreet = $customer_info->address_1;
				$avsZip = $customer_info->zip;
			}
			else{
				$avsStreet = '';
				$avsZip = '';
			}
			$data = array(
				'merchantId' => $merchant_id,
				'amount' => $amount,
				"tenderType" => "Card",
				"cardAccount" => array(
					"number"=>$card_number,
					"expiryMonth"=>$expiryMonth,
					"expiryyear"=>$expiryyear,
					"avsStreet"=>$avsStreet,
					"avsZip"=>$avsZip,
					"cvv"=>$cvc,
				),
			);
						
			
			$data = json_encode($data);
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $endpoint,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 360,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $data,
				CURLOPT_USERPWD => $coreclear_consumer_key.":".$coreclear_secret_key,
				CURLOPT_HTTPHEADER => array(
					"Content-Type: application/json",
					"cache-control: no-cache"
				),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			$total_time = curl_getinfo($curl, CURLINFO_TOTAL_TIME)*1000;
			$response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

			curl_close($curl);

			$response_data = json_decode($response,TRUE);
	
			if($response_code == 200 || $response_code == 201){
			
				if($response_data['status'] == 'Settled' || $response_data['status'] == 'Approved'){
					if (isset($response_data['amount']))
					{
						$Authorize = to_currency_no_money($response_data['amount']);
					}
					$charge_id = $response_data['id'];
					$masked_account = $response_data['cardAccount']['last4'];
					$card_brand = $response_data['cardAccount']['cardType'];
					$auth_code = $response_data['authCode'];
					$cc_token = $response_data['paymentToken'];
					$cc_exp = $response_data['cardAccount']['expiryMonth'].$response_data['cardAccount']['expiryYear'];
					$entry_method = $response_data['cardAccount']['entryMode'];
					$tran_type = $response_data['type'];
					$acq_ref_data = $response_data['reference'];
					$process_data = $response_data['authMessage'];
				}
				else{
					$this->controller->cart->delete_payment($this->controller->cart->get_not_processed_cc_payment_ids());
					$this->controller->cart->save();
					$this->CI->session->set_flashdata('cc_process_error_message', $response_data['authMessage']);
					redirect(site_url('sales'));
					return;
				}
			}
			else{				
				$this->controller->cart->delete_payment($this->controller->cart->get_not_processed_cc_payment_ids());
				$this->controller->cart->save();

				if(is_array($response_data)){
					$this->CI->session->set_flashdata('cc_process_error_message', $response_data['message']);
				}
				else{
					$this->CI->session->set_flashdata('cc_process_error_message', $response);
				}
				redirect(site_url('sales'));
				return;
			}
		
		

		$this->controller->session->set_userdata('ref_no', $charge_id);
		$this->controller->session->set_userdata('auth_code', $auth_code);
		$this->controller->session->set_userdata('masked_account', $masked_account);
		$this->controller->session->set_userdata('card_issuer', $card_brand);
		$this->controller->session->set_userdata('cc_token', $cc_token);
		$this->controller->session->set_userdata('entry_method', $entry_method);
		$this->controller->session->set_userdata('tran_type', $tran_type);
		$this->controller->session->set_userdata('acq_ref_data', $acq_ref_data);
		$this->controller->session->set_userdata('process_data', $process_data);
		
		if ($this->controller->_payments_cover_total() && $Authorize == $cc_full_charge_amount)
		{
			$this->controller->session->set_userdata('CC_SUCCESS', TRUE);
			$credit_card_amount = to_currency_no_money($Authorize);
			
			$this->controller->cart->delete_payment($this->controller->cart->get_not_processed_cc_payment_ids());
			$this->controller->cart->add_payment(new PHPPOSCartPaymentSale(array(
				'payment_type' => $payment_type,
				'payment_amount' => $credit_card_amount,
				'payment_date' => date('Y-m-d H:i:s'),
				'truncated_card' => $masked_account,
				'card_issuer' => $card_brand,
				'ref_no' => $charge_id,
				'auth_code' => $auth_code,
				'cc_token' => $cc_token,
				'entry_method' => $entry_method,
				'tran_type' => $tran_type,
				'acq_ref_data' => $acq_ref_data,
				'process_data' => $process_data,
			)));
			
			redirect(site_url('sales/complete'));
		}
		else //Change payment type to Partial Credit Card and show sales interface
		{
			$credit_card_amount = $Authorize;

			$partial_transaction = array(
				'charge_id' => $charge_id,
			);
			
			$this->controller->cart->delete_payment($this->controller->cart->get_not_processed_cc_payment_ids());
			$this->controller->cart->add_payment(new PHPPOSCartPaymentSale(array(
				'payment_type' => $is_cnp ? lang('common_cnp') : lang('sales_partial_credit'),
				'payment_amount' => $credit_card_amount,
				'payment_date' => date('Y-m-d H:i:s'),
				'truncated_card' => $masked_account,
				'card_issuer' => $card_brand,
				'ref_no' => $charge_id,
				'auth_code' => $auth_code,
				'cc_token' => $cc_token,
				'entry_method' => $entry_method,
				'tran_type' => $tran_type,
				'acq_ref_data' => $acq_ref_data,
				'process_data' => $process_data,
				
			)));
			$this->controller->cart->add_partial_transaction($partial_transaction);
			$this->controller->cart->save();
			$this->log_charge($charge_id,$credit_card_amount,false);
			$this->controller->_reload(array('warning' => lang('sales_credit_card_partially_charged_please_complete_sale_with_another_payment_method')), false);
			return;
		}
	}

	function get_transaction_result($uri,$method,$coreclear_consumer_key,$coreclear_secret_key,$get_transaction_result_start_time){
		$endpoint = $uri.'/'.$method;

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $endpoint,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 360,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_USERPWD => $coreclear_consumer_key.":".$coreclear_secret_key,
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"cache-control: no-cache"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		$total_time = curl_getinfo($curl, CURLINFO_TOTAL_TIME)*1000;
		$response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if($response_code == 200){
			
			$response_data = json_decode($response,TRUE);
			if($response_data['status'] == 'Settled' || $response_data['status'] == 'Approved'){
				return array('success'=>true,'response'=>$response);
			}
			else if($response_data['status'] == 'Declined'){
				if($this->declined_index == 1){
					return array('success'=>false,'response'=>$response);
				}
				else{
					$this->declined_index++;
					sleep(2);
					return $this->get_transaction_result($uri,$method,$coreclear_consumer_key,$coreclear_secret_key,$get_transaction_result_start_time);
				}
				
			}
			else{
				return array('success'=>false,'response'=>$response);
			}
		}
		else if($response_code == 404){
			$get_transaction_result_end_time = microtime_float();
			
			//If the transaction is not complete we will get an error 404 (not found), so we need to keep calling every 2 seconds (I think we will have to time out after a certain amount of time, I'm thinking 1 minute).
			if(($get_transaction_result_end_time - $get_transaction_result_start_time)>60){
				return array('success'=>false,'timeout'=>true);
			}
			else{
				sleep(2);
				return $this->get_transaction_result($uri,$method,$coreclear_consumer_key,$coreclear_secret_key,$get_transaction_result_start_time);
			}
			
		}
		else{
			return array('success'=>false,'response'=>$response);
		}
	}

	public function cancel_cc_processing()
	{
		$this->controller->cart->delete_payment($this->controller->cart->get_not_processed_cc_payment_ids());
		$this->controller->cart->save();
		$this->controller->_reload(array('error' => lang('sales_cc_processing_cancelled')), false);
		
	}
	public function void_partial_transactions()
	{
		$void_success = true;
		
		$partial_transactions = $this->controller->cart->get_partial_transactions() ;

		$cur_location_info = $this->CI->Location->get_info($this->CI->Employee->get_logged_in_employee_current_location_id());
		$test_mode = $cur_location_info->coreclear_sandbox;
		$merchant_id = $cur_location_info->coreclear_mx_merchant_id;
		$coreclear_consumer_key = $cur_location_info->coreclear_consumer_key;
		$coreclear_secret_key = $cur_location_info->coreclear_secret_key;
		
		if($test_mode){
			$uri = "https://sandbox.api.mxmerchant.com";
		}
		else{
			$uri = "https://api.mxmerchant.com";
		}

		$method = "checkout/v3/payment?echo=true";
		$endpoint = $uri.'/'.$method;

		if ($partial_transactions)
		{
			foreach($partial_transactions as $transaction)
			{
				$amount = (-1)*($transaction['payment_amount']);

				$data = array(
					'merchantId' => $merchant_id,
					"tenderType" => "Card",
					'amount' => $amount,
					"paymentToken" => $transaction['cc_token'],
				);
	
				$data = json_encode($data);
				
				$endpoint = $uri.'/'.$method;
	
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $endpoint,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 360,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => $data,
					CURLOPT_USERPWD => $coreclear_consumer_key.":".$coreclear_secret_key,
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"cache-control: no-cache"
					),
				));
	
				$response = curl_exec($curl);
				$err = curl_error($curl);
	
				$total_time = curl_getinfo($curl, CURLINFO_TOTAL_TIME)*1000;
				$response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
				curl_close($curl);

				if($response_code == 200 || $response_code == 201){
				}
				else{	
					$void_success = false;
				}
			}
		}
		
		return $void_success;
		
	}
	
	public function void_sale($sale_id)
	{
		if ($this->controller->Sale->can_void_cc_sale($sale_id))
		{
			$void_success = true;
			
			$payments = $this->_get_cc_payments_for_sale($sale_id);

			$cur_location_info = $this->CI->Location->get_info($this->CI->Employee->get_logged_in_employee_current_location_id());
			$test_mode = $cur_location_info->coreclear_sandbox;
			$merchant_id = $cur_location_info->coreclear_mx_merchant_id;
			$coreclear_consumer_key = $cur_location_info->coreclear_consumer_key;
			$coreclear_secret_key = $cur_location_info->coreclear_secret_key;
			
			if($test_mode){
				$uri = "https://sandbox.api.mxmerchant.com";
			}
			else{
				$uri = "https://api.mxmerchant.com";
			}

			$method = "checkout/v3/payment?echo=true";
			$endpoint = $uri.'/'.$method;

			foreach($payments as $payment)
			{
				$amount = (-1)*($payment['payment_amount']);

				$data = array(
					'merchantId' => $merchant_id,
					"tenderType" => "Card",
					'amount' => $amount,
					"paymentToken" => $payment['cc_token'],
				);
	
				$data = json_encode($data);
				
				$endpoint = $uri.'/'.$method;
	
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $endpoint,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 360,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => $data,
					CURLOPT_USERPWD => $coreclear_consumer_key.":".$coreclear_secret_key,
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"cache-control: no-cache"
					),
				));
	
				$response = curl_exec($curl);
				$err = curl_error($curl);
	
				$total_time = curl_getinfo($curl, CURLINFO_TOTAL_TIME)*1000;
				$response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
				curl_close($curl);

				if($response_code == 200 || $response_code == 201){
				}
				else{	
					$void_success = false;
				}
			}
			
			return $void_success;
		}
		
		return FALSE;
		
	}
	public function void_return($sale_id)
	{
		return FALSE;
	}	
	
	function tip($sale_id, $tip_amount)
	{
		return false;
	}
	
}
