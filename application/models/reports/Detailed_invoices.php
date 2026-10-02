<?php
require_once ("Report.php");
class Detailed_invoices extends Report
{
	function __construct()
	{
		parent::__construct();
		$this->lang->load('invoices');
	}
	
	public function getDataColumns()
	{
		$type = $this->settings['type'];		
		
		return array(
			array('data'=>lang('invoices_invoice'), 'align'=>'left'),
			array('data'=>lang('invoices_po_'.$type), 'align'=> 'left'), 
			array('data'=>lang('invoices_'.$type), 'align'=> 'left'), 
			array('data'=>lang('invoices_terms'), 'align'=> 'left'), 
			array('data'=>lang('invoices_invoice_date'), 'align'=> 'left'), 
			array('data'=>lang('invoices_due_date'), 'align'=> 'left'), 
			array('data'=>lang('common_total'), 'align'=> 'left'), 
			array('data'=>lang('common_balance'), 'align'=> 'left'), 
			array('data'=>lang('invoices_last_paid'), 'align'=> 'left')); 
	}
	
	public function getInputData()
	{
		$type = $this->settings['type'];		
		
		$input_params = array();

		if ($this->settings['display'] == 'tabular')
		{
			$input_data = Report::get_common_report_input_data(FALSE);
			
			$specific_entity_data = array();
			$specific_entity_data['specific_input_name'] = 'person_id';
			
			if ($type == 'customer')
			{
				$specific_entity_data['specific_input_label'] = lang('reports_customer');
				$specific_entity_data['search_suggestion_url'] = site_url('reports/customer_search/1');
			}
			else
			{
				$specific_entity_data['specific_input_label'] = lang('reports_supplier');
				$specific_entity_data['search_suggestion_url'] = site_url('reports/supplier_search/1');
			}
			$specific_entity_data['view'] = 'specific_entity';
			
			$input_params = array(
				array('view' => 'date_range', 'with_time' => FALSE),
				$specific_entity_data,
				array('view' => 'checkbox','checkbox_label' => lang('reports_show_invoices_with_balance'),'checkbox_name'=>'show_invoices_with_balance'),
				array('view' => 'excel_export'),
				array('view' => 'locations'),
				array('view' => 'submit'),
			);
		}
		
		$input_data['input_report_title'] = lang('reports_report_options');
		$input_data['input_params'] = $input_params;
		return $input_data;
	}
	
	
	function getOutputData()
	{
		$this->setupDefaultPagination();
		
		$type = $this->settings['type'];		
		
		$tabular_data = array();
		$report_data = $this->getData();
		foreach($report_data as $row)
		{			
			$tabular_data[] = array(
				array('data'=>$row['invoice_id'], 'align'=> 'left'), 
				array('data'=>$row[$type.'_po'], 'align'=> 'left'),
				array('data'=>$row['person'], 'align'=> 'left'),
				array('data'=>$row['terms'], 'align'=> 'left'),
				array('data'=>date_as_display_date($row['invoice_date']), 'align'=> 'left'),
				array('data'=>date_as_display_date($row['due_date']), 'align'=> 'left'),
				array('data'=>to_currency($row['total']), 'align'=> 'left'),
				array('data'=>to_currency($row['balance']), 'align'=> 'left'),
				array('data'=>date_as_display_date($row['last_paid']), 'align'=> 'left'),
			);
		}

		$data = array(
			"view" => 'tabular',
			"title" => lang('reports_'.$type.'_invoices'),
			"subtitle" => date(get_date_format(), strtotime($this->params['start_date'])) .'-'.date(get_date_format(), strtotime($this->params['end_date'])),
			"headers" => $this->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $this->getSummaryData(),
			"export_excel" => $this->params['export_excel'],
			"pagination" => $this->pagination->create_links(),
		);
		
		return $data;
	}
	
	public function getData()
	{
		$type = $this->settings['type'];
		$this->db->select('terms.name as terms,'.$type.'_'.'invoices.*,'.($type == 'customer' ? 'CONCAT(person.first_name, " ", person.last_name)' : 'company_name').' as person, person.last_name as person_last_name', false);
		$this->dataQuery();
		$this->db->order_by('invoice_date', 'desc');
		
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			if (isset($this->params['offset']))
			{
				$this->db->offset($this->params['offset']);
			}
		}
		
		return $this->db->get()->result_array();
	}
	
	public function getSummaryData()
	{		
		$type = $this->settings['type'];
		$this->db->select('SUM(phppos_'.$type.'_'.'invoices.total) as total,SUM(phppos_'.$type.'_'.'invoices.balance) as balance', false);
		$this->dataQuery();
		return $this->db->get()->row_array();
	}
	
	function getTotalRows()
	{
		$type = $this->settings['type'];
		$this->db->select('terms.name as terms,'.$type.'_'.'invoices.*,'.($type == 'customer' ? 'CONCAT(person.first_name, " ", person.last_name)' : 'company_name').' as person, person.last_name as person_last_name', false);
		$this->dataQuery();
		return $this->db->count_all_results();
	}
	
	function dataQuery()
	{
		$location_ids = self::get_selected_location_ids();
		
		$type = $this->settings['type'];
		
		$this->db->from($type.'_'.'invoices');
		$this->db->join($type.'s', $type.'s.person_id = '.$type.'_'.'invoices.'.$type.'_id','left');
		$this->db->join('people as person', 'person.person_id = '.$type.'_'.'invoices.'.$type.'_id','left');
		$this->db->join('terms', 'terms.term_id = '.$type.'_'.'invoices.term_id','left');
		$this->db->where($type.'_'.'invoices.deleted', 0);
		$this->db->where_in($type.'_'.'invoices.location_id',$location_ids);
		if (isset($this->params['show_invoices_with_balance']) && $this->params['show_invoices_with_balance'])
		{
			$this->db->where($type.'_invoices.balance > 0');
		}
		
		if (isset($this->params['person_id']) && $this->params['person_id'])
		{
			$this->db->where('person.person_id',$this->params['person_id']);
		}
		
		$this->db->where('invoice_date BETWEEN '.$this->db->escape($this->params['start_date']).' and '.$this->db->escape($this->params['end_date']));
		
		
	}
	
}
?>