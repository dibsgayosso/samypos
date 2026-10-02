<?php
require_once ("Creditcardprocessor.php");
require_once (APPPATH."libraries/blockchyp/vendor/autoload.php");

use \BlockChyp\BlockChyp;

class Coreclearblockchypprocessor extends Creditcardprocessor
{	
	function __construct($controller,$override_id = null)
	{
		parent::__construct($controller);
		$this->controller->load->helper('sale');	
		

		if (isset($override_id)) {
			$override_id = 1;
		} else {
			$override_id = FALSE;
		}
		$current_register_id = $this->controller->Employee->get_logged_in_employee_current_register_id();
		$register_info = $this->controller->Register->get_info($current_register_id);
		$this->emv_terminal_id = $register_info && property_exists($register_info,'emv_terminal_id') ? $register_info->emv_terminal_id : FALSE;
		$this->test_mode = (boolean)$this->controller->Location->get_info_for_key('blockchyp_test_mode',$override_id);
		$this->is_card_not_present = !$this->emv_terminal_id || $this->use_backup_gateway() || $this->controller->session->userdata('use_manual_entry');
		$this->register_tip_mode = $register_info->enable_tips;
		
		try
		{
	    	BlockChyp::setApiKey($this->controller->Location->get_info_for_key('blockchyp_api_key',$override_id));
	    	BlockChyp::setBearerToken($this->controller->Location->get_info_for_key('blockchyp_bearer_token',$override_id));
	    	BlockChyp::setSigningKey($this->controller->Location->get_info_for_key('blockchyp_signing_key',$override_id));
		}
		catch(Exception $e)
		{
			
		}
		
	}
	
	function use_backup_gateway()
	{
        $cur_location_info = $this->controller->Location->get_info($this->controller->Employee->get_logged_in_employee_current_location_id());
		
        // retry the same payment with MX Merchant if the mx merchant credentials exist
        if ($cur_location_info->coreclear_mx_merchant_id && $cur_location_info->coreclear_consumer_key && $cur_location_info->coreclear_secret_key && $this->controller->session->userdata('use_backup_gateway')) 
		{
            return TRUE;
        }
		
		return false;
	}
	
	public function start_cc_processing()
	{
		//When we charge a card on file we don't want to do manual checkout
		if ($this->is_card_not_present && !$this->controller->cart->use_cc_saved_info)
		{
			$cc_amount = $this->controller->cart->get_payment_amount(lang('common_credit'));
		
			$data['cc_amount'] = to_currency($cc_amount);
			$data['amount'] = $cc_amount;
			$data['test_mode'] = $this->test_mode;
			
			$this->controller->load->view('sales/coreclear_blockchyp_manual_checkout', $data);
			
		}
		else
		{
			$this->controller->load->view('sales/coreclear_blockchyp_start_cc_processing');
		}
	}	
			
