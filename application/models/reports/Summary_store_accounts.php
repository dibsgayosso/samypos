<?php
require_once ("Report.php");
class Summary_store_accounts extends Report
{
        function __construct()
        {
                parent::__construct();
        }

        public function getDataColumns()
        {
                return array(
                        array('data'=>lang('common_location'), 'align'=> 'left'),
                        array('data'=>lang('reports_customer'), 'align'=> 'left'),
                        array('data'=>lang('common_credit_limit'), 'align'=> 'right'),
                        array('data'=>lang('common_balance'), 'align'=> 'right'),
                        array('data'=>lang('common_pay'), 'align'=> 'right'),
                );
        }

        public function getInputData()
        {
                return array(
                'input_params' => array(
                                array('view' => 'date'),
                                array('view' => 'locations'),
                                array('view' => 'checkbox','checkbox_label' => lang('reports_show_accounts_over_credit_limit'),'checkbox_name' => 'show_accounts_over_credit_limit'),
                                array('view' => 'excel_export'),
                                array('view' => 'submit'),
                        ),
                        'input_report_title' => lang('reports_report_input')
                );
        }

        public function getOutputData()
        {
                $this->setupDefaultPagination();

                $tabular_data = array();
                $report_data = $this->getData();
                foreach($report_data as $row)
                {
                        $tabular_data[] = array(
                                array('data'=>$row['location_name'], 'align'=> 'left'),
                                array('data'=>$row['customer'], 'align'=> 'left'),
                                array('data'=>$row['credit_limit'] ? to_currency($row['credit_limit']) : lang('common_not_set'), 'align'=> 'right'),
                                array('data'=>to_currency($row['balance']), 'align'=> 'right'),
                                array('data'=>anchor("customers/pay_now/".$row['person_id'],lang('common_pay'),array('title'=>lang('common_update_customer'),'class'=>'btn btn-info')), 'align'=> 'right'),
                        );
                }

                $data = array(
                        "view" => 'tabular',
                        "title" => lang('reports_store_account_summary_report'),
                        "subtitle" => '',
                        "headers" => $this->getDataColumns(),
                        "data" => $tabular_data,
                        "summary_data" => $this->getSummaryData(),
                        "export_excel" => $this->params['export_excel'],
                        'pagination' => $this->pagination->create_links()
                );

                return $data;
        }

        public function getData()
        {
                $date = $this->params['date'];
                $lookup_balance_in_past = $date != date('Y-m-d');
                $location_ids = self::get_selected_location_ids();

                if (!$lookup_balance_in_past)
                {
                        $this->db->select('CONCAT(first_name, " ",last_name) as customer, balance, credit_limit, customers.person_id, locations.name as location_name', false);
                        $this->db->from('customers');
                        $this->db->join('people', 'customers.person_id = people.person_id');
                        $this->db->join('locations', 'locations.location_id = customers.location_id', 'left');
                        $this->db->where('customers.balance != 0');
                        $this->db->where('customers.deleted',0);
                        $this->db->where_in('customers.location_id', $location_ids);

                        if (isset($this->params['show_accounts_over_credit_limit']) && $this->params['show_accounts_over_credit_limit'])
                        {
                                $this->db->where('balance > credit_limit');
                        }
                }
                else
                {

                        $this->db->select('CONCAT(first_name, " ",last_name) as customer, outersa.balance as balance, credit_limit, customers.person_id, locations.name as location_name', false);
                        $this->db->from('store_accounts as outersa');
                        $this->db->join('customers', 'customers.person_id = outersa.customer_id');
                        $this->db->join('people', 'customers.person_id = people.person_id');
                        $this->db->join('locations', 'locations.location_id = customers.location_id', 'left');
                        $this->db->where("date = (SELECT MAX(date) FROM ".$this->db->dbprefix('store_accounts')." as innersa WHERE innersa.customer_id=outersa.customer_id and date < '$date 23:59:59' )");
                        $this->db->where('outersa.balance != 0');
                        $this->db->where('customers.deleted',0);
                        $this->db->where_in('customers.location_id', $location_ids);

                        if (isset($this->params['show_accounts_over_credit_limit']) && $this->params['show_accounts_over_credit_limit'])
                        {
                                $this->db->where('outersa.balance > credit_limit');
                        }

                }
                //If we are exporting NOT exporting to excel make sure to use offset and limit
                if (isset($this->params['export_excel']) && !$this->params['export_excel'])
                {
                        $this->db->limit($this->report_limit);
                        $this->db->offset(isset($this->params['offset']) ? $this->params['offset'] : 0);
                }

                return $this->db->get()->result_array();
        }


        public function getTotalRows()
        {
                $this->db->select('CONCAT(first_name, " ",last_name) as customer, balance, customers.person_id', false);
                $this->db->from('customers');
                $this->db->join('people', 'customers.person_id = people.person_id');
                $this->db->where('customers.balance != 0');
                $this->db->where('customers.deleted',0);
                $this->db->where_in('customers.location_id', self::get_selected_location_ids());

                if (isset($this->params['show_accounts_over_credit_limit']) && $this->params['show_accounts_over_credit_limit'])
                {
                        $this->db->where('balance > credit_limit');
                }

                return $this->db->count_all_results();
        }

        public function getSummaryData()
        {
                $date = $this->params['date'];

                $lookup_balance_in_past = $date != date('Y-m-d');
                $selected_location_ids = self::get_selected_location_ids();

                $selected_total = $this->get_total_for_locations($lookup_balance_in_past, $selected_location_ids, $date);
                $all_locations_total = $this->get_total_for_locations($lookup_balance_in_past, NULL, $date);

                return array(
                        'total' => $selected_total,
                        'all_locations_total' => $all_locations_total,
                );
        }

        private function get_total_for_locations($lookup_balance_in_past, $location_ids = NULL, $date = '')
        {
                if (!$lookup_balance_in_past)
                {
                        $this->db->select('SUM(balance) as total', false);
                        $this->db->from('customers');
                        $this->db->where('customers.balance != 0');
                        $this->db->where('customers.deleted',0);

                        if ($location_ids)
                        {
                                $this->db->where_in('customers.location_id', $location_ids);
                        }

                        if (isset($this->params['show_accounts_over_credit_limit']) && $this->params['show_accounts_over_credit_limit'])
                        {
                                $this->db->where('balance > credit_limit');
                        }
                }
                else
                {
                        $this->db->select('SUM(outersa.balance) as total', false);
                        $this->db->from('store_accounts as outersa');
                        $this->db->join('customers', 'customers.person_id = outersa.customer_id');
                        $this->db->join('people', 'customers.person_id = people.person_id');

                        if ($location_ids)
                        {
                                $this->db->where_in('customers.location_id', $location_ids);
                        }

                        $this->db->where("date = (SELECT MAX(date) FROM ".$this->db->dbprefix('store_accounts')." as innersa WHERE innersa.customer_id=outersa.customer_id and date < '$date 23:59:59' )");
                        $this->db->where('outersa.balance != 0');
                        $this->db->where('customers.deleted',0);

                        if (isset($this->params['show_accounts_over_credit_limit']) && $this->params['show_accounts_over_credit_limit'])
                        {
                                $this->db->where('outersa.balance > credit_limit');
                        }

                }

                $row = $this->db->get()->row_array();
                return isset($row['total']) ? $row['total'] : 0;
        }
}
?>