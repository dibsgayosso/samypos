<?php
// require_once (APPPATH."models/cart/PHPPOSCartSale.php");
require_once (APPPATH."libraries/Fatoora.php");
require_once (APPPATH."models/cart/PHPPOSCartSale.php");

define ('FATOORA_TRY_TIMES', 3); // Cron job trying times
define ('CRON_SYNC_LAST_DAYS', 7); // set 7 days 
class Zatca extends MY_Controller
{
	public $cart;
	public $cron_log;

	function __construct()
    {
		$this->cron_log ='';
		set_time_limit(0);
		ini_set('max_input_time','-1');
        ini_set('memory_limit', '1024M');
        parent::__construct();
        if (!is_cli()) //Running from web should have store config permissions
        {
            $this->load->model('Employee');
            $this->load->model('Location');
            if (!$this->Employee->is_logged_in()) {
                redirect('login?continue=' . rawurlencode(uri_string() . '?' . $_SERVER['QUERY_STRING']));
            }

            if (!$this->Employee->has_module_permission('config', $this->Employee->get_logged_in_employee_info()->person_id)) {
                redirect('no_access/config');
            }
        }

		$this->load->model('Sale');
		$this->load->helper('text');
		$this->load->helper('date');
		$this->load->helper('items');

		$this->cart = PHPPOSCartSale::get_instance('sale');
	}

	function clean_zatca_invoice_history(){
		$retR = $this->Invoice->remove_zatca_invoices();
		if($retR){
			$ret =  array(
				'state' => 1,
				'message'=> "The database has been successfully cleaned up.",
			);
			echo json_encode($ret);
			exit();
		}
		$ret =  array(
			'state' => 0,
			'message'=> "There was an error in the database.",
		);
		echo json_encode($ret);
		exit();
	}

	// config functions
	function log($msg)
	{
		$msg = date(get_date_format().' h:i:s ').': '.$msg."\n"; 
		if (is_cli())
		{
			echo $msg;
		}
		$this->cron_log.=$msg;
	}

	function save_log()
	{
	    $CI =& get_instance();	
		$CI->load->model("Appfile");
		$this->Appfile->save('zatca_log.txt',$this->cron_log." \n ",'+72 hours');
	}

	function get_sync_progress()
	{
		$ret =  array(
			'percent_complete' => $this->config->item('zatca_sync_percent_complete'),
			'location_id' => $this->config->item('zatca_sync_location_id'),
			'sale_id' => $this->config->item('zatca_sync_sale_id'),
			'message'=> $this->config->item('zatca_sync_message'),
		);
		echo json_encode($ret);
		exit();
	}

	function update_sync_progress($progress = 0, $location_id = "", $sale_id = "", $message = "")
	{
		$this->Appconfig->save('zatca_sync_percent_complete', $progress);
		$this->Appconfig->save('zatca_sync_location_id', $location_id);
		$this->Appconfig->save('zatca_sync_sale_id', $sale_id);
		$this->Appconfig->save('zatca_sync_message', $message ? $message : '');
	}

	function get_unreported_sales_list($location_id, $count = NULL){
		$last_report_sale_id = $this->Invoice->get_zatca_last_report_sale_id($location_id);
		$from_date = "";
		if($last_report_sale_id){
			$sale_info = $this->Sale->get_info($last_report_sale_id)->row_array();
			$from_date = $sale_info['sale_time'];
		}else{
			$six_days_ago = date('Y-m-d', mktime(0,0,0,date("m"),date("d")-CRON_SYNC_LAST_DAYS,date("Y")));				
			$from_date = $six_days_ago;
		}
		
		$sales_list = $this->Invoice->get_zatca_invoice_unreported_sales($location_id, $from_date);
		if($count == NULL) return $sales_list;
		return array_slice($sales_list, 0, $count);
	}

	//step - 1
	function invoice_generate_xml(){
		session_write_close();

		if ($this->Appconfig->get_raw_zatca_cron_running() && $this->Appconfig->zatca_has_run_recently()) {
			echo json_encode(array('state' => 0, 'message' => lang('common_ecommerce_running')));
			exit();
		}

		// $sale_id = $this->input->post('sale_id');
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$sales_list = $this->get_unreported_sales_list($location_id, 1);
		if( count($sales_list) == 0 ){
			$ret = array(
				'state' => 0,
				'message' => "There's no unreported sales",
			);
			echo json_encode($ret);
			exit();
		}
		$sale_id = $sales_list[0]['sale_id'];
		$params = array(
			'sale_id' => $sale_id,
		);

		$ret = $this->invoice_generate_xml_process($params);

		echo json_encode($ret);
		exit();
	}

	//step - 2
	function invoice_sign(){
		session_write_close();
		if ($this->Appconfig->get_raw_zatca_cron_running() && $this->Appconfig->zatca_has_run_recently()) {
			echo json_encode(array('state' => 0, 'message' => lang('common_ecommerce_running')));
			exit();
		}

		// $sale_id = $this->input->post('sale_id');
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$sales_list = $this->get_unreported_sales_list($location_id, 1);
		if( count($sales_list) == 0 ){
			$ret = array(
				'state' => 0,
				'message' => "There's no unreported sales",
			);
			echo json_encode($ret);
			exit();
		}
		$sale_id = $sales_list[0]['sale_id'];
		$params = array(
			'sale_id' => $sale_id,
		);
		$ret = $this->invoice_sign_process($params);
		echo json_encode($ret);
		exit();
	}

	//step - 3
	function invoice_validate(){
		session_write_close();
		if ($this->Appconfig->get_raw_zatca_cron_running() && $this->Appconfig->zatca_has_run_recently()) {
			echo json_encode(array('state' => 0, 'message' => lang('common_ecommerce_running')));
			exit();
		}

		// $sale_id = $this->input->post('sale_id');
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$sales_list = $this->get_unreported_sales_list($location_id, 1);
		if( count($sales_list) == 0 ){
			$ret = array(
				'state' => 0,
				'message' => "There's no unreported sales",
			);
			echo json_encode($ret);
			exit();
		}
		$sale_id = $sales_list[0]['sale_id'];
		$params = array(
			'sale_id' => $sale_id,
		);
		$ret = $this->invoice_validate_process($params);
		echo json_encode($ret);
		exit();
	}

	//step - 4
	function invoice_generate_request(){
		session_write_close();
		if ($this->Appconfig->get_raw_zatca_cron_running() && $this->Appconfig->zatca_has_run_recently()) {
			echo json_encode(array('state' => 0, 'message' => lang('common_ecommerce_running')));
			exit();
		}

		// $sale_id = $this->input->post('sale_id');
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$sales_list = $this->get_unreported_sales_list($location_id, 1);
		if( count($sales_list) == 0 ){
			$ret = array(
				'state' => 0,
				'message' => "There's no unreported sales",
			);
			echo json_encode($ret);
			exit();
		}
		$sale_id = $sales_list[0]['sale_id'];

		$params = array(
			'sale_id' => $sale_id,
		);
		$ret = $this->invoice_generate_request_process ($params);
		echo json_encode($ret);
		exit();
	}

