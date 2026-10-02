<?php
/*
This abstact class is implemented by any credit card processor in the system
*/

abstract class Creditcardprocessor
{
	public abstract function start_cc_processing();
	public abstract function finish_cc_processing();
	public abstract function cancel_cc_processing();
	public abstract function void_partial_transactions();
	public abstract function void_sale($sale_id);
	public abstract function void_return($sale_id);
	public abstract function tip($sale_id,$tip_amount);
	protected $controller;
	
	function __construct($controller) 
	{
		set_time_limit(0);
		ini_set('max_input_time','-1');
		ini_set('max_execution_time', 0);
		$this->controller = $controller;
	}
	
	protected function _get_session_invoice_no($clear_old = false)
	{
		if (!$this->controller->cart->invoice_no || $clear_old)
		{
			$this->controller->cart->invoice_no = substr((date('mdy')).(time() - strtotime("today")).($this->controller->Employee->get_logged_in_employee_info()->person_id), 0, 16);
			$this->controller->cart->save();
		}
		
		return $this->controller->cart->invoice_no;
	}
	
	protected function _is_valid_zip($zip)
	{
		if (strlen($zip) == 5 || strlen($zip) == 9)
		{
			return is_numeric($zip);
		}
		elseif(strlen($zip) == 10)
		{
			$parts = explode('-', $zip);
			return (count($parts) == 2 && is_numeric($parts[0]) && is_numeric($parts[1]));
		}
		return FALSE;
	}

	protected function _get_cc_payments_for_sale($sale_id)
	{
   	$this->controller->db->from('sales_payments');
		$this->controller->db->where('sale_id', $sale_id);
		$this->controller->db->where_in('payment_type', array(lang('common_credit'),lang('sales_partial_credit'), lang('common_ebt')));
		$this->controller->db->order_by('payment_id');
		
		return $this->controller->db->get()->result_array();
	}
	
	function log_charge($charge_id,$amount,$add_payment_to_cart)
 	{				
		if($add_payment_to_cart)
		{
			$masked_account = $this->controller->session->userdata('masked_account') ? $this->controller->session->userdata('masked_account') : '';
			$card_issuer = $this->controller->session->userdata('card_issuer') ? $this->controller->session->userdata('card_issuer') : '';
			$auth_code = $this->controller->session->userdata('auth_code') ? $this->controller->session->userdata('auth_code') : '';
			$ref_no = $this->controller->session->userdata('ref_no') ? $this->controller->session->userdata('ref_no') : '';
			$cc_token = $this->controller->session->userdata('cc_token') ? $this->controller->session->userdata('cc_token') : '';
			$acq_ref_data = $this->controller->session->userdata('acq_ref_data') ? $this->controller->session->userdata('acq_ref_data') : '';
			$process_data = $this->controller->session->userdata('process_data') ? $this->controller->session->userdata('process_data') : '';
			$entry_method = $this->controller->session->userdata('entry_method') ? $this->controller->session->userdata('entry_method') : '';
			$aid = $this->controller->session->userdata('aid') ? $this->controller->session->userdata('aid') : '';
			$tvr = $this->controller->session->userdata('tvr') ? $this->controller->session->userdata('tvr') : '';
			$iad = $this->controller->session->userdata('iad') ? $this->controller->session->userdata('iad') : '';
			$tsi = $this->controller->session->userdata('tsi') ? $this->controller->session->userdata('tsi') : '';
			$arc = $this->controller->session->userdata('arc') ? $this->controller->session->userdata('arc') : '';
			$cvm = $this->controller->session->userdata('cvm') ? $this->controller->session->userdata('cvm') : '';
			$tran_type = $this->controller->session->userdata('tran_type') ? $this->controller->session->userdata('tran_type') : '';
			$application_label = $this->controller->session->userdata('application_label') ? $this->controller->session->userdata('application_label') : '';
			
	        $this->controller->cart->delete_payment($this->controller->cart->get_not_processed_cc_payment_ids());
                  
             $this->controller->cart->add_payment(new PHPPOSCartPaymentSale(array(
                 'payment_type'             => lang('common_credit'),
                 'payment_amount'           => $amount,
                 'payment_date'             => date('Y-m-d H:i:s'),
                 'truncated_card'           => $masked_account,
                 'card_issuer'              => $card_issuer,
                 'ref_no'                   => $ref_no,
                 'auth_code'                => $auth_code,
                 'cc_token'                 => $cc_token,
                 'entry_method'             => $entry_method,
                 'tran_type'                => $tran_type,
                 'process_data'             => $process_data,
                 'aid'                      => $aid,
                 'tvr'                      => $tvr,
                 'iad'                      => $iad,
                 'tsi'                      => $tsi,
                 'application_label'        => $application_label,
        
             )));
        
             $this->controller->cart->save();
		}
		
 		$data = array(
 			'time_of_charge' => date('Y-m-d H:i:s'),
 			'register_id_of_charge' => $this->controller->Employee->get_logged_in_employee_current_register_id(),
 			'transaction_charge_id' => $charge_id,
 			'amount' => $amount,
 			'cart_data' => serialize($this->controller->cart),
 		);
		
		$this->controller->load->model('Credit_card_charge_unconfirmed');
 		return $this->controller->Credit_card_charge_unconfirmed->insert($data);
 	}
}
?>