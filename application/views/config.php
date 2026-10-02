<?php
$this->load->view("partial/header"); 
$this->load->helper('demo');
$this->load->helper('update');


?>

<style>
	.config-nav {
		background: white;
	}

	.config-nav>li>a:hover {
		background-color: #adcce7;
	}
</style>

<div class="manage_buttons">
	<div class="manage-row-options">
		<div class="email_buttons text-center">
			<div class="row">
				<div class="col-md-3 col-sm-3 col-xs-2">
					<div class="search-tpl">
					<div class="input-group">
					  <span class="input-group-addon" id="search-addon"><span class="glyphicon glyphicon-search"></span></span>
						<input aria-describedby="search-addon" type="text" class="form-control" name ="search" id="search"  placeholder="<?php echo lang('common_search') ?>" value="<?php echo H($search); ?>" />
					</div>
					</div>
				</div>
				<div class="col-md-3">	
					<?php echo form_dropdown('section_names', $section_names, '', 'class="form-control input_radius" id="section_names"'); ?>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-10 pull-right">
					<div class="pull-left">
						<?php echo anchor('config/backup', '<span class="ion-load-a"> </span><span class="">' . lang('config_backup_database') . '</span>', array('class' => 'btn btn-primary btn-lg dbBackup hidden-xs')); ?>
						<?php
					 $this->load->helper('update');
					 if (!is_on_phppos_host()) {?>
						<?php echo anchor('config/is_update_available', '<span class="glyphicon glyphicon-import"></span> <span class="hidden-xs hidden-sm">' . lang('common_check_for_update'). '</span>', array('class' => 'checkForUpdate btn btn-success btn-lg hidden-xs')); ?> 
						<?php } ?>
					</div>
				</div>
			</div>
		</div><!-- end email_buttons -->
	</div><!-- manage-row-options -->
</div><!-- manage_buttons -->

<div class="manage_buttons buttons-list config-page container">
	
</div>
<div class="text-center location-settings">
	<?php echo lang('config_looking_for_location_settings').' '.anchor($this->Location->count_all() > 1 ? 'locations' : 'locations/view/1', lang('module_locations').' '.lang('config_module'), 'class="btn btn-info"');?>