	//step - 5
	function invoice_compliance(){
		session_write_close();
		if ($this->Appconfig->get_raw_zatca_cron_running() && $this->Appconfig->zatca_has_run_recently()) {
			echo json_encode(array('state' => 0, 'message' => lang('common_ecommerce_running')));
			exit();
		}

		// $sale_id = $this->input->post('sale_id');
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$sales_list = $this->get_unreported_sales_list($location_id, 1);
		if( count($sales_list) == 0 ){
			$ret = array(
				'state' => 0,
				'message' => "There's no unreported sales",
			);
			echo json_encode($ret);
			exit();
		}
		$sale_id = $sales_list[0]['sale_id'];

		$params = array(
			'sale_id' => $sale_id,
		);
		$ret = $this->invoice_compliance_process($params);
		echo json_encode($ret);
		exit();
	}

	//step - 6
	function invoice_report(){
		session_write_close();
		if ($this->Appconfig->get_raw_zatca_cron_running() && $this->Appconfig->zatca_has_run_recently()) {
			echo json_encode(array('state' => 0, 'message' => lang('common_ecommerce_running')));
			exit();
		}

		// $sale_id = $this->input->post('sale_id');
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$sales_list = $this->get_unreported_sales_list($location_id, 1);
		if( count($sales_list) == 0 ){
			$ret = array(
				'state' => 0,
				'message' => "There's no unreported sales",
			);
			echo json_encode($ret);
			exit();
		}
		$sale_id = $sales_list[0]['sale_id'];
		$clearance_status = $this->input->post('clearance_status');

		$params = array(
			'sale_id' => $sale_id,
			'clearance_status' => $clearance_status,
		);
		$ret = $this->invoice_report_process($params);
		echo json_encode($ret);
		exit();
	}

	//step - 7
	function invoice_clearance(){
		session_write_close();
		if ($this->Appconfig->get_raw_zatca_cron_running() && $this->Appconfig->zatca_has_run_recently()) {
			echo json_encode(array('state' => 0, 'message' => lang('common_ecommerce_running')));
			exit();
		}

		// $sale_id = $this->input->post('sale_id');
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$sales_list = $this->get_unreported_sales_list($location_id, 1);
		if( count($sales_list) == 0 ){
			$ret = array(
				'state' => 0,
				'message' => "There's no unreported sales",
			);
			echo json_encode($ret);
			exit();
		}
		$sale_id = $sales_list[0]['sale_id'];

		$params = array(
			'sale_id' => $sale_id,
		);
		$ret = $this->invoice_clearance_process($params);
		echo json_encode($ret);
		exit();
	}

	//process step - 1
	private function invoice_generate_xml_process($params, $cron = 0){

		$sale_id = $params['sale_id'];
		$sale_info = $this->Sale->get_info($sale_id)->row_array();

		$location_id = $sale_info['location_id'];
		$customer_id = $sale_info['customer_id'];

		$this->lang->load('customers');
		$customer_info = $this->Customer->get_info($customer_id);
		$customer_zatca = $this->Customer->get_zatca($customer_id);

		$ret = array(
			'state' => 0,
			'response'=>"",
			'message' => ""
		);

		$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);
		$pih =  $this->Invoice->get_zatca_invoice_pih($location_id);

		$invoice_data = array(
			'sale_id' => $sale_id,
			'id' => ($this->config->item('sale_prefix') ? $this->config->item('sale_prefix') : 'POS').'-'.$sale_id,
			'UUID' => Fatoora::getUUID(), //random
			'issue_date' => new \DateTime(),
			'issue_time' => new \DateTime(),
		);

		if($sale_info['ref_sale_id']){
			if($sale_info['total_quantity_purchased'] < 0){
				$invoice_data['invoice_type_code'] = '381'; //credit notes
			} else {
				$invoice_data['invoice_type_code'] = '383'; //debit notes
			}

			$ref_zatca_invoice = $this->Invoice->get_zatca_invoice_by_sale_id($sale_info['ref_sale_id']);
			$invoice_data['billing_reference'] = array(
				'invoice_document_reference' => array(
					'id' => json_decode($ref_zatca_invoice['invoice_data'], true)['id'],
					'issue_date' => $ref_zatca_invoice['issue_date']
				)
			);
		}else{
			$invoice_data['invoice_type_code'] = '388'; //Tax invoice
		}

		$invoice_subtype = "0000000";
		// NNPNESB

		if(strlen(trim($customer_info->company_name)) > 0){
			// "standard";
			$invoice_subtype[0] = 0;
			$invoice_subtype[1] = 1;
		}else{
			// "simplified";
			$invoice_subtype[0] = 0;
			$invoice_subtype[1] = 2;
		}
		$invoice_data['invoice_subtype'] = $invoice_subtype;

		$invoice_accounting_supplier_party = array(
			'party_identification' => array(
				'id' => $location_zatca_config['seller_id'], //'324223432432432' check config
				'scheme_id' => $location_zatca_config['seller_scheme_id'],
			),
			'postal_address' => array(
				'country' => $location_zatca_config['seller_party_postal_country'],
				'street_name' => $location_zatca_config['seller_party_postal_street_name'],
				'building_number' => $location_zatca_config['seller_party_postal_building_number'],
				'plot_id' => $location_zatca_config['seller_party_postal_plot_id'],
				'city_subdivision_name' => $location_zatca_config['seller_party_postal_district'],
				'city_name' => $location_zatca_config['seller_party_postal_city'],
				'postal_zone' => $location_zatca_config['seller_party_postal_code'],
			),
			'party_tax_scheme' => array(
				'company_id' => $location_zatca_config['csr_organization_identifier'],
				'tax_scheme' => 'VAT',
			),
			'party_legal_entity' => array(
				'registration_name' => $this->config->item('company'),
			),
		);
		$invoice_data['accounting_supplier_party'] = $invoice_accounting_supplier_party;

		$customer_registration_name = "";
		if($customer_info->company_name){
			$customer_registration_name = $customer_info->company_name;
		}else if($customer_info->first_name){
			$customer_registration_name = $customer_info->first_name.' '.$customer_info->last_name;
		}

		$invoice_accounting_customer_party = array(
			'party_identification' => array(
				'id' => isset($customer_zatca['buyer_id']) ? $customer_zatca['buyer_id'] : "",
				'scheme_id' => isset($customer_zatca['buyer_scheme_id']) ? $customer_zatca['buyer_scheme_id'] : "",
			),
			'postal_address' => array(
				'country' => isset($customer_zatca['buyer_party_postal_country']) ? $customer_zatca['buyer_party_postal_country'] : "",
				'street_name' => isset($customer_zatca['buyer_party_postal_street_name']) ? $customer_zatca['buyer_party_postal_street_name'] : "",
				'building_number' => isset($customer_zatca['buyer_party_postal_building_number']) ? $customer_zatca['buyer_party_postal_building_number'] : "",
				'plot_id' => isset($customer_zatca['buyer_party_postal_plot_id']) ? $customer_zatca['buyer_party_postal_plot_id'] : "",
				'city_subdivision_name' => isset($customer_zatca['buyer_party_postal_district']) ? $customer_zatca['buyer_party_postal_district'] : "",
				'city_name' => isset($customer_zatca['buyer_party_postal_city']) ? $customer_zatca['buyer_party_postal_city'] : "",
				'postal_zone' => isset($customer_zatca['buyer_party_postal_code']) ? $customer_zatca['buyer_party_postal_code'] : "",
			),
			'party_tax_scheme' => array(
				'company_id' => isset($customer_zatca['buyer_tax_id']) ? $customer_zatca['buyer_tax_id'] : "",
				'tax_scheme' => 'VAT',
			),
			'party_legal_entity' => array(
				'registration_name' => $customer_registration_name,
			),
		);

		$invoice_data['accounting_customer_party'] = $invoice_accounting_customer_party;

