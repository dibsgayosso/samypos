<?php
require_once ("Secure_area.php");
require_once ("interfaces/Idata_controller.php");
require_once (APPPATH."models/cart/PHPPOSCartSale.php");
require_once (APPPATH."models/cart/PHPPOSCartRecv.php");
require_once (APPPATH."libraries/Fatoora.php");

class Invoices extends Secure_area
{
	function __construct()
	{
		parent::__construct('invoices');	
		$this->lang->load('module');	
		$this->lang->load('items');	
		$this->lang->load('invoices');
		$this->lang->load('sales');
		$this->load->model('Invoice');	
		$this->load->helper('items');
		$this->invoice_type = 'customer';
	}
	
	function sorting($type='customer')
	{
		$this->invoice_type = $type;
		
		$this->lang->load('invoices');
		
		$this->check_action_permission('search');
		$params 	= $this->session->userdata($this->invoice_type.'_invoices_search_data') ? $this->session->userdata($this->invoice_type.'_invoices_search_data') : array('order_col' => 'invoice_id', 'order_dir' => 'desc','deleted' => 0,'days_past_due' => NULL);
		$search 	= $this->input->post('search') ? $this->input->post('search') : "";
		$status 	= $this->input->post('status') ? $this->input->post('status') : "";
		$days_past_due = $this->input->post('days_past_due') ? $this->input->post('days_past_due') : $params['days_past_due'];
		$deleted 	= $this->input->post('deleted') ? $this->input->post('deleted') : $params['deleted'];
		
		$per_page 	= $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$offset 	= $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col 	= $this->input->post('order_col') ? $this->input->post('order_col') : $params['order_col'];
		$order_dir 	= $this->input->post('order_dir') ? $this->input->post('order_dir'): $params['order_dir'];
		
		$item_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search,'deleted' => $deleted, 'status' => $status);
		
		$this->session->set_userdata($this->invoice_type.'_invoices_search_data',$item_search_data);
		
		if ($search)
		{
			$config['total_rows'] = $this->Invoice->search_count_all($this->invoice_type,$search,$days_past_due, $deleted, $status);
			$table_data = $this->Invoice->search($this->invoice_type,$search, $days_past_due, $deleted,$per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $order_col, $order_dir, $status);
		}
		else
		{
			$config['total_rows'] = $this->Invoice->count_all($this->invoice_type,$days_past_due,$deleted, $status);
			$table_data = $this->Invoice->get_all($this->invoice_type,$days_past_due,$deleted,$per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $order_col,$order_dir, $status);
		}
		
		$config['base_url'] = site_url('invoices/sorting');
		$config['per_page'] = $per_page; 
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['invoice_type'] = $this->invoice_type;
		
