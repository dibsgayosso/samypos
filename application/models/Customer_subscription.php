<?php

require_once (APPPATH."models/cart/PHPPOSCartSale.php");

class Customer_subscription extends MY_Model {
    /*
      Determines if a given person_id is a customer
     */

    function exists($subscription) {
        $this->db->from('customer_subscriptions');
        $this->db->where('customer_subscriptions.id', $subscription);
        $query = $this->db->get();

        return ($query->num_rows() == 1);
    }

    /*
      Returns all the customer_subscriptions
     */

    function get_all($deleted=0,$limit = 10000, $offset = 0, $col = 'id', $order = 'desc',$location_id_override = NULL) {
			
			if (!$deleted)
			{
				$deleted = 0;
			}
			
		$location_id = $location_id_override ? $location_id_override : $this->Employee->get_logged_in_employee_current_location_id();
		$this->db->select('customer_subscriptions.*, customer.*,items.*');
		$this->db->from('customer_subscriptions');
		$this->db->join('people as customer', 'customer.person_id = customer_subscriptions.customer_id','left');
		$this->db->join('items', 'items.item_id = customer_subscriptions.item_id','left');
		$this->db->where('customer_subscriptions.deleted', $deleted);
		$this->db->where('location_id', $location_id);
		$this->db->order_by($col, $order);
		$this->db->limit($limit);
		$this->db->offset($offset);
      return $this->db->get();
    }

    function count_all($deleted = 0,$location_id_override = NULL) {
			if (!$deleted)
			{
				$deleted = 0;
			}
		 
			$location_id = $location_id_override ? $location_id_override : $this->Employee->get_logged_in_employee_current_location_id();
        $this->db->from('customer_subscriptions');
        $this->db->where('location_id', $location_id);
        $this->db->where('deleted', $deleted);
        return $this->db->count_all_results();
    }

    /*
      Gets information about a particular subscription
     */

    function get_info($subscription_id) {
        $this->db->from('customer_subscriptions');
        $this->db->where('customer_subscriptions.id', $subscription_id);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row();
        }
		