		$cart = PHPPOSCartSale::get_instance_from_sale_id($sale_id);

		$cart_items = $cart->get_items();
		$payments = $cart->get_payments();

		$number_of_items_sold = 0;
		$number_of_items_returned = 0;

		$BT_113 = 0; // prepaid amount ; paid amount ? //pre-paid amount (advance received)
		// $prepaid_amount

		$BT_114  = 0.01; // rounding amount cac:LegalMonetaryTotal/cbc:PayableRoundingAmount
		$BT_115 = 0;

		$payment_meam_code = 1; // Instrument not defined (Free text)
		foreach ($payments as $payment_id => $payment) {
			$BT_115 += $payment->payment_amount;
			if($payment->payment_type == lang('common_credit')){
				if($payment_meam_code == 1 || $payment_meam_code == 30){
					$payment_meam_code = 30;
				}else{
					$payment_meam_code = 1;
					break;
				}
			}

			if($payment->payment_type == lang('common_cash')){
				if($payment_meam_code == 1 || $payment_meam_code == 10){
					$payment_meam_code = 10;
				}else{
					$payment_meam_code = 1;
					break;
				}
			}
		}

		$sales_payment_means = array(
			'PaymentMeansCode' => $payment_meam_code, // cash:10, debit:14, credit:15
			'InstructionNote' => $sale_info['ref_sale_desc'], // cash:10, debit:14, credit:15
		);

		$sales_allowance_charge_list = array(
			'allowance_92_list' => array(),
			'tax_category_list' => array()
		); // document level bt-92

		$sales_tax_total = array(
			'tax_amount' => 0,
			'tax_subtotal' => array(
			),
		);

		$taxes = $cart->get_taxes(1);//show cumulative name if cumulative set
		$total_tax_percent = 0;

		foreach($taxes as $tax_name=>$tax_amount){
			$sales_tax_total['tax_amount'] += $tax_amount;
			$percent = explode("% ", $tax_name)[0];
			$total_tax_percent += $percent;

			$subtotal = array(
				'taxable_amount' =>  $tax_amount / ($percent / 100),
				'tax_amount' => $tax_amount,
				'tax_category' => array(
					'id' => "S",
					'percent' => $percent,
					'tax_scheme' => array(
						'id' => "VAT",
					),
				),
			);
			array_push($sales_tax_total['tax_subtotal'], $subtotal);

			$tax_category = array(
				'id' => "S",
				'percent' => $percent,
				'tax_scheme' => array(
					'id' => "VAT",
				),
			);

			array_push($sales_allowance_charge_list['tax_category_list'], $tax_category);
		}

		//BR-CO-14 rule
		$sales_tax_total['tax_amount'] = round($sales_tax_total['tax_amount'], 2);
		$sum_sub_total = 0;
		foreach($sales_tax_total['tax_subtotal'] as $k => $tax_subtotal){
			if($k == (count($sales_tax_total['tax_subtotal']) - 1)){
				$sales_tax_total['tax_subtotal'][$k]['tax_amount'] = $sales_tax_total['tax_amount'] - $sum_sub_total;
				break;
			}
			$sales_tax_total['tax_subtotal'][$k]['tax_amount'] = round($tax_subtotal['tax_amount'],2);
			$sum_sub_total += $sales_tax_total['tax_subtotal'][$k]['tax_amount'];
		}

		//Todo cash amount check???
		//Todo check allowance
		$sales_legal_monetary_total = array(
			//SUM_BT-131
			'line_extension_amount' => 0, 
			'tax_exclusive_amount' => 0, 
			'tax_inclusive_amount' => 0,
			//SUM BT-92
			'allowance_total_amount' => 0,
			'prepaid_amount' => 0,
			'payable_amount' => 0,
		);

		$ALLOWANCES_BT_92_LIST = array(); // document level allowance charge list

		$invoice_lines = array();

