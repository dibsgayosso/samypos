<?php
require_once ("Secure_area.php");

class Work_orders extends Secure_area
{
	function __construct()
	{
		parent::__construct('work_orders');	
		$this->load->model('Work_order');
		$this->load->model('Employee');
		$this->load->model('Sale');
		$this->load->model('Customer');
		$this->load->model('Category');
		$this->load->model('Appfile');
		$this->load->model('Location');
		$this->load->model('Tier');
		$this->load->model('Item');
		$this->load->model('Item_kit');
		$this->load->model('Item_kit_items');
		$this->load->model('Item_kit_taxes');
		$this->load->model('Item_location_taxes');
		$this->load->model('Item_taxes');
		$this->load->model('Item_taxes_finder');
		$this->load->model('Item_kit_taxes_finder');
		$this->load->model('Item_location');
		$this->load->model('Item_variation_location');
		$this->load->model('Item_variations');
		$this->load->model('Item_serial_number');
		$this->load->model('Manufacturer');
		$this->load->model('Item_attribute_value');
		$this->load->model('Item_attribute');
		$this->load->model('Employee_appconfig');
		$this->load->model('Sale_types');
		$this->load->model('Supplier');

		$this->load->helper('work_order');

		$this->lang->load('work_orders');
		$this->lang->load('module');
		$this->lang->load('sales');	
		$this->load->helper('text');
		$this->load->model('Item_modifier');
	}