		return NULL; 
    }

    function search_count_all($search, $deleted=0,$limit = 10000,$location_id_override = NULL) {
			if (!$deleted)
			{
				$deleted = 0;
			}
			$location_id = $location_id_override ? $location_id_override : $this->Employee->get_logged_in_employee_current_location_id();
			
			$this->db->select('customer_subscriptions.*, customer.*');
			$this->db->from('customer_subscriptions');
			$this->db->join('people as customer', 'customer.person_id = customer_subscriptions.customer_id','left');
					 
 		if ($search)
 		{
			$this->db->where("(first_name LIKE '".($this->config->item('customer_allow_partial_match') ? '%' : '').$this->db->escape_like_str($search)."%' or 
			last_name LIKE '".($this->config->item('customer_allow_partial_match') ? '%' : '').$this->db->escape_like_str($search)."%' or 
			email LIKE '".($this->config->item('customer_allow_partial_match') ? '%' : '').$this->db->escape_like_str($search)."%' or 
			phone_number LIKE '".($this->config->item('customer_allow_partial_match') ? '%' : '').$this->db->escape_like_str($search)."%' or 
			full_name LIKE '".($this->config->item('customer_allow_partial_match') ? '%' : '').$this->db->escape_like_str($search)."%') and deleted=$deleted");		
		}
		else
		{
			$this->db->where('customer_subscriptions.deleted',$deleted);
		}
 		
		$this->db->where('customer_subscriptions.location_id', $location_id);
	
		$this->db->limit($limit);
      $result = $this->db->get();
      return $result->num_rows();
    }

    /*
      Preform a search on customer_subscriptions
     */

    function search($search, $deleted=0,$limit = 20, $offset = 0, $column = 'id', $orderby = 'asc',$location_id_override = NULL) {
			
		$location_id = $location_id_override ? $location_id_override : $this->Employee->get_logged_in_employee_current_location_id();
		
		if (!$deleted)
		{
			$deleted = 0;
		}
		
		
		$this->db->select('customer_subscriptions.*, customer.*,items.*');
		$this->db->from('customer_subscriptions');
		$this->db->join('people as customer', 'customer.person_id = customer_subscriptions.customer_id','left');
		$this->db->join('items', 'items.item_id = customer_subscriptions.item_id','left');
				 
 		if ($search)
 		{
			$this->db->where("(first_name LIKE '".($this->config->item('customer_allow_partial_match') ? '%' : '').$this->db->escape_like_str($search)."%' or 
			last_name LIKE '".($this->config->item('customer_allow_partial_match') ? '%' : '').$this->db->escape_like_str($search)."%' or 
			email LIKE '".($this->config->item('customer_allow_partial_match') ? '%' : '').$this->db->escape_like_str($search)."%' or 
			phone_number LIKE '".($this->config->item('customer_allow_partial_match') ? '%' : '').$this->db->escape_like_str($search)."%' or 
			full_name LIKE '".($this->config->item('customer_allow_partial_match') ? '%' : '').$this->db->escape_like_str($search)."%') and phppos_customer_subscriptions.deleted=$deleted");		
		}
		else
		{
			$this->db->where('customer_subscriptions.deleted',$deleted);
		}
		
		$this->db->where('customer_subscriptions.location_id', $location_id);
		
       $this->db->order_by($column,$orderby);
	 
       $this->db->limit($limit);
      $this->db->offset($offset);
      return $this->db->get();
			
	  }

    /*
      Gets information about multiple customer_subscriptions
     */

    function get_multiple_info($customer_subscriptions_ids) {
        $this->db->from('customer_subscriptions');
        $this->db->where_in('customer_subscriptions.id', $customer_subscriptions_ids);
        $this->db->order_by("id", "asc");
        return $this->db->get();
    }

    /*
      Inserts or updates a customer_subscriptions
     */


    function save(&$subscription_data, $subscription_id = false) {
        if (!$subscription_id or !$this->exists($subscription_id)) {
            if ($this->db->insert('customer_subscriptions', $subscription_data)) {
                $subscription_data['id'] = $this->db->insert_id();
                return true;
            }
            return false;
        }

        $this->db->where('id', $subscription_id);
        return $this->db->update('customer_subscriptions', $subscription_data);
    }

    /*
      Get search suggestions to find customer_subscriptions
     */

    function get_search_suggestions($search, $deleted=0,$limit = 25) 
	{
		$this->load->model('Customer');
		
		return $this->Customer->get_customer_search_suggestions($search, $deleted,$limit);
    }

    /*
      Deletes one Subscription
     */

    function delete($subscription_id) {
        $this->db->where('id', $subscription_id);
        return $this->db->update('customer_subscriptions', array('deleted' => 1));
    }

    /*
      Deletes a list of expeses
     */

    function delete_list($subscription_ids) {

        $this->db->where_in('id', $subscription_ids);
        return $this->db->update('customer_subscriptions', array('deleted' => 1));
    }
		
    function undelete_list($subscription_ids) {

        $this->db->where_in('id', $subscription_ids);
        return $this->db->update('customer_subscriptions', array('deleted' => 0));
	}
	
	function get_next_payment_date($subscription_id)
	{
		if (!is_numeric($subscription_id))
		{
			$sub_info = $subscription_id;	
		}
		else
		{
			$sub_info = $this->get_info($subscription_id);
		}
		return $this->{'get_next_payment_date_'.$sub_info->interval}($sub_info);
	}
	
	
	private function get_next_payment_date_weekly($sub_info)
	{		
		$weekday = $sub_info->weekday;
		
		$date_map = array(
		'0' => 'Sunday',
		'1' => 'Monday',
		'2' => 'Tuesday',
		'3' => 'Wednesday',
		'4' => 'Thursday',
		'5' => 'Friday',
		'6' => 'Saturday',
		);
		
		return date('Y-m-d H:i:s',strtotime('next '.$date_map[$weekday]));
		
	}
	
	
	private function get_next_payment_date_monthly_on_day_of_month($sub_info)
	{		
		$day_number = $sub_info->day_number;
		@$next_payment_date = strtotime($sub_info->next_payment_date);		
		$this_months_day = strtotime(date('Y-m-'.$day_number));
			
		if (@$sub_info->next_payment_date)
		{
			if ($this_months_day > $next_payment_date)
			{
				return date('Y-m-'.$day_number);
			}
		}
		else
		{
			if ($this_months_day > time())
			{
				return date('Y-m-'.$day_number);
			}
		}
		
		if (@$next_payment_date <= strtotime(date('Y-m-d')))
		{
			$begin = new DateTime(date('Y-m-').$day_number);
		    $end = clone $begin;
		    $end->modify('+1 month');
		    while (($begin->format('m')+1)%12 != $end->format('m')%12) {
		        $end->modify('-1 day');
		    }
			return $end->format('Y-m-d');
					
		}
		
		//no change as we haven't billed this month yet
		return $sub_info->next_payment_date;
		
	}
	
	private function get_next_payment_date_monthly_on_day_of_week($sub_info)
	{		
		$weekday = $sub_info->weekday;
		$day = $sub_info->day;
		
		
		$weekday_map = array(
		'0' => 'Sunday',
		'1' => 'Monday',
		'2' => 'Tuesday',
		'3' => 'Wednesday',
		'4' => 'Thursday',
		'5' => 'Friday',
		'6' => 'Saturday',
		);
		
		$day_map = array(
			'1' => 'first',	
			'2' => 'second',	
			'3' => 'third',	
			'4' => 'fourth',	
			'5' => 'last',	
		);
		$now=date("U");
		$monthyear=date("Y-m");
		$calculated_date=date('U', strtotime($day_map[$day].' '.$weekday_map[$weekday].' of '.$monthyear));
		if ($now>$calculated_date) 
		{
		    $monthyear=date("Y-m", strtotime("next month"));
			$calculated_date=date('U', strtotime($day_map[$day].' '.$weekday_map[$weekday].' of '.$monthyear));
		}

		return date("Y-m-d",$calculated_date);
				
	}
	
	private function get_next_payment_date_yearly_on_date($sub_info)
	{		
		$month = $sub_info->month;
		$day_number = $sub_info->day_number;
		$this_years_day = strtotime(date('Y-'.$month.'-'.$day_number));
		@$next_payment_date = strtotime($sub_info->next_payment_date);		
			
			
		if (@$sub_info->next_payment_date)
		{
			if (@$this_years_day > $next_payment_date)
			{
				return date('Y-'.$month.'-'.$day_number);
			}
		}
		else
		{
			if ($this_years_day > time())
			{
				return date('Y-'.$month.'-'.$day_number);
			}
		}
		
		if (@$next_payment_date <= strtotime(date('Y-m-d')))
		{
			$begin = new DateTime(date('Y-'.$month.'-').$day_number);
		    $end = clone $begin;
		    $end->modify('+1 year');
			return $end->format('Y-m-d');
					
		}
		
		//no change as we haven't billed this month yet
		return $sub_info->next_payment_date;
		
	}
	
	private function get_next_payment_date_yearly_on_month_on_day_of_week($sub_info)
	{		
		$month = $sub_info->month;
		$day = $sub_info->day;
		$weekday = $sub_info->weekday;
		
		
		$weekday_map = array(
		'0' => 'Sunday',
		'1' => 'Monday',
		'2' => 'Tuesday',
		'3' => 'Wednesday',
		'4' => 'Thursday',
		'5' => 'Friday',
		'6' => 'Saturday',
		);
		
		$day_map = array(
			'1' => 'first',	
			'2' => 'second',	
			'3' => 'third',	
			'4' => 'fourth',	
			'5' => 'last',	
		);
		$now=date("U");
		$monthyear=date("Y-".$month);
		$calculated_date=date('U', strtotime($day_map[$day].' '.$weekday_map[$weekday].' of '.$monthyear));
		if ($now>$calculated_date) 
		{
		    $monthyear=date("Y-".$month, strtotime("next year"));
			$calculated_date=date('U', strtotime($day_map[$day].' '.$weekday_map[$weekday].' of '.$monthyear));
		}

		return date("Y-m-d",$calculated_date);
		
	}	
	
	function get_subs_to_process()
	{
		$this->db->from('customer_subscriptions');
		$this->db->group_start();
		$this->db->where('next_payment_date <=', date('Y-m-d'));
		$this->db->or_where('next_retry_date <=',date('Y-m-d'));
		$this->db->group_end();
		$this->db->where_in('status', array('current','failed'));
		$this->db->where('deleted',0);
		
		return $this->db->get()->result_array();
	}
	
	function save_sale_for_charge($sub,$response)
	{
		$CI =& get_instance();			
		
		$CI->cart = new PHPPOSCartSale();
		$CI->cart->location_id = $sub['location_id'];
		$CI->cart->customer_subscription_id = $sub['id'];
		$item = new PHPPOSCartItemSale(array('unit_price' =>$sub['recurring_charge_amount'],'cart' => $CI->cart,'scan' => $sub['item_id'].($sub['variation_id'] ? '#'.$sub['variation_id'] : '').'|FORCE_ITEM_ID|','quantity' => 1,'is_recurring' => FALSE));				
		$CI->cart->add_item($item);
		$CI->cart->customer_id = $sub['customer_id'];
		$CI->cart->employee_id = 1;
		$CI->cart->location_id = $sub['location_id'];
		
		$AcctNo = $response['maskedPan'];
		$TranCode = lang('sales_card_transaction');
		$AuthCode = $response['authCode'];
		$RefNo = $response['transactionId'];
		$RecordNo = $sub['card_on_file_token'];
		@$CardType = $response['paymentType'];
		@$EntryMethod = $response['entryMethod'];
		@$ApplicationLabel = $response['receiptSuggestions']['applicationLabel'];

		@$AID = $response['receiptSuggestions']['aid'];
		@$TVR = $response['receiptSuggestions']['tvr'];
		@$IAD = $response['receiptSuggestions']['iad'];
		@$TSI = $response['receiptSuggestions']['tsi'];
	
		$CI->cart->add_payment(new PHPPOSCartPaymentSale(array(
			'payment_type' => lang('common_credit'),
			'payment_amount' => $response['authorizedAmount'],
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
		$this->Sale->save($CI->cart, false);
		
	}
	
	function get_subscription_amount_with_tax($sub)
	{
		$CI =& get_instance();			
		
		$CI->cart = new PHPPOSCartSale();
		$CI->cart->location_id = $sub['location_id'];
		$item = new PHPPOSCartItemSale(array('unit_price' =>$sub['recurring_charge_amount'],'cart' => $CI->cart,'scan' => $sub['item_id'].($sub['variation_id'] ? '#'.$sub['variation_id'] : '').'|FORCE_ITEM_ID|','quantity' => 1,'is_recurring' => FALSE));				
		$CI->cart->add_item($item);
		
		return $CI->cart->get_total();
	}
}
?>