		foreach (array_reverse($cart_items, true) as $k => $item) {

			// invoice line net amount
			$ITEM_BT_131 = 0;
			// invoice line allowance amount
			$ITEM_BT_136 = 0;
			// item net price
			$ITEM_BT_146 = 0;
			// item price base quantity
			$ITEM_BT_149 = 0;
			// invoice quantity
			$ITEM_BT_129 = 0;
			// item gross price
			$ITEM_BT_148 = 0;
			// item price discount
			$ITEM_BT_147 = 0;

			//entire discount
			if($item->system_item == 1){
				if( $item->product_id == lang('common_discount')){

					$BT_92 = array(
						'reason' => lang('common_discount'),
						'amount' => $item->unit_price,
						// 'amount' => round($item->unit_price * ($total_tax_percent + 100) / 100,3),
					);
					array_push($ALLOWANCES_BT_92_LIST, $BT_92);
				}
				continue;
			}

			$item_taxes = array();
			if(strtoupper(substr($item->item_id, 0, 3)) == 'KIT')
			{
				$item_taxes = $this->Item_kit_taxes_finder->get_info(str_replace('KIT ','',$item['item_id']));
			}
			else
			{
				$item_taxes = $this->Item_taxes_finder->get_info($item->item_id);
			}

			$classified_tax_categories = array();
			$classified_tax_category_ids = array("S", "Z", "E", "O");
			//Todo check //VAT category code must contain one of the values (S, Z, E, O).
			// Standard rate
			// Zero rated
			// Exempt from tax
			// Out of scope of VAT

			$prev_percent = 0;
			$total_tax_percent = 0;
			foreach($item_taxes as $i_tax => $item_tax){
				$current_percent = $item_tax['percent'];
				if($item_tax['cumulative'] == 1){
					$current_percent = ($prev_percent/100 + 1) * $item_tax['percent'];
					$prev_percent = $current_percent;
				}

				$total_tax_percent += $current_percent;

				$classified_tax_category = array(
					'id' => $classified_tax_category_ids[0],
					'percent' => $current_percent,
					'tax_scheme' => array(
						'id' =>"VAT",
					)
				);
				$prev_percent = $item_tax['percent'];
				array_push($classified_tax_categories, $classified_tax_category);
			}

			if ($item->tax_included) {
				$unit_price = $item->unit_price;
				if(count($item->modifier_items)>0){
					$unit_price = $item->unit_price + $item->get_modifier_price_exclusive_of_tax();
				}
				$ITEM_BT_146 = $unit_price;
				
				$ITEM_BT_148 = $item->regular_price / ((100 + $total_tax_percent)/100);
				if(count($item->modifier_items)>0){
					$ITEM_BT_148 = $ITEM_BT_148 + $item->get_modifier_price_exclusive_of_tax();
				}

				if (get_class($item) == 'PHPPOSCartItemSale') {
					if ($item->tax_included) {
						$this->load->helper('items');
						$unit_price = get_price_for_item_including_taxes($item->item_id, $unit_price);
						$price_including_tax = $unit_price;
						$price_excluding_tax = get_price_for_item_excluding_taxes($item->item_id, $unit_price);
					}
				} else {
					if ($item->tax_included) {
						$this->load->helper('item_kits');
						$unit_price = get_price_for_item_kit_including_taxes($item->item_kit_id, $unit_price);
						$price_including_tax = $unit_price;
						$price_excluding_tax = get_price_for_item_kit_excluding_taxes($item->item_kit_id, $unit_price);
					}
				}
			} else {

				$unit_price = $item->unit_price;
				if(count($item->modifier_items)>0){
					$unit_price += $item->get_modifier_unit_total();
				}
				$ITEM_BT_146 = $unit_price;
				
				$ITEM_BT_148 = $item->regular_price;
				if(count($item->modifier_items)>0){
					$ITEM_BT_148 = $ITEM_BT_148 + $item->get_modifier_price_exclusive_of_tax();
				}
				//item
				if (get_class($item) == 'PHPPOSCartItemSale') {
					$this->load->helper('items');
					$price_excluding_tax = $unit_price;
					$price_including_tax = get_price_for_item_including_taxes($item->item_id, $unit_price);
				} else //Kit
				{
					$this->load->helper('item_kits');
					$price_excluding_tax = $unit_price;
					$price_including_tax = get_price_for_item_kit_including_taxes($item->item_kit_id, $unit_price);
				}
			}
			$ITEM_BT_147 = $ITEM_BT_148 - $ITEM_BT_146; //Item price discount

			// set 1 instead of $item->quantity_unit_quantity : Quantity Units & Modifiers
			$ITEM_BT_149  = 1;

			$ITEM_BT_129 = $item->quantity;

			// currently set 0
			$ITEM_BT_136 = $ITEM_BT_146 * $item->discount / 100;
			$ITEM_BT_136 = to_currency_no_money($ITEM_BT_136, 2);

			$ITEM_BT_131_0 = $ITEM_BT_146 / $ITEM_BT_149 * $ITEM_BT_129 - $ITEM_BT_136;
			$ITEM_BT_131 = to_currency_no_money($ITEM_BT_131_0, 2);
			
			$ITEM_PRICE_DECIMAL = 2;
			//BT-131 rule check
			$ITEM_BT_131_1 = to_currency_no_money($ITEM_BT_146, $ITEM_PRICE_DECIMAL) / $ITEM_BT_149 * $ITEM_BT_129 - $ITEM_BT_136;
			while(abs($ITEM_BT_131_1 - $ITEM_BT_131) > $BT_114){

				$ITEM_PRICE_DECIMAL ++;
				$ITEM_BT_131_1 = to_currency_no_money($ITEM_BT_146, $ITEM_PRICE_DECIMAL) / $ITEM_BT_149 * $ITEM_BT_129 - $ITEM_BT_136;
				if( $ITEM_PRICE_DECIMAL > 5 ) break;
			}

			$price_including_tax = $price_including_tax * (1 - ($item->discount / 100));
			$price_excluding_tax = $price_excluding_tax * (1 - ($item->discount / 100));

			$item_tax_amount = ($price_including_tax - $price_excluding_tax); //todo get_modifier_unit_total

			if ($item->quantity > 0 && $item->name != lang('common_store_account_payment', '', array(), FALSE) && $item->name != lang('common_discount', '', array(), FALSE) && $item->name != lang('common_refund', '', array(), FALSE) && $item->name != lang('common_fee', '', array(), FALSE)) {
				$number_of_items_sold = $number_of_items_sold + $item->quantity;
			} elseif ($item->quantity < 0 && $item->name != lang('common_store_account_payment', '', array(), FALSE) && $item->name != lang('common_discount', '', array(), FALSE) && $item->name != lang('common_refund', '', array(), FALSE) && $item->name != lang('common_fee', '', array(), FALSE)) {
				$number_of_items_returned = $number_of_items_returned + abs($item->quantity);
			}

			if ($item->quantity > 0 && $item->name != lang('common_store_account_payment', '', array(), FALSE) && $item->name != lang('common_discount', '', array(), FALSE) && $item->name != lang('common_refund', '', array(), FALSE) && $item->name != lang('common_fee', '', array(), FALSE)) {
				$number_of_items_sold = $number_of_items_sold + $item->quantity;
			} elseif ($item->quantity < 0 && $item->name != lang('common_store_account_payment', '', array(), FALSE) && $item->name != lang('common_discount', '', array(), FALSE) && $item->name != lang('common_refund', '', array(), FALSE) && $item->name != lang('common_fee', '', array(), FALSE)) {
				$number_of_items_returned = $number_of_items_returned + abs($item->quantity);
			}

			// item name

			$item_name = $item->name;
			$item_number_for_receipt = false;

			if ($this->config->item('show_item_id_on_receipt')) {
				switch ($this->config->item('id_to_show_on_sale_interface')) {
					case 'number':
						$item_number_for_receipt = property_exists($item, 'item_number') ? H($item->item_number) : H($item->item_kit_number);
						break;

					case 'product_id':
						$item_number_for_receipt = property_exists($item, 'product_id') ? H($item->product_id) : '';
						break;

					case 'id':
						$item_number_for_receipt = property_exists($item, 'item_id') ? H($item->item_id) : 'KIT ' . H($item->item_kit_id);
						break;

					default:
						$item_number_for_receipt = property_exists($item, 'item_number') ? H($item->item_number) : H($item->item_kit_number);
						break;
				}
			}

			if($item_number_for_receipt){
				$item_name = $item_name . " - " . $item_number_for_receipt;
				if($item->size){
					$item_name = $item_name . "(" . $item->size . ")";
				}
			}

			if (property_exists($item, 'quantity_unit_quantity') && $item->quantity_unit_quantity !== NULL) {
				$item_name .= "[" . lang('common_quantity_unit_name') . ': ' . $item->quantity_units[$item->quantity_unit_id] . ', ' . lang('common_quantity_units') . ': ' . H(to_quantity($item->quantity_unit_quantity)) . "]";
			}

			$item_modifier_description = "";
			if (count($item->modifier_items) > 0) {
				$item_modifier_description .= '
				<div class="invoice-desc">
					'.lang('common_quantity_units').' '.lang('common_price').':'.to_currency($item->unit_price).'
				</div>';
			}

			foreach ($item->modifier_items as $modifier) {
				$item_modifier_description .= '
				<div class="invoice-desc">
					'. $modifier['display_name'] .'
				</div>
				<div class="invoice-desc">
					'.(isset($item->variation_name) && $item->variation_name ? H($item->variation_name) : '').'
				</div>
				<div class="invoice-desc">
					'.((!$this->config->item('hide_desc_on_receipt') && !$item->description == "")? nl2br(clean_html($item->description)) : "").'
				</div>
				<div class="invoice-desc">
					'.((isset($item->serialnumber) && $item->serialnumber != "")? H($item->serialnumber) : "").'
				</div>
				<br>';
			}

			// $sales_tax_total['tax_amount'] += $item_tax_amount * $item->quantity;
			// $sales_tax_total['tax_subtotal']['taxable_amount'] += $price_excluding_tax * $item->quantity;
			// $sales_tax_total['tax_subtotal']['tax_amount'] += $item_tax_amount * $item->quantity;

			$invoice_line = array(
				'id' => $k + 1,
				'invoiced_quantity' => $ITEM_BT_129,
				'line_extension_amount' => $ITEM_BT_131,
				'tax_total' => array(
					'tax_amount' => to_currency_no_money(get_price_for_item_including_taxes($item->item_id, $ITEM_BT_131) - $ITEM_BT_131),
					'rounding_amount' => to_currency_no_money(get_price_for_item_including_taxes($item->item_id, $ITEM_BT_131)),
				),
				'item' => array(
					'description' => $item_modifier_description . $item->description,
					'name' => $item_name,
					'classified_tax_category' => $classified_tax_categories
				),
				'price' => array(
					'price_amount' => to_currency_no_money($ITEM_BT_146, $ITEM_PRICE_DECIMAL),
					'allowance_charge' => array(
						'charge_indicator' => false,
						'allowance_charge_reason' => lang('common_discount'),
						'amount' => to_currency_no_money($ITEM_BT_147),
					)
				),
				'allowance_charge' => array(
					'allowance_charge_reason_code' => 95,
					'allowance_charge_reason' => lang('common_discount'),
					'amount' => $ITEM_BT_136,
				),
			);

			//BT-106
			$sales_legal_monetary_total['line_extension_amount'] += $ITEM_BT_131;
			
			$sales_legal_monetary_total['tax_inclusive_amount'] += to_currency_no_money(get_price_for_item_including_taxes($item->item_id, $ITEM_BT_131));

			array_push($invoice_lines, $invoice_line);
		}
		$invoice_data['invoice_lines'] = $invoice_lines;

