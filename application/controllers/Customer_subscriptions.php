<?php
use BlockChyp\BlockChyp;

require_once ("Secure_area.php");
require_once ("interfaces/Idata_controller.php");
require_once (APPPATH."traits/creditcardProcessingTrait.php");
require_once (APPPATH."traits/subscriptionProcessingTrait.php");

class Customer_subscriptions extends Secure_area implements Idata_controller {

	use creditcardProcessingTrait;
	use subscriptionProcessingTrait;

    function __construct() {

        parent::__construct('customers');
  		$this->lang->load('module');		
  		$this->lang->load('customers');		
  		$this->lang->load('items');		
  		$this->load->model('Customer_subscription');	
		$this->load->helper('report');			  
    }

    function index($offset = 0) {
        $params = $this->session->userdata('subscriptions_search_data') ? $this->session->userdata('subscriptions_search_data') : array('offset' => 0, 'order_col' => 'id', 'order_dir' => 'desc', 'search' => FALSE,'deleted' => 0);

        if ($offset != $params['offset']) {
            redirect('subscriptions/index/' . $params['offset']);
        }

        $this->check_action_permission('search');
        $config['base_url'] = site_url('customer_subscriptions/sorting');
        $config['total_rows'] = $this->Customer_subscription->count_all();
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
        $data['controller_name'] = strtolower(get_class());
        $data['per_page'] = $config['per_page'];
				$data['deleted'] = $params['deleted'];
        $data['search'] = $params['search'] ? $params['search'] : "";
        if ($data['search']) {
            $config['total_rows'] = $this->Customer_subscription->search_count_all($data['search'],$params['deleted']);
            $table_data = $this->Customer_subscription->search($data['search'],$params['deleted'], $data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        } else {
            $config['total_rows'] = $this->Customer_subscription->count_all($params['deleted']);
            $table_data = $this->Customer_subscription->get_all($params['deleted'],$data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        }
        $this->load->library('pagination');$this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['order_col'] = $params['order_col'];
        $data['order_dir'] = $params['order_dir'];
        $data['total_rows'] = $config['total_rows'];
        $data['manage_table'] = get_subscriptions_manage_table($table_data, $this);
        $this->load->view('subscriptions/manage', $data);
    }

    function sorting() {
        $this->check_action_permission('search');
				$params = $this->session->userdata('subscriptions_search_data');

        $search = $this->input->post('search') ? $this->input->post('search') : "";
        $per_page = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;

        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'id';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';
				$deleted = $this->input->post('deleted') ? $this->input->post('deleted') : $params['deleted'];

        $subscriptions_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search,'deleted' => $deleted);
        $this->session->set_userdata("subscriptions_search_data", $subscriptions_search_data);

        if ($search) {
            $config['total_rows'] = $this->Customer_subscription->search_count_all($search,$deleted);
            $table_data = $this->Customer_subscription->search($search, $deleted,$per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        } else {
            $config['total_rows'] = $this->Customer_subscription->count_all($deleted);
            $table_data = $this->Customer_subscription->get_all($deleted,$per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        }
        $config['base_url'] = site_url('customer_subscriptions/sorting');
        $config['per_page'] = $per_page;
        $this->load->library('pagination');$this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_subscriptions_manage_table_data_rows($table_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));
    }

    function search() {
			
				$params = $this->session->userdata('subscriptions_search_data');
        $this->check_action_permission('search');

        $search = $this->input->post('search');
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'id';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';
				$deleted = isset($params['deleted']) ? $params['deleted'] : 0;

        $subscriptions_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search,'deleted' => $deleted);
        $this->session->set_userdata("subscriptions_search_data", $subscriptions_search_data);
        $per_page = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
        $search_data = $this->Customer_subscription->search($search, $deleted,$per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        $config['base_url'] = site_url('subscriptions/search');
        $config['total_rows'] = $this->Customer_subscription->search_count_all($search,$deleted);
        $config['per_page'] = $per_page;
        $this->load->library('pagination');$this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_subscriptions_manage_table_data_rows($search_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));
    }

    function clear_state() {
			$params = $this->session->userdata('subscriptions_search_data');
			$this->session->set_userdata('subscriptions_search_data',array('offset' => 0, 'order_col' => 'id', 'order_dir' => 'desc', 'search' => FALSE,'deleted' => $params['deleted']));
			redirect('customer_subscriptions');
    }

    /*
      Gives search suggestions based on what is being searched for
     */

    function suggest() {
			//allow parallel searchs to improve performance.
			session_write_close();
			$params = $this->session->userdata('subscriptions_search_data') ? $this->session->userdata('subscriptions_search_data') : array('deleted' => 0);
			$suggestions = $this->Customer_subscription->get_search_suggestions($this->input->get('term'),$params['deleted'], 100);
			echo json_encode(H($suggestions));
    }

    function view($subscription_id = -1, $redirect_code = 0) {
        $this->check_action_permission('add_update');
        $logged_employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
        $data['subscription_info'] = $this->Customer_subscription->get_info($subscription_id);
        $data['all_modules'] = $this->Module->get_all_modules();
        $data['controller_name'] = strtolower(get_class());

        $data['redirect_code'] = $redirect_code;
			
        $this->load->view("subscriptions/form", $data);
    }

    function save($id = -1) 
    {
        $this->check_action_permission('add_update');		 
		  
		$customer_subscription_data = 
		array(
			'recurring_charge_amount' => $this->input->post('recurring_charge_amount'),
			'interval' => $this->input->post('interval') ? $this->input->post('interval') : NULL,
			'weekday' => $this->input->post('weekday') !== NULL ? $this->input->post('weekday') : NULL,
			'day_number' => $this->input->post('day_number') !== NULL ? $this->input->post('day_number') : NULL,
			'month' => $this->input->post('month') !== NULL ? $this->input->post('month') : NULL,
			'day' => $this->input->post('day') !== NULL ? $this->input->post('day') : NULL,
		);
		
		
		if ($this->input->post('cc_number'))
		{
			$cc_number = $this->input->post('cc_number');			
			list($cc_month,$cc_year) = explode('/',$this->input->post('cc_exp_date'));
			
			$credit_card_processor = $this->_get_cc_processor();
			
			$token_result = $credit_card_processor->enroll_pan($cc_number,$cc_month,$cc_year,$this->input->post('cvv'));
			
			if ($token_result)
			{
				$customer_subscription_data['card_on_file_token'] = $token_result['token'];
				$customer_subscription_data['card_on_file_masked'] = $token_result['maskedPan'];
			}	
			
		}
		
        if ($this->Customer_subscription->save($customer_subscription_data, $id)) 
        {
			$customer_subscription_data['next_payment_date'] = $this->Customer_subscription->get_next_payment_date($id);
			$this->Customer_subscription->save($customer_subscription_data,$id);
			
            if($id==-1)
            {
                $subscription_info = $this->Customer_subscription->get_info($subscription_data['id']);
            }
            else
            {
                $subscription_info = $this->Customer_subscription->get_info($id);
            }
			
            $redirect = $this->input->post('redirect');
			
            $success_message = H(lang('common_items_successful_updating'));
            $this->session->set_flashdata('manage_success_message', $success_message);
            echo json_encode(array('success' => true, 'message' => $success_message, 'id' => $id, 'redirect' => $redirect));
        } 
        else 
        {//failure
            echo json_encode(array('success' => false, 'message' => lang('subscriptions_error_adding_updating')));
        }
    }

    function delete() {
        $this->check_action_permission('delete');
        $subscriptions_to_delete = $this->input->post('ids');
        if ($this->Customer_subscription->delete_list($subscriptions_to_delete)) {

            echo json_encode(array('success' => true, 'message' => lang('subscriptions_successful_deleted') . ' ' . lang('subscriptions_one_or_multiple')));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('subscriptions_cannot_be_deleted')));
        }
    }
    function undelete() {
        $this->check_action_permission('delete');
        $subscriptions_to_delete = $this->input->post('ids');
        if ($this->Customer_subscription->undelete_list($subscriptions_to_delete)) {

            echo json_encode(array('success' => true, 'message' => lang('subscriptions_successful_undeleted') . ' ' . lang('subscriptions_one_or_multiple')));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('subscriptions_cannot_be_undeleted')));
        }
    }
		
		
	function toggle_show_deleted($deleted=0)
	{
		$this->check_action_permission('search');
	
		$params = $this->session->userdata('subscriptions_search_data') ? $this->session->userdata('subscriptions_search_data') : array('offset' => 0, 'order_col' => 'id', 'order_dir' => 'desc', 'search' => FALSE,'deleted' => 0);
		$params['deleted'] = $deleted;
		$params['offset'] = 0;
		
		$this->session->set_userdata("subscriptions_search_data",$params);
    }
	
	function process_recurring_payments() 
	{
		session_write_close();
		$subs_to_process = $this->Customer_subscription->get_subs_to_process();
			
		foreach($subs_to_process as $sub)
		{
			$this->process_sub($sub);
		}
	}
        
}

?>