</div>
<div class="config-panel">
	
	<?php
	//for help window popups
	$popupAtts = array(
    'width'       => 800,
    'height'      => 600,
    'scrollbars'  => 'yes',
    'status'      => 'yes',
    'resizable'   => 'yes',
    'screenx'     => 0,
    'screeny'     => 0,
    'window_name' => '_blank'
	);
	
	function create_section($title)
	{
		return $title ;
	}
	?>
	
	<div class="row">
		<?php echo form_open_multipart('config/save/',array('id'=>'config_form','class'=>'form-horizontal', 'autocomplete'=> 'off'));  ?>
	<?php 
		$this->load->helper('update');		
		if($this->config->item('ecommerce_platform') == "woocommerce" )
			$woo_hidden_class ="";
		else
			$woo_hidden_class="hidden";

		if($this->config->item('ecommerce_platform') == "shopify" )
			$shopify_hidden_class ="";
		else
			$shopify_hidden_class="hidden";
		
		?>

		<div class="col-xs-12 col-md-3">
			<ul class="nav nav-pills nav-stacked custom-pills config-nav">
				<li role="presentation" class="active"><a href="#company_information" data-toggle="pill"><?php echo lang("config_company_info"); ?></a></li>
				<li role="presentation"><a href="#taxes" data-toggle="pill"><?php echo lang("config_taxes_info"); ?></a></li>
				<li role="presentation"><a href="#currency" data-toggle="pill"><?php echo lang("config_currency_info"); ?></a></li>
				<li role="presentation"><a href="#payment_types" data-toggle="pill"><?php echo lang("config_payment_types_info"); ?></a></li>
				<li role="presentation"><a href="#price_rules" data-toggle="pill"><?php echo lang("config_price_rules_info"); ?></a></li>
				<li role="presentation"><a href="#orders_deliveries" data-toggle="pill"><?php echo lang("config_orders_and_deliveries_info"); ?></a></li>
				<li role="presentation"><a href="#returns" data-toggle="pill"><?php echo lang("common_return_info"); ?></a></li>
				<li role="presentation"><a href="#sales" data-toggle="pill"><?php echo lang("config_sales_info"); ?></a></li>
				<li role="presentation"><a href="#suspended_sales" data-toggle="pill"><?php echo lang("config_suspended_sales_layaways_info"); ?></a></li>
				<li role="presentation"><a href="#receipt" data-toggle="pill"><?php echo lang("config_receipt_info"); ?></a></li>
				<li role="presentation"><a href="#profit_calculation" data-toggle="pill"><?php echo lang("config_profit_info"); ?></a></li>
				<li role="presentation"><a href="#barcodes" data-toggle="pill"><?php echo lang("config_barcodes_info"); ?></a></li>
				<li role="presentation"><a href="#customer_loyalty" data-toggle="pill"><?php echo lang("config_customer_loyalty_info"); ?></a></li>
				<li role="presentation"><a href="#price_tiers" data-toggle="pill"><?php echo lang("config_price_tiers_info"); ?></a></li>
				<li role="presentation"><a href="#id_numbers" data-toggle="pill"><?php echo lang("config_auto_increment_ids_info"); ?></a></li>
				<li role="presentation"><a href="#items_settings" data-toggle="pill"><?php echo lang("config_items_info"); ?></a></li>
				<li role="presentation"><a href="#employee_settings" data-toggle="pill"><?php echo lang("config_employee_info"); ?></a></li>
				<li role="presentation"><a href="#store_accounts" data-toggle="pill"><?php echo lang("config_store_accounts_info"); ?></a></li>
				<li role="presentation"><a href="#disable_modules" data-toggle="pill"><?php echo lang("config_disable_modules"); ?></a></li>
				<li role="presentation"><a href="#application_settings" data-toggle="pill"><?php echo lang("config_application_settings_info"); ?></a></li>
				<li role="presentation"><a href="#email_settings" data-toggle="pill"><?php echo lang("config_email_settings_info"); ?></a></li>
				<li role="presentation"><a href="#sso_info" data-toggle="pill"><?php echo lang("config_sso_info"); ?></a></li>
				<li role="presentation"><a href="#qb_settings" data-toggle="pill"><?php echo lang("config_quickbooks_settings"); ?></a></li>
				<li role="presentation"><a href="#ecommerce_store" data-toggle="pill"><?php echo lang("config_ecommerce_settings_info"); ?></a></li>
				<li class="shopify_settings <?php echo $shopify_hidden_class; ?>" role="presentation"><a href="#shopify_settings" data-toggle="pill"><?php echo lang("config_shopify_settings_info"); ?></a></li>
				<li class="woo_settings <?php echo $woo_hidden_class; ?>" role="presentation"><a href="#woo_settings" data-toggle="pill"><?php echo lang("config_woocommerce_settings_info"); ?></a></li>
				<li role="presentation"><a href="#api_settings" data-toggle="pill"><?php echo lang("config_api_settings_info"); ?></a></li>
				<li role="presentation"><a href="#web_hooks" data-toggle="pill"><?php echo create_section(lang('config_webhooks'), 'store-configuration-options', 'section-webhooks-settings')  ?></a></li>
				<li role="presentation"><a href="#work_order" data-toggle="pill"><?php echo lang("config_work_order"); ?></a></li>
				
				<?php 
				if($this->config->item('branding')['code'] == 'phppointofsale'){
				?>
				<li role="presentation"><a href="#lookup_api_integration" data-toggle="pill"><?php echo lang("config_lookup_api_integration"); ?></a></li>
				<?php
				}
				?>
			</ul>
		</div>
		
		<div class="col-xs-12 col-md-9">
			<div class="tab-content">
				<!-- Company Information -->
				<div class="tab-pane fade in active" id="company_information">
					<div class="row">
						<div class="col-md-12 company_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#company_information_collapse">
										<?php echo create_section(lang("config_company_info")) ?>
									</a>
								</div>
								<div id="company_information_collapse" class="panel-collapse collapse in" aria-expanded="true">
									<div class="panel-body">
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_company')) ?>">	
											<?php echo form_label(lang('common_company_logo').':', 'company_logo',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												
												<input type="file" name="company_logo" id="company_logo" class="filestyle" data-icon="false">  	
											</div>	
										</div>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_company')) ?>">	
											<?php echo form_label(lang('common_delete_logo').':', 'delete_logo',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox('delete_logo', '1', null,'id="delete_logo"');?>
												<label for="delete_logo"><span></span></label>
											</div>	
										</div>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_company')) ?>">	
											<?php echo form_label(lang('common_company').':', 'company',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(array(
													'class'=>'validate form-control form-inps',
												'name'=>'company',
												'id'=>'company',
												'value'=>$this->config->item('company')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_company')) ?>">	
											<?php echo form_label(lang('common_tax_id').':', 'tax_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(array(
													'class'=>'validate form-control form-inps',
												'name'=>'tax_id',
												'id'=>'tax_id',
												'value'=>$this->config->item('tax_id')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_company')) ?>">	
											<?php echo form_label(lang('common_website').':', 'website',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
											<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'website',
												'id'=>'website',
												'value'=>$this->config->item('website')));?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Company Information -->

				<!-- Taxes -->
				<div class="tab-pane fade" id="taxes">
					<div class="row">
						<div class="col-md-12 taxes_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#taxes_collapse">
									<?php echo create_section(lang("config_taxes_info")) ?>
									</a>
								</div>
								<div id="taxes_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
											<?php echo form_label(lang('config_taxjar_api_key').':', 'taxjar_api_key',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'taxjar_api_key',
													'id'=>'taxjar_api_key',
													'value'=>$this->config->item('taxjar_api_key')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
											<?php echo form_label(lang('config_tax_jar_location').':', 'tax_jar_location',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'tax_jar_location',
													'id'=>'tax_jar_location',
													'value'=>'tax_jar_location',
													'checked'=>$this->config->item('tax_jar_location')));?>
												<label for="tax_jar_location"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
											<?php echo form_label(lang('config_flat_discounts_discount_tax').':', 'flat_discounts_discount_tax',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'flat_discounts_discount_tax',
													'id'=>'flat_discounts_discount_tax',
													'value'=>'flat_discounts_discount_tax',
													'checked'=>$this->config->item('flat_discounts_discount_tax')));?>
												<label for="flat_discounts_discount_tax"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
											<?php echo form_label(lang('common_prices_include_tax').':', 'prices_include_tax',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'prices_include_tax',
													'id'=>'prices_include_tax',
													'value'=>'prices_include_tax',
													'checked'=>$this->config->item('prices_include_tax')));?>
												<label for="prices_include_tax"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
											<?php echo form_label(lang('config_charge_tax_on_recv').':', 'charge_tax_on_recv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'charge_tax_on_recv',
													'id'=>'charge_tax_on_recv',
													'value'=>'charge_tax_on_recv',
													'checked'=>$this->config->item('charge_tax_on_recv')));?>
												<label for="charge_tax_on_recv"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
											<?php echo form_label(lang('config_use_tax_value_at_all_locations').':', 'use_tax_value_at_all_locations',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'use_tax_value_at_all_locations',
													'id'=>'use_tax_value_at_all_locations',
													'value'=>'use_tax_value_at_all_locations',
													'checked'=>$this->Appconfig->all_locations_use_global_tax()));?>
												<label for="use_tax_value_at_all_locations"><span></span></label>
											</div>
										</div>					

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
											<?php echo form_label(lang('config_use_sa_einvoice').':', 'use_saudi_tax_config',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'use_saudi_tax_config',
													'id'=>'use_saudi_tax_config',
													'value'=>'use_saudi_tax_config',
													'checked'=>$this->config->item('use_saudi_tax_config')));?>
												<label for="use_saudi_tax_config"><span></span></label>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('common_test_mode').':', 'use_saudi_tax_test_config',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'use_saudi_tax_test_config',
													'id'=>'use_saudi_tax_test_config',
													'value'=>'use_saudi_tax_test_config',
													'checked'=>$this->config->item('use_saudi_tax_test_config')));?>
												<label for="use_saudi_tax_test_config"><span></span></label>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(('Comon Name').' - CSR'.':', 'saudi_tax_common_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saudi_tax_common_name',
													'id'=>'saudi_tax_common_name',
													'placeholder' => lang('config_saudi_tax_common_name_placeholder'),
													'value'=>isset($location_zatca_config['csr_common_name'])?$location_zatca_config['csr_common_name']:""));?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_sn').' - CSR'.':', 'saudi_tax_sn',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saudi_tax_sn',
													'id'=>'saudi_tax_sn',
													'placeholder' => lang('config_saudi_tax_sn_placeholder'),
													'value'=>isset($location_zatca_config['csr_serial_number'])?$location_zatca_config['csr_serial_number']:""));?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_org_id').' - CSR'.':', 'saudi_tax_org_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saudi_tax_org_id',
													'id'=>'saudi_tax_org_id',
													'placeholder' => 'VAT or Group VAT Registration Number',
													'value'=>isset($location_zatca_config['csr_organization_identifier'])?$location_zatca_config['csr_organization_identifier']:""));?>
											</div>
											<?php echo form_label(('use 300075588700003 in test mode for the sandbox'), 'saudi_tax_org_id',array('class'=>'col-sm-offset-3 col-md-offset-3 col-lg-offset-2 col-sm-9 col-md-9 col-lg-10 control-label ', 'style' => 'text-align:left; text-transform: unset;')); ?>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
											<?php echo form_label(lang('config_saudi_tax_org_unit_name').' - CSR'.':', 'saudi_tax_org_unit_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
											<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'saudi_tax_org_unit_name',
												'id'=>'saudi_tax_org_unit_name',
												'placeholder' => lang('config_saudi_tax_org_unit_name'),
												'value'=> isset($location_zatca_config['csr_organization_unit_name'])?$location_zatca_config['csr_organization_unit_name']:""));?>
											</div>
										</div>
										
										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_org_name').' - CSR'.':', 'saudi_tax_org_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saudi_tax_org_name',
													'id'=>'saudi_tax_org_name',
													'placeholder' => lang('config_saudi_tax_payer_name'),
													'value'=>isset($location_zatca_config['csr_organization_name'])?$location_zatca_config['csr_organization_name']:""));?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_country_name').' - CSR'.':', 'saudi_tax_country_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php
													echo form_dropdown('saudi_tax_country_name',
														array(
															'SA'=>'SA',
														),
														isset($location_zatca_config['saudi_tax_country_name'])?$location_zatca_config['saudi_tax_country_name']:"",
														'class="form-control" id="saudi_tax_country_name"'
													);
												?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_invoice_type').' - CSR'.':', 'saudi_tax_invoice_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saudi_tax_invoice_type',
													'id'=>'saudi_tax_invoice_type',
													'placeholder' => lang('config_saudi_tax_invoice_type_placeholder'),
													'value'=>isset($location_zatca_config['csr_invoice_type'])?$location_zatca_config['csr_invoice_type']:""));?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('common_location').' - CSR'.':', 'saudi_tax_location',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saudi_tax_location',
													'id'=>'saudi_tax_location',
													'placeholder'=>lang('config_saudi_tax_location_placeholder'),
													'value'=>isset($location_zatca_config['csr_location_address'])?$location_zatca_config['csr_location_address']:""));?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_industry').' - CSR'.':', 'saudi_tax_industry',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saudi_tax_industry',
													'id'=>'saudi_tax_industry',
													'placeholder'=>lang('config_saudi_tax_industry_placeholder'),
													'value'=>isset($location_zatca_config['csr_industry_business_category'])?$location_zatca_config['csr_industry_business_category']:"" ));?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_seller_tax_id').' - ZATCA'.':', 'zatca_seller_tax_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'zatca_seller_tax_id',
													'id'=>'zatca_seller_tax_id',
													'placeholder'=>'',
													'value'=>isset($location_zatca_config['seller_tax_id'])?$location_zatca_config['seller_tax_id']:"" ));?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_seller_id').' - ZATCA'.':', 'zatca_seller_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'zatca_seller_id',
													'id'=>'zatca_seller_id',
													'placeholder'=>'',
													'value'=>isset($location_zatca_config['seller_id'])?$location_zatca_config['seller_id']:"" ));?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_seller_scheme_id').' - ZATCA'.':', 'zatca_seller_scheme_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_dropdown('zatca_seller_scheme_id',
													array(
														''=>lang('config_saudi_tax_seller_scheme_id_select'),
														'CRN'=>'CRN(Commercial Registration BN)',
														'TIN'=>'TIN(Tax Identification Number)',
														'MOM'=>'MOM(Momra license)',
														'MLS'=>"MLS(MLSD license)",
														'700'=>"700(700 Number)",
														'SAG'=>"SAG(Sagia license)",
														'NAT'=>"NAT(National ID)",
														'GCC'=>"GCC(GCC ID)",
														'OTH'=>"OTH(Other ID)",
													),
													isset($location_zatca_config['seller_scheme_id'])?$location_zatca_config['seller_scheme_id']:"",
													'class="form-control" id="zatca_seller_scheme_id"'
												);
												?>							
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_postal_street_name').' - ZATCA'.':', 'zatca_seller_party_postal_street_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(
													array(
														'name'=>'zatca_seller_party_postal_street_name',
														'id'=>'zatca_seller_party_postal_street_name',
														'class'=>'form-control',
														'value'=>isset($location_zatca_config['seller_party_postal_street_name'])?$location_zatca_config['seller_party_postal_street_name']:""
													)
												);
												?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_postal_building_number').' - ZATCA'.':', 'zatca_seller_party_postal_building_number',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(
													array(
														'name'=>'zatca_seller_party_postal_building_number',
														'id'=>'zatca_seller_party_postal_building_number',
														'class'=>'form-control',
														'value'=>isset($location_zatca_config['seller_party_postal_building_number'])?$location_zatca_config['seller_party_postal_building_number']:""
													)
												);
												?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_postal_code').' - ZATCA'.':', 'zatca_seller_party_postal_code',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(
													array(
														'name'=>'zatca_seller_party_postal_code',
														'id'=>'zatca_seller_party_postal_code',
														'class'=>'form-control',
														'placeholder'=>lang('config_saudi_tax_postal_code_placeholder'),
														'value'=>isset($location_zatca_config['seller_party_postal_code'])?$location_zatca_config['seller_party_postal_code']:""
													)
												);
												?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_postal_city_name').' - ZATCA'.':', 'zatca_seller_party_postal_city',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(
													array(
														'name'=>'zatca_seller_party_postal_city',
														'id'=>'zatca_seller_party_postal_city',
														'class'=>'form-control',
														'value'=>isset($location_zatca_config['seller_party_postal_city'])?$location_zatca_config['seller_party_postal_city']:""
													)
												);
												?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_postal_district_name').' - ZATCA'.':', 'zatca_seller_party_postal_district',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(
													array(
														'name'=>'zatca_seller_party_postal_district',
														'id'=>'zatca_seller_party_postal_district',
														'class'=>'form-control',
														'value'=>isset($location_zatca_config['seller_party_postal_district'])?$location_zatca_config['seller_party_postal_district']:""
													)
												);
												?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_postal_plot').' - ZATCA'.':', 'zatca_seller_party_postal_plot_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(
													array(
														'name'=>'zatca_seller_party_postal_plot_id',
														'id'=>'zatca_seller_party_postal_plot_id',
														'class'=>'form-control',
														'value'=>isset($location_zatca_config['seller_party_postal_plot_id'])?$location_zatca_config['seller_party_postal_plot_id']:""
													)
												);
												?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_saudi_tax_postal_country').' - ZATCA'.':', 'zatca_seller_party_postal_country',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php
													echo form_dropdown('zatca_seller_party_postal_country',
														array(
															'SA'=>'SA',
														),
														isset($location_zatca_config['seller_party_postal_country']) ? $location_zatca_config['seller_party_postal_country']:"",
														'class="form-control" id="zatca_seller_party_postal_country"'
													);
												?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label('ZATCA Onboarding'.':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<span class="pull-left">
													<button style="<?php echo isset($location_zatca_config['compliance_csid']) && $location_zatca_config['compliance_csid']  ? "display:none" : "" ?>" id="zatca_input_otp_ccsid_pcsid" type="button" class="btn btn-lg btn-primary"><span class="glyphicon glyphicon-check"></span> <?php echo lang('config_saudi_tax_generate'); ?> CCSID and PCSID </button>
													<button style="<?php echo isset($location_zatca_config['compliance_csid']) && $location_zatca_config['compliance_csid'] ? "" : "display:none" ?>" id="zatca_input_otp_renew_pcsid" type="button" class="btn btn-lg btn-primary"><span class="glyphicon glyphicon-check"></span> <?php echo lang('config_saudi_tax_renew'); ?> PCSID </button>

													<button style="<?php echo isset($location_zatca_config['csr']) && $location_zatca_config['csr'] ? "" : "display:none" ?>" id="zatca_display_csr_key" type="button" class="btn btn-lg btn-primary"><span class="glyphicon glyphicon-eye-open"></span> <?php echo lang('common_view'); ?> CSR/KEY </button>
													<button style="<?php echo isset($location_zatca_config['compliance_csid']) && $location_zatca_config['compliance_csid'] ? "" : "display:none" ?>" id="zatca_display_ccsid" type="button" class="btn btn-lg btn-primary"><span class="glyphicon glyphicon-eye-open"></span> <?php echo lang('common_view'); ?> CCSID </button>
													<button style="<?php echo isset($location_zatca_config['production_csid']) && $location_zatca_config['production_csid'] ? "" : "display:none" ?>" id="zatca_display_pcsid" type="button" class="btn btn-lg btn-primary"><span class="glyphicon glyphicon-eye-open"></span> <?php echo lang('common_view'); ?> PCSID </button>

													<button style="<?php echo $this->config->item('use_saudi_tax_test_config')? "" : "display:none;" ?> margin-left:32px;" id="zatca_init_test_db" type="button" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-trash"></span>Clean Test DB</button>

													<p style="display:none;" id="zatca_csr"><?php echo isset($location_zatca_config['csr']) && $location_zatca_config['csr'] ? $location_zatca_config['csr'] : "" ?></p>
													<p style="display:none;" id="zatca_csr_private_key"><?php echo isset($location_zatca_config['csr_private_key']) && $location_zatca_config['csr_private_key'] ? $location_zatca_config['csr_private_key'] : "" ?></p>
													<p style="display:none;" id="zatca_ccsid"><?php echo isset($location_zatca_config['compliance_csid']) && $location_zatca_config['compliance_csid'] ? $location_zatca_config['compliance_csid'] : "" ?></p>
													<p style="display:none;" id="zatca_pcsid"><?php echo isset($location_zatca_config['production_csid']) && $location_zatca_config['production_csid'] ? $location_zatca_config['production_csid'] : "" ?></p>
												</span>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label('', '', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<?php echo form_label('Please input Cert & PrivateKey after generate CCSID and PCSID.', '', array('class'=>'col-sm-9 col-md-9 col-lg-10 ' , 'style'=> 'padding-top:20px;')); ?>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(('Cert(Cert.Pem)').':', 'zatca_cert',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_textarea(array(
												'name'=>'zatca_cert',
												'id'=>'zatca_cert',
												'class'=>'form-control text-area',
												'rows'=>'10',
												'cols'=>'30',
												'value'=>$location_zatca_config ? $location_zatca_config['cert'] : ""));?>
											</div>
										</div>

										<div class="form-group saudi_tax_config_item <?php echo $this->config->item('use_saudi_tax_config') ? '' : 'hide'; ?>" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(('Private Key(Ec-Secp256k1-Priv-Key.Pem)').':', 'zatca_private_key',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_textarea(array(
												'name'=>'zatca_private_key',
												'id'=>'zatca_private_key',
												'class'=>'form-control text-area',
												'rows'=>'5',
												'cols'=>'30',
												'value'=>$location_zatca_config ? $location_zatca_config['private_key'] : ""));?>
											</div>
										</div>

										<!-- Tax Classes -->
										<div class="form-group no-padding-right" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">
											<?php echo form_label(lang('config_tax_classes').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-md-9 col-sm-9 col-lg-10">
												<div class="table-responsive">
													<table id="tax_classes" class="table">
														<thead>
															<tr>
																<th><?php echo lang('common_name'); ?></th>
																<th><?php echo lang('common_tax_name'); ?></th>
																<th><?php echo lang('common_tax_percent'); ?></th>			
																<th><?php echo lang('common_cumulative'); ?></th>	
																<th><?php echo lang('common_default'); ?></th>
																<th><?php echo lang('common_delete'); ?></th>		
																<th><?php echo lang('common_add'); ?></th>
																<th><?php echo lang('common_id'); ?></th>
																<th><?php echo lang('config_sort'); ?></th>
															</tr>
														</thead>
											
														<tbody>
															<?php
															foreach($tax_classes as $tax_class_id => $tax_class) { 
															?>
															<tr data-index="<?php echo H($tax_class_id); ?>">
																<td class="tax_class_name top">
																	<input type="text" class="rates form-control" name="tax_classes[<?php echo H($tax_class_id); ?>][name]" value="<?php echo H($tax_class['name']);?>" />
																	<?php foreach($tax_class['taxes'] as $tax_class_tax) { ?>
																		<input type="hidden" name="taxes[<?php echo H($tax_class_id); ?>][tax_class_tax_id][]" value="<?php echo H($tax_class_tax['id']); ?>">
																	<?php } ?>
																</td>
																<td class="tax_class_rate_name top">
																	<?php foreach($tax_class['taxes'] as $tax_class_taxes_data) { ?>
																		<input data-tax-class-tax-id="<?php echo H($tax_class_taxes_data['id']); ?>" type="text" class="rates form-control" name="taxes[<?php echo H($tax_class_id); ?>][name][]" value="<?php echo H($tax_class_taxes_data['name']);?>" />
																	<?php } ?>
																</td>
																<td class="tax_class_rate_percent top">
																	<?php foreach($tax_class['taxes'] as $tax_class_taxes_data) { ?>
																		<input type="text" class="rates form-control" name="taxes[<?php echo H($tax_class_id); ?>][percent][]" value="<?php echo H($tax_class_taxes_data['percent']);?>" />
																	<?php } ?>
																</td>
																<td class="tax_class_rate_cumulative top">
																	<?php 
																	$tax_class_cum_counter = 0;
																	foreach($tax_class['taxes'] as $tax_class_data) { 
																		$cum_id = 'tax_class_'.$tax_class_id.'_cumulative_'.$tax_class_cum_counter;
																		
																		if ($tax_class_cum_counter == 1)
																		{
																	?>
																			<?php echo form_checkbox('taxes['.H($tax_class_id).'][cumulative][]', '1', $tax_class_data['cumulative'],'id="'.$cum_id.'" class="form-control rates cumulative_checkbox"');  ?>
																			<label class="tax_class_cumulative_element" for="<?php echo $cum_id; ?>"><span></span></label>			
																	<?php
																}
																else
																{
																	?>
																			<?php 
																			echo form_hidden('taxes['.H($tax_class_id).'][cumulative][]', '0');
																			echo form_checkbox('taxes['.H($tax_class_id).'][cumulative][]', '1', $tax_class_data['cumulative'],'disabled id="'.$cum_id.'" class="form-control rates cumulative_checkbox invisible"');  ?>
																			<label class="tax_class_cumulative_element invisible" for="<?php echo $cum_id; ?>"><span></span></label>
																	<?php
																}
																	$tax_class_cum_counter++;
																	} ?>
																</td>
																<td class="tax_class_rate_default">
																	<?php 
																	$tax_class_default_counter = 0;
																		$default_id = 'tax_class_'.$tax_class_id.'_default_'.$tax_class_default_counter;
																	
																		echo form_radio(array(
																			'id' => $default_id,
																			'name' =>'tax_class_id',
																			'value' => $tax_class_id,
																			'checked' => $this->config->item('tax_class_id') == $tax_class_id ? 'checked' : '',
																		)); 
																	?>
																	<label class="tax_class_default_element" for="<?php echo $default_id; ?>"><span></span></label>
																																
																	<?php
																	$tax_class_default_counter++;
																	?>
																</td>
																<td>
																	<a class="delete_tax_rate tax_table_rate_text_element"><?php echo lang('common_delete'); ?></a>
																</td>
																<td><a href="javascript:void(0);" class="add_tax_rate"><?php echo lang('config_add_rate'); ?></a></td>
																<td><?php echo $tax_class_id; ?></td>
																<td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
															</tr>
															<?php } ?>
														</tbody>
													</table>
													
													<a href="javascript:void(0);" class="add_tax_class"><?php echo lang('config_add_tax_class'); ?></a>
													<!-- </div>								 -->
												</div><!-- end col -->
											</div>
										</div>
										<!-- end form-group -->
									
										<?php if (!$this->config->item('tax_class_id')) {?>
									
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
											<?php echo form_label(lang('common_default_tax_rate_1').':', 'default_tax_1_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-4 col-md-4 col-lg-5">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'default_tax_1_name',
												'placeholder' => lang('common_tax_name'),
												'id'=>'default_tax_1_name',
												'size'=>'10',
												'value'=>$this->config->item('default_tax_1_name')!==NULL ? $this->config->item('default_tax_1_name') : lang('common_sales_tax_1')));?>
											</div>
											<div class="col-sm-4 col-md-4 col-lg-5">
												<div class="input-group">
													<?php echo form_input(array(
													'class'=>'form-control form-inps-tax',
													'placeholder' => lang('common_tax_percent'),
													'name'=>'default_tax_1_rate',
													'id'=>'default_tax_1_rate',
													'size'=>'4',
													'value'=>$this->config->item('default_tax_1_rate')));?>
													<span class="input-group-addon">%</span>
												</div>
												<div class="clear"></div>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
											<?php echo form_label(lang('common_default_tax_rate_2').':', 'default_tax_1_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-4 col-md-4 col-lg-5">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'default_tax_2_name',
												'placeholder' => lang('common_tax_name'),
												'id'=>'default_tax_2_name',
												'size'=>'10',
												'value'=>$this->config->item('default_tax_2_name')!==NULL ? $this->config->item('default_tax_2_name') : lang('common_sales_tax_2')));?>
											</div>

											<div class="col-sm-4 col-md-4 col-lg-5">
												<div class="input-group">
													<?php echo form_input(array(
													'class'=>'form-control form-inps-tax',	
													'name'=>'default_tax_2_rate',
													'placeholder' => lang('common_tax_percent'),
													'id'=>'default_tax_2_rate',
													'size'=>'4',
													'value'=>$this->config->item('default_tax_2_rate')));?>
													<span class="input-group-addon">%</span>
												</div>
												<div class="clear"></div>
												<?php echo form_checkbox('default_tax_2_cumulative', '1', $this->config->item('default_tax_2_cumulative') ? true : false, 'id="default_tax_2_cumulative" class="cumulative_checkbox"');  ?>
												<label for="default_tax_2_cumulative"><span></span></label>
												<span class="cumulative_label">
													<?php echo lang('common_cumulative'); ?>
												</span>
											</div>
											<div class="col-sm-9 col-sm-offset-3 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3" style="display: <?php echo $this->config->item('default_tax_3_rate') ? 'none' : 'block';?>">
												<a href="javascript:void(0);" class="show_more_taxes btn btn-orange btn-round"><?php echo lang('common_show_more');?> &raquo;</a>
											</div>
								
											<div class="col-md-12 more_taxes_container" style="display: <?php echo $this->config->item('default_tax_3_rate') ? 'block' : 'none';?>">
												<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
													<?php echo form_label(lang('common_default_tax_rate_3').':', 'default_tax_3_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
													<div class="col-sm-4 col-md-4 col-lg-5">
														<?php echo form_input(array(
														'class'=>'form-control form-inps',
														'name'=>'default_tax_3_name',
														'placeholder' => lang('common_tax_name'),
														'id'=>'default_tax_3_name',
														'size'=>'10',
														'value'=>$this->config->item('default_tax_3_name')!==NULL ? $this->config->item('default_tax_3_name') : ''));?>
													</div>
								
													<div class="col-sm-4 col-md-4 col-lg-5">
														<div class="input-group">
															<?php echo form_input(array(
															'class'=>'form-control form-inps-tax',
															'placeholder' => lang('common_tax_percent'),
															'name'=>'default_tax_3_rate',
															'id'=>'default_tax_3_rate',
															'size'=>'4',
															'value'=>$this->config->item('default_tax_3_rate')));?>
															<span class="input-group-addon">%</span>
														</div>
														<div class="clear"></div>
													</div>
												</div>
									
												<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
													<?php echo form_label(lang('common_default_tax_rate_4').':', 'default_tax_4_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
													<div class="col-sm-4 col-md-4 col-lg-5">
														<?php echo form_input(array(
														'class'=>'form-control form-inps',
														'placeholder' => lang('common_tax_name'),
														'name'=>'default_tax_4_name',
														'id'=>'default_tax_4_name',
														'size'=>'10',
														'value'=>$this->config->item('default_tax_4_name')!==NULL ? $this->config->item('default_tax_4_name') : ''));?>
													</div>
								
													<div class="col-sm-4 col-md-4 col-lg-5">
														<div class="input-group">
															<?php echo form_input(array(
															'class'=>'form-control form-inps-tax',
															'placeholder' => lang('common_tax_percent'),
															'name'=>'default_tax_4_rate',
															'id'=>'default_tax_4_rate',
															'size'=>'4',
															'value'=>$this->config->item('default_tax_4_rate')));?>
															<span class="input-group-addon">%</span>
														</div>
														<div class="clear"></div>
													</div>
												</div>
									
												<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
													<?php echo form_label(lang('common_default_tax_rate_5').':', 'default_tax_5_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
													<div class="col-sm-4 col-md-4 col-lg-5">
														<?php echo form_input(array(
														'class'=>'form-control form-inps',
														'placeholder' => lang('common_tax_name'),
														'name'=>'default_tax_5_name',
														'id'=>'default_tax_5_name',
														'size'=>'10',
														'value'=>$this->config->item('default_tax_5_name')!==NULL ? $this->config->item('default_tax_5_name') : ''));?>
													</div>
								
													<div class="col-sm-4 col-md-4 col-lg-5">
														<div class="input-group">
															<?php echo form_input(array(
															'class'=>'form-control form-inps-tax',
															'placeholder' => lang('common_tax_percent'),
															'name'=>'default_tax_5_rate',
															'id'=>'default_tax_5_rate',
															'size'=>'4',
															'value'=>$this->config->item('default_tax_5_rate')));?>
															<span class="input-group-addon">%</span>
														</div>
														<div class="clear"></div>
													</div>
												</div>
											</div>
										</div>

										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Taxes -->

				<!-- Currency -->
				<div class="tab-pane fade" id="currency">
					<div class="row">
						<div class="col-md-12 tax_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#currency_collapse">
									<?php echo create_section(lang("config_currency_info")) ?>
									</a>
								</div>
								<div id="currency_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">
											<?php echo form_label(lang('config_currency_symbol').':', 'currency_symbol',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'currency_symbol',
												'id'=>'currency_symbol',
												'value'=>$this->config->item('currency_symbol')));?>
											</div>
										</div>	

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
											<?php echo form_label(lang('config_currency_code').':', 'currency_code',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'currency_code',
												'id'=>'currency_code',
												'value'=>$this->config->item('currency_code')));?>
											</div>
										</div>	


										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
										<?php echo form_label(lang('config_currency_exchange_rates').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="table-responsive col-sm-9 col-md-9 col-lg-10">
											<table id="currency_exchange_rates" class="table">
												<thead>
													<tr>
													<th><?php echo lang('common_exchange_to'); ?></th>
													<th><?php echo lang('config_currency_symbol'); ?></th>
													<th><?php echo lang('config_currency_symbol_location'); ?></th>
													<th><?php echo lang('config_number_of_decimals'); ?></th>
													<th><?php echo lang('config_thousands_separator'); ?></th>
													<th><?php echo lang('config_decimal_point'); ?></th>
													<th><?php echo lang('config_exchange_rate'); ?></th>
													<th><?php echo lang('common_delete'); ?></th>
													</tr>
												</thead>
												
												<tbody>
												<?php foreach($currency_exchange_rates->result() as $currency_exchange_rate) { ?>
													<tr>
														<td><input type="text" name="currency_exchange_rates_to[]" class="form-control" value="<?php echo H($currency_exchange_rate->currency_code_to); ?>" /></td>
														<td><input type="text" name="currency_exchange_rates_symbol[]" class="form-control" value="<?php echo H($currency_exchange_rate->currency_symbol); ?>" /></td>
														<td><?php echo form_dropdown('currency_exchange_rates_symbol_location[]', array(
															'before'    => lang('config_before_number'),
															'after'    => lang('config_after_number'),
														),$currency_exchange_rate->currency_symbol_location,'class="form-control"');?></td>
														<td><?php echo form_dropdown('currency_exchange_rates_number_of_decimals[]', array(
																''  => lang('config_let_system_decide'),
																'0'    => '0',
																'1'    => '1',
																'2'    => '2',
																'3'    => '3',
																'4'    => '4',
																'5'    => '5',
															),$currency_exchange_rate->number_of_decimals
																, 'class="form-control" id="number_of_decimals"');
															?></td>
														<td><input type="text" name="currency_exchange_rates_thousands_separator[]" class="form-control" value="<?php echo H($currency_exchange_rate->thousands_separator); ?>" /></td>
														<td><input type="text" name="currency_exchange_rates_decimal_point[]" class="form-control" value="<?php echo H($currency_exchange_rate->decimal_point); ?>" /></td>
														<td><input type="text" name="currency_exchange_rates_rate[]" class="form-control" value="<?php echo H(to_currency_no_money($currency_exchange_rate->exchange_rate,10)); ?>" /></td>
														<td><a class="delete_currency_exchange_rate text-primary" href="javascript:void(0);"><?php echo lang('common_delete'); ?></a></td>
													</tr>
												<?php } ?>
												</tbody>
											</table>
											
											<a href="javascript:void(0);" id="add_exchange_rate"><?php echo lang('config_add_currency_exchange_rate'); ?></a>
											</div>
										</div>


										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
										<?php echo form_label(lang('config_currency_symbol_location').':', 'currency_symbol_location',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('currency_symbol_location', array(
												'before'    => lang('config_before_number'),
												'after'    => lang('config_after_number'),
											),
												$this->config->item('currency_symbol_location')===NULL ? 'before' : $this->config->item('currency_symbol_location') , 'class="form-control" id="currency_symbol_location"');
												?>
											</div>						
										</div>				
												
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
										<?php echo form_label(lang('config_number_of_decimals').':', 'number_of_decimals',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('number_of_decimals', array(
												''  => lang('config_let_system_decide'),
												'0'    => '0',
												'1'    => '1',
												'2'    => '2',
												'3'    => '3',
												'4'    => '4',
												'5'    => '5',
											),
												$this->config->item('number_of_decimals')===NULL ? '' : $this->config->item('number_of_decimals') , 'class="form-control" id="number_of_decimals"');
												?>
											</div>						
										</div>				

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
											<?php echo form_label(lang('config_thousands_separator').':', 'thousands_separator',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(array(
													'class'=>'validate form-control form-inps',
												'name'=>'thousands_separator',
												'id'=>'thousands_separator',
												'value'=>$this->config->item('thousands_separator') ? $this->config->item('thousands_separator') : ','));?>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
											<?php echo form_label(lang('config_decimal_point').':', 'decimal_point',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10 input-field">
												<?php echo form_input(array(
													'class'=>'validate form-control form-inps',
												'name'=>'decimal_point',
												'id'=>'decimal_point',
												'value'=>$this->config->item('decimal_point') ? $this->config->item('decimal_point') : '.'));?>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
											<?php echo form_label(lang('config_currency_denoms').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="table-responsive col-sm-9 col-md-4 col-lg-4">
												<table id="currency_denoms" class="table">
													<thead>
														<tr>
														<th><?php echo lang('common_denomination'); ?></th>
														<th><?php echo lang('config_currency_value'); ?></th>
														<th><?php echo lang('common_delete'); ?></th>
														</tr>
													</thead>
													
													<tbody>
													<?php foreach($currency_denoms->result() as $currency_denom) { ?>
														<tr>
															<td><input type="text" name="currency_denoms_name[]" class="form-control" value="<?php echo H($currency_denom->name); ?>" /></td>
															<td><input type="text" name="currency_denoms_value[]" class="form-control" value="<?php echo H(to_currency_no_money($currency_denom->value)); ?>" /></td>
															<td><a class="delete_currency_denom text-primary" data-id="<?php echo H($currency_denom->id); ?>" href="javascript:void(0);"><?php echo lang('common_delete'); ?></a></td>
															<input type="hidden" name="currency_denoms_ids[]" value="<?php echo H($currency_denom->id); ?>" />
														</tr>
													<?php } ?>
													</tbody>
												</table>
											
												<a href="javascript:void(0);" id="add_denom"><?php echo lang('config_add_currency_denom'); ?></a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Currency -->

				<!-- Payment Types -->
				<div class="tab-pane fade" id="payment_types">
					<div class="row">
						<div class="col-md-12 payment_types_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#payment_types_collapse">
									<?php echo create_section(lang("config_payment_types_info")) ?>
									</a>
								</div>
								<div id="payment_types_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
								
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_payment')) ?>">	
											<?php echo form_label(lang('config_payment_types').':', 'additional_payment_types',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_cash'); ?></a> 
												<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_check'); ?></a> 
												<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_giftcard'); ?></a> 
												<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_debit'); ?></a> 
												<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_credit'); ?></a>
												<br>
												<br>
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'additional_payment_types',
													'id'=>'additional_payment_types',
													'size'=> 40,
													'value'=>$this->config->item('additional_payment_types')));?>
											</div>
										</div>
										
										<?php
										
										$markup_markdown = array();
										if ($this->config->item('markup_markdown'))
										{
											$markup_markdown = unserialize($this->config->item('markup_markdown'));
										}
										
										foreach(array_keys($this->Sale->get_payment_options_with_language_keys()) as $payment_type)
										{
										?>
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_payment')) ?>">
												<?php echo form_label($payment_type.' '.lang('config_markup_markdown').' '.lang('common_percentage'), 'payment_type_markup_markdown',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													
													<?php 
													$markup_down_value = isset($markup_markdown[$payment_type]) ? $markup_markdown[$payment_type] : '';
													echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'markup_markdown['.hex_encode($payment_type).']',
													'id'=>'sale_prefix',
													'value'=>$markup_down_value));?>
												</div>
											</div>
											
										<?php
										}
										?>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_payment')) ?>">	
											<?php echo form_label(lang('config_default_payment_type').':', 'default_payment_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('default_payment_type', $payment_options, $this->config->item('default_payment_type'),'class="form-control" id="default_payment_type"'); ?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_payment')) ?>">	
											<?php echo form_label(lang('config_default_payment_type_recv').':', 'default_payment_type_recv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('default_payment_type_recv', $payment_options, $this->config->item('default_payment_type_recv'),'class="form-control" id="default_payment_type_recv"'); ?>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_payment')) ?>">
											<?php echo form_label(lang('config_show_selling_price_on_recv'). ':', 'show_selling_price_on_recv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
												'name'=>'show_selling_price_on_recv',
												'id'=>'show_selling_price_on_recv',
												'value'=>'1',
												'checked'=>$this->config->item('show_selling_price_on_recv')));?>
												<label for="show_selling_price_on_recv"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_payment')) ?>">
											<?php echo form_label(lang('config_enable_ebt_payments'). ':', 'enable_ebt_payments',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
												'name'=>'enable_ebt_payments',
												'id'=>'enable_ebt_payments',
												'value'=>'1',
												'checked'=>$this->config->item('enable_ebt_payments')));?>
												<label for="enable_ebt_payments"><span></span></label>
											</div>
										</div>
				
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_payment')) ?>">
											<?php echo form_label(lang('config_enable_wic'). ':', 'enable_wic',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
												'name'=>'enable_wic',
												'id'=>'enable_wic',
												'value'=>'1',
												'checked'=>$this->config->item('enable_wic')));?>
												<label for="enable_wic"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_payment')) ?>">	
											<?php echo form_label(lang('config_prompt_for_ccv_swipe').':', 'prompt_for_ccv_swipe',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'prompt_for_ccv_swipe',
												'id'=>'prompt_for_ccv_swipe',
												'value'=>'1',
												'checked'=>$this->config->item('prompt_for_ccv_swipe')));?>
												<label for="prompt_for_ccv_swipe"><span></span></label>
											</div>
										</div>					
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Payment Types -->

				<!-- Price Rules -->
				<div class="tab-pane fade" id="price_rules">
					<div class="row">
						<div class="col-md-12 price_rules_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#price_rules_collapse">
									<?php echo create_section(lang("config_price_rules_info")) ?>
									</a>
								</div>
								<div id="price_rules_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_price_rules')) ?>">	
											<?php echo form_label(lang('config_disable_price_rules_dialog').':', 'disable_price_rules_dialog',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_price_rules_dialog',
												'id'=>'disable_price_rules_dialog',
												'value'=>'disable_price_rules_dialog',
												'checked'=>$this->config->item('disable_price_rules_dialog')));?>
												<label for="disable_price_rules_dialog"><span></span></label>
											</div>
										</div>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Price Rules -->

				<!-- Orders and Deliveries -->
				<div class="tab-pane fade" id="orders_deliveries">
					<div class="row">
						<div class="col-md-12 orders_and_deliveries_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#orders_deliveries_collapse">
									<?php echo create_section(lang("config_orders_and_deliveries_info")) ?>
									</a>
								</div>
								<div id="orders_deliveries_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">					
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_orders_deliveries')) ?>">	
											<?php echo form_label(lang('config_do_not_tax_service_items_for_deliveries').':', 'do_not_tax_service_items_for_deliveries',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_tax_service_items_for_deliveries',
												'id'=>'do_not_tax_service_items_for_deliveries',
												'value'=>'1',
												'checked'=>$this->config->item('do_not_tax_service_items_for_deliveries')));?>
												<label for="do_not_tax_service_items_for_deliveries"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_orders_deliveries')) ?>">	
											<?php echo form_label(lang('config_delivery_color_based_on').':', 'delivery_color_based_on',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php
													$color_options = array(
														'status' => lang('config_delivery_color_based_on_status'),
														'category' => lang('config_delivery_color_based_on_category')
													);
													echo form_dropdown('delivery_color_based_on', $color_options, $this->config->item('delivery_color_based_on'),'class="form-control" id="delivery_color_based_on"');
												?>
											</div>
										</div>
											
										<div class="form-group no-padding-right" data-keyword="<?php echo H(lang('config_keyword_orders_deliveries')) ?>">	
											<?php echo form_label(lang('config_shipping_providers').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-md-9 col-sm-9 col-lg-10">
													<div class="table-responsive">
														<table id="shipping_providers" class="table">
															<thead>
																<tr>
																	<th><?php echo lang('common_name'); ?></th>
																	<th><?php echo lang('config_rate_name'); ?></th>
																	<th><?php echo lang('config_rate_fee'); ?></th>			
																	<th><?php echo lang('config_delivery_time'); ?></th>
																					
																	<th><?php echo lang('common_default'); ?></th>
																	<th><?php echo lang('common_delete'); ?></th>
																	
																	<th><?php echo lang('common_add'); ?></th>
																	<th><?php echo lang('config_sort'); ?></th>
																</tr>
															</thead>
															
															<tbody>
																
															<?php
															foreach($shipping_providers->result_array() as $shipping_provider) { 
																$provider_id = $shipping_provider['id'];
																
																$methods = $this->Shipping_method->get_all($provider_id)->result_array();
																$sorted_shipping_methods = array();
																
																foreach($methods as $method)
																{
																	$sorted_shipping_methods['id'][] = $method['id'];
																	$sorted_shipping_methods['name'][] = $method['name'];
																	$sorted_shipping_methods['fee'][] = $method['fee'];
																	$sorted_shipping_methods['time_in_days'][] = $method['time_in_days'];
																	$sorted_shipping_methods['is_default'][] = $method['is_default'];
																}
																	
																//If we don't have any methods the 1st row should have blanks
																if (empty($sorted_shipping_methods['name']))
																{
																	$sorted_shipping_methods['id'][] = '';
																		$sorted_shipping_methods['name'][] = '';
																		$sorted_shipping_methods['fee'][] = 0;
																		$sorted_shipping_methods['fee_tax'][] = 0;
																		$sorted_shipping_methods['time_in_days'][] = '';
																		$sorted_shipping_methods['is_default'][] = '';
																}
																	
																?>
																<tr data-index="<?php echo H($provider_id); ?>">
																	<td class="shipping_provider_name top">
																		<input type="text" class="rates form-control" name="providers[<?php echo H($provider_id); ?>][name]" value="<?php echo H($shipping_provider['name']);?>" />
																		<?php foreach($sorted_shipping_methods['name'] as $index => $name) { ?>
																			<input type="hidden" name="methods[<?php echo H($provider_id); ?>][method_id][]" value="<?php echo H($sorted_shipping_methods['id'][$index]); ?>">
																		<?php } ?>
																	</td>
																	<td class="delivery_rate_name top">
																		
																		<?php foreach($sorted_shipping_methods['name'] as $index => $name) { ?>
																			<input data-method-id="<?php echo H($sorted_shipping_methods['id'][$index]); ?>" type="text" class="rates form-control" name="methods[<?php echo H($provider_id); ?>][name][]" value="<?php echo H($name);?>" />
																		<?php } ?>
																	</td>
																	
																	<td class="delivery_fee top">
																		<?php foreach($sorted_shipping_methods['fee'] as $fee) { ?>
																			<input type="text" class="rates form-control" name="methods[<?php echo H($provider_id); ?>][fee][]" value="<?php echo H(to_currency_no_money($fee));?>" />
																		<?php } ?>
																	</td>
																	
																	<td class="delivery_time top">
																		<?php foreach($sorted_shipping_methods['time_in_days'] as $time_in_days) { ?>
																			<input type="text" class="rates form-control" name="methods[<?php echo H($provider_id); ?>][time_in_days][]" value="<?php echo H(to_quantity($time_in_days, ''));?>" />
																		<?php } ?>
																	</td>
																	
																	
																	<td class="delivery_default top">
																		<?php 
																		$i = 0;
																		foreach($sorted_shipping_methods['is_default'] as $is_default) { 
																			echo form_radio(array(
																				'id' => 'default_shipping_rate_'. $provider_id . '_' . $i,
																				'name' =>'methods['. H($provider_id) .']'.'[is_default][]',
																				'value' => '1',
																				'checked' => $is_default == 1 ? 'checked' : '',
																			)); 
																		?>
																		<label class="shipping_table_rate_element" for="<?php echo H('default_shipping_rate_' . $provider_id . '_' . $i); ?>"><span></span></label>
																		<?php
																			$i++;
																		} ?>
																	</td>
																	<td>
																		<a class="delete_rate shipping_table_rate_text_element"><?php echo lang('common_delete'); ?></a>
																	</td>
																	<td><a href="javascript:void(0);" class="add_delivery_rate"><?php echo lang('config_add_rate'); ?></a></td>
																	<td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
															</tr>
															
															<?php } ?>
															</tbody>
														</table>
														
														<a href="javascript:void(0);" class="add_shipping_provider"><?php echo lang('config_add_shipping_provider'); ?></a>
														</div>
													</div>
												</div>
												
												<div class="form-group no-padding-right" data-keyword="<?php echo H(lang('config_keyword_orders_deliveries')) ?>">	
													<?php echo form_label(lang('config_shipping_zones').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
														<div class="col-md-9 col-sm-9 col-lg-10">
															<div class="table-responsive">
																<table id="shipping_zones" class="table">
																	<thead>
																		<tr>
																			<th><?php echo lang('common_name'); ?></th>
																			<th><?php echo lang('common_zips').' ('.lang('config_support_regex'); ?>)</th>
																			<th><?php echo lang('common_fee'); ?></th>
																			<th><?php echo lang('config_tax_class'); ?></th>
																			<th><?php echo lang('common_delete'); ?></th>
																			<th><?php echo lang('config_sort'); ?></th>
																		</tr>
																	</thead>
															
																	<tbody>
																
																	<?php
																	foreach($shipping_zones->result_array() as $shipping_zone) { 
																		$zone_id = $shipping_zone['id'];
																		$zips_for_zone = array();
																		$zips_for_zone_str = '';
																		foreach($this->Zip->get_zips_for_zone($zone_id)->result_array() as $zip_row)
																		{
																			$zips_for_zone[] = $zip_row['name'];
																		}
																		
																		
																		$zips_for_zone_str = implode('|',$zips_for_zone);
																		?>
																		<tr data-index="<?php echo H($zone_id); ?>">
																			<td class="shipping_zone_name top" style="width: 10%; min-width:100px;">
																				<input type="text" class="zones form-control" name="zones[<?php echo H($zone_id); ?>][name]" value="<?php echo H($shipping_zone['name']);?>" />
																			</td>
																			
																			<td class="shipping_zone_zips top" style="width: 50%;">
																				<input type="text" class="zones form-control" name="zones[<?php echo H($zone_id); ?>][zips]" value="<?php echo H($zips_for_zone_str);?>" />
																			</td>
																			
																			<td class="shipping_zone_fee top" style="width: 10%; min-width:100px;">
																				<input type="text" class="zones form-control" name="zones[<?php echo H($zone_id); ?>][fee]" value="<?php echo H(to_currency_no_money($shipping_zone['fee']));?>" />
																			</td>
																			
																			<td class="shipping_zone_tax_group top" style="width: 10%; min-width:200px;">
																				<select class="zones form-control" name="zones[<?php echo H($zone_id); ?>][tax_class_id]">
																					<?php foreach($tax_groups as $tax_group) { ?>
																						<option value="<?php echo $tax_group['val'] ?>" <?php echo $shipping_zone['tax_class_id'] == $tax_group['val'] ? 'selected="selected"' : '' ?>><?php echo $tax_group['text'] ?></option>
																					<?php } ?>
																				</select>
																			</td>
																			
																			<td style="width: 10%;">
																				<a class="delete_zone"><?php echo lang('common_delete'); ?></a>
																			</td>
																			
																			<td style="width: 10%;"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
																	</tr>
															
																	<?php } //end shipping zones ?>
																	</tbody>
																</table>
														
																<a href="javascript:void(0);" class="add_shipping_zone"><?php echo lang('config_add_shipping_zone'); ?></a>
																</div>
															</div>
														</div>

												<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_orders_deliveries')) ?>">	
													<?php echo form_label(lang('config_default_employee_for_deliveries').':', 'default_employee_for_deliveries',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
													<div class="col-sm-9 col-md-9 col-lg-10">
													<?php 
														$employees = array('' => lang('common_logged_in_employee'));

														foreach($this->Employee->get_all()->result() as $employee)
														{
															$employees[$employee->person_id] = $employee->first_name .' '.$employee->last_name;
														}
														
														echo form_dropdown('default_employee_for_deliveries', $employees, $this->config->item('default_employee_for_deliveries'), 'class="form-control" id="default_employee_for_deliveries"'); 
													?>
													</div>
												</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Orders and Deliveries -->

				<!-- Returns -->
				<div class="tab-pane fade" id="returns">
					<div class="row">
						<div class="col-md-12 returns_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#returns_collapse">
									<?php echo create_section(lang("common_return_info")) ?>
									</a>
								</div>
								<div id="returns_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<div class="form-group" data-keyword="<?php echo H(lang('common_return_info')) ?>">	
											<?php echo form_label(lang('config_return_reasons').':', 'return_reasons',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'return_reasons',
													'id'=>'return_reasons',
													'size'=> 40,
													'value'=>$this->config->item('return_reasons')));?>
											</div>
										</div>

										<script>
										$('#return_reasons').selectize({
											delimiter: ',',
											persist: false,
											create: function(input) {
												return {
													value: input,
													text: input
												}
											}
										});	
										</script>
						
										<div class="form-group" data-keyword="<?php echo H(lang('common_return_info')) ?>">
											<?php echo form_label(lang('config_require_customer_for_return'). ':', 'require_customer_for_return',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
												'name'=>'require_customer_for_return',
												'id'=>'require_customer_for_return',
												'value'=>'1',
												'checked'=>$this->config->item('require_customer_for_return')));?>
												<label for="require_customer_for_return"><span></span></label>
											</div>
										</div>
						
										<div class="form-group" data-keyword="<?php echo H(lang('common_return_info')) ?>">
											<?php echo form_label(lang('config_require_receipt_for_return'). ':', 'require_receipt_for_return',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
												'name'=>'require_receipt_for_return',
												'id'=>'require_receipt_for_return',
												'value'=>'1',
												'checked'=>$this->config->item('require_receipt_for_return')));?>
												<label for="require_receipt_for_return"><span></span></label>
											</div>
										</div>
						
										<div class="form-group" data-keyword="<?php echo H(lang('common_return_info')) ?>">
											<?php echo form_label(lang('config_prompt_for_sale_id_on_return'). ':', 'prompt_for_sale_id_on_return',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
												'name'=>'prompt_for_sale_id_on_return',
												'id'=>'prompt_for_sale_id_on_return',
												'value'=>'1',
												'checked'=>$this->config->item('prompt_for_sale_id_on_return')));?>
												<label for="prompt_for_sale_id_on_return"><span></span></label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Returns -->

				<!-- Sales -->
				<div class="tab-pane fade" id="sales">
					<div class="row">
						<div class="col-md-12 sales_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#sales_collapse">
									<?php echo create_section(lang("config_sales_info")) ?>
									</a>
								</div>
								<div id="sales_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_prefix').':', 'sale_prefix',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
												'name'=>'sale_prefix',
												'id'=>'sale_prefix',
												'value'=>$this->config->item('sale_prefix')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_id_to_show_on_sale_interface').':', 'id_to_show_on_sale_interface',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('id_to_show_on_sale_interface', array(
												'number'  => lang('common_item_number_expanded'),
												'product_id'    => lang('common_product_id'),
												'id'   => lang('common_item_id')
												),
												$this->config->item('id_to_show_on_sale_interface'), 'class="form-control" id="id_to_show_on_sale_interface"')
												?>
											</div>
										</div>
										
																
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">
											<?php echo form_label(lang('config_hide_supplier_on_sales_interface'). ':', 'hide_supplier_on_sales_interface',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
												'name'=>'hide_supplier_on_sales_interface',
												'id'=>'hide_supplier_on_sales_interface',
												'value'=>'1',
												'checked'=>$this->config->item('hide_supplier_on_sales_interface')));?>
												<label for="hide_supplier_on_sales_interface"><span></span></label>
											</div>
										</div>
										
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">
											<?php echo form_label(lang('config_hide_supplier_on_recv_interface'). ':', 'hide_supplier_on_recv_interface',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
												'name'=>'hide_supplier_on_recv_interface',
												'id'=>'hide_supplier_on_recv_interface',
												'value'=>'1',
												'checked'=>$this->config->item('hide_supplier_on_recv_interface')));?>
												<label for="hide_supplier_on_recv_interface"><span></span></label>
											</div>
										</div>
										
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">
											<?php echo form_label(lang('config_allow_drag_drop_sale'). ':', 'allow_drag_drop_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
												'name'=>'allow_drag_drop_sale',
												'id'=>'allow_drag_drop_sale',
												'value'=>'1',
												'checked'=>$this->config->item('allow_drag_drop_sale')));?>
												<label for="allow_drag_drop_sale"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">
											<?php echo form_label(lang('config_allow_drag_drop_recv'). ':', 'allow_drag_drop_recv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
												'name'=>'allow_drag_drop_recv',
												'id'=>'allow_drag_drop_recv',
												'value'=>'1',
												'checked'=>$this->config->item('allow_drag_drop_recv')));?>
												<label for="allow_drag_drop_recv"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_disable_discounts_percentage_per_line_item').':', 'disable_discounts_percentage_per_line_item',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_discounts_percentage_per_line_item',
												'id'=>'disable_discounts_percentage_per_line_item',
												'value'=>'1',
												'checked'=>$this->config->item('disable_discounts_percentage_per_line_item')));?>
												<label for="disable_discounts_percentage_per_line_item"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_disabled_fixed_discounts').':', 'disabled_fixed_discounts',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disabled_fixed_discounts',
												'id'=>'disabled_fixed_discounts',
												'value'=>'1',
												'checked'=>$this->config->item('disabled_fixed_discounts')));?>
												<label for="disabled_fixed_discounts"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_disable_discount_by_percentage').':', 'disable_discount_by_percentage',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_discount_by_percentage',
												'id'=>'disable_discount_by_percentage',
												'value'=>'1',
												'checked'=>$this->config->item('disable_discount_by_percentage')));?>
												<label for="disable_discount_by_percentage"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('common_disable_sale_cloning').':', 'disable_sale_cloning',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_sale_cloning',
												'id'=>'disable_sale_cloning',
												'value'=>'1',
												'checked'=>$this->config->item('disable_sale_cloning')));?>
												<label for="disable_sale_cloning"><span></span></label>
											</div>
										</div>
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_always_put_last_added_item_on_top_of_cart').':', 'always_put_last_added_item_on_top_of_cart',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'always_put_last_added_item_on_top_of_cart',
												'id'=>'always_put_last_added_item_on_top_of_cart',
												'value'=>'1',
												'checked'=>$this->config->item('always_put_last_added_item_on_top_of_cart')));?>
												<label for="always_put_last_added_item_on_top_of_cart"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('common_disable_recv_cloning').':', 'disable_recv_cloning',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_recv_cloning',
												'id'=>'disable_recv_cloning',
												'value'=>'1',
												'checked'=>$this->config->item('disable_recv_cloning')));?>
												<label for="disable_recv_cloning"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_scan_and_set_sales').':', 'scan_and_set_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'scan_and_set_sales',
												'id'=>'scan_and_set_sales',
												'value'=>'1',
												'checked'=>$this->config->item('scan_and_set_sales')));?>
												<label for="scan_and_set_sales"><span></span></label>
											</div>
										</div>
										
										
				
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_scan_and_set_recv').':', 'scan_and_set_recv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'scan_and_set_recv',
												'id'=>'scan_and_set_recv',
												'value'=>'1',
												'checked'=>$this->config->item('scan_and_set_recv')));?>
												<label for="scan_and_set_recv"><span></span></label>
											</div>
										</div>
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_damaged_reasons').':', 'damaged_reasons',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'damaged_reasons',
												'id'=>'damaged_reasons',
												'size'=> 40,
												'value'=>$this->config->item('damaged_reasons')));?>
											</div>
											
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_enable_tips').':', 'enable_tips',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'enable_tips',
												'id'=>'enable_tips',
												'value'=>'enable_tips',
												'checked'=>$this->config->item('enable_tips')));?>
												<label for="enable_tips"><span></span></label>
												<p><?php echo lang('config_not_all_processors_support_tips'); ?></p>
											</div>
											
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_tip_preset_zero').':', 'tip_preset_zero',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'tip_preset_zero',
												'id'=>'tip_preset_zero',
												'value'=>'tip_preset_zero',
												'checked'=>$this->config->item('tip_preset_zero')));?>
												<label for="tip_preset_zero"><span></span></label>
											</div>
											
										</div>
										
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_auto_focus_on_item_after_sale_and_receiving').':', 'auto_focus_on_item_after_sale_and_receiving',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'auto_focus_on_item_after_sale_and_receiving',
												'id'=>'auto_focus_on_item_after_sale_and_receiving',
												'value'=>'auto_focus_on_item_after_sale_and_receiving',
												'checked'=>$this->config->item('auto_focus_on_item_after_sale_and_receiving')));?>
												<label for="auto_focus_on_item_after_sale_and_receiving"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_capture_internal_notes_during_sale').':', 'capture_internal_notes_during_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'capture_internal_notes_during_sale',
												'id'=>'capture_internal_notes_during_sale',
												'value'=>'capture_internal_notes_during_sale',
												'checked'=>$this->config->item('capture_internal_notes_during_sale')));?>
												<label for="capture_internal_notes_during_sale"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_capture_sig_for_all_payments').':', 'capture_sig_for_all_payments',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'capture_sig_for_all_payments',
												'id'=>'capture_sig_for_all_payments',
												'value'=>'capture_sig_for_all_payments',
												'checked'=>$this->config->item('capture_sig_for_all_payments')));?>
												<label for="capture_sig_for_all_payments"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_number_of_recent_sales').':', 'number_of_recent_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
				
											<?php echo form_dropdown('number_of_recent_sales', 
											array(
												'10'=>'10',
												'20'=>'20',
												'50'=>'50',
												'100'=>'100',
												'200'=>'200',
												'500'=>'500'
												), $this->config->item('number_of_recent_sales') ? $this->config->item('number_of_recent_sales') : '10', 'class="form-control" id="number_of_recent_sales"');
												?>
				
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_hide_customer_recent_sales').':', 'hide_customer_recent_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_customer_recent_sales',
												'id'=>'hide_customer_recent_sales',
												'value'=>'hide_customer_recent_sales',
												'checked'=>$this->config->item('hide_customer_recent_sales')));?>
												<label for="hide_customer_recent_sales"><span></span></label>
											</div>
										</div>
										
															
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_enable_customer_quick_add').':', 'enable_customer_quick_add',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'enable_customer_quick_add',
												'id'=>'enable_customer_quick_add',
												'value'=>'enable_customer_quick_add',
												'checked'=>$this->config->item('enable_customer_quick_add')));?>
												<label for="enable_customer_quick_add"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_enable_supplier_quick_add').':', 'enable_supplier_quick_add',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'enable_supplier_quick_add',
												'id'=>'enable_supplier_quick_add',
												'value'=>'enable_supplier_quick_add',
												'checked'=>$this->config->item('enable_supplier_quick_add')));?>
												<label for="enable_supplier_quick_add"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_collapse_sales_ui_by_default').':', 'collapse_sales_ui_by_default',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'collapse_sales_ui_by_default',
												'id'=>'collapse_sales_ui_by_default',
												'value'=>'collapse_sales_ui_by_default',
												'checked'=>$this->config->item('collapse_sales_ui_by_default')));?>
												<label for="collapse_sales_ui_by_default"><span></span></label>
											</div>
										</div>
				
				
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_collapse_recv_ui_by_default').':', 'collapse_recv_ui_by_default',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'collapse_recv_ui_by_default',
												'id'=>'collapse_recv_ui_by_default',
												'value'=>'collapse_recv_ui_by_default',
												'checked'=>$this->config->item('collapse_recv_ui_by_default')));?>
												<label for="collapse_recv_ui_by_default"><span></span></label>
											</div>
										</div>
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('disable_confirmation_sale').':', 'disable_confirmation_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_confirmation_sale',
												'id'=>'disable_confirmation_sale',
												'value'=>'disable_confirmation_sale',
												'checked'=>$this->config->item('disable_confirmation_sale')));?>
												<label for="disable_confirmation_sale"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_disable_confirm_recv').':', 'disable_confirm_recv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_confirm_recv',
												'id'=>'disable_confirm_recv',
												'value'=>'disable_confirm_recv',
												'checked'=>$this->config->item('disable_confirm_recv')));?>
												<label for="disable_confirm_recv"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('disable_quick_complete_sale').':', 'disable_quick_complete_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_quick_complete_sale',
												'id'=>'disable_quick_complete_sale',
												'value'=>'disable_quick_complete_sale',
												'checked'=>$this->config->item('disable_quick_complete_sale')));?>
												<label for="disable_quick_complete_sale"><span></span></label>
											</div>
										</div>					
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_automatically_calculate_average_cost_price_from_receivings').':', 'calculate_average_cost_price_from_receivings',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'calculate_average_cost_price_from_receivings',
												'id'=>'calculate_average_cost_price_from_receivings',
												'value'=>'1',
												'checked'=>$this->config->item('calculate_average_cost_price_from_receivings')));?>
												<label for="calculate_average_cost_price_from_receivings"><span></span></label>
											</div>
										</div>
										
										<div id="average_cost_price_from_receivings_methods">
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_averaging_method').':', 'averaging_method',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_dropdown('averaging_method', array('moving_average' => lang('config_moving_average'), 'historical_average' => lang('config_historical_average'), 'dont_average' => lang('config_dont_average_use_current_recv_price')), $this->config->item('averaging_method'),'class="form-control" id="averaging_method"'); ?>
												</div>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_update_base_cost_price_from_units').':', 'update_base_cost_price_from_units',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'update_base_cost_price_from_units',
												'id'=>'update_base_cost_price_from_units',
												'value'=>'update_base_cost_price_from_units',
												'checked'=>$this->config->item('update_base_cost_price_from_units')));?>
												<label for="update_base_cost_price_from_units"><span></span></label>
											</div>
										</div>					
										
										
										<div id="update_cost_price_on_transfer_container">
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
												<?php echo form_label(lang('config_update_cost_price_on_transfer').':', 'update_cost_price_on_transfer',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'update_cost_price_on_transfer',
													'id'=>'update_cost_price_on_transfer',
													'value'=>'1',
													'checked'=>$this->config->item('update_cost_price_on_transfer')));?>
													<label for="update_cost_price_on_transfer"><span></span></label>
												</div>
											</div>
											
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_require_supplier_recv').':', 'require_supplier_for_recv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'require_supplier_for_recv',
												'id'=>'require_supplier_for_recv',
												'value'=>'1',
												'checked'=>$this->config->item('require_supplier_for_recv')));?>
												<label for="require_supplier_for_recv"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_track_shipping_cost_for_receivings').':', 'track_shipping_cost_recv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'track_shipping_cost_recv',
												'id'=>'track_shipping_cost_recv',
												'value'=>'1',
												'checked'=>$this->config->item('track_shipping_cost_recv')));?>
												<label for="track_shipping_cost_recv"><span></span></label>
											</div>
										</div>
										
														
										<?php if ($this->config->item('always_use_average_cost_method')) { ?>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_always_use_average_cost_method').':', 'always_use_average_cost_method',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'always_use_average_cost_method',
												'id'=>'always_use_average_cost_method',
												'value'=>'1',
												'checked'=>$this->config->item('always_use_average_cost_method')));?>
												<label for="always_use_average_cost_method"><span></span></label>
											</div>
										</div>
										<?php } ?>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_hide_suspended_recv_in_reports').':', 'hide_suspended_recv_in_reports',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_suspended_recv_in_reports',
												'id'=>'hide_suspended_recv_in_reports',
												'value'=>'1',
												'checked'=>$this->config->item('hide_suspended_recv_in_reports')));?>
												<label for="hide_suspended_recv_in_reports"><span></span></label>
											</div>
										</div>
										<?php
										$track_payment_types = $this->config->item('track_payment_types') ? unserialize($this->config->item('track_payment_types')) : array();
										?>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('common_track_cash').':', 'track_cash',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'track_payment_types[]',
												'id'=>'track_cash',
												'value'=>'common_cash',
												'checked'=>in_array('common_cash',$track_payment_types)));?>
												<label for="track_cash"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_amount_of_cash_to_be_left_in_drawer_at_closing').':', 'amount_of_cash_to_be_left_in_drawer_at_closing',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
												'name'=>'amount_of_cash_to_be_left_in_drawer_at_closing',
												'id'=>'amount_of_cash_to_be_left_in_drawer_at_closing',
												'value'=>$this->config->item('amount_of_cash_to_be_left_in_drawer_at_closing')));?>
											</div>
										</div>	
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_cash_alert_high').':', 'cash_alert_high',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
												'name'=>'cash_alert_high',
												'id'=>'cash_alert_high',
												'value'=>$this->config->item('cash_alert_high')));?>
											</div>
										</div>	
				
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_cash_alert_low').':', 'cash_alert_low',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
												'name'=>'cash_alert_low',
												'id'=>'cash_alert_low',
												'value'=>$this->config->item('cash_alert_low')));?>
											</div>
										</div>	
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('common_track_checks_in_register').':', 'track_check',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'track_payment_types[]',
												'id'=>'track_check',
												'value'=>'common_check',
												'checked'=>in_array('common_check',$track_payment_types)));?>
												<label for="track_check"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('common_track_giftcards_in_register').':', 'track_giftcard',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'track_payment_types[]',
												'id'=>'track_giftcard',
												'value'=>'common_giftcard',
												'checked'=>in_array('common_giftcard',$track_payment_types)));?>
												<label for="track_giftcard"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('common_track_debit_cards_in_register').':', 'track_debit_cards',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'track_payment_types[]',
												'id'=>'track_debit_cards',
												'value'=>'common_debit',
												'checked'=>in_array('common_debit',$track_payment_types)));?>
												<label for="track_debit_cards"><span></span></label>
											</div>
										</div>
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('common_track_credit_cards_in_register').':', 'track_credit_cards',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'track_payment_types[]',
												'id'=>'track_credit_cards',
												'value'=> 'common_credit',
												'checked'=>in_array('common_credit',$track_payment_types)));?>
												<label for="track_credit_cards"><span></span></label>
											</div>
										</div>
										
										
										<?php
										foreach($this->Appconfig->get_additional_payment_types() as $additional_payment_type)
										{
										?>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('common_track').' '.$additional_payment_type.' '.lang('common_in_register').':', 'track_'.$additional_payment_type,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'track_payment_types[]',
												'id'=>'track_'.$additional_payment_type,
												'value'=> $additional_payment_type,
												'checked'=>in_array($additional_payment_type,$track_payment_types)));?>
												<label for="track_<?php echo $additional_payment_type;?>"><span></span></label>
											</div>
										</div>
										
										<?php
										}
										?>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_do_not_show_closing').':', 'do_not_show_closing',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_show_closing',
												'id'=>'do_not_show_closing',
												'value'=>'1',
												'checked'=>$this->config->item('do_not_show_closing')));?>
												<label for="do_not_show_closing"><span></span></label>
											</div>
										</div>
										
						
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_hide_available_giftcards').':', 'hide_available_giftcards',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_available_giftcards',
												'id'=>'hide_available_giftcards',
												'value'=>'1',
												'checked'=>$this->config->item('hide_available_giftcards')));?>
												<label for="hide_available_giftcards"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_show_giftcards_even_if_0_balance').':', 'show_giftcards_even_if_0_balance',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_giftcards_even_if_0_balance',
												'id'=>'show_giftcards_even_if_0_balance',
												'value'=>'1',
												'checked'=>$this->config->item('show_giftcards_even_if_0_balance')));?>
												<label for="show_giftcards_even_if_0_balance"><span></span></label>
											</div>
										</div>
										
										
									
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_disable_giftcard_detection').':', 'disable_giftcard_detection',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_giftcard_detection',
												'id'=>'disable_giftcard_detection',
												'value'=>'1',
												'checked'=>$this->config->item('disable_giftcard_detection')));?>
												<label for="disable_giftcard_detection"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_always_show_item_grid').':', 'always_show_item_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'always_show_item_grid',
												'id'=>'always_show_item_grid',
												'value'=>'1',
												'checked'=>$this->config->item('always_show_item_grid')));?>
												<label for="always_show_item_grid"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_hide_images_in_grid').':', 'hide_images_in_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_images_in_grid',
												'id'=>'hide_images_in_grid',
												'value'=>'1',
												'checked'=>$this->config->item('hide_images_in_grid')));?>
												<label for="hide_images_in_grid"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_quick_variation_grid').':', 'quick_variation_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'quick_variation_grid',
												'id'=>'quick_variation_grid',
												'value'=>'1',
												'checked'=>$this->config->item('quick_variation_grid')));?>
												<label for="quick_variation_grid"><span></span></label>
											</div>
										</div>
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_hide_out_of_stock_grid').':', 'hide_out_of_stock_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_out_of_stock_grid',
												'id'=>'hide_out_of_stock_grid',
												'value'=>'1',
												'checked'=>$this->config->item('hide_out_of_stock_grid')));?>
												<label for="hide_out_of_stock_grid"><span></span></label>
											</div>
										</div>
															
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_default_type_for_grid').':', 'default_type_for_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_dropdown('default_type_for_grid', array(
													'categories'  => lang('reports_categories'), 
													'tags'  => lang('common_tags'),
													'suppliers'  => lang('common_suppliers'),
													'favorites'  => lang('common_favorite'),
												),
												$this->config->item('default_type_for_grid'), 'class="form-control" id="default_type_for_grid"');
												?>
											</div>
										</div>
										
