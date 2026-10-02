<?php
require_once ("Secure_area.php");

require_once (APPPATH."libraries/google2fa/vendor/autoload.php");
require_once (APPPATH."libraries/bacon-qr-code/vendor/autoload.php");

use PragmaRX\Google2FAQRCode\Google2FA;

class Home extends Secure_area 
{
	function __construct()
	{
		parent::__construct();	
		$this->load->helper('report');
		$this->lang->load('module');
		$this->lang->load('home');
		$this->load->model('Item');
		$this->load->model('Item_kit');
		$this->load->model('Supplier');
		$this->load->model('Customer');
		$this->load->model('Employee');
		$this->load->model('Giftcard');
		$this->load->model('Sale');
		$this->load->helper('cloud');
		$this->load->helper('text');
		$this->load->model('Appfile');
	}
	function payvantage()
		{
		$this->load->view("payvantage");

		}

		private function get_today_payment_breakdown($location_id)
		{
                $start_date = date('Y-m-d 00:00:00');
                $end_date = date('Y-m-d 23:59:59');

                $sales_payments_table = $this->db->dbprefix('sales_payments');
                $sales_table = $this->db->dbprefix('sales');

                $this->db->select("sp.payment_type, SUM(sp.payment_amount) as total", false);
                $this->db->from("{$sales_payments_table} AS sp");
                $this->db->join("{$sales_table} AS s", 's.sale_id = sp.sale_id');
                $this->db->where('s.sale_time >=', $start_date);
                $this->db->where('s.sale_time <=', $end_date);
                $this->db->where('s.location_id', $location_id);
                $this->db->where('s.deleted', 0);
                $this->db->group_by('sp.payment_type');

		$rows = $this->db->get()->result_array();

		$labels = array();
		$totals = array();

		foreach ($rows as $row)
		{
		$payment_type = $row['payment_type'];

		if (strpos($payment_type, ':') !== FALSE)
		{
		$payment_type = substr($payment_type, 0, strpos($payment_type, ':'));
		}

		if (strpos($payment_type, 'common_') !== FALSE)
		{
		$payment_type = lang($payment_type);
		}

		$labels[] = $payment_type;
		$totals[] = (float)$row['total'];
		}

		return array('labels' => $labels, 'totals' => $totals);
		}

		private function get_today_location_sales()
		{
                $start_date = date('Y-m-d 00:00:00');
                $end_date = date('Y-m-d 23:59:59');

                $sales_table = $this->db->dbprefix('sales');
                $locations_table = $this->db->dbprefix('locations');

                $this->db->select('l.name as location_name, SUM(s.total) as total', false);
                $this->db->from("{$sales_table} AS s");
                $this->db->join("{$locations_table} AS l", 's.location_id = l.location_id');
                $this->db->where('s.sale_time >=', $start_date);
                $this->db->where('s.sale_time <=', $end_date);
                $this->db->where('s.deleted', 0);
                $this->db->group_by('s.location_id');

		$rows = $this->db->get()->result_array();

		$labels = array();
		$totals = array();

		foreach ($rows as $row)
		{
		$labels[] = $row['location_name'];
		$totals[] = (float)$row['total'];
		}

		return array('labels' => $labels, 'totals' => $totals);
		}

function index($choose_location=0)
{
		require_once (APPPATH.'models/reports/Report.php');
		
		if (!$choose_location && $this->config->item('timeclock') && !$this->Employee->is_clocked_in() && !$this->Employee->get_logged_in_employee_info()->not_required_to_clock_in)
		{
			redirect('timeclocks');
		}


		$data['choose_location'] = $choose_location;
		
		$data['total_items']=$this->Item->count_all();
		$data['total_item_kits']=$this->Item_kit->count_all();
		$data['total_suppliers']=$this->Supplier->count_all();
		$data['total_customers']=$this->Customer->count_all($this->config->item('only_allow_current_location_customers') ? $this->Employee->get_logged_in_employee_current_location_id() : '');
		$data['total_employees']=$this->Employee->count_all();
		$data['total_locations']=$this->Location->count_all();
		$data['total_giftcards']=$this->Giftcard->count_all();
$data['total_sales']=$this->Sale->count_all();
$data['saved_reports'] = Report::get_saved_reports();

$current_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id());
$current_location_id = $this->Employee->get_logged_in_employee_current_location_id();
$data['payment_breakdown'] = $this->get_today_payment_breakdown($current_location_id);
$data['location_sales'] = $this->get_today_location_sales();
$data['message']  = "";
		
