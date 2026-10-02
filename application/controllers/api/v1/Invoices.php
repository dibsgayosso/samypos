<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Invoices extends REST_Controller {
	
	protected $methods = [
		'index_get' 	=> ['level' => 1, 'limit' => 60],
		'index_post' 	=> ['level' => 2, 'limit' => 60],
		'index_delete' 	=> ['level' => 2, 'limit' => 60],
		'batch_post' 	=> ['level' => 2, 'limit' => 60],
    ];

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
		$this->load->model('Invoice');
    }
					
	private function _invoices_result_to_array($invoice_type, $invoice)
	{
		$this->load->helper('date');

		$invoice_details_charge = array();
		$invoice_details_credit = array();

		foreach($this->Invoice->get_details($invoice_type, $invoice->invoice_id) as $details){
			if($details['total'] > 0){
				$invoice_details_charge[] = $details;
			}else if($details['total'] < 0){
				$details['total'] = abs($details['total']);
				$invoice_details_credit[] = $details;
			}else{
				continue;
			}
		}

		$person_id = null;
		$person_po = null;

		if($invoice_type == 'customer'){
			$person_id = $invoice->customer_id ? (int)$invoice->customer_id : NULL;
			$person_po = $invoice->customer_po ? $invoice->customer_po : NULL;
		} else if($invoice_type == 'supplier'){
			$person_id = $invoice->supplier_id ? (int)$invoice->supplier_id : NULL;
			$person_po = $invoice->supplier_po ? $invoice->supplier_po : NULL;
		}

		$invoices_return = array(
			'invoice_id' => (int)$invoice->invoice_id,
			'location_id' => (int)$invoice->location_id,
			$invoice_type.'_id' => $person_id,
			$invoice_type.'_po' => $person_po,
			'invoice_date' => date_as_display_datetime($invoice->invoice_date),
			'due_date' => date_as_display_datetime($invoice->due_date),
			'total' => (float)$invoice->total,
			'balance' => (float)$invoice->balance,
			'last_paid' => date_as_display_datetime($invoice->last_paid),
			'person' => $invoice->person,
			'term_id' => (int)$invoice->term_id,
			'term_name' => $invoice->terms,
			'term_description' => $invoice->term_description,
			'payments' => $this->Invoice->get_payments($invoice_type, $invoice->invoice_id)->result_array(),
			'invoice_details_charge' => $invoice_details_charge,
			'invoice_details_credit' => $invoice_details_credit,
		);
		
		return $invoices_return;
	}

	private function _payments_result_to_array($payment)
	{
		$this->load->helper('date');

		$payment_return = array(
			'payment_id' => (int)$payment->payment_id,
			'invoice_id' => (int)$payment->invoice_id,
			'payment_date' => date_as_display_datetime($payment->payment_date),
			'payment_type' => $payment->payment_type,
			'total' => (float)$payment->payment_amount
		);
		
		return $payment_return;
	}
	
	function index_delete($invoice_type, $invoice_id)
	{

		if ($invoice_type === NULL || $invoice_id === NULL || !$invoice_type || !$invoice_id || !is_numeric($invoice_id))
		{
				$this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
		}

		$invoice = $this->Invoice->get_info($invoice_type, $invoice_id);
				
		if ($invoice->invoice_id  && !$invoice->deleted)
		{
			$this->Invoice->delete($invoice_type, $invoice->invoice_id );
			$invoice_return = $this->_invoices_result_to_array($invoice_type, $invoice);
			
			$this->response($invoice_return, REST_Controller::HTTP_OK);
		}
		else
		{
			$this->response(NULL, REST_Controller::HTTP_NOT_FOUND);
		}			
		
	}
				
    public function index_get($invoice_type, $invoice_id=null)
    {
		$this->load->helper('url');
		$this->load->helper('date');
		
		if ($invoice_id === NULL) {
      		$search = $this->input->get('search');
			$offset = $this->input->get('offset');
			$limit = $this->input->get('limit');

			$days_past_due = $this->input->get('days_past_due');
			$status = $this->input->get('status') ? $this->input->get('status') : "";
			$deleted=0;

			if ($limit !== NULL && $limit > 100)
			{
				$limit = 100;
			}

			$location_id = $this->input->get('location_id') ? $this->input->get('location_id') : 1;

			if ($search)
			{
				$sort_col = $this->input->get('sort_col') ? $this->input->get('sort_col') : 'invoice_id';
				$sort_dir = $this->input->get('sort_dir') ? $this->input->get('sort_dir') : 'asc';
				
				$invoices = $this->Invoice->search($invoice_type, $search, $days_past_due, $deleted, $limit!==NULL ? $limit : 20, $offset!==NULL ? $offset : 0, $sort_col, $sort_dir, $status, $location_id)->result();
				$total_records = $this->Invoice->search_count_all($invoice_type, $search, $days_past_due, $deleted, $status, $location_id);

			}
			else
			{
				$sort_col = $this->input->get('sort_col') ? $this->input->get('sort_col') : 'invoice_id';
				$sort_dir = $this->input->get('sort_dir') ? $this->input->get('sort_dir') : 'desc';

				$invoices = $this->Invoice->search($invoice_type, null, $days_past_due, $deleted, $limit!==NULL ? $limit : 20, $offset!==NULL ? $offset : 0, $sort_col, $sort_dir, $status, $location_id)->result();
				$total_records = $this->Invoice->search_count_all($invoice_type, null, $days_past_due, $deleted, $status, $location_id);
			}
			
			$invoices_return = array();
			foreach($invoices as $invoice)
			{
				$invoices_return[] = $this->_invoices_result_to_array($invoice_type, $invoice);
			}
			
			header("x-total-records: $total_records");
			
			$this->response($invoices_return, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
      	} else {    			
      		$invoice = $this->Invoice->get_info($invoice_type, $invoice_id);
      		if ($invoice->invoice_id) {
				$invoices_return = $this->_invoices_result_to_array($invoice_type, $invoice);
				$this->response($invoices_return, REST_Controller::HTTP_OK);
			} else {
				$this->response(NULL, REST_Controller::HTTP_NOT_FOUND);
			}			
      }
    }
		
    public function index_post($invoice_type, $invoice_id = NULL)
    {
		$invoice_request = json_decode($this->input->raw_input_stream, true);
		
		if ($invoice_id!== NULL) {
			$invoice_id = $this->_update_invoice($invoice_type, $invoice_id, $invoice_request);
			$invoice_return = $this->_invoices_result_to_array($invoice_type, $this->Invoice->get_info($invoice_type, $invoice_id));
			$this->response($invoice_return, REST_Controller::HTTP_OK);
		}
		
		if ($invoice_id = $this->_create_invoice($invoice_type, $invoice_request)) {
			$invoice_return = $this->_invoices_result_to_array($invoice_type, $this->Invoice->get_info($invoice_type, $invoice_id));
			$this->response($invoice_return, REST_Controller::HTTP_OK);
		}
		
		$this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
    }
			
    private function _create_invoice($invoice_type, $invoice_request)
    {
		
 			date_default_timezone_set($this->Location->get_info_for_key('timezone',isset($invoice_request['location_id']) && $invoice_request['location_id'] ? $invoice_request['location_id'] : 1));
			
			if(!isset($invoice_request[$invoice_type.'_id']) || !$invoice_request[$invoice_type.'_id'])
			{
				$this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
			}
			
			$invoice_data=array(
				'location_id' => isset($invoice_request['location_id']) && $invoice_request['location_id'] ? $invoice_request['location_id'] : 1,
				$invoice_type.'_id' => isset($invoice_request[$invoice_type.'_id']) && $invoice_request[$invoice_type.'_id'] ? $invoice_request[$invoice_type.'_id'] : NULL,
				$invoice_type.'_po' => isset($invoice_request[$invoice_type.'_po']) && $invoice_request[$invoice_type.'_po'] ? $invoice_request[$invoice_type.'_po'] : NULL,
				'term_id' => isset($invoice_request['term_id']) && $invoice_request['term_id'] ? $invoice_request['term_id'] : NULL,
				'invoice_date' => isset($invoice_request['start_time']) && $invoice_request['start_time'] ? date('Y-m-d H:i:s',strtotime($invoice_request['start_time'])) : date('Y-m-d H:i:s'),
				'due_date' => isset($invoice_request['end_time']) && $invoice_request['end_time'] ? date('Y-m-d H:i:s',strtotime($invoice_request['end_time'])) : date('Y-m-d H:i:s'),
			);

			if($this->Invoice->save($invoice_type, $invoice_data)){

				$invoice_id = 	$invoice_data['invoice_id'];

				if($invoice_request['invoice_details_charge']){
					$this->add_to_invoice_manual($invoice_type, $invoice_id, $invoice_request['invoice_details_charge']);
				}

				if($invoice_request['invoice_details_credit']){

					$invoice_details_credit = array();
					foreach($invoice_request['invoice_details_credit'] as $details){
						$details['total'] = abs($details['total'])*-1;
						$invoice_details_credit[] = $details;
					}

					$this->add_to_invoice_manual($invoice_type, $invoice_id, $invoice_details_credit);
				}

				return $invoice_id;
			}
    }
    
    private function _update_invoice($invoice_type, $invoice_id, $invoice_request)
    {
 		date_default_timezone_set($this->Location->get_info_for_key('timezone',isset($invoice_request['location_id']) && $invoice_request['location_id'] ? $invoice_request['location_id'] : 1));
		
		//Don't allow invoice primary key to change
		if (isset($invoice_request['invoice_id']))
		{
			unset($invoice_request['invoice_id']);
		}

		$invoice_data=array(
			'location_id' => isset($invoice_request['location_id']) && $invoice_request['location_id'] ? $invoice_request['location_id'] : 1,
			$invoice_type.'_id' => isset($invoice_request[$invoice_type.'_id']) && $invoice_request[$invoice_type.'_id'] ? $invoice_request[$invoice_type.'_id'] : NULL,
			$invoice_type.'_po' => isset($invoice_request[$invoice_type.'_po']) && $invoice_request[$invoice_type.'_po'] ? $invoice_request[$invoice_type.'_po'] : NULL,
			'term_id' => isset($invoice_request['term_id']) && $invoice_request['term_id'] ? $invoice_request['term_id'] : NULL,
			'invoice_date' => isset($invoice_request['start_time']) && $invoice_request['start_time'] ? date('Y-m-d H:i:s',strtotime($invoice_request['start_time'])) : date('Y-m-d H:i:s'),
			'due_date' => isset($invoice_request['end_time']) && $invoice_request['end_time'] ? date('Y-m-d H:i:s',strtotime($invoice_request['end_time'])) : date('Y-m-d H:i:s'),
		);
		
		if ($this->Invoice->save($invoice_type, $invoice_data, $invoice_id))
		{

			if($invoice_request['invoice_details_charge']){
				$this->add_to_invoice_manual($invoice_type, $invoice_id, $invoice_request['invoice_details_charge']);
			}

			if($invoice_request['invoice_details_credit']){

				$invoice_details_credit = array();
				foreach($invoice_request['invoice_details_credit'] as $details){
					$details['total'] = abs($details['total'])*-1;
					$invoice_details_credit[] = $details;
				}

				$this->add_to_invoice_manual($invoice_type, $invoice_id, $invoice_details_credit);
			}

			return $invoice_id;
		}
		
		return NULL;
    }

	function add_to_invoice_manual($invoice_type,$invoice_id,$invoice_details_request)
	{
		foreach($invoice_details_request as $invoice_details){
			$old_invoice_info = $this->Invoice->get_info($invoice_type,$invoice_id);
			$old_total = $old_invoice_info->total;
			$old_balance = $old_invoice_info->balance;

			$invoice_details_id = isset($invoice_details['invoice_details_id']) && $invoice_details['invoice_details_id'] && is_numeric($invoice_details['invoice_details_id']) ? $invoice_details['invoice_details_id'] : NULL;
			
			$total = isset($invoice_details['total']) && $invoice_details['total'] ? $invoice_details['total'] : 0;

			$type_prefix = $invoice_type == 'customer' ? 'sale' : 'receiving';

			$details_data = array(
				'invoice_id' => $invoice_id,
				'total' => $total,
				'description' => isset($invoice_details['description']) && $invoice_details['description'] ? $invoice_details['description'] : '',
				'account' => isset($invoice_details['account']) && $invoice_details['account'] ? $invoice_details['account'] : '',
				'line_id' => isset($invoice_details['line_id']) && $invoice_details['line_id'] ? $invoice_details['line_id'] : NULL,
                $type_prefix.'_id' => isset($invoice_details[$type_prefix.'_id']) && $invoice_details[$type_prefix.'_id'] ? $invoice_details[$type_prefix.'_id'] : NULL,
			);

			$this->Invoice->save_invoice_details($invoice_type, $details_data, $invoice_details_id);
			
			$new_total = $this->Invoice->get_total_from_invoice_details($invoice_type,$invoice_id);
			
			//Update balance and total since we just added a order to this invoice
			$total_change = $new_total - $old_total;
			$invoice_data = array('total' => $old_total + $total_change,'balance' => $old_balance + $total_change);
			$this->Invoice->save($invoice_type, $invoice_data, $invoice_id);

			if($invoice_details[$type_prefix.'_id']){
				$this->add_to_invoice($invoice_type, $invoice_id, $invoice_details[$type_prefix.'_id']);
			}
		}
	}
	
	function add_to_invoice($type, $invoice_id, $order_id)
	{
		
		//if already added then skip
		if ($this->Invoice->is_order_in_invoice($type,$invoice_id,$order_id)) {
			return false;
		}

		$old_invoice_info = $this->Invoice->get_info($type,$invoice_id);
		$old_total = $old_invoice_info->total;
		$old_balance = $old_invoice_info->balance;
		
		$details_data = array();
		$details_data['invoice_id'] = $invoice_id;
		if ($type=='customer')
		{
			$details_data['sale_id'] = $order_id;
			$details_data['total'] = $this->Sale->get_sale_total($order_id);
		}
		else
		{
			$details_data['receiving_id'] = $order_id;
			$details_data['total'] = $this->Receiving->get_receiving_total($order_id);	
		}

		$this->Invoice->save_invoice_details($type,$details_data);
		
		$new_total = $this->Invoice->get_total_from_invoice_details($type,$invoice_id);
		
		//Update balance and total since we just added a order to this invoice
		$total_change = $new_total - $old_total;
		$invoice_data = array('total' => $old_total + $total_change, 'balance' => $old_balance + $total_change);
		$this->Invoice->save($type,$invoice_data,$invoice_id);
		
		return true;
	}

	function payments_post($type)
	{
		$this->load->model("Customer");
		$payment_request = json_decode($this->input->raw_input_stream,TRUE);

		$invoice_id = $payment_request['invoice_id'];

		$invoice_info = $this->Invoice->get_info($type,$invoice_id);
		$address = $invoice_info->address_1;
		$zip = $invoice_info->zip;
		
		$payment_type = $payment_request['payment_type'];
		$amount = $payment_request['payment_amount'];

		if(!$payment_type || !$amount){
			$this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
		}

		$register = isset($payment_request['register']) && $payment_request['register'] && in_array($payment_request['register'], array('1','-1','-2')) ? $payment_request['register'] : 1;

		$cc_number = $payment_request['cc_number'];
		$ccv = $payment_request['cc_ccv'];
		
		$cc_token = FALSE;
		
		$is_coreclear_processing = $this->Location->get_info_for_key('credit_card_processor') == 'coreclear' || $this->Location->get_info_for_key('credit_card_processor') == 'coreclear2';
		
		if ($type == 'customer' && $payment_type == lang('common_credit') && $is_coreclear_processing)
		{
			if ($register == -2)
			{
				//Tokens only apply to customers right now
				$cc_token = $this->Customer->get_info($invoice_info->person_id)->cc_token;
			}
		
			list($expire_month,$expire_year) = explode('/',$payment_request['cc_exp_date']);
		
			$process_payment_response = $this->Invoice->process_payment($amount,$register,$cc_token,$cc_number,$ccv,$expire_month,$expire_year,$address,$zip);
		
			if($process_payment_response['success'])
			{
				$payment_data = $process_payment_response['payment_response_data'];
			
				$this->Invoice->add_payment($type,$invoice_id,$payment_data);
			
				//Update balance as we made a payment
				$invoice_data = array('balance' => $invoice_info->balance - $payment_data['payment_amount'],'last_paid' => date('Y-m-d'));
				$this->Invoice->save($type,$invoice_data,$invoice_id);
			
				$this->response(NULL, REST_Controller::HTTP_OK);
			
			}
			else
			{
				$this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
			}
		}
		else
		{
			$payment_data = array(
			    'payment_date' => date('Y-m-d H:i:s'),	
			    'payment_type' => $payment_type,
			    'payment_amount' => $amount,
			);
			$payment_id = $this->Invoice->add_payment($type,$invoice_id,$payment_data);
		
			//Update balance as we made a payment
			$invoice_data = array('balance' => $invoice_info->balance - $payment_data['payment_amount'],'last_paid' => date('Y-m-d'));
			$this->Invoice->save($type,$invoice_data,$invoice_id);

			$payment_return = $this->_payments_result_to_array($this->Invoice->get_payments($type, null, $payment_id)->row());
			$this->response($payment_return, REST_Controller::HTTP_OK);
		}
	}
	
	function payments_get($type, $payment_id=null){
		$this->load->helper('url');
		$invoice_id = $this->input->get('invoice_id');

		$offset = $this->input->get('offset') ? $this->input->get('offset') : 0;
		$limit = $this->input->get('limit') ? $this->input->get('limit') : 20;
		$sort_col = $this->input->get('sort_col') ? $this->input->get('sort_col') : 'payment_id';
		$sort_dir = $this->input->get('sort_dir') ? $this->input->get('sort_dir') : 'asc';

		$payments_query = $this->Invoice->get_payments($type, $invoice_id, $payment_id, $limit, $offset, $sort_col, $sort_dir)->result();

		if ($payment_id === NULL) {

			$payments = $payments_query->result();
			$total_records = $payments_query->num_rows();
			$payments_return = array();
			
			foreach($payments as $payment)
			{
				$payments_return[] = $this->_payments_result_to_array($payment);
			}
	
			header("x-total-records: $total_records");
			
			$this->response($payments_return, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}else if($payment_id){
			$payment = $payments_query->row();

			if($payment){
				$payments_return = $this->_payments_result_to_array($payment);
				$this->response($payments_return, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
			}else{
				$this->response(NULL, REST_Controller::HTTP_NOT_FOUND);
			}
		}
	}
}