	public function do_start_cc_processing()
	{			
		
		if ($this->controller->session->userdata('use_backup_gateway'))
		{
        	require_once (APPPATH.'libraries/Coreclearprocessor.php');
        	$credit_card_processor = new Coreclearprocessor($this->controller);
        	$credit_card_processor->finish_cc_processing();
        	return;
		}
		$cc_amount = to_currency_no_money($this->controller->cart->get_payment_amount(lang('common_credit')));
		$ebt_amount = to_currency_no_money($this->controller->cart->get_payment_amount(lang('common_ebt')));
		$this->controller->load->helper('sale');
		$is_ebt = is_ebt_sale($this->controller->cart);
		if ($is_ebt)
		{
			$cc_amount = $ebt_amount;
		}
		
		$customer_id = $this->controller->cart->customer_id;
		
		$customer_name = '';
		if ($customer_id != -1)
		{
			$customer_info=$this->controller->Customer->get_info($customer_id);
			$customer_name = $customer_info->first_name.' '.$customer_info->last_name;
		}
				
		$prompt = $this->controller->cart->prompt_for_card;
		
		//Just need token
		if ($cc_amount ==0)
		{
			if ($this->is_card_not_present)
			{
				list($cc_month,$cc_year) = explode('/',$this->controller->input->post('cc_exp_date'));
				
				$response = $this->enroll_pan($this->controller->input->post('cc_number'),$cc_month,$cc_year,$this->controller->input->post('cvv'));
		        $charge_data = [
		            'pan' => $this->controller->input->post('cc_number'),
					'cvv' => $this->controller->input->post('cvv'),
		            'expMonth' => $cc_month,
		            'expYear' => $cc_year,
					'amount' => $cc_amount,
					'test' => $this->test_mode,
					'enroll' => TRUE,
		        ];				
			}
			else
			{
				$response = $this->enroll_terminal();
				
			}
		}
		else//charging cards
		{		
			if(!$this->controller->cart->use_cc_saved_info)
			{
				if ($this->is_card_not_present)
				{
					list($cc_month,$cc_year) = explode('/',$this->controller->input->post('cc_exp_date'));
				
			        $charge_data = [
			            'pan' => $this->controller->input->post('cc_number'),
			            'expMonth' => $cc_month,
			            'expYear' => $cc_year,
						'amount' => $cc_amount,
						'test' => $this->test_mode,
						'enroll' => TRUE,
			        ];				
				}
				else
				{
					$charge_data = array(
						'test' => $this->test_mode,
						'terminalName' => $this->emv_terminal_id,
						'amount' => $cc_amount,
						'enroll' => TRUE,
						'sigFormat' => BlockChyp::SIGNATURE_FORMAT_PNG,
						'sigWidth' => 400,
					);
				
					if ($is_ebt)
					{
						$charge_data['cardType'] = BlockChyp::CARD_TYPE_EBT;
					}
				
					if ($prompt)
					{
						$charge_data['manualEntry'] = TRUE;
					}
				
					if ($this->controller->config->item('enable_tips') || $this->register_tip_mode)
					{
						$charge_data['promptForTip'] = TRUE;
					}
				
				
					if ($this->controller->config->item('disable_signature_capture_on_terminal_for_phppos_credit_card_processing'))
					{
						$charge_data['disableSignature'] = TRUE;
					}
				
					if (($terms_and_conditions = $this->controller->Location->get_info_for_key('blockchyp_terms_and_conditions')) && $this->controller->cart->show_terms_and_conditions)
					{
						// Populate request values
						$tc_request = [
							'test' => $this->test_mode,
						    'terminalName' => $this->emv_terminal_id,

						    // Name of the contract or document if not using an alias.
						    'tcName' => 'Terms & Conditions',

						    // Full text of the contract or disclosure if not using an alias.
						    'tcContent' => $terms_and_conditions,

						    // File format for the signature image.
						    'sigFormat' => BlockChyp::SIGNATURE_FORMAT_PNG,

						    // Width of the signature image in pixels.
						    'sigWidth' => 200,

						    // Whether or not a signature is required. Defaults to true.
						    'sigRequired' => true,
						];
					
						BlockChyp::termsAndConditions($tc_request);
					}
				}
			
			}
			elseif($customer_info->cc_token)
			{			
				$charge_data = array(
					'test' => $this->test_mode,
					'token' => $customer_info->cc_token,
					'amount' => $cc_amount,
					'enroll' => TRUE,
				);			
			}
		
			try
			{
				$charge_data['transactionRef'] = $this->_get_session_invoice_no();
			
				if ($cc_amount <=0)
				{
					$charge_data['amount'] = to_currency_no_money(abs($cc_amount));
					$response = BlockChyp::refund($charge_data);
				}
				else
				{
					$response = BlockChyp::charge($charge_data);
				}
			
				if ($response === NULL)
				{
					$status_request = [
						'test' => $this->test_mode,
					    'transactionRef' => $this->_get_session_invoice_no()
					];
			
					$response = BlockChyp::transactionStatus($status_request);
				}
			
			}
			catch(Exception $e)
			{
				$status_request = [
					'test' => $this->test_mode,
				    'transactionRef' => $this->_get_session_invoice_no()
				];
		
				$response = BlockChyp::transactionStatus($status_request);
			}
	}
		
		
				 
		$TextResponse = isset($response['error']) && $response['error']  ? $response['error'] : $response['responseDescription'];
		if ($response['success'] && $response['approved'])
		{
			if (isset($response['receiptSuggestions']))
			{
				@$CardType = $response['paymentType'];
				@$EntryMethod = $response['entryMethod'];
				@$ApplicationLabel = $response['receiptSuggestions']['applicationLabel'];

				@$AID = $response['receiptSuggestions']['aid'];
				@$TVR = $response['receiptSuggestions']['tvr'];
				@$IAD = $response['receiptSuggestions']['iad'];
				@$TSI = $response['receiptSuggestions']['tsi'];
		  	}
			else
			{
				@$EntryMethod = $prompt ? lang('sales_manual_entry') : lang('common_credit');
				@$ApplicationLabel = $customer_info->card_issuer ? $customer_info->card_issuer : $EntryMethod;
				@$CardType = $customer_info->card_issuer ? $customer_info->card_issuer : $EntryMethod;
			}
			
			//Catch all
			if (!$CardType && $customer_info->card_issuer)
			{
				$CardType = $customer_info->card_issuer;
			}
			
		   $MerchantID =  '';
		   $Signature = hex2bin($response['sigFile']);
		   $tip_amount = make_currency_no_money($response['tipAmount']);
		   $AcctNo = $response['maskedPan'];
		   $TranCode = lang('sales_card_transaction');
		   $AuthCode = $response['authCode'];
		   $RefNo = $response['transactionId'];
		   $Purchase = to_currency_no_money($cc_amount + $tip_amount);
		   
		   $Authorize = isset($response['authorizedAmount']) ? make_currency_no_money($response['authorizedAmount']) : to_currency_no_money(0);
		   
		   $RecordNo = $response['token'];
		   
			if (!$RecordNo && $this->controller->input->post('cc_number'))
			{
				list($cc_month,$cc_year) = explode('/',$this->controller->input->post('cc_exp_date'));
		
				$RecordNo = $this->enroll_pan($this->controller->input->post('cc_number'),$cc_month,$cc_year,$this->controller->input->post('cvv'))['token'];
			}
		   
		   $CCExpire = lang('common_unknown');
			
			$this->controller->session->set_userdata('ref_no', $RefNo);
			$this->controller->session->set_userdata('tip_amount', $tip_amount);
			$this->controller->session->set_userdata('auth_code', $AuthCode);
			$this->controller->session->set_userdata('cc_token', $RecordNo);
			$this->controller->session->set_userdata('entry_method', $EntryMethod);
			$this->controller->session->set_userdata('cc_signature', $Signature);
			$this->controller->session->set_userdata('tip_amount', $tip_amount);
			
			if (isset($response['receiptSuggestions']))
			{
				$this->controller->session->set_userdata('aid', $AID);
				$this->controller->session->set_userdata('tvr', $TVR);
				$this->controller->session->set_userdata('iad', $IAD);
				$this->controller->session->set_userdata('tsi', $TSI);
			}
			
			$this->controller->session->set_userdata('application_label', $ApplicationLabel);
			$this->controller->session->set_userdata('tran_type', $TranCode);
			$this->controller->session->set_userdata('text_response', $TextResponse);
			
			
			//return amount we need negative value
			if ($Purchase < 0)
			{
				$Authorize = $Authorize*-1;
			}
			
			
			//Payment covers purchase amount
			if ($Authorize == $Purchase)
			{
				$this->controller->session->set_userdata('masked_account', $AcctNo);
				$this->controller->session->set_userdata('card_issuer', $CardType);
						
				
				//We want to save/update card when we have a customer AND they have chosen to save OR we have a customer and they are using a saved card
				if (($this->controller->cart->save_credit_card_info) && $this->controller->cart->customer_id 
				|| ($this->controller->cart->customer_id && $this->controller->cart->use_cc_saved_info))
				{
					if ($RecordNo)
					{
						$person_info = array('person_id' => $this->controller->cart->customer_id);
						$customer_info = array('cc_token' => $RecordNo, 'cc_expire' => $CCExpire, 'cc_ref_no' => $RefNo, 'cc_preview' => $AcctNo);
						$this->controller->Customer->save_customer($person_info,$customer_info,$this->controller->cart->customer_id);
					}
				}
				
				
				//If the sale payments cover the total, redirect to complete (receipt)
				if ($this->controller->_payments_cover_total())
				{
					$this->controller->session->set_userdata('CC_SUCCESS', TRUE);					
					$this->log_charge($RefNo,$Authorize, true);
					redirect(site_url('sales/complete'));
				}
				else //Change payment type to Partial Credit Card and show sales interface
				{							
					$credit_card_amount = to_currency_no_money($this->controller->cart->get_payment_amount(lang('common_credit')));
				
					$partial_transaction = array(
						'AuthCode' => $AuthCode,
						'MerchantID' => $this->merchant_id ,
						'Purchase' => $Purchase,
						'RefNo' => $RefNo,
						'RecordNo' => $RecordNo,
					);
														
					$this->controller->cart->delete_payment($this->controller->cart->get_payment_ids(lang('common_credit')));												
				
					@$this->controller->cart->add_payment(new PHPPOSCartPaymentSale(array(
						'payment_type' => lang('sales_partial_credit'),
						'payment_amount' => $credit_card_amount,
						'payment_date' => date('Y-m-d H:i:s'),
						'truncated_card' => $AcctNo,
						'card_issuer' => $CardType,
						'auth_code' => $AuthCode,
						'ref_no' => $RefNo,
						'cc_token' => $RecordNo,
						'entry_method' => $EntryMethod,
						'aid' => $AID,
						'tvr' => $TVR,
						'iad' => $IAD,
						'tsi' => $TSI,
						'tran_type' => $TranCode,
						'application_label' => $ApplicationLabel,
					)));
					
					$this->controller->cart->add_partial_transaction($partial_transaction);
					$this->controller->cart->save();
					$this->log_charge($RefNo,$credit_card_amount,false);
					$this->controller->_reload(array('warning' => lang('sales_credit_card_partially_charged_please_complete_sale_with_another_payment_method')), false);			
				}
			}
			elseif($Authorize < $Purchase)
			{
					$partial_transaction = array(
						'AuthCode' => $AuthCode,
						'MerchantID' => $this->merchant_id ,
						'Purchase' => $Authorize,
						'RefNo' => $RefNo,
						'RecordNo' => $RecordNo,
					);
			
					$this->controller->cart->delete_payment($this->controller->cart->get_payment_ids(lang('common_credit')));
					
					@$this->controller->cart->add_payment(new PHPPOSCartPaymentSale(array(
						'payment_type' => lang('sales_partial_credit'),
						'payment_amount' => $Authorize,
						'payment_date' => date('Y-m-d H:i:s'),
						'truncated_card' => $AcctNo,
						'card_issuer' => $CardType,
						'auth_code' => $AuthCode,
						'ref_no' => $RefNo,
						'cc_token' => $RecordNo,
						'entry_method' => $EntryMethod,
						'aid' => $AID,
						'tvr' => $TVR,
						'iad' => $IAD,
						'tsi' => $TSI,
						'tran_type' => $TranCode,
						'application_label' => $ApplicationLabel,
					)));
					
					$this->controller->cart->add_partial_transaction($partial_transaction);
					$this->controller->cart->save();
					$this->log_charge($RefNo,$credit_card_amount,false);
					$this->controller->_reload(array('warning' => lang('sales_credit_card_partially_charged_please_complete_sale_with_another_payment_method')), false);	
				}
		}
		else
		{
			
			//If we are using saved token and have a failed response remove token from customer
			if ($this->controller->cart->use_cc_saved_info && $this->controller->cart->customer_id)
			{
				//If we have failed, remove cc token and cc preview
				$person_info = array('person_id' => $this->controller->cart->customer_id);
				$customer_info = array('cc_token' => NULL, 'cc_ref_no' => NULL, 'cc_preview' => NULL, 'card_issuer' => NULL);
				
				if (!$this->controller->config->item('do_not_delete_saved_card_after_failure'))
				{
					$this->controller->Customer->save_customer($person_info,$customer_info,$this->controller->cart->customer_id);
				}
								
				//Clear cc token for using saved cc info
				$this->controller->cart->use_cc_saved_info = NULL;
				$this->controller->cart->save();
			}

			if ($response === NULL)
			{
				try
				{
					$request = [
						'test' => $this->test_mode,
					    'transactionRef' => $this->_get_session_invoice_no()
					];
					$reverse_result = BlockChyp::reverse($request);
					
					if (!($reverse_result['success'] && $reverse_result['approved']))
					{
						$this->controller->_reload(array('error' => lang('sales_unable_to_determine_transaction_status_please_check_coreclear')), false);
					}
					else
					{
						$this->controller->_reload(array('error' => lang('sales_terminal_connection_failed_please_try_again')), false);
					}
				}
				catch(Exception $e)
				{
					$this->controller->_reload(array('error' => lang('sales_unable_to_determine_transaction_status_please_check_coreclear')), false);
				}
			}
			else
			{
				$this->controller->_reload(array('error' => $TextResponse), false);
			}

		}
	}
	