		if ($this->Employee->has_module_action_permission('reports', 'view_dashboard_stats', $this->Employee->get_logged_in_employee_info()->person_id))
		{	
			$data['month_sale'] = $this->sales_widget();
		}
		$this->load->helper('demo');
		$data['can_show_mercury_activate'] = (!is_on_demo_host() && !$this->config->item('mercury_activate_seen')) && !$this->Location->get_info_for_key('enable_credit_card_processing') && $this->config->item('branding_code') == 'phppointofsale';		
		$data['can_show_setup_wizard'] = !$this->config->item('shown_setup_wizard');
		$data['can_show_feedback_promotion'] = !$this->config->item('shown_feedback_message')  && $this->config->item('branding_code') == 'phppointofsale';		
		$data['can_show_reseller_promotion'] = !$this->config->item('reseller_activate_seen')  && $this->config->item('branding_code') == 'phppointofsale';
		if (is_on_phppos_host())
		{
			$this->lang->load('login');
			$site_db = $this->load->database('site', TRUE);
			
			if (!is_on_demo_host())
			{
				$data['announcement'] = get_cloud_announcement($site_db);
			}
			
			if (is_subscription_cancelled($site_db) || is_subscription_failed($site_db) || is_in_trial($site_db))
			{
				$data['cloud_customer_info'] = get_cloud_customer_info($site_db);
				
				if (is_in_trial($site_db))
				{
						$data['trial_on']  = TRUE;
				}
				elseif (is_subscription_failed($site_db))
				{
					$data['subscription_payment_failed']  = TRUE;
				}
				elseif (is_subscription_cancelled_within_grace_period($site_db))
				{
					$data['subscription_cancelled_within_5_days']  = TRUE;
				}
			}
		}
		
				
		$start_date = date('Y-m-d 00:0:00');
		$end_date = date('Y-m-d 23:59:59',strtotime('+6 months'));
		$this->db->select('locations.name as location_name, items.name, SUM(quantity_purchased) as quantity_expiring,items.size,receivings_items.expire_date, categories.id as category_id,categories.name as category, company_name, item_number, product_id, 
		'.$this->db->dbprefix('receivings_items').'.item_unit_price as cost_price, 
		IFNULL('.$this->db->dbprefix('location_items').'.unit_price, '.$this->db->dbprefix('items').'.unit_price) as unit_price,
		SUM(quantity) as quantity, 
		IFNULL('.$this->db->dbprefix('location_items').'.reorder_level, '.$this->db->dbprefix('items').'.reorder_level) as reorder_level, 
		items.description', FALSE);
		$this->db->from('items');
		$this->db->join('receivings_items', 'receivings_items.item_id = items.item_id');
		$this->db->join('receivings', 'receivings_items.receiving_id = receivings.receiving_id');
		$this->db->join('suppliers', 'items.supplier_id = suppliers.person_id', 'left outer');
		$this->db->join('categories', 'items.category_id = categories.id', 'left outer');
		$this->db->join('locations', 'locations.location_id = receivings.location_id');
		$this->db->join('location_items', "location_items.item_id = items.item_id and location_items.location_id = $current_location_id", 'left');

		$this->db->where('items.deleted', 0);
		$this->db->where('items.system_item',0);
		
		$this->db->where('receivings.location_id', $current_location_id);
			
		$this->db->where('receivings_items.expire_date >=', $start_date);
		$this->db->where('receivings_items.expire_date <=', $end_date);

		$this->db->group_by('receivings_items.receiving_id,receivings_items.item_id,receivings_items.line');
		$this->db->order_by('receivings_items.expire_date');
		
		$expire_result = $this->db->get()->result_array();
		$data['expiring_items'] = $expire_result;
		
		$data['ecommerce_realtime'] = $this->config->item('ecommerce_realtime');
		
		if (isset($site_db) && $site_db)
		{
			$site_db->close();
		}
		$this->load->view("home",$data);
	}
	
	function dismiss_setup_wizard()
	{
		$this->Appconfig->save('shown_setup_wizard',1);
	}
	
	function dismiss_feedback_message()
	{
		$this->Appconfig->save('shown_feedback_message',1);
	}

	function dismiss_mercury_message()
	{
		$this->Appconfig->mark_mercury_activate(true);
	}
	
	function dismiss_reseller_message()
	{
		$this->Appconfig->mark_reseller_message(true);		
	}
	