<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">
<?php echo form_label(lang('config_require_customer_for_sale').':', 'require_customer_for_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
<div class="col-sm-9 col-md-9 col-lg-10">
<?php echo form_checkbox(array(
'name'=>'require_customer_for_sale',
'id'=>'require_customer_for_sale',
'value'=>'1',
'checked'=>$this->config->item('require_customer_for_sale')));?>
<label for="require_customer_for_sale"><span></span></label>
</div>
</div>

<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">
<?php echo form_label(lang('config_require_customer_for_sale_by_payment').':', 'require_customer_for_sale_by_payment',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
<div class="col-sm-9 col-md-9 col-lg-10">
<?php echo form_checkbox(array(
'name'=>'require_customer_for_sale_by_payment',
'id'=>'require_customer_for_sale_by_payment',
'value'=>'1',
'checked'=>$this->config->item('require_customer_for_sale_by_payment')));?>
<label for="require_customer_for_sale_by_payment"><span></span></label>
</div>
</div>

<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">
<?php echo form_label(lang('config_payment_type_to_require_customer').':', 'payment_type_to_require_customer',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
<div class="col-sm-9 col-md-9 col-lg-10">
<?php echo form_dropdown('payment_type_to_require_customer',$payment_options, $this->config->item('payment_type_to_require_customer'),'class="form-control" id="payment_type_to_require_customer"'); ?>
<p class="help-block"><?php echo lang('config_payment_type_to_require_customer_help'); ?></p>
</div>
</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_select_sales_person_during_sale').':', 'select_sales_person_during_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'select_sales_person_during_sale',
												'id'=>'select_sales_person_during_sale',
												'value'=>'1',
												'checked'=>$this->config->item('select_sales_person_during_sale')));?>
												<label for="select_sales_person_during_sale"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_default_sales_person').':', 'default_sales_person',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10"> 
											<?php echo form_dropdown('default_sales_person', array('logged_in_employee' => lang('common_logged_in_employee'), 'not_set' => lang('common_not_set')), $this->config->item('default_sales_person'),'class="form-control" id="default_sales_person"'); ?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('common_commission_default_rate'). ':', 'commission_default_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<div class="input-group">
													<?php echo form_input(array(
													'name'=>'commission_default_rate',
													'id'=>'commission_default_rate',
													'class'=>'form-control',
													'value'=>$this->config->item('commission_default_rate')));?>
												<span class="input-group-addon">%</span>
												</div>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('common_commission_percent_calculation').': ', 'commission_percent_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('commission_percent_type', array(
												'selling_price'  => lang('common_unit_price'),
												'profit'    => lang('common_profit'),
												),
												$this->config->item('commission_percent_type'),
												array('id' => 'commission_percent_type', 'class' => 'form-control'))
												?>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_disable_sale_notifications').':', 'disable_sale_notifications',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_sale_notifications',
												'id'=>'disable_sale_notifications',
												'value'=>'1',
												'checked'=>$this->config->item('disable_sale_notifications')));?>
												<label for="disable_sale_notifications"><span></span></label>
											</div>
										</div>
				
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_confirm_error_messages_modal').':', 'confirm_error_adding_item',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'confirm_error_adding_item',
												'id'=>'confirm_error_adding_item',
												'value'=>'1',
												'checked'=>$this->config->item('confirm_error_adding_item')));?>
												<label for="confirm_error_adding_item"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_change_sale_date_for_new_sale').':', 'change_sale_date_for_new_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'change_sale_date_for_new_sale',
												'id'=>'change_sale_date_for_new_sale',
												'value'=>'1',
												'checked'=>$this->config->item('change_sale_date_for_new_sale')));?>
												<label for="change_sale_date_for_new_sale"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_do_not_group_same_items').':', 'do_not_group_same_items',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_group_same_items',
												'id'=>'do_not_group_same_items',
												'value'=>'1',
												'checked'=>$this->config->item('do_not_group_same_items')));?>
												<label for="do_not_group_same_items"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_do_not_allow_below_cost').':', 'do_not_allow_below_cost',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_allow_below_cost',
												'id'=>'do_not_allow_below_cost',
												'value'=>'1',
												'checked'=>$this->config->item('do_not_allow_below_cost')));?>
												<label for="do_not_allow_below_cost"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_do_not_allow_out_of_stock_items_to_be_sold').':', 'do_not_allow_out_of_stock_items_to_be_sold',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_allow_out_of_stock_items_to_be_sold',
												'id'=>'do_not_allow_out_of_stock_items_to_be_sold',
												'value'=>'do_not_allow_out_of_stock_items_to_be_sold',
												'checked'=>$this->config->item('do_not_allow_out_of_stock_items_to_be_sold')));?>
												<label for="do_not_allow_out_of_stock_items_to_be_sold"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_do_not_allow_items_to_go_out_of_stock_when_transfering').':', 'do_not_allow_items_to_go_out_of_stock_when_transfering',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_allow_items_to_go_out_of_stock_when_transfering',
												'id'=>'do_not_allow_items_to_go_out_of_stock_when_transfering',
												'value'=>'do_not_allow_items_to_go_out_of_stock_when_transfering',
												'checked'=>$this->config->item('do_not_allow_items_to_go_out_of_stock_when_transfering')));?>
												<label for="do_not_allow_items_to_go_out_of_stock_when_transfering"><span></span></label>
											</div>
										</div>
										
				
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_do_not_allow_item_with_variations_to_be_sold_without_selecting_variation').':', 'do_not_allow_item_with_variations_to_be_sold_without_selecting_variation',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_allow_item_with_variations_to_be_sold_without_selecting_variation',
												'id'=>'do_not_allow_item_with_variations_to_be_sold_without_selecting_variation',
												'value'=>'do_not_allow_item_with_variations_to_be_sold_without_selecting_variation',
												'checked'=>$this->config->item('do_not_allow_item_with_variations_to_be_sold_without_selecting_variation')));?>
												<label for="do_not_allow_item_with_variations_to_be_sold_without_selecting_variation"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_edit_item_price_if_zero_after_adding').':', 'edit_item_price_if_zero_after_adding',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'edit_item_price_if_zero_after_adding',
												'id'=>'edit_item_price_if_zero_after_adding',
												'value'=>'edit_item_price_if_zero_after_adding',
												'checked'=>$this->config->item('edit_item_price_if_zero_after_adding')));?>
												<label for="edit_item_price_if_zero_after_adding"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_remind_customer_facing_display').':', 'remind_customer_facing_display',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'remind_customer_facing_display',
												'id'=>'remind_customer_facing_display',
												'value'=>'remind_customer_facing_display',
												'checked'=>$this->config->item('remind_customer_facing_display')));?>
												<label for="remind_customer_facing_display"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" >	
										<?php echo form_label(lang('config_do_not_allow_sales_with_zero_value').':', 'do_not_allow_sales_with_zero_value',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_allow_sales_with_zero_value',
												'id'=>'do_not_allow_sales_with_zero_value',
												'value'=>'do_not_allow_sales_with_zero_value',
												'checked'=>$this->config->item('do_not_allow_sales_with_zero_value')));?>
												<label for="do_not_allow_sales_with_zero_value"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" >	
										<?php echo form_label(lang('config_do_not_allow_sales_with_zero_value_line_items').':', 'do_not_allow_sales_with_zero_value_line_items',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_allow_sales_with_zero_value_line_items',
												'id'=>'do_not_allow_sales_with_zero_value_line_items',
												'value'=>'do_not_allow_sales_with_zero_value_line_items',
												'checked'=>$this->config->item('do_not_allow_sales_with_zero_value_line_items')));?>
												<label for="do_not_allow_sales_with_zero_value_line_items"><span></span></label>
											</div>
										</div>
										
										
										
										<div class="form-group" >	
										<?php echo form_label(lang('config_prompt_amount_for_cash_sale').':', 'prompt_amount_for_cash_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'prompt_amount_for_cash_sale',
												'id'=>'prompt_amount_for_cash_sale',
												'value'=>'prompt_amount_for_cash_sale',
												'checked'=>$this->config->item('prompt_amount_for_cash_sale')));?>
												<label for="prompt_amount_for_cash_sale"><span></span></label>
											</div>
										</div>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>" >
										<?php echo form_label(lang('config_show_qr_code_for_sale').':', 'show_qr_code_for_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_qr_code_for_sale',
												'id'=>'show_qr_code_for_sale',
												'value'=>'show_qr_code_for_sale',
												'checked'=>$this->config->item('show_qr_code_for_sale')));?>
												<label for="show_qr_code_for_sale"><span></span></label>
											</div>
										</div>
				
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_qr_code_format').': ', 'qr_code_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('qr_code_format', array(
													'link_to_receipt' => lang('config_link_to_receipt'),
													'sale_summary_info' => lang('config_sale_summary_info'),
													'saudi_arabia_digital_receipt' => lang('config_saudi_arabia_digital_receipt'),
												),
												$this->config->item('qr_code_format'),
												array('id' => 'qr_code_format', 'class' => 'form-control'))
												?>
											</div>
										</div>
				
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>" >
										<?php echo form_label(lang('config_disable_verification_for_qr_codes').':', 'disable_verification_for_qr_codes',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_verification_for_qr_codes',
												'id'=>'disable_verification_for_qr_codes',
												'value'=>'disable_verification_for_qr_codes',
												'checked'=>$this->config->item('disable_verification_for_qr_codes')));?>
												<label for="disable_verification_for_qr_codes"><span></span></label>
											</div>
										</div>
				
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>" >
										<?php echo form_label(lang('config_hide_categories_sales_grid').':', 'hide_categories_sales_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_categories_sales_grid',
												'id'=>'hide_categories_sales_grid',
												'value'=>'hide_categories_sales_grid',
												'checked'=>$this->config->item('hide_categories_sales_grid')));?>
												<label for="hide_categories_sales_grid"><span></span></label>
											</div>
										</div>
				
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>" >
										<?php echo form_label(lang('config_hide_tags_sales_grid').':', 'hide_tags_sales_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_tags_sales_grid',
												'id'=>'hide_tags_sales_grid',
												'value'=>'hide_tags_sales_grid',
												'checked'=>$this->config->item('hide_tags_sales_grid')));?>
												<label for="hide_tags_sales_grid"><span></span></label>
											</div>
										</div>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>" >
										<?php echo form_label(lang('config_hide_suppliers_sales_grid').':', 'hide_suppliers_sales_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_suppliers_sales_grid',
												'id'=>'hide_suppliers_sales_grid',
												'value'=>'hide_suppliers_sales_grid',
												'checked'=>$this->config->item('hide_suppliers_sales_grid')));?>
												<label for="hide_suppliers_sales_grid"><span></span></label>
											</div>
										</div>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>" >
										<?php echo form_label(lang('config_hide_favorites_sales_grid').':', 'hide_favorites_sales_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_favorites_sales_grid',
												'id'=>'hide_favorites_sales_grid',
												'value'=>'hide_favorites_sales_grid',
												'checked'=>$this->config->item('hide_favorites_sales_grid')));?>
												<label for="hide_favorites_sales_grid"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_number_of_decimals_displayed_on_sales_interface').':', 'number_of_decimals_displayed_on_sales_interface',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_dropdown('number_of_decimals_displayed_on_sales_interface', array(
														''  => lang('config_let_system_decide'),
														'0'    => '0',
														'1'    => '1',
														'2'    => '2',
														'3'    => '3',
														'4'    => '4',
														'5'    => '5',
														'6'    => '6',
														'7'    => '7',
														'8'    => '8',
														'9'    => '9',
														'10'    => '10',
													),$this->config->item('number_of_decimals_displayed_on_sales_interface')
														, 'class="form-control" id="number_of_decimals_displayed_on_sales_interface"');
													?>
											</div>
										</div>
				
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>" >
										<?php echo form_label(lang('config_do_not_allow_edit_of_overall_subtotal').':', 'do_not_allow_edit_of_overall_subtotal',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_allow_edit_of_overall_subtotal',
												'id'=>'do_not_allow_edit_of_overall_subtotal',
												'value'=>'1',
												'checked'=>$this->config->item('do_not_allow_edit_of_overall_subtotal')));?>
												<label for="do_not_allow_edit_of_overall_subtotal"><span></span></label>
											</div>
										</div>
				
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_disable_supplier_selection_on_sales_interface').':', 'disable_supplier_selection_on_sales_interface',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'		=>	'disable_supplier_selection_on_sales_interface',
												'id'		=>	'disable_supplier_selection_on_sales_interface',
												'value'		=>	'1',
												'checked'	=>	$this->config->item('disable_supplier_selection_on_sales_interface')));?>
												<label for="disable_supplier_selection_on_sales_interface"><span></span></label>
											</div>
										</div>
												
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_create_work_order_for_customer').':', 'create_work_order_for_customer',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'		=>	'create_work_order_for_customer',
												'id'		=>	'create_work_order_for_customer',
												'value'		=>	'1',
												'checked'	=>	$this->config->item('create_work_order_for_customer')));?>
												<label for="create_work_order_for_customer"><span></span></label>
											</div>
										</div>
				
										<div class="form-group" data-keyword="<?php echo H(lang('config_create_work_order_is_checked_by_default_for_sale')) ?>">	
												<?php echo form_label(lang('config_create_work_order_is_checked_by_default_for_sale').':', 'create_work_order_is_checked_by_default_for_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_checkbox(array(
														'name' 		=> 'create_work_order_is_checked_by_default_for_sale',
														'id' 		=> 'create_work_order_is_checked_by_default_for_sale',
														'value' 	=> 'create_work_order_is_checked_by_default_for_sale',
														'checked' 	=> $this->config->item('create_work_order_is_checked_by_default_for_sale'))); ?>
													<label for="create_work_order_is_checked_by_default_for_sale"><span></span></label>
												</div>
											</div>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Sales -->

				<!-- Suspended Sales/Layaways -->
				<div class="tab-pane fade" id="suspended_sales">
					<div class="row">
						<div class="col-md-12 suspended_sales_layaways_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#suspended_sales_collapse">
									<?php echo create_section(lang("config_suspended_sales_layaways_info")) ?>
									</a>
								</div>
								<div id="suspended_sales_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<div class="form-group no-padding-right" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
										<?php echo form_label(lang('config_additional_suspend_types').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-md-9 col-sm-9 col-lg-10">
												<div class="table-responsive">
													<table id="sale_types" class="table">
														<thead>
															<tr>
															<th><?php echo lang('config_sort'); ?></th>
															<th><?php echo lang('common_id'); ?></th>
															<th><?php echo lang('common_suspended_sale_type'); ?></th>
															<th><?php echo lang('config_remove_quantity_suspending'); ?></th>
															<th><?php echo lang('common_delete'); ?></th>
															</tr>
														</thead>
														
														<tbody>
														<?php foreach($sale_types->result() as $sale_type) { ?>
															<tr>
																<td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
																<td><?php echo $sale_type->id; ?></td>
																<td><input type="text" data-index="<?php echo $sale_type->id; ?>" class="sale_types_to_edit form-control" name="sale_types_to_edit[<?php echo $sale_type->id; ?>][name]" value="<?php echo H($sale_type->name); ?>" /></td>
																
																<td class="text-center">
																<?php echo form_checkbox(array(
																	'name'=>'sale_types_to_edit['.$sale_type->id.'][remove_quantity]',
																	'id'=>'remove_quantity_'.$sale_type->id,
																	'value'=>'1',
																	'data-index' => $sale_type->id,
																	'checked'=>$sale_type->remove_quantity));?>
																	<label for="remove_quantity_<?php echo $sale_type->id;?>"><span></span></label>
																</td>
																
																<td><a class="delete_sale_type" href="javascript:void(0);" data-sale-type-id='<?php echo $sale_type->id; ?>'><?php echo lang('common_delete'); ?></a></td>
														</tr>
														<?php } ?>
														</tbody>
													</table>
													
													<a href="javascript:void(0);" id="add_sale_type"><?php echo lang('config_add_suspended_sale_type'); ?></a>
													</div>
												</div>
											</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
											<?php echo form_label(lang('config_require_customer_for_suspended_sale').':', 'require_customer_for_suspended_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'require_customer_for_suspended_sale',
												'id'=>'require_customer_for_suspended_sale',
												'value'=>'1',
												'checked'=>$this->config->item('require_customer_for_suspended_sale')));?>
												<label for="require_customer_for_suspended_sale"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
											<?php echo form_label(lang('config_dont_lock_suspended_sales').':', 'dont_lock_suspended_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'dont_lock_suspended_sales',
												'id'=>'dont_lock_suspended_sales',
												'value'=>'1',
												'checked'=>$this->config->item('dont_lock_suspended_sales')));?>
												<label for="dont_lock_suspended_sales"><span></span></label>
											</div>
										</div>
										
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
											<?php echo form_label(lang('config_dont_recalculate_cost_price_when_unsuspending_estimates').':', 'dont_recalculate_cost_price_when_unsuspending_estimates',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'dont_recalculate_cost_price_when_unsuspending_estimates',
												'id'=>'dont_recalculate_cost_price_when_unsuspending_estimates',
												'value'=>'1',
												'checked'=>$this->config->item('dont_recalculate_cost_price_when_unsuspending_estimates')));?>
												<label for="dont_recalculate_cost_price_when_unsuspending_estimates"><span></span></label>
											</div>
										</div>
										
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">
											<?php echo form_label(lang('config_user_configured_layaway_name').':', 'user_configured_layaway_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
												'name'=>'user_configured_layaway_name',
												'id'=>'user_configured_layaway_name',
												'value'=>$this->config->item('user_configured_layaway_name')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">
											<?php echo form_label(lang('config_override_estimate_name').':', 'user_configured_estimate_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
												'name'=>'user_configured_estimate_name',
												'id'=>'user_configured_estimate_name',
												'value'=>$this->config->item('user_configured_estimate_name')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
											<?php echo form_label(lang('common_hide_layaways_sales_in_reports').':', 'hide_layaways_sales_in_reports',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_layaways_sales_in_reports',
												'id'=>'hide_layaways_sales_in_reports',
												'value'=>'1',
												'checked'=>$this->config->item('hide_layaways_sales_in_reports')));?>
												<label for="hide_layaways_sales_in_reports"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
											<?php echo form_label(lang('config_change_sale_date_when_suspending').':', 'change_sale_date_when_suspending',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'change_sale_date_when_suspending',
												'id'=>'change_sale_date_when_suspending',
												'value'=>'1',
												'checked'=>$this->config->item('change_sale_date_when_suspending')));?>
												<label for="change_sale_date_when_suspending"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
											<?php echo form_label(lang('config_change_sale_date_when_completing_suspended_sale').':', 'change_sale_date_when_completing_suspended_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'change_sale_date_when_completing_suspended_sale',
												'id'=>'change_sale_date_when_completing_suspended_sale',
												'value'=>'1',
												'checked'=>$this->config->item('change_sale_date_when_completing_suspended_sale')));?>
												<label for="change_sale_date_when_completing_suspended_sale"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
											<?php echo form_label(lang('config_show_receipt_after_suspending_sale').':', 'show_receipt_after_suspending_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_receipt_after_suspending_sale',
												'id'=>'show_receipt_after_suspending_sale',
												'value'=>'1',
												'checked'=>$this->config->item('show_receipt_after_suspending_sale')));?>
												<label for="show_receipt_after_suspending_sale"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
										<?php echo form_label(lang('config_layaway_statement_message').':', 'layaway_statement_message',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_textarea(array(
												'name'=>'layaway_statement_message',
												'id'=>'layaway_statement_message',
												'class'=>'form-control text-area',
												'rows'=>'4',
												'cols'=>'30',
												'value'=>$this->config->item('layaway_statement_message')));?>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Suspended Sales/Layaways -->

				<!-- Receipt -->
				<div class="tab-pane fade" id="receipt">
					<div class="row">
						<div class="col-md-12 receipt_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#receipt_collapse">
									<?php echo create_section(lang("config_receipt_info")) ?>
									</a>
								</div>
								<div id="receipt_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
							
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_override_receipt_title').':', 'override_receipt_title',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
												'name'=>'override_receipt_title',
												'id'=>'override_receipt_title',
												'value'=>$this->config->item('override_receipt_title')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_emailed_receipt_subject').':', 'emailed_receipt_subject',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
												'name'=>'emailed_receipt_subject',
												'id'=>'emailed_receipt_subject',
												'placeholder' => lang('sales_receipt'),
												'value'=>$this->config->item('emailed_receipt_subject')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_override_employee_label_on_receipt').':', 'override_employee_label_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
												'name'=>'override_employee_label_on_receipt',
												'id'=>'override_employee_label_on_receipt',
												'value'=>$this->config->item('override_employee_label_on_receipt')));?>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_show_item_id_on_receipt').':', 'show_item_id_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_item_id_on_receipt',
												'id'=>'show_item_id_on_receipt',
												'value'=>'show_item_id_on_receipt',
												'checked'=>$this->config->item('show_item_id_on_receipt')));?>
												<label for="show_item_id_on_receipt"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_show_images_on_receipt').':', 'show_images_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_images_on_receipt',
												'id'=>'show_images_on_receipt',
												'value'=>'show_images_on_receipt',
												'checked'=>$this->config->item('show_images_on_receipt')));?>
												<label for="show_images_on_receipt"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_show_images_on_receipt_width_percent').':', 'show_images_on_receipt_width_percent',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php 
												echo form_dropdown('show_images_on_receipt_width_percent', array(
														'1'    => '1%',
														'2'    => '2%',
														'3'    => '3%',
														'4'    => '4%',
														'5'    => '5%',
														'10'    => '10%',
														'15'    => '15%',
														'20'    => '20%',
														'25'    => '25%',
														'30'    => '30%',
														'35'    => '35%',
														'40'    => '40%',
														'45'    => '45%',
														'50'    => '50%',
														'55'    => '55%',
														'60'    => '60%',
														'65'    => '65%',
														'70'    => '70%',
														'75'    => '75%',
														'80'    => '80%',
														'85'    => '85%',
														'90'    => '90%',
														'95'    => '95%',
														'100'    => '100%',
													),
													$this->config->item('show_images_on_receipt_width_percent')?$this->config->item('show_images_on_receipt_width_percent'):25, 'class="form-control" id="show_images_on_receipt_width_percent"');
												?>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_show_person_id_on_receipt').':', 'show_person_id_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_person_id_on_receipt',
												'id'=>'show_person_id_on_receipt',
												'value'=>'1',
												'checked'=>$this->config->item('show_person_id_on_receipt')));?>
												<label for="show_person_id_on_receipt"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_show_total_at_top_on_receipt').':', 'show_total_at_top_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_total_at_top_on_receipt',
												'id'=>'show_total_at_top_on_receipt',
												'value'=>'1',
												'checked'=>$this->config->item('show_total_at_top_on_receipt')));?>
												<label for="show_total_at_top_on_receipt"><span></span></label>
											</div>
										</div>
										
										
										

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_show_tags_on_fulfillment_sheet').':', 'show_tags_on_fulfillment_sheet',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_tags_on_fulfillment_sheet',
												'id'=>'show_tags_on_fulfillment_sheet',
												'value'=>'show_tags_on_fulfillment_sheet',
												'checked'=>$this->config->item('show_tags_on_fulfillment_sheet')));?>
												<label for="show_tags_on_fulfillment_sheet"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_show_total_on_fulfillment').':', 'show_total_on_fulfillment',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_total_on_fulfillment',
												'id'=>'show_total_on_fulfillment',
												'value'=>'show_total_on_fulfillment',
												'checked'=>$this->config->item('show_total_on_fulfillment')));?>
												<label for="show_total_on_fulfillment"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_taxes_summary_on_receipt').':', 'taxes_summary_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'taxes_summary_on_receipt',
												'id'=>'taxes_summary_on_receipt',
												'value'=>'taxes_summary_on_receipt',
												'checked'=>$this->config->item('taxes_summary_on_receipt')));?>
												<label for="taxes_summary_on_receipt"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">
											<div <?php echo $this->config->item('taxes_summary_on_receipt')?"":"style='display:none;'" ?>
										id="override_symbol_taxable_summary_container">
												<?php echo form_label(lang('config_override_symbol_taxable_summary').':', 'override_symbol_taxable_summary',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'override_symbol_taxable_summary',
													'id'=>'override_symbol_taxable_summary',
													'value'=>$this->config->item('override_symbol_taxable_summary')));?>
												</div>
											</div>
										</div>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">
											<div <?php echo $this->config->item('taxes_summary_on_receipt')?"":"style='display:none;'" ?>
										id="override_symbol_non_taxable_summary_container">
												<?php echo form_label(lang('config_override_symbol_non_taxable_summary').':', 'override_symbol_non_taxable_summary',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'override_symbol_non_taxable_summary',
													'id'=>'override_symbol_non_taxable_summary',
													'value'=>$this->config->item('override_symbol_non_taxable_summary')));?>
												</div>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_taxes_summary_details_on_receipt').':', 'taxes_summary_details_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'taxes_summary_details_on_receipt',
												'id'=>'taxes_summary_details_on_receipt',
												'value'=>'taxes_summary_details_on_receipt',
												'checked'=>$this->config->item('taxes_summary_details_on_receipt')));?>
												<label for="taxes_summary_details_on_receipt"><span></span></label>
											</div>
										</div>
										
										

										
										<div class="form-group">	
										<?php echo form_label(lang('config_second_language').':', 'second_language',array('class'=>'col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('second_language', array(
												''  => lang('common_none'),
												'english'  => 'English',
												'indonesia'    => 'Indonesia',
												'spanish'   => 'Español', 
												'french'    => 'Fançais',
												'italian'    => 'Italiano',
												'german'    => 'Deutsch',
												'dutch'    => 'Nederlands',
												'portugues'    => 'Portugues',
												'arabic' => 'العَرَبِيةُ‎‎',
												'khmer' => 'Khmer',
												'vietnamese' => 'Vietnamese',
												'chinese' => '中文',
												'chinese_traditional' => '繁體中文',
												'tamil' => 'Tamil',
												),
												$this->config->item('second_language'), 'class="form-control" id="second_language"');
												?>
											</div>
										</div>
										
															
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_uppercase_receipts').':', 'uppercase_receipts',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'uppercase_receipts',
												'id'=>'uppercase_receipts',
												'value'=>'uppercase_receipts',
												'checked'=>$this->config->item('uppercase_receipts')));?>
												<label for="uppercase_receipts"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_show_item_kit_items_on_receipt').':', 'show_item_kit_items_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_item_kit_items_on_receipt',
												'id'=>'show_item_kit_items_on_receipt',
												'value'=>'show_item_kit_items_on_receipt',
												'checked'=>$this->config->item('show_item_kit_items_on_receipt')));?>
												<label for="show_item_kit_items_on_receipt"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_show_total_discount_on_receipt').':', 'show_total_discount_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_total_discount_on_receipt',
												'id'=>'show_total_discount_on_receipt',
												'value'=>'show_total_discount_on_receipt',
												'checked'=>$this->config->item('show_total_discount_on_receipt')));?>
												<label for="show_total_discount_on_receipt"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_hide_prices_on_fill_sheet').':', 'hide_prices_on_fill_sheet',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_prices_on_fill_sheet',
												'id'=>'hide_prices_on_fill_sheet',
												'value'=>'hide_prices_on_fill_sheet',
												'checked'=>$this->config->item('hide_prices_on_fill_sheet')));?>
												<label for="hide_prices_on_fill_sheet"><span></span></label>
											</div>
										</div>
										

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_show_item_id_on_recv_receipt').':', 'show_item_id_on_recv_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_item_id_on_recv_receipt',
												'id'=>'show_item_id_on_recv_receipt',
												'value'=>'show_item_id_on_recv_receipt',
												'checked'=>$this->config->item('show_item_id_on_recv_receipt')));?>
												<label for="show_item_id_on_recv_receipt"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_hide_all_prices_on_recv').':', 'hide_all_prices_on_recv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_all_prices_on_recv',
												'id'=>'hide_all_prices_on_recv',
												'value'=>'hide_all_prices_on_recv',
												'checked'=>$this->config->item('hide_all_prices_on_recv')));?>
												<label for="hide_all_prices_on_recv"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_number_of_decimals_for_quantity_on_receipt').':', 'number_of_decimals_for_quantity_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_dropdown('number_of_decimals_for_quantity_on_receipt', array(
														''  => lang('config_let_system_decide'),
														'0'    => '0',
														'1'    => '1',
														'2'    => '2',
														'3'    => '3',
														'4'    => '4',
														'5'    => '5',
														'6'    => '6',
														'7'    => '7',
														'8'    => '8',
														'9'    => '9',
														'10'    => '10',
													),$this->config->item('number_of_decimals_for_quantity_on_receipt')
														, 'class="form-control" id="number_of_decimals_for_quantity_on_receipt"');
													?>
											</div>
										</div>
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('common_indicate_taxable_on_receipt').':', 'indicate_taxable_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'indicate_taxable_on_receipt',
												'id'=>'indicate_taxable_on_receipt',
												'value'=>'indicate_taxable_on_receipt',
												'checked'=>$this->config->item('indicate_taxable_on_receipt')));?>
												<label for="indicate_taxable_on_receipt"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('common_indicate_non_taxable_on_receipt').':', 'indicate_non_taxable_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'indicate_non_taxable_on_receipt',
												'id'=>'indicate_non_taxable_on_receipt',
												'value'=>'indicate_non_taxable_on_receipt',
												'checked'=>$this->config->item('indicate_non_taxable_on_receipt')));?>
												<label for="indicate_non_taxable_on_receipt"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">
											<div <?php echo $this->config->item('indicate_non_taxable_on_receipt')?"":"style='display:none;'" ?>
										id="override_symbol_non_taxable_container">
												<?php echo form_label(lang('config_override_symbol_non_taxable').':', 'override_symbol_non_taxable',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'override_symbol_non_taxable',
													'id'=>'override_symbol_non_taxable',
													'value'=>$this->config->item('override_symbol_non_taxable')));?>
												</div>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_show_tax_per_item_on_receipt').':', 'show_tax_per_item_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_tax_per_item_on_receipt',
												'id'=>'show_tax_per_item_on_receipt',
												'value'=>'show_tax_per_item_on_receipt',
												'checked'=>$this->config->item('show_tax_per_item_on_receipt')));?>
												<label for="show_tax_per_item_on_receipt"><span></span></label>
											</div>
										</div>
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_hide_desc_on_receipt').':', 'hide_desc_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_desc_on_receipt',
												'id'=>'hide_desc_on_receipt',
												'value'=>'hide_desc_on_receipt',
												'checked'=>$this->config->item('hide_desc_on_receipt')));?>
												<label for="hide_desc_on_receipt"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_hide_merchant_id_from_receipt').':', 'hide_merchant_id_from_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_merchant_id_from_receipt',
												'id'=>'hide_merchant_id_from_receipt',
												'value'=>'hide_merchant_id_from_receipt',
												'checked'=>$this->config->item('hide_merchant_id_from_receipt')));?>
												<label for="hide_merchant_id_from_receipt"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_hide_desc_emailed_receipts').':', 'hide_desc_emailed_receipts',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_desc_emailed_receipts',
												'id'=>'hide_desc_emailed_receipts',
												'value'=>'hide_desc_emailed_receipts',
												'checked'=>$this->config->item('hide_desc_emailed_receipts')));?>
												<label for="hide_desc_emailed_receipts"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_hide_description_on_sales_and_recv').':', 'hide_description_on_sales_and_recv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_description_on_sales_and_recv',
												'id'=>'hide_description_on_sales_and_recv',
												'value'=>'hide_description_on_sales_and_recv',
												'checked'=>$this->config->item('hide_description_on_sales_and_recv')));?>
												<label for="hide_description_on_sales_and_recv"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_hide_description_on_suspended_sales').':', 'hide_description_on_suspended_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_description_on_suspended_sales',
												'id'=>'hide_description_on_suspended_sales',
												'value'=>'hide_description_on_suspended_sales',
												'checked'=>$this->config->item('hide_description_on_suspended_sales')));?>
												<label for="hide_description_on_suspended_sales"><span></span></label>
											</div>
										</div>
										
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_show_orig_price_if_marked_down_on_receipt').':', 'show_orig_price_if_marked_down_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_orig_price_if_marked_down_on_receipt',
												'id'=>'show_orig_price_if_marked_down_on_receipt',
												'value'=>'show_orig_price_if_marked_down_on_receipt',
												'checked'=>$this->config->item('show_orig_price_if_marked_down_on_receipt')));?>
												<label for="show_orig_price_if_marked_down_on_receipt"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_print_after_sale').':', 'print_after_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'print_after_sale',
												'id'=>'print_after_sale',
												'value'=>'print_after_sale',
												'checked'=>$this->config->item('print_after_sale')));?>
												<label for="print_after_sale"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_wide_printer_receipt_format').':', 'wide_printer_receipt_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'wide_printer_receipt_format',
												'id'=>'wide_printer_receipt_format',
												'value'=>'wide_printer_receipt_format',
												'checked'=>$this->config->item('wide_printer_receipt_format')));?>
												<label for="wide_printer_receipt_format"><span></span></label>
											</div>
										</div>
										

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_print_after_receiving').':', 'print_after_receiving',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'print_after_receiving',
												'id'=>'print_after_receiving',
												'value'=>'print_after_receiving',
												'checked'=>$this->config->item('print_after_receiving')));?>
												<label for="print_after_receiving"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_hide_signature').':', 'hide_signature',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_signature',
												'id'=>'hide_signature',
												'value'=>'hide_signature',
												'checked'=>$this->config->item('hide_signature')));?>
												<label for="hide_signature"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_disable_signature_capture_on_terminal_for_phppos_credit_card_processing').':', 'disable_signature_capture_on_terminal_for_phppos_credit_card_processing',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_signature_capture_on_terminal_for_phppos_credit_card_processing',
												'id'=>'disable_signature_capture_on_terminal_for_phppos_credit_card_processing',
												'value'=>'disable_signature_capture_on_terminal_for_phppos_credit_card_processing',
												'checked'=>$this->config->item('disable_signature_capture_on_terminal_for_phppos_credit_card_processing')));?>
												<label for="disable_signature_capture_on_terminal_for_phppos_credit_card_processing"><span></span></label>
											</div>
										</div>
										
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_auto_capture_signature').':', 'auto_capture_signature',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'auto_capture_signature',
												'id'=>'auto_capture_signature',
												'value'=>'auto_capture_signature',
												'checked'=>$this->config->item('auto_capture_signature')));?>
												<label for="auto_capture_signature"><span></span></label>
											</div>
										</div>
										
																	
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_remove_customer_name_from_receipt').':', 'remove_customer_name_from_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'remove_customer_name_from_receipt',
												'id'=>'remove_customer_name_from_receipt',
												'value'=>'remove_customer_name_from_receipt',
												'checked'=>$this->config->item('remove_customer_name_from_receipt')));?>
											<label for="remove_customer_name_from_receipt"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>"> 
										<?php echo form_label(lang('config_remove_employee_lastname_from_receipt').':', 'remove_employee_lastname_from_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
										<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_checkbox(array(
												'name'=>'remove_employee_lastname_from_receipt',
												'id'=>'remove_employee_lastname_from_receipt',
												'value'=>'remove_employee_lastname_from_receipt',
												'checked'=>$this->config->item('remove_employee_lastname_from_receipt')));?>
										<label for="remove_employee_lastname_from_receipt"><span></span></label>
										</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>"> 
										<?php echo form_label(lang('config_remove_employee_from_receipt').':', 'remove_employee_from_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
										<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_checkbox(array(
												'name'=>'remove_employee_from_receipt',
												'id'=>'remove_employee_from_receipt',
												'value'=>'remove_employee_from_receipt',
												'checked'=>$this->config->item('remove_employee_from_receipt')));?>
										<label for="remove_employee_from_receipt"><span></span></label>
										</div>
										</div>		
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_remove_customer_company_from_receipt').':', 'remove_customer_company_from_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'remove_customer_company_from_receipt',
												'id'=>'remove_customer_company_from_receipt',
												'value'=>'remove_customer_company_from_receipt',
												'checked'=>$this->config->item('remove_customer_company_from_receipt')));?>
											<label for="remove_customer_company_from_receipt"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_remove_customer_contact_info_from_receipt').':', 'remove_customer_contact_info_from_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'remove_customer_contact_info_from_receipt',
												'id'=>'remove_customer_contact_info_from_receipt',
												'value'=>'remove_customer_contact_info_from_receipt',
												'checked'=>$this->config->item('remove_customer_contact_info_from_receipt')));?>
											<label for="remove_customer_contact_info_from_receipt"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_hide_email_on_receipts').':', 'hide_email_on_receipts',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_email_on_receipts',
												'id'=>'hide_email_on_receipts',
												'value'=>'hide_email_on_receipts',
												'checked'=>$this->config->item('hide_email_on_receipts')));?>
											<label for="hide_email_on_receipts"><span></span></label>
											</div>
										</div>
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_sort_receipt_column').':', 'sort_receipt_column',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_dropdown('sort_receipt_column', array(
													''   => lang('common_none'),
													'name'   => lang('common_item_name'),
													'item_number'  => lang('common_item_number_expanded'),
													'product_id'    => lang('common_product_id'),
													),
													$this->config->item('sort_receipt_column'), 'class="form-control" id="sort_receipt_column"')
													?>
												
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_automatically_email_receipt').':', 'automatically_email_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'automatically_email_receipt',
												'id'=>'automatically_email_receipt',
												'value'=>'automatically_email_receipt',
												'checked'=>$this->config->item('automatically_email_receipt')));?>
												<label for="automatically_email_receipt"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_automatically_sms_receipt').':', 'automatically_sms_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'automatically_sms_receipt',
												'id'=>'automatically_sms_receipt',
												'value'=>'automatically_sms_receipt',
												'checked'=>$this->config->item('automatically_sms_receipt')));?>
												<label for="automatically_sms_receipt"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_automatically_print_duplicate_receipt_for_cc_transactions').':', 'automatically_print_duplicate_receipt_for_cc_transactions',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'automatically_print_duplicate_receipt_for_cc_transactions',
												'id'=>'automatically_print_duplicate_receipt_for_cc_transactions',
												'value'=>'automatically_print_duplicate_receipt_for_cc_transactions',
												'checked'=>$this->config->item('automatically_print_duplicate_receipt_for_cc_transactions')));?>
												<label for="automatically_print_duplicate_receipt_for_cc_transactions"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_always_print_duplicate_receipt_all').':', 'always_print_duplicate_receipt_all',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'always_print_duplicate_receipt_all',
												'id'=>'always_print_duplicate_receipt_all',
												'value'=>'always_print_duplicate_receipt_all',
												'checked'=>$this->config->item('always_print_duplicate_receipt_all')));?>
												<label for="always_print_duplicate_receipt_all"><span></span></label>
											</div>
										</div>
										
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_automatically_show_comments_on_receipt').':', 'automatically_show_comments_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'automatically_show_comments_on_receipt',
												'id'=>'automatically_show_comments_on_receipt',
												'value'=>'automatically_show_comments_on_receipt',
												'checked'=>$this->config->item('automatically_show_comments_on_receipt')));?>
												<label for="automatically_show_comments_on_receipt"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_hide_barcode_on_sales_and_recv_receipt').':', 'hide_barcode_on_sales_and_recv_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_barcode_on_sales_and_recv_receipt',
												'id'=>'hide_barcode_on_sales_and_recv_receipt',
												'value'=>'1',
												'checked'=>$this->config->item('hide_barcode_on_sales_and_recv_receipt')));?>
												<label for="hide_barcode_on_sales_and_recv_receipt"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_group_all_taxes_on_receipt').':', 'group_all_taxes_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'group_all_taxes_on_receipt',
												'id'=>'group_all_taxes_on_receipt',
												'value'=>'1',
												'checked'=>$this->config->item('group_all_taxes_on_receipt')));?>
												<label for="group_all_taxes_on_receipt"><span></span></label>
											</div>
										</div>						
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_redirect_to_sale_or_recv_screen_after_printing_receipt').':', 'redirect_to_sale_or_recv_screen_after_printing_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'redirect_to_sale_or_recv_screen_after_printing_receipt',
												'id'=>'redirect_to_sale_or_recv_screen_after_printing_receipt',
												'value'=>'1',
												'checked'=>$this->config->item('redirect_to_sale_or_recv_screen_after_printing_receipt')));?>
												<label for="redirect_to_sale_or_recv_screen_after_printing_receipt"><span></span></label>
											</div>
										</div>
																		
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_receipt_text_size').':', 'receipt_text_size',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('receipt_text_size', $receipt_text_size_options, $this->config->item('receipt_text_size'),'class="form-control" id="receipt_text_size"'); ?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_hide_store_account_balance_on_receipt').':', 'hide_store_account_balance_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_store_account_balance_on_receipt',
												'id'=>'hide_store_account_balance_on_receipt',
												'value'=>'1',
												'checked'=>$this->config->item('hide_store_account_balance_on_receipt')));?>
												<label for="hide_store_account_balance_on_receipt"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_round_cash_on_sales').':', 'round_cash_on_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'round_cash_on_sales',
												'id'=>'round_cash_on_sales',
												'value'=>'round_cash_on_sales',
												'checked'=>$this->config->item('round_cash_on_sales')));?>
												<label for="round_cash_on_sales"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
										<?php echo form_label(lang('common_return_policy').':', 'return_policy',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_textarea(array(
												'name'=>'return_policy',
												'id'=>'return_policy',
												'class'=>'form-control text-area',
												'rows'=>'4',
												'cols'=>'30',
												'value'=>$this->config->item('return_policy')));?>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
										<?php echo form_label(lang('common_announcement_special').':', 'announcement_special',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_textarea(array(
												'name'=>'announcement_special',
												'id'=>'announcement_special',
												'class'=>'form-control text-area',
												'rows'=>'4',
												'cols'=>'30',
												'value'=>$this->config->item('announcement_special')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_enable_pdf_receipts').':', 'enable_pdf_receipts', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'enable_pdf_receipts',
												'id'=>'enable_pdf_receipts',
												'value'=>'enable_pdf_receipts',
												'checked'=>$this->config->item('enable_pdf_receipts')));?>
												<label for="enable_pdf_receipts"><span></span></label>
											</div>
										</div>
															
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
										<?php echo form_label(lang('config_pdf_receipt_message').':', 'pdf_receipt_message',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_textarea(array(
												'name'=>'pdf_receipt_message',
												'id'=>'pdf_receipt_message',
												'class'=>'form-control text-area',
												'rows'=>'4',
												'cols'=>'30',
												'value'=>$this->config->item('pdf_receipt_message')));?>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_show_signature_on_receiving_receipt').':', 'show_signature_on_receiving_receipt', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_signature_on_receiving_receipt',
												'id'=>'show_signature_on_receiving_receipt',
												'value'=>'show_signature_on_receiving_receipt',
												'checked'=>$this->config->item('show_signature_on_receiving_receipt')));?>
												<label for="show_signature_on_receiving_receipt"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
										<?php echo form_label(lang('config_override_signature_text').':', 'override_signature_text',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_textarea(array(
												'name'=>'override_signature_text',
												'id'=>'override_signature_text',
												'class'=>'form-control text-area',
												'rows'=>'4',
												'cols'=>'30',
												'value'=>$this->config->item('override_signature_text')));?>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>" >
										<?php echo form_label(lang('config_hide_categories_receivings_grid').':', 'hide_categories_receivings_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_categories_receivings_grid',
												'id'=>'hide_categories_receivings_grid',
												'value'=>'hide_categories_receivings_grid',
												'checked'=>$this->config->item('hide_categories_receivings_grid')));?>
												<label for="hide_categories_receivings_grid"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>" >
										<?php echo form_label(lang('config_hide_tags_receivings_grid').':', 'hide_tags_receivings_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_tags_receivings_grid',
												'id'=>'hide_tags_receivings_grid',
												'value'=>'hide_tags_receivings_grid',
												'checked'=>$this->config->item('hide_tags_receivings_grid')));?>
												<label for="hide_tags_receivings_grid"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>" >
										<?php echo form_label(lang('config_hide_suppliers_receivings_grid').':', 'hide_suppliers_receivings_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_suppliers_receivings_grid',
												'id'=>'hide_suppliers_receivings_grid',
												'value'=>'hide_suppliers_receivings_grid',
												'checked'=>$this->config->item('hide_suppliers_receivings_grid')));?>
												<label for="hide_suppliers_receivings_grid"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>" >
										<?php echo form_label(lang('config_hide_favorites_receivings_grid').':', 'hide_favorites_receivings_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_favorites_receivings_grid',
												'id'=>'hide_favorites_receivings_grid',
												'value'=>'hide_favorites_receivings_grid',
												'checked'=>$this->config->item('hide_favorites_receivings_grid')));?>
												<label for="hide_favorites_receivings_grid"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_receipt_download_filename_prefix').':', 'receipt_download_filename_prefix',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'receipt_download_filename_prefix',
												'id'=>'receipt_download_filename_prefix',
												'size'=> 40,
												'value'=>$this->config->item('receipt_download_filename_prefix')));?>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_capture_internal_notes_during_receiving').':', 'capture_internal_notes_during_receiving',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'capture_internal_notes_during_receiving',
												'id'=>'capture_internal_notes_during_receiving',
												'value'=>'capture_internal_notes_during_receiving',
												'checked'=>$this->config->item('capture_internal_notes_during_receiving')));?>
												<label for="capture_internal_notes_during_receiving"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_disable_variation_popup_in_receivings').':', 'disable_variation_popup_in_receivings',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_variation_popup_in_receivings',
												'id'=>'disable_variation_popup_in_receivings',
												'value'=>'disable_variation_popup_in_receivings',
												'checked'=>$this->config->item('disable_variation_popup_in_receivings')));?>
												<label for="disable_variation_popup_in_receivings"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_hide_location_name_on_receipt').':', 'hide_location_name_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_location_name_on_receipt',
												'id'=>'hide_location_name_on_receipt',
												'value'=>'hide_location_name_on_receipt',
												'checked'=>$this->config->item('hide_location_name_on_receipt')));?>
												<label for="hide_location_name_on_receipt"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_allow_reorder_sales_receipt').':', 'allow_reorder_sales_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'allow_reorder_sales_receipt',
												'id'=>'allow_reorder_sales_receipt',
												'value'=>'allow_reorder_sales_receipt',
												'checked'=>$this->config->item('allow_reorder_sales_receipt')));?>
												<label for="allow_reorder_sales_receipt"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_allow_reorder_receiving_receipt').':', 'allow_reorder_receiving_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'allow_reorder_receiving_receipt',
												'id'=>'allow_reorder_receiving_receipt',
												'value'=>'allow_reorder_receiving_receipt',
												'checked'=>$this->config->item('allow_reorder_receiving_receipt')));?>
												<label for="allow_reorder_receiving_receipt"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_remove_weight_from_receipt').':', 'remove_weight_from_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'remove_weight_from_receipt',
												'id'=>'remove_weight_from_receipt',
												'value'=>'remove_weight_from_receipt',
												'checked'=>$this->config->item('remove_weight_from_receipt')));?>
												<label for="remove_weight_from_receipt"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
											<?php echo form_label(lang('config_show_exchanged_totals_on_receipt').':', 'show_exchanged_totals_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_exchanged_totals_on_receipt',
												'id'=>'show_exchanged_totals_on_receipt',
												'value'=>'show_exchanged_totals_on_receipt',
												'checked'=>$this->config->item('show_exchanged_totals_on_receipt')));?>
												<label for="show_exchanged_totals_on_receipt"><span></span></label>
											</div>
										</div>


									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Receipt -->

				<!-- Profit -->
				<div class="tab-pane fade" id="profit_calculation">
					<div class="row">
						<div class="col-md-12 profit_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#profit_collapse">
									<?php echo create_section(lang("config_profit_info")) ?>
									</a>
								</div>
								<div id="profit_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_profit')) ?>">
											<?php echo form_label(lang('config_calculate_profit_for_giftcard_when').':', 'calculate_profit_for_giftcard_when',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_dropdown('calculate_profit_for_giftcard_when', array(
													''  => lang('common_do_nothing'),
													'redeeming_giftcard'   => lang('config_redeeming_giftcard'), 
													'selling_giftcard'  => lang('config_selling_giftcard'),
												),
												$this->config->item('calculate_profit_for_giftcard_when'), 'class="form-control" id="calculate_profit_for_giftcard_when"');
												?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_profit')) ?>">	
										<?php echo form_label(lang('config_remove_commission_from_profit_in_reports').':', 'remove_commission_from_profit_in_reports',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'remove_commission_from_profit_in_reports',
												'id'=>'remove_commission_from_profit_in_reports',
												'value'=>'1',
												'checked'=>$this->config->item('remove_commission_from_profit_in_reports')));?>
												<label for="remove_commission_from_profit_in_reports"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_profit')) ?>">	
										<?php echo form_label(lang('config_remove_points_from_profit').':', 'remove_points_from_profit',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'remove_points_from_profit',
												'id'=>'remove_points_from_profit',
												'value'=>'1',
												'checked'=>$this->config->item('remove_points_from_profit')));?>
												<label for="remove_points_from_profit"><span></span></label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Profit -->

				<!-- Barcodes -->
				<div class="tab-pane fade" id="barcodes">
					<div class="row">
						<div class="col-md-12 barcodes_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#barcodes_collapse">
									<?php echo create_section(lang("config_barcodes_info")) ?>
									</a>
								</div>
								<div id="barcodes_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
							
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
											<?php echo form_label(lang('config_id_to_show_on_barcode').':', 'id_to_show_on_barcode',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('id_to_show_on_barcode', array(
												'id'   => lang('common_item_id'),
												'number'  => lang('common_item_number_expanded'),
												'product_id'    => lang('common_product_id'),
												),
												$this->config->item('id_to_show_on_barcode'), 'class="form-control" id="id_to_show_on_barcode"')
												?>
											</div>
										</div>
										
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
											<?php echo form_label(lang('config_display_item_name_first_for_variation_name').':', 'display_item_name_first_for_variation_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'display_item_name_first_for_variation_name',
												'id'=>'display_item_name_first_for_variation_name',
												'value'=>'display_item_name_first_for_variation_name',
												'checked'=>$this->config->item('display_item_name_first_for_variation_name')));?>
												<label for="display_item_name_first_for_variation_name"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
											<?php echo form_label(lang('config_hide_barcode_on_barcode_labels').':', 'hide_barcode_on_barcode_labels',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_barcode_on_barcode_labels',
												'id'=>'hide_barcode_on_barcode_labels',
												'value'=>'hide_barcode_on_barcode_labels',
												'checked'=>$this->config->item('hide_barcode_on_barcode_labels')));?>
												<label for="hide_barcode_on_barcode_labels"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
											<?php echo form_label(lang('config_barcode_price_include_tax').':', 'barcode_price_include_tax',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'barcode_price_include_tax',
												'id'=>'barcode_price_include_tax',
												'value'=>'barcode_price_include_tax',
												'checked'=>$this->config->item('barcode_price_include_tax')));?>
												<label for="barcode_price_include_tax"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
											<?php echo form_label(lang('config_hide_expire_date_on_barcodes').':', 'hide_expire_date_on_barcodes',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_expire_date_on_barcodes',
												'id'=>'hide_expire_date_on_barcodes',
												'value'=>'hide_expire_date_on_barcodes',
												'checked'=>$this->config->item('hide_expire_date_on_barcodes')));?>
												<label for="hide_expire_date_on_barcodes"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
											<?php echo form_label(lang('config_hide_price_on_barcodes').':', 'hide_price_on_barcodes',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_price_on_barcodes',
												'id'=>'hide_price_on_barcodes',
												'value'=>'hide_price_on_barcodes',
												'checked'=>$this->config->item('hide_price_on_barcodes')));?>
												<label for="hide_price_on_barcodes"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
											<?php echo form_label(lang('config_hide_name_on_barcodes').':', 'hide_name_on_barcodes',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_name_on_barcodes',
												'id'=>'hide_name_on_barcodes',
												'value'=>'hide_name_on_barcodes',
												'checked'=>$this->config->item('hide_name_on_barcodes')));?>
												<label for="hide_name_on_barcodes"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
											<?php echo form_label(lang('config_disable_recv_number_on_barcode').':', 'disable_recv_number_on_barcode',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_recv_number_on_barcode',
												'id'=>'disable_recv_number_on_barcode',
												'value'=>'disable_recv_number_on_barcode',
												'checked'=>$this->config->item('disable_recv_number_on_barcode')));?>
												<label for="disable_recv_number_on_barcode"><span></span></label>
											</div>
										</div>
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
											<?php echo form_label(lang('config_show_barcode_company_name').':', 'show_barcode_company_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_barcode_company_name',
												'id'=>'show_barcode_company_name',
												'value'=>'show_barcode_company_name',
												'checked'=>$this->config->item('show_barcode_company_name')));?>
												<label for="show_barcode_company_name"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
											<?php echo form_label(lang('config_use_rtl_barcode_library').':', 'use_rtl_barcode_library',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'use_rtl_barcode_library',
												'id'=>'use_rtl_barcode_library',
												'value'=>'use_rtl_barcode_library',
												'checked'=>$this->config->item('use_rtl_barcode_library')));?>
												<label for="use_rtl_barcode_library"><span></span></label>
											</div>
										</div>
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
											<?php echo form_label(lang('config_enable_scale').':', 'enable_scale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'enable_scale',
												'id'=>'enable_scale',
												'value'=>'enable_scale',
												'checked'=>$this->config->item('enable_scale')));?>
												<label for="enable_scale"><span></span></label>
											</div>
										</div>
													
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
											<?php echo form_label(lang('config_scale_format').':', 'scale_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('scale_format', array(
												'scale_1'   => lang('config_scale_1'),
												'scale_2'  => lang('config_scale_2'),
												'scale_3'    => lang('config_scale_3'),
												'scale_4'    => lang('config_scale_4'),
												'scale_5'    => lang('config_scale_5'),
												'scale_6'    => lang('config_scale_6'),
												'scale_7'    => lang('config_scale_7'),
												'scale_8'    => lang('config_scale_8'),
												),
												$this->config->item('scale_format'), 'class="form-control" id="scale_format"')
												?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
											<?php echo form_label(lang('config_scale_divide_by').':', 'scale_divide_by',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('scale_divide_by', array(
												'10000'   => '10000',
												'1000'   => '1000',
												'100'   => '100',
												'10'  => '10',
												'1'    => '1',
												),
												$this->config->item('scale_divide_by'), 'class="form-control" id="scale_divide_by"')
												?>
											</div>
										</div>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Barcodes -->

				<!-- Custom Loyalty -->
				<div class="tab-pane fade" id="customer_loyalty">
					<div class="row">
						<div class="col-md-12 customer_loyalty_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#custom_loyalty_collapse">
									<?php echo create_section(lang("config_customer_loyalty_info")) ?>
									</a>
								</div>
								<div id="custom_loyalty_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
							
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
										<?php echo form_label(lang('config_enable_customer_loyalty_system').':', 'enable_customer_loyalty_system',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'enable_customer_loyalty_system',
												'id'=>'enable_customer_loyalty_system',
												'value'=>'enable_customer_loyalty_system',
												'checked'=>$this->config->item('enable_customer_loyalty_system')));?>
												<label for="enable_customer_loyalty_system"><span></span></label>
											</div>
										</div>
										
										<div id="loyalty_setup">
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
											<?php echo form_label(lang('config_disable_loyalty_by_default').':', 'disable_loyalty_by_default',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'disable_loyalty_by_default',
													'id'=>'disable_loyalty_by_default',
													'value'=>'disable_loyalty_by_default',
													'checked'=>$this->config->item('disable_loyalty_by_default')));?>
													<label for="disable_loyalty_by_default"><span></span></label>
												</div>
											</div>
											
											<div class="form-group" id="loyalty_option_wrapper" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
												<?php echo form_label(lang('config_loyalty_option').':', 'loyalty_option',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_dropdown('loyalty_option', 
												array(
													'simple'=> lang('config_simple'),
													'advanced'=>lang('config_advanced'),
												), $this->config->item('loyalty_option') ? $this->config->item('loyalty_option') : '20', 'class="form-control" id="loyalty_option"');
													?>
												</div>
											</div>		
											
											
											<div id="loyalty_setup_simple" style="display: none;">
													<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
													<?php echo form_label(lang('config_number_of_sales_for_discount').':', 'number_of_sales_for_discount',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
														<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
														'class'=>'validate form-control form-inps',
														'name'=>'number_of_sales_for_discount',
														'id'=>'number_of_sales_for_discount',
														'value'=>$this->config->item('number_of_sales_for_discount')));?>
														</div>
													</div>
													
													<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
													<?php echo form_label(lang('config_discount_percent_earned').':', 'discount_percent_earned',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
														<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
														'class'=>'validate form-control form-inps',
														'name'=>'discount_percent_earned',
														'id'=>'discount_percent_earned',
														'value'=>$this->config->item('discount_percent_earned')));?>
														</div>
													</div>
													
													<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
													<?php echo form_label(lang('hide_sales_to_discount_on_receipt').':', 'hide_sales_to_discount_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
														<div class="col-sm-9 col-md-9 col-lg-10">
														<?php echo form_checkbox(array(
															'name'=>'hide_sales_to_discount_on_receipt',
															'id'=>'hide_sales_to_discount_on_receipt',
															'value'=>'hide_sales_to_discount_on_receipt',
															'checked'=>$this->config->item('hide_sales_to_discount_on_receipt')));?>
															<label for="hide_sales_to_discount_on_receipt"><span></span></label>
														</div>
													</div>
													
													
											</div>
									
											
											<div id="loyalty_setup_advanced" style="display: none;">					
												<?php
												$spend_amount_for_points = '';
												$points_to_earn= '';
												if (strpos($this->config->item('spend_to_point_ratio'),':') !== FALSE)
												{
													list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);
													$spend_amount_for_points = (float)$spend_amount_for_points;
													$points_to_earn = (float)$points_to_earn;
													
												}
												?>
												<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
												<?php echo form_label(lang('config_spend_to_point_ratio').':', 'spend_amount_for_points',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
													<div class="col-sm-9 col-md-9 col-lg-10">
											
												<?php echo form_input(array(
													'class'=>'validate form-control form-inps',
													'name'=>'spend_amount_for_points',
													'id'=>'spend_amount_for_points',
													'placeholder' => lang('config_loyalty_explained_spend_amount'),
													'value'=>$spend_amount_for_points));?>
													<?php echo form_input(array(
														'class'=>'validate form-control form-inps',
														'name'=>'points_to_earn',
														'id'=>'points_to_earn',
														'placeholder' => lang('config_loyalty_explained_points_to_earn'),
														'value'=>$points_to_earn));?>
													</div>
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
											<?php echo form_label(lang('config_point_value').':', 'point_value',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_input(array(
												'class'=>'validate form-control form-inps',
												'name'=>'point_value',
												'id'=>'point_value',
												'value'=>$this->config->item('point_value') ? $this->config->item('point_value') : ''));?>

												</div>
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
											<?php echo form_label(lang('config_minimum_points_to_redeem').':', 'minimum_points_to_redeem',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_input(array(
												'class'=>'validate form-control form-inps',
												'name'=>'minimum_points_to_redeem',
												'id'=>'minimum_points_to_redeem',
												'value'=>$this->config->item('minimum_points_to_redeem') ? $this->config->item('minimum_points_to_redeem') : ''));?>

												</div>
											</div>
											
											
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
											<?php echo form_label(lang('config_loyalty_points_without_tax').':', 'loyalty_points_without_tax',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'loyalty_points_without_tax',
													'id'=>'loyalty_points_without_tax',
													'value'=>'loyalty_points_without_tax',
													'checked'=>$this->config->item('loyalty_points_without_tax')));?>
													<label for="loyalty_points_without_tax"><span></span></label>
												</div>
											</div>

											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
											<?php echo form_label(lang('config_prompt_to_use_points').':', 'prompt_to_use_points',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'prompt_to_use_points',
													'id'=>'prompt_to_use_points',
													'value'=>'prompt_to_use_points',
													'checked'=>$this->config->item('prompt_to_use_points')));?>
													<label for="prompt_to_use_points"><span></span></label>
												</div>
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
											<?php echo form_label(lang('config_hide_points_on_receipt').':', 'hide_points_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'hide_points_on_receipt',
													'id'=>'hide_points_on_receipt',
													'value'=>'hide_points_on_receipt',
													'checked'=>$this->config->item('hide_points_on_receipt')));?>
													<label for="hide_points_on_receipt"><span></span></label>
												</div>
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
											<?php echo form_label(lang('config_disable_gift_cards_sold_from_loyalty').':', 'disable_gift_cards_sold_from_loyalty',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'disable_gift_cards_sold_from_loyalty',
													'id'=>'disable_gift_cards_sold_from_loyalty',
													'value'=>'disable_gift_cards_sold_from_loyalty',
													'checked'=>$this->config->item('disable_gift_cards_sold_from_loyalty')));?>
													<label for="disable_gift_cards_sold_from_loyalty"><span></span></label>
												</div>
											</div>
											
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
											<?php echo form_label(lang('config_enable_points_for_giftcard_payments').':', 'enable_points_for_giftcard_payments',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'enable_points_for_giftcard_payments',
													'id'=>'enable_points_for_giftcard_payments',
													'value'=>'enable_points_for_giftcard_payments',
													'checked'=>$this->config->item('enable_points_for_giftcard_payments')));?>
													<label for="enable_points_for_giftcard_payments"><span></span></label>
												</div>
											</div>
											
											
											
										</div>
																									
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Custom Loyalty -->

				<!-- Price Tiers -->
				<div class="tab-pane fade" id="price_tiers">
					<div class="row">
						<div class="col-md-12 price_tiers_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#price_tiers_collapse">
									<?php echo create_section(lang("config_price_tiers_info")) ?>
									</a>
								</div>
								<div id="price_tiers_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
							
										<div class="form-group no-padding-right" data-keyword="<?php echo H(lang('config_keyword_price_tiers')) ?>">	
										<?php echo form_label(lang('config_price_tiers').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-md-9 col-sm-9 col-lg-10">
												<div class="table-responsive">
													<table id="price_tiers" class="table">
														<thead>
															<tr>
															<th><?php echo lang('config_sort'); ?></th>
															<th><?php echo lang('common_tier_name'); ?></th>
															<th><?php echo lang('config_default_percent_off'); ?></th>
															<th><?php echo lang('config_default_cost_plus_percent'); ?></th>
															<th><?php echo lang('config_default_cost_plus_fixed_amount'); ?></th>
															<th><?php echo lang('common_delete'); ?></th>
															</tr>
														</thead>
														
														<tbody>
														<?php foreach($tiers->result() as $tier) { ?>
															<tr>
																<td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
																<td><input type="text" data-index="<?php echo $tier->id; ?>" class="tiers_to_edit form-control" name="tiers_to_edit[<?php echo $tier->id; ?>][name]" value="<?php echo H($tier->name); ?>" /></td>
																<td><input type="text" data-index="<?php echo $tier->id; ?>" class="tiers_to_edit form-control default_percent_off" name="tiers_to_edit[<?php echo $tier->id; ?>][default_percent_off]" value="<?php echo $tier->default_percent_off ? to_quantity($tier->default_percent_off) : ''; ?>" /></td>
																<td><input type="text" data-index="<?php echo $tier->id; ?>" class="tiers_to_edit form-control default_cost_plus_percent" name="tiers_to_edit[<?php echo $tier->id; ?>][default_cost_plus_percent]" value="<?php echo $tier->default_cost_plus_percent ? to_quantity($tier->default_cost_plus_percent) : ''; ?>" /></td>
																<td><input type="text" data-index="<?php echo $tier->id; ?>" class="tiers_to_edit form-control default_cost_plus_fixed_amount" name="tiers_to_edit[<?php echo $tier->id; ?>][default_cost_plus_fixed_amount]" value="<?php echo $tier->default_cost_plus_fixed_amount ? to_currency_no_money($tier->default_cost_plus_fixed_amount) : ''; ?>" /></td>
															<td>
																<?php if ($this->Employee->has_module_action_permission('items', 'delete', $this->Employee->get_logged_in_employee_info()->person_id) || $this->Employee->has_module_action_permission('item_kits', 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>				
																<a class="delete_tier" href="javascript:void(0);" data-tier-id='<?php echo $tier->id; ?>'><?php echo lang('common_delete'); ?></a>
																<?php }else { ?>
																	&nbsp;
																<?php } ?>
																</td>
														</tr>
														<?php } ?>
														</tbody>
													</table>
													
													<a href="javascript:void(0);" id="add_tier"><?php echo lang('config_add_tier'); ?></a>
													</div>
												</div>
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_price_tiers')) ?>">	
												<?php echo form_label(lang('config_override_tier_name').':', 'override_tier_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
														'class'=>'form-control form-inps',
													'name'=>'override_tier_name',
													'id'=>'override_tier_name',
													'value'=>$this->config->item('override_tier_name')));?>
												</div>
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_price_tiers')) ?>">	
												<?php echo form_label(lang('config_hide_tier_on_receipt').':', 'hide_tier_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'hide_tier_on_receipt',
													'id'=>'hide_tier_on_receipt',
													'value'=>'1',
													'checked'=>$this->config->item('hide_tier_on_receipt')));?>
													<label for="hide_tier_on_receipt"><span></span></label>
												</div>
											</div>
											
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_price_tiers')) ?>">	
											<?php echo form_label(lang('config_default_tier_percent_type_for_excel_import').':', 'default_tier_percent_type_for_excel_import',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_dropdown('default_tier_percent_type_for_excel_import', array(
													'percent_off'    => lang('common_percent_off'),
													'cost_plus_percent'    => lang('common_cost_plus_percent'),
												),
													$this->config->item('default_tier_percent_type_for_excel_import')===NULL ? 'before' : $this->config->item('default_tier_percent_type_for_excel_import') , 'class="form-control" id="default_tier_percent_type_for_excel_import"');
													?>
												</div>						
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_price_tiers')) ?>">	
											<?php echo form_label(lang('config_default_tier_fixed_type_for_excel_import').':', 'default_tier_fixed_type_for_excel_import',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_dropdown('default_tier_fixed_type_for_excel_import', array(
													'fixed_amount'    => lang('common_fixed_amount'),
													'cost_plus_fixed_amount'    => lang('common_cost_plus_fixed_amount'),
												),
													$this->config->item('default_tier_fixed_type_for_excel_import')===NULL ? 'before' : $this->config->item('default_tier_fixed_type_for_excel_import') , 'class="form-control" id="default_tier_fixed_type_for_excel_import"');
													?>
												</div>						
											</div>
											
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_price_tiers')) ?>">	
												<?php echo form_label(lang('config_round_tier_prices_to_2_decimals').':', 'round_tier_prices_to_2_decimals',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'round_tier_prices_to_2_decimals',
													'id'=>'round_tier_prices_to_2_decimals',
													'value'=>'1',
													'checked'=>$this->config->item('round_tier_prices_to_2_decimals')));?>
													<label for="round_tier_prices_to_2_decimals"><span></span></label>
												</div>
											</div>
											
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Price Tiers -->

				<!-- Auto Increment IDs Settings -->
				<div class="tab-pane fade" id="id_numbers">
					<div class="row">
						<div class="col-md-12 auto_increment_ids_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#id_numbers_collapse">
									<?php echo create_section(lang("config_payment_types_info")) ?>
									</a>
								</div>
								<div id="id_numbers_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<div class="">
											<div class="col-sm-offset-3 col-md-offset-3 col-lg-offset-2 col-sm-9 col-md-9 col-lg-10">
												<div class="alert alert-info" role="alert"><strong><?php echo lang('common_note') ?>:</strong> <?php echo lang('config_auto_increment_note') ?></div>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_auto_increment')) ?>">
											
											<?php echo form_label(lang('config_item_id_auto_increment').':', 'item_id_auto_increment',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'validate form-control form-inps',
												'name'=>'item_id_auto_increment',
												'id'=>'item_id_auto_increment',
												'value'=>''));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_auto_increment')) ?>">	
											<?php echo form_label(lang('config_item_kit_id_auto_increment').':', 'item_kit_id_auto_increment',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'validate form-control form-inps',
												'name'=>'item_kit_id_auto_increment',
												'id'=>'item_kit_id_auto_increment',
												'value'=>''));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_auto_increment')) ?>">	
											<?php echo form_label(lang('config_sale_id_auto_increment').':', 'sale_id_auto_increment',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'validate form-control form-inps',
												'name'=>'sale_id_auto_increment',
												'id'=>'sale_id_auto_increment',
												'value'=>''));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_auto_increment')) ?>">	
											<?php echo form_label(lang('config_receiving_id_auto_increment').':', 'receiving_id_auto_increment',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'validate form-control form-inps',
												'name'=>'receiving_id_auto_increment',
												'id'=>'receiving_id_auto_increment',
												'value'=>''));?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Auto Increment IDs Settings -->

				<!-- Items Settings -->
				<div class="tab-pane fade" id="items_settings">
					<div class="row">
						<div class="col-md-12 items_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#items_settings_collapse">
									<?php echo create_section(lang("config_items_info")) ?>
									</a>
								</div>
								<div id="items_settings_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<?php if ($this->Employee->has_module_action_permission('items', 'manage_categories', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo anchor("items/manage_categories",lang('items_manage_categories'),array('target' => '_blank', 'title'=>lang('items_manage_categories')));?>
												</div>
											</div>
										<?php } ?>		
									
										<?php if ($this->Employee->has_module_action_permission('items', 'manage_tags', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo anchor("items/manage_tags",lang('items_manage_tags'),array('target' => '_blank', 'title'=>lang('items_manage_tags')));?>
												</div>
											</div>
										<?php } ?>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_number_of_items_per_page').':', 'number_of_items_per_page',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('number_of_items_per_page', 
											array(
												'20'=>'20',
												'50'=>'50',
												'100'=>'100',
												'200'=>'200',
												'500'=>'500'
												), $this->config->item('number_of_items_per_page') ? $this->config->item('number_of_items_per_page') : '20', 'class="form-control" id="number_of_items_per_page"');
												?>
											</div>
										</div>		
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_items_per_search_suggestions').':', 'items_per_search_suggestions',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('items_per_search_suggestions', 
											array(
												'20'=>'20',
												'50'=>'50',
												'100'=>'100',
												'200'=>'200',
												'500'=>'500'
												), $this->config->item('items_per_search_suggestions') ? $this->config->item('items_per_search_suggestions') : '20', 'class="form-control" id="items_per_search_suggestions"');
												?>
											</div>
										</div>		
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_number_of_items_in_grid').':', 'number_of_items_in_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php 
												$numbers = array();
												foreach(range(1, 50) as $number) 
												{ 
													$numbers[$number] = $number;
													
												}
												?> 
											<?php echo form_dropdown('number_of_items_in_grid', 
												$numbers, $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : '14', 'class="form-control" id="number_of_items_in_grid"');
												?>
											</div>
										</div>				
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_default_reorder_level_when_creating_items').':', 'default_reorder_level_when_creating_items',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'default_reorder_level_when_creating_items',
												'id'=>'default_reorder_level_when_creating_items',
												'value'=>$this->config->item('default_reorder_level_when_creating_items')));?>
											</div>
										</div>
										

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_default_days_to_expire_when_creating_items').':', 'default_days_to_expire_when_creating_items',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'default_days_to_expire_when_creating_items',
												'id'=>'default_days_to_expire_when_creating_items',
												'value'=>$this->config->item('default_days_to_expire_when_creating_items')));?>
											</div>
										</div>
										
										
										
													
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_default_new_items_to_service').':', 'default_new_items_to_service',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'default_new_items_to_service',
												'id'=>'default_new_items_to_service',
												'value'=>'default_new_items_to_service',
												'checked'=>$this->config->item('default_new_items_to_service')));?>
												<label for="default_new_items_to_service"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_highlight_low_inventory_items_in_items_module').':', 'highlight_low_inventory_items_in_items_module',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'highlight_low_inventory_items_in_items_module',
												'id'=>'highlight_low_inventory_items_in_items_module',
												'value'=>'highlight_low_inventory_items_in_items_module',
												'checked'=>$this->config->item('highlight_low_inventory_items_in_items_module')));?>
												<label for="highlight_low_inventory_items_in_items_module"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_limit_manual_price_adj').':', 'limit_manual_price_adj',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'limit_manual_price_adj',
												'id'=>'limit_manual_price_adj',
												'value'=>'1',
												'checked'=>$this->config->item('limit_manual_price_adj')));?>
												<label for="limit_manual_price_adj"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('common_max_discount_percent').' % :', 'max_discount_percent',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?> 
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'max_discount_percent',
												'id'=>'max_discount_percent',
												'value'=>$this->config->item('max_discount_percent')));?>
											</div>
										</div>
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_enable_markup_calculator').':', 'enable_markup_calculator',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'enable_markup_calculator',
												'id'=>'enable_markup_calculator',
												'value'=>'1',
												'checked'=>$this->config->item('enable_markup_calculator')));?>
												<label for="enable_markup_calculator"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_enable_margin_calculator').':', 'enable_margin_calculator',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'enable_margin_calculator',
												'id'=>'enable_margin_calculator',
												'value'=>'1',
												'checked'=>$this->config->item('enable_margin_calculator')));?>
												<label for="enable_margin_calculator"><span></span></label>
											</div>
										</div>
									
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_verify_age_for_products').':', 'verify_age_for_products',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'verify_age_for_products',
												'id'=>'verify_age_for_products',
												'value'=>'1',
												'checked'=>$this->config->item('verify_age_for_products')));?>
												<label for="verify_age_for_products"><span></span></label>
											</div>
										</div>


										<div id="default_age_input_container" class="form-group  <?php if (!$this->config->item('verify_age_for_products')){echo 'hidden';} ?>" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_default_age_to_verify').':', 'default_age_to_verify',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'default_age_to_verify',
												'id'=>'default_age_to_verify',
												'value'=>$this->config->item('default_age_to_verify')));?>
											</div>
										</div>

										<div id="strict_age_format_check_container" class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_strict_age_format_check').':', 'strict_age_format_check',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'strict_age_format_check',
												'id'=>'strict_age_format_check',
												'value'=>'1',
												'checked'=>$this->config->item('strict_age_format_check')));?>
												<label for="strict_age_format_check"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('hide_supplier_in_item_search_result').':', 'hide_supplier_in_item_search_result',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_supplier_in_item_search_result',
												'id'=>'hide_supplier_in_item_search_result',
												'value'=>'hide_supplier_in_item_search_result',
												'checked'=>$this->config->item('hide_supplier_in_item_search_result')));?>
												<label for="hide_supplier_in_item_search_result"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_hide_supplier_from_item_popup').':', 'hide_supplier_from_item_popup',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_supplier_from_item_popup',
												'id'=>'hide_supplier_from_item_popup',
												'value'=>'hide_supplier_from_item_popup',
												'checked'=>$this->config->item('hide_supplier_from_item_popup')));?>
												<label for="hide_supplier_from_item_popup"><span></span></label>
											</div>
										</div>
										

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_easy_item_clone_button').':', 'easy_item_clone_button',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'easy_item_clone_button',
												'id'=>'easy_item_clone_button',
												'value'=>'1',
												'checked'=>$this->config->item('easy_item_clone_button')));?>
												<label for="easy_item_clone_button"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
											<?php echo form_label(lang('config_add_ck_editor').':', 'add_ck_editor_to_item',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'add_ck_editor_to_item',
												'id'=>'add_ck_editor_to_item',
												'value'=>'1',
												'checked'=>$this->config->item('add_ck_editor_to_item')));?>
												<label for="add_ck_editor_to_item"><span></span></label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Items Settings -->

				<!-- Employee Settings -->
				<div class="tab-pane fade" id="employee_settings">
					<div class="row">
						<div class="col-md-12 employee_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#employee_settings_collapse">
									<?php echo create_section(lang("config_employee_info")) ?>
									</a>
								</div>
								<div id="employee_settings_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_employees')) ?>">	
											<?php echo form_label(lang('config_allow_employees_to_use_2fa').':', 'allow_employees_to_use_2fa',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'allow_employees_to_use_2fa',
												'id'=>'allow_employees_to_use_2fa',
												'value'=>'allow_employees_to_use_2fa',
												'checked'=>$this->config->item('allow_employees_to_use_2fa')));?>
												<label for="allow_employees_to_use_2fa"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_employees')) ?>">	
											<?php echo form_label(lang('config_enable_timeclock').':', 'timeclock',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'timeclock',
												'id'=>'timeclock',
												'value'=>'timeclock',
												'checked'=>$this->config->item('timeclock')));?>
												<label for="timeclock"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_employees')) ?>">	
											<?php echo form_label(lang('config_enable_timeclock_pto').':', 'timeclock_pto',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'timeclock_pto',
												'id'=>'timeclock_pto',
												'value'=>'timeclock_pto',
												'checked'=>$this->config->item('timeclock_pto')));?>
												<label for="timeclock_pto"><span></span></label>
											</div>
										</div>


										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_employees')) ?>">	
											<?php echo form_label(lang('config_logout_on_clock_out').':', 'logout_on_clock_out',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'logout_on_clock_out',
												'id'=>'logout_on_clock_out',
												'value'=>'logout_on_clock_out',
												'checked'=>$this->config->item('logout_on_clock_out')));?>
												<label for="logout_on_clock_out"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_employees')) ?>">	
											<?php echo form_label(lang('config_fast_user_switching').':', 'fast_user_switching',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'fast_user_switching',
												'id'=>'fast_user_switching',
												'value'=>'fast_user_switching',
												'checked'=>$this->config->item('fast_user_switching')));?>
												<label for="fast_user_switching"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_employees')) ?>">	
											<?php echo form_label(lang('config_require_employee_login_before_each_sale').':', 'require_employee_login_before_each_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'require_employee_login_before_each_sale',
												'id'=>'require_employee_login_before_each_sale',
												'value'=>'require_employee_login_before_each_sale',
												'checked'=>$this->config->item('require_employee_login_before_each_sale')));?>
												<label for="require_employee_login_before_each_sale"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_employees')) ?>">	
											<?php echo form_label(lang('config_reset_location_when_switching_employee').':', 'reset_location_when_switching_employee',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'reset_location_when_switching_employee',
												'id'=>'reset_location_when_switching_employee',
												'value'=>'reset_location_when_switching_employee',
												'checked'=>$this->config->item('reset_location_when_switching_employee')));?>
												<label for="reset_location_when_switching_employee"><span></span></label>
											</div>
										</div>
										</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Employee Settings -->

				<!-- Store Accounts -->
				<div class="tab-pane fade" id="store_accounts">
					<div class="row">
						<div class="col-md-12 store_accounts_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#store_accounts_collapse">
									<?php echo create_section(lang("config_store_accounts_info")) ?>
									</a>
								</div>
								<div id="store_accounts_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
							
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
										<?php echo form_label(lang('config_customers_store_accounts').':', 'customers_store_accounts',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'customers_store_accounts',
												'id'=>'customers_store_accounts',
												'value'=>'customers_store_accounts',
												'checked'=>$this->config->item('customers_store_accounts')));?>
												<label for="customers_store_accounts"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
										<?php echo form_label(lang('config_create_invoices_for_customer_store_account_charges').':', 'create_invoices_for_customer_store_account_charges',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'create_invoices_for_customer_store_account_charges',
												'id'=>'create_invoices_for_customer_store_account_charges',
												'value'=>'create_invoices_for_customer_store_account_charges',
												'checked'=>$this->config->item('create_invoices_for_customer_store_account_charges')));?>
												<label for="create_invoices_for_customer_store_account_charges"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
										<?php echo form_label(lang('config_automatically_email_invoice').':', 'automatically_email_invoice',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'automatically_email_invoice',
												'id'=>'automatically_email_invoice',
												'value'=>'automatically_email_invoice',
												'checked'=>$this->config->item('automatically_email_invoice')));?>
												<label for="automatically_email_invoice"><span></span></label>
											</div>
										</div>
																
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_default_credit_limit').':', 'default_credit_limit',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'default_credit_limit',
												'id'=>'default_credit_limit',
												'value'=>$this->config->item('default_credit_limit')));?>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
										<?php echo form_label(lang('config_suppliers_store_accounts').':', 'suppliers_store_accounts',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'suppliers_store_accounts',
												'id'=>'suppliers_store_accounts',
												'value'=>'suppliers_store_accounts',
												'checked'=>$this->config->item('suppliers_store_accounts')));?>
												<label for="suppliers_store_accounts"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
										<?php echo form_label(lang('config_create_invoices_for_supplier_store_account_charges').':', 'create_invoices_for_supplier_store_account_charges',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'create_invoices_for_supplier_store_account_charges',
												'id'=>'create_invoices_for_supplier_store_account_charges',
												'value'=>'create_invoices_for_supplier_store_account_charges',
												'checked'=>$this->config->item('create_invoices_for_supplier_store_account_charges')));?>
												<label for="create_invoices_for_supplier_store_account_charges"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_disable_store_account_when_over_credit_limit').':', 'disable_store_account_when_over_credit_limit',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_store_account_when_over_credit_limit',
												'id'=>'disable_store_account_when_over_credit_limit',
												'value'=>'1',
												'checked'=>$this->config->item('disable_store_account_when_over_credit_limit')));?>
												<label for="disable_store_account_when_over_credit_limit"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
										<?php echo form_label(lang('config_store_account_statement_message').':', 'store_account_statement_message',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_textarea(array(
												'name'=>'store_account_statement_message',
												'id'=>'store_account_statement_message',
												'class'=>'form-control text-area',
												'rows'=>'4',
												'cols'=>'30',
												'value'=>$this->config->item('store_account_statement_message')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_hide_store_account_payments_in_reports').':', 'hide_store_account_payments_in_reports',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_store_account_payments_in_reports',
												'id'=>'hide_store_account_payments_in_reports',
												'value'=>'1',
												'checked'=>$this->config->item('hide_store_account_payments_in_reports')));?>
												<label for="hide_store_account_payments_in_reports"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_hide_store_account_payments_from_report_totals').':', 'hide_store_account_payments_from_report_totals',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_store_account_payments_from_report_totals',
												'id'=>'hide_store_account_payments_from_report_totals',
												'value'=>'1',
												'checked'=>$this->config->item('hide_store_account_payments_from_report_totals')));?>
												<label for="hide_store_account_payments_from_report_totals"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_paypal_me').':', 'paypal_me',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'paypal_me',
												'id'=>'paypal_me',
												'placeholder' => 'paypal.me '.lang('common_username'),
												'value'=>$this->config->item('paypal_me')));?>
											</div>
										</div>
														
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Store Accounts -->

				<!-- Disable Moudles -->
				<div class="tab-pane fade" id="disable_modules">
					<div class="row">
						<div class="col-md-12 disable_modules">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#disable_modules_collapse">
									<?php echo create_section(lang("config_disable_modules")) ?>
									</a>
								</div>
								<div id="disable_modules_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<?php
											foreach ($all_modules->result() as $module) {
												if($module->module_id=='config'){
													continue;
												}
												$checkbox_options = array(
													'name' => 'disable_modules[]',
													'id' => 'permissions' . $module->module_id,
													'value' => $module->module_id,
													'checked' => array_search($module->module_id, $disable_modules) === false ? false: true ,
													'class' => 'module_checkboxes '
												);

												if ($logged_in_employee_id != 1) {
													if (($checkbox_options['checked']) || !$this->Employee->has_module_permission($module->module_id, $logged_in_employee_id, FALSE, TRUE)) {
														$checkbox_options['disabled'] = 'disabled';

														//Only send permission if checked
														if ($checkbox_options['checked']) {
															echo form_hidden('permissions[]', $module->module_id);
														}
													}
												}
										?>
											<div class="form-group" data-keyword="<?php echo H(lang('module_config')) ?>">	
												<?php echo form_label(lang('common_disable').' '.lang($module->name_lang_key).':', $module->module_id, array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_checkbox($checkbox_options, '1', null,'id="'.$module->module_id.'"');?>
													<label for="<?php echo 'permissions'.$module->module_id; ?>"><span></span></label>
												</div>	
											</div>
										<?php } ?>
									
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_modules')) ?>">
											<?php echo form_label(lang('config_hover_to_expand_sub_modules') . ':', 'hover_to_expand_sub_modules', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
														'name' => 'hover_to_expand_sub_modules',
														'id' => 'hover_to_expand_sub_modules',
														'value' => 'hover_to_expand_sub_modules',
														'checked' => $this->config->item('hover_to_expand_sub_modules'))); ?>
												<label for="hover_to_expand_sub_modules"><span></span></label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Disable Moudles -->

				<!-- Application Settings -->
				<div class="tab-pane fade" id="application_settings">
					<div class="row">
						<div class="col-md-12 application_settings_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#application_settings_collapse">
									<?php echo create_section(lang("config_application_settings_info")) ?>
									</a>
								</div>
								<div id="application_settings_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<?php if(is_on_demo_host()) { ?>
											<div class="form-group">	
												<div class="col-sm-9 col-md-9 col-lg-10">
												<span class="text-danger"><?php echo lang('config_cannot_change_language'); ?></span>
												</div>
											</div>
										<?php } ?>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('common_language').':', 'language',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_dropdown('language', array(
														'english'  => 'English',
														'indonesia'    => 'Indonesia',
														'spanish'   => 'Español', 
														'french'    => 'Fançais',
														'italian'    => 'Italiano',
														'german'    => 'Deutsch',
														'dutch'    => 'Nederlands',
														'portugues'    => 'Portugues',
														'arabic' => 'العَرَبِيةُ‎‎',
														'khmer' => 'Khmer',
														'vietnamese' => 'Vietnamese',
														'chinese' => '中文',
														'chinese_traditional' => '繁體中文',
														'tamil' => 'Tamil',
													),
													$this->Appconfig->get_raw_language_value(), 'class="form-control" id="language"');
													?>
											</div>						
										</div>
									
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_date_format').':', 'date_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('date_format', array(
												'middle_endian'    => '12/30/2000',
												'little_endian'  => '30-12-2000',
												'big_endian'   => '2000-12-30'), $this->config->item('date_format'), 'class="form-control" id="date_format"');
												?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_time_format').':', 'time_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('time_format', array(
												'12_hour'    => '1:00 PM',
												'24_hour'  => '13:00'
												), $this->config->item('time_format'), 'class="form-control" id="time_format"');
												?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_week_start_day').':', 'week_start_day',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('week_start_day', array(
												'monday'    => lang('common_monday'),
												'sunday'  => lang('common_sunday')
												), $this->config->item('week_start_day'), 'class="form-control" id="week_start_day"');
												?>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">
												<?php echo form_label(lang('config_store_opening_time').' :', 'store_opening_time', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<div class="input-group">
														<span class="input-group-addon bg"><i class="glyphicon glyphicon-time"></i></span>
														<?php echo form_input(array(
														'name'=>'store_opening_time',
														'id'=>'store_opening_time',
																'class'=>'form-control timepicker',
														'value'=> $this->config->item('store_opening_time') ? $this->config->item('store_opening_time') : ''
														));?>
													</div>
												</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">
												<?php echo form_label(lang('config_store_closing_time').' :', 'store_closing_time', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<div class="input-group">
														<span class="input-group-addon bg"><i class="glyphicon glyphicon-time"></i></span>
														<?php echo form_input(array(
														'name'=>'store_closing_time',
														'id'=>'store_closing_time',
																'class'=>'form-control timepicker',
														'value'=> $this->config->item('store_closing_time') ? $this->config->item('store_closing_time') : ''
														));?>
													</div>
												</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label('PayVantage:', 'payvantage',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'payvantage',
												'id'=>'payvantage',
												'value'=>'payvantage',
												'checked'=>$this->config->item('payvantage')));?>
												<label for="payvantage"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_offline_mode').':', 'offline_mode',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'offline_mode',
												'id'=>'offline_mode',
												'value'=>'offline_mode',
												'checked'=>$this->config->item('offline_mode')));?>
												<label for="offline_mode"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_auto_sync_offline_sales').':', 'auto_sync_offline_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'auto_sync_offline_sales',
												'id'=>'auto_sync_offline_sales',
												'value'=>'auto_sync_offline_sales',
												'checked'=>$this->config->item('auto_sync_offline_sales')));?>
												<label for="auto_sync_offline_sales"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_offline_mode_sync_period').':', 'offline_mode_sync_period',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php 
												$numbers = array();
												foreach(range(1, 48) as $number) 
												{ 
													$numbers[$number] = $number;
												}
												?> 
											<?php echo form_dropdown('offline_mode_sync_period', 
												$numbers, $this->config->item('offline_mode_sync_period') ? $this->config->item('offline_mode_sync_period') : '24', 'class="form-control" id="number_of_items_in_grid"');
												?>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_reset_offline_data').':', 'offline_mode',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<button onclick='delete_all_client_side_dbs();bootbox.alert(<?php echo json_encode(lang('common_success')); ?>);' id="reset_offline_mode" type="button" class="btn btn-lg btn-primary"><?php echo lang('config_reset_offline_data');?></button>
											</div>
										</div>
										
						
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('common_dark_mode').':', 'dark_mode',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'dark_mode',
												'id'=>'dark_mode',
												'value'=>'dark_mode',
												'checked'=>$this->config->item('dark_mode')));?>
												<label for="dark_mode"><span></span></label>
											</div>
										</div>
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_only_allow_current_location_customers').':', 'only_allow_current_location_customers',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'only_allow_current_location_customers',
												'id'=>'only_allow_current_location_customers',
												'value'=>'only_allow_current_location_customers',
												'checked'=>$this->config->item('only_allow_current_location_customers')));?>
												<label for="only_allow_current_location_customers"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_default_new_customer_to_current_location').':', 'default_new_customer_to_current_location',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'default_new_customer_to_current_location',
												'id'=>'default_new_customer_to_current_location',
												'value'=>'default_new_customer_to_current_location',
												'checked'=>$this->config->item('default_new_customer_to_current_location')));?>
												<label for="default_new_customer_to_current_location"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_only_allow_current_location_employees').':', 'only_allow_current_location_employees',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'only_allow_current_location_employees',
												'id'=>'only_allow_current_location_employees',
												'value'=>'only_allow_current_location_employees',
												'checked'=>$this->config->item('only_allow_current_location_employees')));?>
												<label for="only_allow_current_location_employees"><span></span></label>
											</div>
										</div>
										
									
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_force_https').':', 'force_https',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'force_https',
												'id'=>'force_https',
												'value'=>'force_https',
												'checked'=>$this->config->item('force_https')));?>
												<label for="force_https"><span></span></label>
											</div>
										</div>

										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_hide_expire_dashboard').':', 'hide_expire_dashboard',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_expire_dashboard',
												'id'=>'hide_expire_dashboard',
												'value'=>'hide_expire_dashboard',
												'checked'=>$this->config->item('hide_expire_dashboard')));?>
												<label for="hide_expire_dashboard"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_do_not_delete_saved_card_after_failure').':', 'do_not_delete_saved_card_after_failure',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_delete_saved_card_after_failure',
												'id'=>'do_not_delete_saved_card_after_failure',
												'value'=>'do_not_delete_saved_card_after_failure',
												'checked'=>$this->config->item('do_not_delete_saved_card_after_failure')));?>
												<label for="do_not_delete_saved_card_after_failure"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>"> 
											<?php echo form_label(lang('config_do_not_force_http').':', 'do_not_force_http',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_force_http',
												'id'=>'do_not_force_http',
												'value'=>'do_not_force_http',
												'checked'=>$this->config->item('do_not_force_http')));?>
												<label for="do_not_force_http"><span></span></label>
											</div>
										</div>
										
										<?php if (!is_on_demo_host()) { ?>
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('common_test_mode').' ('.lang('config_test_mode_help').'):', 'test_mode',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'test_mode',
													'id'=>'test_mode',
													'value'=>'test_mode',
													'checked'=>$this->config->item('test_mode')));?>
													<label for="test_mode"><span></span></label>
												</div>
											</div>
										<?php } ?>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('common_disable_test_mode').':', 'disable_test_mode',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'disable_test_mode',
												'id'=>'disable_test_mode',
												'value'=>'disable_test_mode',
												'checked'=>$this->config->item('disable_test_mode')));?>
												<label for="disable_test_mode"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_hide_item_descriptions_in_reports').':', 'hide_item_descriptions_in_reports',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'hide_item_descriptions_in_reports',
												'id'=>'hide_item_descriptions_in_reports',
												'value'=>'hide_item_descriptions_in_reports',
												'checked'=>$this->config->item('hide_item_descriptions_in_reports')));?>
												<label for="hide_item_descriptions_in_reports"><span></span></label>
											</div>
										</div>
									
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_enable_sounds').':', 'enable_sounds',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'enable_sounds',
												'id'=>'enable_sounds',
												'value'=>'enable_sounds',
												'checked'=>$this->config->item('enable_sounds')));?>
												<label for="enable_sounds"><span></span></label>
											</div>
										</div>
																			
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_show_language_switcher').':', 'show_language_switcher',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_language_switcher',
												'id'=>'show_language_switcher',
												'value'=>'1',
												'checked'=>$this->config->item('show_language_switcher')));?>
												<label for="show_language_switcher"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_show_clock_on_header').':', 'show_clock_on_header',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_clock_on_header',
												'id'=>'show_clock_on_header',
												'value'=>'1',
												'checked'=>$this->config->item('show_clock_on_header')));?>
												<label for="show_clock_on_header"><span></span></label>
													<p class="help-block"><?php echo lang('config_show_clock_on_header_help_text'); ?></p>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_legacy_detailed_report_export').':', 'legacy_detailed_report_export',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'legacy_detailed_report_export',
												'id'=>'legacy_detailed_report_export',
												'value'=>'1',
												'checked'=>$this->config->item('legacy_detailed_report_export')));?>
												<label for="legacy_detailed_report_export"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">
											<?php echo form_label(lang('config_overwrite_existing_items_on_excel_import').':', 'overwrite_existing_items_on_excel_import',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'overwrite_existing_items_on_excel_import',
												'id'=>'overwrite_existing_items_on_excel_import',
												'value'=>'1',
												'checked'=>$this->config->item('overwrite_existing_items_on_excel_import')));?>
												<label for="overwrite_existing_items_on_excel_import"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_report_sort_order').':', 'report_sort_order',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('report_sort_order', array('asc' => lang('config_asc'), 'desc' => lang('config_desc')), $this->config->item('report_sort_order'),'class="form-control" id="report_sort_order"'); ?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_speed_up_search_queries').' ('.lang('config_speed_up_note').')'.':', 'speed_up_search_queries',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'speed_up_search_queries',
												'id'=>'speed_up_search_queries',
												'value'=>'1',
												'checked'=>$this->config->item('speed_up_search_queries')));?>
												<label for="speed_up_search_queries"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_dont_show_images_in_search_suggestions').' ('.lang('config_speed_up_note').')'.':', 'dont_show_images_in_search_suggestions',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'dont_show_images_in_search_suggestions',
												'id'=>'dont_show_images_in_search_suggestions',
												'value'=>'1',
												'checked'=>$this->config->item('dont_show_images_in_search_suggestions')));?>
												<label for="dont_show_images_in_search_suggestions"><span></span></label>
											</div>
										</div>
										
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_customer_allow_partial_match').':', 'customer_allow_partial_match',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'customer_allow_partial_match',
												'id'=>'customer_allow_partial_match',
												'value'=>'1',
												'checked'=>$this->config->item('customer_allow_partial_match')));?>
												<label for="customer_allow_partial_match"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_enable_quick_edit').':', 'enable_quick_edit',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'enable_quick_edit',
												'id'=>'enable_quick_edit',
												'value'=>'1',
												'checked'=>$this->config->item('enable_quick_edit')));?>
												<label for="enable_quick_edit"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_enable_quick_expense').':', 'enable_quick_expense',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'enable_quick_expense',
												'id'=>'enable_quick_expense',
												'value'=>'1',
												'checked'=>$this->config->item('enable_quick_expense')));?>
												<label for="enable_quick_expense"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_enhanced_search_method').':', 'enhanced_search_method',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php 
												$enhanced_search_options = array(
													'name'=>'enhanced_search_method',
													'id'=>'enhanced_search_method',
													'value'=>'1',
													'checked'=>$this->config->item('enhanced_search_method') && $this->config->item('supports_full_text'));
												
													if (!$this->config->item('supports_full_text'))
													{
														$enhanced_search_options['disabled'] = 'disabled';
													}
												
													echo form_checkbox($enhanced_search_options);
												
													?>
													<label for="enhanced_search_method"><span></span></label>
												</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_include_child_categories_when_searching_or_reporting').':', 'include_child_categories_when_searching_or_reporting',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'include_child_categories_when_searching_or_reporting',
												'id'=>'include_child_categories_when_searching_or_reporting',
												'value'=>'1',
												'checked'=>$this->config->item('include_child_categories_when_searching_or_reporting')));?>
												<label for="include_child_categories_when_searching_or_reporting"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_show_full_category_path').':', 'show_full_category_path',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'show_full_category_path',
												'id'=>'show_full_category_path',
												'value'=>'1',
												'checked'=>$this->config->item('show_full_category_path')));?>
												<label for="show_full_category_path"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_spreadsheet_format').':', 'spreadsheet_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('spreadsheet_format', array('CSV' => lang('config_csv'), 'XLSX' => lang('config_xlsx')), $this->config->item('spreadsheet_format'),'class="form-control" id="spreadsheet_format"'); ?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_mailing_labels_type').':', 'mailing_labels_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('mailing_labels_type', array('pdf' => 'PDF', 'excel' => 'Excel'), $this->config->item('mailing_labels_type'),'class="form-control" id="mailing_labels_type"'); ?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_phppos_session_expiration').':', 'phppos_session_expiration',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('phppos_session_expiration',$phppos_session_expirations, $this->config->item('phppos_session_expiration')!==NULL ? $this->config->item('phppos_session_expiration') : 0,'class="form-control" id="phppos_session_expiration"'); ?>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_always_minimize_menu').':', 'always_minimize_menu',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'always_minimize_menu',
												'id'=>'always_minimize_menu',
												'value'=>'1',
												'checked'=>$this->config->item('always_minimize_menu')));?>
												<label for="always_minimize_menu"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_item_lookup_order').':', 'item_lookup_order',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<ul id="item_lookup_order_list" class="list-group">
													<?php 
													foreach($item_lookup_order as $item_lookup_number)
													{
													?>
														<li class="list-group-item"><input name="item_lookup_order[]" type="hidden" value="<?php echo H($item_lookup_number); ?>"><?php echo lang('common_'.$item_lookup_number); ?><span class="ui-icon ui-icon-arrowthick-2-n-s pull-right"></span></li>
													<?php 
													}
													?>
												</ul>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_allow_scan_of_customer_into_item_field').':', 'allow_scan_of_customer_into_item_field',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'allow_scan_of_customer_into_item_field',
												'id'=>'allow_scan_of_customer_into_item_field',
												'value'=>'1',
												'checked'=>$this->config->item('allow_scan_of_customer_into_item_field')));?>
												<label for="allow_scan_of_customer_into_item_field"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_send_sms_via_whatsapp').':', 'send_sms_via_whatsapp',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'send_sms_via_whatsapp',
												'id'=>'send_sms_via_whatsapp',
												'value'=>'1',
												'checked'=>$this->config->item('send_sms_via_whatsapp')));?>
												<label for="send_sms_via_whatsapp"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_enable_quick_customers').':', 'enable_quick_customers',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'		=>	'enable_quick_customers',
												'id'		=>	'enable_quick_customers',
												'value'		=>	'1',
												'checked'	=>	$this->config->item('enable_quick_customers')));?>
												<label for="enable_quick_customers"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_enable_quick_suppliers').':', 'enable_quick_suppliers',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'		=>	'enable_quick_suppliers',
												'id'		=>	'enable_quick_suppliers',
												'value'		=>	'1',
												'checked'	=>	$this->config->item('enable_quick_suppliers')));?>
												<label for="enable_quick_suppliers"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_enable_quick_items')) ?>">	
											<?php echo form_label(lang('config_enable_quick_items').':', 'enable_quick_items',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'		=>	'enable_quick_items',
												'id'		=>	'enable_quick_items',
												'value'		=>	'1',
												'checked'	=>	$this->config->item('enable_quick_items')));?>
												<label for="enable_quick_items"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<label class='col-sm-3 col-md-3 col-lg-2 control-label' for="additional_appointment_note"><?php echo lang('config_additional_appointment_note'); ?>
												<br>
												<small>**<?php echo lang('common_bold');?>**, ~~<?php echo lang('common_italic');?>~~, ||<?php echo lang('common_underline');?>||</small>
												<br>
												<small style="border-bottom: 1px dotted #000000;text-decoration: none;cursor:pointer;" title="<?php echo implode("&#013;",$this->Appconfig->get_replaceable_keywords());?>" ><?php echo lang("config_keywords_help_text"); ?></small>
											</label>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_textarea(array(
												'name'=>'additional_appointment_note',
												'id'=>'additional_appointment_note',
												'class'=>'form-control text-area',
												'rows'=>'5',
												'cols'=>'30',
												'value'=>$this->config->item('additional_appointment_note')));?>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_do_not_delete_serial_number_when_selling').':', 'do_not_delete_serial_number_when_selling',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_delete_serial_number_when_selling',
												'id'=>'do_not_delete_serial_number_when_selling',
												'value'=>'1',
												'checked'=>$this->config->item('do_not_delete_serial_number_when_selling')));?>
												<label for="do_not_delete_serial_number_when_selling"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_enable_name_prefix').':', 'enable_name_prefix',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
												'name'=>'enable_name_prefix',
												'id'=>'enable_name_prefix',
												'value'=>'1',
												'checked'=>$this->config->item('enable_name_prefix')));?>
												<label for="enable_name_prefix"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
											<?php echo form_label(lang('config_change_to_recv_when_unsuspending_po').':', 'change_to_recv_when_unsuspending_po',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
												'name'=>'change_to_recv_when_unsuspending_po',
												'id'=>'change_to_recv_when_unsuspending_po',
												'value'=>'1',
												'checked'=>$this->config->item('change_to_recv_when_unsuspending_po')));?>
												<label for="change_to_recv_when_unsuspending_po"><span></span></label>
											</div>
										</div>
									</div>	
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Application Settings -->

				<!-- Email Settings -->
				<div class="tab-pane fade" id="email_settings">
					<div class="row">
						<div class="col-md-12 email_settings_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#email_settings_collapse">
									<?php echo create_section(lang("config_email_settings_info")) ?>
									</a>
								</div>
								<div id="email_settings_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
							
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">
											<?php echo form_label('Select A Provider'.':', 'email_provider',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php
													$provider_options = array('Use System Default'=>'Use System Default', 'Gmail API'=>'Gmail API', 'Gmail'=>'Gmail', 'Office 365'=>'Office 365', 'Windows Live Hotmail'=>'Windows Live Hotmail', 'Other'=>'Other');
													echo form_dropdown('email_provider', $provider_options, $this->config->item('email_provider'), 'id="email_provider" class="form-control"');
												?>
											</div>
										</div>

										<div class="email_gmail_api">
											<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">
												<div class="col-sm-3 col-md-3 col-lg-2"></div>
												<div class="col-sm-9 col-md-9 col-lg-10">
													
													<a href="javaascript:void(0)" id="gmail_api_authorize_button" style="<?php echo $this->config->item('gmail_api_token')?"display:none;":""; ?>">
														<img src="assets/img/btn_google_signin_dark_pressed_web.png">
													</a>
													<a id="gmail_api_signout_button" class="btn btn-lg btn-danger" style="border:0px; <?php echo $this->config->item('gmail_api_token')?"":"display:none;"; ?>">
														<?php echo lang("gmail_api_remove"); ?>
													</a>
												</div>
											</div>
										</div>

										<div class="email_basic">
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
											<?php echo form_label(lang('config_smtp_user').':', 'smtp_user',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'smtp_user',
												'id'=>'smtp_user',
												'placeholder' => 'username@domain.com',
												'value'=>$this->config->item('smtp_user')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
											<?php echo form_label(lang('config_smtp_pass').':', 'smtp_pass',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_password(array(
												'class'=>'form-control form-inps',
												'name'=>'smtp_pass',
												'id'=>'smtp_pass',
												'placeholder'=> 'password',
												'value'=>$this->config->item('smtp_pass')));?>
											</div>
										</div>
									</div>
									<div class="email_advanced">	
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
											<?php echo form_label(lang('config_smtp_crypto').':', 'smtp_crypto',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php
													$smtp_crypto_options = array(''=>'','ssl'=>'ssl','tls'=>'tls');
													echo form_dropdown('smtp_crypto', $smtp_crypto_options, $this->config->item('smtp_crypto'), 'id="smtp_crypto" class="form-control"');
												?>
											</div>
										</div>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">
											<?php echo form_label(lang('config_email_protocol').':', 'protocol',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php
													$protocol_options = array(''=>'','smtp'=>'smtp','mail'=>'mail','sendmail'=>'sendmail');
													echo form_dropdown('protocol', $protocol_options, $this->config->item('protocol'), 'id="protocol" class="form-control"');
												?>
											</div>
										</div>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
											<?php echo form_label(lang('config_smtp_host').':', 'smtp_host',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'smtp_host',
												'id'=>'smtp_host',
												'placeholder' => 'smtp.domain.com',
												'value'=>$this->config->item('smtp_host')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
											<?php echo form_label(lang('config_smtp_port').':', 'smtp_port',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'smtp_port',
												'id'=>'smtp_port',
												'placeholder'=>'25',
												'value'=>$this->config->item('smtp_port')));?>
											</div>
										</div>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
											<?php echo form_label(lang('config_email_charset').':', 'email_charset',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'email_charset',
												'id'=>'email_charset',
												'placeholder'=>'utf-8',
												'value'=>$this->config->item('email_charset')));?>
											</div>
										</div>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
											<?php echo form_label(lang('config_email_newline').':', 'newline',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php
													$newline_options = array('rn'=>'\r\n','n'=>'\n','r'=>'\r');
													$selected_option = 'rn';
													
													if ($option = $this->config->item('newline'))
													{
														if ($option == "\r\n")
														{
															$selected_option = 'rn';
														}
														elseif($option == "\n")
														{
															$selected_option = 'n';										
														}
														elseif($option == "\r")
														{
															$selected_option='r';
														}
													}
													
													echo form_dropdown('newline', $newline_options,$selected_option, 'id="newline" class="form-control"');
												?>
											</div>
										</div>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
											<?php echo form_label(lang('config_email_crlf').':', 'crlf',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php
													$crlf_options = array('rn'=>'\r\n','n'=>'\n','r'=>'\r');
													$selected_option = 'rn';
													
													if ($option = $this->config->item('crlf'))
													{
														if ($option == "\r\n")
														{
															$selected_option = 'rn';
														}
														elseif($option == "\n")
														{
															$selected_option = 'n';										
														}
														elseif($option == "\r")
														{
															$selected_option='r';
														}
													}
													
													echo form_dropdown('crlf', $crlf_options,$selected_option, 'id="crlf" class="form-control"');
												?>
											</div>
										</div>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">
											<?php echo form_label(lang('config_smtp_timeout').':', 'smtp_timeout',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'smtp_timeout',
												'id'=>'smtp_timeout',
												'placeholder'=>'5',
												'value'=>$this->config->item('smtp_timeout')));?>
											</div>
										</div>
									</div> <!-- end advanced email -->
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">
											<div class="col-sm-12 col-md-12 col-lg-12">
												<span class="pull-right">
										<button id="test_email" type="button" class="btn btn-lg btn-primary"><span id="test_email_icon" class="glyphicon glyphicon-envelope"></span> <?php echo lang('config_send_test_email');?></button>
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Email Settings -->

				<!-- SSO -->
				<div class="tab-pane fade" id="sso_info">
					<div class="row">
						<div class="col-md-12 company_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#sso_collapse">
									<?php echo create_section(lang("config_sso_info")) ?>
									</a>
								</div>
								<div id="sso_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
				
										<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
											<?php
											echo form_label(lang('config_sso_protocol').':', 'ecommerce_platform',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php
														echo form_dropdown('sso_protocol', array('saml'=> 'saml','oidc' => 'oidc'), $this->config->item('sso_protocol'),'id="sso_protocol" class="sso_protocol form-control"');
													?>          
												</div>
										</div>
										
										<div id="saml_config" style="display: none;">
											
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
												<?php echo form_label(lang('config_saml_single_sign_on_service').':', 'saml_single_sign_on_service',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saml_single_sign_on_service',
													'id'=>'saml_single_sign_on_service',
													'value'=>$this->config->item('saml_single_sign_on_service')));?>
												</div>
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
												<?php echo form_label(lang('config_saml_single_logout_service').':', 'saml_single_logout_service',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saml_single_logout_service',
													'id'=>'saml_single_logout_service',
													'value'=>$this->config->item('saml_single_logout_service')));?>
												</div>
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
											<?php echo form_label(lang('config_saml_x509_cert').':', 'saml_x509_cert',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_textarea(array(
													'name'=>'saml_x509_cert',
													'id'=>'saml_x509_cert',
													'class'=>'form-control text-area',
													'rows'=>'4',
													'cols'=>'30',
													'value'=>$this->config->item('saml_x509_cert')));?>
												</div>
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
												<?php echo form_label(lang('config_saml_idp_entity_id').':', 'saml_idp_entity_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saml_idp_entity_id',
													'id'=>'saml_idp_entity_id',
													'value'=>$this->config->item('saml_idp_entity_id')));?>
												</div>
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
												<?php echo form_label(lang('config_saml_name_id_format').':', 'saml_name_id_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saml_name_id_format',
													'id'=>'saml_name_id_format',
													'value'=>$this->config->item('saml_name_id_format')));?>
												</div>
											</div>
											
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
												<?php echo form_label(lang('config_saml_groups_field').':', 'saml_groups_field',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saml_groups_field',
													'id'=>'saml_groups_field',
													'value'=>$this->config->item('saml_groups_field')));?>
												</div>
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
												<?php echo form_label(lang('config_saml_locations_field').':', 'saml_locations_field',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saml_locations_field',
													'id'=>'saml_locations_field',
													'value'=>$this->config->item('saml_locations_field')));?>
												</div>
											</div>
											
											
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
												<?php echo form_label(lang('config_saml_first_name_field').':', 'saml_first_name_field',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saml_first_name_field',
													'id'=>'saml_first_name_field',
													'value'=>$this->config->item('saml_first_name_field')));?>
												</div>
											</div>
											
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
												<?php echo form_label(lang('config_saml_last_name_field').':', 'saml_last_name_field',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saml_last_name_field',
													'id'=>'saml_last_name_field',
													'value'=>$this->config->item('saml_last_name_field')));?>
												</div>
											</div>
											
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
												<?php echo form_label(lang('config_saml_email_field').':', 'saml_email_field',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'saml_email_field',
													'id'=>'saml_email_field',
													'value'=>$this->config->item('saml_email_field')));?>
												</div>
											</div>
											
										</div>


										<div id="oidc_config" style="display: none;">
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
											<?php echo form_label(lang('config_oidc_host').':', 'oidc_host',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'name'=>'oidc_host',
													'id'=>'oidc_host',
													'class'=>'form-control form-inps',
													'value'=>$this->config->item('oidc_host')));?>
												</div>
											</div>
											
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
											<?php echo form_label(lang('config_oidc_client_id').':', 'oidc_client_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'name'=>'oidc_client_id',
													'id'=>'oidc_client_id',
													'class'=>'form-control form-inps',
													'value'=>$this->config->item('oidc_client_id')));?>
												</div>
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
											<?php echo form_label(lang('config_oidc_secret').':', 'oidc_secret',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'name'=>'oidc_secret',
													'type' => 'password',
													'id'=>'oidc_secret',
													'class'=>'form-control form-inps',
													'value'=>$this->config->item('oidc_secret')));?>
												</div>
											</div>
											
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
											<?php echo form_label(lang('config_oidc_cert_url').':', 'oidc_cert_url',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'name'=>'oidc_cert_url',
													'id'=>'oidc_cert_url',
													'class'=>'form-control form-inps',
													'value'=>$this->config->item('oidc_cert_url')));?>
												</div>
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
												<?php echo form_label(lang('config_oidc_additional_scopes').':', 'oidc_additional_scopes',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'oidc_additional_scopes',
													'id'=>'oidc_additional_scopes',
													'value'=>$this->config->item('oidc_additional_scopes')));?>
												</div>
											</div>
											
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
												<?php echo form_label(lang('config_oidc_username_field').':', 'oidc_username_field',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'oidc_username_field',
													'id'=>'oidc_username_field',
													'value'=>$this->config->item('oidc_username_field')));?>
												</div>
											</div>
											
											
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
												<?php echo form_label(lang('config_oidc_groups_field').':', 'oidc_groups_field',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'oidc_groups_field',
													'id'=>'oidc_groups_field',
													'value'=>$this->config->item('oidc_groups_field')));?>
												</div>
											</div>
											
											<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
												<?php echo form_label(lang('config_oidc_locations_field').':', 'oidc_locations_field',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'oidc_locations_field',
													'id'=>'oidc_locations_field',
													'value'=>$this->config->item('oidc_locations_field')));?>
												</div>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_sso_info')) ?>">	
										<?php echo form_label(lang('config_only_allow_sso_logins').':', 'only_allow_sso_logins',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'only_allow_sso_logins',
												'id'=>'only_allow_sso_logins',
												'value'=>'1',
												'checked'=>$this->config->item('only_allow_sso_logins')));?>
												<label for="only_allow_sso_logins"><span></span></label>
											</div>
										</div>
										
										<script>
											function sso_protocol_check()
											{
												if ($("#sso_protocol").val() == 'saml')
												{
													$("#saml_config").show();
													$("#oidc_config").hide();
												}
												else
												{
													$("#saml_config").hide();
													$("#oidc_config").show();
												}
											}
											$("#sso_protocol").change(sso_protocol_check);
											sso_protocol_check();
											
										</script>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End SSO -->

				<!-- QB Settings -->
				<div class="tab-pane fade" id="qb_settings">
					<div class="row">
						<div class="col-md-12 quickbooks_settings">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#qb_settings_collapse">
									<?php echo create_section(lang("config_quickbooks_settings")) ?>
									</a>
								</div>
								<div id="qb_settings_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
							
										<div class="text-center">
											<?php if ($this->config->item('quickbooks_access_token') && $this->config->item('quickbooks_access_token')){ ?>
												<a href="<?php echo site_url('quickbooks/refresh_tokens/1');?>" class="btn btn-primary"><?php echo lang('config_refresh_tokens'); ?></a>
												<br />
												<br />
												<a href="<?php echo site_url('quickbooks/oauth');?>" class="btn btn-primary"><?php echo lang('config_reconnect_quickbooks'); ?></a>
												<br />
												<br />
												<button id="reset_quickbooks" type="button" class="btn btn-lg btn-danger"> <?php echo lang('config_reset_quickbooks');?></button>
												<br />
												<br />
												
												<?php } else { ?>
												<a href="<?php echo site_url('quickbooks/oauth');?>" class="btn btn-primary"><?php echo lang('config_connect_to_qb_online'); ?></a>
											<?php } ?>
											<br />
											<br />
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('common_quickbooks')) ?>">  
											<?php echo form_label(lang('config_qb_sync_operations').':', 'qb_sync_operations',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10 qb_sync_operations">
													<ul id="check-list-box" data-name="qb_sync_operations[]" class="list-group checked-list-box">
																										<li class="list-group-item" data-value="export_journalentry_to_quickbooks" data-color="success"><?php echo lang('config_export_journalentry_to_quickbooks'); ?></li>
													</ul>
												</div>
										</div>
											
										<div class="form-group" data-keyword="<?php echo H(lang('common_quickbooks')) ?>">  
											<?php
																echo form_label(lang('config_qb_sync_logs').':', 'qb_sync_logs',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
																<div class="col-sm-9 col-md-9 col-lg-10">
																		<ul>
																		<?php
																		foreach($this->Appfile->get_files_with_name('quickbooks_log.txt') as $file) 
																		{
																				echo '<li>'.anchor($this->Appfile->get_url_for_file($file['file_id']),date(get_date_format().' '.get_time_format(), strtotime($file['timestamp'])),array('target' => '_blank')).'</li>';
																		} 
																		?>           
																</ul>
																</div>
										</div>
										
										<div id="quickbooks_sync_progress" class="form-group hidden" data-keyword="<?php echo H(lang('common_quickbooks')) ?>"> 
												<?php echo form_label(lang('config_quickbooks_progress').':', 'quickbooks_progress',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
														<div class="col-sm-9 col-md-9 col-lg-10">
																<div class="well well-sm">
																		<div class="progress">
																			<div class="progress-bar progress-bar-striped active" id="quickbooks_progessbar" role="progressbar"
																			aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
																				<span id="quickbooks_progress_percent">0</span>% <span id="quickbooks_progress_message"></span>
																			</div>
																		</div>
																</div>
														</div>
										</div>
										<div class="form-group" data-keyword="<?php echo H(lang('common_quickbooks')) ?>">
											<?php echo form_label(lang('qb_export_start_date').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
														<div class="input-group">
															<?php echo form_input(array(
																'name'=>'export_start_date',
																'id'=>'export_start_date',
																'class'=>'form-control datepicker',
																'placeholder' => 'YYYY-MM-DD',
																'value'=>$this->config->item('qb_export_start_date')
														));?>
														</div>
											</div>
										</div>
										<div class="form-group" data-keyword="<?php echo H(lang('common_quickbooks')) ?>">
											<?php echo form_label(lang('config_last_sync_date').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<div class="input-group">
													<input readonly type="text" class="form-control form-inps" placeholder="<?php echo lang('config_last_qb_sync_date'); ?>" name="qb_sync_date" id="qb_sync_date" value="<?php echo $this->config->item('last_qb_sync_date') ?  date(get_date_format().' '.get_time_format(),strtotime($this->config->item('last_qb_sync_date'))) : ''; ?>" aria-describedby="input-group-btn">
															<span class = "input-group-btn">
																	<button id="sync_qb" type="button" class="btn btn-lg  btn-warning"><span id="sync_qb_button_icon" class="glyphicon glyphicon-refresh"></span> <?php echo lang('config_sync');?></button>
																								</span>
																						
																								<span class = "input-group-btn hidden" id="qb-cancel-button">
																	<button id="cancel_qb" type="button" class="btn btn-lg btn-danger"> <?php echo lang('common_cancel');?></button>
															</span>
													
													</div>              
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End QB Settings -->

				<!-- Ecommerce Store -->
				<div class="tab-pane fade" id="ecommerce_store">
					<div class="row">
						<div class="col-md-12 ecommerce_settings_info">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#ecommerce_settings_collapse">
									<?php echo create_section(lang("config_ecommerce_settings_info")) ?>
									</a>
								</div>
								<div id="ecommerce_settings_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">	
											<?php
											echo form_label(lang('config_ecommerce_platform').':', 'ecommerce_platform',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php
													echo form_dropdown('ecommerce_platform', $ecommerce_platforms, $this->config->item('ecommerce_platform'),'id="ecommerce_platform" class="ecommerce_platform form-control"');
												?>          
											</div>
										</div>
									
										<div class="form-group" data-keyword="<?php echo H(lang('config_ecommerce_settings_info')) ?>">	
										<?php echo form_label(lang('config_ecommerce_realtime').':', 'ecommerce_realtime',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'ecommerce_realtime',
												'id'=>'ecommerce_realtime',
												'value'=>'1',
												'checked'=>$this->config->item('ecommerce_realtime')));?>
												<label for="ecommerce_realtime"><span></span></label>
											</div>
										</div>
									
									
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
											<?php echo form_label(lang('config_sku_sync_field').':', 'sku_sync_field',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_dropdown('sku_sync_field', array(
												'item_number'  => lang('common_item_number_expanded'),
												'product_id'    => lang('common_product_id'),
												'item_id'   => lang('common_item_id')
												),
												$this->config->item('sku_sync_field') ? $this->config->item('sku_sync_field') : 'item_number', 'class="form-control" id="sku_sync_field"')
												?>
											</div>
										</div>
									
													
										<div class="form-group" data-keyword="<?php echo H(lang('config_ecommerce_settings_info')) ?>">	
										<?php echo form_label(lang('config_do_not_upload_images_to_ecommerce').':', 'do_not_upload_images_to_ecommerce',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_upload_images_to_ecommerce',
												'id'=>'do_not_upload_images_to_ecommerce',
												'value'=>'1',
												'checked'=>$this->config->item('do_not_upload_images_to_ecommerce')));?>
												<label for="do_not_upload_images_to_ecommerce"><span></span></label>
											</div>
										</div>
											
										<div class="form-group" data-keyword="<?php echo H(lang('config_ecommerce_settings_info')) ?>">	
										<?php echo form_label(lang('config_ecommerce_only_sync_completed_orders').':', 'ecommerce_only_sync_completed_orders',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'ecommerce_only_sync_completed_orders',
												'id'=>'ecommerce_only_sync_completed_orders',
												'value'=>'1',
												'checked'=>$this->config->item('ecommerce_only_sync_completed_orders')));?>
												<label for="ecommerce_only_sync_completed_orders"><span></span></label>
											</div>
										</div>
											
										<div class="form-group" data-keyword="<?php echo H(lang('config_ecommerce_settings_info')) ?>">	
										<?php echo form_label(lang('config_import_ecommerce_orders_suspended').':', 'import_ecommerce_orders_suspended',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'import_ecommerce_orders_suspended',
												'id'=>'import_ecommerce_orders_suspended',
												'value'=>'1',
												'checked'=>$this->config->item('import_ecommerce_orders_suspended')));?>
												<label for="import_ecommerce_orders_suspended"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_ecommerce_settings_info')) ?>">	
										<?php echo form_label(lang('config_new_items_are_ecommerce_by_default').':', 'new_items_are_ecommerce_by_default',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'new_items_are_ecommerce_by_default',
												'id'=>'new_items_are_ecommerce_by_default',
												'value'=>'1',
												'checked'=>$this->config->item('new_items_are_ecommerce_by_default')));?>
												<label for="new_items_are_ecommerce_by_default"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_ecommerce_settings_info')) ?>">	
										<?php echo form_label(lang('config_use_main_image_as_default_image_in_e_commerce').':', 'use_main_image_as_default_image_in_e_commerce',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'use_main_image_as_default_image_in_e_commerce',
												'id'=>'use_main_image_as_default_image_in_e_commerce',
												'value'=>'1',
												'checked'=>$this->config->item('use_main_image_as_default_image_in_e_commerce')));?>
												<label for="use_main_image_as_default_image_in_e_commerce"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">	
											<?php
												echo form_label(lang('config_store_location').':', 'ecom_store_location',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php
														echo form_dropdown('ecom_store_location', $store_locations, $this->config->item('ecom_store_location'), 'class="form-control"');
													?>           
												</div>
										</div>
											
											
										<?php						
										foreach($store_locations as $r_location_id=>$r_location_name)
										{
										?>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_sync_inventory_from_location').' '.$r_location_name, "location_".$r_location_id,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'ecommerce_locations[]',
												'id'=>"location_".$r_location_id,
												'value'=>$r_location_id,
												'checked'=>isset($ecommerce_locations[$r_location_id])))?>
												<label for="location_<?php echo $r_location_id;?>"><span></span></label>
											</div>
										</div>
										
										<?php	
										}
										?>
											
										<?php if(count($online_price_tiers) > 1) { ?>
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">	
											<?php
												echo form_label(lang('config_online_price_tier').':', 'online_price_tier',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php 
														echo form_dropdown('online_price_tier', $online_price_tiers, $this->config->item('online_price_tier'), 'class="form-control"');
													?>           
												</div>
										</div>
										<?php } ?>
																	
											
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">	
											<?php echo form_label(lang('config_ecommerce_cron_sync_operations').':', 'ecommerce_cron_sync_operations',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10 ecommerce_cron_sync_operations">
													<ul id="check-list-box" data-name="ecommerce_cron_sync_operations[]" class="list-group checked-list-box">
														<li class="list-group-item" data-value="sync_inventory_changes" data-color="success"><?php echo lang('config_sync_inventory_changes'); ?></li>
														<li class="list-group-item woo-only" data-value="import_ecommerce_tags_into_phppos" data-color="success"><?php echo lang('config_import_ecommerce_tags_into_phppos'); ?></li>
														<li class="list-group-item woo-only" data-value="import_ecommerce_categories_into_phppos" data-color="success"><?php echo lang('config_import_ecommerce_categories_into_phppos'); ?></li>
														<li class="list-group-item woo-only" data-value="import_ecommerce_attributes_into_phppos" data-color="success"><?php echo lang('config_import_ecommerce_attributes_into_phppos'); ?></li>
														<li class="list-group-item woo-only" data-value="import_tax_classes_into_phppos" data-color="success"><?php echo lang('config_import_tax_classes_into_phppos'); ?></li>
														<li class="list-group-item woo-only" data-value="import_shipping_classes_into_phppos" data-color="success"><?php echo lang('config_import_shipping_classes_into_phppos'); ?></li>
														<li class="list-group-item" data-value="import_ecommerce_items_into_phppos" data-color="success"><?php echo lang('config_import_ecommerce_items_into_phppos'); ?></li>
														<li class="list-group-item" data-value="import_ecommerce_orders_into_phppos" data-color="success"><?php echo lang('config_import_ecommerce_orders_into_phppos'); ?></li>
														<li class="list-group-item woo-only" data-value="export_phppos_tags_to_ecommerce" data-color="success"><?php echo lang('config_export_phppos_tags_to_ecommerce'); ?></li>
														<li class="list-group-item" data-value="export_phppos_categories_to_ecommerce" data-color="success"><?php echo lang('config_export_phppos_categories_to_ecommerce'); ?></li>
														<li class="list-group-item woo-only" data-value="export_phppos_attributes_to_ecommerce" data-color="success"><?php echo lang('config_export_phppos_attributes_to_ecommerce'); ?></li>
														<li class="list-group-item woo-only" data-value="export_tax_classes_into_phppos" data-color="success"><?php echo lang('config_export_tax_classes_into_phppos'); ?></li>
														<li class="list-group-item" data-value="export_phppos_items_to_ecommerce" data-color="success"><?php echo lang('config_export_phppos_items_to_ecommerce'); ?></li>
													</ul>
												</div>
										</div>
											
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">	
											<?php
											echo form_label(lang('config_ecom_sync_logs').':', 'ecom_sync_logs',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<ul>
												<?php
												foreach($this->Appfile->get_files_with_name('ecom_log.txt') as $file) 
												{
													echo '<li>'.anchor($this->Appfile->get_url_for_file($file['file_id']),date(get_date_format().' '.get_time_format(), strtotime($file['timestamp'])),array('target' => '_blank')).'</li>';
												} 
												?>           
												</ul>
											</div>
										</div>
											
										<div id="ecommerce_sync_progress" class="form-group hidden" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">	
											<?php echo form_label(lang('config_ecommerce_progress').':', 'ecommerce_progress',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<div class="well well-sm">
													<div class="progress">
														<div class="progress-bar progress-bar-striped active" id="ecommerce_progessbar" role="progressbar"
														aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
														<span id="ecommerce_progress_percent">0</span>% <span id="ecommerce_progress_message"></span>
														</div>
													</div>
												</div>
											</div>
										</div>
											
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">
											<?php echo form_label(lang('config_last_sync_date').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
												<div class="input-group">
													<input readonly type="text" class="form-control form-inps" placeholder="<?php echo lang('config_last_sync_date'); ?>" name="ecommerce_sync_date" id="ecommerce_sync_date" value="<?php echo $this->config->item('last_ecommerce_sync_date') ?  date(get_date_format().' '.get_time_format(),strtotime($this->config->item('last_ecommerce_sync_date'))) : ''; ?>" aria-describedby="input-group-btn">
													<span class = "input-group-btn">
													<button id="sync_woo" type="button" class="btn btn-lg  btn-warning"><span id="sync_woo_button_icon" class="glyphicon glyphicon-refresh"></span> <?php echo lang('config_sync');?></button>
													</span>
													
													<span class = "input-group-btn hidden" id="ecommerce-cancel-button">
													<button id="cancel_woo" type="button" class="btn btn-lg btn-danger"> <?php echo lang('common_cancel');?></button>
													</span>
													
												</div>				
											</div>
										</div>
											
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">	
											<?php
											echo form_label(lang('config_reset_ecommerce').':', 'reset_ecommerce',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<button id="reset_ecommerce" type="button" class="btn btn-lg btn-danger"> <?php echo lang('config_reset_ecommerce');?></button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Ecommerce Store -->

				<!-- Shopify Settings -->
				<div class="tab-pane fade shopify_settings" id="shopify_settings">
					<div class="row">
						<div class="col-md-12 ecom_settings">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<?php echo create_section(lang("config_shopify_settings_info")) ?>
								</div>
								<div class="panel-body">
									<?php if (!is_on_phppos_host()) { ?>
									<?php
									if (!$this->config->item('shopify_public') || !$this->config->item('shopify_private'))
									{
									?>
									<h3 style="text-align: center;"><?php echo 'E-mail <a href="mailto:support@phppointofsale.com">support@phppointofsale.com</a> to obtain these values'?></h3>
									
									<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_woocommerce')) ?>">	
										<?php echo form_label(lang('shopify_public_key').':', 'shopify_public',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
										<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_input(array(
											'class'=>'form-control form-inps',
											'name'=>'shopify_public',
											'id'=>'shopify_public',
											'value'=>$this->config->item('shopify_public')));?>
										</div>
									</div>
									
									<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_woocommerce')) ?>">	
										<?php echo form_label(lang('shopify_private_key').':', 'shopify_private',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
										<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_input(array(
											'class'=>'form-control form-inps',
											'name'=>'shopify_private',
											'id'=>'shopify_private',
											'value'=>$this->config->item('shopify_private')));?>
										</div>
									</div>
									<?php
									}}
					
									echo form_hidden('shopify_shop',$this->config->item('shopify_shop'));
									
									if ($this->config->item('shopify_shop'))
									{
									?>
										<div class='text-center'>
											<p><?php echo lang('config_connected_to_shopify')?> [<strong><?php echo $this->config->item('shopify_shop').'.myshopify.com' ?></strong>]</p>
											<br />
											<br />
											<?php
											if ($this->config->item('shopify_charge_id'))
											{
											?>
											
											<br />
											<br />
											
											<?php
											if ($this->config->item('shopify_code'))
											{
											?>
											<a href="<?php echo site_url('ecommerce/oauth_shopify_disconnect/0');?>" class="btn btn-danger" id="shopify_oauth_disconnect"><?php echo lang('config_disconnect_to_shopify'); ?></a>
											<?php
											}
											else
											{
											?>
											<a href="<?php echo site_url('ecommerce/oauth_shopify');?>" class="btn btn-success" id="shopify_oauth_connectt"><?php echo lang('config_reconnect_to_shopify'); ?></a>
											<?php
											}
											?>
											
											<br />
											<br />
											
												<a href="<?php echo site_url('ecommerce/cancel_shopify_billing');?>" class="btn btn-danger" id="shopify_cancel_billing"><?php echo lang('config_cancel_shopify'); ?></a>
												<script>
												$("#shopify_cancel_billing").click(function(e)
												{
													e.preventDefault();
													
													bootbox.confirm(<?php echo json_encode(lang('config_confirm_cancel_shopify')); ?>, function(response)
													{
														if (response)
														{
															window.location = $("#shopify_cancel_billing").attr('href');
														}
													});
													
												})
												</script>
											<?php	
											}
											else
											{
											?>
											<a href="<?php echo site_url('ecommerce/activate_shopify_billing');?>" class="btn btn-success" id="shopify_activate_billing"><?php echo str_replace('{SHOPIFY_PRICE}',SHOPIFY_PRICE,lang('config_shopify_billing_terms')); ?></a>
											<br /><br />
											<a href="<?php echo site_url('ecommerce/oauth_shopify_disconnect');?>" class="btn btn-danger" id="shopify_oauth_disconnect"><?php echo lang('config_disconnect_to_shopify'); ?></a>
											
											<?php
											}
											?>
											
										</div>
											
										<?php
										}
										else
										{
											?>
											<div class='text-center'>
											<?php
											if ($this->config->item('shopify_charge_id'))
											{
											?>
											
											<br /><br />
											<a href="<?php echo site_url('ecommerce/oauth_shopify');?>" class="btn btn-success" id="shopify_oauth_connectt"><?php echo lang('config_reconnect_to_shopify'); ?></a>
											<br /><br />
											<?php
											}
										?>
											<p><a href="https://apps.shopify.com/php-point-of-sale" target="_blank"><?php echo lang('config_connect_shopify_in_app_store')?></a></p>
											<br />
											<br />
										</div>
											
										<?php
										}
										?>	
										<!-- </div> -->
										
										<script>
											$("#shopify_oauth").click(function(e)
											{
												e.preventDefault();
												var url = $(th_pis).attr('href');
												$("#config_form").ajaxSubmit(function()
												{
													window.location = url;
												});
											});
											
										</script>
								</div>	
							</div>
						</div>
					</div>
				</div>
				<!-- End Shopify Settings -->

				<!-- Woo Settings -->
				<div class="tab-pane fade woo_settings" id="woo_settings">
					<div class="row">
						<div class="col-md-12 ecom_settings">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#woo_settings_collapse">
									<?php echo create_section(lang("config_woocommerce_settings_info")) ?>
									</a>
								</div>
								<div id="woo_settings_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_woocommerce')) ?>">	
												<?php echo form_label(lang('config_woo_version').':', 'woo_version',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php
														echo form_dropdown('woo_version', $woo_versions, $this->config->item('woo_version'),'id="woo_version" class="woo_version form-control"');
													?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_woocommerce')) ?>">	
												<?php echo form_label(lang('config_woo_api_url').':', 'woo_api_url',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
												<div class="col-sm-9 col-md-9 col-lg-10">
													<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'woo_api_url',
													'id'=>'woo_api_url',
													'value'=>$this->config->item('woo_api_url')));?>
												</div>
										</div>

										<div class="form-group text-right" data-keyword="<?php echo H(lang('config_keyword_woocommerce')) ?>">
											<button id="woo_oauth" type="button" class="btn btn-lg btn-primary" style="margin-right: 15px;"><?php echo lang('config_connect_to_woocommerce'); ?></button>
										</div>
																
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_import_all_past_orders_for_woo_commerce').':', 'import_all_past_orders_for_woo_commerce',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'import_allast_orders_for_woo_commerce',
												'id'=>'import_all_past_orders_for_woo_commerce',
												'value'=>'1',
												'checked'=>$this->config->item('import_all_past_orders_for_woo_commerce')));?>
												<label for="import_all_past_orders_for_woo_commerce"><span></span></label>
											</div>
										</div>
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_woo_enable_html_desc').':', 'woo_enable_html_desc',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'woo_enable_html_desc',
												'id'=>'woo_enable_html_desc',
												'value'=>'1',
												'checked'=>$this->config->item('woo_enable_html_desc')));?>
												<label for="woo_enable_html_desc"><span></span></label>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
										<?php echo form_label(lang('config_do_not_treat_service_items_as_virtual').':', 'do_not_treat_service_items_as_virtual',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'do_not_treat_service_items_as_virtual',
												'id'=>'do_not_treat_service_items_as_virtual',
												'value'=>'1',
												'checked'=>$this->config->item('do_not_treat_service_items_as_virtual')));?>
												<label for="do_not_treat_service_items_as_virtual"><span></span></label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Woo Settings -->

				<!-- Api Settings -->
				<div class="tab-pane fade" id="api_settings">
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#api_settings_collapse">
										<?php echo create_section(lang('config_api_settings_info'), 'store-configuration-options', 'section-api-settings')  ?>
									</a>
								</div>
								<div id="api_settings_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<div class="form-group no-padding-right" data-keyword="<?php echo H(lang('config_keyword_api')) ?>">	
										<?php echo form_label(lang('config_api_keys').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-md-9 col-sm-9 col-lg-10">
												<div class="table-responsive">
													<table id="api_keys" class="table">
														<thead>
															<tr>
															<th><?php echo lang('common_description'); ?></th>
															<th><?php echo lang('config_api_key_ending_in'); ?></th>
															<th><?php echo lang('config_permissions'); ?></th>
															<th><?php echo lang('common_delete'); ?></th>
															</tr>
														</thead>
												
														<tbody id="api_keys_body">
														<?php foreach($api_keys as $key) { ?>
															<tr>
																<td><?php echo $key->description;?></td>
																<td>...<?php echo $key->key_ending; ?></td>
																<td>
																	<?php	echo  $key->level == 1 ? lang('config_read') : lang('config_read_write'); ?>
																</td>
																<td><a class="delete_api_key" href="javascript:void(0);" data-key-id='<?php echo $key->id; ?>'><?php echo lang('common_delete'); ?></a></td>
														</tr>
														<?php } ?>
														</tbody>
													</table>
											
													<a tabindex="-1" href="<?php echo site_url('config/add_api_key');?>" data-toggle="modal" data-target="#myModal" data-toggle="model"><?php echo lang('config_add_key'); ?></a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Api Settings -->

				<!-- Web Hooks -->
				<div class="tab-pane fade" id="web_hooks">
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#web_hooks_collapse">
									<?php echo create_section(lang('config_webhooks'), 'store-configuration-options', 'section-webhooks-settings')  ?>
									</a>
								</div>
								<div id="web_hooks_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_new_customer_web_hook').':', 'new_customer_web_hook',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'new_customer_web_hook',
												'id'=>'new_customer_web_hook',
												'placeholder' => 'http://URL',
												'value'=>$this->config->item('new_customer_web_hook')));?>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_new_sale_web_hook').':', 'new_sale_web_hook',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'new_sale_web_hook',
												'id'=>'new_sale_web_hook',
												'placeholder' => 'http://URL',
												'value'=>$this->config->item('new_sale_web_hook')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_new_receiving_web_hook').':', 'new_receiving_web_hook',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'new_receiving_web_hook',
												'id'=>'new_receiving_web_hook',
												'placeholder' => 'http://URL',
												'value'=>$this->config->item('new_receiving_web_hook')));?>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_new_item_web_hook').':', 'new_item_web_hook',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'new_item_web_hook',
												'id'=>'new_item_web_hook',
												'placeholder' => 'http://URL',
												'value'=>$this->config->item('new_item_web_hook')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_new_work_order_web_hook').':', 'new_work_order_web_hook',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'new_work_order_web_hook',
												'id'=>'new_work_order_web_hook',
												'placeholder' => 'http://URL',
												'value'=>$this->config->item('new_work_order_web_hook')));?>
											</div>
										</div>
										
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_edit_customer_web_hook').':', 'edit_customer_web_hook',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'edit_customer_web_hook',
												'id'=>'edit_customer_web_hook',
												'placeholder' => 'http://URL',
												'value'=>$this->config->item('edit_customer_web_hook')));?>
											</div>
										</div>
										
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_edit_sale_web_hook').':', 'edit_sale_web_hook',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'edit_sale_web_hook',
												'id'=>'edit_sale_web_hook',
												'placeholder' => 'http://URL',
												'value'=>$this->config->item('edit_sale_web_hook')));?>
											</div>
										</div>


										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_edit_recv_web_hook').':', 'edit_recv_web_hook',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'edit_recv_web_hook',
												'id'=>'edit_recv_web_hook',
												'placeholder' => 'http://URL',
												'value'=>$this->config->item('edit_recv_web_hook')));?>
											</div>
										</div>
													
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_edit_item_web_hook').':', 'edit_item_web_hook',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'edit_item_web_hook',
												'id'=>'edit_item_web_hook',
												'placeholder' => 'http://URL',
												'value'=>$this->config->item('edit_item_web_hook')));?>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
											<?php echo form_label(lang('config_edit_work_order_web_hook').':', 'edit_work_order_web_hook',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'edit_work_order_web_hook',
												'id'=>'edit_work_order_web_hook',
												'placeholder' => 'http://URL',
												'value'=>$this->config->item('edit_work_order_web_hook')));?>
											</div>
										</div>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Web Hooks -->

				<!-- Work Order -->
				<div class="tab-pane fade" id="work_order">
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#work_order_collapse">
										<?php echo lang('config_work_order'); ?>
									</a>
								</div>
								<div id="work_order_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										<div class="form-group" data-keyword="<?php echo H(lang('config_work_order_notes_internal')) ?>">
											<?php echo form_label(lang('config_work_order_notes_internal').':', 'work_order_notes_internal',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'work_order_notes_internal',
													'id'=>'work_order_notes_internal',
													'value'=>'work_order_notes_internal',
													'checked'=>$this->config->item('work_order_notes_internal')));?>
												<label for="work_order_notes_internal"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_show_prices_on_work_orders')) ?>">
											<?php echo form_label(lang('config_show_prices_on_work_orders').':', 'show_prices_on_work_orders',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'show_prices_on_work_orders',
													'id'=>'show_prices_on_work_orders',
													'value'=>'show_prices_on_work_orders',
													'checked'=>$this->config->item('show_prices_on_work_orders')));?>
												<label for="show_prices_on_work_orders"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_work_repair_item_taxable')) ?>">
											<?php echo form_label(lang('config_work_repair_item_taxable').':', 'work_repair_item_taxable',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'work_repair_item_taxable',
													'id'=>'work_repair_item_taxable',
													'value'=>'work_repair_item_taxable',
													'checked'=>$this->config->item('work_repair_item_taxable')));?>
												<label for="work_repair_item_taxable"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_work_order_notes_internal')) ?>">	
											<?php echo form_label(lang('config_work_order_device_locations').':', 'work_order_device_locations',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
													'class'=>'form-control form-inps',
													'name'=>'work_order_device_locations',
													'id'=>'work_order_device_locations',
													'size'=> 40,
													'value'=>$this->config->item('work_order_device_locations')));?>
											</div>
										</div>
													
										<div class="form-group" data-keyword="<?php echo H(lang('config_work_order_notes_internal')) ?>">	
											<?php echo form_label(lang('config_default_workorder_tech_is_logged_employee').':', 'default_tech_is_logged_employee',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">

												<?php echo form_checkbox(array(
													'name' => 'default_tech_is_logged_employee',
													'id' => 'default_tech_is_logged_employee',
													'value' => 'default_tech_is_logged_employee',
													'checked' => $this->config->item('default_tech_is_logged_employee'))); ?>
												<label for="default_tech_is_logged_employee"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_hide_repair_items_in_sales_interface')) ?>">
											<?php echo form_label(lang('config_hide_repair_items_in_sales_interface').':', 'hide_repair_items_in_sales_interface',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'hide_repair_items_in_sales_interface',
													'id'=>'hide_repair_items_in_sales_interface',
													'value'=>'hide_repair_items_in_sales_interface',
													'checked'=>$this->config->item('hide_repair_items_in_sales_interface')));?>
												<label for="hide_repair_items_in_sales_interface"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_hide_repair_items_on_receipt')) ?>">
											<?php echo form_label(lang('config_hide_repair_items_on_receipt').':', 'hide_repair_items_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'hide_repair_items_on_receipt',
													'id'=>'hide_repair_items_on_receipt',
													'value'=>'hide_repair_items_on_receipt',
													'checked'=>$this->config->item('hide_repair_items_on_receipt')));?>
												<label for="hide_repair_items_on_receipt"><span></span></label>
											</div>
										</div>
										

										<div class="form-group" data-keyword="<?php echo H(lang('config_work_orders_show_condensed_receipt')) ?>">
											<?php echo form_label(lang('config_work_orders_show_condensed_receipt').':', 'work_orders_show_condensed_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name'=>'work_orders_show_condensed_receipt',
													'id'=>'work_orders_show_condensed_receipt',
													'value'=>'work_orders_show_condensed_receipt',
													'checked'=>$this->config->item('work_orders_show_condensed_receipt')));?>
												<label for="work_orders_show_condensed_receipt"><span></span></label>
											</div>
										</div>
										
										

										<div class="form-group" data-keyword="<?php echo H(lang('config_work_order_notes_internal')) ?>">	
											<?php echo form_label(lang('config_show_item_description_service_tag').':', 'show_item_description_service_tag',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name' 		=> 'show_item_description_service_tag',
													'id' 		=> 'show_item_description_service_tag',
													'value' 	=> 'show_item_description_service_tag',
													'checked' 	=> $this->config->item('show_item_description_service_tag'))); ?>
												<label for="show_item_description_service_tag"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_work_order_notes_internal')) ?>">	
											<?php echo form_label(lang('config_show_phone_number_service_tag').':', 'show_phone_number_service_tag',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name' 		=> 'show_phone_number_service_tag',
													'id' 		=> 'show_phone_number_service_tag',
													'value' 	=> 'show_phone_number_service_tag',
													'checked' 	=> $this->config->item('show_phone_number_service_tag'))); ?>
												<label for="show_phone_number_service_tag"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_change_work_order_status_from_sales')) ?>">	
											<?php echo form_label(lang('config_change_work_order_status_from_sales').':', 'change_work_order_status_from_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name' 		=> 'change_work_order_status_from_sales',
													'id' 		=> 'change_work_order_status_from_sales',
													'value' 	=> 'change_work_order_status_from_sales',
													'checked' 	=> $this->config->item('change_work_order_status_from_sales'))); ?>
												<label for="change_work_order_status_from_sales"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_show_custom_fields_service_tag_work_orders')) ?>">	
											<?php echo form_label(lang('config_show_custom_fields_service_tag_work_orders').':', 'show_custom_fields_service_tag_work_orders',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name' 		=> 'show_custom_fields_service_tag_work_orders',
													'id' 		=> 'show_custom_fields_service_tag_work_orders',
													'value' 	=> 'show_custom_fields_service_tag_work_orders',
													'checked' 	=> $this->config->item('show_custom_fields_service_tag_work_orders'))); ?>
												<label for="show_custom_fields_service_tag_work_orders"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_show_custom_fields_label_service_tag_work_orders')) ?>">	
											<?php echo form_label(lang('config_show_custom_fields_label_service_tag_work_orders').':', 'show_custom_fields_label_service_tag_work_orders',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name' 		=> 'show_custom_fields_label_service_tag_work_orders',
													'id' 		=> 'show_custom_fields_label_service_tag_work_orders',
													'value' 	=> 'show_custom_fields_label_service_tag_work_orders',
													'checked' 	=> $this->config->item('show_custom_fields_label_service_tag_work_orders'))); ?>
												<label for="show_custom_fields_label_service_tag_work_orders"><span></span></label>
											</div>
										</div>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_show_estimated_repair_date_on_service_tag_work_orders')) ?>">	
											<?php echo form_label(lang('config_show_estimated_repair_date_on_service_tag_work_orders').':', 'show_estimated_repair_date_on_service_tag_work_orders',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_checkbox(array(
													'name' 		=> 'show_estimated_repair_date_on_service_tag_work_orders',
													'id' 		=> 'show_estimated_repair_date_on_service_tag_work_orders',
													'value' 	=> 'show_estimated_repair_date_on_service_tag_work_orders',
													'checked' 	=> $this->config->item('show_estimated_repair_date_on_service_tag_work_orders'))); ?>
												<label for="show_estimated_repair_date_on_service_tag_work_orders"><span></span></label>
											</div>
										</div>

										<div class="form-group" data-keyword="<?php echo H(lang('config_work_order_change_status_on_sales_complete')) ?>">	
											<?php echo form_label(lang('config_work_order_change_status_on_sales_complete').':', 'work_order_change_status_on_sales_complete',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<!-- Dropdown work_order_status -->
												<?php echo form_dropdown('work_order_status_on_complete', $work_order_status, $this->config->item('work_order_status_on_complete'), 'class="form-control" id="work_order_status_on_complete"'); ?>
												
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Work Order -->

				<!-- Lookup Api Integration -->
				<?php 
				if($this->config->item('branding')['code'] == 'phppointofsale'){
				?>
				<div class="tab-pane fade" id="lookup_api_integration">
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-piluku">
								<div class="panel-heading">
									<a data-toggle="collapse" data-parent="#collapsePanels" href="#lookup_api_integration_collapse">
									<?php echo lang('config_lookup_api_integration');?>
									</a>
								</div>
								<div id="lookup_api_integration_collapse" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
										
										
										<div class="form-group">	
										<?php echo form_label(lang('config_enable_ig_integration').':', 'enable_ig_integration',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'enable_ig_integration',
												'id'=>'enable_ig_integration',
												'value'=>'1',
												'checked'=>$this->config->item('enable_ig_integration')));?>
												<label for="enable_ig_integration"><span></span></label>
											</div>
										</div>
										
										
										<?php
										if(!is_on_phppos_host()) { 
										?>
										<div class="form-group" data-keyword="<?php echo H(lang('config_ig_api_bearer_token')) ?>">
											<?php echo form_label(lang('config_ig_api_bearer_token').':', 'ig_api_bearer_token',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'ig_api_bearer_token',
												'id'=>'ig_api_bearer_token',
												'placeholder' => '',
												'value'=>$this->config->item('ig_api_bearer_token')));?>
											</div>
										</div>
										<?php
										}
										?>
										<br /><br /><br />
										
										<div class="form-group">	
										<?php echo form_label(lang('config_enable_wgp_integration').':', 'enable_wgp_integration',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'enable_wgp_integration',
												'id'=>'enable_wgp_integration',
												'value'=>'1',
												'checked'=>$this->config->item('enable_wgp_integration')));?>
												<label for="enable_wgp_integration"><span></span></label>
											</div>
										</div>
										
										<?php
										if(!is_on_phppos_host()) { 
										?>
										
										<div class="form-group" data-keyword="<?php echo H(lang('config_wgp_integration_pkey')) ?>">
											<?php echo form_label(lang('config_wgp_integration_pkey').':', 'wgp_integration_pkey',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'wgp_integration_pkey',
												'id'=>'wgp_integration_pkey',
												'placeholder' => '',
												'value'=>$this->config->item('wgp_integration_pkey')));?>
											</div>
										</div>
										<?php } ?>
										
										<?php
										echo form_hidden('wgp_integration_userid',$this->config->item('wgp_integration_userid'));
										?>

										<br /><br /><br />
										<div class="form-group">
											<?php echo form_label(lang('config_enable_p4_integration').':', 'enable_p4_integration',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_checkbox(array(
												'name'=>'enable_p4_integration',
												'id'=>'enable_p4_integration',
												'value'=>'1',
												'checked'=>$this->config->item('enable_p4_integration')));?>
												<label for="enable_p4_integration"><span></span></label>
											</div>
										</div>
										
										
										<?php
										if(!is_on_phppos_host()) { 
										?>
										<div class="form-group" data-keyword="<?php echo H(lang('config_ig_api_bearer_token')) ?>">
											<?php echo form_label(lang('config_p4_api_bearer_token').':', 'p4_api_bearer_token',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-9 col-md-9 col-lg-10">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'p4_api_bearer_token',
												'id'=>'p4_api_bearer_token',
												'placeholder' => '',
												'value'=>$this->config->item('p4_api_bearer_token')));?>
											</div>
										</div>
										<?php
										}
										?>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
				}
				?>
				<!-- End Lookup Api Integration -->
			</div>
		</div>
			
		</div>
			
			<div class="form-actions">
			<?php echo form_submit(array(
				'name'=>'submitf',
				'id'=>'submitf',
				'value'=>lang('common_save'),
				'class'=>'submit_button floating-button btn btn-primary btn-lg pull-right')); ?>
			</div>
    
		<?php echo form_close(); ?>	
	</div>