		//BT-107
		foreach($ALLOWANCES_BT_92_LIST as $BT_92){
			$sales_legal_monetary_total['allowance_total_amount'] += $BT_92['amount'];
		}

		$sales_allowance_charge_list['allowance_92_list'] = $ALLOWANCES_BT_92_LIST;

		// Invoice total amount without VAT
		// BT-109 = sum(BT-131) - sum(BT-107) 
		$BT_109 = $sales_legal_monetary_total['line_extension_amount'] - $sales_legal_monetary_total['allowance_total_amount'];
		$sales_legal_monetary_total['tax_exclusive_amount'] = $BT_109;
		$sales_legal_monetary_total['tax_inclusive_amount'] = $BT_109 + $sales_tax_total['tax_amount'] /* BT-110 BT_110 */;

		if($BT_115 != $sales_legal_monetary_total['tax_inclusive_amount'] && abs( $BT_115 -  $sales_legal_monetary_total['tax_inclusive_amount'] + $BT_113 ) >= $BT_114 ){
			$ret = array(
				'state' => 0,
				'message' => "BT-112 is not equal to BT-115",
				'sale_id' => $sale_id,
				'data' => ""
			);
			return $ret;
		}

		$sales_legal_monetary_total['payable_amount'] = $sales_legal_monetary_total['tax_inclusive_amount'] - $BT_113;
		$sales_legal_monetary_total['prepaid_amount'] = $BT_113;

		// Invoice total vat amount
		// $BT_110 = sum($BT_117);

		// Invoice total amount with VAT
		// $BT_112 = $BT_110 + $BT_109

		$invoice_data['payment_means'] = $sales_payment_means;
		$invoice_data['allowance_charge'] = $sales_allowance_charge_list;
		$invoice_data['tax_total'] = $sales_tax_total;
		$invoice_data['legal_monetary_total'] = $sales_legal_monetary_total;

		$invoice_data['note'] = 'ABC';
		$invoice_data['document_currency_code'] = 'SAR'; //Todo check 
		$invoice_data['tax_currency_code'] = 'SAR'; //Todo check

		$invoice_data['additional_documnet_reference'] = array(
			array(
				'id' => 'ICV',
				'UUID' => $sale_id,
			),
			array(
				'id' => "PIH",
				'attachment' => $pih
			),
		); //Todo check - add QR

		$ret_xml = Fatoora::generate_invoice($invoice_data, $cron?1:0);

		if($ret_xml['state'] == 0){
			$ret = array(
				'state' => 0,
				'sale_id' => $sale_id,
				'message' => lang("sales_zatca_xml_generate_failed"),
			);
			return $ret;
		}

		$zatca_invoice = array(
			'location_id' => $location_id,
			'sale_id' => $invoice_data['sale_id'],
			'PIH' => $pih,
			'invoice_data' => json_encode($invoice_data),
			'invoice_type_code' => $invoice_data['invoice_type_code'],
			'invoice_subtype' => $invoice_data['invoice_subtype'],
			'invoice_xml' => $ret_xml['data'],
			'reported' => 0,
		);

		$this->Invoice->save_zatca_invoice($zatca_invoice);