	function logout()
	{
		if (isset($_SESSION['samlNameId']) && $this->config->item('saml_single_logout_service'))
		{
			redirect('login/samlassertionconsumerservice?slo');
		}
		else
		{
			$this->Employee->logout();
		}
	}
	
	function set_employee_current_location_id()
	{
		$this->Employee->set_employee_current_location_id($this->input->post('employee_current_location_id'));
		
		//Clear out logged in register when we switch locations
		$this->Employee->set_employee_current_register_id(null);
	}

	function get_employee_current_location_id()
	{
		
		$current_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id());

		echo $current_location->current_announcement;

	}
	
	function keep_alive()
	{
		//Set keep alive session to prevent logging out
		$this->session->set_userdata("keep_alive",time());
		echo $this->session->userdata('keep_alive');
	}
	
	function set_fullscreen($on = 0)
	{
		$this->session->set_userdata("fullscreen",$on);		
	}
		
	function view_item_modal($item_id, $line= -1, $customer_id = NULL)
	{
		$this->lang->load('items');
		$this->lang->load('receivings');
		$this->load->model('Tier');
		$this->load->model('Category');
		$this->load->model('Manufacturer');
		$this->load->model('Tag');
		$this->load->model('Item_location');
		$this->load->model('Item_taxes_finder');
		$this->load->model('Item_location_taxes');
		$this->load->model('Receiving');
		$this->load->model('Item_taxes');
		$this->load->model('Additional_item_numbers');
		$this->load->model('Item_variations');
		$this->load->model('Item_variation_location');
		
		$data['redirect'] = $this->input->get('redirect');
			
		$data['item_info'] = $this->Item->get_info($item_id);
		$data['item_images'] = $this->Item->get_item_images($item_id);
		$data['item_variations'] = $this->Item_variations->get_variations($item_id);
		$data['item_variation_location'] = $this->Item_variation_location->get_variations_with_quantity($item_id);
		
		$data['additional_item_numbers'] = $this->Additional_item_numbers->get_item_numbers($item_id);
		
		$data['tier_prices'] = array();
		
		foreach($this->Tier->get_all()->result() as $tier)
		{
			$tier_id = $tier->id;
			$tier_price = $this->Item->get_tier_price_row($tier_id,$item_id);
			
			if ($tier_price)
			{
				$value = $tier_price->unit_price !== NULL ? to_currency($tier_price->unit_price) : $tier_price->percent_off.'%';			
				$data['tier_prices'][] = array('name' => $tier->name, 'value' => $value);
			}
		}
		
		$data['category'] = $this->Category->get_full_path($data['item_info']->category_id);
		$data['manufacturer'] = $this->Manufacturer->get_info($data['item_info']->manufacturer_id)->name;
		$logged_in_employee_info=$this->Employee->get_logged_in_employee_info();
		
	
		if ($this->Employee->has_module_action_permission('items', 'view_inventory_at_all_locations', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			//Make all locations authed for modal to see all locations inventory
			$authed_locations = array();
			
			foreach($this->Location->get_all()->result_array() as $all_loc)
			{
				$authed_locations[] = $all_loc['location_id'];
			}
		}
		else
		{
			$authed_locations = $this->Employee->get_authenticated_location_ids($logged_in_employee_info->person_id);
		}
		
		$data['item_location_info']=$this->Item_location->get_info($item_id);
		
		$data['authed_locations'] = $authed_locations;
		foreach($authed_locations as $authed_location_id)
		{
			$data['item_location_info_all'][$authed_location_id]=$this->Item_location->get_info($item_id,$authed_location_id);
			$data['reorder_level'][$authed_location_id] = ($data['item_location_info_all'][$authed_location_id] && $data['item_location_info_all'][$authed_location_id]->reorder_level) ? $data['item_location_info_all'][$authed_location_id]->reorder_level : $data['item_info']->reorder_level;
		}
		foreach($authed_locations as $authed_location_id)
		{
			foreach(array_keys($data['item_variations']) as $variation_id)
			{
				$data['item_variation_location_info_all'][$authed_location_id][$variation_id]=$this->Item_variation_location->get_info($variation_id,$authed_location_id);
			}
		}
		
		$data['item_tax_info']=$this->Item_taxes_finder->get_info($item_id);
		
		if ($supplier_id = $this->Item->get_info($item_id)->supplier_id)
		{
			$supplier = $this->Supplier->get_info($supplier_id);
			$data['supplier'] = $supplier->company_name . ' ('.$supplier->first_name.' '.$supplier->last_name.')';
		}
		
		$data['suspended_receivings'] = $this->Receiving->get_suspended_receivings_for_item($item_id);		

		if($customer_id) {
			$data['customer_sales_history'] = $this->Sale->get_sales_for_customer_by_items($item_id, $customer_id);
		}

		$data['customer_id'] = $customer_id;
		$data['line'] = $line;

		$this->load->view("items/items_modal",$data);
	}
	
	// Function to show the modal window when clicked on kit name
	function view_item_kit_modal($item_kit_id)
	{
		$this->lang->load('item_kits');
		$this->lang->load('items');
		$this->lang->load('receivings');
		$this->load->model('Item');
		$this->load->model('Item_kit');
		$this->load->model('Item_kit_items');
		$this->load->model('Tier');
		$this->load->model('Category');
		$this->load->model('Manufacturer');
		$this->load->model('Tag');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_taxes_finder');
		$this->load->model('Item_kit_location_taxes');
		$this->load->model('Receiving');
		$this->load->model('Item_kit_taxes');
		
		$data['redirect'] = $this->input->get('redirect');
		
		// Fetching Kit information using kit_id
		$data['item_kit_info']=$this->Item_kit->get_info($item_kit_id);
		
		$tier_prices = $this->Item->get_all_tiers_prices();
		
		$data['tier_prices'] = array();
		foreach($this->Tier->get_all()->result() as $tier)
		{
			$tier_id = $tier->id;
			$tier_price = $this->Item_kit->get_tier_price_row($tier_id,$item_kit_id);
			
			if ($tier_price)
			{
				$value = $tier_price->unit_price !== NULL ? to_currency($tier_price->unit_price) : $tier_price->percent_off.'%';			
				$data['tier_prices'][] = array('name' => $tier->name, 'value' => $value);
			}
		}
		
		$data['manufacturer'] = $this->Manufacturer->get_info($data['item_kit_info']->manufacturer_id)->name;
		$data['category'] = $this->Category->get_full_path($data['item_kit_info']->category_id);
		
		//$data['item_kit_location_info']=$this->Item_kit_location->get_info($item_kit_id);
		
		
		$this->load->view("item_kits/items_modal",$data);
	}

	function sales_widget($type = 'monthly')
	{
		$day = array();
		$count = array();

		if($type == 'monthly')
		{
			$start_date = date('Y-m-d', mktime(0,0,0,date("m"),1,date("Y"))).' 00:00:00';
			$end_date = date('Y-m-d').' 23:59:59';
		}
		else
		{
			$current_week = strtotime("-0 week +1 day");
			$current_start_week = strtotime("last monday midnight",$current_week);
			$current_end_week = strtotime("next sunday",$current_start_week);

			$start_date = date("Y-m-d",$current_start_week).' 00:00:00';
			$end_date = date("Y-m-d",$current_end_week).' 23:59:59';
		}

		$return = $this->Sale->get_sales_amount_for_range($start_date, $end_date);	

		foreach ($return as $key => $value) {
			if($type == 'monthly')
			{
				$day[] = date('d',strtotime($value['sale_date']));	
			}
			else
			{
				$day[] = lang('common_'.strtolower(date('l',strtotime($value['sale_date']))));
			}
			$amount[] = $value['sale_amount'];
		}	

		
		if(empty($return))
		{
			$day = array(0);
			$amount = array(0);
			$data['message'] = lang('common_not_found');
		}
		$data['day'] = json_encode($day);
		$data['amount'] = json_encode($amount);
		
		if($this->input->is_ajax_request())
		{
			if(empty($return))
			{
				echo json_encode(array('message'=>lang('common_not_found')));
				die();
			}
		    echo json_encode(array('day'=>$day,'amount'=>$amount));
		    die();
		}
		return $data;
	}
	
	function enable_test_mode()
	{
		$this->load->helper('demo');
		if (!is_on_demo_host())
		{
			$this->Appconfig->save('test_mode','1');
		}
		redirect('home');
	}
	
	function disable_test_mode()
	{
		$this->load->helper('demo');
		if (!is_on_demo_host())
		{
			$this->Appconfig->save('test_mode','0');
		}
		redirect('home');	
	}
	
	function dismiss_test_mode()
	{
		$this->Appconfig->save('hide_test_mode_home','1');		
	}
	
	
	function get_ecommerce_sync_progress()
	{	
		if ($this->config->item("ecommerce_platform"))
		{
			require_once (APPPATH."models/interfaces/Ecom.php");
			$ecom_model = Ecom::get_ecom_model();
			
			$progress = $ecom_model->get_sync_progress();
			echo json_encode(array('running' => $this->Appconfig->get_raw_ecommerce_cron_running() ? $this->Appconfig->get_raw_ecommerce_cron_running() : FALSE,'percent_complete' => $progress['percent_complete'],'message' => $progress['message']));
		}
		else
		{
			echo json_encode(array('running' => FALSE,'progress' =>0,'message' => ''));
		}
	}
	
	function get_qb_sync_progress()
	{	
		$this->load->model('QuickbooksModel');
		$progress = $this->QuickbooksModel->get_sync_progress();
		echo json_encode(array('running' => $this->Appconfig->get_raw_qb_cron_running() ? $this->Appconfig->get_raw_qb_cron_running() : FALSE,'percent_complete' => $progress['percent_complete'],'message' => $progress['message']));
	}
	
	function reset_barcode_labels()
	{
		$this->load->model('Appconfig');
		$this->Appconfig->save('barcode_width','');
		$this->Appconfig->save('barcode_height','');
		$this->Appconfig->save('scale','');
		$this->Appconfig->save('thickness','');
		$this->Appconfig->save('font_size','');
		$this->Appconfig->save('overall_font_size','');
		$this->Appconfig->save('zerofill_barcode','');
		redirect($_SERVER['HTTP_REFERER'] ? strtok($_SERVER['HTTP_REFERER'], '?') : site_url('items'));
	}
	
	function save_barcode_settings()
	{
		$this->load->model('Appconfig');
		$saved_name = $this->input->get('saved_name');
		foreach($this->input->get() as $var=>$value)
		{
			$this->Appconfig->save($var,$value);
		}
		
		if ($saved_name)
		{
			$this->Appconfig->save('barcoded_labels_'.$saved_name,serialize($this->input->get()));
		}
	}
	
	function save_scroll()
	{
		$save_scroll = $this->input->get('scroll_to');
		$this->session->set_userdata('scroll_to',$save_scroll);
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
	
	function offline($ignore_timestamp='0')
	{
		$this->load->model('Appconfig');
		
		$data  = array();
		
		$data['default_payment_type'] = $this->config->item('default_payment_type') ? $this->config->item('default_payment_type') : lang('common_cash');
		
		$payment_options=array(
			lang('common_cash') => lang('common_cash'),
			lang('common_check') => lang('common_check'),
			lang('common_debit') => lang('common_debit'),
			lang('common_credit') => lang('common_credit')
			);
			
		foreach($this->Appconfig->get_additional_payment_types() as $additional_payment_type)
		{
			$payment_options[$additional_payment_type] = $additional_payment_type;
		}
			
		
		$data['payment_options'] = $payment_options;
		
		$this->load->view('offline',$data);
	}
	
	function datatable_language()
	{
		$this->load->model('Employee');
		$table_lang = $this->Employee->datatable_language();
		echo $table_lang;
	}
	
	function sync_wgp_inventory_search()
	{
		session_write_close();
		$search = $this->input->get('term');
		$url_parameter = rawurlencode($this->config->item('wgp_integration_pkey'));
		$url_userid = rawurlencode($this->config->item('wgp_integration_userid'));
		$url = "https://api.wgp.com/wgpjson.aspx?reqtype=user-sku-price&wgpcustid=".$url_userid."&sku=".$search."&pkey=".$url_parameter;
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$headers = array(
		"Accept: application/json",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		
		$resp = curl_exec($curl);
		curl_close($curl);
		$ret_array = json_decode($resp, true);
		
		$search_item_list = array();

		foreach($ret_array as $item_info)
		{

			//there is a match add that item to the database and then add that same item to the sale
			$item_data = array(
				'name'=>$item_info['name'],
				'cost_price'=>((isset($item_info['tier_price']) && $item_info['tier_price']) ? $item_info['tier_price'] : 0),
				'unit_price'=>((isset($item_info['tier_price']) && $item_info['tier_price']) ? $item_info['tier_price'] : 0),
				'product_id'=> $item_info['SKU']
			);

			if(isset($item_info['itemId'])){
				$phppos_additional_item_numbers = array(
					"WGP-".$item_info['itemId']
				);
			}else{
				$phppos_additional_item_numbers = array();
			}

			$item_image_link = "";
			if(isset($item_info['image_url'])){
				$item_image_link = $item_info['image_url'];
			}

			$search_item_id = $this->Item->lookup_item_id($item_data['product_id'], array('item_id', 'item_number'));
			$new_item = 0;
			if($search_item_id == false){
				$new_item = 1;
			}
			$this->Item->save($item_data, $search_item_id);
			$search_item_id = $this->Item->lookup_item_id($item_data['product_id'], array('item_id', 'item_number'));

			if($new_item == 1){

				$image_contents = @file_get_contents($item_image_link);

				if ($image_contents)
				{
					$image_file_id = $this->Appfile->save(basename($item_image_link), $image_contents);
				}

				if (isset($image_file_id))
				{
					$this->Item->add_image($search_item_id, $image_file_id);
					$this->Item->set_main_image($search_item_id, $image_file_id);
				}


				if(count($phppos_additional_item_numbers) > 0){
					//additional item number process
					$this->Additional_item_numbers->save($search_item_id, $phppos_additional_item_numbers);
				}

				$supplier_id = $this->Supplier->find_supplier_id('WGP');
			}

			$this->set_supplier_item("WGP", $search_item_id);

			$item_data1 = $this->Item->get_info($search_item_id, false);
			$item_data2 = array(
				'category' => null,
				'default_supplier' => [],
				'image' => $item_data1->main_image_id ?  cacheable_app_file_url($item_data1->main_image_id) : base_url()."assets/img/item.png",
				'quantity' => "Not set",
				'secondary_suppliers' => [],
				'supplier_name' => "WGP",

				'item_number' => $item_data1->item_number,
				'cost_price'=>to_currency($item_data1->cost_price),
				'unit_price'=>to_currency($item_data1->unit_price),
				'label' => $item_data1->product_id." (".$item_data1->name.") - ".to_currency($item_data1->unit_price),
				'product_id'=>$item_data1->product_id,

				'value' => $search_item_id,
			);
			array_push($search_item_list, $item_data2);
			break;

		}

		echo json_encode(H($search_item_list));
		
	}
	
	function sync_ig_item_search(){
		session_write_close();

		// $search_key = "IP5S-Assem-Premium-Black";
		$search_key = $this->input->get("term");
		$search_key = rawurlencode($search_key);

		$url = "https://www.injuredgadgets.com/rest/V1/pos/products?searchCriteria[filterGroups][0][filters][0][field]=sku&searchCriteria[filterGroups][0][filters][0][value]=".$search_key."&[filterGroups][0][filters][0][conditionType]=eq&searchCriteria[pageSize]=20&searchCriteria[currentPage]=1";

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$headers = array(
		   "Accept: application/json",
		   "Authorization: Bearer ".$this->config->item('ig_api_bearer_token'),
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		
		$resp = curl_exec($curl);
		curl_close($curl);
		// var_dump($resp);
		$ret_array = json_decode($resp, true);

		if(isset($ret_array->message) && (strpos($ret_array->message, "The consumer isn't authorized to access") > -1)){
			echo json_encode(array('success'=>false,'message'=>lang('items_sync_ig_bestsellers_failed')));
		}if(isset($ret_array->message) && $ret_array->message == "Request does not match any route."){
			echo json_encode(array('success'=>false,'message'=>lang('items_sync_ig_bestsellers_failed')));
		}else{

			$search_item_list = array();
			if(count($ret_array) == 0){
				//upc search
				$url = "https://www.injuredgadgets.com/rest/V1/pos/products?searchCriteria[filterGroups][0][filters][0][field]=upc&searchCriteria[filterGroups][0][filters][0][value]=".$search_key."&[filterGroups][0][filters][0][conditionType]=eq&searchCriteria[pageSize]=20&searchCriteria[currentPage]=1";
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				
				$headers = array(
				   "Accept: application/json",
				   "Authorization: Bearer ".$this->config->item('ig_api_bearer_token'),
				);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				//for debug only!
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				
				$resp = curl_exec($curl);
				curl_close($curl);
				// var_dump($resp);
				$ret_array = json_decode($resp, true);
			}

			foreach($ret_array as $item_info){

				//there is a match add that item to the database and then add that same item to the sale
				$item_data = array(
					'name'=>$item_info['name'],
					'item_number'=>((isset($item_info['upc']) && $item_info['upc']) ? $item_info['upc'] : NULL),
					'cost_price'=>((isset($item_info['price']) && $item_info['price']) ? $item_info['price'] : 0),
					'unit_price'=>((isset($item_info['price']) && $item_info['price']) ? $item_info['price'] : 0),
					'product_id'=> $item_info['sku']
				);

				if(isset($item_info['entity_id'])){
					$phppos_additional_item_numbers = array(
						"IG-".$item_info['entity_id']
					);
				}else{
					$phppos_additional_item_numbers = array();
				}

				$item_image = "";
				if(isset($item_info['image'])){
					$item_image = $item_info['image'];
				}

				$search_item_id = $this->Item->lookup_item_id($item_data['product_id'], array('item_id', 'item_number'));
				$new_item = 0;
				if($search_item_id == false){
					$new_item = 1;
				}
				$this->Item->save($item_data, $search_item_id);
				$search_item_id = $this->Item->lookup_item_id($item_data['product_id'], array('item_id', 'item_number'));

				if($new_item == 1){

					$item_image_link = "https://www.injuredgadgets.com/pub/media/catalog/product".$item_image;

					$image_contents = @file_get_contents($item_image_link);

					if ($image_contents)
					{
						$image_file_id = $this->Appfile->save(basename($item_image), $image_contents);
					}

					if (isset($image_file_id))
					{
						$this->Item->add_image($search_item_id, $image_file_id);
						$this->Item->set_main_image($search_item_id, $image_file_id);
					}

					//update category : skip now

					if(count($phppos_additional_item_numbers) > 0){
						//additional item number process
						$this->Additional_item_numbers->save($search_item_id, $phppos_additional_item_numbers);
					}

					$supplier_id = $this->Supplier->find_supplier_id('Injured Gadgets');
				}

				$this->set_supplier_item("Injured Gadgets", $search_item_id);

				$item_data1 = $this->Item->get_info($search_item_id, false);
				$item_data2 = array(
					'category' => null,
					'default_supplier' => [],
					'image' => $item_data1->main_image_id ?  cacheable_app_file_url($item_data1->main_image_id) : base_url()."assets/img/item.png",
					'quantity' => "Not set",
					'secondary_suppliers' => [],
					'supplier_name' => "Injured Gadgets",

					'item_number' => $item_data1->item_number,
					'cost_price'=>to_currency($item_data1->cost_price),
					'unit_price'=>to_currency($item_data1->unit_price),
					'label' => $item_data1->product_id." (".$item_data1->name.") - ".to_currency($item_data1->unit_price),
					'product_id'=>$item_data1->product_id,

					'value' => $search_item_id,
				);
				array_push($search_item_list, $item_data2);

			}

			echo json_encode(H($search_item_list));
		}
	}

	function sync_p4_item_search(){
		session_write_close();

		$search_key = $this->input->get("term");
		$search_key = rawurlencode($search_key);

		$url = "https://parts4cells.com/rest/all/V1/products?searchCriteria[currentPage]=1&searchCriteria[pageSize]=20&searchCriteria[filterGroups][0][filters][0][field]=sku&searchCriteria[filterGroups][0][filters][0][value]=".$search_key."&searchCriteria[filterGroups][0][filters][0][conditionType]=eq";

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$headers = array(
		   "Accept: application/json",
		   "Authorization: Bearer ".$this->config->item('p4_api_bearer_token'),
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		
		$resp = curl_exec($curl);
		curl_close($curl);
		// var_dump($resp);
		$ret_array = json_decode($resp, true);

		if(isset($ret_array['message']) && (strpos($ret_array['message'], "The consumer isn't authorized to access") > -1)){
			echo json_encode(array('success'=>false,'message'=>lang('items_sync_p4_inventory_failed')));
			exit(0);
		}
		
		if(isset($ret_array['message']) && $ret_array['message'] == "Request does not match any route."){
			echo json_encode(array('success'=>false,'message'=>lang('items_sync_p4_inventory_failed')));
			exit(0);

		}else{

			$search_item_list = array();
			// if(count($ret_array) == 0){
			// 	//upc search
			// 	$url = "https://parts4cells.com/rest/all/V1/products?searchCriteria[filterGroups][0][filters][0][field]=upc&searchCriteria[filterGroups][0][filters][0][value]=".$search_key."&[filterGroups][0][filters][0][conditionType]=eq&searchCriteria[pageSize]=20&searchCriteria[currentPage]=1";
			// 	$curl = curl_init($url);
			// 	curl_setopt($curl, CURLOPT_URL, $url);
			// 	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				
			// 	$headers = array(
			// 	   "Accept: application/json",
			// 	   "Authorization: Bearer ".$this->config->item('p4_api_bearer_token'),
			// 	);
			// 	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			// 	//for debug only!
			// 	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			// 	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				
			// 	$resp = curl_exec($curl);
			// 	curl_close($curl);
			// 	// var_dump($resp);
			// 	$ret_array = json_decode($resp, true);
			// }

			if(!isset($ret_array['items'])){
				echo json_encode(array('success'=>false,'message'=>"API Error."));
				exit(0);
			}

			foreach($ret_array['items'] as $item_info){

				//there is a match add that item to the database and then add that same item to the sale
				$item_data = array(
					'name'=>$item_info['name'],
					'item_number'=>((isset($item_info['upc']) && $item_info['upc']) ? $item_info['upc'] : NULL),
					'cost_price'=>((isset($item_info['price']) && $item_info['price']) ? $item_info['price'] : 0),
					'unit_price'=>((isset($item_info['price']) && $item_info['price']) ? $item_info['price'] : 0),
					'product_id'=> $item_info['sku'],
				);

				if(isset($item_info['entity_id'])){
					$phppos_additional_item_numbers = array(
						"P4-".$item_info['entity_id']
					);
				}else{
					$phppos_additional_item_numbers = array();
				}

				$item_image = "";
				$item_description = "";
				if(isset($item_info['custom_attributes'])){
					foreach($item_info['custom_attributes'] as $item_attribute){
						if($item_attribute['attribute_code'] == "image"){
							$item_image = $item_attribute['value'];
						}
						if($item_attribute['attribute_code'] == "description"){
							$item_description = $item_attribute['value'];
						}
					}
				}
				$item_data['description'] = $item_description;

				$search_item_id = $this->Item->lookup_item_id($item_data['product_id'], array('item_id', 'item_number'));
				$new_item = 0;
				if($search_item_id == false){
					$new_item = 1;
				}
				$this->Item->save($item_data, $search_item_id);
				$search_item_id = $this->Item->lookup_item_id($item_data['product_id'], array('item_id', 'item_number'));

				if($new_item == 1){

					$item_image_link = "https://parts4cells.com/media/catalog/product".$item_image;

					$image_contents = @file_get_contents($item_image_link);

					if ($image_contents)
					{
						$image_file_id = $this->Appfile->save(basename($item_image), $image_contents);
					}

					if (isset($image_file_id))
					{
						$this->Item->add_image($search_item_id, $image_file_id);
						$this->Item->set_main_image($search_item_id, $image_file_id);
					}

					//update category : skip now

					if(count($phppos_additional_item_numbers) > 0){
						//additional item number process
						$this->Additional_item_numbers->save($search_item_id, $phppos_additional_item_numbers);
					}

				}

				$this->set_supplier_item("Parts4cells", $search_item_id);

				$item_data1 = $this->Item->get_info($search_item_id, false);
				$item_data2 = array(
					'category' => null,
					'default_supplier' => [],
					'image' => $item_data1->main_image_id ?  cacheable_app_file_url($item_data1->main_image_id) : base_url()."assets/img/item.png",
					'quantity' => "Not set",
					'secondary_suppliers' => [],
					'supplier_name' => "Parts4cells",

					'item_number' => $item_data1->item_number,
					'cost_price'=>to_currency($item_data1->cost_price),
					'unit_price'=>to_currency($item_data1->unit_price),
					'label' => $item_data1->product_id." (".$item_data1->name.") - ".to_currency($item_data1->unit_price),
					'product_id'=>$item_data1->product_id,

					'value' => $search_item_id,
				);
				array_push($search_item_list, $item_data2);
			}

			echo json_encode(H($search_item_list));
		}
	}
	
	private function set_supplier_item($company_name, $item_id){

		$this->load->model('Supplier');

		$supplier_id = $this->Supplier->find_supplier_id($company_name);
		if(!$supplier_id){
			$person_data = array('first_name' => '', 'last_name' => '');
			$supplier_data = array('company_name' => $company_name);
			$this->Supplier->save_supplier($person_data, $supplier_data);
			$supplier_id = $this->Supplier->find_supplier_id($company_name);
		}

		if($supplier_id){
			$item_data = array('supplier_id' => $supplier_id);
			$this->Item->save($item_data, $item_id);
		}
	}
	
	function async_external_inventory_updates()
	{
		$async_updates = $_SESSION['async_inventory_updates'];
		unset($_SESSION['async_inventory_updates']);
		session_write_close();
		$this->load->model('Inventory');
		foreach($async_updates as $inventory_update)
		{
			$this->Inventory->external_inventory_update($inventory_update);
		}
	}
	
}
?>