</div>
<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{	

	// --- ZATCA Integration START--- //

	$("#use_saudi_tax_config").on('click', function(){
		if($("#use_saudi_tax_config").is(':checked')){
			$(".saudi_tax_config_item").removeClass('hide');
		}else{
			$(".saudi_tax_config_item").addClass('hide');
		}
	});
	
	zatca_generate_ccsid_pcsid = {
		submit: 0,
		zatca_otp: 0,
	};

	$("#zatca_input_otp_ccsid_pcsid").click(function()
	{
		event.preventDefault();

		bootbox.prompt({
			title: "Please enter OTP to generate CCSID and PCSID.",
			intpuType: 'text',
			value: "", 
			placeholder: "123345", 
			callback: function(zatca_otp){
				if(zatca_otp == null) return;

				if(zatca_otp.trim() == ""){
					show_feedback('error', "Please Input OPT.", <?php echo json_encode(lang('common_error')); ?>);
					return;
				}
				zatca_generate_ccsid_pcsid = {
					submit: 1,
					zatca_otp: zatca_otp
				};
				$("#config_form").submit();
			}
		})
	});

	zatca_generate_renew_pcsid = {
		submit: 0,
		zatca_otp: 0,
	};
	$("#zatca_input_otp_renew_pcsid").click(function()
	{
		event.preventDefault();

		bootbox.prompt({
			title: "Please enter OTP to renew PCSID",
			intpuType: 'text',
			value: "", 
			placeholder: "123456", 
			callback: function(zatca_otp){
				if(zatca_otp == null) return;

				if(zatca_otp.trim() == ""){
					show_feedback('error', "Please Input OPT.", <?php echo json_encode(lang('common_error')); ?>);
					return;
				}

				zatca_generate_renew_pcsid = {
					submit: 1,
					zatca_otp: zatca_otp 
				};
				$("#config_form").submit();
			}
		})
	});

	$("#zatca_init_test_db").on('click', function()
	{
		event.preventDefault();
		bootbox.confirm({
			message: "Are you sure you want to clean the database?",
			callback: function(result){
				if(result){
					$.post(SITE_URL+"/zatca/clean_zatca_invoice_history",{}, function(response)
					{
						if(response.state){
							show_feedback('success', response.message,COMMON_SUCCESS);
						}else{
							show_feedback('error', response.message, <?php echo json_encode(lang('common_error')); ?>);
						}
					},'json');
				}
			}
		});
	});

	$("#use_saudi_tax_test_config").on("change", function(){
		if($(this).is(":checked")){
			$("#zatca_init_test_db").show();
		}else{
			$("#zatca_init_test_db").hide();
		}
	});

	$("#zatca_display_csr_key").on("click", function(){
		bootbox.alert({
			title: "CSR / KEY",
			message: '<p style="word-wrap:break-word;"> CSR:' + $("#zatca_csr").text() + '<br/><br/> PrivateKey:' + $("#zatca_csr_private_key").text() + '</p>'
		});
	});	
	$("#zatca_display_ccsid").on("click", function(){
		bootbox.alert({
			title: "CCSID",
			message: '<p style="word-wrap:break-word;">' + $("#zatca_ccsid").text() + '</p>'
		});
	});
	$("#zatca_display_pcsid").on("click", function(){
		bootbox.alert({
			title: "PCSID",
			message: '<p style="word-wrap:break-word;">' + $("#zatca_pcsid").text() + '</p>'
		});
	});

	$.validator.addMethod(
		"zatca",
		function(value1, element, type) {
			var value = value1.trim();
			var check = false;
			if(type == "csr-tax-sn"){
				if(value.split("|").length == 3){
					let manufacture_str =  value.split("|")[0];
					let model_str =  value.split("|")[1];
					let sn_str =  value.split("|")[2];

					if(manufacture_str.split("1-").length >= 2 ){
						let manufacture = manufacture_str.split("1-")[1];

						if(model_str.split("2-").length >= 2 ){
							let model = model_str.split("2-")[1];

							if(sn_str.split("3-").length >= 2 ){
								let sn = sn_str.split("3-")[1];

								if(manufacture != "" && model != "" && sn != ""){
									check = true;
								}
							}
						}
					}
				}
			}else if(type == "csr-tax-org-id"){
				if(value.length == 15){
					if(value[0] == "3" && value[14] == "3"){
						check = true;
					}
				}
			}else if(type == "csr-tax-org-unit-name"){
				// rule check only vat group case
				// ...
				check = true;
			}else if(type == "csr-tax-org-name"){
				check = true;
			}else if(type == "csr-tax-invoice-type"){
				if(value.length == 4){
					if(value[0] == 1 || value[0] == 0){
						if(value[1] == 1 || value[1] == 0){
							if(value[2] == 1 || value[2] == 0){
								if(value[3] == 1 || value[3] == 0){
									check = true;
								}
							}
						}
					}
				}
			}else {
				check = true;
			}
			return check;
		},
		"Please check the ZATCA configuration input validation rules."
	);
	
	function zatca_integration_after_config_submit(){

		$.post(<?php echo json_encode(site_url('config/get_zatca_config_state')); ?>,{},function(response){

			if(response.state == 1){
				if(response.zatca_config['compliance_csid'] == ""){
					zatca_generate_renew_pcsid = {
						submit: 0,
						zatca_otp: 0,
					};
					$("#zatca_input_otp_ccsid_pcsid").show();
					$("#zatca_input_otp_renew_pcsid").hide();

					$("#zatca_cert").val("");
					$("#zatca_private_key").val("");
					$('#zatca_cert').prop('disabled', true);
					$('#zatca_private_key').prop('disabled', true);

					$("#zatca_display_csr_key").hide();
					$("#zatca_display_ccsid").hide();
					$("#zatca_display_pcsid").hide();

				} else if(response.zatca_config['compliance_csid'] != "" && response.zatca_config['production_csid'] == ""){
					zatca_generate_ccsid_pcsid = {
						submit: 0,
						zatca_otp: 0,
					};
					$("#zatca_display_csr_key").show();
					$("#zatca_display_ccsid").show();
					$("#zatca_display_pcsid").hide();

					$("#zatca_input_otp_ccsid_pcsid").hide();
					$("#zatca_input_otp_renew_pcsid").show();

					$("#zatca_cert").val("");
					$("#zatca_private_key").val("");
					$('#zatca_cert').prop('disabled', true);
					$('#zatca_private_key').prop('disabled', true);

				} else if(response.zatca_config['compliance_csid'] != "" && response.zatca_config['production_csid'] != ""){
					$("#zatca_cert").val(response.zatca_config['cert']);
					$("#zatca_private_key").val(response.zatca_config['private_key']);
					$('#zatca_cert').prop('disabled', false);
					$('#zatca_private_key').prop('disabled', false);

					$("#zatca_display_csr_key").show();
					$("#zatca_display_ccsid").show();
					$("#zatca_display_pcsid").show();
				}

				if(zatca_generate_ccsid_pcsid && zatca_generate_ccsid_pcsid.submit == 1){
					$.post(<?php echo json_encode(site_url('invoices/zatca_generate_ccsid_pcsid')); ?>,{zatca_otp:zatca_generate_ccsid_pcsid.zatca_otp},function(response)
					{
						if (response.state == 1)
						{
							$("#zatca_input_otp_ccsid_pcsid").hide();
							$("#zatca_input_otp_renew_pcsid").show();
							show_feedback('success', response.message, <?php echo json_encode(lang('common_success')); ?>);

							show_feedback('warning', "Please update zatca integration Cert & Private key.", "<?php echo ucfirst(lang('common_warning')); ?>");
							$("#zatca_cert").val("");
							$("#zatca_private_key").val("");
							$('#zatca_cert').prop('disabled', false);
							$('#zatca_private_key').prop('disabled', false);

							$("#zatca_display_csr_key").show();
							$("#zatca_display_ccsid").show();
							$("#zatca_display_pcsid").show();
							$("#zatca_csr").text(response.data.csr);
							$("#zatca_csr_private_key").text(response.data.csr_private_key);
							$("#zatca_ccsid").text(response.data.ccsid);
							$("#zatca_pcsid").text(response.data.pcsid);
						}
						else
						{
							show_feedback('error', "Generating CCSID & PCSID error.", <?php echo json_encode(lang('common_error')); ?>);
						}
					},'json');

					zatca_generate_ccsid_pcsid = {
						submit: 0,
						zatca_otp: 0
					};
				}

				if(zatca_generate_renew_pcsid && zatca_generate_renew_pcsid.submit == 1){
					$.post(<?php echo json_encode(site_url('invoices/zatca_renew_pcsid')); ?>,{renew_opt:zatca_generate_renew_pcsid.zatca_otp},function(response)
					{
						if (response.state == 1)
						{
							show_feedback('success', response.message, <?php echo json_encode(lang('common_success')); ?>);
							$("#zatca_input_otp_ccsid_pcsid").hide();
							$("#zatca_input_otp_renew_pcsid").show();

							show_feedback('warning', "Please update zatca integration Cert & Private key.", "<?php echo ucfirst(lang('common_warning')); ?>");
							$("#zatca_cert").val("");
							$("#zatca_private_key").val("");
							$('#zatca_cert').prop('disabled', false);
							$('#zatca_private_key').prop('disabled', false);

							$("#zatca_display_csr_key").show();
							$("#zatca_display_ccsid").show();
							$("#zatca_display_pcsid").show();

							$("#zatca_pcsid").text(response.data);
						}
						else
						{
							show_feedback('error', "Renew PCSID Error.", <?php echo json_encode(lang('common_error')); ?>);
						}
					},'json');
					zatca_generate_renew_pcsid = {
						submit: 0,
						zatca_otp: 0,
					};
				}
			}
		}, 'json');

	}
	// --- ZATCA Integration END--- //

	date_time_picker_field($('.timepicker'), JS_TIME_FORMAT);
	date_time_picker_field($('.datepicker'), 'YYYY-MM-DD');
	
	$("#section_names").change(function (data) {
		var section_name = $(this).val();
		if (section_name == '') {
			$('html,body').animate({
						scrollTop: 0,
					},
					'fast');
		} else {
			$('html,body').animate({
						scrollTop: $('.' + section_name).offset().top - ($(".email_buttons").height() + 36),
					},
					'fast');

			var tabId = $('.' + section_name).closest('.tab-pane').attr('id');
			var configNav = $('a[href="#' + tabId + '"]').closest('li');

			configNav.find('a').trigger('click');
			$('.config-nav li').removeClass('active');
			configNav.addClass('active');

			// $('.' + section_name + ' > .panel > .collapse').collapse('show');
		}

	});
	
	$("#test_email").click(function()
	{
		bootbox.prompt({
			title: <?php echo json_encode(lang('config_please_enter_email_to_send_test_to')); ?>,
			value: <?php echo json_encode($this->Location->get_info_for_key('email')); ?>, 
			callback: function(email) 
			{
				$("#config_form").ajaxSubmit(function()
				{
					$.post(<?php echo json_encode(site_url('config/send_smtp_test_email')); ?>,{email:email},function(response)
					{
						if (response.success)
						{
							show_feedback('success',response.message,<?php echo json_encode(lang('common_success')); ?>);
						}
						else
						{
							show_feedback('error',<?php echo json_encode(lang('common_error')); ?>,<?php echo json_encode(lang('common_error')); ?>);
							bootbox.alert({
								title: <?php echo json_encode(lang('common_error')); ?>,
								message: response.message
							});
						}
					},'json');
				})
			
			}
		});
	});
	
	var gmail = {
		smtp_crypto : 'ssl',
		protocol : 'smtp', 
		smtp_host : 'smtp.gmail.com',
		smtp_user : 'username@gmail.com',
		smtp_pass : '',
		smtp_port : '465',
		email_charset : 'utf-8',
		newline : 0,
		crlf : 0,
		smtp_timeout : '10'
	};
	
	var gmail_api = {
		smtp_crypto : 'ssl',
		protocol : 'smtp', 
		smtp_host : 'smtp.gmail.com',
		smtp_user : 'username@gmail.com',
		smtp_pass : '',
		smtp_port : '465',
		email_charset : 'utf-8',
		newline : 0,
		crlf : 0,
		smtp_timeout : '10'
	};

	var office_365 = {
		smtp_crypto : 'tls',
		protocol : 'smtp', 
		smtp_host : 'smtp.office365.com',
		smtp_user : 'user@domain.com',
		smtp_pass : '',
		smtp_port : '587',
		email_charset : 'utf-8',
		newline : 0,
		crlf : 0,
		smtp_timeout : '10'
	};
	
	var windows_live_hotmail = {
		smtp_crypto : 'tls',
		protocol : 'smtp', 
		smtp_host : 'smtp.live.com',
		smtp_user : 'user@outlook.com',
		smtp_pass : '',
		smtp_port : '587',
		email_charset : 'utf-8',
		newline : 0,
		crlf : 0,
		smtp_timeout : '10'
	};
	
	var other = {
		smtp_crypto : '',
		protocol : '', 
		smtp_host : '',
		smtp_user : 'user@domain.com',
		smtp_pass : '',
		smtp_port : '',
		email_charset : '',
		newline : 0,
		crlf : 0,
		smtp_timeout : ''
	};
	
	var system_default = {
		smtp_crypto : '',
		protocol : '', 
		smtp_host : '',
		smtp_user : '',
		smtp_pass : '',
		smtp_port : '',
		email_charset : '',
		newline : 0,
		crlf : 0,
		smtp_timeout : ''
	};
	
	if($("#email_provider").val() !== "Other")
	{
		$(".email_advanced").hide();
	}
	
	if($("#email_provider").val() == "Use System Default")
	{
		$(".email_basic").hide();
	}

	if($("#email_provider").val() == "Gmail API")
	{
		$(".email_basic").hide();
		$(".email_advanced").hide();
		$(".email_gmail_api").show();
	}else{
		$(".email_gmail_api").hide();
	}

	$("#email_provider").change(function(e){
		$(".email_gmail_api").hide();

		switch ($("#email_provider").val()) {
				case 'Use System Default':
						$(".email_basic").hide();
						$(".email_advanced").hide();
						var settings = false;
						break;
		    case 'Gmail':
						$(".email_basic").show();
						$(".email_advanced").hide();
						var settings = gmail;
		        break;
			case 'Gmail API':
						$(".email_basic").hide();
						$(".email_advanced").hide();
						$(".email_gmail_api").show();
						var settings = gmail_api;
				break;
		    case 'Office 365':
						$(".email_basic").show();
						$(".email_advanced").hide();
		        var settings = office_365;
		        break;
		    case 'Windows Live Hotmail':
						$(".email_basic").show();
						$(".email_advanced").hide();
		        var settings = windows_live_hotmail;
		        break;
		    case 'Other':
						var settings = other;
						$(".email_basic").show();
		        $(".email_advanced").show();
		        break;
		}
		
		if(settings)
		{
			for (var key in settings)
			{
				if(key == 'smtp_user')
				{
					$("#"+key).val('');
					$("#"+key).attr('placeholder', settings[key]);
				} else if (key == 'newline' || key == 'crlf'){
					$("#"+key).prop('selectedIndex', settings[key]);
				}	else {
					$("#"+key).val(settings[key]);
				}
			}
		} else {
			for (var key in system_default)
			{
				if (key == 'newline' || key == 'crlf'){
					$("#"+key).prop('selectedIndex', settings[key]);
				}	else {
					$("#"+key).val(settings[key]);
				}
			}
		} 
	});

	// $("#gmail_credential_json").on('change', function(){
	// 	var file_data = $(this).prop('files')[0];

	// 	var fileread = new FileReader();
	// 	fileread.onload = function(e) {
	// 		var content = e.target.result;
	// 		var credential = JSON.parse(content); // Array of Objects.
	// 		// console.log(credential);
	// 		if(credential.web && credential.web.client_id){
	// 			$("#gmail_client_id").val(credential.web.client_id);
	// 		}
	// 		if(credential.web && credential.web.client_secret){
	// 			$("#gmail_client_secret").val(credential.web.client_secret);
	// 		}

	// 		document.getElementById("gmail_credential_json").value = "";
	// 	};
	// 	fileread.readAsText(file_data);
	// });

	$("#gmail_api_authorize_button").on('click', function(){
		event.preventDefault();
		var href = '<?php echo site_url("configGmailAPI/gmail_api_credential");?>';
		$.ajax({
			type: "POST",
			url: href,
			dataType: 'json',
			data: {},
			success: function(result) {
				// Reset the form.
				if(result.status == 1){
					authorize_dialog_window = window.open( result.authURL, '_blank', 'width=500,height=600');
				}else{
					alert(result.error);
				}
			}
		});
	});

	$("#gmail_api_signout_button").on("click", function(){
		event.preventDefault();
		var href = '<?php echo site_url("configGmailAPI/gmail_api_signout");?>';
		$.ajax({
			type: "POST",
			url: href,
			dataType: 'json',
			data: {},
			success: function(result) {
				// Reset the form.
				if(result.status == 1){
					$("#gmail_api_signout_button").hide();
					document.getElementById("gmail_api_authorize_button").style.display = "inline-block";
					show_feedback('success', result.message, "<?php echo lang("gmail_api_success");?>");
					$("#submitf").trigger("click");
				}else{
					show_feedback('error', result.message, "<?php echo lang("gmail_api_error");?>");
				}
			}
		});
	});

	$(".ecommerce_platform").change(function() 
	{
		if ($(".ecommerce_platform").val() == "woocommerce")
		{
			$(".woo_settings").removeClass('hidden');
			$(".shopify_settings").addClass('hidden');
			$('.woo-only').show();
		}
		else
		{
			$(".woo_settings").addClass('hidden');
		}
		
		if ($(".ecommerce_platform").val() == "shopify")
		{
			$(".shopify_settings").removeClass('hidden');
			$(".woo_settings").addClass('hidden');
			$('.woo-only').hide();
		}
		else{
			$(".shopify_settings").addClass('hidden');
		}
		
	});
	
	if ($(".ecommerce_platform").val() == "woocommerce")
	{
		$('.woo-only').show();
	}
	else if ($(".ecommerce_platform").val() == "shopify")
	{
		$('.woo-only').hide();
	}
	
	$(document).on('keyup', ".default_percent_off",function(e){
		
		if ($(this).val())
		{
			$(this).parent().parent().find('.default_cost_plus_percent').val('');
			$(this).parent().parent().find('.default_cost_plus_fixed_amount').val('');
		}
	});

	$(document).on('keyup', ".default_cost_plus_percent",function(e){
		if($(this).val())
		{
			$(this).parent().parent().find('.default_percent_off').val('');
			$(this).parent().parent().find('.default_cost_plus_fixed_amount').val('');
		}
	});

	$(document).on('keyup', ".default_cost_plus_fixed_amount",function(e){
		if($(this).val())
		{
			$(this).parent().parent().find('.default_percent_off').val('');
			$(this).parent().parent().find('.default_cost_plus_percent').val('');
		}
	});
		
	
	$(".delete_tier").click(function()
	{
		$("#config_form").append('<input type="hidden" name="tiers_to_delete[]" value="'+$(this).data('tier-id')+'" />');
		$(this).parent().parent().remove();
	});
	
	$(".delete_api_key").click(function()
	{
		var api_key_id = $(this).data('key-id');
		
		bootbox.confirm(<?php echo json_encode(lang('config_api_key_confirm_delete')); ?>, function(response)
		{
			if (response)
			{
				 post_submit('<?php echo site_url("config/delete_key"); ?>', 'POST',[{name:'api_key_id', value:api_key_id}]);
			}
		});
	});
	
	$("#price_tiers tbody").sortable();
	
	var add_index = -1;
	
	$("#add_tier").click(function()
	{
		$("#price_tiers tbody").append('<tr><td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td><td><input type="text" class="tiers_to_edit form-control" data-index="'+add_index+'" name="tiers_to_edit['+add_index+'][name]" value="" /></td><td><input type="text" class="tiers_to_edit form-control default_percent_off" data-index="'+add_index+'" name="tiers_to_edit['+add_index+'][default_percent_off]" value=""/></td><td><input type="text" class="tiers_to_edit form-control default_cost_plus_percent" data-index="'+add_index+'" name="tiers_to_edit['+add_index+'][default_cost_plus_percent]" value=""/></td><td><input type="text" class="tiers_to_edit form-control default_cost_plus_fixed_amount" data-index="'+add_index+'" name="tiers_to_edit['+add_index+'][default_cost_plus_fixed_amount]" value=""/></td><td>&nbsp;</td></tr>');
		
		add_index--;
	});
	
	$('#additional_payment_types').selectize({
	    delimiter: ',',
	    persist: false,
	    create: function(input) {
	        return {
	            value: input,
	            text: input
	        }
	    }
	});	
	
	$(".delete_sale_type").click(function()
	{
		$("#config_form").append('<input type="hidden" name="sale_types_to_delete[]" value="'+$(this).data('sale-type-id')+'" />');
		$(this).parent().parent().remove();
	});
	
	$("#sale_types tbody").sortable();
	
	var add_sale_type = -1;
	
	$("#add_sale_type").click(function()
	{	
		$("#sale_types tbody").append('<tr><td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td><td></td><td><input type="text" class="sale_types_to_edit form-control" data-index="'+add_sale_type+'" name="sale_types_to_edit['+add_sale_type+'][name]" value="" /></td><td class="text-center"><input type="checkbox" name="sale_types_to_edit['+add_sale_type+'][remove_quantity]" value="1" id="remove_quantity_'+add_sale_type+'" data-index="'+add_sale_type+'"><label for="remove_quantity_'+add_sale_type+'"><span></span></label></td><td>&nbsp;</td></tr>');
		add_sale_type--;
	});
	
	$('#additional_payment_types').selectize({
	    delimiter: ',',
	    persist: false,
	    create: function(input) {
	        return {
	            value: input,
	            text: input
	        }
	    }
	});
	
	$('#damaged_reasons').selectize({
	    delimiter: ',',
	    persist: false,
	    create: function(input) {
	        return {
	            value: input,
	            text: input
	        }
	    }
	})
	
	
	$(".delete_currency_denom").click(function()
	{
		var id = $(this).data('id');
		$("#currency_denoms").append('<input class="deleted_denmos" type="hidden" name="deleted_denmos[]" value="'+id+'" />');
		
		$(this).parent().parent().remove();
	});
	
	$(".delete_currency_exchange_rate").click(function()
	{
		$(this).parent().parent().remove();	
	});
	
	$("#add_denom").click(function()
	{
		$("#currency_denoms tbody").append('<tr><td><input type="text" class="form-control" name="currency_denoms_name[]" value="" /></td><td><input type="text" class="form-control" name="currency_denoms_value[]" value="" /></td><td>&nbsp;</td><input type="hidden" name="currency_denoms_ids[]" /></tr>');
	});
	
	$("#add_exchange_rate").click(function()
	{		
		$("#currency_exchange_rates tbody").append('<tr>'+
		'<td><input type="text" class="form-control" name="currency_exchange_rates_to[]" value="" /></td>'+
		'<td><input type="text" class="form-control" name="currency_exchange_rates_symbol[]" value="$" /></td>'+
		'<td><select name="currency_exchange_rates_symbol_location[]" class="form-control"><option value="before"><?php echo lang('config_before_number'); ?></option><option value="after"><?php echo lang('config_after_number'); ?></option></select></td>'+
		'<td><select name="currency_exchange_rates_number_of_decimals[]" class="form-control"><option value=""><?php echo lang('config_let_system_decide'); ?></option><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></td>'+
		'<td><input type="text" class="form-control" name="currency_exchange_rates_thousands_separator[]" value="," /></td>'+
		'<td><input type="text" class="form-control" name="currency_exchange_rates_decimal_point[]" value="." /></td>'+
		'<td><input type="text" class="form-control" name="currency_exchange_rates_rate[]" value="" /></td>'+
		'<td>&nbsp;</td></tr>');
	});
	
	$(".dbOptimize").click(function(event)
	{
		event.preventDefault();
		$('#ajax-loader').removeClass('hidden');
		
		$.getJSON($(this).attr('href'), function(response) 
		{
			$('#ajax-loader').addClass('hidden');
			bootbox.alert(response.message);
		});
		
	});
	
	$(".checkForUpdate").click(function(event)
	{
		event.preventDefault();
		$('#ajax-loader').removeClass('hidden');
		
		$.getJSON($(this).attr('href'), function(update_available) 
		{
			$('#ajax-loader').addClass('hidden');
			if(update_available)
			{
				bootbox.confirm(<?php echo json_encode(lang('common_update_available')); ?>, function(response)
				{
					if (response)
					{
						window.location="http://<?php echo $this->config->item('branding')['domain']; ?>/downloads.php";
					}
				});
			}
			else
			{
				bootbox.alert(<?php echo json_encode(lang('common_not_update_available')); ?>);
			}
		});
		
	});
	
	
	$("#reset_ecommerce").click(function(event)
	{
		bootbox.confirm(<?php echo json_encode(lang('config_confirm_reset_ecom')); ?>, function(response)
		{
			if (response)
			{
				$.getJSON(<?php echo json_encode(site_url('config/reset_ecom')); ?>,function(response)
				{
					if (response.success)
					{
						show_feedback('success',response.message,<?php echo json_encode(lang('common_success')); ?>);
					}
				});
			}
		});
	});
	var submitting = false;

	function objectifyForm(formArray) {
		//serialize data function
		var returnArray = {};
		for (var i = 0; i < formArray.length; i++){
			if(returnArray[formArray[i]['name']] ){
				if(!Array.isArray(returnArray[formArray[i]['name']])){
					returnArray[formArray[i]['name']] = [returnArray[formArray[i]['name']]];
				}
				returnArray[formArray[i]['name']].push(formArray[i]['value']);
			}else{
				returnArray[formArray[i]['name']] = formArray[i]['value'];
			}
		}
		return returnArray;
	}

	$('#config_form').validate({
		submitHandler:function(form)
		{
			console.log("test123 --------------------------------------------------------------------- btn click")
			if (submitting) return;
			submitting = true;
			$(form).ajaxSubmit({
			success:function(response)
			{
				
				
				//Don't let the tiers, taxes, providers, methods double submitted, so we change the name
				$('.zones,.tiers_to_edit,.providers,.methods,.taxes,.tax_classes,.sale_types_to_edit').filter(function() {
				    return parseInt($(this).data('index')) < 0;
				}).attr('name','items_added[]');
				
				if(response.success)
				{
					if(zatca_integration_after_config_submit)
						zatca_integration_after_config_submit();

					formDataArray =  objectifyForm($("#config_form").serializeArray());
					let disable_modules = formDataArray['disable_modules[]'];
					
					if (!disable_modules)
					{
						disable_modules = [];
					}
					$("#mainMenu>li").show();
					for(let i=0; i<disable_modules.length; i++){
						$("#mainMenu>li."+disable_modules[i]).hide();
					}

					show_feedback('success',response.message,<?php echo json_encode(lang('common_success')); ?>);
				}
				else
				{
					show_feedback('error',response.message,<?php echo json_encode(lang('common_error')); ?>);
					
				}
				submitting = false;
			},
			dataType:'json'
		});

		},
		errorClass: "text-danger",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
		},
		rules: 
		{
    		company: "required",
    		sale_prefix: "required",
			return_policy:
			{
				required: true
			},
			item_id_auto_increment:
			{
				number: true,
				min: 1,
				max: 999999999
			},
			item_kit_id_auto_increment:
			{
				number: true,
				min: 1,
				max: 999999999
			},
			sale_id_auto_increment:
			{
				number: true,
				min: 1,
				max: 999999999
			},
			receiving_id_auto_increment:
			{
				number: true,
				min: 1,
				max: 999999999
			},
			saudi_tax_sn:{
				required: true,
				zatca: 'csr-tax-sn'
			},
			saudi_tax_org_id:{
				required: true,
				zatca: 'csr-tax-org-id'
			},
			saudi_tax_org_unit_name:{
				required: true,
				zatca: 'csr-tax-org-unit-name'
			},
			saudi_tax_org_name:{
				required: true,
				zatca: 'csr-tax-org-name'
			},
			saudi_tax_country_name:{
				required: true,
			},
			saudi_tax_invoice_type:{
				required: true,
				zatca: 'csr-tax-invoice-type'
			},
			saudi_tax_location:{
				required: true,
			},
			saudi_tax_industry:{
				required: true,
			},
			zatca_seller_id:{
				required: true,
			},
			zatca_seller_tax_id:{
				required: true,
			},
			zatca_seller_scheme_id:{
				required: true,
			},
			zatca_seller_party_postal_street_name:{
				required: true,
			},
			zatca_seller_party_postal_building_number:{
				required: true,
				minlength:4,
				maxlength:4,
			},
			zatca_seller_party_postal_code:{
				required: true,
				minlength:5,
				maxlength:5,
			},
			zatca_seller_party_postal_city:{
				required: true,
			},
			zatca_seller_party_postal_district:{
				required: true,
			},
			zatca_seller_party_postal_plot_id: {
				required: true,
				minlength:4,
				maxlength:4,
			}
	   	},
		messages: 
		{
     		company: <?php echo json_encode(lang('config_company_required')); ?>,
     		sale_prefix: <?php echo json_encode(lang('config_sale_prefix_required')); ?>,
			return_policy:
			{
				required:<?php echo json_encode(lang('config_return_policy_required')); ?>
			},
		}
	});

});