		$ret = array(
			'state' => 1,
			'message' => lang("sales_zatca_xml_created_success"),
			'sale_id' => $sale_id,
			'data' => $ret_xml['data']
		);
		return $ret;
	}

	//process step - 2
	private function invoice_sign_process($params, $cron = 0){
		$sale_id = $params['sale_id'];
		$sale_info = $this->Sale->get_info($sale_id)->row_array();

		$location_id = $sale_info['location_id'];

		$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);
		$zatca_invoice = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);

		$sdk_config = array(
			'cert' => $location_zatca_config['cert'],
			'private_key' => $location_zatca_config['private_key'],
			'pih' => $zatca_invoice['PIH'], //start from generate step
			'cert_password' => "123456789", // ??? not working in test 
		);
		
		//sign invoice
        $ret_sign = Fatoora::api_sign_xml_invoice($zatca_invoice, $sdk_config);

		if(!$ret_sign){
            $ret = array(
                'state' => 0,
                'message' => lang("sales_zatca_invoice_sign_failed"),
                'sale_id' => $sale_id,
                'data' => ""
            );
			if($cron == 1) return $ret;

			echo json_encode($ret);
			exit();
		}else if($ret_sign['state'] == 0){
            $ret = array(
                'state' => 0,
                'message' => "Sign invoice:".$ret_sign['message'],
                'sale_id' => $sale_id,
                'data' => isset($ret_sign['data'])?$ret_sign['data']:"",
            );
			if($cron == 1) return $ret;

			echo json_encode($ret);
			exit();
        } else if($ret_sign['state'] == 2){
            $ret = array(
                'state' => 2,
                'message' => "Awaiting invoice xml signature.(".$ret_sign['message'].")",
                'sale_id' => $sale_id,
            );
			if($cron == 1) return $ret;
			echo json_encode($ret);
			exit();
		}else{
			$zatca_invoice['invoice_xml_sign'] = $ret_sign['data'];
			$output = json_decode($ret_sign['output']);
			$hash_line = end($output);
			if($hash_line){
				if(strpos($hash_line,"INVOICE HASH = ") > -1){
					$invoice_hash = explode("INVOICE HASH = ",$hash_line)[1];
					$zatca_invoice['hash'] = $invoice_hash;
				}
			}
		}

		$this->Invoice->save_zatca_invoice($zatca_invoice);
		$ret = array(
			'state' => 1,
			'message' => lang("sales_zatca_invoice_sign_success"),
			'sale_id' => $zatca_invoice['sale_id'],
			'data' => $ret_sign['data']
		);
		return $ret;
	}

	//process step - 3
	private function invoice_validate_process($params, $cron = 0){
		$sale_id = $params['sale_id'];
		$sale_info = $this->Sale->get_info($sale_id)->row_array();

		$location_id = $sale_info['location_id'];

		$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);
		$zatca_invoice = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);

		$sdk_config = array(
			'cert' => $location_zatca_config['cert'],
			'private_key' => $location_zatca_config['private_key'],
			'pih' => $zatca_invoice['PIH'], //start from generate step
		);

		//check invoice validate
        $ret_validate = Fatoora::api_validate_xml_invoice($zatca_invoice, $sdk_config);
        if(!$ret_validate){
            $ret = array(
                'state' => 0,
                'message' => lang("sales_zatca_invoice_validate_failed"),
                'sale_id' => $sale_id,
                'data' => ""
            );
			if($cron == 1) return $ret;
			echo json_encode($ret);
			exit();
		}else if($ret_validate['state'] == 0){
            $ret = array(
                'state' => 0,
                'message' => lang("sales_zatca_invoice_validate_failed"),
                'sale_id' => $sale_id,
                'data' => $ret_validate['data'],
            );
			if($cron == 1) return $ret;
			echo json_encode($ret);
			exit();
        }else if($ret_validate['state'] == 2){
            $ret = array(
                'state' => 2,
                'message' => lang("sales_zatca_invoice_awaiting_validate")."(".$ret_validate['message'].")",
                'sale_id' => $sale_id,
            );
			if($cron == 1) return $ret;
			echo json_encode($ret);
			exit();
		}else{
			$zatca_invoice['validate'] = 1;

			$ret_qr = Fatoora::api_generate_invoice_qr($zatca_invoice, $sdk_config);
			$zatca_invoice['qr_code'] = $ret_qr['data']; 
			//set qr
		}
		$this->Invoice->save_zatca_invoice($zatca_invoice);

		$ret = array(
			'state' => 1,
			'message' => $ret_validate['message'],
			'sale_id' => $sale_id,
			'data' => $ret_validate['data']
		);
		return $ret;
	}

	//process step - 4
	private function invoice_generate_request_process($params, $cron = 0){
		$sale_id = $params['sale_id'];
		$sale_info = $this->Sale->get_info($sale_id)->row_array();

		$location_id = $sale_info['location_id'];

		$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);
		$zatca_invoice = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);

		//generating JSON API Request 
        $ret_request = Fatoora::api_request_xml_invoice($zatca_invoice);
        if(!$ret_request){
            $ret = array(
                'state' => 0,
                'message' => lang("sales_zatca_invoice_request_failed"),
                'sale_id' => $sale_id,
                'data' => '',
            );

			if($cron == 1) return $ret;

			echo json_encode($ret);
			exit();
		}else{
			$zatca_invoice['invoice_request'] = $ret_request['data'];
		}

		$this->Invoice->save_zatca_invoice($zatca_invoice);
		$ret = array(
			'state' => 1,
			'message' => lang("sales_zatca_invoice_request_success"),
			'sale_id' => $sale_id,
			'data' =>  $ret_request['data'],
		);
		return $ret;
	}

	//process step - 5
	private function invoice_compliance_process($params, $cron = 0){
		$sale_id = $params['sale_id'];
		$sale_info = $this->Sale->get_info($sale_id)->row_array();

		$location_id = $sale_info['location_id'];

		$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);
		$ccsid = $location_zatca_config['compliance_csid'];
		
		$data = array(
			'ccsid' => json_decode($ccsid, true)
		);

		$zatca_invoice = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);
		$data['invoice'] = $zatca_invoice;

		$ret_compliance = Fatoora::check_compliance_invoice($data);

		$ret = array();
		if($ret_compliance['state'] == 1){
			$zatca_invoice['check_compliance'] = 1;
			$this->Invoice->save_zatca_invoice($zatca_invoice);

            $ret = array(
                'state' => 1,
                'message' => lang("sales_zatca_invoice_compliant_success"),
                'data' => $ret_compliance['data'],
				'zatca_invoice' => $zatca_invoice,
            );
		}else{
            $ret = array(
                'state' => 0,
                'message' => lang("sales_zatca_invoice_compliant_failed"),
                'data' => $ret_compliance['data'],
				'zatca_invoice' => $zatca_invoice,
            );
		}
		return $ret;
	}

	//process step - 6
	private function invoice_report_process($params, $cron = 0){
		$sale_id = $params['sale_id'];
		$sale_info = $this->Sale->get_info($sale_id)->row_array();

		$location_id = $sale_info['location_id'];
		$clearance_status = isset($params['clearance_status']) ? $params['clearance_status'] : 1;

		$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);
		$pcsid = $location_zatca_config['production_csid'];
		$zatca_invoice_data = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);

		$data['invoice'] = $zatca_invoice_data;
		$data['pcsid'] = json_decode($pcsid, true);
		$data['clearance_status'] = $clearance_status;

		$ret_report = Fatoora::report_api($data);
		if($ret_report['state'] == 0){
            $ret = array(
                'state' => 0,
                'message' => lang("sales_zatca_invoice_report_failed"),
                'sale_id' => $sale_id,
				'data' => $ret_report['data']
            );
			if($cron == 1) return $ret;
			echo json_encode($ret);
			exit();
		}

		$zatca_invoice_data['reporting_response'] = json_encode($ret_report['data']);
		$zatca_invoice_data['reported'] = 1;

		$this->Invoice->save_zatca_invoice($zatca_invoice_data);
		$ret = array(
			'state' => 1,
			'message' => lang("sales_zatca_invoice_report_success"),
			'sale_id' => $sale_id,
			'data' => $ret_report['data']
		);
		return $ret;
	}

	//process step - 7
	private function invoice_clearance_process($params, $cron = 0){
		$sale_id = $params['sale_id'];
		$sale_info = $this->Sale->get_info($sale_id)->row_array();

		$location_id = $sale_info['location_id'];

		$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);
		$pcsid = $location_zatca_config['production_csid'];
		$zatca_invoice_data = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);

		$data['invoice'] = $zatca_invoice_data;
		$data['pcsid'] = json_decode($pcsid, true);
		$data['clearance_status'] = 1;

		$ret_clearance = Fatoora::clearance_api($data);
		if($ret_clearance['state'] == 0){
            $ret = array(
                'state' => 0,
                'message' => "Clearance failed",
                'sale_id' => $sale_id,
				'data' => $ret_clearance['data']
            );
			if($cron == 1) return $ret;
			echo json_encode($ret);
			exit();
		} else if($ret_clearance['state'] == 303){
            $ret = array(
                'state' => 303,
                'message' => "Clearance is deactiviated.",
                'sale_id' => $sale_id,
				'data' => $ret_clearance['data']
            );
			if($cron == 1) return $ret;
			echo json_encode($ret);
			exit();
		}
		$zatca_invoice_data['reporting_response'] = json_encode($ret_clearance['data']);
		$zatca_invoice_data['reported'] = 2;
		$this->Invoice->save_zatca_invoice($zatca_invoice_data);
		$ret = array(
			'state' => 1,
			'message' => "It has passed Clearance successfully",
			'sale_id' => $sale_id,
			'data' => $ret_clearance['data']
		);
		return $ret;
	}

	function integration_cron($select_location_id = NULL){
		$this->load->model('Invoice');

		if(!$this->config->item('use_saudi_tax_config')){
			return;
		}

		// get location list
		$locations_result = $this->Location->get_all();
		$locations = $locations_result->result_array();
		for($iLocation = 0; $iLocation < count($locations); $iLocation++){

			$eLocation = $locations[$iLocation];
			$location_id = $eLocation['location_id'];

			if($select_location_id != NULL){
				if($location_id != $select_location_id){
					continue;
				}
			}

			$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);
			if(!$location_zatca_config){
				continue;
			}

			$sales_list = $this->get_unreported_sales_list($location_id);
			if(count($sales_list) > 0){

				$stepsCompleted = 0;
				$numsteps = count($sales_list);

				$percent = floor(($stepsCompleted / $numsteps) * 100);
				$message = "";
				$this->update_sync_progress($percent, $location_id, "*", "*");

				$this->log("Location ID:".$location_id."\n");
				$location_cron_state = 1;
				for($iSale = 0; $iSale < count($sales_list); $iSale++){
					$sale = $sales_list[$iSale];

					$sale_id = $sale['sale_id'];
					$location_id = $sale['location_id'];
					$params = array(
						'sale_id' => $sale_id,
						'location_id' => $location_id
					);

					if (is_cli()) {
						echo "Sale ID:".$sale_id."<br>";
					}
					$this->log("Sale ID:".$sale_id."\n");

					$this->update_sync_progress($percent, $location_id, $sale_id, "Generating xml invoice...");
					$ret = $this->invoice_generate_xml_process($params, 1);
					if($ret['state'] == 0){
						$zatca_invoice_data = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);
						$zatca_invoice_data['status'] = json_encode($ret);

						if (is_cli()) {
							echo "Error:".$zatca_invoice_data['status']."<br>";
						}
						$this->log("Error:".$zatca_invoice_data['status']."\n");
						$this->Invoice->save_zatca_invoice($zatca_invoice_data);
						$location_cron_state = 0;
						break;
					}
					$this->log("Passed.\n");

					$this->log("Step: Sign\n");
					$this->update_sync_progress($percent, $location_id, $sale_id, "Signing xml invoice...");
					$ret = $this->invoice_sign_process($params, 1);
					if($ret['state'] == 0){
						$zatca_invoice_data = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);
						$zatca_invoice_data['status'] = json_encode($ret);
						if (is_cli()) {
							echo "Error:".$zatca_invoice_data['status']."<br>";
						}
						$this->log("Error:".$zatca_invoice_data['status']."\n");
						$this->Invoice->save_zatca_invoice($zatca_invoice_data);
						$location_cron_state = 0;
						break;
					}else if($ret['state'] == 2){

						$try_again_times = 1;
						$zatca_invoice_data = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);
						while($try_again_times <= FATOORA_TRY_TIMES){// try 3 times more
							sleep(3);
							$this->update_sync_progress($percent, $location_id, $sale_id, "Signing xml invoice... try again:".$try_again_times);
							$try_again_times++;
							$ret = $this->invoice_sign_process($params, 1);
							if($ret['state'] == 0){
								$zatca_invoice_data['status'] = json_encode($ret);
								if (is_cli()) {
									echo "Error:".$zatca_invoice_data['status']."<br>";
								}
								$this->log("Error:".$zatca_invoice_data['status']."\n");
								$this->Invoice->save_zatca_invoice($zatca_invoice_data);
								$location_cron_state = 0;
								break;
							}else if($ret['state'] == 1){
								$zatca_invoice_data['status'] = json_encode($ret);
								$this->Invoice->save_zatca_invoice($zatca_invoice_data);
								break;
							}
							//else state == 2: try again 
							$zatca_invoice_data['status'] = json_encode($ret);
							$this->Invoice->save_zatca_invoice($zatca_invoice_data);
						}

						if($try_again_times > FATOORA_TRY_TIMES){
							if (is_cli()) {
								echo "Error: Try again ".FATOORA_TRY_TIMES." times";
							}
							$this->log("Error: Try again ".FATOORA_TRY_TIMES." times\n");
							$location_cron_state = 0;
							break;
						}
						if($location_cron_state == 0){
							break;
						}
						//other case passed
					}
					$this->log("Passed.\n");

					$this->log("Step: Validation\n");
					$this->update_sync_progress($percent, $location_id, $sale_id, "Validating xml invoice...");
					$ret = $this->invoice_validate_process($params, 1);
					if($ret['state'] == 0){
						$zatca_invoice_data = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);
						$zatca_invoice_data['status'] = json_encode($ret);
						if (is_cli()) {
							echo "Error:".$zatca_invoice_data['status']."<br>";
						}
						$this->log("Error:".$zatca_invoice_data['status']."\n");
						$this->Invoice->save_zatca_invoice($zatca_invoice_data);
						$location_cron_state = 0;
						break;
					}else if($ret['state'] == 2){
						$try_again_times = 1;
						$zatca_invoice_data = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);
						while($try_again_times <= FATOORA_TRY_TIMES){// try 3 times more
							sleep(3);
							$this->update_sync_progress($percent, $location_id, $sale_id, "Validating xml invoice... try again:".$try_again_times);
							$try_again_times++;
							$ret = $this->invoice_validate_process($params, 1);
							if($ret['state'] == 0){
								$zatca_invoice_data['status'] = json_encode($ret);
								if (is_cli()) {
									echo "Error:".$zatca_invoice_data['status']."<br>";
								}
								$this->log("Error:".$zatca_invoice_data['status']."\n");
								$this->Invoice->save_zatca_invoice($zatca_invoice_data);
								$location_cron_state = 0;
								break;
							}else if($ret['state'] == 1){
								$zatca_invoice_data['status'] = json_encode($ret);
								$this->Invoice->save_zatca_invoice($zatca_invoice_data);
								break;
							}
							//else state == 2: try again 
							$zatca_invoice_data['status'] = json_encode($ret);
							$this->Invoice->save_zatca_invoice($zatca_invoice_data);
						}

						if($try_again_times > FATOORA_TRY_TIMES){
							if (is_cli()) {
								echo "Error: Try again ".FATOORA_TRY_TIMES." times";
							}
							$this->log("Error: Try again ".FATOORA_TRY_TIMES." times\n");
							$location_cron_state = 0;
							break;
						}
						if($location_cron_state == 0){
							break;
						}
						//other case passed
					}
					$this->log("Passed.\n");

					$this->log("Step: Generate Request\n");
					$this->update_sync_progress($percent, $location_id, $sale_id, "Generating invoice request...");
					$ret = $this->invoice_generate_request_process($params, 1);
					if($ret['state'] == 0){
						$zatca_invoice_data = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);
						$zatca_invoice_data['status'] = json_encode($ret);
						if (is_cli()) {
							echo "Error:".$zatca_invoice_data['status']."<br>";
						}
						$this->log("Error:".$zatca_invoice_data['status']."\n");
						$this->Invoice->save_zatca_invoice($zatca_invoice_data);
						$location_cron_state = 0;
						break;
					}else if($ret['state'] == 2){
						$try_again_times = 1;
						$zatca_invoice_data = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);
						while($try_again_times <= FATOORA_TRY_TIMES){// try 3 times more
							sleep(3);
							$this->update_sync_progress($percent, $location_id, $sale_id, "Generating invoice request... try again:".$try_again_times);
							$try_again_times++;
							$ret = $this->invoice_generate_request_process($params, 1);
							if($ret['state'] == 0){
								$zatca_invoice_data['status'] = json_encode($ret);
								if (is_cli()) {
									echo "Error:".$zatca_invoice_data['status']."<br>";
								}
								$this->log("Error:".$zatca_invoice_data['status']."\n");
								$this->Invoice->save_zatca_invoice($zatca_invoice_data);
								$location_cron_state = 0;
								break;
							}else if($ret['state'] == 1){
								$zatca_invoice_data['status'] = json_encode($ret);
								$this->Invoice->save_zatca_invoice($zatca_invoice_data);
								break;
							}
							//else state == 2: try again 
							$zatca_invoice_data['status'] = json_encode($ret);
							$this->Invoice->save_zatca_invoice($zatca_invoice_data);
						}

						if($try_again_times > FATOORA_TRY_TIMES){
							if (is_cli()) {
								echo "Error: Try again ".FATOORA_TRY_TIMES." times";
							}
							$this->log("Error: Try again ".FATOORA_TRY_TIMES." times\n");
							$location_cron_state = 0;
							break;
						}
						if($location_cron_state == 0){
							break;
						}
						//other case passed
					}
					$this->log("Passed.\n");

					$this->log("Step: Compliance\n");
					$this->update_sync_progress($percent, $location_id, $sale_id, "Checking invoice compliance...");
					$ret = $this->invoice_compliance_process($params, 1);
					if($ret['state'] == 0){
						$zatca_invoice_data = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);
						$zatca_invoice_data['status'] = json_encode($ret);
						if (is_cli()) {
							echo "Error:".$zatca_invoice_data['status']."<br>";
						}
						$this->log("Error:".$zatca_invoice_data['status']."\n");
						$this->Invoice->save_zatca_invoice($zatca_invoice_data);
						$location_cron_state = 0;
						break;
					}
					$this->log("Passed.\n");

					$zatca_invoice_data = $this->Invoice->get_zatca_invoice_by_sale_id($sale_id);
					if(
						$zatca_invoice_data['invoice_subtype'] &&
						$zatca_invoice_data['invoice_subtype'][0] == 0 &&
						$zatca_invoice_data['invoice_subtype'][1] == 1
					){// standard tax invoice
						$this->log("Step: Clearance\n");
						$this->update_sync_progress($percent, $location_id, $sale_id, "Clearance standard invoice...");
						$ret = $this->invoice_clearance_process($params, 1);
						if($ret['state'] == 0){
							$zatca_invoice_data['status'] = json_encode($ret);
							$this->Invoice->save_zatca_invoice($zatca_invoice_data);
							if (is_cli()) {
								echo "Error:".$zatca_invoice_data['status']."<br>";
							}
							$location_cron_state = 0;
							break;
						}else if($ret['state'] == 303){
							$params['clearance_status'] = 0;
							$this->log("Clearance Deactive.\n");
							$this->log("Step: Reporting\n");
							$this->update_sync_progress($percent, $location_id, $sale_id, "Reporting standard invoice ...");
							$ret = $this->invoice_report_process($params, 1);
							if($ret['state'] == 0){
								$zatca_invoice_data['status'] = json_encode($ret);
								$this->Invoice->save_zatca_invoice($zatca_invoice_data);
								if (is_cli()) {
									echo "Error:".$zatca_invoice_data['status']."<br>";
								}
								$location_cron_state = 0;
								$this->log("Error:".$zatca_invoice_data['status']."\n");
								break;
							}else{
								if (is_cli()) {
									echo "Success"."<br/>";
								}
								$this->log("Success.\n");
							}
						}else{
							if (is_cli()) {
								echo "Success"."<br/>";
							}
							$this->log("Success.\n");
						}
					}else if(
						$zatca_invoice_data['invoice_subtype'] &&
						$zatca_invoice_data['invoice_subtype'][0] == 0 &&
						$zatca_invoice_data['invoice_subtype'][1] == 2
					){// simplified tax invoice
						$this->log("Step: Reporting\n");
						$this->update_sync_progress($percent, $location_id, $sale_id, "Reporting simplified invoice ...");
						$ret = $this->invoice_report_process($params, 1);
						if($ret['state'] == 0){
							$zatca_invoice_data['status'] = json_encode($ret);
							$this->Invoice->save_zatca_invoice($zatca_invoice_data);
							if (is_cli()) {
								echo "Error:".$zatca_invoice_data['status']."<br>";
							}
							$location_cron_state = 0;
							$this->log("Error:".$zatca_invoice_data['status']."\n");
							break;
						}else{
							if (is_cli()) {
								echo "Success"."<br/>";
							}
							$this->log("Success.\n");
						}
					}

					if($location_cron_state == 0){
						if (is_cli()) {
							echo "Location ". $location_id .": error.\n";
						}
						$this->log("Location ". $location_id .": error.\n");
						break;
					}

					$stepsCompleted++;
					$percent = floor(($stepsCompleted/$numsteps)*100);
					$this->update_sync_progress($percent, $location_id, $sale_id, 'Done');
				}
				if($location_cron_state == 1){
					if (is_cli()) {
						echo "Location ". $location_id .": passed.\n";
					}
					$this->log("Location ". $location_id .": passed.\n");
				}
			}
		}
	}

	function invoice_search(){
		session_write_close();
		$suggestions = $this->Invoice->get_zatca_invoice_search_suggestions($this->input->get('term'),0,10);

		echo json_encode(H($suggestions));
	}

	function cancel()
	{
		$this->load->model('Appconfig');
		$this->Appconfig->save('kill_zatca_cron',1);
		$this->Appconfig->save('zatca_cron_running',0);
		$this->Appconfig->save('zatca_sync_percent_complete', 100);
		$ret = array(
			'state' => 1,
			'message'=> 'done'
		);
		echo json_encode($ret);
		exit();
	}

	function manual_sync()
    {
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
        $this->cron('', '', $location_id);
    }

    /*
		This function is used to sync the PHPPOS items with online ecommerce store.
		$base_url is used NOT used in this function but in application/config/config.php
		$db_override is NOT used at all; but in database.php to select database based on CLI args for cron in cloud
	*/
	public function cron($base_url = '', $db_override = '', $select_location_id = NULL)
    {

        ignore_user_abort(TRUE);
        set_time_limit(0);
        ini_set('max_input_time', '-1');
        session_write_close();

        //Cron's always run on current server path; but if we are between migrations we should run the cron on the previous folder passing along any arguements
        if (defined('SHOULD_BE_ON_OLD') && SHOULD_BE_ON_OLD) {
            global $argc, $argv;
            $prev_folder = isset($_SERVER['CI_PREV_FOLDER']) ?  $_SERVER['CI_PREV_FOLDER'] : 'PHP-Point-Of-Sale-Prev';
            system('php ' . FCPATH . "$prev_folder/index.php zatca cron " . $argv[3] . $prev_folder . '/ ' . $argv[4]);
            exit();
        }

        $this->load->helper('demo');
        if (is_on_demo_host()) {
            echo json_encode(array('success' => FALSE, 'message' => lang('common_disabled_on_demo')));
            die();
        }
        try {

			//Todo set zatca store location config
            $this->load->model('Location');
            if ($timezone = ($this->Location->get_info_for_key('timezone', $this->config->item('zatca_store_location') ? $this->config->item('zatca_store_location') : 1))) {
                date_default_timezone_set($timezone);
            }

            $this->Appconfig->save('kill_zatca_cron', 0);

            $this->load->model("Appconfig");

            if ($this->Appconfig->get_raw_zatca_cron_running() && $this->Appconfig->zatca_has_run_recently()) {
                echo json_encode(array('success' => FALSE, 'message' => lang('common_ecommerce_running')));
                die();
            }

			$this->Appconfig->save('zatca_cron_running', 1);
            $this->Appconfig->save('zatca_sync_percent_complete', 0);

			if (is_cli()) {
				echo "START\n";
			}

			$this->integration_cron($select_location_id);

			$sync_date = date('Y-m-d H:i:s');
			$this->save_log();
			if($select_location_id > 0){
				echo $this->cron_log;
			}
			echo json_encode(array('date' => $sync_date));

			$this->Appconfig->save('last_zatca_sync_date', $sync_date);
			if (is_cli())
			{
				echo "\n\n***************************DONE***********************\n";
			}

            $this->Appconfig->save('zatca_sync_percent_complete', 100);
            $this->Appconfig->save('zatca_cron_running', 0);
        } catch (Exception $e) {
            if (is_cli()) {
                echo "*******EXCEPTION: " . var_export($e->getMessage(), TRUE);
            }
            $this->Appconfig->save('zatca_cron_running', 0);
        }
    }
}
