<?php
use BlockChyp\BlockChyp;

class Stat extends CI_Model
{
	function get_credit_card_stats($days = 30,$location_id = NULL)
	{
		$start_date = date('Y-m-d',strtotime('-'.$days.' days'));
		$end_date = date('Y-m-d');
		
		if ($location_id === NULL)
		{
			$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		}
		
    	BlockChyp::setApiKey($this->Location->get_info_for_key('blockchyp_api_key',$location_id));
    	BlockChyp::setBearerToken($this->Location->get_info_for_key('blockchyp_bearer_token', $location_id));
    	BlockChyp::setSigningKey($this->Location->get_info_for_key('blockchyp_signing_key',$location_id));
	
	
		$params = [
			'startDate' => date('c',strtotime(date('Y-m-d',strtotime('-'.$days.' days')))),
			'endDate' => date('c'),
		    'maxResults' => 50,
			'startIndex' => 0,
		];
		
		$sheet_report_data = array();
		$all_batches = array();
		$batches = BlockChyp::batchHistory($params);
		$all_batches = array_merge($all_batches,$batches['batches']);
		$total_batches = $batches['totalResultCount'];

		$total_pages = ceil($total_batches/$params['maxResults']);
		for($startIndex=$params['maxResults'];$startIndex<$params['maxResults']*$total_pages;$startIndex+=$params['maxResults'])
		{
			$params['startIndex'] = $startIndex;
			$batches = BlockChyp::batchHistory($params);
			$all_batches = array_merge($all_batches,$batches['batches']);

		}
		
		
		$return = array();
		
		foreach(array_reverse($all_batches) as $batch)
		{
			$return[] = array('date' => date("Y-m-d",strtotime($batch['closeDate'])),'sales_total' => make_currency_no_money($batch['capturedAmount']));			
		}
					
		return $return;
	}
	
	private function get_total_number_of_credit_card_sales_per_day($start_date,$end_date,$location_id)
	{
		$this->db->select('date(payment_date) as payment_date, count(*) as count', false);
		$this->db->from('sales_payments');
		$this->db->join('sales', 'sales.sale_id=sales_payments.sale_id');
		$this->db->where('payment_date BETWEEN '.$this->db->escape($start_date). ' and '. $this->db->escape($end_date).' and location_id = '.$location_id);
		
		if ($this->config->item('hide_store_account_payments_in_reports'))
		{
			$this->db->where('store_account_payment',0);
		}
				
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);
		$this->db->group_start();
		$this->db->where('sales_payments.payment_type','Credit Card');
		$this->db->or_where('sales_payments.payment_type','Partial Credit Card');
		$this->db->group_end();
		$this->db->group_by('date(payment_date)');
		
		$return = array();
		
		foreach($this->db->get()->result_array() as $row)
		{
			$return[$row['payment_date']] = $row['count'];
		}
		
		return $return;
	}
	
	
	private function get_sale_ids_for_payments($start_date,$end_date,$location_id)
	{
		$sale_ids = array();
		
		$this->db->select('sales_payments.sale_id');
		$this->db->distinct();
		$this->db->from('sales_payments');
		$this->db->join('sales', 'sales.sale_id=sales_payments.sale_id');
		$this->db->where('payment_date BETWEEN '. $this->db->escape($start_date). ' and '. $this->db->escape($end_date).' and location_id ='.$location_id);
		
		foreach($this->db->get()->result_array() as $sale_row)
		{
			 $sale_ids[] = $sale_row['sale_id'];
		}
		
		return $sale_ids;
	}
	
}