//gmail api integration
var authorize_dialog_window = null;
function authorize_dialog_message(msg){
	// console.log("messge from authorize dialog:" + msg);
	if(msg == "success"){
		authorize_dialog_window.close();
		document.getElementById("gmail_api_signout_button").style.display = "inline-block";
		document.getElementById("gmail_api_authorize_button").style.display = "none";
		$("#submitf").trigger("click");
	}
}

$("#offline_mode").change(offline_mode_change).ready(offline_mode_change);

function offline_mode_change()
{
	if($("#offline_mode").prop('checked'))
	{
		$("#force_https").prop('checked', true);
	}	
}

$("#calculate_average_cost_price_from_receivings").change(check_calculate_average_cost_price_from_receivings).ready(check_calculate_average_cost_price_from_receivings);

function check_calculate_average_cost_price_from_receivings()
{
	if($("#calculate_average_cost_price_from_receivings").prop('checked'))
	{
		$("#average_cost_price_from_receivings_methods").show();
		$("#update_cost_price_on_transfer_container").show();
	}
	else
	{
		$("#average_cost_price_from_receivings_methods").hide();
		$("#update_cost_price_on_transfer_container").hide();
		
	}
}

$("#enable_customer_loyalty_system,#loyalty_option").change(check_loyalty_setup).ready(check_loyalty_setup);

