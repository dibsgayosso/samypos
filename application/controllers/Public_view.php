<?php

require_once (APPPATH."models/cart/PHPPOSCartSale.php");
require_once (APPPATH."traits/taxOverrideTrait.php");

class Public_view extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();	
		$this->lang->load('sales');
		$this->lang->load('module');
		$this->load->helper('order');
		$this->load->helper('items');
		$this->load->helper('sale');
		$this->load->model('Sale');
		$this->load->model('Customer');
		$this->load->model('Tier');
		$this->load->model('Category');
		$this->load->model('Giftcard');
		$this->load->model('Tag');
		$this->load->model('Item');
		$this->load->model('Item_location');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_location_taxes');
		$this->load->model('Item_kit');
		$this->load->model('Item_kit_items');
		$this->load->model('Item_kit_taxes');
		$this->load->model('Item_location_taxes');
		$this->load->model('Item_taxes');
		$this->load->model('Item_taxes_finder');
		$this->load->model('Item_kit_taxes_finder');
		$this->load->model('Appfile');
		$this->load->model('Item_serial_number');
		$this->load->model('Price_rule');
		$this->load->model('Shipping_provider');
		$this->load->model('Shipping_method');
		$this->lang->load('deliveries');
		$this->load->model('Item_variation_location');
		$this->load->model('Item_variations');
		$this->load->helper('giftcards');
		$this->load->model('Item_attribute_value');
		$this->load->model('Item_modifier');
		
	}
	
	function _does_discount_exists($cart)
	{
		foreach($cart as $line=>$item)
		{
			if( (isset($item->discount) && $item->discount >0 ) || (is_array($item) && isset($item['discount_percent']) && $item['discount_percent'] >0 ) )
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
		
	function receipt($sms_sale_id)
	{ 
		require_once (APPPATH."libraries/hashids/vendor/autoload.php");
		
		$this->load->model('Sale');	
		$hashids = new Hashids\Hashids(base_url());
		$sale_id = current($hashids->decode($sms_sale_id));
		
		if ($sale_id === FALSE)
		{
			return;
		}
		$receipt_cart = PHPPOSCartSale::get_instance_from_sale_id($sale_id);
		if ($this->config->item('sort_receipt_column'))
		{
			$receipt_cart->sort_items($this->config->item('sort_receipt_column'));
		}
		
		$data = array();
		
		$data = array_merge($data,$receipt_cart->to_array());
		$data['is_sale'] = FALSE;
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		$data['is_sale_cash_payment'] = $receipt_cart->has_cash_payment();
		$data['show_payment_times'] = TRUE;
		$data['signature_file_id'] = $sale_info['signature_image_id'];
		
		$tier_id = $sale_info['tier_id'];
		$tier_info = $this->Tier->get_info($tier_id);
		$data['tier'] = $tier_info->name;
		$data['register_name'] = $this->Register->get_register_name($sale_info['register_id']);
		$data['override_location_id'] = $sale_info['location_id'];
		$data['deleted'] = $sale_info['deleted'];

		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : ( !$receipt_cart->suspended ? lang('sales_receipt') : '');
		$data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($sale_info['sale_time']));
		$customer_id=$receipt_cart->customer_id;
		
		$emp_info=$this->Employee->get_info($sale_info['employee_id']);
		$sold_by_employee_id=$sale_info['sold_by_employee_id'];
		$sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
		$data['payment_type']=$sale_info['payment_type'];
		$data['amount_change']=$receipt_cart->get_amount_due() * -1;
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $sale_info['employee_id'] ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
		$data['ref_no'] = $sale_info['cc_ref_no'];
		$data['auth_code'] = $sale_info['auth_code'];
		$data['discount_exists'] = $this->_does_discount_exists($data['cart_items']);
		$data['disable_loyalty'] = 0;
		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id;
		$data['sale_id_raw']=$sale_id;
		$data['store_account_payment'] = FALSE;
		$data['is_purchase_points'] = FALSE;
		
		foreach($data['cart_items'] as $item)
		{
			if ($item->name == lang('common_store_account_payment'))
			{
				$data['store_account_payment'] = TRUE;
				break;
			}
		}

		foreach($data['cart_items'] as $item)
		{
			if ($item->name == lang('common_purchase_points'))
			{
				$data['is_purchase_points'] = TRUE;
				break;
			}
		}
		
		if ($sale_info['suspended'] > 0)
		{
			if ($sale_info['suspended'] == 1)
			{
				$data['sale_type'] = ($this->config->item('user_configured_layaway_name') ? $this->config->item('user_configured_layaway_name') : lang('common_layaway'));
			}
			elseif ($sale_info['suspended'] == 2)
			{
				$data['sale_type'] = ($this->config->item('user_configured_estimate_name') ? $this->config->item('user_configured_estimate_name') : lang('common_estimate'));
			}
			else
			{
				$this->load->model('Sale_types');
				$data['sale_type'] = $this->Sale_types->get_info($sale_info['suspended'])->name;				
			}
		}
		
		$exchange_rate = $receipt_cart->get_exchange_rate() ? $receipt_cart->get_exchange_rate() : 1;
		
		if($receipt_cart->get_has_delivery())
		{
			$data['delivery_person_info'] = $receipt_cart->get_delivery_person_info();
						
			$data['delivery_info'] = $receipt_cart->get_delivery_info();
		}
		
		$data['standalone'] = TRUE;
		
		if($this->config->item('show_qr_code_for_sale') && $this->config->item('disable_verification_for_qr_codes')){
			$this->load->view("sales/receipt",$data);
		}else if($this->config->item('show_qr_code_for_sale') && !$this->config->item('disable_verification_for_qr_codes')){
			if(($customer_id && $this->Sale->has_email_or_phone($sale_id,$this->input->post('email_phone')))){
				$this->load->view("sales/receipt",$data);
			}else{
				$this->load->view("sales/receipt_auth",$data);
			}
		}else{
			$this->load->view("sales/receipt",$data);
		}
	}

	function pay_receipt($sms_sale_id) 
	{
		
		$invoice_id 		= do_decrypt($sms_sale_id,$this->Appconfig->get_secure_key());
		$this->invoice_type = 'customer';
		$type 				= 'customer';
		$registers = array();
		foreach($this->Register->get_all()->result() as $register)
		{
			$registers[$register->register_id] = $register->name;
		}
		
		$registers['-1'] = lang('sales_manual_entry');
		
		if ($type == 'customer')
		{
			$registers['-2'] = lang('sales_card_on_file');
		}
		
		
		$this->load->model('Sale');
		
		$payment_types = array();
				
		$payment_types[lang('common_cash')] 	= lang('common_cash');
		$payment_types[lang('common_check')] 	= lang('common_check');
		$payment_types[lang('common_credit')] 	= lang('common_credit');
				
		$data = array();
		$data['invoice_info'] 		= $this->Invoice->get_info($this->invoice_type,$invoice_id);
		$data['invoice_type'] 		= $this->invoice_type;
		$data['invoice_id'] 		= $invoice_id;
		$data['registers'] 			= $registers;
		$data['payments'] 			= $this->Invoice->get_payments($this->invoice_type,$invoice_id)->result_array();
		$data['payment_types'] 		= $payment_types;
		$is_coreclear_processing 	= $this->Location->get_info_for_key('credit_card_processor') == 'coreclear' || $this->Location->get_info_for_key('credit_card_processor') == 'coreclear2';
		$data['is_coreclear_processing'] = $is_coreclear_processing;

		$this->load->view("invoices/pay_receipt",$data);
	}


	public function start_cc_processing_coreclear2()
	{
		$cc_amount 				= $this->input->post('amount');
		$total 					= $this->input->post('total');
		$id 					= $this->input->post('id');
		$emp_location_id 		= $this->input->post('location_id');

		$credit_card_processor 	= $this->_get_cc_processor($emp_location_id);

		if ($credit_card_processor)
		{
			$credit_card_processor->do_start_cc_processing_without_login($cc_amount,$total,$id);
		}
		else
		{
			$this->_reload(array('error' => lang('sales_credit_card_processing_is_down')), false);
			return;
		}
	}

	function _get_cc_processor($emp_location_id = NULL)
	{
		require_once (APPPATH.'libraries/Coreclearblockchypprocessor.php');
		$credit_card_processor = new Coreclearblockchypprocessor($this,$emp_location_id);
		return $credit_card_processor;

	}


	function view_payment_receipt($id)
	{
		$invoice_id = do_decrypt($id,$this->Appconfig->get_secure_key());
		$data 		= $this->Invoice->get_payment_receipt($invoice_id);
		$this->load->view("invoices/payment_receipt",$data);
	}
	
	function customer_intake_form()
	{
		$allowed = $this->input->get('allowed');
		if ($allowed == md5('customer_view_'.$this->Appconfig->get_secure_key()))
		{
			if (count($this->input->post()))
			{
				$person_data = array(
				'title' => $this->input->post('title') ? $this->input->post('title') : null,
				'first_name'	=>	$this->input->post('first_name'),
				'last_name'		=>	$this->input->post('last_name'),
				'email'			=>	$this->input->post('email'),
				'phone_number'	=>	$this->input->post('phone_number'),
				'address_1'		=>	$this->input->post('address_1'),
				'address_2'		=>	$this->input->post('address_2') ? $this->input->post('address_2') : '',
				'city'			=>	$this->input->post('city'),
				'state'			=>	$this->input->post('state') ? $this->input->post('state') : '',
				'zip'			=>	$this->input->post('zip') ? $this->input->post('zip') : '',
				'country'		=>	$this->input->post('country') ? $this->input->post('country') : '',
				'comments'		=>	$this->input->post('comments') ? $this->input->post('comments') : '',
				);
		
		
				$customer_data=array();
				
				$this->Customer->save_customer($person_data,$customer_data);
					
				$this->load->view('customer_intake_form_success');
			
			}
			else
			{
				$this->load->view('customer_intake_form');
			}		
		}
	}	
	
	function build_timestamp()
	{
		echo BUILD_TIMESTAMP;
	}

}
?>