		$data['manage_table'] = get_invoices_manage_table_data_rows($table_data, $this);
		
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'], 'total_rows' => $config['total_rows']));
	}	
	

	function index($type='customer',$offset=0)
	{
		
		$this->invoice_type = $type;
		


		$this->check_action_permission('search');
		$this->check_action_permission('search');
		
		$this->lang->load('invoices');
		
		$params = $this->session->userdata($this->invoice_type.'_invoices_search_data') ? $this->session->userdata($this->invoice_type.'_invoices_search_data') : array('offset' => 0, 'order_col' => 'invoice_id', 'order_dir' => 'desc', 'search' => FALSE,'deleted' => 0,'days_past_due' => NULL, 'status' => FALSE);
		if ($offset != $params['offset'])
		{
		   redirect('invoices/index/'.$this->invoice_type.'/'.$params['offset']);
		}
		
		$config['base_url'] = site_url('invoices/sorting/'.$this->invoice_type);
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 5;
		
		$data['controller_name']=strtolower(get_class());
		$data['per_page'] = $config['per_page'];
		
		$data['search'] 		= isset($params['search']) && $params['search'] ? $params['search'] : "";
		$data['status'] 		= isset($params['status']) && $params['status'] ? $params['status'] : "";
		$data['days_past_due'] 	= isset($params['days_past_due']) && $params['days_past_due'] ? $params['days_past_due'] : NULL;
		
		$data['deleted'] = $params['deleted'];
		$data['invoice_type'] = $this->invoice_type;
		if ($data['search'])
		{
			$config['total_rows'] = $this->Invoice->search_count_all($this->invoice_type,$data['search'],$data['days_past_due'], $params['deleted'],$params['status']);
			$table_data = $this->Invoice->search($this->invoice_type,$data['search'],$data['days_past_due'],$params['deleted'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir'],$params['status']);
		}
		else
		{	
			$config['total_rows'] = $this->Invoice->count_all($this->invoice_type,$data['days_past_due'],$params['deleted'],$params['status']);
			$table_data = $this->Invoice->get_all($this->invoice_type,$data['days_past_due'],$params['deleted'],$data['per_page'], $params['offset'],$params['order_col'],$params['order_dir'],$params['status']);
		}
				
		$data['total_rows'] = $config['total_rows'];
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		
		$data['pagination'] = $this->pagination->create_links();
		$data['order_col'] = $params['order_col'];
		$data['order_dir'] = $params['order_dir'];
		
		
		$data['default_columns'] 	= $this->Invoice->get_default_columns($this->invoice_type);
		$data['selected_columns'] 	= $this->Employee->get_invoice_columns_to_display($this->invoice_type);
		$data['all_columns'] 		= array_merge($data['selected_columns'],$this->Invoice->get_displayable_columns($this->invoice_type));
		
	
		$data['manage_table']=get_invoices_manage_table($table_data,$this);

		$invoice_status = array(
			'0' => lang('common_please_select'),
			'1' => lang('common_all'),
			'2' => lang('common_unpaid'),
			'3' => lang('common_paid'),
		);
		$data['invoice_status'] = $invoice_status;

		$this->load->view('invoices/manage',$data);
	}
		
	function suggest($type='customer')
	{
		$this->invoice_type = $type;
		
		$this->check_action_permission('search');
		//allow parallel searchs to improve performance.
		session_write_close();
		$params = $this->session->userdata($this->invoice_type.'_invoices_search_data') ? $this->session->userdata($this->invoice_type.'_invoices_search_data') : array('deleted' => 0);
		$suggestions = $this->Invoice->get_search_suggestions($this->invoice_type,$this->input->get('term'),$params['deleted'],100);
		echo json_encode($suggestions);
	}	

	/*
	Gives search suggestions based on what is being searched for
	*/
	function search($type='customer')
	{
		$this->invoice_type = $type;
		
		$this->check_action_permission('search');
		$params 		= 	$this->session->userdata($this->invoice_type.'_invoices_search_data');
		$search 		=	$this->input->post('search') ? $this->input->post('search') : "";
		$status 		=	$this->input->post('status') ? $this->input->post('status') : "";
		$days_past_due 	= 	$this->input->post('days_past_due') ? $this->input->post('days_past_due') : $params['days_past_due'];
		
		$per_page 		=	$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$offset 		= 	$this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col 		= 	$this->input->post('order_col') ? $this->input->post('order_col') : 'invoice_id';
		$order_dir 		= 	$this->input->post('order_dir') ? $this->input->post('order_dir'): 'desc';
		$deleted 		= 	$this->input->post('deleted') ? $this->input->post('deleted'): $params['deleted'];
		
		$invoices_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search, 'deleted' => $deleted,'days_past_due' => $days_past_due, 'status' => $status);
		$this->session->set_userdata($this->invoice_type.'_invoices_search_data',$invoices_search_data);
		
		if ($search)
		{
			$config['total_rows'] = $this->Invoice->search_count_all($this->invoice_type,$search,$days_past_due,$deleted,$status);
			$table_data = $this->Invoice->search($this->invoice_type,$search,$days_past_due, $deleted,$per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'invoice_id' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'desc',$status);
		}
		else
		{
			$config['total_rows'] = $this->Invoice->count_all($this->invoice_type,$days_past_due,$deleted,$status);
			$table_data = $this->Invoice->get_all($this->invoice_type,$days_past_due,$deleted,$per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'invoice_id' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'desc',$status);
		}
		
		$config['base_url'] = site_url('invoices/sorting/'.$this->invoice_type);
		$config['uri_segment'] = 5;
		
		$config['per_page'] = $per_page;
		
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table']=get_invoices_manage_table_data_rows($table_data,$this);
		$data['invoice_type'] = $this->invoice_type;
		
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));
	}
	
	/*
	Loads the price rule edit form
	*/
	function view($type,$invoice_id=-1)
	{
		$this->invoice_type = $type;
		
		if ($invoice_id == -1)
		{
			$this->check_action_permission('add');			
		}
		else
		{
			$this->check_action_permission('edit');
		}
		
		$data = array();
		$data['invoice_info'] = $this->Invoice->get_info($this->invoice_type,$invoice_id);
		$data['invoice_type'] = $this->invoice_type;
		$data['invoice_id'] = $invoice_id;
		$data['payments'] = $this->Invoice->get_payments($this->invoice_type,$invoice_id)->result_array();
		$data['type_prefix'] = $this->invoice_type == 'customer' ? 'sale' : 'receiving';
		     		
		$terms = array('' => lang('common_none'));
			
		foreach($this->Invoice->get_all_terms() as $term_id => $term)
		{
			$terms[$term_id] = $term['name'];
		}

		
		$data['terms'] = $terms;
		
		
		$this->invoice_type = $type;


		if ($data['invoice_info']->{$type.'_id'})
		{
			if ($this->invoice_type == 'customer')
			{
				$sale_ids = $this->Sale->get_unpaid_store_account_sale_ids($data['invoice_info']->customer_id);

				$unpaid_orders = $this->Sale->get_unpaid_store_account_sales($sale_ids,'DESC');
			}
			else
			{
				$recv_ids = $this->Receiving->get_unpaid_store_account_recv_ids($data['invoice_info']->supplier_id);

				$unpaid_orders = $this->Receiving->get_unpaid_store_account_recvs($recv_ids,'DESC');
			}
			
			$data['orders'] = $unpaid_orders;
			
			$data['details'] = $this->Invoice->get_details($type,$invoice_id);
		}
		

		
		$this->load->view("invoices/form",$data);
		
		
	}
		
	function save($type,$invoice_id=-1)
	{

		$this->invoice_type = $type;
		
		if (empty($this->input->post($this->invoice_type.'_id'))) {

			echo json_encode(array('error' => true, 'message' => lang('common_please_select').' '.$this->invoice_type));
			die;
		}

		if ($invoice_id == -1)
		{
			$this->check_action_permission('add');			
		}
		else
		{
			$this->check_action_permission('edit');
		}		
		
		//Don't allow anything outside of customer or supplier
		if (!($this->invoice_type == 'customer' || $this->invoice_type == 'supplier'))
		{
			$this->invoice_type = 'customer';
		}
		$invoice_data = array(
			'invoice_date' => date('Y-m-d',$this->input->post('invoice_date') ? strtotime($this->input->post('invoice_date')) : time()),
			'due_date' => date('Y-m-d',strtotime($this->input->post('due_date'))),
			'term_id' => $this->input->post('term_id') ? $this->input->post('term_id') : NULL,
			$this->invoice_type."_id" => $this->input->post($this->invoice_type.'_id'),
			$this->invoice_type.'_po' => $this->input->post($this->invoice_type.'_po'),
			
		);
		
		if ($invoice_id == -1)
		{
			$invoice_data['location_id'] = $this->Employee->get_logged_in_employee_current_location_id();
		}
		
		$this->Invoice->save($this->invoice_type,$invoice_data,$invoice_id);
		
		$id = $invoice_id == -1 ? $invoice_data['invoice_id'] : $invoice_id;

		if (empty($id)) {
			echo json_encode(array('error' => true, 'message' => lang('common_please_select').' '.$this->invoice_type));
		} else {
			echo json_encode(array('success' => true, 'message' => lang('common_success'), 'invoice_id' => $id, 'redirect' => 2));
		}
    	
	}
	
	function delete($type)
	{
		$this->invoice_type = $type;
		
		$this->check_action_permission('delete');
		$invoices_to_delete=$this->input->post('ids');
		
		if($this->Invoice->delete_list($this->invoice_type,$invoices_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>lang('invoices_successful_deleted').' '.
			count($invoices_to_delete).' '.lang('invoices_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('invoices_cannot_be_deleted')));
		}
		
	}
	
	function undelete($type)
	{
		$this->invoice_type = $type;
		
		$this->check_action_permission('delete');
		$invoices_to_delete=$this->input->post('ids');
		
		if($this->Invoice->undelete_list($this->invoice_type,$invoices_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>lang('invoices_successful_undeleted').' '.
			count($invoices_to_delete).' '.lang('invoices_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('invoices_cannot_be_undeleted')));
		}
	}
	
 	function toggle_show_deleted($deleted=0)
 	{
 		$this->check_action_permission('search');
		$params = $this->session->userdata($this->invoice_type.'_invoices_search_data') ? $this->session->userdata($this->invoice_type.'_invoices_search_data') : array('order_col' => 'invoice_id', 'order_dir' => 'desc','deleted' => 0,'days_past_due' => NULL);
 		$params['deleted'] = $deleted;
		$params['offset'] = 0;
		
 		$this->session->set_userdata($this->invoice_type.'_invoices_search_data',$params);
		
	}
		
	function reload_invoice_table($type='customer')
	{
		$this->invoice_type = $type;
		
		$config['base_url'] = site_url('invoices/sorting/'.$type);
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$params = $this->session->userdata($this->invoice_type.'_invoices_search_data') ? $this->session->userdata($this->invoice_type.'_invoices_search_data') : array('order_col' => 'invoice_id', 'order_dir' => 'desc','deleted' => 0,'days_past_due' => NULL);

		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";		
		$data['days_past_due'] = $params['days_past_due'] ? $params['days_past_due'] : NULL;		
		$data['invoice_type'] = $this->invoice_type;

		if ($data['search'])
		{
			$config['total_rows'] = $this->Invoice->search_count_all($this->invoice_type,$data['search'],$data['days_past_due'], $params['deleted'],10000);
			$table_data = $this->Invoice->search($this->invoice_type,$data['search'],$data['days_past_due'], $params['deleted'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		else
		{
			$config['total_rows'] = $this->Invoice->count_all($this->invoice_type,$data['days_past_due'],$params['deleted']);
			$table_data = $this->Invoice->get_all($this->invoice_type,$data['days_past_due'],$params['deleted'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		
		echo get_invoices_manage_table($table_data,$this);
	}
	
	function suggest_customer()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Customer->get_customer_search_suggestions($this->input->get('term'),0,100);
		echo json_encode(H($suggestions));
	}	
	
	function suggest_supplier()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Supplier->get_supplier_search_suggestions($this->input->get('term'),0,100);
		echo json_encode(H($suggestions));
	}
	
	function save_column_prefs($type)
	{
		$this->invoice_type = $type;
		
		$this->load->model('Employee_appconfig');
		
		if ($this->input->post('columns'))
		{
			$this->Employee_appconfig->save($this->invoice_type.'_invoices_column_prefs',serialize($this->input->post('columns')));
		}
		else
		{
			$this->Employee_appconfig->delete($this->invoice_type.'_invoices_column_prefs');			
		}
	}
	
	function manage_terms()
	{
		$terms = $this->Invoice->get_all_terms();
		$data = array('terms' => $terms, 'term_list' => $this->_term_list());
		$data['redirect'] = $this->input->get('redirect');

		$progression = $this->input->get('progression');
		$quick_edit = $this->input->get('quick_edit');
		$data['progression'] = !empty($progression);
		$data['quick_edit'] = !empty($quick_edit);
		$this->load->view('invoices/terms',$data);
	}

	function save_term($term_id = FALSE)
	{

		if ($this->input->post('term_id'))
		{
			$term_id = $this->input->post('term_id');
		}
		
		$term_data = array(
			'name' => $this->input->post('name'),
			'description' => $this->input->post('description'),
			'days_due' => $this->input->post('days_due'),
		);
		if ($this->Invoice->save_term($term_data, $term_id))
		{
			echo json_encode(array('success'=>true,'message'=>lang('invoices_term_successful_adding')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('invoices_term_successful_error')));
		}
	}

	function delete_term()
	{
		$term_id = $this->input->post('term_id');
		if($this->Invoice->delete_term($term_id))
		{
			echo json_encode(array('success'=>true,'message'=>lang('invoices_terms_successful_deleted')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('invoices_terms_cannot_be_deleted')));
		}
	}

	function term_list()
	{
		echo $this->_term_list();
	}

	function _term_list()
	{
		$terms = $this->Invoice->get_all_terms();
     			
		
		
		$return = '<ul>';
		foreach($terms as $term_id => $term)
		{
			$return .='<li>'.H($term['name']).
					'<a href="javascript:void(0);" class="edit_term" data-days_due="'.H($term['days_due']).'" data-description = "'.H($term['description']).'" data-name = "'.H($term['name']).'" data-term_id="'.$term_id.'">['.lang('common_edit').']</a> '.
					'<a href="javascript:void(0);" class="delete_term" data-term_id="'.$term_id.'">['.lang('common_delete').']</a> ';
			 $return .='</li>';
		}
     	$return .='</ul>';

		return $return;
	}
	
	function add_to_invoice_credit_memo($type,$invoice_id)
	{
		//make sure negative
		$_POST['total'] = abs($_POST['total'])*-1;		
		$this->add_to_invoice_manual($type,$invoice_id);
	}
	
	function add_to_invoice_manual($type,$invoice_id)
	{
		$this->invoice_type = $type;
		
		$old_invoice_info = $this->Invoice->get_info($type,$invoice_id);
		$old_total = $old_invoice_info->total;
		$old_balance = $old_invoice_info->balance;
		$details_data = array();
		$details_data['invoice_id'] = $invoice_id;
		$details_data['total'] = $this->input->post('total');	
		$details_data['description'] = $this->input->post('description');	
		$details_data['account'] = $this->input->post('account');	
		$this->Invoice->save_invoice_details($type,$details_data);
		
		$new_total = $this->Invoice->get_total_from_invoice_details($type,$invoice_id);
		
		//Update balance and total since we just added a order to this invoice
		$total_change = $new_total - $old_total;
		$invoice_data = array('total' => $old_total + $total_change,'balance' => $old_balance + $total_change);
		$this->Invoice->save($type,$invoice_data,$invoice_id);
		
		redirect(site_url("invoices/view/$type/$invoice_id"));
	}
	
	function add_to_invoice($type,$invoice_id,$order_id)
	{
		$this->invoice_type = $type;
		
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
		$invoice_data = array('total' => $old_total + $total_change,'balance' => $old_balance + $total_change);
		$this->Invoice->save($type,$invoice_data,$invoice_id);
		
		redirect(site_url("invoices/view/$type/$invoice_id"));
	}
	
	function edit_detail($type,$invoice_details_id)
	{
		$invoice_id = $this->Invoice->get_invoice_id_for_detail($type,$invoice_details_id);
		$old_invoice_info = $this->Invoice->get_info($type,$invoice_id);
		$old_total = $old_invoice_info->total;
		$old_balance = $old_invoice_info->balance;
		
		$details_data = array($this->input->post('name') => $this->input->post('value'));
		$this->Invoice->save_invoice_details($type,$details_data,$invoice_details_id);
		
		
		$new_total = $this->Invoice->get_total_from_invoice_details($type,$invoice_id);
		
		//Update balance and total if we edited a total charge for an invoice
		$total_change = $new_total - $old_total;
		$invoice_data = array('total' => $old_total + $total_change,'balance' => $old_balance + $total_change);
		$this->Invoice->save($type,$invoice_data,$invoice_id);
	}
	
	function delete_detail($type,$invoice_details_id)
	{
		$invoice_id = $this->Invoice->get_invoice_id_for_detail($type,$invoice_details_id);
		$old_invoice_info = $this->Invoice->get_info($type,$invoice_id);
		$old_total = $old_invoice_info->total;
		$old_balance = $old_invoice_info->balance;
		
		$this->Invoice->delete_invoice_details($type,$invoice_details_id);
		
		
		$new_total = $this->Invoice->get_total_from_invoice_details($type,$invoice_id);
		
		//Update balance and total if we edited a total charge for an invoice
		$total_change = $new_total - $old_total;
		$invoice_data = array('total' => $old_total + $total_change,'balance' => $old_balance + $total_change);
		$this->Invoice->save($type,$invoice_data,$invoice_id);
		
		redirect(site_url("invoices/view/$type/$invoice_id"));

	}
	
	function show($type,$invoice_id)
	{
		$this->invoice_type = $type;
		
		$data = array();
		$data['invoice_info'] = $this->Invoice->get_info($this->invoice_type,$invoice_id);
		$data['invoice_type'] = $this->invoice_type;
		$data['invoice_id'] = $invoice_id;
		$data['payments'] = $this->Invoice->get_payments($this->invoice_type,$invoice_id)->result_array();
		
		$this->invoice_type = $type;
		
		$data['details'] = $this->Invoice->get_details($type,$invoice_id);
		$data['type_prefix'] = $this->invoice_type == 'customer' ? 'sale' : 'receiving';
		
		$this->load->view("invoices/show",$data);
	}
	
	function email_invoice($type,$invoice_id)
	{
		$this->load->library('email');
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		
		
		$this->invoice_type = $type;
		
		$data = array();
		$data['invoice_info'] = $this->Invoice->get_info($this->invoice_type,$invoice_id);
		$data['invoice_type'] = $this->invoice_type;
		$data['invoice_id'] = $invoice_id;
		$data['payments'] = $this->Invoice->get_payments($this->invoice_type,$invoice_id)->result_array();
		$data['type_prefix'] = $this->invoice_type == 'customer' ? 'sale' : 'receiving';
				
		$data['details'] = $this->Invoice->get_details($type,$invoice_id);
		
		$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@coreware.com', $this->config->item('company'));

		if($this->Location->get_info_for_key('cc_email'))
		{
			$this->email->cc($this->Location->get_info_for_key('cc_email'));
		}

		if($this->Location->get_info_for_key('bcc_email'))
		{
			$this->email->bcc($this->Location->get_info_for_key('bcc_email'));
		}


		if ($this->invoice_type == 'customer')
		{
			$this->email->to($this->Customer->get_info($data['invoice_info']->customer_id)->email);
		}
		else
		{
			$this->email->to($this->Supplier->get_info($data['invoice_info']->supplier_id)->email);
		}

		$this->email->subject('Invoice from '.$this->config->item('company'));
		$this->email->message($this->load->view("invoices/email",$data, true));
		$this->email->send();
		
	}
	
	function pay($type,$invoice_id)
	{
		$this->invoice_type = $type;
		
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
				
		$payment_types[lang('common_cash')] = lang('common_cash');
		$payment_types[lang('common_check')] = lang('common_check');
		$payment_types[lang('common_credit')] = lang('common_credit');
				
		$data = array();
		$data['invoice_info'] = $this->Invoice->get_info($this->invoice_type,$invoice_id);
		$data['invoice_type'] = $this->invoice_type;
		$data['invoice_id'] = $invoice_id;
		$data['registers'] = $registers;
		$data['payments'] = $this->Invoice->get_payments($this->invoice_type,$invoice_id)->result_array();
		$data['payment_types'] = $payment_types;
		$is_coreclear_processing = $this->Location->get_info_for_key('credit_card_processor') == 'coreclear' || $this->Location->get_info_for_key('credit_card_processor') == 'coreclear2';
		$data['is_coreclear_processing'] = $is_coreclear_processing;
		$this->load->view("invoices/pay",$data);
		
	}
	
	function process_payment($type,$invoice_id)
	{
			
		$invoice_info = $this->Invoice->get_info($type,$invoice_id);
		$payment_type = $this->input->post('payment_type');
		
		$amount = $this->input->post('amount');
		$register = $this->input->post('register');
		$cc_number = $this->input->post('cc_number');
		$ccv = $this->input->post('cc_ccv');
		$address = $invoice_info->address_1;
		$zip = $invoice_info->zip;
		$cc_token = FALSE;
		
		$is_coreclear_processing = $this->Location->get_info_for_key('credit_card_processor') == 'coreclear' || $this->Location->get_info_for_key('credit_card_processor') == 'coreclear2';
		if ($type == 'customer' && $payment_type == lang('common_credit') && $is_coreclear_processing)
		{
			if ($register == -2)
			{
				//Tokens only apply to customers right now
				$cc_token = $this->Customer->get_info($invoice_info->person_id)->cc_token;
			}
		
			list($expire_month,$expire_year) = explode('/',$this->input->post('cc_exp_date'));
		
			$process_payment_response = $this->Invoice->process_payment($amount,$register,$cc_token,$cc_number,$ccv,$expire_month,$expire_year,$address,$zip);
		
			if($process_payment_response['success'])
			{
				$payment_data = $process_payment_response['payment_response_data'];
			
				$this->Invoice->add_payment($type,$invoice_id,$payment_data);
			
				//Update balance as we made a payment
				$invoice_data = array('balance' => $invoice_info->balance - $payment_data['payment_amount'],'last_paid' => date('Y-m-d'));
				$this->Invoice->save($type,$invoice_data,$invoice_id);
			
				redirect(site_url("invoices/pay/$type/$invoice_id?success=1"));
			
			}
			else
			{
				redirect(site_url("invoices/pay/$type/$invoice_id?success=0"));
			}
		}
		else
		{
			$payment_data = array(
			    'payment_date' => date('Y-m-d H:i:s'),	
			    'payment_type' => $payment_type,
			    'payment_amount' => $amount,
			);
			$this->Invoice->add_payment($type,$invoice_id,$payment_data);
		
			//Update balance as we made a payment
			$invoice_data = array('balance' => $invoice_info->balance - $payment_data['payment_amount'],'last_paid' => date('Y-m-d'));
			$this->Invoice->save($type,$invoice_data,$invoice_id);
			redirect(site_url("invoices/pay/$type/$invoice_id?success=true"));
			
		}
	}	
	
	function clear_state($type)
	{
		$this->invoice_type = $type;
		$this->session->set_userdata($this->invoice_type.'_invoices_search_data', array('offset' => 0, 'order_col' => 'invoice_id', 'order_dir' => 'desc','deleted' => 0,'days_past_due' => NULL));
		redirect("invoices/index/$type");
	}
	
	function get_default_terms($type,$person_id)
	{
		$default_term_id = NULL;
		
		if ($type =='customer')
		{
			$default_term_id = $this->Customer->get_info($person_id)->default_term_id;
		}
		else
		{
			$default_term_id = $this->Supplier->get_info($person_id)->default_term_id;
		}
		
		echo json_encode(array('default_term_id' => $default_term_id ));
	}
	
	function get_term_default_due_date($term_id=false)
	{
		if ($term_id)
		{
			$term = $this->Invoice->get_term($term_id);
			$default_due_date = date(get_date_format(),strtotime('+'.$term->days_due.' days'));
		
			echo json_encode(array('term_default_due_date' => $default_due_date ));
		}
	}

	function zatca_invoice($select_range = "LAST_7"){

		$this->load->model('Customer');

		$today = date('Y-m-d');
		$yesterday = date('Y-m-d', mktime(0,0,0,date("m"),date("d")-1,date("Y")));
		$six_days_ago = date('Y-m-d', mktime(0,0,0,date("m"),date("d")-6,date("Y")));
		$twenty_nine_days_ago = date('Y-m-d', mktime(0,0,0,date("m"),date("d")-29,date("Y")));

		$dates = array(
			'TODAY' => array('start_date' => $today.' 00:00:00', 'end_date'=> $today.' 23:59:59'),
		);

		if ($this->Employee->has_module_action_permission('reports', 'can_change_report_date', $this->Employee->get_logged_in_employee_info()->person_id)){
			$dates = array_merge($dates, array(
				'YESTERDAY'	=> array('start_date' =>$yesterday.' 00:00:00' ,'end_date'=> $yesterday.' 23:59:59'),
				'LAST_7' => array('start_date' =>$six_days_ago.' 00:00:00' ,'end_date' =>$today.' 23:59:59'),
				'LAST_30' => array('start_date' =>$twenty_nine_days_ago.' 00:00:00' ,'end_date' =>$today.' 23:59:59'),
			));
		}

		$day_start = $dates[$select_range]['start_date'];
		$day_end = $dates[$select_range]['end_date'];

		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);

		if($location_zatca_config){
			$sale_ids = $this->Sale->get_sale_ids_for_range($day_start, $day_end, NULL, $location_id);

			$zatca_invoice_array = $this->Invoice->get_zatca_invoice_by_range($day_start, $day_end, $location_id);
			$sale_zatca_ids = array();
			if($zatca_invoice_array && count($zatca_invoice_array) > 0){
				foreach($zatca_invoice_array as $zatca_invoice){
					$sale_zatca_ids[$zatca_invoice['sale_id']]  = $zatca_invoice['reported'];
				}
			}
	
			$data = array(
				'select_range' => $select_range,
				'sale_id' => (count($sale_ids) > 0 ? $sale_ids[0] : 0 ), // selected id
				'sale_ids' => $sale_ids,
				'sale_zatca_ids' => $sale_zatca_ids,
				'ccsid' => isset($location_zatca_config['compliance_csid'])?$location_zatca_config['compliance_csid']:"",
				'cert' => isset($location_zatca_config['cert'])?$location_zatca_config['cert']:"",
				'private_key' => isset($location_zatca_config['private_key'])?$location_zatca_config['private_key']:"",
				'pcsid' => isset($location_zatca_config['production_csid'])?$location_zatca_config['production_csid']:"",
				'location_zatca_config' => $location_zatca_config
			);
	
			$this->load->view('invoices/zatca_invoice',$data);
		} else {
			$data = array(
				'select_range' => $select_range,
				'sale_id' => 0,
				'sale_ids' => array(),
				'sale_zatca_ids' => array(),
				'ccsid' => "",
				'cert' => "",
				'private_key' => "",
				'pcsid' => "",
				'location_zatca_config' => NULL
			);
			$this->load->view('invoices/zatca_invoice',$data);
		}
	}

	public function zatca_generate_ccsid_pcsid(){
		$zatca_otp = $this->input->post('zatca_otp');

		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);

		$data = array();
		if($location_zatca_config){

			$data = array(
				'csr.common.name' => $location_zatca_config['csr_common_name'],
				'csr.serial.number' => $location_zatca_config['csr_serial_number'],
				'csr.organization.identifier' => $location_zatca_config['csr_organization_identifier'],
				'csr.organization.unit.name' => $location_zatca_config['csr_organization_unit_name'],
				'csr.organization.name' => $location_zatca_config['csr_organization_name'],
				'csr.country.name' => $location_zatca_config['csr_country_name'],
				'csr.invoice.type' => $location_zatca_config['csr_invoice_type'],
				'csr.location.address' => $location_zatca_config['csr_location_address'],
				'csr.industry.business.category' => $location_zatca_config['csr_industry_business_category'],
			);
		}else{
			$ret = array(
				'state' => 0,
				'message' => "Please config csr inputs"
			);
			echo json_encode($ret);
			exit();
		}

		$ret_csr = Fatoora::api_generate_csr($data);

		if(!$ret_csr['state']){
			echo json_encode($ret_csr);
			exit();
		}

		$csr = $ret_csr['csr'];
		$csr_private_key = $ret_csr['private_key'];

		$curl = curl_init();
        $zatca_api_url = "";
        if($this->config->item('use_saudi_tax_test_config')){
            $zatca_api_url = ZATCA_API_TEST_URL;
        }else{
            $zatca_api_url = ZATCA_API_LIVE_URL;
        }

		curl_setopt_array($curl, 
			array(
				CURLOPT_URL => $zatca_api_url.'/compliance',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>'{
					"csr": "'.$csr.'"
				}',
				CURLOPT_HTTPHEADER => array(
					'OTP: ' . $zatca_otp,
					'Accept-Version: V2',
					'Content-Type: application/json'
				),
			)
		);

		$response0 = curl_exec($curl);

		curl_close($curl);
		$response = json_decode($response0, true);

		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);
	
		$ret_state = 0;
		$ret_msg = "";
		if(isset($response['requestID'])){
			$ret_state = 1;
			$ret_msg = "Compliance CSID generated successfully.";
			$location_zatca_config['csr'] = $csr;
			$location_zatca_config['csr_private_key'] = $csr_private_key;
			$location_zatca_config['cert'] = ""; // clear cert
			$location_zatca_config['private_key'] = ""; // clear private_key
			$location_zatca_config['compliance_csid'] = $response0;
			$location_zatca_config['production_csid'] = "";
			$this->Appconfig->save_zatca_config($location_zatca_config);

		}else {

			if(isset($response['errors'])){
				if(is_array($response['errors'])){
					$ret_msg = $response['errors'][0]['message'];
				}
			}else{
				if(isset($response['message'])){
					$ret_msg = $response['message'];
				}else{
					$ret_msg = $response;
				}
			}
			$ret_msg = "Compliance CSID error: ".$ret_msg;
			echo json_encode(['state' => $ret_state, 'csr' => $response, 'message' => $ret_msg]);
			exit();
		}

		$ccsid = $location_zatca_config['compliance_csid'];
		$data = array(
			'ccsid' => json_decode($ccsid, true)
		);

		$response_pcsid = Fatoora::generate_pcsid($data);
		$response1 = json_decode($response_pcsid, true);

		if(isset($response1['requestID'])){

			$location_zatca_config['production_csid'] = $response_pcsid;

			$location_zatca_config['cert'] = "";
			$location_zatca_config['private_key'] = "";

			$this->Appconfig->save_zatca_config($location_zatca_config);
			$ret = array(
                'state' => 1,
                'message' => "Compliance and Production CSID generated successfully.",
				'data' => array(
					'csr' => $location_zatca_config['csr'],
					'csr_private_key' => $location_zatca_config['csr_private_key'],
					'ccsid' => $ccsid,
					'pcsid' => $response_pcsid
				)
            );

		}else{
            $ret = array(
                'state' => 0,
                'message' => "Production CSID generation failed.",
            );
		}

		echo json_encode($ret);
		exit();

	}

	function zatca_generate_pcsid(){

		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);

		//get sale invoice id json request content
		$ccsid = $location_zatca_config['compliance_csid'];
		if(!$ccsid){
			$ret = array(
				'state' => 0,
				'message' => "Please first generate ccsid.",
			);

			echo json_encode($ret);
			exit();
		}

		$data = array(
			'ccsid' => json_decode($ccsid, true)
		);

		$response = Fatoora::generate_pcsid($data);
		$response1 = json_decode($response, true);

		if(isset($response1['requestID'])){

			$location_zatca_config['production_csid'] = $response;
			$location_zatca_config['cert'] = "";
			$location_zatca_config['private_key'] = "";
			$this->Appconfig->save_zatca_config($location_zatca_config);
			$ret = array(
                'state' => 1,
                'message' => "Production CSID generated successfully.",
				'data' => $response
            );

		}else{
            $ret = array(
                'state' => 0,
                'message' => "Production CSID generation failed.",
            );
		}

		echo json_encode($ret);
		exit();
	}

	function zatca_renew_pcsid(){

		$renew_opt = $this->input->post('renew_opt');
		
		//get sale invoice id json request content
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);

		//get sale invoice id json request content
		$ccsid = $location_zatca_config['compliance_csid'];
		$pcsid = $location_zatca_config['production_csid'];

		$data = array(
			'renew_opt' => $renew_opt,
			'ccsid' => json_decode($ccsid, true),
			'pcsid' => json_decode($pcsid, true),
			'csr' =>  $location_zatca_config['csr']
		);

		$response = Fatoora::renew_pcsid($data);

		$response1 = json_decode($response, true);

		if(isset($response1['requestID'])){

			$location_zatca_config['production_csid'] = $response;
			$location_zatca_config['cert'] = "";
			$location_zatca_config['private_key'] = "";
			$this->Appconfig->save_zatca_config($location_zatca_config);

			$ret = array(
                'state' => 1,
                'message' => "Renews an X509 Certificate (CSID) based on submitted CSR.",
				'data' => $response
            );

		}else{
            $ret = array(
                'state' => 0,
                'message' => "Production CSID renews failed.",
            );
		}

		echo json_encode($ret);
		exit();
	}
	
	public function zatca_submit_cert(){
		$zatca_cert = $this->input->post('zatca_cert');
		$zatca_private_key = $this->input->post('zatca_private_key');

		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);
		$location_zatca_config['cert'] = $zatca_cert; // clear cert
		$location_zatca_config['private_key'] = $zatca_private_key; // clear cert
		$ret_save = $this->Appconfig->save_zatca_config($location_zatca_config);

		$ret = array();
		if($ret_save){
			$ret = array(
				'state' => 1,
				'message' => "Your certificate has been successfully submitted.",
			);
		}else{
			$ret = array(
				'state' => 0,
				'message' => "Failed to save certificate.",
			);
		}
		echo json_encode($ret);
		exit();
	}
}