function check_loyalty_setup()
{
	if($("#enable_customer_loyalty_system").prop('checked'))
	{
		$("#loyalty_setup").show();
	}
	else
	{
		$("#loyalty_setup").hide();
	}
	
	if ($("#loyalty_option").val() == 'simple')
	{
		$("#loyalty_setup_simple").show();
		$("#loyalty_setup_advanced").hide();
	}
	else
	{
		$("#loyalty_setup_simple").hide();	
		$("#loyalty_setup_advanced").show();	
	}
}

<?php
if ($search = $this->input->get('search')) { ?>
	$("#search").val(<?php echo json_encode($this->input->get('search')); ?>);	
<?php } ?>

$( document ).ready(function() {
	$(".config-panel").sieve({
		itemSelector: "div.form-group",
		searchInput: $('#search'),
		complete: function() {
			if (event.type == 'keyup') {
				$(".panel-body").each(function(index) {
					var $this = $(this);

					var $visible_element = $this.find('.form-group').filter(function() 
					{
						return $(this).css('display') != 'none'
					});

					if($visible_element.length == 0)
					{
						var navId = $this.closest('.tab-pane').attr('id');
						var configNav = $('a[href="#' + navId + '"]').closest('li').hide();

						$this.closest('.col-md-12').hide();
						// $this.closest('.col-md-12').collapse('hide');
						// var section_name = 'col-md-12';
						// $('.' + section_name + ' > .panel > .collapse').collapse('show');
					}
					else
					{
						var navId = $this.closest('.tab-pane').attr('id');
						var configNav = $('a[href="#' + navId + '"]').closest('li').show();

						$this.closest('.col-md-12').show();
						// var section_name = 'col-md-12';
						// $('.' + section_name + ' > .panel > .collapse').collapse('hide');

					}

				})
			}
		}
	});
	$("#search").focus().trigger('keyup');

	if($(window).width() < 992) {
		$('.config-nav').hide();

		$('.tab-pane').each(function() {
			if(!$(this).hasClass('in')) {
				$(this).addClass('in');
			}

			if(!$(this).hasClass('active')) {
				$(this).addClass('active');
			}
		});
	} else {
		$('.config-nav').show();

		$('.tab-pane').each(function() {
			if($(this).hasClass('in')) {
				$(this).removeClass('in');
			}

			if($(this).hasClass('active')) {
				$(this).removeClass('active');
			}
		});

		$('#company_information').addClass('in active');
	}

	$(window).resize(function() {
		if($(window).width() < 992) {
			$('.config-nav').hide();

			$('.tab-pane').each(function() {
				if(!$(this).hasClass('in')) {
					$(this).addClass('in');
				}
	
				if(!$(this).hasClass('active')) {
					$(this).addClass('active');
				}
			});
		} else {
			$('.config-nav').show();

			$('.tab-pane').each(function() {
				if($(this).hasClass('in')) {
					$(this).removeClass('in');
				}

				if($(this).hasClass('active')) {
					$(this).removeClass('active');
				}
			});

			$('#company_information').addClass('in active');
		}
	})
});





