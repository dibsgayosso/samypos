<?php
require_once (APPPATH."libraries/blockchyp/vendor/autoload.php");

use \BlockChyp\BlockChyp;

class Invoice extends CI_Model
{
	public function __construct()
	{
      parent::__construct();
	}
	
	public function get_info($type,$invoice_id)
	{
		$this->db->select('terms.name as term_name,terms.description as term_description,'.$type.'_'.'invoices.*,'.($type == 'customer' ? 'CONCAT(person.first_name, " ", person.last_name)' : 'company_name').' as person, person.zip as zip,person.address_1 as address_1, person.person_id as person_id', false);
		$this->db->from($type.'_'.'invoices');
		$this->db->join($type.'s', $type.'s.person_id = '.$type.'_'.'invoices.'.$type.'_id','left');
		$this->db->join('people as person', 'person.person_id = '.$type.'_'.'invoices.'.$type.'_id','left');
		$this->db->join('terms', 'terms.term_id = '.$type.'_'.'invoices.term_id','left');
		
		$this->db->where('invoice_id',$invoice_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $item_id is NOT an item
			$invoice_obj=new stdClass();

			//Get all the fields from items table
			$fields = $this->db->list_fields($type.'_'.'invoices');

			foreach ($fields as $field)
			{
				$invoice_obj->$field='';
			}			

			$invoice_obj->person = '';
			$invoice_obj->term_name = '';
			return $invoice_obj;
		}
	}
		
	
	/*
	Perform a search on invoices
	*/
	function search($type,$search, $days_past_due = NULL, $deleted = 0, $limit=20, $offset=0, $column='invoice_date', $orderby='desc',$status="",$location_id_override=null)
	{
		if (!$deleted)
		{
			$deleted = 0;
		}
		
		$location_id = $location_id_override ? $location_id_override : $this->Employee->get_logged_in_employee_current_location_id();
		
		$this->db->select('terms.name as terms,'.$type.'_'.'invoices.*,'.($type == 'customer' ? 'CONCAT(person.first_name, " ", person.last_name)' : 'company_name').' as person, person.last_name as person_last_name, terms.description as term_description', false);
		$this->db->from($type.'_'.'invoices');
		$this->db->join($type.'s', $type.'s.person_id = '.$type.'_'.'invoices.'.$type.'_id','left');
		$this->db->join('people as person', 'person.person_id = '.$type.'_'.'invoices.'.$type.'_id','left');
		$this->db->join('terms', 'terms.term_id = '.$type.'_'.'invoices.term_id','left');
		
		$this->db->where($type.'_'.'invoices.deleted', $deleted);
		$this->db->where($type.'_'.'invoices.location_id', $location_id);
		
		if ($status == 2) {
			$this->db->where($type.'_'.'invoices.balance >', 0);
		} elseif($status == 3) {
			$this->db->where($type.'_'.'invoices.balance <=', 0);
		} else {
			$this->db->where($type.'_'.'invoices.balance >', 0);
		}

		if ($days_past_due !== NULL)
		{
			if($days_past_due == 'current'){
				$this->db->where('datediff(NOW(),phppos_'.$type.'_'.'invoices.due_date) < 30', NULL, FALSE);
			}
			else{
				$this->db->where('datediff(NOW(),phppos_'.$type.'_'.'invoices.due_date) > '.$days_past_due, NULL, FALSE);
			}
			$this->db->where($type.'_invoices.balance > 0');
		}
		
		if ($search)
		{
			$this->db->where("(
			".($type == 'customer' ? 'person.full_name' : 'company_name')." LIKE '".$this->db->escape_like_str($search)."%' or
			".$type.'_invoices.'.$type."_po LIKE '".$this->db->escape_like_str($search)."%' or 
			person.first_name LIKE '".$this->db->escape_like_str($search)."%' or 
			person.last_name LIKE '".$this->db->escape_like_str($search)."%' or 
			person.email LIKE '".$this->db->escape_like_str($search)."%' or 
			person.phone_number LIKE '".$this->db->escape_like_str($search)."%' or 
			person.full_name LIKE '".$this->db->escape_like_str($search)."%') and ".$type."_invoices.deleted=$deleted");					
		}
				
		$this->db->order_by($column, $orderby);
		$this->db->limit($limit);
		$this->db->offset($offset);
		
		
	 return $this->db->get();
		
	}
	