	function index($offset=0, $open_new=0)
	{
		$this->check_action_permission('search');
		$data['open_new'] = $open_new;
		
		if ($this->input->get('new')) {
			$data['open_new'] = 1;
		}		
		$params = $this->session->userdata('work_orders_search_data') ? $this->session->userdata('work_orders_search_data') : array('offset' => 0, 'order_col' => 'id', 'order_dir' => 'desc', 'search' => FALSE,'deleted' => 0,'status' => '','technician' => '','hide_completed_work_orders' => $this->Employee_appconfig->get('hide_completed_work_orders'));
		
		if ($offset != $params['offset'])
		{
		   redirect('work_orders/index/'.$params['offset']);
		}
		
		$config['base_url'] = site_url('work_orders/sorting');
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		
		$data['controller_name']=strtolower(get_class());
		$data['per_page'] = $config['per_page'];
		
		$data['search'] = $params['search'] ? $params['search'] : "";
		$data['deleted'] = $params['deleted'];
		$data['status'] = $params['status'] ? $params['status'] : "";
		
		$default_tech_is_logged_employee = $this->config->item('default_tech_is_logged_employee');
		
		$data['technician'] = $params['technician'] ? $params['technician'] : ( $default_tech_is_logged_employee ? $this->Employee->get_logged_in_employee_info()->person_id : "");
		$data['hide_completed_work_orders'] = $params['hide_completed_work_orders'] ? $params['hide_completed_work_orders'] : "";

		if ($data['search'] || $data['status'] || $data['technician']!=-1 || $data['hide_completed_work_orders'])
		{
			$config['total_rows'] = $this->Work_order->search_count_all($data['search'],$params['deleted'],10000,$data['status'],$data['technician'],$data['hide_completed_work_orders']);
			$table_data = $this->Work_order->search($data['search'],$params['deleted'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir'],$data['status'],$data['technician'],$data['hide_completed_work_orders']);
		}
		else
		{	
			$config['total_rows'] = $this->Work_order->count_all($params['deleted']);
			$table_data = $this->Work_order->get_all($params['deleted'],$data['per_page'], $params['offset'],$params['order_col'],$params['order_dir']);
		}
				
		$data['total_rows'] = $config['total_rows'];
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		
		$data['pagination'] = $this->pagination->create_links();
		$data['order_col'] = $params['order_col'];
		$data['order_dir'] = $params['order_dir'];
		$data['manage_table']= get_work_orders_manage_table($table_data,$this);
		
		$data['default_columns'] = $this->Work_order->get_default_columns();
		$data['selected_columns'] = $this->Employee->get_work_order_columns_to_display();
		$data['all_columns'] = array_merge($data['selected_columns'],$this->Work_order->get_displayable_columns());
		$repair_item_id = $this->work_order->create_or_update_repair_item();
		$data['work_orders_repair_item'] = $repair_item_id;
		$change_status_array = array(''=>lang('work_orders_change_status'));
		$search_status_array = array(''=>lang('common_all'));

		$all_statuses = $this->Work_order->get_all_statuses();
		foreach($all_statuses as $id => $row)
		{
			$change_status_array[$id] = $row['name'];
			$search_status_array[$id] = $row['name'];
		}
		
		$data['change_status_array'] = $change_status_array;
		$data['search_status_array'] = $search_status_array;
		
		$employees = array('-1' => lang('common_all'));

		foreach($this->Employee->get_all(0,10000,0,'first_name')->result() as $employee)
		{
			$employees[$employee->person_id] = $employee->first_name .' '.$employee->last_name;
		}
		$data['employees'] = $employees;

		$data['status_boxes'] = $this->Work_order->get_work_orders_by_status();
		
		$data['customer_id_for_new_work_order'] = $this->session->userdata('customer_id_for_new_work_order') ? $this->session->userdata('customer_id_for_new_work_order') : '';
		if($data['customer_id_for_new_work_order']){
			$data['customer_info'] = $this->Customer->get_info($data['customer_id_for_new_work_order']);
		}

		$data['items_for_new_work_order'] = $this->session->userdata('items_for_new_work_order') ? $this->session->userdata('items_for_new_work_order') : array();

		$suppliers = array('' => lang('work_orders_select_supplier'));
		foreach($this->Supplier->get_all()->result_array() as $row)
		{
			$suppliers[$row['person_id']] = $row['company_name'] .' ('.$row['first_name'] .' '. $row['last_name'].')';
		}
		$data['suppliers'] = $suppliers;

		$this->load->view('work_orders/manage', $data);

	}
	
	function clear_state()
	{
		$params = array('offset' => 0, 'order_col' => 'id', 'order_dir' => 'desc', 'search' => FALSE,'deleted' => 0,'status' => '','technician' => '-1','hide_completed_work_orders' => $this->Employee_appconfig->get('hide_completed_work_orders'));
		$this->session->set_userdata('work_orders_search_data', $params);
		redirect('work_orders');
	}
	
	function search()
	{
		$this->check_action_permission('search');
		$params = $this->session->userdata('work_orders_search_data');
		
		$search=$this->input->post('search') ? $this->input->post('search') : "";
		$status = $this->input->post('status');
		$technician = $this->input->post('technician');
		$hide_completed_work_orders = $this->input->post('hide_completed_work_orders') ? 1 : 0;
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'id';
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): 'desc';
		$deleted = $this->input->post('deleted') ? $this->input->post('deleted'): (isset($params['deleted']) && $params['deleted'] ? 1 : 0);
		
		$work_orders_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search, 'deleted' => $deleted,'status'=>$status,'technician'=>$technician,'hide_completed_work_orders'=>$hide_completed_work_orders);
		$this->session->set_userdata("work_orders_search_data",$work_orders_search_data);
		
		if ($search || $status || $technician!=-1 || $hide_completed_work_orders)
		{
			$config['total_rows'] = $this->Work_order->search_count_all($search,$deleted,10000,$status,$technician,$hide_completed_work_orders);
			$table_data = $this->Work_order->search($search,$deleted,$per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'desc',$status,$technician,$hide_completed_work_orders);
		}
		else
		{
			$config['total_rows'] = $this->Work_order->count_all($deleted);
			$table_data = $this->Work_order->get_all($deleted,$per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'desc');
		}
		
		$config['base_url'] = site_url('work_orders/sorting');
		
		$config['per_page'] = $per_page;
		
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table']=get_work_orders_manage_table_data_rows($table_data,$this);

		$this->Employee_appconfig->save('hide_completed_work_orders',$hide_completed_work_orders);

		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));
	}
	
	function sorting()
	{
		$this->check_action_permission('search');
		$params = $this->session->userdata('work_orders_search_data') ? $this->session->userdata('work_orders_search_data') : array('order_col' => 'id', 'order_dir' => 'desc','deleted' => 0,'status' => '','technician' => '-1','hide_completed_work_orders' => $this->Employee_appconfig->get('hide_completed_work_orders'));
		$search = $this->input->post('search') ? $this->input->post('search') : "";
		$deleted = $this->input->post('deleted') ? $this->input->post('deleted') : $params['deleted'];
		$status = $params['status'] ? $params['status'] : "";
		$technician = $params['technician'] ? $params['technician'] : "-1";
		$hide_completed_work_orders = $params['hide_completed_work_orders'] ? $params['hide_completed_work_orders'] : "";

		$per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : $params['order_col'];
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): $params['order_dir'];
		
		$item_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search,'deleted' => $deleted,'status' => $status,'technician'=>$technician ,'hide_completed_work_orders'=>$hide_completed_work_orders);
		
		$this->session->set_userdata("work_orders_search_data",$item_search_data);
		
		if ($search || $status || $technician!=-1 || $hide_completed_work_orders)
		{
			$config['total_rows'] = $this->Work_order->search_count_all($search,$deleted,10000,$status,$technician,$hide_completed_work_orders);
			$table_data = $this->Work_order->search($search, $deleted,$per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $order_col, $order_dir,$status,$technician,$hide_completed_work_orders);
		}
		else
		{
			$config['total_rows'] = $this->Work_order->count_all($deleted);
			$table_data = $this->Work_order->get_all($deleted,$per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $order_col,$order_dir);
		}
		
		$config['base_url'] = site_url('work_orders/sorting');
		$config['per_page'] = $per_page; 
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		$this->load->model('Employee_appconfig');
		$data['default_columns'] = $this->Work_order->get_default_columns();
		$data['manage_table'] = get_work_orders_manage_table_data_rows($table_data, $this);
		
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'], 'total_rows' => $config['total_rows']));
	}	

	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
		$this->check_action_permission('search');
		//allow parallel searchs to improve performance.
		session_write_close();
		$params = $this->session->userdata('work_orders_search_data') ? $this->session->userdata('work_orders_search_data') : array('deleted' => 0);
		$suggestions = $this->Work_order->get_search_suggestions($this->input->get('term'),$params['deleted'],100);
		echo json_encode($suggestions);
	}
	
	/*
	Loads the Work order edit form
	*/
	function view($work_order_id=-1,$redirect_code=0)
	{
		$this->load->model('Module_action');
		$this->check_action_permission('edit');
		
		$data = $this->_get_work_order_data($work_order_id);
		$data['redirect']= $redirect_code;
		$data['work_order_id'] = $work_order_id;

		$data['redirect_code']=$redirect_code;
		$data['files'] = $this->Work_order->get_files($work_order_id)->result();
		$data['controller_name']=strtolower(get_class());

		$data['checkbox_groups'] = $this->Work_order->get_checkbox_groups();

		$repair_item_id = $this->work_order->create_or_update_repair_item();
		$data['work_orders_repair_item'] = $repair_item_id;
		
		$data['selected_checkbox_groups'] = array();
		if($workorder_checkbox_group_id = $this->Work_order->get_workorder_checkbox_group_id($work_order_id)){
			$data['selected_checkbox_groups'] = $this->Work_order->get_checkbox_groups($workorder_checkbox_group_id);
		}
		
		$data['checkboxes_state_list'] = $this->Work_order->get_checkboxes_states($work_order_id);
		$this->load->view('work_orders/form', $data);
	}

	private function _get_work_order_data($work_order_id)
	{
		$data = array();
		$data['work_order_info_object'] = $this->Work_order->get_info($work_order_id)->row();

		$work_order_info = $this->Work_order->get_info($work_order_id)->row_array();

		$data['sale_info'] = $this->Sale->get_info($work_order_info['sale_id'])->row();
		$data['work_order_info'] = $work_order_info;
		$data['work_order_status_info'] = $this->Work_order->get_status_info($work_order_info['status']);
		$data['all_workorder_statuses'] = $this->Work_order->get_all_statuses();
		
		$change_status_array = array(''=>lang('work_orders_change_status'));

		foreach($data['all_workorder_statuses'] as $id => $row)
		{
			$change_status_array[$id] = $row['name'];
		}

		unset($change_status_array[$work_order_info['status']]);
		$data['change_status_array'] = $change_status_array;

		$data['customer_info'] = $this->Work_order->get_customer_info($work_order_id);
		$data['items_being_repaired'] = $this->Work_order->get_work_order_items($work_order_id,1);
		$data['notes'] = $this->Work_order->get_sales_items_notes($work_order_id);
		$first_line_note = $this->Work_order->get_first_line_note($work_order_id);

		$data['work_order_images'] = $work_order_info['images'] && unserialize($work_order_info['images']) ? unserialize($work_order_info['images']) : array();

		$data['first_line_note'] = $first_line_note;

		$data['work_order_items'] = $this->Work_order->get_work_order_items($work_order_id,0);
		
		$employees = array('' => lang('common_none'));

		foreach($this->Employee->get_all()->result() as $employee)
		{
			$employees[$employee->person_id] = $employee->first_name .' '.$employee->last_name;
		}
		$data['employees'] = $employees;

		return $data;
	}
	
	function save($work_order_id=-1)
	{
		$this->check_action_permission('edit');

		$work_order_data = array();

		$work_order_data['estimated_repair_date'] = $this->input->post('estimated_repair_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('estimated_repair_date'))) : NULL;
		$work_order_data['estimated_parts'] = $this->input->post('estimated_parts') ? $this->input->post('estimated_parts') : NULL;
		$work_order_data['estimated_labor'] = $this->input->post('estimated_labor') ? $this->input->post('estimated_labor') : NULL;
		$work_order_data['warranty'] = $this->input->post('warranty') ? 1 : 0;
		
		for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++)
		{
			if ($this->Work_order->get_custom_field($k) !== FALSE)
			{
				if ($this->Work_order->get_custom_field($k,'type') == 'checkbox')
				{
					$work_order_data["custom_field_{$k}_value"] = $this->input->post("custom_field_{$k}_value");
				}
				elseif($this->Work_order->get_custom_field($k,'type') == 'date')
				{
					$work_order_data["custom_field_{$k}_value"] = strtotime($this->input->post("custom_field_{$k}_value"));
				}
				elseif(isset($_FILES["custom_field_{$k}_value"]['tmp_name']) && $_FILES["custom_field_{$k}_value"]['tmp_name'])
				{
					if ($this->Work_order->get_custom_field($k,'type') == 'image')
					{
				    	$this->load->library('image_lib');
					
						$allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
						$extension = strtolower(pathinfo($_FILES["custom_field_{$k}_value"]['name'], PATHINFO_EXTENSION));
						if (in_array($extension, $allowed_extensions))
						{
							$config['image_library'] = 'gd2';
							$config['source_image']	= $_FILES["custom_field_{$k}_value"]['tmp_name'];
							$config['create_thumb'] = FALSE;
							$config['maintain_ratio'] = TRUE;
							$config['width']	 = 1200;
							$config['height']	= 900;
								$this->image_lib->initialize($config);
							$this->image_lib->resize();
							$this->load->model('Appfile');
							$image_file_id = $this->Appfile->save($_FILES["custom_field_{$k}_value"]['name'], file_get_contents($_FILES["custom_field_{$k}_value"]['tmp_name']));
								$work_order_data["custom_field_{$k}_value"] = $image_file_id;
						}
					} else {
						$this->load->model('Appfile');	
						$custom_file_id = $this->Appfile->save($_FILES["custom_field_{$k}_value"]['name'], file_get_contents($_FILES["custom_field_{$k}_value"]['tmp_name']));
						$work_order_data["custom_field_{$k}_value"] = $custom_file_id;	
					}
				}
				elseif($this->Work_order->get_custom_field($k,'type') != 'image' && $this->Work_order->get_custom_field($k,'type') != 'file')
				{
					$work_order_data["custom_field_{$k}_value"] = $this->input->post("custom_field_{$k}_value");
				}
			}
		}

		$this->Work_order->save( $work_order_data, $work_order_id );

		if (isset($_FILES['files']))
		{	$this->load->model('Appfile');
			for($k=0; $k<count($_FILES['files']['name']); $k++)
			{
				if($_FILES['files']['tmp_name'][$k])
				{
					$file_id = $this->Appfile->save($_FILES['files']['name'][$k], file_get_contents($_FILES['files']['tmp_name'][$k]));
					
					$this->Work_order->log_activity($work_order_id,lang('common_added_file').' '.$_FILES['files']['name'][$k]);
					
					$this->Work_order->add_file($work_order_id==-1 ? $work_order_data['id'] : $work_order_id, $file_id);
				}
			}
		}

		$status_id_to_change = $this->input->post('change_status');

		if($status_id_to_change){
			$this->change_status($work_order_id,$status_id_to_change);
		}
		
		if($this->config->item('edit_work_order_web_hook'))
		{
			$this->load->helper('webhook');
			$work_order_info = $this->Work_order->get_info($work_order_id)->row_array();
			$work_order_info['items'] = $this->Work_order->get_work_order_items($work_order_id);
			do_webhook($work_order_info,$this->config->item('edit_work_order_web_hook'));
		}
		
		echo json_encode(array('success'=>true, 'redirect' => $redirect ?? false));
		
	}

	function change_status($work_order_id,$status_id_to_change){
		
		$work_order_info = $this->Work_order->get_info($work_order_id)->row();
		$work_order_status_info = $this->Work_order->get_status_info($status_id_to_change);
		
		$update_data = array(
			'status'=>$status_id_to_change,
		);
		$this->Work_order->save($update_data,$work_order_id);

		if($work_order_status_info->notify_by_email || $work_order_status_info->notify_by_sms){
			$this->load->model('Common');
			$company_name = $this->config->item('company');
			$message = sprintf($this->lang->line('work_orders_work_order_status_update_message'), $company_name,$work_order_id,$work_order_status_info->description?$work_order_status_info->description:$work_order_status_info->name);

			// Render Status Template 
			$status_template = $this->status_email_template($status_id_to_change, $work_order_info, $work_order_status_info);

			if ($status_template) {
				$message =  nl2br($status_template['message']);
			}

			if($work_order_status_info->notify_by_email){
				$customer_email = $this->Customer->get_info($work_order_info->customer_id)->email;
				if($customer_email){
					$subject = lang('work_orders_work_order_status_update');

					$this->Common->send_email($customer_email,$subject,$message);
				}
			}

			if($work_order_status_info->notify_by_sms){
				$customer_phone_number = $this->Customer->get_info($work_order_info->customer_id)->phone_number;
				if($customer_phone_number){
					$this->Common->send_sms($customer_phone_number,$message);
				}
			}
		}

		return true;
	}
	
	function delete()
	{
		$this->check_action_permission('delete');
		$work_orders_to_delete=$this->input->post('ids');
		
		if($this->Work_order->delete_list($work_orders_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>lang('work_orders_successful_deleted').' '.
			count($work_orders_to_delete).' '.lang('work_orders_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('work_orders_cannot_be_deleted')));
		}
	}
	
	function undelete()
	{
		$this->check_action_permission('delete');
		$work_orders_to_delete=$this->input->post('ids');
		
		if($this->Work_order->undelete_list($work_orders_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>lang('work_orders_successful_undeleted').' '.
			count($work_orders_to_delete).' '.lang('work_orders_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('work_orders_cannot_be_undeleted')));
		}
	}
	
	
	function save_column_prefs()
	{
		$this->load->model('Employee_appconfig');
		
		if ($this->input->post('columns'))
		{
			$this->Employee_appconfig->save('work_orders_column_prefs',serialize($this->input->post('columns')));
		}
		else
		{
			$this->Employee_appconfig->delete('work_orders_column_prefs');			
		}
	}
	
	function reload_work_order_table()
	{
		
		$config['base_url'] = site_url('work_orders/sorting');
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$params = $this->session->userdata('work_orders_search_data') ? $this->session->userdata('work_orders_search_data') : array('offset' => 0, 'order_col' => 'id', 'order_dir' => 'desc', 'search' => FALSE,'deleted' => 0,'status' => '','technician' => '-1','hide_completed_work_orders' => $this->Employee_appconfig->get('hide_completed_work_orders'));

		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";		
		$data['status'] = $params['status'] ? $params['status'] : "";
		$data['technician'] = $params['technician'] ? $params['technician'] : "-1";
		$data['hide_completed_work_orders'] = $params['hide_completed_work_orders'] ? $params['hide_completed_work_orders'] : "";

		if ($data['search'] || $data['status'] || $data['technician']!=-1 || $data['hide_completed_work_orders'])
		{
			$config['total_rows'] = $this->Work_order->search_count_all($data['search'],$params['deleted'],10000,$data['status'],$data['technician'],$data['hide_completed_work_orders']);
			$table_data = $this->Work_order->search($data['search'],$params['deleted'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir'],$data['status'],$data['technician'],$data['hide_completed_work_orders']);
		}
		else
		{
			$config['total_rows'] = $this->Work_order->count_all($params['deleted']);
			$table_data = $this->Work_order->get_all($params['deleted'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		
		echo get_work_orders_manage_table($table_data,$this);
	}
			 
	function toggle_show_deleted($deleted=0)
	{
		$params = $this->session->userdata('work_orders_search_data') ? $this->session->userdata('work_orders_search_data') : array('offset' => 0, 'order_col' => 'id', 'order_dir' => 'desc', 'search' => FALSE,'deleted' => 0,'status' => '','technician' => '-1','hide_completed_work_orders' => $this->Employee_appconfig->get('hide_completed_work_orders'));
		
		$params['deleted'] = $deleted;
		$params['offset'] = 0;
		
		$this->session->set_userdata("work_orders_search_data",$params);
	}

	function custom_fields()
	{
		$this->lang->load('config');
		$fields_prefs = $this->config->item('work_order_custom_field_prefs') ? unserialize($this->config->item('work_order_custom_field_prefs')) : array();
		$data = array_merge(array('controller_name' => strtolower(get_class())),$fields_prefs);
		$locations_list = $this->Location->get_all()->result();
		$data['locations'] = $locations_list;
		$this->load->view('custom_fields',$data);
	}
	
	function save_custom_fields()
	{
		$this->load->model('Appconfig');
		$this->Appconfig->save('work_order_custom_field_prefs',serialize($this->input->post()));
	}

	function work_orders_status_change()
	{
		$work_order_ids=$this->input->post('work_order_ids');
		$status = $this->input->post('status');
		
		foreach($work_order_ids as $work_order_id)
		{
			$work_order_info = $this->Work_order->get_info($work_order_id)->row();
			if($work_order_info->status != $status){
				$this->change_status($work_order_id,$status);
			}
		}
		
		echo json_encode(array('success'=>true,'message'=>lang('work_orders_successful_changed')));
	}

	function print_work_order($work_order_ids)
	{	
		$result = array();

		$work_order_ids = explode('~', $work_order_ids);
		foreach($work_order_ids as $work_order_id)
		{

			$sale_id = $this->Work_order->get_info($work_order_id)->row()->sale_id;

			$sale_info = $this->Sale->get_info($sale_id)->row_array();
			$data['work_order_info'] = $this->Work_order->get_info($work_order_id)->row_array();
			$data['sale_info'] = $sale_info;

			$tier_id = $sale_info['tier_id'];
			$tier_info = $this->Tier->get_info($tier_id);
			$data['tier'] = $tier_info->name;
			$data['work_order_info'] = $this->Work_order->get_info($work_order_id)->row();
			
			$data['register_name'] = $this->Register->get_register_name($sale_info['register_id']);
			$data['override_location_id'] = $sale_info['location_id'];
			$data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($sale_info['sale_time']));
			$customer_id=$sale_info['customer_id'];
			
			$emp_info=$this->Employee->get_info($sale_info['employee_id']);
			$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;
			
			if($customer_id)
			{
				$cust_info=$this->Customer->get_info($customer_id);
				$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->account_number==''  ? '':' - '.$cust_info->account_number);
				$data['customer_company']= $cust_info->company_name;
				$data['customer_address_1'] = $cust_info->address_1;
				$data['customer_address_2'] = $cust_info->address_2;
				$data['customer_city'] = $cust_info->city;
				$data['customer_state'] = $cust_info->state;
				$data['customer_zip'] = $cust_info->zip;
				$data['customer_country'] = $cust_info->country;
				$data['customer_phone'] = format_phone_number($cust_info->phone_number);
				$data['customer_email'] = $cust_info->email;
			}
			else{
				$data['customer']='no_customer!';
				$data['customer_company']= '';
				$data['customer_address_1'] = '';
				$data['customer_address_2'] = '';
				$data['customer_city'] = '';
				$data['customer_state'] = '';
				$data['customer_zip'] = '';
				$data['customer_country'] = '';
				$data['customer_phone'] = '';
				$data['customer_email'] = '';
			}
			
			$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id;
			$data['sale_id_raw']=$sale_id;
			$data['comment']=$sale_info['comment'];
			$data['show_comment_on_receipt']=$sale_info['show_comment_on_receipt'];
			$data['sales_items'] = $this->Sale->get_sale_items_ordered_by_name($sale_id)->result_array();
			
			$data['sales_item_kits'] = $this->Sale->get_sale_item_kits_ordered_by_category($sale_id)->result_array();
			$data['discount_exists'] = $this->_does_discount_exists($data['sales_items']) || $this->_does_discount_exists($data['sales_item_kits']);
					
			$this->load->model('Delivery');
			$this->load->model('Person');
			
			$delivery = $this->Delivery->get_info_by_sale_id($sale_id);
			
			if($delivery->num_rows()==1)
			{
				$data['delivery_info'] = $delivery->row_array();			

				if(isset($data['delivery_info']['contact_preference'])){
					$data['delivery_info']['contact_preference'] = unserialize($data['delivery_info']['contact_preference']);
				}else{
					$data['delivery_info']['contact_preference'] = array();
				}
				
				$data['delivery_person_info'] = (array)$this->Person->get_info($this->Delivery->get_delivery_person_id($sale_id));
			}

			$result[] = $data;
		}
		
		$datas['datas'] = $result;
		$datas['sale_type'] = lang('common_workorder');
		
		$this->load->view("work_orders/print_work_order",$datas);
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

	function print_service_tag($work_order_ids)
	{
		$this->load->model('Item_taxes');
		$this->load->model('Item_location');
		$this->load->model('Item_location_taxes');
		$this->load->model('Item_taxes_finder');
		$this->load->helper('items');
		$this->load->helper('item_kits');
		
		$customers 				= array();
		$items_barcodes 		= array();
		$estimated_repair_date 	= '';

		

		foreach(explode('~',$work_order_ids) as $work_order_id){
			$item_ids = array();
			$customer_id = $this->Work_order->get_info($work_order_id)->row()->customer_id;
			$customer_info = $this->Customer->get_info($customer_id);
			// Get Work Order Detail
			$work_order_info = $this->Work_order->get_info($work_order_id)->row();
			
			$customer_name 	= $customer_info->first_name.' '.$customer_info->last_name;
			$customer_phone = format_phone_number($customer_info->phone_number);
			$customers[count($item_ids)] = array(
				'work_order_id' 	=> $work_order_id,
				'customer_name' 	=> $customer_name,
				'customer_phone' 	=> $customer_phone
			);

			$sale_id = $this->Work_order->get_info($work_order_id)->row()->sale_id;
			$barcode = lang('common_sale_id').' '.$sale_id;
			
			foreach($this->Work_order->get_work_order_items($work_order_id) as $key => $item)
			{
				if(isset($item['item_kit_id']))
				{
					$item_kit_ids[] = $item['item_kit_id'];
				} else {
					$item_ids[] 	= $item['item_id'];
				}
			}

			// If Store Config show_custom_fields_service_tag_work_orders 	
			$custom_fields 			= array();		
			if($this->config->item('show_custom_fields_service_tag_work_orders')) {
				
				$custom_field_labels 	= '';
				$work_order_info_object	= $this->Work_order->get_info($work_order_id)->row();
				

				for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++) {
					$custom_field_label = $this->Work_order->get_custom_field($k);
					if($custom_field_label !== FALSE && !empty($work_order_info_object->{"custom_field_${k}_value"})) {

						if($this->config->item('show_custom_fields_label_service_tag_work_orders')) { 
							$custom_field_labels = $custom_field_label.': ';
						}

						if($this->Work_order->get_custom_field($k,'type') != 'file' && $this->Work_order->get_custom_field($k,'type') != 'image') {
							if($this->Work_order->get_custom_field($k,'type') == 'date') {
								$convert_date  		= is_numeric($work_order_info_object->{"custom_field_${k}_value"}) ? date(get_date_format(), $work_order_info_object->{"custom_field_${k}_value"}) : '';
								$custom_fields[]  	= $custom_field_labels.$convert_date;
							} else {
								$custom_fields[]  	= $custom_field_labels.$work_order_info_object->{"custom_field_${k}_value"};
							}
						}
					}
				}
			}
			
			if($work_order_info->estimated_repair_date) {
				$estimated_repair_date =	date(get_date_format().' '.get_time_format(), strtotime($work_order_info->estimated_repair_date));
			}
			
			if (!empty($item_ids))
			{
				$items_barcodes = array_merge($items_barcodes, get_items_barcode_data(implode('~',$item_ids), $barcode, $custom_fields, $estimated_repair_date));
			}

			if (!empty($item_kit_ids))
			{
				$items_barcodes = array_merge($items_barcodes, get_item_kits_barcode_data(implode('~',$item_kit_ids), $barcode, $custom_fields, $estimated_repair_date));
			}
		}

		$data = array();
				
		$data['customers'] = $customers;
		$data['items'] = $items_barcodes;
		$data['selected_ids'] = $work_order_ids;
		$data['excel_url'] = site_url('work_orders/print_service_tag_excel/'.$work_order_ids );
		$data['font_enlarge'] = true;

		$this->load->view("barcode_labels", $data);
	}

	function get_items_raw_print_service_tag($work_order_ids)
	{
		
		$this->load->model('Item_variations');

		$result = array();
		$item_ids = array();

		foreach(explode('~',$work_order_ids) as $work_order_id){
			$data = $this->Work_order->get_raw_print_data($work_order_id);
			foreach($data as $key => $row){
				$data[$key]['cost_code'] = to_cost_code($row['cost_price']);
			}
			$result = array_merge($result,$data);
		}
		
		return $result;
	}

	function raw_print_service_tag($work_order_ids)
	{				
		$this->load->model('Label');

		$data['datas'] = $this->get_items_raw_print_service_tag($work_order_ids);
		$data['selected_ids'] = $work_order_ids;		
		
		$data['label_name'] = $this->Label->get_all();
		$data['raw_is'] = 'work_orders';

		$this->load->model('Employee_appconfig');
		$data['saved_label'] = $this->Employee_appconfig->get('work_orders_label') ? unserialize($this->Employee_appconfig->get('work_orders_label')) : array();


		$this->load->view("raw_print", $data);
	}

	
	function print_service_tag_excel($work_order_ids)
	{

		$this->load->helper('items');
		$export_data[] = array(lang('common_sale_id'), lang('common_item_number'),lang('common_name'), lang('common_description'),lang('common_unit_price'));

		foreach(explode('~',$work_order_ids) as $work_order_id){
			$item_ids = array();
			$items_barcodes = array();

			$sale_id = $this->Work_order->get_info($work_order_id)->row()->sale_id;
			$barcode = lang('common_sale_id').' '.$sale_id;

			foreach($this->Sale->get_sale_items($sale_id)->result() as $item){
				$item_ids[] = $item->item_id;
			}

			if (!empty($item_ids)){
				$items_barcodes = get_items_barcode_data(implode('~',$item_ids));
			}

			foreach($items_barcodes as $row){
				$data = trim(strip_tags($row['name']));
				$price = substr($data,0,strpos($data,' '));
				$name = str_replace($price.' ','',$data);
				$description = $row['description'];
				$export_data[] = array($barcode, $row['id'], $name, $description, $price);
			}
		}
		
		$this->load->helper('spreadsheet');
		array_to_spreadsheet($export_data,'barcode_export.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'));
	}

	function _excel_get_header_row()
	{
		$return = array(lang('common_sale_id'),lang('work_orders_date'),lang('common_status'),lang('common_first_name'),lang('common_last_name'),lang('common_address'),lang('common_city'),lang('common_state'),lang('common_zip'),lang('common_email'),lang('common_phone_number'),lang('work_orders_work_order_id'));
		return $return;
	}

	function excel_export_selected_rows($ids) {
		ini_set('memory_limit','1024M');
		set_time_limit(0);
		ini_set('max_input_time','-1');

		$this->load->helper('report');
		$rows = array();
		$header_row = $this->_excel_get_header_row();
		$rows[] = $header_row;
		
		$ids = explode('~', $ids);
		foreach ($ids as $id)
		{
			$r = $this->Work_order->get_by_id($id);

			$row = array(
				$r->sale_id,
				date_time_to_date($r->sale_time),
				work_order_status($r->status),
				$r->first_name,
				$r->last_name,
				$r->full_address,
				$r->city,
				$r->state,
				$r->zip,
				$r->email,
				$r->phone_number,
				$r->id,
			);
			
			$rows[] = $row;
		}
		$this->load->helper('spreadsheet');
		array_to_spreadsheet($rows,'work_orders_export.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'));
	}

	function save_repaired_item_notes()
	{		
		$data = array();
		
		$line = 0;
		$item_id_being_repaired = $this->input->post('item_id_being_repaired');
		$sale_id = $this->input->post('sale_id');
		$sale_item_note = $this->input->post('sale_item_note');
		$sale_item_detailed_notes = $this->input->post('sale_item_detailed_notes');
		$sale_item_note_internal = $this->input->post('sale_item_note_internal') ? 1 : 0;
		$note_id = $this->input->post('note_id');
		$device_location = $this->input->post('device_location');
		$status_id = $this->input->post('status_id');

		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;

		$sales_items_notes_data = array
		(
			'sale_id'=>$sale_id,
			'item_id'=>$item_id_being_repaired,
			'line'=>$line,
			'note'=>$sale_item_note,
			'detailed_notes'=>$sale_item_detailed_notes,
			'internal'=>$sale_item_note_internal,
			'employee_id'=>$employee_id,
			'images'=>serialize(array()),
			'device_location'=>$device_location,
			'status'=>$status_id,
			'note_timestamp' => date('Y-m-d H:i:s')
		);
		
		$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
		$work_order_id = $work_order_info['id'];
		
		if ($work_order_id)
		{
			$this->Work_order->log_activity($work_order_id,lang('common_added_note').' '.$sale_item_note);
		}
		$this->Sale->save_sales_items_notes_data($sales_items_notes_data,$note_id);

		if($status_id){
			$this->change_status($work_order_id,$status_id);
		}
	}
	
	function workorder_images_upload(){
		$work_order_id = $this->input->post('work_order_id');
		$work_order_info = $this->Work_order->get_info($work_order_id)->row();

		$exists_images = $work_order_info->images ? unserialize($work_order_info->images) : array();
		$new_images = array();
		
		foreach($_FILES['file']['tmp_name'] as $key => $value) {
			$tempFile = $_FILES['file']['tmp_name'][$key];
			$fileName =  $_FILES['file']['name'][$key];
		    $image_file_id = $this->Appfile->save($fileName, file_get_contents($tempFile));
			$new_images[] = $image_file_id;
		}

		$images = array_merge($exists_images, $new_images);

		$images_data = array(
			'images'=>serialize($images),
		);
		
		$this->Work_order->save($images_data,$work_order_id);
	}

	function delete_work_order_image()
	{
		$work_order_id = $this->input->post('work_order_id');
		$image_index = $this->input->post('image_index');
		
		$work_order_info = $this->Work_order->get_info($work_order_id)->row();
		$images = $work_order_info->images ? unserialize($work_order_info->images) : array();
		
		$this->Appfile->delete($images[$image_index]);
		unset($images[$image_index]);
		$images_data = array(
			'images'=>serialize(array_values($images)),
		);
		
		$this->Work_order->save($images_data,$work_order_id);

		echo json_encode(array('success'=>true,'message'=>lang('work_orders_successful_deleted')));
	}

	function item_search()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		if(!$this->config->item('speed_up_search_queries'))
		{
			$suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'),0,'unit_price',100,'sales');
			$suggestions = array_merge($suggestions, $this->Item_kit->get_item_kit_search_suggestions_sales_recv($this->input->get('term'),0,'unit_price', 100));
		}
		else
		{
			$suggestions = $this->Item->get_item_search_suggestions_without_variations($this->input->get('term'),0,100,'unit_price');
			$suggestions = array_merge($suggestions, $this->Item_kit->get_item_kit_search_suggestions_sales_recv($this->input->get('term'),0,'unit_price', 100));

			for($k=0;$k<count($suggestions);$k++)
			{
				if(isset($suggestions[$k]['avatar']))
				{
					$suggestions[$k]['image'] = $suggestions[$k]['avatar'];
				}

				if(isset($suggestions[$k]['subtitle']))
				{
					$suggestions[$k]['category'] = $suggestions[$k]['subtitle'];
				}
			}
		}
		
		//Lookup by item id
		if ($item_id = $this->Item->lookup_item_id($this->input->get('term'),array('item_number','item_variation_item_number','product_id','additional_item_numbers','serial_numbers')))
		{
			$item_info = $this->Item->get_info($item_id);
			$suggestions[]=array('value'=> $item_id, 'label' => $item_info->name, 'image' =>  $item_info->main_image_id ?  cacheable_app_file_url($item_info->main_image_id) : base_url()."assets/img/item.png", 'subtitle' => '');		
		}

		if(empty($suggestions) && $this->Item->get_item_id(lang('work_orders_repair_item'))){
			$suggestions[]=array('value'=> $this->Item->get_item_id(lang('work_orders_repair_item')), 'label' => lang('items_item_not_found'), 'image' => base_url()."assets/img/item.png", 'subtitle' => lang('items_add_as_repair_item').' '.lang('common_or').' '.lang('items_press_enter_to_contine_search_in_other_venders'));
		}

		if(empty($suggestions)){
			$suggestions[]=array('value'=> false, 'label' => lang('items_item_not_found'), 'image' => base_url()."assets/img/item.png", 'subtitle' => lang('items_add_as_repair_item').' '.lang('common_or').' '.lang('items_press_enter_to_contine_search_in_other_venders'));
		}
		
		echo json_encode(H($suggestions));
	}

	function delete_item($sale_id,$line){

		$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
		$work_order_id = $work_order_info['id'];
		
		if ($work_order_id)
		{
			$sale_item_info = $this->Sale->get_sale_item_info_by_sale_id_and_line($sale_id,$line);
			$item_name = $this->Item->get_info($sale_item_info->item_id)->name;
			$this->Work_order->log_activity($work_order_id,$item_name.' '.lang('common_removed_from_work_order'));
		}

		$this->Work_order->delete_item($sale_id,$line);
		
		$this->Sale->update_sale_statistics($sale_id);
	}


	function add_sale_item(){
		//item_id = item ID or KIT #
		$item_id = $barcode_scan_data = $this->input->post('item');
		$sale_id = $this->input->post('sale_id');
		$item_identifier = $this->input->post('item_identifier');
		
		if($this->is_valid_receipt($barcode_scan_data)){
			$pieces = explode(' ',$barcode_scan_data);
			echo json_encode(["redirect" => site_url('sales/unsuspend/'.$pieces[1])]);
			return false;
		}

		// Validate Item Kit 
		if($this->is_valid_item_kit($barcode_scan_data)) {
			// Verify if "kit" exists in $barcode_scan_data
			if (strpos(strtolower($barcode_scan_data), 'kit') !== FALSE) {
				// if "repair_item" exists in $barcode_scan_data then add as repair item kit
				if($item_identifier === 'repair_item') {
					$this->add_sale_item_kits($sale_id, $barcode_scan_data, 1);
				} else {
					
					// Explode $barcode_scan_data to get item_kit_id and Get item_kit_info 
					$pieces 		= explode(' ',$barcode_scan_data);
					$item_kit_info 	= $this->Item_kit->get_info((int)$pieces[1]);
					// Validate if cost_price and unit_price are not empty 
					if($item_kit_info->cost_price > 0 AND $item_kit_info->unit_price > 0) {
						$this->add_sale_item_kits($sale_id, $barcode_scan_data, 0);
					} else { 
						$this->add_sale_item_kit($sale_id, $barcode_scan_data, 0);
					}
				}
				return true;
			}
			return false;
		}

		$is_serialnumber = false;
		if($temp_item_id = $this->Item_serial_number->get_item_id($barcode_scan_data) && $this->is_valid_item($barcode_scan_data)){
			$item_id = $temp_item_id;
			
			$items = $this->Sale->get_sale_items($sale_id)->result_array();

			if(count($items) > 0){
				foreach($items as $item){
					if($item['serialnumber'] && ($item['serialnumber'] == $barcode_scan_data)){
						echo json_encode(array('success'=>false,'message'=>lang('common_serialnumber_duplicate')));
						return;
					}
				}
			}
			$is_serialnumber = true;
		}

		if($item_identifier === 'repair_item') {
			$exist_sale_item = $this->Sale->get_sale_item($sale_id,$item_id);
		} else {
			$exist_sale_item = $this->Sale->get_sale_item($sale_id,$item_id, 0, 1);
		}
		
		$repair_item_id = $this->work_order->create_or_update_repair_item();

		if(!$is_serialnumber && $exist_sale_item && $item_id != $repair_item_id){
			$this->Sale->sale_item_quantity_update($sale_id,$item_id,$exist_sale_item->quantity_purchased+1);
			$return = array('success'=>true);
		} else {
			
			if($this->Sale->add_sale_item($sale_id, $item_id, 1, $barcode_scan_data, $item_identifier == 'repair_item' ? 1 : 0)){
				
				$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
				$work_order_id = $work_order_info['id'];
				
				if ($work_order_id)
				{
					$item_name = $this->Item->get_info($item_id)->name;
					$this->Work_order->log_activity($work_order_id,$item_name.' '.lang('common_added_to_work_order'));
				}
				
				$return = array('success'=>true);
			}
			else{
				$return = array('success'=>false);
			}
		}

		if($return['success'])
		{
				$qty_buy = 1;
				$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
				$location_id = $this->Employee->get_logged_in_employee_current_location_id();

				$variation_id = NULL;

				if (($item_identifer_parts = explode('#', $item_id)) !== false)
				{
					if (isset($item_identifer_parts[1]))
					{
						$item_id = $item_identifer_parts[0];
						$variation_id = $item_identifer_parts[1];
					}
				}

			}

		echo json_encode($return);

	}

	function add_but_not_save($item_id=false){
		$barcode_scan_data = $item_id ? $item_id : $this->input->post("item");
		$items = $this->session->userdata('items_for_new_work_order') ? $this->session->userdata('items_for_new_work_order') : array();

		if($this->is_valid_item_kit($barcode_scan_data)){
			$pieces = explode(' ',$barcode_scan_data);
			$item_kit_id = (int)$pieces[1];
			$item_kit_info = $this->Item_kit->get_info($item_kit_id);

			if($item_kit_info->item_kit_id){
				$model = '';
				$description = '';
				foreach($this->Item_kit_items->get_info($item_kit_id) as $key => $item){
					$selected_item_id = $item->item_id;
					$item_info = $this->Item->get_info($item->item_id);
					$model = $item_info->name;
					$description = $item_info->description;
					$new_item = array(
						'description' => $description,
						'serial_number'=>'',
						'model'=> $model,
						'item_id' => $selected_item_id,
						'item_variation_id' => null,
						'is_serialized' => $item_info->is_serialized,
						'quantity' => $item->quantity,
						'is_repair_item' => 1
					);
			 
					$items[] = $new_item;
				};
	
				$this->session->set_userdata('items_for_new_work_order', $items);
			}
		}else if($item_id = $this->Item_serial_number->get_item_id($barcode_scan_data) && $this->is_valid_item($barcode_scan_data)){

			if(count($items) > 0){
				foreach($items as $item){
					if($item['serial_number'] && ($item['serial_number'] == $barcode_scan_data)){
						echo json_encode(array('success'=>true,'message'=>lang('common_serialnumber_duplicate')));
						return;
					}
				}
			}

			$item_info = $this->Item->get_info($item_id);
			$item_info = array(
				'description'=>$item_info->description,
				'serial_number'=>$barcode_scan_data,
				'model'=>$item_info->name,
				'item_id' => $item_id,
				'item_variation_id' =>null,
				'is_serialized' => $item_info->is_serialized,
				'quantity' => 1,
				'is_repair_item' => 1
			);

			$items[] = $item_info;

			$this->session->set_userdata('items_for_new_work_order', $items);
			
		}else if(strpos($barcode_scan_data, '#') !== false){
			list($item_id, $item_variation_id) = explode('#', $barcode_scan_data);
			if($this->item->exists($item_id)){
				$item_info = $this->item->get_info($item_id);
				$new_item = array(
					'description'=>$item_info->description,
					'serial_number'=>'',
					'model'=>$item_info->name,
					'item_id' => $item_id,
					'item_variation_id' => $item_variation_id,
					'is_serialized' => $item_info->is_serialized,
					'quantity' => 1,
					'is_repair_item' => 1
				);
		
				$items[] = $new_item;
				$this->session->set_userdata('items_for_new_work_order', $items);
			}
		}else if($this->Item->exists($barcode_scan_data)){
			$item_info = $this->Item->get_info($barcode_scan_data, false);

			$new_item = array(
				'description'=>$item_info->description,
				'serial_number'=>'',
				'model'=>$item_info->name,
				'item_id' => $barcode_scan_data,
				'item_variation_id' => null,
				'is_serialized' => $item_info->is_serialized,
				'quantity' => 1,
				'is_repair_item' => 1
			);
	
			$items[] = $new_item;

			$this->session->set_userdata('items_for_new_work_order', $items);
		}

		echo json_encode(array('item_info'=> $items));
		return;
	}

	function select_item(){
		$item_id = $scan = $this->input->post('item_id');

		$item_variation_id = null;

		if(strpos('#', $item_id)){
			$pieces = explode('#',$scan);
			$item_variation_id = (int)$pieces[1];
		}

		$item_id = strstr($item_id, '#', true) ? strstr($item_id, '#', true) : $item_id;
		
		if(!$item_id){
			$item_id = $this->input->post("item");
		}

		if($this->Item->exists($item_id)){
			$item_info = $this->Item->get_info($item_id);
			$item_info->item_variation_id = $item_variation_id;
		} else if($this->is_valid_item_kit($scan)){
			$pieces = explode(' ',$scan);
			$item_info = $this->Item_kit->get_info((int)$pieces[1]);
		}else{
			$item_info = $this->Item->get_info($item_id);
			$item_info->item_variation_id = $item_variation_id;
		}

		$items = $this->session->userdata('items_for_new_work_order') ? $this->session->userdata('items_for_new_work_order') : array();
		
		echo json_encode(array('item_info'=>$item_info, 'total_item' => count($items)));
	}

	function add_item(){
		$description 	= $this->input->post('description');
		$serial_number 	= $this->input->post('serial_number');
		$model 			= $this->input->post('model');
		$item_id 		= $this->input->post('item_id');
		$item_kit_id 	= $this->input->post('item_kit_id');
		$is_serialized 	= $this->input->post('is_serialized') ? 1 : 0;
		
		$items = $this->session->userdata('items_for_new_work_order') ? $this->session->userdata('items_for_new_work_order') : array();

		if($item_kit_id){
			// Get Item Kit Info
			$item_kit_info 			= $this->Item_kit->get_info($item_kit_id);
			// if Item Kit Cost Price and Unit Price is not null
			if($item_kit_info->cost_price != '0.00' && $item_kit_info->unit_price != '0.00') {
				$new_item = array(
					'description'		=>	$item_kit_info->description,
					'serial_number'		=>	'',
					'model'				=>	$model,
					'item_id' 			=> 	$item_kit_id,
					'item_variation_id' => 	null,
					'is_serialized' 	=> 	$is_serialized,
					'quantity' 			=> 	1,
					'cost_price' 		=> 	$item_kit_info->cost_price,
					'unit_price' 		=> 	$item_kit_info->unit_price,
					'is_item_kit' 		=> 	1,
				);
				$items[] = $new_item;
			} else {
				// Get Item Kit Items
				foreach($this->Item_kit_items->get_info($item_kit_id) as $key => $item){
					$selected_item_id 	= $item->item_id;
					$item_info 			= $this->Item->get_info($item->item_id);
					$selected_item_variation_id =  null;
					$new_item = array(
						'description'		=>	$item_info->description,
						'serial_number'		=>	'',
						'model'				=>	$model,
						'item_id' 			=> 	$selected_item_id,
						'item_variation_id' => 	$selected_item_variation_id,
						'is_serialized' 	=> 	$is_serialized,
						'quantity' 			=> 	$item->quantity,
						'is_item_kit' 		=> 	0,
					);
			
					$items[] = $new_item;
				};
			}

			$this->session->set_userdata('items_for_new_work_order', $items);
	
			echo json_encode(array('success'=>true, 'model' => $model, 'description' => $description ));
			return;
		}

		$selected_item = "";
		$serial_number_item_id = false;



		if(!empty($serial_number)){

			if ($item_id_from_serial_number = $this->Item_serial_number->get_item_id($serial_number))
			{
				
				$variation_id_from_serial_number = $this->Item_serial_number->get_variation_id($serial_number);
				$serial_number_item_id = $item_id_from_serial_number. ($variation_id_from_serial_number ? '#'.$variation_id_from_serial_number : '');

				$item_info = $this->Item->get_info($serial_number_item_id);
				$description = $item_info->description;
				$model = $item_info->name;

				if(count($items) > 0){
					foreach($items as $item){
						if($item['serial_number'] && ($item['serial_number'] == $serial_number)){
							echo json_encode(array('success'=>false,'message'=>lang('common_serialnumber_duplicate')));
							return;
						}
					}
				}
				$selected_item = $serial_number_item_id;
		
			}else{
				echo json_encode(array('success'=>false,'message'=>lang('common_serialnumber_not_found')));
				return;
			}
		}else{
			$selected_item = $item_id;
		}

		$ids = explode("#", $selected_item);
		$selected_item_id = $ids[0];
		$selected_item_variation_id = (count($ids) >= 2 ? $ids[1] : null ) ;
		$new_item = array(
			'description'=>$description,
			'serial_number'=>$serial_number,
			'model'=>$model,
			'item_id' => $selected_item_id,
			'item_variation_id' => $selected_item_variation_id,
			'is_serialized' => $is_serialized,
			'quantity' => 1
		);

		$items[] = $new_item;
		$this->session->set_userdata('items_for_new_work_order', $items);

		echo json_encode(array('success'=>true, 'model' => $model, 'description' => $description ));
	}

	function add_sale_item_kit($sale_id, $scan_data){
		$return = array('success'=>false,'message'=>lang('work_orders_unable_to_add_item'));
		if($this->is_valid_item_kit($scan_data)) {
			if (strpos(strtolower($scan_data), 'kit') !== FALSE){
				//KIT #
				$pieces = explode(' ',$scan_data);
				
				//We call the lookup function so it can pass though item banning
				$item_kit_to_add = $this->Item_kit->get_info((int)$pieces[1]);
		
				if($item_kit_to_add->item_kit_id){
					$item_kit_item_kits = $this->Item_kit_items->get_info($item_kit_to_add->item_kit_id);

					foreach($item_kit_item_kits as $row){
						$exist_sale_item = $this->Sale->get_sale_item($sale_id, $row->item_id);
						if($exist_sale_item){
							$this->Sale->sale_item_quantity_update($sale_id,$row->item_id,$exist_sale_item->quantity_purchased+$row->quantity);
						} else{
							if($this->Sale->add_sale_item($sale_id, $row->item_id, $row->quantity)){
								$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
								$work_order_id = $work_order_info['id'];
								if ($work_order_id) {
									$item_name = $this->Item->get_info($row->item_id)->name;
									$this->Work_order->log_activity($work_order_id,$item_name.' '.lang('common_added_to_work_order'));
								}
							}
						}
					}
					$return = array('success'=>true);
				}else{
					$return = array('success'=>false,'message'=>lang('work_orders_unable_to_add_item'));
				}
			}
		}

		echo json_encode($return);
	}

	// Add Item Kits to Work Order by Item Kit ID 
	function add_sale_item_kits($sale_id, $scan_data, $is_repair = null){
		$return = array('success'=>false,'message'=>lang('work_orders_unable_to_add_item'));
		if($this->is_valid_item_kit($scan_data)) {
			if (strpos(strtolower($scan_data), 'kit') !== FALSE){
				//KIT #
				$pieces 	= explode(' ',$scan_data);
				$quantity 	= 1;
				//We call the lookup function so it can pass though item banning
				$item_kit_to_add 	= $this->Item_kit->get_info((int)$pieces[1]);
				$item_kit_id 		= $item_kit_to_add->item_kit_id;
				if($item_kit_to_add->item_kit_id){
					
					
					$exist_sale_item = $this->Sale->get_item_kits_sale($sale_id, $item_kit_id, $is_repair)->row();
					if($exist_sale_item){
						$this->Sale->sale_item_kit_quantity_update($sale_id,$item_kit_id,$exist_sale_item->quantity_purchased + $quantity);
					} else {
						if($this->Sale->add_sale_item_kit($sale_id, $item_kit_id, 1, $is_repair)){
							$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
							$work_order_id = $work_order_info['id'];
							if ($work_order_id) {
								$item_name = $this->Item_kit->get_info($item_kit_id)->name;
								$this->Work_order->log_activity($work_order_id,$item_name.' '.lang('common_added_to_work_order'));
							}
						}
					}
					$return = array('success'=>true);
				}else{
					$return = array('success'=>false,'message'=>lang('work_orders_unable_to_add_item'));
				}
			}
		}

		echo json_encode($return);
	}


	function is_valid_item_kit($item_kit_id){
		$pieces = explode(' ',$item_kit_id);

		if(count($pieces)==2 && strtolower($pieces[0]) == 'kit'){
			return $this->Item_kit->exists($pieces[1]);
		} else {
			return $this->Item_kit->get_item_kit_id($item_kit_id) !== FALSE;
		}
	}

	function edit_sale_item_quantity($sale_id, $item_id, $line, $item_variation_id=false, $is_item_kit = false) {

		if($is_item_kit){
			$item_name 	= $this->Item_kit->get_info($item_id)->name;
			$sale_item 	= $this->Sale->get_sale_item_kits($sale_id,$line, 0)->row();
			$oldvalue 	= to_quantity($sale_item->quantity_purchased);
		} else {
			$item_name 	= $this->Item->get_info($item_id)->name;
			$sale_item 	= $this->Sale->get_sale_item($sale_id,$item_id,$line, 0);
			$oldvalue 	= to_quantity($sale_item->quantity_purchased);
		}

		if($item_variation_id){
			$item_id = $item_id.'#'.$item_variation_id;
		}

		$quantity_purchased = $this->input->post("value");

		if($is_item_kit) {
			$this->Sale->sale_item_kit_quantity_update($sale_id, $line, $quantity_purchased);
		} else {
			$this->Sale->sale_item_quantity_update($sale_id, $item_id, $quantity_purchased, $line);
		}

		$qty_buy = $quantity-$sale_item_info->quantity_purchased;
		$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();

		$variation_id = NULL;

		if (($item_identifer_parts = explode('#', $item_id)) !== false)
		{
			if (isset($item_identifer_parts[1]))
			{
				$item_id = $item_identifer_parts[0];
				$variation_id = $item_identifer_parts[1];
			}
		}
	}

	function edit_sale_item_unit_price($sale_id,$item_id,$item_variation_id=false, $line, $is_item_kit = false)
	{
		
		if($is_item_kit){
			$item_name 	= $this->Item_kit->get_info($item_id)->name;
			$sale_item 	= $this->Sale->get_sale_item_kits($sale_id,$line)->row();
			$oldvalue 	= $sale_item->item_kit_unit_price ?? '0.00';
		}else{
			$item_name 	= $this->Item->get_info($item_id)->name;
			$sale_item 	= $this->Sale->get_sale_item($sale_id,$item_id,$line);
			$oldvalue 	= $sale_item->item_unit_price;
		}
		

		if($item_variation_id){
			$item_id = $item_id.'#'.$item_variation_id;
		}
		$unit_price = $this->input->post("value");

		if($is_item_kit) {
			$this->Sale->sale_item_kit_unit_price_update($sale_id,$sale_item->item_kit_id,$line,$unit_price);
		} else {
			$this->Sale->sale_item_unit_price_update($sale_id,$item_id,$line,$unit_price + $this->Work_order->get_modifiers_unit_total($sale_id, $item_id));
		}
		
		
		$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
		$work_order_id = $work_order_info['id'];
		
		if ($work_order_id)
		{
			$this->Work_order->log_activity($work_order_id,$item_name.' [field]unit_price[/field] '.lang('common_changed').' '.lang('common_from').' [oldvalue]'.$oldvalue.'[/oldvalue] '.lang('common_to').' [newvalue]'.$unit_price.'[/newvalue]');
		}
		
	}

	function select_technician(){
		$work_order_id = $this->input->post('work_order_id');
		$employee_id = $this->input->post('employee_id');
		
		$data = array('employee_id'=>$employee_id);
		
		$this->Work_order->save($data,$work_order_id);

		$technician_info = $this->Employee->get_info($employee_id);

		if($this->config->item('notify_technician_via_email') || $this->config->item('notify_technician_via_sms')){
			$this->load->model('Common');

			if($this->config->item('notify_technician_via_email')){
				$technician_email = $this->Employee->get_info($employee_id)->email;
				if($technician_email){
					$subject = lang('work_orders_you_have_been_assigned_a_work_order');
					$message = lang('work_orders_you_have_been_assigned_work_order').': ';
					$message .= '<a href="'.site_url("work_orders/view/".$work_order_id).'" >'.$work_order_id.'</a>';

					$this->Common->send_email($technician_email,$subject,$message);
				}
			}

			if($this->config->item('notify_technician_via_sms')){
				$technician_phone_number = $technician_info->phone_number;
				if($technician_phone_number){
					$message = lang('work_orders_you_have_been_assigned_work_order').': '.$work_order_id."\n";
					$message .= site_url('work_orders/view/').$work_order_id;
					$this->Common->send_sms($technician_phone_number,$message);
				}
			}
		}
	}

	function remove_technician(){
		$work_order_id = $this->input->post('work_order_id');
		
		$data = array('employee_id'=>NULL);
		
		$this->Work_order->save($data,$work_order_id);

	}

	function manage_statuses()
	{
		$this->check_action_permission('manage_statuses');
		$statuses = $this->Work_order->get_all_statuses();
		$data = array('statuses' => $statuses, 'statuses_list' => $this->_statuses_list());
		
		$data['redirect'] = $this->input->get('redirect');
		
		$this->load->view('work_orders/manage_statuses',$data);		
	
	}

	function save_status($status_id = FALSE)
	{
		$this->check_action_permission('manage_statuses');
		$status_name = $this->input->post('status_name');
		$status_color = $this->input->post('status_color');
		$status_sort_order = $this->input->post('status_sort_order');

		$status_data = array(
			'name'=> $status_name,
			'description'=> $this->input->post('status_description'),
			'notify_by_email'=> $this->input->post('notify_by_email') ? 1 : 0,
			'notify_by_sms'=> $this->input->post('notify_by_sms') ? 1 : 0,
			'color'=> $status_color,
			'sort_order'=> $status_sort_order,
		);
		
		if ($this->Work_order->status_save($status_data, $status_id))
		{
			echo json_encode(array('success'=>true,'message'=>lang('work_orders_status_successful_adding').' '.H($status_name)));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('work_orders_status_successful_error')));
		}
	
	}
	
	function delete_status()
	{
		$this->check_action_permission('manage_statuses');
		$status_id = $this->input->post('status_id');
		if($this->Work_order->delete_status($status_id))
		{
			echo json_encode(array('success'=>true,'message'=>lang('work_orders_successful_deleted')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('work_orders_cannot_be_deleted')));
		}
		
	}

	function statuses_list()
	{
		echo $this->_statuses_list();
	}
	
	function _statuses_list()
	{
		$statuses = $this->Work_order->get_all_statuses();
     	$return = '<ul>';
		foreach($statuses as $status_id => $status) 
		{
			$return .='<li>'.H($status['name']).
			'<a href="javascript:void(0);" class="edit_status" data-name = "'.H($status['name']).'" data-description = "'.H($status['description']).'" data-notify_by_email = "'.H($status['notify_by_email']).'" data-notify_by_sms = "'.H($status['notify_by_sms']).'" data-color = "'.H($status['color']).'" data-sort_order = "'.H($status['sort_order']).'" data-status_id="'.$status_id.'">['.lang('common_edit').']</a> '.
			'<a href="javascript:void(0);" class="delete_status" data-status_id="'.$status_id.'">['.lang('common_delete').']</a> ';
	 		$return .='</li>';
		}
     	$return .='</ul>';
		
		return $return;
	}

	function manage_checkboxes()
	{	
		$checkboxes_pre = $this->Work_order->get_all_checkboxes(1);
		$data = array('checkboxes_pre' => $checkboxes_pre, 'checkboxes_pre_list' => $this->_checkboxes_list(1));
		$checkboxes_post = $this->Work_order->get_all_checkboxes(2);
		$data += array('checkboxes_post' => $checkboxes_post, 'checkboxes_post_list' => $this->_checkboxes_list(2));
		
		$data['redirect'] = $this->input->get('redirect');
		$data['checkbox_groups'] = $this->Work_order->get_checkbox_groups();
		$this->load->view('work_orders/manage_checkboxes',$data);
	}

	function checkbox_group($group_id = NULL)
	{
		$data['group_info'] = $this->Work_order->get_checkbox_group_info($group_id);
		$data['pre_checkboxes'] = $this->Work_order->get_all_checkboxes($group_id, 1);
		$data['post_checkboxes'] = $this->Work_order->get_all_checkboxes($group_id, 2);
		$this->load->view('work_orders/checkbox_group', $data);
	}

	function save_checkbox($group_id = FALSE)
	{
		$group_name = $this->input->post('group_name');
		$sort_order = $this->input->post('group_sort_order');
		$pre_checkboxes = $this->input->post('pre_checkbox_items');
		$post_checkboxes = $this->input->post('post_checkbox_items');
		$checkbox_items_to_delete = $this->input->post('checkbox_items_to_delete');

		$checkbox_data = array(
			'group_name'=> $group_name,
			'sort_order'=> $sort_order,
			'pre_checkboxes'=> $pre_checkboxes,
			'post_checkboxes'=> $post_checkboxes,
			'checkbox_items_to_delete' => $checkbox_items_to_delete
		);

		$this->Work_order->save_checkbox($group_id, $checkbox_data);

		redirect(site_url('work_orders/manage_checkboxes'));
	}

	function delete_checkbox()
	{
		$group_id = $this->input->post('group_id');
		if($this->Work_order->delete_checkbox($group_id)) {
			echo json_encode(array('success'=>true,'message'=>lang('work_orders_successful_deleted')));
		} else {
			echo json_encode(array('success'=>false,'message'=>lang('work_orders_cannot_be_deleted')));
		}
	}

	function checkboxes_list($type = 0)
	{
		echo $this->_checkboxes_list($type);
	}

	function _checkboxes_list($type = 0)
	{
		$return = "";
		if($type > 0){
			$checkboxes = $this->Work_order->get_all_checkboxes($type);
			$return = '<ul>';
			foreach($checkboxes as $checkbox_id => $checkbox) 
			{
				$return .='<li>'.H($checkbox['name']).
						'<a href="javascript:void(0);" class="edit_checkbox" data-type="'.$type.'" data-name = "'.H($checkbox['name']).'" data-description = "'.H($checkbox['description']).'" data-sort_order = "'.H($checkbox['sort_order']).'" data-checkbox_id="'.$checkbox_id.'">['.lang('common_edit').']</a> '.
						'<a href="javascript:void(0);" class="delete_checkbox" data-checkbox_id="'.$checkbox_id.'">['.lang('common_delete').']</a> ';
				 $return .='</li>';
			}
			$return .='</ul>';
		}else if($type == 0){
			$return = "<p> - ".lang('work_orders_pre')." </p>";
			$return .= $this->_checkboxes_list(1);
			$return .= "<p> - ".lang('work_orders_post')." </p>";
			$return .= $this->_checkboxes_list(2);
		}
		return $return;
	}

	function set_checkbox(){
		$checkbox_ids 			= $this->input->post('checkbox_ids');
		$workorder_id 			= $this->input->post('workorder_id');
		
		// Get Data from DB to compare with new data to get added and deleted items ids
		$checkboxes_states 		= $this->Work_order->get_checkboxes_states($workorder_id);
		$previous_checkbox_ids	= array_column($checkboxes_states, 'checkbox_id');
		$added_checkbox_ids 	= array_diff($checkbox_ids, $previous_checkbox_ids);
		$deleted_checkbox_ids 	= array_diff($previous_checkbox_ids, $checkbox_ids);

		// Log activity for added
		foreach ($checkbox_ids as $key => $row) {
			if (in_array($row, $added_checkbox_ids)) {
				$this->checkbox_groups_log_activity($row, $workorder_id, 'add');
			}
		}
		
		// Log activity for deleted
		foreach ($checkboxes_states as $key => $row) {
			if (in_array($row['checkbox_id'], $deleted_checkbox_ids)) {
				$this->checkbox_groups_log_activity($row, $workorder_id, 'remove');
			}

			if(empty($checkbox_ids) && $workorder_id) {
				$this->checkbox_groups_log_activity($row, $workorder_id, 'remove');
			}
		}
		
		$checkbox_data = array();

		foreach($checkbox_ids as $id){
			$checkbox_data[] = array(
				'checkbox_id'=> $id,
				'workorder_id'=> $workorder_id,
			);
		}

		if ($this->Work_order->workorder_checkbox_state_save($checkbox_data, $workorder_id)){
			echo json_encode(array('success'=>true,'message'=>lang('work_orders_checkbox_successful_adding')));
		} else {
			echo json_encode(array('success'=>false,'message'=>lang('work_orders_checkbox_successful_error')));
		}
	}

	function delete_note()
	{
		$note_id = $this->input->post('note_id');
		if($this->Work_order->delete_note($note_id))
		{
			echo json_encode(array('success'=>true,'message'=>lang('work_orders_successful_deleted')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('work_orders_unable_to_delete')));
		}
		
	}

	function customer_search()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Customer->get_customer_search_suggestions($this->input->get('term'),0,100);
		
		if ($this->config->item('enable_customer_quick_add'))
		{
			$suggestions[] = array('subtitle' => '','avatar' => base_url()."assets/img/user.png",'value' => 'QUICK_ADD|'.$this->input->get('term'), 'label' => lang('customers_add_new_customer').' '.$this->input->get('term'));
		}
		
		echo json_encode(H($suggestions));
	}

	function select_customer(){
		
		if ($this->config->item('enable_customer_quick_add') && strpos($this->input->post('customer'),'QUICK_ADD|') !== FALSE)
		{
			$_POST['customer'] = str_replace('QUICK_ADD|','',$_POST['customer']);
			$_POST['customer'] = str_replace('|FORCE_PERSON_ID|','',$_POST['customer']);
			$this->load->helper('text');
			list($first_name,$last_name) = split_name($_POST['customer']);
			$person_data = array('first_name' => $first_name,'last_name' => $last_name);
			$customer_data = array();
			$this->Customer->save_customer($person_data, $customer_data);
			$_POST['customer'] =  $person_data['person_id'];
		}
		
		$person_id = $_POST['customer'];
		$customer_data = $this->Customer->get_info($person_id);

		$this->session->set_userdata('customer_id_for_new_work_order',$person_id);
		echo json_encode(array('customer_data' => $customer_data));
	}

	function save_new_work_order(){
		$customer_id = $this->input->post("customer_id");
		$items = $this->session->userdata('items_for_new_work_order');
		
		if(empty($items)){
			echo json_encode(array('success' => false,'message'=>lang('work_orders_must_select_item')));
		}
		else{
			$this->session->set_userdata('items_for_new_work_order',array());
			$this->session->set_userdata('customer_id_for_new_work_order','');

			$this->db->trans_begin();
			$work_order_id = $this->Work_order->save_new_work_order($customer_id,$items);
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
			}else{
				$this->db->trans_commit();
			}
			echo json_encode(array('success' => true,'message'=>lang('work_orders_successful_added_new_work_order'),'work_order_id'=>$work_order_id));
		}
	}

	function add_item_to_session(){
		$item_id = $this->input->post('item_id');
		$this->session->set_userdata('item_id_for_new',$item_id);
	}

	function add_item_serial_number_to_session(){
		$serial_number = $this->input->post('serial_number');
		$this->session->set_userdata('item_serial_number_for_new',$serial_number);
	}

	function add_customer_to_session(){
		$customer_id = $this->input->post('customer_id');
		$this->session->set_userdata('customer_id_for_new_work_order',$customer_id);
	}
	function get_work_order_status_info(){
		$status_id = $this->input->get('status_id');
		$work_order_status_info = $this->Work_order->get_status_info($status_id);
		echo json_encode($work_order_status_info);
	}

	function edit_item_serialnumber($line){
		$edit_value = $this->input->post("value");
		$edit_name = $this->input->post("name");

		$items = $this->session->userdata('items_for_new_work_order') ? $this->session->userdata('items_for_new_work_order') : array();
		if(isset($items[$line])){
			$items[$line]['serial_number'] = $edit_value;
			$this->session->set_userdata('items_for_new_work_order', $items);
			echo json_encode(array('success'=>true, 'serial_number' => $edit_value, 'id' => $edit_name ));
			return;
		}
		echo json_encode(array('success'=>false, 'serial_number' => $edit_value ));
		return;
	}

	function remove_items_for_new_work_order(){
		$index = $this->input->post('index');
		$items = $this->session->userdata('items_for_new_work_order');
		
		unset($items[$index]);
		
		$this->session->set_userdata('items_for_new_work_order',$items);
 		echo json_encode(array('success' => true));
	}

	function init_for_new_work_order(){
		$this->session->set_userdata('items_for_new_work_order',array());
		$this->session->set_userdata('customer_id_for_new_work_order','');
		echo json_encode(array('success' => true));
	}

	function added_new_item_for_work_order(){
		$item_id = $this->input->post('item');
		$this->session->set_flashdata('added_new_item_id_work_order', $item_id);
	}

	function download($file_id)
    {
        //Don't allow images to cause hangups with session
        session_write_close();
        $this->load->model('Appfile');
        $file = $this->Appfile->get($file_id);
        $this->load->helper('file');
        $this->load->helper('download');
        force_download($file->file_name,$file->file_data);
    }
    
    function delete_file($file_id){
        $this->Work_order->delete_file($file_id);
    }

    function manage_template()
	{

		$delivery_statuses = $this->Work_order->get_all_statuses();

		
		// Get Default Status Using 0 ID
		$default_status 			= $this->Work_order->get_status_id(0);

		
		if ($default_status) {
			$data['default'] = $default_status->content;
		} else {
			$data['default'] = '';
		}

		foreach ($delivery_statuses as $key => $status) {
	
			if ($status['notify_by_email'] == 1) {

				$data['delivery_statuses'][$key]['name'] 				= $status['name'];
				$data['delivery_statuses'][$key]['description'] 		= $status['description'];
				$data['delivery_statuses'][$key]['notify_by_email'] 	= $status['notify_by_email'];
				$data['delivery_statuses'][$key]['notify_by_sms'] 		= $status['notify_by_sms'];
				$data['delivery_statuses'][$key]['color'] 				= $status['color'];

				$result = $this->Work_order->get_status_id($key);
				if (isset($result)) {
					$data['delivery_statuses'][$key]['data'] = $result->content;
				} else {
					$data['delivery_statuses'][$key]['data'] = '';
				}
			}
		}

		$data['redirect'] = $this->input->get('redirect');

		$this->load->view('work_orders/manage_template',$data);		
	}

	function save_template()
	{
		
		$template_data = array(
			'status_id' =>	$this->input->post('status_id'),
			'content' 	=>	$this->input->post('email_template'),
		);

		// Email Tempalte Text Validate
		if ($template_data['status_id'] == NULL) {
			echo json_encode(array('success'=>false,'message'=>lang('deliveries_email_template_successful_error')));
			return FALSE;
		}
		
		if ($template_data['content'] == NULL) {
			echo json_encode(array('success'=>false,'message'=>lang('deliveries_email_template_successful_error')));
			return FALSE;
		}

		$refer = 'work_orders/manage_template';

		if($this->input->get('redirect')){
			$refer = $this->input->get('redirect');
		}

		if($this->Work_order->save_template($template_data))
		{
			echo json_encode(array('success'=>true,'message'=>lang('deliveries_status_successful_adding')));
			
		}
		else
		{
	
			echo json_encode(array('success'=>false,'message'=>lang('deliveries_email_template_successful_error')));
		
		}
	}

	/**
	* Get Template Content From Database & Replace with Keys
	* 
	* @var $company_name PHP Point of Sale
	* @param Search Values & Replace Value
	*
	* @return Render Template 
	**/

	function status_email_template($status_id, $work_order_info, $work_order_status_info)
	{
		// Search and Replace Template 
		$status_template = $this->Work_order->get_status_email_template($status_id);
		
		if ($status_template) {

			$sale_info = $this->Sale->get_info($work_order_info->sale_id)->row();

			$work_order_info_object = $this->Work_order->get_info($work_order_info->id)->row();
			$work_images = array();
			if (!empty($work_order_info->images)) {
				foreach (unserialize($work_order_info->images) as $key => $image) {

					$work_images[] 	= app_file_url($image);
				}
			}
			$customer_info 	= $this->Work_order->get_customer_info($work_order_info->id);
			$notes  = '';
			foreach ($this->Work_order->get_sales_items_notes($work_order_info->id,true) as $key => $row) {
				if ($row['internal'] == 0) {
					$notes = $row['note'].'<br>'.$row['detailed_notes'];
				}
			}

			$data    		= $status_template->content;
			$search_replace = array(
								"%company_name%"			=> $this->config->item('company'),
								"%customer_name%"			=> $customer_info['full_name'],
								"%work_order_id%"			=> $this->config->item('sale_prefix').' '.$work_order_info->sale_id,
								"%estimated_parts%"			=> to_currency($work_order_info->estimated_parts),
								"%estimated_labor%"			=> to_currency($work_order_info->estimated_labor),
								"%estimated_repair_date%"	=> date(get_date_format().' '.get_time_format(), strtotime($work_order_info->estimated_repair_date)),
								"%warranty_repair%"			=> $work_order_info->warranty,
								"%work_order_status%"		=> $work_order_status_info->name,
								"%customer_notes%"			=> $notes,
								"%work_images%"				=> '',
							);

			for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++) { 

				$custom_field = $this->Work_order->get_custom_field($k);
				$replace_value = str_replace(' ', '_', strtolower($custom_field)); 
		
				if ($this->Work_order->get_custom_field($k,'type') == 'date') {
					$search_replace["%custom_field_".$replace_value."%"] = date(get_date_format(), $work_order_info_object->{"custom_field_${k}_value"});
				} else {
					$search_replace["%custom_field_".$replace_value."%"] = $work_order_info_object->{"custom_field_${k}_value"};
				}
			}

			$return['message'] 	=  str_replace(array_keys($search_replace), $search_replace, $data);
			$return['images'] 	=  $work_images;

			return $return;
		}
		return false;

	}
	
	function pre_auth_capture($work_order_id)
	{
		$emp_location_id = $this->Employee->get_logged_in_employee_current_location_id();

		$credit_card_processor 	= $this->_get_cc_processor($emp_location_id);

		if ($credit_card_processor)
		{
			$image = $credit_card_processor->capture_signature($this->Location->get_info_for_key('blockchyp_work_order_pre_auth'));
	    	$image_file_id = $this->Appfile->save('signature_'.$work_order_id.'.png', $image);
			$this->db->where('id',$work_order_id);
			$this->db->update('sales_work_orders', array('pre_auth_signature_file_id' => $image_file_id));
			
		}
		else
		{
			$this->_reload($work_order_id, array('error' => lang('sales_credit_card_processing_is_down')), false);
			return;
		}
			
	}
	
	function post_auth_capture($work_order_id)
	{
		$emp_location_id = $this->Employee->get_logged_in_employee_current_location_id();

		$credit_card_processor 	= $this->_get_cc_processor($emp_location_id);

		if ($credit_card_processor)
		{
			$image = $credit_card_processor->capture_signature($this->Location->get_info_for_key('blockchyp_work_order_post_auth'));
	    	$image_file_id = $this->Appfile->save('signature_'.$work_order_id.'.png', $image);
			$this->db->where('id',$work_order_id);
			$this->db->update('sales_work_orders', array('post_auth_signature_file_id' => $image_file_id));
			
		}
		else
		{
			$this->_reload($work_order_id, array('error' => lang('sales_credit_card_processing_is_down')), false);
			return;
		}
	}
	
	function _get_cc_processor($emp_location_id = NULL)
	{
		require_once (APPPATH.'libraries/Coreclearblockchypprocessor.php');
		$credit_card_processor = new Coreclearblockchypprocessor($this,$emp_location_id);
		return $credit_card_processor;

	}

	function edit_approved_by($sale_id,$item_id, $item_variation_id=false, $line,$is_item_kit = false){

		if($is_item_kit) {
			$item_name = $this->Item_kit->get_info($item_id)->name;
		} else {
			$item_name = $this->Item->get_info($item_id)->name;
		}
		
		if($item_variation_id){
			$item_id = $item_id.'#'.$item_variation_id;
		}
		
		$sale_item = $this->Sale->get_sale_item($sale_id,$item_id,$line);

		$oldvalue = $sale_item->approved_by;
		$approved_by = $this->input->post("value");
		$this->Sale->sale_item_approved_by_update($sale_id,$item_id,$line,$approved_by,$is_item_kit);
		
		$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
		$work_order_id = $work_order_info['id'];
		
		if ($work_order_id)
		{
			$this->Work_order->log_activity($work_order_id,$item_name.' [field]approved_by[/field] '.lang('common_changed').' '.lang('common_from').' [oldvalue]'.$this->Employee->get_info($oldvalue)->full_name.'[/oldvalue] '.lang('common_to').' [newvalue]'.$this->Employee->get_info($approved_by)->full_name.'[/newvalue]');
		}
		return true;
	}

	function edit_assigned_to($sale_id,$item_id,$item_variation_id=false,$line,$is_item_kit = false){
		$item_name = $this->Item->get_info($item_id)->name;
		$sale_item = $this->Sale->get_sale_item($sale_id,$item_id,$line);
		$oldvalue = $sale_item->assigned_to;
		
		if($item_variation_id){
			$item_id = $item_id.'#'.$item_variation_id;
		}
		$assigned_to = $this->input->post("value");
		$this->Sale->sale_item_assigned_to_update($sale_id,$item_id,$line,$assigned_to,$is_item_kit);
		
		$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
		$work_order_id = $work_order_info['id'];
		
		if ($work_order_id)
		{
			$this->Work_order->log_activity($work_order_id,$item_name.' [field]assigned_to[/field] '.lang('common_changed').' '.lang('common_from').' [oldvalue]'.$this->Employee->get_info($oldvalue)->full_name.'[/oldvalue] '.lang('common_to').' [newvalue]'.$this->Employee->get_info($$assigned_to)->full_name.'[/newvalue]');
		}
		return true;
	}

	function save_additional_item()
	{
		$item_id = $this->work_order->create_or_update_repair_item();
		$sale_id =  $this->input->post('sale_id');
		$item_identifier = $this->input->post('item_identifier');

		$item_data = array(
			'deleted' => 0,
			'name'=> lang('work_orders_repair_item'),
			'description'=> $this->input->post('item_description'),
			'category_id'=> $this->Category->save(lang('work_orders_repair_item'), TRUE, NULL, $this->Category->get_category_id(lang('work_orders_repair_item'))),
			'size'=>'',
			'item_number'=> lang('work_orders_repair_item'),
			'product_id'=> lang('work_orders_repair_item'),
			'cost_price'=> 0,
			'unit_price'=> 0,
			'allow_alt_description'=> 1,
			'is_serialized'=> 0,
			'system_item'=> 1,
			'override_default_tax'=> 1,
			'is_ecommerce' => 0,
			'disable_loyalty' => 1,
		);

		if($this->Item->save($item_data,$item_id)) {

			if(isset($item_data['item_id'])){
				$item_id = $item_data['item_id'];
			}

			if($sale_id){
				if($this->Sale->add_sale_item($sale_id, $item_id, 1, null, $item_identifier == 'repair_item' ? 1 : 0)){
					$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
					$work_order_id = $work_order_info['id'];
					if ($work_order_id){
						$item_info = $this->Item->get_info($item_id,false);
						$item_name = $item_info->name . ($item_info->description ? ': '.$item_info->description : '');
						$this->Work_order->log_activity($work_order_id, $item_name.' '.lang('common_added_to_work_order'));
						echo json_encode(array('success'=>true, 'message'=>lang('common_successful_adding')));
					}
				} else {
					echo json_encode(array('success'=>false, 'message'=>lang('work_orders_unable_to_add_item')));
				}
			}else{
				$this->add_but_not_save($item_id);
			}
		} else {
			echo json_encode(array('success'=>false, 'message'=>lang('common_error_adding_updating').' '.H($item_data['name']),'item_id'=>-1));
		}
	}

	function edit_sale_item_description($sale_id, $item_id, $line, $item_variation_id=false){
		
		$item_name = $this->Item->get_info($item_id)->name;
		$sale_item = $this->Sale->get_sale_item($sale_id,$item_id,$line);
		$oldvalue = $sale_item->description;
		
		if($item_variation_id){
			$item_id = $item_id.'#'.$item_variation_id;
		}
		
		$description = $this->input->post("value");

		$this->Sale->sale_item_description_update($sale_id, $item_id, $line, $description);
		
		$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
		$work_order_id = $work_order_info['id'];
		
		if ($work_order_id)
		{
			$this->Work_order->log_activity($work_order_id,$item_name.' [field]description[/field] '.lang('common_changed').' '.lang('common_from').' [oldvalue]'.$oldvalue.'[/oldvalue] '.lang('common_to').' [newvalue]'.$description.'[/newvalue]');
		}
		
	}

	function is_valid_receipt($receipt_sale_id) {
		//POS #
		$pieces = explode(' ',$receipt_sale_id);
		if(count($pieces)==2 && strtolower($pieces[0]) == strtolower($this->config->item('sale_prefix') ? $this->config->item('sale_prefix') : 'POS'))
		{
			return $this->Sale->exists($pieces[1]);
		}
		return false;	
	}

	function is_valid_item($item){
		return $item !='' && $item!== NULL;
	}

	function _reload($work_order_id, $data=null){
		redirect("work_orders/view/$work_order_id");
		return true;
	}

	function save_modifiers(){
		$sale_id = $this->input->post('sale_id');
		$item_id = $this->input->post('item_id');
		$line = $this->input->post('line');

		$modifiers = $this->input->post('modifiers') ? $this->input->post('modifiers') : array();
		$modifier_items = array();
		foreach($modifiers as $modifier_item_id){
			$modifier_item_info = $this->Item_modifier->get_modifier_item_info($modifier_item_id);
			$modifier_items[$modifier_item_id] = array('sale_id' => $sale_id, 'item_id'=> $item_id, 'line'=> $line, 'unit_price' => $modifier_item_info['unit_price'], 'cost_price' => $modifier_item_info['cost_price']);
		}

		$this->work_order->save_modifiers($sale_id, $item_id, $line, $modifier_items);
	}

	function get_modifiers(){
		$sale_id = $this->input->get('sale_id');
		$item_id = $this->input->get('item_id');
		$line = $this->input->get('line');

		echo '<div class="container"><form action="'.site_url('work_orders/save_modifiers').'" id="modifier_form" method="POST">';
		echo form_hidden('sale_id', $sale_id);
		echo form_hidden('item_id', $item_id);
		echo form_hidden('line', $line);
		
		foreach($this->Item_modifier->get_modifiers_for_work_order_item($item_id)->result_array() as $modifier){
			foreach($this->Item_modifier->get_modifier_items($modifier['id'])->result_array() as $modifier_item){
				echo '<div class="row">';
				echo form_label($modifier['name'].' > '.$modifier_item['name'].': '.to_currency($modifier_item['unit_price']), '',array('class'=>'col-sm-4 col-md-4 col-lg-4 control-label wide')); 
				echo form_checkbox(array(
					'name'=>'modifiers[]',
					'id'=>'modifier_'.$modifier_item['id'],
					'class' => 'modifier',
					'value'=>$modifier_item['id'],
					'checked'=> $this->Sale->get_sale_item_modifiers($sale_id, $item_id, $line, $modifier_item['id'])->row('modifier_item_id') == $modifier_item['id']
				));
				echo '<label for="modifier_'.$modifier_item['id'].'"><span></span></label></div>';
			}
		}
		echo '<input type="submit" class="btn btn-primary" /></form><script>$("#modifier_form").ajaxForm({beforeSubmit: function(){$("#choose_modifiers").modal("hide");}, success: itemScannedSuccess});</script>';
	}

	function edit_item_modifier_price($sale_id, $item_id, $line, $modifier_id){
		$value = $this->input->post('value');
		$this->work_order->update_item_modifier_price($sale_id, $item_id, $line, $modifier_id, $value);
	}

	function delete_item_kit($sale_id,$line){

		$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
		$work_order_id = $work_order_info['id'];
		
		if ($work_order_id)
		{
			$sale_item_info = $this->Sale->get_sale_item_kit_info_by_sale_id_and_line($sale_id,$line);
			$item_name = $this->Item_kit->get_info($sale_item_info->item_kit_id)->name;
			$this->Work_order->log_activity($work_order_id,$item_name.' '.lang('common_removed_from_work_order'));
		}

		$this->Work_order->delete_item_kit($sale_id,$line);
		
		$this->Sale->update_sale_statistics($sale_id);
	}
 
	// delete activity log for work order by id and return message

	function delete_activity_log($activity_id = null)
	{
		$activity_id = $this->input->post('activity_id');
		if($this->Work_order->delete_work_order_log($activity_id))
		{
			echo json_encode(array('success'=>true,'message'=>lang('work_orders_successful_deleted')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('work_orders_unable_to_delete')));
		}
	}

	function checkbox_groups_log_activity($row, $workorder_id, $type = 'add')
	{
		$checkbbox_info = $this->Work_order->get_checkbox_info($row['checkbox_id']);
		
		// check checkbox group type 
		if($checkbbox_info->type == 1) {
			$work_order_type = lang('work_orders_pre');
		} else {
			$work_order_type = lang('work_orders_post');
		}
		// log activity for checkbox group added or removed to work order 
		if($type == 'add') { 
			$this->Work_order->log_activity($workorder_id, $work_order_type.' '.$checkbbox_info->name.' '.lang('common_added_to_work_order'));
		} else {
			$this->Work_order->log_activity($workorder_id, $work_order_type.' '.$checkbbox_info->name.' '.lang('common_removed_from_work_order'));
		}
	}

	function delete_custom_field_value($work_order_id,$k)
	{
		$work_order_info 	= $this->Work_order->get_info($work_order_id)->row();
		$file_id 			= $work_order_info->{"custom_field_{$k}_value"};
		$this->load->model('Appfile');
		$this->Appfile->delete($file_id);
		$work_order_data 	= array();
		$work_order_data["custom_field_{$k}_value"] = NULL;
		$this->Work_order->save($work_order_data,$work_order_id);
	}

	function edit_sale_item_serial_number($sale_id, $item_id, $line, $item_variation_id=false){
		
		$item_name = $this->Item->get_info($item_id)->name;
		$sale_item = $this->Sale->get_sale_item($sale_id,$item_id,$line);
		$oldvalue = $sale_item->serialnumber;
		
		if($item_variation_id){
			$item_id = $item_id.'#'.$item_variation_id;
		}
		
		$serialnumber = $this->input->post("value");
		
		$this->Sale->sale_item_serialnumber_update($sale_id, $item_id, $line, $serialnumber);
		
		$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
		$work_order_id = $work_order_info['id'];
		
		if ($work_order_id)
		{
			$this->Work_order->log_activity($work_order_id,$item_name.' '.lang('common_serial_number').' '.lang('common_changed').' '.lang('common_from').' '.$oldvalue.' '.lang('common_to').' '.$serialnumber.'');
		}
		
	}

	function edit_item_quantity($sale_id, $item_id, $line, $item_variation_id=false, $is_item_kit = false){

		if($is_item_kit){
			$item_name 	= $this->Item_kit->get_info($item_id)->name;
			$sale_item 	= $this->Sale->get_sale_item_kits($sale_id,$line, 1)->row();
			$oldvalue 	= to_quantity($sale_item->quantity_purchased);
		} else {
			$item_name 	= $this->Item->get_info($item_id)->name;
			$sale_item 	= $this->Sale->get_sale_item($sale_id,$item_id,$line, 1);
			$oldvalue 	= to_quantity($sale_item->quantity_purchased);
		}

		if($item_variation_id){
			$item_id = $item_id.'#'.$item_variation_id;
		}

		$quantity_purchased = $this->input->post("value");

		if($is_item_kit) {
			$this->Sale->sale_item_kit_quantity_update($sale_id, $line, $quantity_purchased);
		} else {
			$this->Sale->sale_item_quantity_update($sale_id, $item_id, $quantity_purchased, $line);
		}
		
		$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
		$work_order_id = $work_order_info['id'];
		
		if ($work_order_id)
		{
			$this->Work_order->log_activity($work_order_id,$item_name.' '.lang('work_orders_quantity').' '.lang('common_changed').' '.lang('common_from').' '.$oldvalue.' '.lang('common_to').' '.to_quantity($quantity_purchased));
		}
		
	}

	function edit_item_discount($sale_id, $item_id, $line, $item_variation_id = false, $is_item_kit = false){

		if($is_item_kit){
			$item_name 	= $this->Item_kit->get_info($item_id)->name;
			$sale_item 	= $this->Sale->get_sale_item_kits($sale_id,$item_id,$line, 1);
			$oldvalue 	= to_quantity($sale_item->discount_percent ?? 0);
		} else {
			$item_name 	= $this->Item->get_info($item_id)->name;
			$sale_item 	= $this->Sale->get_sale_item($sale_id,$item_id,$line, 1);
			$oldvalue 	= to_quantity($sale_item->discount_percent ?? 0);
		}

		if($item_variation_id){
			$item_id = $item_id.'#'.$item_variation_id;
		}
		
		$discount = $this->input->post("value");

		$this->Sale->sale_item_discount_update($sale_id, $item_id,  $line, $discount, $is_item_kit);
		
		$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
		$work_order_id = $work_order_info['id'];
		
		if ($work_order_id)
		{
			$this->Work_order->log_activity($work_order_id,$item_name.' Discount '.lang('common_changed').' '.lang('common_from').' '.$oldvalue.' '.lang('common_to').' '.to_quantity($discount));
		}
		
	}
	
    function import_work_orders() {
        $this->load->view("work_orders/import_work_orders", null);
    }

    function do_excel_upload_work_order() {
        ini_set('memory_limit', '1024M');
        $this->load->helper('demo');

        //Write to app files
        $this->load->model('Appfile');
        $cur_timezone = date_default_timezone_get();
        //We are doing this to make sure same timezone is used for expiration date
        date_default_timezone_set('America/New_York');
        $app_file_file_id = $this->Appfile->save($_FILES["file"]["name"], file_get_contents($_FILES["file"]["tmp_name"]), '+3 hours');
        date_default_timezone_set($cur_timezone);
        //Store file_id from app files in session so we can reference later
        $this->session->set_userdata("excel_import_file_id_work_order", $app_file_file_id);

        $file_info = pathinfo($_FILES["file"]["name"]);
        $file = $this->Appfile->get($this->session->userdata('excel_import_file_id_work_order'));
        $tmpFilename = tempnam(ini_get('upload_tmp_dir'), 'cexcel');

        file_put_contents($tmpFilename, $file->file_data);
        $this->load->helper('spreadsheet');

        $first_row = get_spreadsheet_first_row($tmpFilename, $file_info['extension']);
        unlink($tmpFilename);

        $fields = $this->_get_database_fields_for_import_as_array_work_order();

        $k = 0;
        foreach ($first_row as $col_name) {
            $column = array('Spreadsheet Column' => $col_name, 'Index' => $k);

            if ($column['Spreadsheet Column'] == '') {
                echo json_encode(array(
                    'success' => false,
                    'message' => lang('common_spreadsheet_columns_must_have_labels')
                ));
                return;
            }

            $cols = array_column($fields, 'Name');
            $cols = array_map('strtolower', $cols);
            $search = strtolower($column['Spreadsheet Column']);
            $matchIndex = array_search($search, $cols);

            if (is_numeric($matchIndex)) {
                $column['Database Field'] = $fields[$matchIndex]['Id'];
            }

            $columns[] = $column;
            $k++;
        }

        $this->session->set_userdata("import_work_orders_excel_import_column_map", $columns);
        echo json_encode(array('success' => true, 'message' => lang('common_import_successful')));
    }

    function do_excel_import_map_work_order() {
        ini_set('memory_limit', '1024M');
        $this->load->helper('text');
        $this->load->model('Appfile');

        $file = $this->Appfile->get($this->session->userdata('excel_import_file_id_work_order'));

        $tmpFilename = tempnam(ini_get('upload_tmp_dir'), 'cexcel');

        file_put_contents($tmpFilename, $file->file_data);
        $this->load->helper('spreadsheet');

        $file_info = pathinfo($file->file_name);
        $sheet = file_to_spreadsheet($tmpFilename, $file_info['extension']);
        unlink($tmpFilename);

        $this->sheet_data = array();

        $columns = array();
        $k = 0;

        $fields = $this->_get_database_fields_for_import_as_array_work_order();
        $numRows = $sheet->getNumberOfRows();

        while ($col_name = $sheet->getCellByColumnAndRow($k, 1)) {
            $column = array('Spreadsheet Column' => $col_name, 'Index' => $k);

            $cols = array_column($fields, 'Name');
            $cols = array_map('strtolower', $cols);
            $search = strtolower($column['Spreadsheet Column']);
            $matchIndex = array_search($search, $cols);

            if (is_numeric($matchIndex)) {
                $column['Database Field'] = $fields[$matchIndex]['Id'];
            }

            $col_data = array();
            for ($i = 2; $i <= $numRows; $i++) {
                $col_data[] = trim(clean_string($sheet->getCellByColumnAndRow($k, $i)));
            }

            $column["data"] = $col_data;

            $columns[] = $column;
            $k++;
        }

        $this->session->set_userdata("import_work_orders_excel_import_num_rows", $numRows);
        $this->session->set_userdata("import_work_orders_excel_import_column_map", $columns);
    }

    function get_database_fields_for_import_work_order() {
        $fields = $this->_get_database_fields_for_import_as_array_work_order();
        array_unshift($fields, array('Name' => '', 'Id' => -1));
        echo json_encode($fields);
    }

    private function _get_database_fields_for_import_as_array_work_order() {
        ini_set('memory_limit', '1024M');
        $fields = array();

        $fields[] = array('Name' => lang('common_work_order_sale_id'), 'key' => 'sale_id');
        $fields[] = array('Name' => lang('common_work_order_date'), 'key' => 'sale_time');
        $fields[] = array('Name' => lang('common_status'), 'key' => 'status');
        $fields[] = array('Name' => lang('work_orders_estimated_repair_date'), 'key' => 'estimated_repair_date');
        $fields[] = array('Name' => lang('work_orders_estimated_parts'), 'key' => 'estimated_parts');
        $fields[] = array('Name' => lang('work_orders_estimated_labor'), 'key' => 'estimated_labor');
        $fields[] = array('Name' => lang('work_orders_warranty_repair'), 'key' => 'warranty');
        $fields[] = array(
            'Name' => lang('common_person_id') . '/' . lang('work_orders_customer_name'),
            'key'  => 'customer_id'
        );
        $fields[] = array('Name' => lang('work_orders_employee_id'), 'key' => 'employee_id');
        $fields[] = array('Name' => lang('work_orders_location_id'), 'key' => 'location_id');
        $fields[] = array('Name' => lang('common_comment'), 'key' => 'comment');
        $fields[] = array(
            'Name' => lang('common_item_id') . '/' . lang('common_item_number') . '/' . lang('common_product_id'),
            'key'  => 'item_id'
        );

		$fields[] = array('Name' => lang('work_orders_quantity_ordered'), 'key' => 'quantity_purchased');	
        $fields[] = array('Name' => lang('work_orders_cost'), 'key' => 'item_cost_price');
        $fields[] = array('Name' => lang('work_orders_price'), 'key' => 'item_unit_price');
        $fields[] = array('Name' => lang('work_orders_tax'), 'key' => 'tax');
        $fields[] = array('Name' => lang('work_orders_paid_amount'), 'key' => 'payment_amount');
        $fields[] = array('Name' => lang('work_orders_payment_date'), 'key' => 'payment_date');
        $fields[] = array('Name' => lang('work_orders_payment_type'), 'key' => 'payment_type');

        for ($k = 1; $k <= NUMBER_OF_PEOPLE_CUSTOM_FIELDS; $k++) {
            if ($this->Work_order->get_custom_field($k) !== FALSE) {
                $fields[] = array(
                    'Name' => $this->Work_order->get_custom_field($k),
                    'key'  => 'custom_field_' . $k . '_value'
                );
            }
        }

        $id = 0;
        foreach ($fields as &$field) {
            $field['Id'] = $id;
            $id++;
        }
        unset($field);

        usort($fields, function ($a, $b) {
            return $a['Name'] <=> $b['Name'];
        });

        return $fields;
    }

    function get_uploaded_excel_columns_work_order() {
        $data = $this->session->userdata("import_work_orders_excel_import_column_map");

        foreach ($data as &$col) {
            unset($col["data"]);
        }

        echo json_encode($data);
    }

    public function set_excel_columns_map_work_order() {
        ini_set('memory_limit', '1024M');
        $data = $this->session->userdata("import_work_orders_excel_import_column_map");

        $mapKeys = json_decode($this->input->post('mapKeys'), true);

        foreach ($mapKeys as $mapKey) {
            foreach ($data as $key => $col) {
                if ($col['Index'] == $mapKey["Index"]) {
                    $data[$key]["Database Field"] = $mapKey["Database Field"];
                }
            }
        }

        $this->session->set_userdata("import_work_orders_excel_import_column_map", $data);
    }

    private function _indexColumnArray($n) {
        if (isset($n['Database Field'])) {
            return $n['Database Field'];
        }

        return 'N/A';
    }

    //new function
    function complete_excel_import_work_order() {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        ini_set('max_input_time', '-1');

        $this->session->set_userdata('excel_import_error_log_work_order', NULL);

        $employee_info = $this->Employee->get_logged_in_employee_info();

        $numRows = $this->session->userdata("import_work_orders_excel_import_num_rows");
        $columns_with_data = $this->session->userdata("import_work_orders_excel_import_column_map");

        $fields = $this->_get_database_fields_for_import_as_array_work_order();

        $fieldId_to_colIndex = array_flip(array_map(array($this, '_indexColumnArray'), $columns_with_data));
        unset($fieldId_to_colIndex['N/A']);

        $can_commit = TRUE;
        $this->db->trans_begin();

        for ($i = 0; $i < $numRows - 1; $i++) {
            $is_new_work_order = FALSE;
            $sale_id = FALSE;
			$work_order_sale_data = array();
            $work_order_data = array();
            $work_order_item_data = array();
            $work_order_payment_data = array();


			$work_order_data_keys = array('status','estimated_repair_date','estimated_parts','estimated_labor','warranty_repair');
			
            $work_order_sale_data_keys = array(
				"sale_id",
                "sale_time",
                "customer_id",
                "employee_id",
                "location_id",
                "comment",
            );
            for ($k = 1; $k <= NUMBER_OF_PEOPLE_CUSTOM_FIELDS; $k++) {
                if ($this->Work_order->get_custom_field($k) !== FALSE) {
                    $work_order_data_keys[] = 'custom_field_' . $k . '_value';
                }
            }

            $work_order_item_data_keys = array(
                "item_id",
                "quantity_purchased",
                "item_cost_price",
                "item_unit_price",
                "tax"
            );
            $work_order_payment_data_keys = array("payment_amount", "payment_date", "payment_type");

			$item_descrption = '';

            foreach ($fields as $field) {

                if (array_key_exists($field['Id'], $fieldId_to_colIndex)) {
                    $key = $fieldId_to_colIndex[$field['Id']];
                }
                else {//if its not mapped skip
                    continue;
                }

                if ($field['key'] !== "") {
					
					if ($field['key'] == 'item_id')
					{
						$item_descrption = $columns_with_data[$key]['data'][$i];
					}
					
                    if (in_array($field['key'], $work_order_data_keys)) {
                        $work_order_data[$field['key']] = $this->_clean($field['key'], $columns_with_data[$key]['data'][$i], $i + 2);
                    }
                    elseif (in_array($field['key'], $work_order_sale_data_keys)) {
                        $work_order_sale_data[$field['key']] = $this->_clean($field['key'], $columns_with_data[$key]['data'][$i], $i + 2);
                    }
                    else if (in_array($field['key'], $work_order_item_data_keys)) {
                        $work_order_item_data[$field['key']] = $this->_clean($field['key'], $columns_with_data[$key]['data'][$i], $i + 2);
                    }
                    else if (in_array($field['key'], $work_order_payment_data_keys)) {
                        $work_order_payment_data[$field['key']] = $this->_clean($field['key'], $columns_with_data[$key]['data'][$i], $i + 2);
                    }
                }
            }//end field foreach

            if (!$work_order_item_data['item_id']) {
                $message = lang('common_item_id') . '/' . lang('common_item_number') . '/' . lang('common_product_id') . ' ' . lang('common_is_empty');
                $this->_log_validation_error($i + 2, $message, 'Error');

                $this->db->trans_rollback();

                echo json_encode(array(
                    'type'    => 'error',
                    'message' => lang('common_errors_occured_durring_import'),
                    'title'   => lang('common_error')
                ));
                return;
            }

            if ($work_order_item_data['item_id'] == 'invalid') {
                $message = lang('common_item_id') . '/' . lang('common_item_number') . '/' . lang('common_product_id') . ' ' . lang('common_is_invalid');
                $this->_log_validation_error($i + 2, $message, 'Error');

                $this->db->trans_rollback();

                echo json_encode(array(
                    'type'    => 'error',
                    'message' => lang('common_errors_occured_durring_import'),
                    'title'   => lang('common_error')
                ));
                return;
            }

            $item_info = $this->Item->get_info($work_order_item_data['item_id']);

            
				if (!isset($work_order_sale_data['employee_id']))
				{
					$work_order_sale_data['employee_id'] = 1;
				}
				
				if (!isset($work_order_sale_data['location_id']))
				{
					$work_order_sale_data['location_id'] = 1;
				}
				
				
                $work_order_sale_data['register_id'] = $this->Employee->get_logged_in_employee_current_register_id();
                $work_order_sale_data['exchange_rate'] = 1;
                $work_order_sale_data['exchange_currency_symbol'] = '$';
                $work_order_sale_data['exchange_currency_symbol_location'] = 'before';
                $work_order_sale_data['exchange_thousands_separator'] = ',';
                $work_order_sale_data['exchange_decimal_point'] = '.';
                $work_order_sale_data['suspended'] = '2';
				
				$sale_id = $work_order_sale_data['sale_id'];
				if (!$this->Sale->exists($sale_id))
				{
                	$sale_id = $this->Sale->save_sale_data($work_order_sale_data);
                }
				
				if ($sale_id) 
				{
					$work_order_id = $this->Work_order->get_work_order_id_by_sale_id($sale_id);
					$work_order_data['sale_id'] = $sale_id;
					
					if (!$work_order_id)
					{
						$this->Work_order->save($work_order_data);
					}
					
                    $work_order_item_data['sale_id'] = $sale_id;
                    $work_order_item_data['description'] = $item_info->name == lang('work_orders_repair_item') ? $item_descrption : $item_info->name;
                    $work_order_item_data['subtotal'] = $work_order_item_data['item_unit_price'] * ($work_order_item_data['quantity_purchased'] ?? 1);
                    $work_order_item_data['total'] = $work_order_item_data['subtotal'] + ($work_order_item_data['tax'] == '' ? 0 : $work_order_item_data['tax']);
                    $work_order_item_data['profit'] = ($work_order_item_data['item_unit_price'] * ($work_order_item_data['quantity_purchased'] ?? 1)) - ($work_order_item_data['item_cost_price'] * ($work_order_item_data['quantity_purchased'] ?? 1));
                    $work_order_item_data['is_repair_item'] = 1;
                    $work_order_item_data['line'] = $this->Sale->get_max_sale_item_line($sale_id) + 1;
					
					if (!isset($work_order_item_data['quantity_purchased']) || $work_order_item_data['quantity_purchased'])
					{
						$work_order_item_data['quantity_purchased'] = 1;
					}
					
                    if ($this->Sale->save_sales_item_data($work_order_item_data)) {
						
						$sales_items_notes_data = array
						(
							'sale_id'=>$sale_id,
							'item_id'=>$work_order_item_data['item_id'],
							'line'=>$work_order_item_data['line'],
							'note'=>lang('common_note'),
							'detailed_notes'=>$work_order_sale_data['comment'],
							'internal'=>1,
							'employee_id'=>$work_order_sale_data['employee_id'],
							'images'=>serialize(array()),
							'device_location'=>'',
							'status'=>$work_order_data['status'],
							'note_timestamp' => $work_order_sale_data['sale_time'],
						);
		
						$work_order_info = $this->Work_order->get_info_by_sale_id($sale_id)->row_array();
						$work_order_id = $work_order_info['id'];
						$this->Sale->save_sales_items_notes_data($sales_items_notes_data);
						
                        $this->Sale->update_sale_statistics($sale_id);

                        if ($work_order_payment_data['payment_amount'] > 0 && $work_order_payment_data['payment_type']) {
                            $work_order_payment_data['sale_id'] = $sale_id;
                            $this->Sale->save_sales_payment_data($work_order_payment_data);
                        }

                        if ($work_order_item_data['tax'] > 0) {
                            $sales_items_taxes_data = array(
                                'sale_id' => $sale_id,
                                'item_id' => $work_order_item_data['item_id'],
                                'line'    => $work_order_item_data['line'],
                                'name'    => 'TAX',
                                'percent' => round(($work_order_item_data['tax'] / ($work_order_item_data['item_unit_price'] * $work_order_item_data['quantity_purchased'])) * 100, 2),
                            );
                            $this->Sale->save_sales_items_taxes($sales_items_taxes_data);
                        }
						
                    }
                    else {
                        $this->_logDbError($i + 2);
                        $can_commit = FALSE;
                        continue;
                    }
                }
                else {
                    $this->_logDbError($i + 2);
                    $can_commit = FALSE;
                    continue;
                }
            

        } //loop done for work_orders

        if ($can_commit) {
            $this->db->trans_commit();
        }
        else {
            $this->db->trans_rollback();
        }

        //if there were any errors or warnings
        if ($this->db->trans_status() === FALSE && !$can_commit) {
            echo json_encode(array(
                'type'    => 'error',
                'message' => lang('common_errors_occured_durring_import'),
                'title'   => lang('common_error')
            ));
        }
        else if ($this->db->trans_status() === FALSE && $can_commit) {
            echo json_encode(array(
                'type'    => 'warning',
                'message' => lang('common_warnings_occured_durring_import'),
                'title'   => lang('common_warning')
            ));
        }
        else {
            //Clear out session data used for import
            $this->session->unset_userdata('excel_import_file_id_work_order');
            $this->session->unset_userdata('import_work_orders_excel_import_column_map');
            $this->session->unset_userdata('import_work_orders_excel_import_num_rows');
            echo json_encode(array(
                'type'    => 'success',
                'message' => lang('common_import_successful'),
                'title'   => lang('common_success')
            ));
        }
    }

    private function _clean($key, $value, $row = NULL) {
        if ($key == 'sale_id') {
            if (!$value) {
                return '';
            }
            return $value;

        }

        if ($key == 'sale_time' || $key == 'estimated_repair_date') {
            if (!$value || strtotime($value) === FALSE) {
                return date('Y-m-d H:i:s');
            }
            return date('Y-m-d H:i:s', strtotime($value));

        }
		
		
        if ($key == 'status') {
            
			$status_id = $this->Work_order->get_status_id_by_name($value);
			
			if ($status_id === FALSE)
			{
				$status_data = array('name' => $value);
				$this->Work_order->status_save($status_data);
				
				return $status_data['id'];
			}
			
			return $status_id;
        }
		

        if ($key == 'customer_id') {
            if ($value) {
                $supplier_name_before_searching = $value;
                $value = $this->Customer->exists($value) ? $value : $this->Customer->find_customer_id($value);

                if (!$value) {
                    $first_and_last_name = explode(' ', $supplier_name_before_searching);
                    $first_name = $first_and_last_name[0] ?: '';
                    $last_name = $first_and_last_name[1] ?: '';
                    $person_data = array('first_name' => $first_name, 'last_name' => $last_name);
                    $customer_data = array();
                    $this->Customer->save_customer($person_data, $customer_data);
                    $value = $customer_data['person_id'];
                }

                return $value;

            }

            return NULL;

        }

        if ($key == 'employee_id') {
            if ($value) {
                return $this->Employee->exists($value) ? $value : $this->Employee->get_logged_in_employee_info()->person_id;
            }

            return $this->Employee->get_logged_in_employee_info()->person_id;
        }

        if ($key == 'sold_by_employee_id') {
            if ($value) {
                return $this->Employee->exists($value) ? $value : NULL;
            }

            return NULL;
        }

        if ($key == 'location_id') {
            if ($value) {
				
				if ($this->Location->exists($value))
				{
					return $value;
				}
				
				if ($location_info = $this->Location->get_info_by_name($value))
				{
					return $location_info->location_id;
				}
            }

            return $this->Employee->get_logged_in_employee_current_location_id();
        }

        if ($key == 'comment') {
            if (!$value) {
                return '';
            }

            return $value;
        }

        if ($key == 'item_id') {
            if ($value) {
                $item_id = $this->Item->lookup_item_by_item_id($value);
                if (!$item_id) {
                    $item_id = $this->Item->lookup_item_by_item_number($value);

                    if (!$item_id) {
                        $item_id = $this->Item->lookup_item_by_product_id($value);
                    }
                }

                if ($item_id) {
                    return $item_id;
                }
                else {
                    return $this->work_order->create_or_update_repair_item();
                }
            }

            return $this->work_order->create_or_update_repair_item();

        }

        if ($key == 'quantity_purchased') {
            if ($value !== '' && $value == (float)$value) {
                return strval((float)$value);
            }
            return 1;
        }

        if ($key == 'item_cost_price') {
            if ($value !== "") {
                return make_currency_no_money($value);
            }
            return 0;
        }

        if ($key == 'item_unit_price') {
            if ($value !== "") {
                return make_currency_no_money($value);
            }
            return 0;
        }

        if ($key == 'tax') {
            if ($value !== "") {
                return make_currency_no_money($value);
            }
            return 0;
        }

        if ($key == 'payment_amount') {
            if ($value !== "") {
                return make_currency_no_money($value);
            }
            return 0;
        }

        if ($key == 'payment_date') {
            if (!$value || strtotime($value) === FALSE) {
                return date('Y-m-d H:i:s');
            }
            return date('Y-m-d H:i:s', strtotime($value));

        }

        if ($key == 'payment_type') {
            if (!$value) {
                return '';
            }
            return $value;

        }
		
        if ($key == 'estimated_parts' || $key == 'estimated_labor') {
            if (!$value) {
                return '';
            }
            return $value;

        }
		
        if ($key == 'warranty') {
            if (!$value) {
                return 0;
            }
            return 1;

        }

        $custom_fields = array();
        for ($k = 1; $k <= NUMBER_OF_PEOPLE_CUSTOM_FIELDS; $k++) {
            if ($this->Work_order->get_custom_field($k) !== FALSE) {
                $custom_fields[] = "custom_field_${k}_value";
            }
        }

        if (in_array($key, $custom_fields)) {
            if (!$value) {
                return '';
            }

            $k = substr($key, strlen('custom_field_'), 1);
            $type = $this->Work_order->get_custom_field($k, 'type');

            if ($type == 'date') {
                $value = strtotime($value);
            }

            return $value;
        }

    }

    private function _logDbError($index) {
        $error = $this->db->error();
        $matches = array();
        preg_match('/for key \'(.+)\'/', $error['message'], $matches);

        if (isset($matches[1])) {
            $col_name = $matches[1];
            $data = $this->_get_database_fields_for_import_as_array_work_order();
            $cols = array_column($data, 'key');
            $match_index = array_search($col_name, $cols);

            if ($match_index !== FALSE) {
                $column_human_name = $data[$match_index]['Name'];
                $error['message'] = str_replace($col_name, $column_human_name, $error['message']);
            }

        }
        $this->_log_validation_error($index, $error['message'], "Error");
    }

    private function _log_validation_error($row, $message, $type = "Warning") {
        //log errors and warnings for import
        if (!$log = $this->session->userdata('excel_import_error_log_work_order')) {
            $log = array();
        }

        $log[] = array("row" => $row, "message" => $message, "type" => $type);

        $this->session->set_userdata('excel_import_error_log_work_order', $log);
    }

    public function get_import_errors() {
        echo json_encode($this->session->userdata('excel_import_error_log_work_order'));
    }

    function _excel_get_header_row_import_work_orders() {
        $header_row = array();

        $header_row[] = lang('common_work_sale_order_id');
        $header_row[] = lang('common_work_order_date');
        $header_row[] = lang('common_status');
        $header_row[] = lang('work_orders_estimated_repair_date');
        $header_row[] = lang('work_orders_estimated_parts');
        $header_row[] = lang('work_orders_estimated_labor');
        $header_row[] = lang('work_orders_warranty_repair');		
        $header_row[] = lang('common_person_id') . '/' . lang('work_orders_customer_name');
        $header_row[] = lang('work_orders_employee_id');
        $header_row[] = lang('work_orders_location_id');
        $header_row[] = lang('common_comment');
        $header_row[] = lang('common_item_id') . '/' . lang('common_item_number') . '/' . lang('common_product_id');
        $header_row[] = lang('work_orders_quantity_ordered');
        $header_row[] = lang('work_orders_cost');
        $header_row[] = lang('work_orders_price');
        $header_row[] = lang('work_orders_tax');
        $header_row[] = lang('work_orders_paid_amount');
        $header_row[] = lang('work_orders_payment_date');
        $header_row[] = lang('work_orders_payment_type');

        for ($k = 1; $k <= NUMBER_OF_PEOPLE_CUSTOM_FIELDS; $k++) {
            if ($this->Work_order->get_custom_field($k) !== FALSE) {
                $header_row[] = $this->Work_order->get_custom_field($k);
            }
        }

        return $header_row;
    }

    function excel_template_for_new_work_orders() {
        $this->load->helper('report');
        $header_row = $this->_excel_get_header_row_import_work_orders();
        $this->load->helper('spreadsheet');
        array_to_spreadsheet(array($header_row), 'work_orders_import.' . ($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'));
    }
}	
?>