	public function finish_cc_processing()
	{
		//No need for this method as it is handled by start method all at once
		return TRUE;
	}
	
	public function cancel_cc_processing()
	{
		$this->controller->cart->delete_payment($this->controller->cart->get_payment_ids(lang('common_credit')));
		$this->controller->cart->save();
		$this->controller->_reload(array('error' => lang('sales_cc_processing_cancelled')), false);
	}
	
	
	private function void_sale_payment($payment_amount,$auth_code,$ref_no,$token,$acq_ref_data,$process_data,$tip_amount = 0)
	{
		
		$void_data = array(
			'test' => $this->test_mode,
			'transactionId' => $ref_no,
		);
		
		//try void first
		try
		{
			$response = BlockChyp::void($void_data);
		
			if (!($response['success'] && $response['approved']))
			{
				$payment_amount = to_currency_no_money($payment_amount);
			
				$refund_data = array(
					'test' => $this->test_mode,
					'transactionId' => $ref_no,
				    'amount' => $payment_amount,
				);
			
				$response = BlockChyp::refund($refund_data);
			
				return $response['success'] && $response['approved'];
			}
		}
		catch(Exception $e)
		{
			
		}
		
		return TRUE;
		
	}
	
	private function void_return_payment($payment_amount,$auth_code,$ref_no,$token,$acq_ref_data,$process_data)
	{
		$void_data = array(
			'test' => $this->test_mode,
			'transactionId' => $ref_no,
		);
		
		try
		{
			//try void first
			$response = BlockChyp::void($void_data);
		
			if (!($response['success'] && $response['approved']))
			{
				$payment_amount = to_currency_no_money($payment_amount);
			
				$refund_data = array(
					'test' => $this->test_mode,
					'transactionId' => $ref_no,
				    'amount' => $payment_amount,
				);
			
				$response = BlockChyp::refund($refund_data);
			
				return $response['success'] && $response['approved'];
			}
		}
		catch(Exception $e)
		{
			
		}
		

		return TRUE;
	}
	