	function search_count_all($type,$search, $days_past_due = NULL, $deleted = 0, $status="", $location_id_override=null)
	{
		if (!$deleted)
		{
			$deleted = 0;
		}
		
		$location_id = $location_id_override ? $location_id_override : $this->Employee->get_logged_in_employee_current_location_id();
		
				
		$this->db->select('terms.name as terms,'.$type.'_'.'invoices.*,'.($type == 'customer' ? 'CONCAT(person.first_name, " ", person.last_name)' : 'company_name').' as person, person.last_name as person_last_name', false);
		$this->db->from($type.'_'.'invoices');
		$this->db->join($type.'s', $type.'s.person_id = '.$type.'_'.'invoices.'.$type.'_id','left');
		
		$this->db->join('people as person', 'person.person_id = '.$type.'_'.'invoices.'.$type.'_id','left');
		
		$this->db->where($type.'_'.'invoices.deleted', $deleted);
		$this->db->where($type.'_'.'invoices.location_id', $location_id);
		
		if ($status == 2) {
			$this->db->where($type.'_'.'invoices.balance >', 0);
		} elseif($status == 3) {
			$this->db->where($type.'_'.'invoices.balance <=', 0);
		} elseif($status == 1) {
			
		} else {
			$this->db->where($type.'_'.'invoices.balance >', 0);
		}

		if ($days_past_due !== NULL)
		{
			if($days_past_due == 'current'){
				$this->db->where('datediff(NOW(),phppos_'.$type.'_'.'invoices.due_date) < 30', NULL, FALSE);
			}
			else{
				$this->db->where('datediff(NOW(),phppos_'.$type.'_'.'invoices.due_date) > '.$days_past_due, NULL, FALSE);
			}
			$this->db->where($type.'_invoices.balance > 0');
		}
				
		if ($search)
		{
			$this->db->where("(
			".($type == 'customer' ? 'person.full_name' : 'company_name')." LIKE '".$this->db->escape_like_str($search)."%' or
			".$type.'_invoices.'.$type."_po LIKE '".$this->db->escape_like_str($search)."%' or 
			person.first_name LIKE '".$this->db->escape_like_str($search)."%' or 
			person.last_name LIKE '".$this->db->escape_like_str($search)."%' or 
			person.email LIKE '".$this->db->escape_like_str($search)."%' or 
			person.phone_number LIKE '".$this->db->escape_like_str($search)."%' or 
			person.full_name LIKE '".$this->db->escape_like_str($search)."%') and ".$type."_invoices.deleted=$deleted");					
		}
								
		return $this->db->count_all_results();
		
	}
	