<?php
$deleted_payment_types = $this->config->item('deleted_payment_types');
$deleted_payment_types = explode(',',$deleted_payment_types);

foreach($deleted_payment_types as $deleted_payment_type)
{
?>
	$( ".payment_types" ).each(function() {
		if ($(this).text() == <?php echo json_encode($deleted_payment_type); ?>)
		{
			$(this).removeClass('btn-primary');			
			$(this).addClass('deleted btn-danger');			
		}
	});
<?php
}
?>
save_deleted_payments();

$(".payment_types").click(function(e)
{
	e.preventDefault();
	$(this).toggleClass('btn-primary');
	$(this).toggleClass('deleted btn-danger');
	save_deleted_payments();
});

function save_deleted_payments()
{
	$(".deleted_payment_types").remove();
	
	var deleted_payment_types = [];
	$( ".payment_types.deleted" ).each(function() {
		deleted_payment_types.push($(this).text());
	});
	$("#config_form").append('<input class="deleted_payment_types" type="hidden" name="deleted_payment_types" value="'+deleted_payment_types.join()+'" />');
	
}

$("#cancel_woo").click(function()
{
	
	bootbox.confirm({
		message: <?php echo json_encode(lang('confirmation_woocommerce_cron_cancel')); ?>,
		buttons: {
      cancel: {
          label: <?php echo json_encode(lang('common_no')); ?>,
          className: 'btn-default'
      	},
        confirm: {
            label: <?php echo json_encode(lang('common_yes')); ?>,
            className: 'btn-primary'
        }
    },
		callback: function(response)
		{
				if (response)
				{	
					$.get(<?php echo json_encode(site_url('ecommerce/cancel'));?>);
				}
		}
	});
});

