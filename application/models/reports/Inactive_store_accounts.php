<?php
require_once ("Report.php");
class Inactive_store_accounts extends Report
{
	function __construct()
	{
		parent::__construct();
	}

	private function get_inactive_days()
	{
		$inactive_days = 30;
		if (isset($this->params['inactive_days']) && is_numeric($this->params['inactive_days']))
		{
			$inactive_days = (int)$this->params['inactive_days'];
		}
		return max(0, $inactive_days);
	}

	private function get_inactive_cutoff($inactive_days)
	{
		return date('Y-m-d H:i:s', strtotime('-'.$inactive_days.' days'));
	}

	public function getDataColumns()
	{
		return array(
			array('data'=>lang('reports_customer'), 'align'=> 'left'),
			array('data'=>lang('common_balance'), 'align'=> 'right'),
		);
	}

	public function getInputData()
	{
		return array(
			'input_params' => array(
				array(
					'view' => 'text',
					'name' => 'inactive_days',
					'label' => lang('reports_inactive_days'),
					'default' => 30,
				),
				array('view' => 'submit'),
			),
			'input_report_title' => lang('reports_report_input'),
		);
	}

	public function getOutputData()
	{
		$this->setupDefaultPagination();
		$inactive_days = $this->get_inactive_days();
		$report_data = $this->getData();
		$tabular_data = array();

		foreach ($report_data as $row)
		{
			$tabular_data[] = array(
				array('data'=>$row['customer'], 'align'=> 'left'),
				array('data'=>to_currency($row['balance']), 'align'=> 'right'),
			);
		}

		$data = array(
			"view" => 'tabular',
			"title" => lang('reports_inactive_store_accounts_report'),
			"subtitle" => lang('reports_inactive_days').': '.$inactive_days,
			"headers" => $this->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $this->getSummaryData(),
			"export_excel" => 0,
			'pagination' => $this->pagination->create_links(),
		);

		return $data;
	}

	public function getData()
	{
		$inactive_days = $this->get_inactive_days();
		$cutoff = $this->get_inactive_cutoff($inactive_days);
		$export_excel = isset($this->params['export_excel']) ? $this->params['export_excel'] : 0;
		$inactive_customer_subquery = 'customers.person_id IN (SELECT customer_id FROM '.$this->db->dbprefix('store_accounts').' GROUP BY customer_id HAVING MAX(`date`) <= '.$this->db->escape($cutoff).')';

		$this->db->select('CONCAT(people.first_name, " ",people.last_name) as customer, customers.balance, customers.person_id', false);
		$this->db->from('customers');
		$this->db->join('people', 'customers.person_id = people.person_id');
		$this->db->where('customers.balance >', 0);
		$this->db->where('customers.deleted', 0);
		$this->db->where($inactive_customer_subquery, null, false);
		$this->db->order_by('customers.balance', 'desc');

		if (!$export_excel)
		{
			$this->db->limit($this->report_limit);
			$this->db->offset(isset($this->params['offset']) ? $this->params['offset'] : 0);
		}

		return $this->db->get()->result_array();
	}

	public function getTotalRows()
	{
		$inactive_days = $this->get_inactive_days();
		$cutoff = $this->get_inactive_cutoff($inactive_days);
		$inactive_customer_subquery = 'customers.person_id IN (SELECT customer_id FROM '.$this->db->dbprefix('store_accounts').' GROUP BY customer_id HAVING MAX(`date`) <= '.$this->db->escape($cutoff).')';

		$this->db->from('customers');
		$this->db->where('customers.balance >', 0);
		$this->db->where('customers.deleted', 0);
		$this->db->where($inactive_customer_subquery, null, false);

		return $this->db->count_all_results();
	}

	public function getSummaryData()
	{
		$inactive_days = $this->get_inactive_days();
		$cutoff = $this->get_inactive_cutoff($inactive_days);
		$inactive_customer_subquery = 'customers.person_id IN (SELECT customer_id FROM '.$this->db->dbprefix('store_accounts').' GROUP BY customer_id HAVING MAX(`date`) <= '.$this->db->escape($cutoff).')';

		$this->db->select('SUM(customers.balance) as total_balance_of_all_store_accounts', false);
		$this->db->from('customers');
		$this->db->where('customers.balance >', 0);
		$this->db->where('customers.deleted', 0);
		$this->db->where($inactive_customer_subquery, null, false);

		return $this->db->get()->row_array();
	}
}
?>
