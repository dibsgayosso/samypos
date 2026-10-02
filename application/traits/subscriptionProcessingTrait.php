<?php
require_once (APPPATH."libraries/blockchyp/vendor/autoload.php");

use \BlockChyp\BlockChyp;

trait subscriptionProcessingTrait
{
	private function process_sub($sub)
	{
		$this->load->model('Customer_subscription');
		
        BlockChyp::setApiKey($this->Location->get_info_for_key('blockchyp_api_key', $sub['location_id']));
        BlockChyp::setBearerToken($this->Location->get_info_for_key('blockchyp_bearer_token', $sub['location_id']));
        BlockChyp::setSigningKey($this->Location->get_info_for_key('blockchyp_signing_key', $sub['location_id']));
		$test_mode = (boolean)$this->Location->get_info_for_key('blockchyp_test_mode',$sub['location_id']);
		$this->session->set_userdata('employee_current_location_id', $sub['location_id']);
		
		$charge_data = array(
			'test' => $test_mode,
			'token' => $sub['card_on_file_token'],
			'amount' => to_currency_no_money($this->Customer_subscription->get_subscription_amount_with_tax($sub)),
		);			
		
		try
		{
			$response = BlockChyp::charge($charge_data);
			$failure = !($response['success'] && $response['approved']);
		}
		catch(Exception $e)
		{
			$failure = TRUE;
		}
		
		if ($failure)
		{
			$this->process_failed_charge($sub,$response);				
		}
		else
		{
			$this->process_successful_charge($sub,$response);
		}
		
	}
	
	private function process_failed_charge($sub,$response)
	{
		$this->load->model('Customer_subscription');
		$sub['status'] = 'failed';
		$sub['retries_attempted']++;
		
		if($sub['retries_attempted'] >=3)
		{
			//cancel
			$sub['status'] = 'cancelled';
		}
		else
		{
			$sub['next_retry_date'] = date('Y-m-d',strtotime('+2 days'));
		}
		
		$this->Customer_subscription->save($sub,$sub['id']);
	}
	
	private function process_successful_charge($sub,$response)
	{
		$this->load->model('Customer_subscription');
		$sub['status'] = 'current';
		$sub['next_payment_date'] = $this->Customer_subscription->get_next_payment_date($sub['id']);
		$sub['next_retry_date'] = NULL;
		$sub['retries_attempted'] = 0;
		
		$this->Customer_subscription->save($sub,$sub['id']);
		$this->Customer_subscription->save_sale_for_charge($sub,$response);
	}
}
