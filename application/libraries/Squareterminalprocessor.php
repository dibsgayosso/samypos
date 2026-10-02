<?php
require_once ("Creditcardprocessor.php");
require_once (APPPATH."libraries/square/vendor/autoload.php");

class Squareterminalprocessor extends Creditcardprocessor
{
	public $emv_terminal_id;
	public $square_access_token;
	public $register_tip_mode;
	function __construct($controller)
	{
		parent::__construct($controller);
		$this->controller->load->helper('sale');	
		$current_register_id = $this->controller->Employee->get_logged_in_employee_current_register_id();
		$this->square_access_token = $this->controller->Location->get_info_for_key('square_access_token');
		$register_info = $this->controller->Register->get_info($current_register_id);
		$this->emv_terminal_id = $register_info && property_exists($register_info,'emv_terminal_id') ? $register_info->emv_terminal_id : FALSE;
		$this->register_tip_mode = $register_info->enable_tips;
		
	}
			
			
	public function start_cc_processing()
	{
		$this->controller->load->view('sales/square_terminal_start_cc_processing');
		
	}
	
	private function create_order_from_cart_and_return_order()
	{
		
		$order = new \Square\Models\Order($this->controller->Location->get_info_for_key('square_location_id'));
		
		$items = array();
		
		$counter = 1;
		
		$taxes = array();
		$discounts = array();
		foreach($this->controller->cart->get_items() as $cart_item)
		{
			$base_price_money = new \Square\Models\Money();
			
			
			$base_price_money->setAmount((int)(round($cart_item->unit_price*100)));
			
			
			$base_price_money->setCurrency('USD');

			$item = new \Square\Models\OrderLineItem(to_quantity($cart_item->quantity));
			$item->setName($cart_item->name);
			$item->setBasePriceMoney($base_price_money);
			
			if ($cart_item->discount)
			{
				$discount_uid = $counter.'_'.$cart_item->discount;
				$order_line_item_applied_discount = new \Square\Models\OrderLineItemAppliedDiscount($discount_uid);
				$applied_discounts = [$order_line_item_applied_discount];
				$item->setAppliedDiscounts($applied_discounts);
				
				$order_line_item_discount = new \Square\Models\OrderLineItemDiscount();
				$order_line_item_discount->setUid($discount_uid);
				$order_line_item_discount->setName(lang('common_discount'));
				$order_line_item_discount->setType('FIXED_PERCENTAGE');
				$order_line_item_discount->setPercentage($cart_item->discount);
				$order_line_item_discount->setScope('LINE_ITEM');
				
				$discounts[] = $order_line_item_discount;
			}
			
			
			if (count($cart_item->modifier_items))
			{
				$modifiers = array();
				foreach($cart_item->modifier_items as $mod_item)
				{
					$order_line_item_modifier = new \Square\Models\OrderLineItemModifier();
					
					$mod_base_price_money = new \Square\Models\Money();
					
					$mod_base_price_money->setAmount((int)(round($mod_item['unit_price']*100)));
					$mod_base_price_money->setCurrency('USD');
					
					
					$order_line_item_modifier->setBasePriceMoney($mod_base_price_money);
					$order_line_item_modifier->setName($mod_item['display_name']);
					
					$modifiers[] = $order_line_item_modifier;
				}
				
				$item->setModifiers($modifiers);
			}
			
			$order_line_item_applied_taxes = array();
			
			
			if (!$this->controller->cart->all_items_have_same_taxes())
			{				
				foreach($cart_item->get_taxes() as $name_percent => $amount)
				{
					$line_item_tax_uid = $counter.'_'.alphanum($name_percent);
				
					$order_line_item_applied_tax = new \Square\Models\OrderLineItemAppliedTax($line_item_tax_uid);
					$order_line_item_applied_tax->setUid('APPLIED_'.$line_item_tax_uid);
					$order_line_item_applied_taxes[] = $order_line_item_applied_tax;
					
					list($percent,$name) = explode('% ',$name_percent);
					$order_line_item_tax = new \Square\Models\OrderLineItemTax();
					$order_line_item_tax->setUid($line_item_tax_uid);
					$order_line_item_tax->setName($name);
					$order_line_item_tax->setPercentage((float)$percent);
					$order_line_item_tax->setScope('LINE_ITEM');
					$taxes[]= $order_line_item_tax;
				}
			}
			
			if (count($order_line_item_applied_taxes) > 0)
			{
				$item->setAppliedTaxes($order_line_item_applied_taxes);
			}
			$items[] = $item;
			
			$counter++;
		}
				
		$order->setLineItems($items);
		
		if ($this->controller->cart->all_items_have_same_taxes())
		{
			foreach($this->controller->cart->get_taxes() as $name_percent => $value)
			{
				list($percent,$name) = explode('% ',$name_percent);
				$global_tax = new \Square\Models\OrderLineItemTax();
				$global_tax->setName($name);
				$global_tax->setPercentage((float)$percent);
				$global_tax->setScope('ORDER');
				$taxes[]= $global_tax;
			}
		}
		
		if (count($taxes) > 0)
		{
			$order->setTaxes($taxes);
		}
		
		if (count($discounts) > 0)
		{
			$order->setDiscounts($discounts);
		}
		
		$client = new \Square\SquareClient([
		    'accessToken' => $this->square_access_token,
		    'environment' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? \Square\Environment::SANDBOX : \Square\Environment::PRODUCTION,
		]);
		
		$body = new \Square\Models\CalculateOrderRequest($order);
		$api_response = $client->getOrdersApi()->calculateOrder($body);
		
		if ($api_response->isSuccess())
		{
			$calculate_result = $api_response->getResult()->getOrder();
			$cc_amount = to_currency_no_money($this->controller->cart->get_payment_amount(lang('common_credit')));
			$amount_money = new \Square\Models\Money();
			$amount_money->setAmount((int)(round($cc_amount*100)));
			$amount_money->setCurrency('USD');			
			if ($calculate_result->getTotalMoney()->getAmount() == $amount_money->getAmount())
			{
				$body = new \Square\Models\CreateOrderRequest();
				$body->setOrder($order);
				$body->setIdempotencyKey($this->_get_session_invoice_no(TRUE));

				$client = new \Square\SquareClient([
			    'accessToken' => $this->square_access_token,
			    'environment' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? \Square\Environment::SANDBOX : \Square\Environment::PRODUCTION,
				]);

				$api_response = $client->getOrdersApi()->createOrder($body);

				if ($api_response->isSuccess()) 
				{
			    	$result = $api_response->getResult();
					return $result->getOrder();
				}
				else 
				{
			 	    $errors = $api_response->getErrors();
				    return FALSE;
				}
			}
		}
		
		
		return false;
	}
	public function do_start_cc_processing()
	{
		try
		{
			///Make sure tokens fresh		
			$this->controller->Location->refresh_square_tokens();
		
			$cc_amount = to_currency_no_money($this->controller->cart->get_payment_amount(lang('common_credit')));
			$customer_id = $this->controller->cart->customer_id;
			$invoice_no = $this->_get_session_invoice_no(TRUE);
			$customer_name = '';
			if ($customer_id != -1)
			{
				$customer_info=$this->controller->Customer->get_info($customer_id);
				$customer_name = $customer_info->first_name.' '.$customer_info->last_name;
			}
		
			if ($cc_amount > 0)
			{							
				$amount_money = new \Square\Models\Money();
				$amount_money->setAmount((int)(round($cc_amount*100)));
				$amount_money->setCurrency('USD');

				$tip_settings = new \Square\Models\TipSettings();
				$tip_settings->setAllowTipping((boolean)($this->controller->config->item('enable_tips') || $this->register_tip_mode));
				$tip_settings->setSeparateTipScreen(true);
				$tip_settings->setCustomTipField(true);

				$device_options = new \Square\Models\DeviceCheckoutOptions($this->emv_terminal_id);
				$device_options->setSkipReceiptScreen(false);
				$device_options->setTipSettings($tip_settings);

				$checkout = new \Square\Models\TerminalCheckout($amount_money, $device_options);
				$order = $this->create_order_from_cart_and_return_order();
		
				
				if ($this->controller->cart->prompt_for_card)
				{
					$checkout->setPaymentType('MANUAL_CARD_ENTRY');
				}
				
				//Partial payments also supported here
				if ($order && $order->getTotalMoney()->getAmount() == $amount_money->getAmount())
				{
					$checkout->setOrderId($order->getId());			
				}
		
				//TODO something with this? Where is it stored
				$checkout->setReferenceId($this->controller->Sale->get_next_sale_id());

				$body = new \Square\Models\CreateTerminalCheckoutRequest($invoice_no, $checkout);
		
				$client = new \Square\SquareClient([
				    'accessToken' => $this->square_access_token,
				    'environment' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? \Square\Environment::SANDBOX : \Square\Environment::PRODUCTION,
				]);
		
				$api_response = $client->getTerminalApi()->createTerminalCheckout($body);
		
				if ($api_response && $api_response->isSuccess()) 
				{
				    $transaction = $api_response->getResult()->getCheckout();
				    $transaction_status = $transaction->getStatus();
					$terminal_checkout_id = $transaction->getId();
							
					$maxAttempts = 40;
					$attempt = 0;

					while ($attempt < $maxAttempts) 
					{
					    // Make the API call to get the transaction status
					    $api_response = $client->getTerminalApi()->getTerminalCheckout($terminal_checkout_id);
					    if ($api_response && $api_response->isSuccess()) 
						{
					        $transaction_status = $api_response->getResult()->getCheckout()->getStatus();
					        // Check if the transaction status is COMPLETED or CANCELED
					        if ($transaction_status === 'COMPLETED' || $transaction_status === 'CANCELED') 
							{
					            break; // Exit the loop if the desired status is reached
					        }

					        sleep(3);
					        $attempt++;
					    } 
						else 
						{
							if ($api_response)
							{
					        	$errors = $api_response->getErrors();
							}
							else
							{
								$errors[] = lang('common_error');
							}
						}
					}
				} 
				else 
				{
					if ($api_response)
					{
			        	$errors = $api_response->getErrors();
					}
					else
					{
						$errors[] = lang('common_error');
					}
				}
			
				if (isset($errors))
				{
					$this->controller->_reload(array('error' => lang('common_error')), false);
					return;
				}
				$response = $api_response->getResult()->getCheckout();
			
			 
		
				//Right now will just have 1 payment id if successful and 0 if NOT
				$square_checkout_payment_ids = $response->getPaymentIds();
				$square_payment = isset($response->getPaymentIds()[0]) ? $client->getPaymentsApi()->getPayment($response->getPaymentIds()[0])->getResult()->getPayment() : FALSE;
				if ($api_response && $api_response->isSuccess() && $response->getStatus() == 'COMPLETED' && count($square_checkout_payment_ids) > 0 && in_array($square_payment->getStatus(),array('APPROVED','COMPLETED')))
				{
					@$TextResponse = '';
			
					@$CardType = $square_payment->getCardDetails()->getCard()->getCardBrand();
					@$EntryMethod = $square_payment->getCardDetails()->getEntryMethod();
					@$ApplicationLabel = '';

					@$AID = '';
					@$TVR = '';
					@$IAD = '';
					@$TSI = '';
			
			
				   $MerchantID =  '';
				   $Signature = '';
				   $tip_amount = $square_payment->getTipMoney() ? ($square_payment->getTipMoney()->getAmount()/100) : 0;
				   $AcctNo =  $square_payment->getCardDetails()->getCard()->getLast4();
				   $TranCode = $response->getPaymentType();
				   $AuthCode = $square_payment->getCardDetails()->getAuthResultCode();
				   $RefNo = $square_payment->getId();
				   $Purchase = (($square_payment->getAmountMoney()->getAmount())/100)+$tip_amount;
				   $Authorize = $Purchase;
		   		   
				   $RecordNo = '';
		   
		   
				   $CCExpire = $square_payment->getCardDetails()->getCard()->getExpMonth().'/'.$square_payment->getCardDetails()->getCard()->getExpYear();
			
					$this->controller->session->set_userdata('ref_no', $RefNo);
					$this->controller->session->set_userdata('tip_amount', $tip_amount);
					$this->controller->session->set_userdata('auth_code', $AuthCode);
					$this->controller->session->set_userdata('cc_token', $RecordNo);
					$this->controller->session->set_userdata('entry_method', $EntryMethod);
					$this->controller->session->set_userdata('cc_signature', $Signature);
					$this->controller->session->set_userdata('tip_amount', $tip_amount);
						
					$this->controller->session->set_userdata('application_label', $ApplicationLabel);
					$this->controller->session->set_userdata('tran_type', $TranCode);
					$this->controller->session->set_userdata('text_response', $TextResponse);
			
			
					//return amount we need negative value
					if ($Purchase < 0)
					{
						$Authorize = abs($Authorize)*-1;
					}
			
					//Payment covers purchase amount
					if ($Authorize == $Purchase)
					{
						$this->controller->session->set_userdata('masked_account', $AcctNo);
						$this->controller->session->set_userdata('card_issuer', $CardType);				
				
						//If the sale payments cover the total, redirect to complete (receipt)
						if ($this->controller->_payments_cover_total())
						{
							$this->controller->session->set_userdata('CC_SUCCESS', TRUE);
							$this->log_charge($RefNo,$Authorize, true);
					
							//TODO fufil order so it shows up in orders section
					
							//https://developer.squareup.com/forums/t/order-is-not-showing-up-in-dashboard/855/3
							redirect(site_url('sales/complete'));
						}
						else //Change payment type to Partial Credit Card and show sales interface
						{							
							$credit_card_amount = to_currency_no_money($this->controller->cart->get_payment_amount(lang('common_credit')));
				
							$partial_transaction = array(
								'AuthCode' => $AuthCode,
								'MerchantID' => $MerchantID ,
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
						
					if (!$square_payment || !in_array($square_payment->getStatus(),array('APPROVED','COMPLETED')))
					{
						redirect(site_url('sales/declined'));
					}
					else
					{
						$this->controller->_reload(array('error' => lang('common_error')), false);
					}
				}
			}
			elseif($return_sale_id = $this->controller->cart->return_sale_id) //Do a return
			{
				if ($this->void_sale($return_sale_id,abs($cc_amount)))
				{
					$this->controller->session->set_userdata('CC_SUCCESS', TRUE);
					redirect(site_url('sales/complete'));
				}
				else
				{
					$this->controller->_reload(array('error' => lang('common_error')), false);
				}
			}
			else
			{
				$this->controller->_reload(array('error' => lang('sales_must_return_based_on_previous_sale')), false);
			}
		}
		catch(Exception $e)
		{
			$this->controller->_reload(array('error' => lang('common_error')), false);
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
	
	public function void_partial_transactions()
	{
		$void_success = true;
		
		if ($partial_transactions = $this->controller->cart->get_partial_transactions())
		{
			for ($k = 0;$k<count($partial_transactions);$k++)
			{
				$partial_transaction = $partial_transactions[$k];
				@$void_success = $this->void_sale_payment(to_currency_no_money($partial_transaction['Purchase']),$partial_transaction['RefNo']);
			}
		}
		return $void_success;
	}
	
	public function void_sale($sale_id,$refund_amount = null)
	{
		if ($this->controller->Sale->can_void_cc_sale($sale_id))
		{
			$void_success = true;
			
			$payments = $this->_get_cc_payments_for_sale($sale_id);
			
			$counter = 0;
			
			foreach($payments as $payment)
			{
				if ($counter == 0)
				{
					$sale_info = $this->controller->Sale->get_info($sale_id)->row();
					$tip = $sale_info->tip;
				}
				else
				{
					$tip = 0;
				}
				
				if (count($payments) == 1 && $refund_amount)
				{
					$payment['payment_amount'] = $refund_amount;
				}
				$void_success = $this->void_sale_payment($payment['payment_amount']+$tip,$payment['ref_no']);
				
				$counter++;
			}
			
			return $void_success;
		}
		
		return FALSE;
	}
	
	private function void_sale_payment($payment_amount,$payment_id)
	{
		$client = new \Square\SquareClient([
		    'accessToken' => $this->square_access_token,
		    'environment' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? \Square\Environment::SANDBOX : \Square\Environment::PRODUCTION,
		]);
		
		
		$amount_money = new \Square\Models\Money();
		$amount_money->setAmount((int)(round($payment_amount*100)));
		$amount_money->setCurrency('USD');

		$body = new \Square\Models\RefundPaymentRequest($this->_get_session_invoice_no(TRUE), $amount_money);
		$body->setPaymentId($payment_id);

		$api_response = $client->getRefundsApi()->refundPayment($body);

		if ($api_response->isSuccess()) 
		{
		    $status = $api_response->getResult()->getRefund()->getStatus();
			return $status =='PENDING' || $status =='COMPLETED';
		} else {
		    $errors = $api_response->getErrors();
		}
		
		return FALSE;
	}
	
	
	public function void_return($sale_id)
	{
		return false;
	}
	
	//Handled during transaction
	public function tip($sale_id,$tip_amount)
	{
		return FALSE;
	}
}