function check_ecommerce_status()
{
	$.getJSON(SITE_URL+'/home/get_ecommerce_sync_progress', function(response)
	{
		if (response.running)
		{
			$("#ecommerce-cancel-button").removeClass('hidden');
			set_ecommerce_progress(response.percent_complete,response.message);
			setTimeout(check_ecommerce_status,5000);
		}
		else
		{
			$("#ecommerce-cancel-button").addClass('hidden');;
		}
	});
}

function set_ecommerce_progress(percent, message)
{
	$("#ecommerce_sync_progress").toggleClass('hidden', false);
	$('#ecommerce_progessbar').attr('aria-valuenow', percent).css('width',percent+'%');
	$('#ecommerce_progress_percent').html(percent);
	if (message !='')
	{
		$("#ecommerce_progress_message").html('('+message+')');
	}
	else
	{
		$("#ecommerce_progress_message").html('');
	}
	
}

check_ecommerce_status();


$('#sync_woo').click(function()
{
	bootbox.confirm({
		message: <?php echo json_encode(lang('confirmation_woocommerce_cron')); ?>,
		buttons: {
      cancel: {
          label: <?php echo json_encode(lang('common_no')); ?>,
          className: 'btn-default'
      	},
        confirm: {
            label: <?php echo json_encode(lang('common_yes')); ?>,
            className: 'btn-primary'
        }
    },
		callback: function(response)
		{
				if (response)
				{	
					$('#sync_woo_button_icon').toggleClass("glyphicon-refresh-animate", true);
					
					
					$("#ecommerce_sync_progress").toggleClass("hidden", false);
					
					
					$("#config_form").ajaxSubmit(function()
					{
					 //Wait 3 seconds before checking status for first time
					 setTimeout(function(){ check_ecommerce_status();}, 3000);
						var href = '<?php echo site_url("ecommerce/manual_sync");?>'
						$.ajax(href, {
	  					dataType: "json",
						  success: function(data) {
							  $('#ajax-loader').addClass('hidden');
							  if(data.success){
									
									if (data.cancelled)
									{
										show_feedback('error',<?php echo json_encode(lang('common_cron_cancelled')); ?>,<?php echo json_encode(lang('common_error')); ?>);
									}
									else
									{
										show_feedback('success', <?php echo json_encode(lang('common_cron_success')); ?>, <?php echo json_encode(lang('common_success')); ?>);
									}
									
									$('#sync_woo').parents('.form-group').addClass('has-success').removeClass('has-error');
									
								  $('#sync_woo_button_icon').toggleClass("glyphicon-refresh-animate", false);
									
									setTimeout(function(){
										$("#ecommerce_sync_progress").toggleClass("hidden", true);
									}, 1000);
									
									$("#ecommerce_sync_date").val(<?php echo json_encode(lang('common_just_now')); ?>);
							  }
							  else{
								  show_feedback('error', data.message);
									$('#sync_woo').parents('.form-group').removeClass('has-success').addClass('has-error');
									
									$('#sync_woo_button_icon').toggleClass("glyphicon-refresh-animate", false);
									$("#ecommerce_sync_progress").toggleClass("hidden", true);
									
							  }
						  },
						  error: function() {
							  show_feedback('error', <?php echo json_encode(lang('common_access_denied')); ?>);
								$('#sync_woo').parents('.form-group').removeClass('has-success').addClass('has-error');
							  $('#sync_woo_button_icon').toggleClass("glyphicon-refresh-animate", false);
								$("#ecommerce_sync_progress").toggleClass("hidden", true);
						  }
					   });
						 						
					});
	   		}
		}
	});
});


$("#cancel_qb").click(function()
{
	
	bootbox.confirm({
		message: <?php echo json_encode(lang('config_confirmation_qb_cron_cancel')); ?>,
		buttons: {
      cancel: {
          label: <?php echo json_encode(lang('common_no')); ?>,
          className: 'btn-default'
      	},
        confirm: {
            label: <?php echo json_encode(lang('common_yes')); ?>,
            className: 'btn-primary'
        }
    },
		callback: function(response)
		{
				if (response)
				{	
					$.get(<?php echo json_encode(site_url('quickbooks/cancel'));?>);
				}
		}
	});
});

function check_quickbooks_status()
{
	$.getJSON(SITE_URL+'/home/get_qb_sync_progress', function(response)
	{
		if (response.running)
		{
			$("#qb-cancel-button").removeClass('hidden');
			set_quickbooks_progress(response.percent_complete,response.message);
			setTimeout(check_quickbooks_status,5000);
		}
		else
		{
			$("#qb-cancel-button").addClass('hidden');;
		}
	});
}

function set_quickbooks_progress(percent, message)
{
	$("#quickbooks_sync_progress").toggleClass('hidden', false);
	$('#quickbooks_progessbar').attr('aria-valuenow', percent).css('width',percent+'%');
	$('#quickbooks_progress_percent').html(percent);
	if (message !='')
	{
		$("#quickbooks_progress_message").html('('+message+')');
	}
	else
	{
		$("#quickbooks_progress_message").html('');
	}
	
}

check_quickbooks_status();


$('#sync_qb').click(function()
{
	bootbox.confirm({
		message: <?php echo json_encode(lang('config_confirmation_qb_cron')); ?>,
		buttons: {
      cancel: {
          label: <?php echo json_encode(lang('common_no')); ?>,
          className: 'btn-default'
      	},
        confirm: {
            label: <?php echo json_encode(lang('common_yes')); ?>,
            className: 'btn-primary'
        }
    },
		callback: function(response)
		{
				if (response)
				{	
					$('#sync_qb_button_icon').toggleClass("glyphicon-refresh-animate", true);
					
					
					$("#quickbooks_sync_progress").toggleClass("hidden", false);
					
					
					$("#config_form").ajaxSubmit(function()
					{
					 //Wait 3 seconds before checking status for first time
					 var start_date = $('#export_start_date').val();
					 if(start_date=="")
					 {
						 alert("Please fill start date");
						 return false;
					 }
					//  return false;
					 setTimeout(function(){ check_quickbooks_status();}, 3000);
						var href = '<?php echo site_url("quickbooks/manual_sync");?>'
						$.ajax(href, {
	  					dataType: "json",
							// type: 'POST',
							// data:  { export_start_date: start_date} ,
						  success: function(data) {
							  $('#ajax-loader').addClass('hidden');
							  if(data.success){
									
									if (data.cancelled)
									{
										show_feedback('error',<?php echo json_encode(lang('common_cron_cancelled')); ?>,<?php echo json_encode(lang('common_error')); ?>);
									}
									else
									{
										show_feedback('success', <?php echo json_encode(lang('common_cron_success_qb')); ?>, <?php echo json_encode(lang('common_success')); ?>);
									}
									
									$('#sync_qb').parents('.form-group').addClass('has-success').removeClass('has-error');
									
								  $('#sync_qb_button_icon').toggleClass("glyphicon-refresh-animate", false);
									
									setTimeout(function(){
										$("#quickbooks_sync_progress").toggleClass("hidden", true);
									}, 1000);
									
									$("#quickbooks_sync_date").val(<?php echo json_encode(lang('common_just_now')); ?>);
							  }
							  else{
								  show_feedback('error', data.message);
									$('#sync_qb').parents('.form-group').removeClass('has-success').addClass('has-error');
									
									$('#sync_qb_button_icon').toggleClass("glyphicon-refresh-animate", false);
									$("#quickbooks_sync_progress").toggleClass("hidden", true);
									
							  }
						  },
						  error: function() {
							  show_feedback('error', <?php echo json_encode(lang('common_access_denied')); ?>);
								$('#sync_qb').parents('.form-group').removeClass('has-success').addClass('has-error');
							  $('#sync_qb_button_icon').toggleClass("glyphicon-refresh-animate", false);
								$("#quickbooks_sync_progress").toggleClass("hidden", true);
						  }
					   });
						 						
					});
	   		}
		}
	});
});


$("#item_lookup_order_list").sortable();

var checklist_ecom = <?php echo json_encode(unserialize($this->config->item('ecommerce_cron_sync_operations'))); ?>;

$(function () {
	$group = $('.ecommerce_cron_sync_operations .list-group.checked-list-box');
    $group.find('.list-group-item').each(function () {
        // Settings
        var $widget = $(this),
            $checkbox = $('<input type="checkbox" class="hidden" />'),
			value = ($widget.data('value') ? $widget.data('value') : '1'),
            color = ($widget.data('color') ? $widget.data('color') : "primary"),
            style = ($widget.data('style') == "button" ? "btn-" : "list-group-item-"),
            settings = {
                on: {
                    icon: 'glyphicon glyphicon-check'
                },
                off: {
                    icon: 'glyphicon glyphicon-unchecked'
                }
            };

		$widget.css('cursor', 'pointer');
		$checkbox.val(value).attr('name', $group.data('name'));
        $widget.append($checkbox);

        // Event Handlers
        $widget.on('click', function () {
            $checkbox.prop('checked', !$checkbox.is(':checked'));
            $checkbox.triggerHandler('change');
        });

        $checkbox.on('change', function () {
            updateDisplay();
        });

        // Actions
        function updateDisplay() {
            var isChecked = $checkbox.is(':checked');
            // Set the button's state
            $widget.data('state', (isChecked) ? "on" : "off");
            // Set the button's icon
            $widget.find('.state-icon')
                .removeClass()
                .addClass('state-icon ' + settings[$widget.data('state')].icon);

            // Update the button's color
            if (isChecked) {
                $widget.addClass(style + color);
            } else {
                $widget.removeClass(style + color);
			}

			if (isChecked) {	
				if(typeof $widget.data('requires') == 'object')
				{
					$.each($widget.data('requires'), function(key, value)
					{
						$(":checkbox[value="+value+"]").prop("checked",true).trigger('change');
					});
				}
			} else {
				$group.find('.list-group-item').each(function(){
					if(typeof $(this).data('requires') == 'object')
					{
						var that = this;
						$.each($(this).data('requires'), function(key, value) {
							
							if(value == $widget.data('value'))
							{
								$(that).find(":checkbox").prop("checked",false).trigger('change');
							}
						});
					}
				});
			}
        }

        // Initialization
        function init() {
			if($.inArray($widget.data('value'), checklist_ecom) !== -1)
			{
				$widget.data('checked', true);
			}
					
          	if ($widget.data('checked') == true) {
              	$checkbox.prop('checked', !$checkbox.is(':checked'));
          	}
          	updateDisplay();

          	// Inject the icon if applicable
          	if ($widget.find('.state-icon').length == 0) {
              	$widget.prepend('<span class="state-icon ' + settings[$widget.data('state')].icon + '"></span>');
          	}
        }
        init();
    });
});

	$("#tax_classes tbody").sortable();

	var tax_class_index = -1;

	$(document).on('click', '.add_tax_class', function(e) {
		var $tbody = $("#tax_classes").find("tbody");

		var tax_rate_index = 0;
											
		var checkbox_template = '<input type="hidden" name="taxes['+ tax_class_index +'][cumulative][]" value="0" /><input disabled data-index="-1" type="checkbox" class="taxes invisible" id="tax_rate_'+ tax_class_index +'_0_cumulative" name="taxes['+ tax_class_index +'][cumulative][]">'
		+ '<label class="tax_class_cumulative_element invisible" for="tax_rate_'+ tax_class_index +'_0_cumulative"><span></span></label>';

		var radio_template = '<input data-index="-1" type="radio" id="tax_class_'+ tax_class_index +'_0_default" name="tax_class_id" value="'+tax_class_index+'">'
		+ '<label class="tax_class_default_element" for="tax_class_'+ tax_class_index +'_0_default"><span></span></label>';


		$("#tax_classes").find("tbody").append('<tr data-index="' + tax_class_index +'">' +
			'<td class="tax_class_name top">' +
				'<input type="text" data-index="-1" class="rates form-control tax_classes" name="tax_classes['+ tax_class_index +'][name]" value="" />' +
			'</td>' +
			'<td class="tax_class_rate_name top">' +
				'<input data-index="-1" data-tax-class-id="-1" type="text" class="rates form-control tax_classes" name="taxes['+ tax_class_index +'][name][]" />' +
			'</td>' +
			'<td class="tax_class_rate_percent top">' +
				'<input data-index="-1" data-tax-class-id="-1" type="text" class="rates form-control tax_classes" name="taxes['+ tax_class_index +'][percent][]" />' +
			'</td>' +
			'<td class="tax_class_rate_cumulative top">' + 
				checkbox_template + 
			'</td>' +
			'<td class="tax_class_rate_default">' + 
				radio_template + 
			'</td>' +
			'<td>' +
				'<a class="delete_tax_rate tax_table_rate_text_element"><?php echo lang('common_delete'); ?></a>' +
			'</td>' +
			'<td>' +
				'<a href="javascript:void(0);" class="add_tax_rate"><?php echo lang('config_add_rate'); ?></a>' +
			'</td>' +
			'<td>' +
				'&nbsp;' +
			'</td>' +
			'<td>' +
				'<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' +
			'</td>' +
		'</tr>');

		tax_class_index -= 1;
	});

	$(document).on('click', '.delete_tax_rate', function(e) {
		var $tr = $(this).closest("tr");

		var tax_class_index = $tr.data('index');
		var tax_rate_index = $tr.find('td.tax_class_rate_name > input').length;
						
		if(tax_rate_index > 1)
		{
			var tax_class_tax_id = parseInt($tr.find('.tax_class_rate_name input').last().data('tax-class-tax-id'));
			$tr.find('.tax_class_rate_name input').last().remove();
			$tr.find('.tax_class_rate_percent input').last().remove();
			$tr.find('.tax_class_rate_cumulative input').last().remove();

			if(tax_class_tax_id > 0)
			{
				$("#config_form").append('<input type="hidden" name="taxes_to_delete[]" value="'+ tax_class_tax_id +'" />');
			}
		}
		else
		{

			$tr.remove();
			
			if(tax_class_index > 0)
			{
				$("#config_form").append('<input type="hidden" name="tax_classes_to_delete[]" value="'+ tax_class_index +'" />');
			}
		}
	});

	$(document).on('click', '.add_tax_rate', function(e) {
			var $tr = $(this).closest("tr");
			var tax_class_index = $tr.data('index');
			var tax_rate_index = $tr.find('td.tax_class_rate_name > input').length;
																
			$tr.find('.tax_class_rate_name').append('<input data-index="-1" type="text" data-tax-class-tax-id="-1" class="rates form-control taxes" name="taxes['+ tax_class_index +'][name][]" >');
			$tr.find('.tax_class_rate_percent').append('<input data-index="-1" type="text" class="rates form-control taxes" name="taxes['+ tax_class_index +'][percent][]" >');
			
			if(tax_rate_index == 1)
			{
				var checkbox_template = '<input data-index="-1" type="checkbox" class="taxes" id="tax_rate_'+ tax_class_index +'_'+tax_rate_index+'_cumulative" name="taxes['+ tax_class_index +'][cumulative][]">'
				+ '<label class="tax_class_cumulative_element" for="tax_rate_'+ tax_class_index +'_'+tax_rate_index+'_cumulative"><span></span></label>';
				$tr.find('.tax_class_rate_cumulative').append(checkbox_template);
			} else {
				var checkbox_template = '<input type="hidden" name="taxes['+ tax_class_index +'][cumulative][]" value="0" /><input disabled data-index="-1" type="checkbox" class="taxes invisible" id="tax_rate_'+ tax_class_index +'_'+tax_rate_index+'_cumulative" name="taxes['+ tax_class_index +'][cumulative][]">'
				+ '<label class="tax_class_cumulative_element invisible" for="tax_rate_'+ tax_class_index +'_'+tax_rate_index+'_cumulative"><span></span></label>';
				$tr.find('.tax_class_rate_cumulative').append(checkbox_template);
			}
								
		});
		
		//delivery stuff
		$("#shipping_zones tbody").sortable();
		
		$('.shipping_zone_zips input').selectize({
			delimiter: '|',
			create: true,
			render: {
		      option_create: function(data, escape) {
					var add_new = <?php echo json_encode(lang('common_add_value')) ?>;
		        return '<div class="create">'+escape(add_new)+' <strong>' + escape(data.input) + '</strong></div>';
		      }
			},
		});

		var zone_index = -1;
		
		$(document).on('click', '.add_shipping_zone', function(e) {
			
			var $tbody = $("#shipping_zones").find("tbody");
			
			$tbody.append('<tr data-index="' + zone_index +'">' +
				'<td class="shipping_zone_name top">' +
					'<input type="text" data-index="-1" class="zones form-control name" name="zones['+ zone_index +'][name]" value="" />' +
				'</td>' +
				'<td class="shipping_zone_zips top">' +
					'<input type="text" data-index="-1" class="zones form-control name" name="zones['+ zone_index +'][zips]" value="" />' +
				'</td>' +
				'<td class="shipping_zone_fee top">' +
					'<input data-index="-1" type="text" class="zones form-control fee" name="zones['+ zone_index +'][fee]" />' +
			'</td>');
			
			$tr = $tbody.find('tr').last();
			
			$tr.find('.shipping_zone_zips input').selectize({
				delimiter: '|',
				create: true,
				render: {
			      option_create: function(data, escape) {
						var add_new = <?php echo json_encode(lang('common_add_value')) ?>;
			        return '<div class="create">'+escape(add_new)+' <strong>' + escape(data.input) + '</strong></div>';
			      }
				},
			});
			
			var tax_groups = <?php echo json_encode($tax_groups); ?>
						
			var tax_group_select = $('<select>').addClass('zones form-control').attr('name', 'zones['+ zone_index +'][tax_class_id]').attr('data-index', zone_index);
			$('<td class="shipping_zone_tax_group top" >').append(tax_group_select).appendTo($tr);
			
			$(tax_groups).each(function() {
			 tax_group_select.append($("<option>").attr('value',this.val).text(this.text));
			});
			
			$tr.append(
				'<td>' +
					'<a class="delete_rate"><?php echo lang('common_delete'); ?></a>' +
				'</td>' +
				'<td>' +
					'<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' +
				'</td>'
				);
				
				zone_index --;
			});
		
		$("#shipping_providers tbody").sortable();
		
		
		var provider_index = -1;
		
		$(document).on('click', '.add_shipping_provider', function(e) {
			var $tbody = $("#shipping_providers").find("tbody");
			
			var rate_index = 0;
			var radio_template = '<input data-index="-1" type="radio" class="methods" id="default_shipping_rate_'+ provider_index +'_0" name="methods['+ provider_index +'][is_default][]" checked="checked">'
			+ '<label class="shipping_table_rate_element" for="default_shipping_rate_'+ provider_index +'_0"><span></span></label>';
			
			$tbody.append('<tr data-index="' + provider_index +'">' +
				'<td class="shipping_provider_name top">' +
					'<input type="text" data-index="-1" class="rates form-control providers" name="providers['+ provider_index +'][name]" value="" />' +
				'</td>' +
				'<td class="delivery_rate_name top">' +
					'<input data-index="-1" data-method-id="-1" type="text" class="rates form-control methods" name="methods['+ provider_index +'][name][]" />' +
				'</td>' +
				'<td class="delivery_fee top">' + 
					'<input type="text" data-index="-1" class="rates form-control methods" name="methods['+ provider_index +'][fee][]" />' +
				'</td>' +
				'<td class="delivery_time top">' +
					'<input type="text" data-index="-1" class="rates form-control methods" name="methods['+ provider_index +'][time_in_days][]" />' +
				'</td>' +
				'<td class="delivery_default top">' + 
					radio_template + 
				'</td>' +
				'<td>' +
					'<a class="delete_rate shipping_table_rate_text_element"><?php echo lang('common_delete'); ?></a>' +
				'</td>' +
				'<td>' +
					'<a href="javascript:void(0);" class="add_delivery_rate"><?php echo lang('config_add_rate'); ?></a>' +
				'</td>' +
				'<td>' +
					'<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' +
				'</td>' +
			'</tr>');
			
			provider_index -= 1;
		});
		
		$(document).on('click', '.delete_zone', function(e) {
			var $tr = $(this).closest("tr");
			var index = $tr.data('index');
						
			$tr.remove();
			
			if(index > 0)
			{
				$("#config_form").append('<input type="hidden" class="delete_zone" name="zones_to_delete[]" value="'+ index +'" />');
			}
		});
		
		$(document).on('click', '.delete_rate', function(e) {
			var $tr = $(this).closest("tr");
			
			var index = $tr.data('index');
			var rate_index = $tr.find('td.delivery_rate_name > input').length;
			var method_id = parseInt($tr.find('.delivery_rate_name input').last().data('method-id'));
			
			if(rate_index > 1)
			{
				$tr.find('.delivery_rate_name input').last().remove();
				$tr.find('.delivery_fee input').last().remove();
				$tr.find('.delivery_time input').last().remove();
				$tr.find('.delivery_default input').last().remove();
				$tr.find('.delivery_default label').last().remove();
				
				if(method_id > 0)
				{
					$("#config_form").append('<input type="hidden" class="delete_method" name="methods_to_delete[]" value="'+ method_id +'" />');
				}
			}
			else
			{
				
				$tr.remove();
				
				if(method_id > 0)
				{
					$("#config_form").append('<input type="hidden" class="delete_method" name="methods_to_delete[]" value="'+ method_id +'" />');
				}
										
				if(index > 0)
				{
					$("#config_form").append('<input type="hidden" class="delete_provider" name="providers_to_delete[]" value="'+ index +'" />');
				}
			}
		});
		
		$(document).on('click', '.add_delivery_rate', function(e) {
				var $tr = $(this).closest("tr");
				var index = $tr.data('index');
				var rate_index = $tr.find('td.delivery_rate_name > input').length;
																				
				$tr.find('.delivery_rate_name').append('<input data-index="-1" type="text" data-method-id="-1" class="rates form-control methods" name="methods['+ index +'][name][]" >');
				$tr.find('.delivery_fee').append('<input data-index="-1" type="text" class="rates form-control methods" name="methods['+ index +'][fee][]" >');
				$tr.find('.delivery_time').append('<input data-index="-1" type="text" class="rates form-control methods" name="methods['+ index +'][time_in_days][]" >');
				
				var radio_template = '<input data-index="-1" type="radio" class="methods" id="default_shipping_rate_'+ index +"_"+ rate_index + '" name="methods['+ index +'][is_default][]">'
				+ '<label class="shipping_table_rate_element" for="default_shipping_rate_'+ index +"_"+ rate_index + '"><span></span></label>';
				
				$tr.find('.delivery_default').append(radio_template);
												
			});

			$("#verify_age_for_products").click(function()
			{
				if ($('#verify_age_for_products').prop('checked'))
				{
					$("#default_age_input_container").removeClass('hidden');	
					$("#strict_age_format_check_container").removeClass('hidden');
				}
				else
				{
					$("#default_age_input_container").addClass('hidden');
					$("#strict_age_format_check_container").addClass('hidden');
				}
	
			});



			$("#reset_quickbooks").click(function(event)
			{
				bootbox.confirm(<?php echo json_encode(lang('config_confirm_reset_qb')); ?>, function(response)
				{
					if (response)
					{
						$.getJSON(<?php echo json_encode(site_url('config/reset_qb')); ?>,function(response)
						{
							if (response.success)
							{
								show_feedback('success',response.message,<?php echo json_encode(lang('common_success')); ?>);
								
								setTimeout(function(){ window.location.reload(); },3000);
								
							}
						});
					}
				});
			});
			
			
			var checklist_qb = <?php echo json_encode(unserialize($this->config->item('qb_sync_operations'))); ?>;

			$(function () {
					$group = $('.qb_sync_operations .list-group.checked-list-box');
			    $group.find('.list-group-item').each(function () {
			        // Settings
			        var $widget = $(this),
			            $checkbox = $('<input type="checkbox" class="hidden" />'),
									value = ($widget.data('value') ? $widget.data('value') : '1'),
			            color = ($widget.data('color') ? $widget.data('color') : "primary"),
			            style = ($widget.data('style') == "button" ? "btn-" : "list-group-item-"),
			            settings = {
			                on: {
			                    icon: 'glyphicon glyphicon-check'
			                },
			                off: {
			                    icon: 'glyphicon glyphicon-unchecked'
			                }
			            };
           					 
							$widget.css('cursor', 'pointer');
							$checkbox.val(value).attr('name', $group.data('name'));
			        $widget.append($checkbox);

			        // Event Handlers
			        $widget.on('click', function () {
			            $checkbox.prop('checked', !$checkbox.is(':checked'));
			            $checkbox.triggerHandler('change');
			        });
				
			        $checkbox.on('change', function () {
			            updateDisplayQB();
			        });
          

			        // Actions
			        function updateDisplayQB() {
						
			            var isChecked = $checkbox.is(':checked');

			            // Set the button's state
			            $widget.data('state', (isChecked) ? "on" : "off");

			            // Set the button's icon
			            $widget.find('.state-icon')
			                .removeClass()
			                .addClass('state-icon ' + settings[$widget.data('state')].icon);

			            // Update the button's color
			            if (isChecked) {
			                $widget.addClass(style + color);
			            } else {
			                $widget.removeClass(style + color);
			            }
						
									if (isChecked) {	
										if(typeof $widget.data('requires') == 'object')
										{
											$.each($widget.data('requires'), function(key, value)
											{
												$(":checkbox[value="+value+"]").prop("checked",true).trigger('change');
											});
										}
									} else {
										$group.find('.list-group-item').each(function(){
											if(typeof $(this).data('requires') == 'object')
											{
												var that = this;
												$.each($(this).data('requires'), function(key, value) {
										
													if(value == $widget.data('value'))
													{
														$(that).find(":checkbox").prop("checked",false).trigger('change');
													}
												});
											}
										});
									}

			        }

			        // Initialization
			        function initQB() {
						var checkboxValue = $widget.data('value');
						if($.inArray(checkboxValue, checklist_qb) !== -1)
						{
							$widget.data('checked', true);
						}
					
			          if ($widget.data('checked') == true) {
			              $checkbox.prop('checked', !$checkbox.is(':checked'));
			          }
            
			          updateDisplayQB();

			          // Inject the icon if applicable
			          if ($widget.find('.state-icon').length == 0) {
			              $widget.prepend('<span class="state-icon ' + settings[$widget.data('state')].icon + '"></span>');
			          }
			        }
			        initQB();
			    });
			});

			$("#indicate_non_taxable_on_receipt").change(function(){
				if(this.checked) {
					$("#override_symbol_non_taxable_container").show();
				}else{
					$("#override_symbol_non_taxable_container").hide();
				}
			});

			$("#taxes_summary_on_receipt").change(function(){
				if(this.checked){
					$("#override_symbol_taxable_summary_container").show();
					$("#override_symbol_non_taxable_summary_container").show();
				}else{
					$("#override_symbol_taxable_summary_container").hide();
					$("#override_symbol_non_taxable_summary_container").hide();
				}
			});

			$('#work_order_device_locations').selectize({
				delimiter: ',',
				persist: false,
				create: function(input) {
					return {
						value: input,
						text: input
					}
				}
			});	

			// woo commerce oauth
			$('#woo_oauth').on('click', function() {
				var href = '<?php echo site_url("config/generate_woo_oauth_url");?>';
				$.ajax({
					type: "POST",
					url: href,
					data: {
						'woo_url': $('#woo_api_url').val()
					},
					dataType: 'json',
					success: function(response) {
						var newWindow = window.open(response.url, "_blank", "width=800, height=600");
                        newWindow.focus();
					},
					error: function(xhr, status, error) {
                        console.log(error);
                    	show_feedback('error', 'Could not connect to WooCommerce. Please try again later.');
                    }
				});
			});

			$('.config-nav > li > a').on('click', function() {
				var id = $(this).attr('href');
				var panelCollapse = $(id).find('.panel-collapse');
				
				panelCollapse.collapse('show')

				$('html, body').animate({ scrollTop: 0 }, 'normal');
			})
</script>

<?php $this->load->view("partial/footer"); ?>