	/*
	Get search suggestions to find invoices
	*/
	function get_search_suggestions($type,$search,$deleted=0,$limit=5)
	{
		if (!trim($search))
		{
			return array();
		}
		if (!$deleted)
		{
			$deleted = 0;
		}
		
			$suggestions = array();
			$location_id = $this->Employee->get_logged_in_employee_current_location_id();
			
			$this->db->select($type.'_'.'invoices.*,'.($type == 'customer' ? 'CONCAT(person.first_name, " ", person.last_name)' : 'company_name').' as person, person.last_name as last_name, person.first_name as first_name', false);
			$this->db->from($type.'_'.'invoices');
		
			$this->db->join($type.'s', $type.'s.person_id = '.$type.'_'.'invoices.'.$type.'_id','left');
			$this->db->join('people as person', 'person.person_id = '.$type.'_'.'invoices.'.$type.'_id','left');
		
			$this->db->where($type.'_'.'invoices.deleted', $deleted);
				
			$this->db->where("(
			".($type == 'customer' ? 'person.full_name' : 'company_name')." LIKE '".$this->db->escape_like_str($search)."%' or
			".$type.'_invoices.'.$type."_po LIKE '".$this->db->escape_like_str($search)."%' or 
			person.first_name LIKE '".$this->db->escape_like_str($search)."%' or 
			person.last_name LIKE '".$this->db->escape_like_str($search)."%' or 
			person.email LIKE '".$this->db->escape_like_str($search)."%' or 
			person.phone_number LIKE '".$this->db->escape_like_str($search)."%' or 
			person.full_name LIKE '".$this->db->escape_like_str($search)."%') and ".$type."_invoices.deleted=$deleted");					
			
			$this->db->limit($limit);
			
			$query=$this->db->get();
			
			$temp_suggestions = array();
						
			foreach($query->result() as $row)
			{
				$data = array(
					'name' => $row->person,
					'subtitle' => '',
					'avatar' => base_url()."assets/img/user.png",
					 );
				$temp_suggestions[] = $data;
			}
		
		
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['subtitle']);		
			}
				
			return $suggestions;
	}	
	
	
	function get_all($type,$days_past_due = NULL,$deleted=0,$limit=10000, $offset=0,$col='invoice_date',$order='desc',$status = null)
	{	
		if (!$deleted)
		{
			$deleted = 0;
		}
		
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		
		$this->db->select('terms.name as terms,'.$type.'_'.'invoices.*,'.($type == 'customer' ? 'CONCAT(person.first_name, " ", person.last_name)' : 'company_name').' as person, person.last_name as person_last_name', false);
		$this->db->from($type.'_'.'invoices');
		$this->db->join($type.'s', $type.'s.person_id = '.$type.'_'.'invoices.'.$type.'_id','left');
		$this->db->join('people as person', 'person.person_id = '.$type.'_'.'invoices.'.$type.'_id','left');
		$this->db->join('terms', 'terms.term_id = '.$type.'_'.'invoices.term_id','left');
		$this->db->where($type.'_'.'invoices.deleted', $deleted);
		$this->db->where($type.'_'.'invoices.location_id', $location_id);
		
		if ($status == 2) {
			$this->db->where($type.'_'.'invoices.balance >', 0);
		} elseif($status == 3) {
			$this->db->where($type.'_'.'invoices.balance <=', 0);
		} elseif($status == 1) {
			
		} else {
			$this->db->where($type.'_'.'invoices.balance >', 0);
		}
		
		if ($days_past_due !== NULL)
		{
			if($days_past_due == 'current'){
				$this->db->where('datediff(NOW(),phppos_'.$type.'_'.'invoices.due_date) < 30', NULL, FALSE);
			}
			else{
				$this->db->where('datediff(NOW(),phppos_'.$type.'_'.'invoices.due_date) > '.$days_past_due, NULL, FALSE);
			}
			$this->db->where($type.'_invoices.balance > 0');
		}
			
		$this->db->order_by($col, $order);
		$this->db->limit($limit);
		$this->db->offset($offset);
  	  	return $this->db->get();
		
	}
	
	function count_all($type,$days_past_due = NULL,$deleted=0,$status=NULL)
	{
		if (!$deleted)
		{
			$deleted = 0;
		}
		
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		
		
		$this->db->select($type.'_'.'invoices.*,CONCAT(person.first_name, " ", person.last_name) as person, person.last_name as person_last_name', false);
		$this->db->from($type.'_'.'invoices');
		$this->db->where($type.'_'.'invoices.deleted', $deleted);		
		$this->db->where($type.'_'.'invoices.location_id', $location_id);
		
		if ($status == 2) {
			$this->db->where($type.'_'.'invoices.balance >', 0);
		} elseif($status == 3) {
			$this->db->where($type.'_'.'invoices.balance <=', 0);
		} elseif($status == 1) {
			
		} else {
			$this->db->where($type.'_'.'invoices.balance >', 0);
		}

		if ($days_past_due !== NULL)
		{
			if($days_past_due == 'current'){
				$this->db->where('datediff(NOW(),phppos_'.$type.'_'.'invoices.due_date) < 30', NULL, FALSE);
			}
			else{
				$this->db->where('datediff(NOW(),phppos_'.$type.'_'.'invoices.due_date) > '.$days_past_due, NULL, FALSE);
			}
			$this->db->where($type.'_invoices.balance > 0');
		}
		

		return $this->db->count_all_results();		
	}
	

	function exists($type,$id)
	{
		$this->db->from($type.'_'.'invoices');
		$this->db->where('invoice_id',$id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}
	
	/*
	Inserts or updates a invoice
	*/
	function save($type,&$invoice_data, $invoice_id = false)
	{		
		
		//If the balance is 0 or less mark any store account sales as paid
		if ($invoice_id && isset($invoice_data['balance']) && $invoice_data['balance'] <= 1e-6)
		{
			$this->mark_store_account_orders_as_paid_for_invoice($type,$invoice_id);
		}
		
		if (!$invoice_id or !$this->exists($type,$invoice_id))
		{
			if($this->db->insert($type.'_'.'invoices',$invoice_data))
			{
				$invoice_data['invoice_id']=$this->db->insert_id();
				return true;
			}
			return false;
		}
		
		$this->db->where('invoice_id', $invoice_id);
		return $this->db->update($type.'_'.'invoices',$invoice_data);
		
	}
	
	function delete($type,$id)
	{	
		$this->db->where('invoice_id', $id);
		return $this->db->update($type.'_'.'invoices', array('deleted' => 1));
	}
	
	function delete_list($type,$invoice_ids)
	{
		foreach($invoice_ids as $invoice_id)
		{
			$result = $this->delete($type,$invoice_id);
			
			if(!$result)
			{
				return false;
			}
		}
		
		return true;
 	}
		
	function undelete($type,$id)
	{	
		$this->db->where('invoice_id', $id);
		return $this->db->update($type.'_'.'invoices', array('deleted' => 0));
	}
	
	function undelete_list($type,$invoice_ids)
	{
		foreach($invoice_ids as $invoice_id)
		{
			$result = $this->undelete($type,$invoice_id);
			
			if(!$result)
			{
				return false;
			}
		}
		
		return true;
 	}
		
	function get_displayable_columns($type)
	{
		return array(
			'invoice_id' =>  array('sort_column' => $type.'_'.'invoices.invoice_id', 'label' => lang('invoices_invoice'),'format_function' => 'strsame'),
			
			'person' => array('sort_column' => 'last_name', 'label' => lang('invoices_'.$type), 'format_function' => 'strsame'),
			'terms' => array('sort_column' => 'terms', 'label' => lang('invoices_terms'), 'format_function' => 'strsame'),
			'invoice_date' => array('sort_column' => $type.'_'.'invoices.invoice_date', 'label' => lang('invoices_invoice_date'), 'format_function' => 'date_as_display_date'),
			'due_date' => array('sort_column' => $type.'_'.'invoices.due_date', 'label' => lang('invoices_due_date'), 'format_function' => 'date_as_display_date'),
			'total' => array('sort_column' => 'total', 'label' => lang('common_total'), 'format_function' => 'to_currency','html' => TRUE),
			'balance' => array('sort_column' => 'balance', 'label' => lang('common_balance'), 'format_function' => 'to_currency','html' => TRUE),
			'last_paid' => array('sort_column' => $type.'_'.'invoices.last_paid', 'label' => lang('invoices_last_paid'), 'format_function' => 'date_as_display_date'),
			"$type".'_po' => array('sort_column' => $type.'_'.'invoices.po', 'label' => lang('invoices_po_'.$type), 'format_function' => 'strsame'),
		);
	}
	
	function get_default_columns($type)
	{
		return array('invoice_id','person','terms','invoice_date','due_date','total','balance','last_paid');
	}
	
	
	function get_all_terms($limit=10000, $offset=0,$col='name',$order='asc')
	{
		$this->db->from('terms');
		$this->db->where('deleted', 0);
		
		$this->db->order_by($col, $order);
		
		$this->db->limit($limit);
		$this->db->offset($offset);
		
		$return = array();
		
		foreach($this->db->get()->result_array() as $result)
		{
			$return[$result['term_id']] = $result;
		}
		
		return $return;
	}
	
	function get_term($term_id)
	{
		$this->db->from('terms');
		$this->db->where('term_id', $term_id);
		
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $item_id is NOT an item
			$term_obj=new stdClass();

			//Get all the fields from items table
			$fields = $this->db->list_fields('terms');

			foreach ($fields as $field)
			{
				$term_obj->$field='';
			}			

			return $term_obj;
		}
	}

	function get_term_by_name($name){
		$this->db->from('terms');
		$this->db->where('name', $name);
		
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $item_id is NOT an item
			$term_obj=new stdClass();

			//Get all the fields from items table
			$fields = $this->db->list_fields('terms');

			foreach ($fields as $field)
			{
				$term_obj->$field='';
			}			

			return $term_obj;
		}
	}

	function save_term($term_data, $term_id = FALSE)
	{
		if ($term_id == FALSE)
		{
			if($this->db->insert('terms',$term_data))
			{
				$term_id = $this->db->insert_id();
			}
		}
		else
		{
			$this->db->where('term_id', $term_id);
			$this->db->update('terms',$term_data);
		}
				
		return $term_id;
	}
	
	/*
	Deletes one tag
	*/
	function delete_term($term_id)
	{		
		$this->db->where('term_id', $term_id);
		return $this->db->update('terms', array('deleted' => 1));
	}	
	
	function get_details($type,$invoice_id)
	{
		$this->db->from($type.'_invoice_details');
		$this->db->where('invoice_id',$invoice_id);
		
		return $this->db->get()->result_array();
	}
	
	function save_invoice_details($type,$details_data,$invoice_details_id = NULL)
	{
		if ($invoice_details_id)
		{
			$this->db->where('invoice_details_id',$invoice_details_id);
			$this->db->update($type.'_invoice_details',$details_data);
		}
		else
		{
			$type_prefix = $type == 'customer' ? 'sale' : 'receiving';
			
			if (!isset($details_data[$type_prefix.'_id']) || !$this->is_order_in_invoice($type,$details_data[$type_prefix.'_id']))
			{
				$this->db->insert($type.'_invoice_details',$details_data);			
			}
		}
	}
	
	function get_invoice_for_order_id($type,$order_id)
	{
		$type_prefix = $type == 'customer' ? 'sale' : 'receiving';
		
		$this->db->from($type.'_invoice_details');
		$this->db->where($type_prefix.'_id',$order_id);
		$query = $this->db->get();

		$row = $query->row();
		
		if ($row->invoice_id)
		{
			return $row->invoice_id;
		}
		
		return FALSE;
	}
	
	function is_order_in_invoice($type,$order_id)
	{
		$type_prefix = $type == 'customer' ? 'sale' : 'receiving';
		
		$this->db->from($type.'_invoice_details');
		$this->db->where($type_prefix.'_id',$order_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
		
	}
	
	function get_invoice_id_for_detail($type,$invoice_details_id)
	{
		$this->db->from($type.'_invoice_details');
		$this->db->where('invoice_details_id',$invoice_details_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row()->invoice_id;
		}
		
		return NULL;
	}
	
	function get_total_from_invoice_details($type,$invoice_id)
	{
		$this->db->select("sum(total) as total_from_details",FALSE);
		$this->db->from($type.'_invoice_details');
		$this->db->where('invoice_id',$invoice_id);
		$query = $this->db->get();
		
		return $query->row()->total_from_details;
	}
	
	function delete_invoice_details($type,$invoice_details_id)
	{		
		$this->db->delete($type.'_invoice_details',array('invoice_details_id' => $invoice_details_id));
	}
	
	function delete_invoice_details_by_order($type,$order_id)
	{		
		$type_prefix = $type == 'customer' ? 'sale' : 'receiving';
		
		$this->db->delete($type.'_invoice_details',array($type_prefix.'_id' => $order_id));
	}
	
	
	public function process_payment($amount,$register,$cc_token,$cc_number = NULL, $cc_ccv = NULL,$expire_month = NULL,$expire_year = NULL,$address = NULL, $zip = NULL)
	{
		if ($this->Location->get_info_for_key('credit_card_processor') == 'coreclear')
		{
			return $this->process_mx($amount,$register,$cc_token,$cc_number,$cc_ccv,$expire_month,$expire_year,$address,$zip);
		}
		elseif ($this->Location->get_info_for_key('credit_card_processor') == 'coreclear2')
		{
			return $this->process_blockchyp($amount,$register,$cc_token,$cc_number,$cc_ccv,$expire_month,$expire_year,$address,$zip);			
		}
	}
	
	private function process_mx($amount,$register,$paymentToken = NULL, $card_number = NULL,$cvc = NULL, $expiryMonth = NULL,$expiryyear = NULL,$avsStreet=NULL, $avsZip=NULL)
	{		
		$amount = (double)$amount;
		$is_card_not_present = $register == -1;		
		$register_info = $this->Register->get_info($register);

		$terminal_id = $register_info->coreclear_terminal_id;
		$card_reader_type = $register_info->coreclear_card_reader_type;
		$cur_location_info = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id());
		
		$test_mode = $cur_location_info->coreclear_sandbox;
		$merchant_id = $cur_location_info->coreclear_merchant_id;
		$authorization_key = $cur_location_info->coreclear_authorization_key;
		$authorization_key_created = $cur_location_info->coreclear_authorization_key_created;
		$coreclear_user = $cur_location_info->coreclear_user;
		$coreclear_password = $cur_location_info->coreclear_password;
		
		
		$authorization_key_created_timestamp = strtotime($authorization_key_created);
		$current_timestamp = time();

		//When processing cards the value of the config field "Token Create Date" is > 8 hours old from the current time then we need to get a new token before processing the credit cards
		if($current_timestamp-$authorization_key_created_timestamp > 28800){
			$get_coreclear_authorization_key = $this->Location->get_coreclear_authorization_key($test_mode,$merchant_id,$coreclear_user,$coreclear_password);
			$authorization_key = $get_coreclear_authorization_key['jwtToken'];
			$authorization_key_created = $get_coreclear_authorization_key['coreclear_authorization_key_created'];
			
			$authorization_key_update_data = array(
				'coreclear_authorization_key' => $authorization_key,
				'coreclear_authorization_key_created' => $authorization_key_created,
			);
			$this->Location->save($authorization_key_update_data,$this->Employee->get_logged_in_employee_current_location_id());
		}
		
		
		if ($is_card_not_present)
		{

			if($test_mode){
				$uri = "https://sandbox.api.mxmerchant.com";
			}
			else{
				$uri = "https://api.mxmerchant.com";
			}

			$method = "checkout/v3/payment?echo=true";
			$endpoint = $uri.'/'.$method;

			if(!$avsStreet)
			{
				$avsStreet = '';
			}
			
			if (!$avsZip)
			{
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
				CURLOPT_USERPWD => $coreclear_user.":".$coreclear_password,
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
	
			if($response_code == 200 || $response_code == 201)
			{
				$this->Logs->logs_save($coreclear_user.":".$coreclear_password,$uri,$method,$data,$total_time,"1","","",$response,'coreCLEAR');
			
				if($response_data['status'] == 'Settled' || $response_data['status'] == 'Approved')
				{
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
					
					
	 			   $payment_response_data = array(
	 				    'payment_date' => date('Y-m-d H:i:s'),	
	 				    'payment_type' => lang('common_credit'),
	 				    'payment_amount' => $amount,
	 				    'auth_code' => $auth_code,
	 				    'ref_no' => $charge_id,
	 				    'cc_token' => $cc_token,
	 				    'entry_method' => $entry_method,
	 				    'tran_type' => $tran_type,
	 				    'truncated_card' => $masked_account,
	 				    'card_issuer' => $card_brand,
						'acq_ref_data' => $acq_ref_data,
						'process_data' => $process_data,
	 				);
			
	 				return array('success' => TRUE, 'payment_response_data' => $payment_response_data);
					
				}
				else
				{
					
					return array('success' => FALSE,'payment_response_data' => NULL);
				}
			}
			else
			{
				$this->Logs->logs_save($coreclear_user.":".$coreclear_password,$uri,$method,$data,$total_time,"0","","",$response,'coreCLEAR');
				return array('success' => FALSE,'payment_response_data' => NULL);
			}
		
		}
		else
		{
			if($test_mode){
				$uri = "https://sandbox-api2.mxmerchant.com";
			}
			else{
				$uri = "https://api2.mxmerchant.com";
			}
	
			$method = "terminal/v1/transaction/merchantid/".$merchant_id."/terminalid/".$terminal_id;
				
			
			//TODO need to do
			if($paymentToken != '')
			{
				
				return array('success' => FALSE,'payment_response_data' => NULL);
			}
			else{
				
				$data = array(
					'amount' => $amount,
					'type' => "Sale",
				);
					
				

				$replayId = $cur_register_id.date('ymdHis');
				$data['replayId'] = $replayId;
			
				$endpoint = $uri.'/'.$method;
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
					CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer ".$authorization_key,
						"Content-Type: application/json",
						"cache-control: no-cache"
					),
				));
		
				$response = curl_exec($curl);
				
				
				$err = curl_error($curl);
		
				$total_time = curl_getinfo($curl, CURLINFO_TOTAL_TIME)*1000;
		
				curl_close($curl);
		
				$response_data = json_decode($response,TRUE);
				
				if($response_data['status'] == 'SENTTOTERMINAL'){
					$this->Logs->logs_save($authorization_key,$uri,$method,$data,$total_time,"1","","",$response,'coreCLEAR');
					
					if($test_mode){
						$uri = "https://sandbox.api.mxmerchant.com";
					}
					else{
						$uri = "https://api.mxmerchant.com";
					}
	
					$method = "checkout/v3/payment?merchantId=".$merchant_id."&replayId=".$replayId;
	
					$get_transaction_result_start_time = $this->microtime_float();
	
					// we need a delay of 3 seconds
					sleep(3);
					
					//If the transaction is not complete we will get an error 404 (not found), so we need to keep calling every 2 seconds (I think we will have to time out after a certain amount of time, I'm thinking 1 minute).
					$get_transaction_result = $this->get_transaction_result($uri,$method,$coreclear_user,$coreclear_password,$get_transaction_result_start_time);
					$transaction_result_response_data = json_decode($get_transaction_result['response'],TRUE);
			
					if($get_transaction_result['success']){
						if (isset($transaction_result_response_data['amount']))
						{
							$Authorize = to_currency_no_money($transaction_result_response_data['amount']);						
						}
						
						$charge_id = $transaction_result_response_data['id'];
						$masked_account = $transaction_result_response_data['cardAccount']['last4'];
						$card_brand = $transaction_result_response_data['cardAccount']['cardType'];
						$auth_code = $transaction_result_response_data['authCode'];
						$cc_token = $transaction_result_response_data['paymentToken'];
						$cc_exp = $transaction_result_response_data['cardAccount']['expiryMonth'].$transaction_result_response_data['cardAccount']['expiryYear'];
						$entry_method = $transaction_result_response_data['cardAccount']['entryMode'];
						$tran_type = $preauth_completed_charge === 0 ? lang('sales_preauthorized_card') : $transaction_result_response_data['type'];
						$acq_ref_data = $transaction_result_response_data['reference'];
						$process_data = $transaction_result_response_data['authMessage'];
						
	 	 			   $payment_response_data = array(
	 	 				    'payment_date' => date('Y-m-d H:i:s'),	
	 	 				    'payment_type' => lang('common_credit'),
	 	 				    'payment_amount' => $amount,
	 	 				    'auth_code' => $auth_code,
	 	 				    'ref_no' => $charge_id,
	 	 				    'cc_token' => $cc_token,
	 	 				    'entry_method' => $entry_method,
	 	 				    'tran_type' => $tran_type,
	 	 				    'truncated_card' => $masked_account,
	 	 				    'card_issuer' => $card_brand,
	 						'acq_ref_data' => $acq_ref_data,
	 						'process_data' => $process_data,
	 	 				);
			
	 	 				return array('success' => TRUE, 'payment_response_data' => $payment_response_data);
						
					}
					else{
	
						if($get_transaction_result['timeout']){
							//If a timeout occurred we should call the following method:
							if($test_mode){
								$uri = "https://sandbox-api2.mxmerchant.com";
							}
							else{
								$uri = "https://api2.mxmerchant.com";
							}
					
							$method = "terminal/v1/transaction/merchantid/".$merchant_id."/terminalid/".$terminal_id;
	
							$endpoint = $uri.'/'.$method;
					
							$curl = curl_init();
							curl_setopt_array($curl, array(
								CURLOPT_URL => $endpoint,
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => "",
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 360,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => "DELETE",
								CURLOPT_HTTPHEADER => array(
									"Authorization: Bearer ".$authorization_key,
									"Content-Type: application/json",
									"cache-control: no-cache"
								),
							));
					
							$response = curl_exec($curl);
							$err = curl_error($curl);
					
							$total_time = curl_getinfo($curl, CURLINFO_TOTAL_TIME)*1000;
					
							curl_close($curl);
					
							$this->Logs->logs_save($authorization_key,$uri,$method,'',$total_time,"1","","",$response,'coreCLEAR');
							return array('success' => FALSE,'payment_response_data' => NULL);
							
						}
						else{
							return array('success' => FALSE,'payment_response_data' => NULL);
							
						}
					}
				}
				else{
					$this->Logs->logs_save($authorization_key,$uri,$method,$data,$total_time,"0","","",$response,'coreCLEAR');
		
					return array('success' => FALSE,'payment_response_data' => NULL);
					
				}
			}
		}		
	}
	
	private function process_blockchyp($amount,$register,$cc_token,$cc_number = NULL,$cc_ccv = NULL, $expire_month = NULL,$expire_year = NULL)
	{
		$is_card_not_present = $register == -1;
		$register_info = $this->Register->get_info($register);
		$emv_terminal_id = $register_info && property_exists($register_info,'emv_terminal_id') ? $register_info->emv_terminal_id : FALSE;
		
		try
		{
	    	BlockChyp::setApiKey($this->Location->get_info_for_key('blockchyp_api_key'));
	    	BlockChyp::setBearerToken($this->Location->get_info_for_key('blockchyp_bearer_token'));
	    	BlockChyp::setSigningKey($this->Location->get_info_for_key('blockchyp_signing_key'));
		
			if ($is_card_not_present)
			{
			
		        $charge_data = [
		            'pan' => $cc_number,
		            'expMonth' => $expire_month,
		            'expYear' => $expire_year,
					'amount' => to_currency_no_money($amount),
					'test' => (boolean)$this->Location->get_info_for_key('blockchyp_test_mode'),
					'enroll' => TRUE,
		        ];				
			}
			else
			{
				
				if ($cc_token)
				{
					$charge_data = array(
					'test' => (boolean)$this->Location->get_info_for_key('blockchyp_test_mode'),
					'token' => $cc_token,
					'amount' => to_currency_no_money($amount),
					'enroll' => TRUE,
					);	
					
				}
				else
				{
					$charge_data = array(
						'test' => (boolean)$this->Location->get_info_for_key('blockchyp_test_mode'),
						'terminalName' => $emv_terminal_id,
						'amount' => to_currency_no_money($amount),
						'enroll' => TRUE,
					);
				}
			}
		
			$response = BlockChyp::charge($charge_data);
			
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
					@$EntryMethod = $is_card_not_present ? lang('sales_manual_entry') : lang('common_credit');
					@$ApplicationLabel = $EntryMethod;
					@$CardType =  $EntryMethod;
				}
			
			   $AcctNo = $response['maskedPan'];
			   $AuthCode = $response['authCode'];
			   $RefNo = $response['transactionId'];
		   
		   
			   $RecordNo = $response['token'];
				
			   $payment_response_data = array(
				    'payment_date' => date('Y-m-d H:i:s'),	
				    'payment_type' => lang('common_credit'),
				    'payment_amount' => $amount,
				    'auth_code' => $AuthCode,
				    'ref_no' => $RefNo,
				    'cc_token' => $RecordNo,
				    'entry_method' => $EntryMethod,
				    'aid'  => $AID,
				    'tvr' => $TVR,
				    'iad' => $IAD,
				    'tsi' => $TSI,
				    'tran_type' => lang('sales_card_transaction'),
				    'application_label' => $ApplicationLabel,
				    'truncated_card' => $AcctNo,
				    'card_issuer' => $CardType,
				);
			}
			
			return array('success' => ($response['success'] && $response['approved']), 'payment_response_data' => $payment_response_data);
		}
		catch(Exception $e)
		{
			return array('success' => FALSE,'payment_response_data' => NULL);
		}
	}
	
	function add_payment($type,$invoice_id,$payment_data)
	{
		$payment_data['invoice_id'] = $invoice_id;
		$this->db->insert($type.'_'.'invoice_payments',$payment_data);
		
		return $this->db->insert_id();
	}
	
	function get_payments($type, $invoice_id=null, $payment_id=null, $limit=null, $offset=null, $sort_col=null, $sort_dir=null)
	{
		$this->db->from($type.'_'.'invoice_payments');

		if($invoice_id){
			$this->db->where('invoice_id',$invoice_id);
		}

		if($payment_id){
			$this->db->where('payment_id', $payment_id);
		}

		if($sort_col && $sort_dir){
			$this->db->order_by($sort_col, $sort_dir);
		}else{
			$this->db->order_by('payment_date DESC');
		}
		
		if($limit){
			$this->db->limit($limit);
		}

		if($limit){
			$this->db->offset($offset);
		}

   	 	return $this->db->get();
	}
	
	private function microtime_float()
	{
			list($usec, $sec) = explode(" ", microtime());
			return ((float)$usec + (float)$sec);
	}
	
	private function get_transaction_result($uri,$method,$coreclear_user,$coreclear_password,$get_transaction_result_start_time){
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
			CURLOPT_USERPWD => $coreclear_user.":".$coreclear_password,
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
			$this->Logs->logs_save($coreclear_user.":".$coreclear_password,$uri,$method,'',$total_time,"1","","",$response,'coreCLEAR');
			
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
					return $this->get_transaction_result($uri,$method,$coreclear_user,$coreclear_password,$get_transaction_result_start_time);
				}
				
			}
			else{
				return array('success'=>false,'response'=>$response);
			}
		}
		else if($response_code == 404){
			$get_transaction_result_end_time = $this->microtime_float();
			
			//If the transaction is not complete we will get an error 404 (not found), so we need to keep calling every 2 seconds (I think we will have to time out after a certain amount of time, I'm thinking 1 minute).
			if(($get_transaction_result_end_time - $get_transaction_result_start_time)>60){
				return array('success'=>false,'timeout'=>true);
			}
			else{
				sleep(2);
				return $this->get_transaction_result($uri,$method,$coreclear_user,$coreclear_password,$get_transaction_result_start_time);
			}
				
		}
		else{
			$this->Logs->logs_save($coreclear_user.":".$coreclear_password,$uri,$method,'',$total_time,"0","","",$response,'coreCLEAR');
			return array('success'=>false,'response'=>$response);
		}
	}
	
	
	function get_coreclear_payment_link($invoice_id)
	{

		$this->load->helper('text');
		$encrypted_invoice_id = do_encrypt($invoice_id,$this->Appconfig->get_secure_key());

		$store_url_base = site_url();
		return $store_url_base.'/i/'.$encrypted_invoice_id;
	}
	
	function mark_store_account_orders_as_paid_for_invoice($type,$invoice_id)
	{
		if ($type == 'customer')
		{
			foreach($this->get_details($type,$invoice_id) as $inv_detail)
			{
				if($sale_id_paid = $inv_detail['sale_id'])
				{
					$customer_id = $this->get_info($type,$invoice_id)->person_id;
					$cust_info = $this->Customer->get_info($customer_id);
				
					$new_balance = $cust_info->balance - $inv_detail['total'];
				
		 			$store_account_transaction = array(
			   		'customer_id'=>$customer_id,
			   		'sale_id'=>$inv_detail['sale_id'],
					 'comment'=>"INVOICE #$invoice_id PAYMENT",
			      	 'transaction_amount'=>$inv_detail['total']*-1,
					 'balance'=>$new_balance,
					 'date' => date('Y-m-d H:i:s')
					);
				
					$this->db->insert('store_accounts',$store_account_transaction);
					$this->db->insert('store_accounts_paid_sales',array('sale_id' => $sale_id_paid,'store_account_payment_sale_id' => NULL));
					
					$this->db->where('person_id', $customer_id);
					$this->db->update('customers', array('balance' => $new_balance));	
				}
			}
		}
		else
		{
			foreach($this->get_details($type,$invoice_id) as $inv_detail)
			{
				if($receiving_id_paid = $inv_detail['receiving_id'])
				{
					$supplier_id = $this->get_info($type,$invoice_id)->person_id;
					$sulp_info = $this->Supplier->get_info($supplier_id);
				
					$new_balance = $sulp_info->balance - $inv_detail['total'];
				
		 			$store_account_transaction = array(
			   		'supplier_id'=>$supplier_id,
			   		'receiving_id'=>$inv_detail['receiving_id'],
					 'comment'=>"INVOICE #$invoice_id PAYMENT",
			      	 'transaction_amount'=>$inv_detail['total']*-1,
					 'balance'=>$new_balance,
					 'date' => date('Y-m-d H:i:s')
					);
				
					$this->db->insert('supplier_store_accounts',$store_account_transaction);
					$this->db->insert('supplier_store_accounts_paid_receivings',array('receiving_id' => $receiving_id_paid,'store_account_payment_receiving_id' => NULL));
					
					$this->db->where('person_id', $supplier_id);
					$this->db->update('suppliers', array('balance' => $new_balance));	
					
				}
			}
		}
	}
	
	function get_balance_past_due($type,$days_past_due)
	{
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		
		$this->db->select('SUM(balance) as balance_due', false);
		$this->db->from($type.'_'.'invoices');		
		$this->db->where($type.'_'.'invoices.deleted', 0);
		$this->db->where($type.'_'.'invoices.location_id', $location_id);
		if($days_past_due == 'current'){
			$this->db->where('datediff(NOW(),phppos_'.$type.'_'.'invoices.due_date) < 30', NULL, FALSE);
		}
		else{
			$this->db->where('datediff(NOW(),phppos_'.$type.'_'.'invoices.due_date) > '.$days_past_due, NULL, FALSE);
		}
		$this->db->where($type.'_invoices.balance > 0');
		return $this->db->get()->row()->balance_due;
	}

	function get_payment_receipt($id)
	{
		$this->db->from('customer_invoice_payments');
		$this->db->where('payment_id', $id);
		
		return $this->db->get()->row();
	}

	function get_invoice_detail($id)
	{
		$this->db->from('customer_invoices');
		$this->db->where('invoice_id', $id);
		
		return $this->db->get()->row();
	}

	function get_zatca_invoice_by_id($id){
		$this->db->from('zatca_invoices');
		$this->db->where('invoice_id', $id);
		
		return $this->db->get()->row_array();
	}

	function get_zatca_invoice_by_sale_id($id){
		$this->db->from('zatca_invoices');
		$this->db->where('sale_id', $id);
		
		return $this->db->get()->row_array();
	}

	function get_zatca_invoice_by_range($start_date, $end_date, $location_id){
		$this->db->from('zatca_invoices');
		$this->db->where('location_id', $location_id);
		$this->db->where('issue_date >=', $start_date);
		$this->db->where('issue_date <=', $end_date);
		$this->db->order_by('issue_date ASC, invoice_id ASC');
		
		return $this->db->get()->result_array();
	}

	function exist_zatca($sale_id){
		$this->db->from('zatca_invoices');
		$this->db->where('sale_id', $sale_id);
		
		$query = $this->db->get();
		return ($query->num_rows()==1);
	}

	function save_zatca_invoice($invoice_data){
		$is_exist = $this->exist_zatca($invoice_data['sale_id']);

		if($is_exist){
			$this->db->where('sale_id',$invoice_data['sale_id']);
			$ret_update = $this->db->update('zatca_invoices',$invoice_data);
			return $ret_update;
		}else{
			if($this->db->insert('zatca_invoices',$invoice_data))
			{
				$invoice_data['invoice_id']=$this->db->insert_id();
				return true;
			}
			return false;
		}
	}

	function remove_zatca_invoices(){

        $tableName = 'zatca_invoices';
        // Delete all rows from the table
		$this->db->from($tableName);
		$ret = $this->db->truncate();
	
		if($ret){
			return 1;
		}
		return 0;
	}
	
	function get_zatca_invoice_pih($location_id){

		$this->db->from('zatca_invoices');
		$this->db->where('location_id', $location_id);
		$this->db->where('reported >', 0);
		$this->db->order_by('issue_date DESC, invoice_id DESC');

		$query = $this->db->get();
		if($query->num_rows() > 0){
			$ret = $query->row();
			return $ret->hash;
		}
		//todo set zatca constant
		return "NWZlY2ViNjZmZmM4NmYzOGQ5NTI3ODZjNmQ2OTZjNzljMmRiYzIzOWRkNGU5MWI0NjcyOWQ3M2EyN2ZiNTdlOQ==";
	}

	function get_zatca_last_report_sale_id($location_id) {

		$this->db->from('zatca_invoices');
		$this->db->where('location_id', $location_id);
		$this->db->where('reported >', 0);
		$this->db->order_by('issue_date DESC, invoice_id DESC');

		$query = $this->db->get();
		if($query->num_rows() > 0){
			$ret = $query->row();
			return $ret->sale_id;
		}
		//todo set zatca constant
		return NULL;
	}

	function get_zatca_invoice_unreported_sales($location_id = NULL, $from_date = NULL){
		$this->db->select('sales.sale_id as sale_id, sales.location_id as location_id');
		$this->db->from('sales');
		$this->db->join('zatca_invoices', 'sales.sale_id = zatca_invoices.sale_id', 'left');

		if($location_id != NULL){
			$this->db->where('sales.location_id', $location_id);
		}
		if($from_date != NULL){
			$this->db->where('sales.sale_time >=', $from_date);
		}
		$this->db->where('sales.deleted',0);
		$this->db->group_start()
			->where('zatca_invoices.reported <', 1)
			->or_where($this->db->dbprefix('zatca_invoices').'.reported is NULL', NULL, FALSE)
		->group_end();

		$this->db->order_by('sales.sale_time ASC');

		$this->db->save_queries = TRUE;
		$ret =  $this->db->get()->result_array();
		$str = $this->db->last_query();	
		return $ret;
	}

	function get_zatca_invoice_search_suggestions($search, $deleted = 0, $limit = 10){
		if (!trim($search))
		{
			return array();
		}
		
		if (!$deleted)
		{
			$deleted = 0;
		}

		$current_location = $this->Employee->get_logged_in_employee_current_location_id();
		$this->db->from('zatca_invoices');
		$this->db->where(" location_id = '".$current_location."' AND reported = 1 AND CAST(sale_id AS CHAR) LIKE '%".$search."%'");
		$this->db->limit($limit);	
		$search_list = $this->db->get();

		$suggestions = array();
		$temp_suggestions = array();

		foreach($search_list->result() as $index=> $row){
			$invoice_data = json_decode($row->invoice_data, TRUE);
			$data = array(
				'name' => $invoice_data['id'],
				'key' => $row->sale_id,
				'avatar' => base_url()."assets/img/cash-register.png",
				'date' => $row->issue_date
			);
			$temp_suggestions[ $index ] = $data;
		}
		uasort($temp_suggestions, 'sort_assoc_array_by_name');

		foreach($temp_suggestions as $key => $value)
		{
			$suggestions[]=array('value'=> $value['key'], 'label' => $value['name'], 'avatar'=>$value['avatar'], 'subtitle'=>$value['date']);
		}
		return $suggestions;
	}
}