	public function void_partial_transactions()
	{
		$void_success = true;
		
		if ($partial_transactions = $this->controller->cart->get_partial_transactions())
		{
			for ($k = 0;$k<count($partial_transactions);$k++)
			{
				$partial_transaction = $partial_transactions[$k];
				@$void_success = $this->void_sale_payment(to_currency_no_money($partial_transaction['Purchase']),$partial_transaction['AuthCode'],$partial_transaction['RefNo'],$partial_transaction['RecordNo'],$partial_transaction['AcqRefData'],$partial_transaction['ProcessData']);
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
			
			foreach($payments as $payment)
			{				
				@$void_success = $this->void_sale_payment($payment['payment_amount'], $payment['auth_code'], $payment['ref_no'], $payment['cc_token'],$payment['acq_ref_data'], $payment['process_data']);
			}
			
			return $void_success;
		}
		
		return FALSE;
	}
	
	public function void_return($sale_id)
	{
		if ($this->controller->Sale->can_void_cc_return($sale_id))
		{
			$void_success = true;
			
			$payments = $this->_get_cc_payments_for_sale($sale_id);
			
			foreach($payments as $payment)
			{
				$void_success = $this->void_return_payment($payment['payment_amount'], $payment['auth_code'], $payment['ref_no'], $payment['cc_token'],$payment['acq_ref_data'], $payment['process_data']);
			}
			
			return $void_success;
		}
		
		return FALSE;	
	}		
	
	//Not implemented on device
	public function tip($sale_id,$tip_amount)
	{
		return FALSE;
	}

	function is_terminal_idle()
	{
		try
		{
			$request = [
				'test' => $this->test_mode,
				'terminalName' => $this->emv_terminal_id,
			];
			$response = BlockChyp::terminalStatus($request);
			
			if ($response['success'])
			{
				return $response['idle'];
			}
			
		}
		catch(Exception $e)
		{
			
		}
		
		return FALSE;
	}	
	public function update_transaction_display($cart)
	{
		$items = array();
		
		if (count($cart->get_items()) == 0)
		{
			$this->clear_terminal();
			return;
		}
		
		foreach($cart->get_items() as $cart_item)
		{
			$item = array();
			$item['description'] = $cart_item->name;
			$item['price'] = to_currency_no_money($cart_item->unit_price - ($cart_item->unit_price*$cart_item->discount/100));
			$item['quantity'] = (float)$cart_item->quantity;
			$item['extended'] = to_currency_no_money($cart_item->unit_price*$cart_item->quantity-$cart_item->unit_price*$cart_item->quantity*$cart_item->discount/100);
			
			$total_discount = to_currency_no_money(($cart_item->unit_price*$cart_item->discount/100)*$cart_item->quantity);
			
			if ($total_discount > 0)
			{
				$item['discounts'] = [
                    [
                        'description' => lang('common_discount'),
                        'amount' => $total_discount,
                    ],
				];
			}
			$items[] = $item;
		}
		
		
		// Populate request values
		$request = [
			'test' => $this->test_mode,
			'terminalName' => $this->emv_terminal_id,
		    'transaction' => [
		        'subtotal' => to_currency_no_money($cart->get_subtotal()),
		        'tax' => to_currency_no_money($cart->get_tax_total_amount()),
		        'total' => to_currency_no_money($cart->get_total()),
		        'items' => $items,
		        ],
		];
		
		
		try
		{
			BlockChyp::newTransactionDisplay($request);
		}
		catch(Exception $e)
		{
			
		}
	}
	
	function get_transaction_history($params=array())
	{
		try
		{
			return BlockChyp::transactionHistory($params);
		}
		catch(Exception $e)
		{
			
		}
	}
	
	function get_batch_history($params=array())
	{
		try
		{
			return BlockChyp::batchHistory($params);
		}
		catch(Exception $e)
		{
			
		}
	}

	/*
	$type can be:
	phone: Captures a phone number.
	email: Captures an email address.
	first-name: Captures a first name.
	last-name: Captures a last name.
	customer-number: Captures a customer number.
	rewards-number: Captures a rewards number.
	*/
	function text_prompt($type)
	{
		try
		{
			$request = [
				'test' => $this->test_mode,
				'terminalName' => $this->emv_terminal_id,
			    'promptType' => $type,
			];
			$response = BlockChyp::textPrompt($request);
			
			if ($response['success'])
			{
				return $response['response'];
			}
		}
		catch(Exception $e)
		{
		
		}
		
		return '';
	}
	
	function boolean_prompt($prompt,$yes_caption='Yes',$no_caption='No')
	{
		try
		{
			$request = [
				'test' => $this->test_mode,
				'terminalName' => $this->emv_terminal_id,
			    'prompt' => $prompt,
			    'yesCaption' => $yes_caption,
			    'noCaption' => $no_caption,
			];
			$response = BlockChyp::booleanPrompt($request);
			
			if ($response['success'])
			{
				return $response['response'];
			}
		}
		catch(Exception $e)
		{
		
		}
		
		return '';
	}
	
	function display_message($message)
	{
		try
		{
			$request = [
				'test' => $this->test_mode,
				'terminalName' => $this->emv_terminal_id,
				'message' => $message,
			];
			$response = BlockChyp::message($request);
			
			return $response['success'];
		}
		catch(Exception $e)
		{
		
		}
		
		return FALSE;
	}

	function get_batch_details($params=array())
	{
		try
		{
			return BlockChyp::batchDetails($params);
		}
		catch(Exception $e)
		{
			
		}
	}
	
	function void_return_transaction_by_id($transaction_id,$amount = NULL)
	{
		$void_data = array(
			'test' => $this->test_mode,
			'transactionId' => $transaction_id,
		);
		
		//try void first
		try
		{
			if ($amount === NULL)
			{
				$response = BlockChyp::void($void_data);
			}
			else
			{
				$response['success'] = FALSE;
			}
			
			if (!($response['success'] && $response['approved']))
			{			
				$refund_data = array(
					'test' => $this->test_mode,
					'transactionId' => $transaction_id,
				);
				
				if ($amount !== NULL)
				{
					$refund_data['amount'] = to_currency_no_money($amount);
				}
			
				$response = BlockChyp::refund($refund_data);
			
				if ($response['success'] && $response['approved'])
				{
					return $response;
				}
				
				return FALSE;
			}
			else
			{
				return $response;
			}
		}
		catch(Exception $e)
		{
			
		}
		
		return FALSE;
		
	}
	
	function clear_terminal()
	{
		try
		{
			return BlockChyp::clear(array('terminalName' => $this->emv_terminal_id));
		}
		catch(Exception $e)
		{
			
		}
	}


	public function do_start_cc_processing_without_login($cc_amount,$total,$id)
	{			
		$cc_amount 			= to_currency_no_money($cc_amount);
		$total 				= to_currency_no_money($total);
		$id 				= $id;
		$invoice_detail 	= $this->controller->Invoice->get_invoice_detail($id);
		$customer_detail 	= $this->controller->Customer->get_info($invoice_detail->customer_id);


		$remaing_balance = $total - $cc_amount;

		$this->controller->load->helper('sale');

		$partial 		= false;
		$full_payment 	= false;
		if ($cc_amount == $total) 
		{
			$payment_type = lang('common_credit');
			$full_payment = true;
		} elseif($cc_amount < $total) {
			$payment_type = lang('sales_partial_credit');
			$partial = true;
		}

				
		
		list($cc_month,$cc_year) = explode('/',$this->controller->input->post('cc_exp_date'));
		
		$charge_data = [
			'pan' 		=> $this->controller->input->post('cc_number'),
			'cvv' => $this->controller->input->post('cvv'),
			'expMonth' 	=> $cc_month,
			'expYear' 	=> $cc_year,
			'amount' 	=> $cc_amount,
			'test' 		=> $this->test_mode,
			'enroll' 	=> TRUE,
		];				
		try
		{
			$response = BlockChyp::charge($charge_data);
		}
		catch(Exception $e)
		{
			
		}

		if (!($response['success'] && $response['approved']))
		{
			$this->controller->session->set_userdata('card_error', 'Invalid Details');
			redirect($_SERVER['HTTP_REFERER']);
		}

		$TextResponse = isset($response['error']) && $response['error']  ? $response['error'] : $response['responseDescription'];
		if ($response['success'] && $response['approved'])
		{

		   	$MerchantID 	=  '';
		   	$truncated_card = $response['maskedPan'];
		   	$card_issuer 	= $response['paymentType'];
		   	$tran_type 		= 'Card Transaction';
		   	$auth_code 		= $response['authCode'];
		   	$ref_no 		= $response['transactionId'];
		   	$entry_method 	= $response['entryMethod'];
		   	$amount 		= make_currency_no_money($response['authorizedAmount']);
		   
			$data = array(
				'invoice_id' 		=> $id,
			    'payment_type' 		=> $payment_type,
				'payment_amount' 	=> $amount,
				'payment_date' 		=> date('Y-m-d H:i:s'),
				'truncated_card' 	=> $truncated_card,
				'card_issuer' 		=> $card_issuer,
				'auth_code' 		=> $auth_code,
				'ref_no' 			=> $ref_no,
				'cc_token' 			=> '',
				'entry_method' 		=> $entry_method,
				'tran_type' 		=> $tran_type,
				'application_label' => '',
			);
			
		
			$invoice_info = $this->controller->Invoice->get_info('customer',$id);
			$invoice_payment_id = $this->controller->Invoice->add_payment('customer',$id,$data);
		
			//Update balance as we made a payment
			$invoice_data = array('balance' => $invoice_info->balance - $amount,'last_paid' => date('Y-m-d'));
			$this->controller->Invoice->save('customer',$invoice_data,$id);
			
			$encrypt = do_encrypt($invoice_payment_id,$this->controller->Appconfig->get_secure_key());

			if (isset($customer_detail->email)) {
				$this->send_invoice_email($cc_amount, $id);
			}
			redirect('payment_success'.'/'.$encrypt);
			
		}
	}

	public function send_invoice_email($cc_amount, $id)
	{

		$invoice_detail 	= $this->controller->Invoice->get_invoice_detail($id);
		$customer_detail 	= $this->controller->Customer->get_info($invoice_detail->customer_id);

		if (isset($customer_detail->email)) {
			$customer_email = $customer_detail->email;
			$subject 		= 'Invoice Payment Confirmation';
			$company 		= $this->controller->config->item('company');
			$message 		= 'Hey , '.$customer_detail->full_name.', it’s '.$company.'<br><br>'.'This is a confirmation that we’ve just received your online payment <br><br> We’ve confirmed your '.$cc_amount.' Payment';

			$this->controller->Common->send_email($customer_email,$subject,$message);
		}
	}
	
	public function capture_signature($terms_and_conditions)
	{
		// Populate request values
		$tc_request = [
			'test' => $this->test_mode,
		    'terminalName' => $this->emv_terminal_id,

		    // Name of the contract or document if not using an alias.
		    'tcName' => 'Terms & Conditions',

		    // Full text of the contract or disclosure if not using an alias.
		    'tcContent' => $terms_and_conditions,

		    // File format for the signature image.
		    'sigFormat' => BlockChyp::SIGNATURE_FORMAT_PNG,

		    // Width of the signature image in pixels.
		    'sigWidth' => 200,

		    // Whether or not a signature is required. Defaults to true.
		    'sigRequired' => true,
		];
		
		$response = BlockChyp::termsAndConditions($tc_request);
		
		if ($response['success'])
		{
	 		return hex2bin($response['sigFile']);
		}
		
		return FALSE;
		
		
	}
	
	function enroll_terminal()
	{
		try
		{
	        $request = [
				'terminalName' => $this->emv_terminal_id,
				'test' => $this->test_mode,
	        ];

	        $response = BlockChyp::enroll($request);
			return $response;
		}
		catch(Exception $e)
		{
		
		}
		
		return FALSE;
	}
	
	public function enroll_pan($pan,$cc_month,$cc_year,$cvv)
	{
		try
		{
	        $request = [
	            'pan' => $pan,
				'cvv' => $cvv,
	            'expMonth' => $cc_month,
	            'expYear' => $cc_year,
				'test' => $this->test_mode,
	        ];

	        $response = BlockChyp::enroll($request);
			return $response;
		}
		catch(Exception $e)
		{
		
		}
		
		return FALSE;
		
	}
}