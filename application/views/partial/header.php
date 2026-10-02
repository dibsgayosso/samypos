<!DOCTYPE html>
<html class="<?php echo $this->config->item('language');?>">
<head>
	<script>
		//OAuth for square appends this, we need to reset to prevent issue with jquery
		if (window.location.hash == "#_=_")
		  window.location.hash = "";
	</script>
	<meta charset="UTF-8" />
    <title><?php 
		 $this->load->helper('demo');
	 	 $company = ($company = $this->Location->get_info_for_key('company')) ? $company : $this->config->item('company');
		 echo !is_on_demo_host() ?  $company.' -- '.lang('common_powered_by').' '.$this->config->item('branding')['name'] : 'Demo - '.$this->config->item('branding')['name'].' | Easy to use Online POS Software' ?></title>
	<link rel="icon" href="<?php echo base_url();?>favicon_<?php echo $this->config->item('branding_code');?>.ico" type="image/x-icon"/>	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/> <!--320-->
	<base href="<?php echo base_url();?>" />
	
	<style>
		<?php if ($this->agent->browser() == 'Chrome') { ?>
			@page {
				margin: 0;
				padding: 0;
			}
		<?php } ?>
			
		@media print {
			.invoice-table-content{ page-break-inside: avoid !important; -webkit-page-break-inside: avoid !important; }
			.panel_inventory_print_list .panel-body{padding:40px 30px !important; border: 0 !important;}
			.panel_inventory_print_list .report-header{display:block !important;}
		}

		<?php if($this->config->item('add_ck_editor_to_item')){?>
				.ck-editor__editable_inline {
					min-height: 250px;
				}
		<?php } ?>
	</style>

	<link rel="icon" href="<?php echo base_url();?>favicon_<?php echo $this->config->item('branding_code');?>.ico" type="image/x-icon"/>
	<script type="text/javascript">
		
		var APPLICATION_VERSION= "<?php echo APPLICATION_VERSION; ?>";
		var SITE_URL= "<?php echo site_url(); ?>";
		var BASE_URL= "<?php echo base_url(); ?>";
		var ENABLE_SOUNDS = <?php echo $this->config->item('enable_sounds') ? 'true' : 'false'; ?>;
		var JS_DATE_FORMAT = <?php echo json_encode(get_js_date_format()); ?>;
		var JS_TIME_FORMAT = <?php echo json_encode(get_js_time_format()); ?>;
		var LOCALE =  <?php echo json_encode(get_js_locale()); ?>;
		var MONEY_NUM_DECIMALS = <?php echo $this->config->item('number_of_decimals') ? (int)$this->config->item('number_of_decimals') : 2; ?>;
		var IS_MOBILE = <?php echo $this->agent->is_mobile() ? 'true' : 'false'; ?>;
		var ENABLE_QUICK_EDIT = <?php echo $this->config->item('enable_quick_edit') ? 'true' : 'false'; ?>;
		var PER_PAGE = <?php echo json_encode($this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20); ?>;
		var EMPLOYEE_PERSON_ID = <?php echo json_encode((!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->Employee->get_logged_in_employee_info()->person_id);?>;
		var INVOICE_NO =  <?php echo json_encode(substr((date('mdy')).(time() - strtotime("today")).($this->Employee->get_logged_in_employee_info()->person_id), 0, 16)); ?>;
		var CONFIRM_CLONE = <?php echo json_encode(lang('common_confirm_clone')); ?>;
		var CONFIRM_IMAGE_DELETE = <?php echo json_encode(lang('common_confirm_image_delete')); ?>;
	</script>
	<?php 
	$this->load->helper('assets');
	foreach(get_css_files() as $css_file) { ?>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url().$css_file['path'].'?'.ASSET_TIMESTAMP;?>" />
	<?php } ?>
	<?php foreach(get_js_files() as $js_file) { ?>
		<script src="<?php echo base_url().$js_file['path'].'?'.ASSET_TIMESTAMP;?>" type="text/javascript" charset="UTF-8"></script>
	<?php } ?>	

	<?php if($this->config->item('add_ck_editor_to_item')){?>
		<script src="<?php echo base_url().'assets/js/ckeditor/ckeditor.js?'.ASSET_TIMESTAMP;?>" type="text/javascript" charset="UTF-8"></script>
	<?php } ?>	

	<script type="text/javascript">
		
		<?php
		$week_start_day = $this->config->item('week_start_day') ? $this->config->item('week_start_day') : 'monday';
		
		$dow = $week_start_day == 'monday' ? 1 : 0;
		?>
		moment.locale(LOCALE, {
		  week: { dow: <?php echo $dow; ?> }
		});
		
		var SCREEN_WIDTH = $(window).width();
		var SCREEN_HEIGHT = $(window).height();
		COMMON_SUCCESS = <?php echo json_encode(lang('common_success')); ?>;
		COMMON_ERROR = <?php echo json_encode(lang('common_error')); ?>;
		
		bootbox.addLocale('ar', {
		    OK : 'حسنا',
		    CANCEL : 'إلغاء',
		    CONFIRM : 'تأكيد'			
		});
		
		bootbox.addLocale('km', {
		    OK :'យល់ព្រម',
		    CANCEL : 'បោះបង់',
		    CONFIRM : 'បញ្ជាក់ការ'			
		});
		bootbox.setLocale(LOCALE);		
		var RATE_LIMIT_IN_MS = 60*1000;
		var NUMBER_OF_REQUESTS_ALLOWED = 120;
		var NUMBER_OF_REQUESTS = 0;
		
		setInterval(function()
		{ 
			NUMBER_OF_REQUESTS = 0;
			
		}, RATE_LIMIT_IN_MS);
		
		$.ajaxSetup ({
			cache: false,
			headers: { "cache-control": "no-cache" },
		  beforeSend: function canSendAjaxRequest()
			{
				var can_send = NUMBER_OF_REQUESTS < NUMBER_OF_REQUESTS_ALLOWED;
				NUMBER_OF_REQUESTS++;
				return can_send;
			}
		});
		
		$(document).on('show.bs.modal','.bootbox.modal', function (e) 
		{
			var isShown = ($(".bootbox.modal").data('bs.modal') || {}).isShown;
			//If we have a dialog already don't open another one
			if (isShown)
			{
				//Cleanup the dialog(s) that was added to dom
				$('.bootbox.modal:not(:first)').remove();
				
				//Prevent double modal from showing up
				return e.preventDefault();
			}
		});
		
		
		toastr.options = {
		  "closeButton": true,
		  "debug": false,
		  "newestOnTop": false,
		  "progressBar": false,
		  "positionClass": "toast-top-right",
		  "preventDuplicates": false,
		  "onclick": null,
		  "showDuration": "300",
		  "hideDuration": "1000",
		  "timeOut": "5000",
		  "extendedTimeOut": "1000",
		  "showEasing": "swing",
		  "hideEasing": "linear",
		  "showMethod": "fadeIn",
		  "hideMethod": "fadeOut"
		}
		
    $.fn.editableform.buttons = 
      '<button tabindex="-1" type="submit" class="btn btn-primary btn-sm editable-submit">'+
        '<i class="icon ti-check"></i>'+
      '</button>'+
      '<button tabindex="-1" type="button" class="btn btn-default btn-sm editable-cancel">'+
        '<i class="icon ti-close"></i>'+
      '</button>';
	  
 	  $.fn.editable.defaults.emptytext = <?php echo json_encode(lang('common_empty')); ?>;
	//https://github.com/OwlCarousel2/OwlCarousel2/issues/1374
	// Disabling bs transitions makes the modals show again:
	// $.support.transition = false
	// https://getbootstrap.com/docs/3.3/javascript/#transitions
	// DO NOT REMOVE THIS
	$.support.transition = false;
		
		$(document).ready(function()
		{
				<?php if ($this->config->item('hover_to_expand_sub_modules') == 'true') { ?>
				$("#mainMenu .has_sub_menu").hover(
					function(){
						$(this).children('.collapse').show();
					},
					function(){
						$(this).children('.collapse').hide();
					}
				);
				<?php } ?>
				$(".wrapper.mini-bar .left-bar").hover(
				   function() {
				     $(this).parent().removeClass('mini-bar');
				   }, function() {
				     $(this).parent().addClass('mini-bar');
				   }
				 );
			
			

	    $('.menu-bar').click(function(e){                  
	    	e.preventDefault();
	        $(".wrapper").toggleClass('mini-bar');        
	    }); 
    					
			//Ajax submit current location
			$(".set_employee_current_location_id").on('click',function(e)
			{
				e.preventDefault();

				var location_id = $(this).data('location-id');
				$.ajax({
				    type: 'POST',
				    url: '<?php echo site_url('home/set_employee_current_location_id'); ?>',
				    data: { 
				        'employee_current_location_id': location_id, 
				    },
				    success: function(){
				    	window.location.reload(true);	
				    }
				});
				
			});
			
			$(".set_employee_language").on('click',function(e)
			{
				e.preventDefault();

				var language_id = $(this).data('language-id');
				$.ajax({
				    type: 'POST',
				    url: '<?php echo site_url('employees/set_language'); ?>',
				    data: { 
				        'employee_language_id': language_id, 
				    },
				    success: function(){
				    	window.location.reload(true);	
				    }
				});
				
			});
			
			<?php
			$this->load->helper('update');
			if (!is_on_phppos_host())
			{
				//If we are using on browser close (NULL or ""; both false) then we want to keep session alive
				if ($this->db->table_exists('app_config') && !$this->Appconfig->get_raw_phppos_session_expiration())
				{		
					?>
					//Keep session alive by sending a request every 5 minutes
					setInterval(function(){$.get('<?php echo site_url('home/keep_alive'); ?>');}, 300000);
					<?php } ?>
			<?php } ?>
		});
	</script>
<?php
$this->load->helper('demo');
if (is_on_demo_host()) { ?>		
	<script src="//<?php echo $this->config->item('branding')['domain']; ?>/js/iframeResizer.contentWindow.min.js"></script>
<?php } ?>
<!-- <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script> -->
</head>
<body>
	<div class="modal fade hidden-print" id="myModal" tabindex="-1" role="dialog" aria-hidden="true"></div>
	<div class="modal fade hidden-print" id="myModalDisableClose" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static"></div>
	
	<div class="wrapper <?php echo $this->uri->segment(1)=='sales' || $this->uri->segment(1)=='receivings' || $this->config->item('always_minimize_menu')  ? 'mini-bar sales-bar' : ''; ?>">
		<div class="left-bar hidden-print" >
			<div class="admin-logo" style="<?php echo isset($location_color) && $location_color ? 'background-color: '.$location_color.' !important': ''; ?>">
				<div class="logo-holder pull-left">
					<?php echo img(
					array(
						'src' => base_url().$this->config->item('branding')['logo_path'],
						'class'=>'hidden-print logo',
						'id'=>'header-logo',

					)); ?>
				</div>
				<!-- logo-holder -->
				<?php  

				?>			
			</div>
			<!-- admin-logo -->

			<ul class="list-unstyled menu-parent" id="mainMenu">
			
				
				<li  <?php echo $this->uri->segment(1)=='home' && $this->uri->segment(2)!='payvantage'  ? 'class="active home"' : 'class="home"'; ?>>
					<a tabindex = "-1" href="<?php echo site_url('home'); ?>" class="waves-effect waves-light">
						<i class="icon ti-dashboard"></i>
						<span class="text"><?php echo lang('common_dashboard'); ?></span>
					</a>
				</li>
				
				<?php
				if ($this->config->item('payvantage'))
				{?>
					<li <?php echo $this->uri->segment(2)=='payvantage'  ? 'class="active home"' : 'class="home"'; ?>>
						<a tabindex = "-1" href="<?php echo site_url('home/payvantage'); ?>" class="waves-effect waves-light">
							<i class="icon ti-mobile"></i>
							<span class="text">PayVantage</span>
						</a>
					</li>
				<?php
				}
				?>


				<?php
				foreach($allowed_modules->result() as $module) { 

					if ($module->module_id == 'invoices') {
						$invoices = true;
						$active_invoice_class = '';
						if ($this->uri->segment(1) == 'invoices' && $this->uri->segment(3) == 'customer' || $this->uri->segment(3) == 'supplier') {
							$active_invoice_class = 'show';
						}
					}
					
					if ($module->module_id == 'customers') {
						$customers = true;
						$active_customers_class = '';
						if ($this->uri->segment(1) == 'customers' || $this->uri->segment(1) == 'customer_subscriptions') {
							$active_customers_class = 'show';
						}
					}
					


					if (array_search('items', $disable_modules) === false || array_search('item_kits', $disable_modules) === false || array_search('price_rules', $disable_modules) === false) {

						if ($module->module_id == 'items' || $module->module_id == 'item_kits' || $module->module_id == 'manage_categories' || $module->module_id == 'manage_manufacturers' || $module->module_id == 'manage_tags' || $module->module_id == 'price_rules') {


							$inventory 		= true;

							$active_submenu_link = '';
							$active_submenu 		 = '';

							if ($this->uri->segment(1) == 'items' || $this->uri->segment(1) == 'item_kits' || $this->uri->segment(2) == 'manage_categories' || $this->uri->segment(2) == 'manage_manufacturers' || $this->uri->segment(2) == 'manage_modifiers' || $this->uri->segment(2) == 'manage_tags' || $this->uri->segment(2) == 'manage_attributes' || $this->uri->segment(1) == 'price_rules') {

								$active_submenu 			= 'show';
								$active_submenu_link 	= 'active_submenu_link';
							}
						}
					}
				}


				if (isset($inventory) && $inventory == true) { ?>
					<li class="has_sub_menu">
						
						<a tabindex = "-1" href=""  class="waves-effect waves-light" data-toggle="collapse" aria-expanded="false">

							<i class="icon ti-harddrive"></i>
							<span class="text"><?php echo lang("common_inventory") ?></span>
							<span class="dropdown pull-right-container">
                    <i class="icon ti-angle-down"></i>
                </span>
						</a>
					<ul class="collapse list-unstyled <?php echo $active_submenu ? $active_submenu : '' ?>" >
							<?php if ($this->Employee->has_module_permission('items', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
								<li class="<?php echo $active_submenu_link;?>" <?php echo array_search('items', $disable_modules) === false ? '': 'style="display: none;"' ?>>
									<a href="<?php echo site_url('items'); ?>"><i class="icon ti-harddrive"></i>
										<?php echo lang("module_items"); ?>
									</a>
								</li>
							<?php } ?>

							<?php if ($this->Employee->has_module_permission('item_kits', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
								<li <?php echo array_search('item_kits', $disable_modules) === false ? '': 'style="display: none;"' ?>>
									<a href="<?php echo site_url('item_kits'); ?>"><i class="icon ti-harddrives"></i>
										<?php echo lang("module_item_kits"); ?>
									</a>
								</li>
							<?php } ?>

							<?php if ($this->Employee->has_module_action_permission('items', 'manage_categories', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
								<li <?php echo array_search('items', $disable_modules) === false ? '': 'style="display: none;"' ?>>
									<a href="<?php echo site_url('items/manage_categories'); ?>"><i class="ion-ios-folder-outline"></i>
										<?php echo lang("reports_categories"); ?>
									</a>
								</li>
							<?php } ?>


							<?php if ($this->Employee->has_module_action_permission('items', 'manage_manufacturers', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
								<li <?php echo array_search('items', $disable_modules) === false ? '': 'style="display: none;"' ?>>
									<a href="<?php echo site_url('items/manage_manufacturers'); ?>"><i class="ion-settings"></i>
										<?php echo lang("reports_manufacturers"); ?>
									</a>
								</li>
							<?php } ?>

							<?php if ($this->Employee->has_module_permission('items', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
							<li <?php echo array_search('items', $disable_modules) === false ? '': 'style="display: none;"' ?>>
								<a href="<?php echo site_url('items/manage_modifiers'); ?>"><i class="ion-android-list"></i>
									<?php echo lang("common_modifiers"); ?>
								</a>
							</li>
							<?php } ?>
							<?php if ($this->Employee->has_module_action_permission('items', 'manage_tags', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
								<li <?php echo array_search('items', $disable_modules) === false ? '': 'style="display: none;"' ?>>
									<a href="<?php echo site_url('items/manage_tags'); ?>"><i class="ion-ios-pricetag-outline"></i>
										<?php echo lang("common_tags"); ?>
									</a>
								</li>
							<?php } ?>
							<?php if ($this->Employee->has_module_permission('items', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
							<li <?php echo array_search('items', $disable_modules) === false ? '': 'style="display: none;"' ?>>
								<a href="<?php echo site_url('items/manage_attributes'); ?>"><i class="ion-ios-toggle-outline"></i>
									<?php echo lang("common_attributes"); ?>
								</a>
							</li>
							<?php } ?>
							<?php if ($this->Employee->has_module_permission('price_rules', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
								<li <?php echo array_search('price_rules', $disable_modules) === false ? '': 'style="display: none;"' ?>>
									<a href="<?php echo site_url('price_rules'); ?>"><i class="ion-ios-pricetags-outline"></i>
										<?php echo lang("module_price_rules"); ?>
									</a>
								</li>
							<?php } ?>
							
							<li <?php echo array_search('items', $disable_modules) === false ? '': 'style="display: none;"' ?>>
								<a target="_blank" href="<?php echo site_url('items/price_check'); ?>"><i class="ion-cash"></i>
									<?php echo lang("common_price_check"); ?>
								</a>
							</li>
														

						</ul>
				</li>
				<?php }


				foreach($allowed_modules->result() as $module) { 
				if ($module->module_id == 'invoices') { ?>
					<li class="has_sub_menu">
						
						<a tabindex = "-1" href=""  class="waves-effect waves-light" data-toggle="collapse" aria-expanded="false">
							<i class="icon ti-receipt"></i>
							<span class="text"><?php echo lang("module_invoices") ?></span>
							<span class="dropdown pull-right-container">
                    <i class="icon ti-angle-down"></i>
                </span>
						</a>
					<ul class="collapse list-unstyled <?php echo $active_invoice_class ? $active_invoice_class :'' ?>" >
						
						<li><a href="<?php echo site_url('invoices/index/customer'); ?>"><i class="icon ti-user"></i><?php echo lang('reports_customers')?></a></li>
						<li><a href="<?php echo site_url('invoices/index/supplier'); ?>"><i class="icon ti-download"></i><?php echo lang('reports_suppliers')?></a></li>

						<?php if($this->config->item('use_saudi_tax_config')){ 
							$location_id = $this->Employee->get_logged_in_employee_current_location_id();
							$location_zatca_config = $this->Appconfig->get_zatca_config($location_id);
							if($location_zatca_config){
						?>
							<li><a href="<?php echo site_url('invoices/zatca_invoice'); ?>"><i class="icon ti-sharethis"></i>ZATCA</a></li>
						<?php 
							}
						} 
						?>
					</ul>
				</li>
				
				<?php } 
				elseif ($module->module_id == 'customers') { ?>
					<li class="has_sub_menu">
						
						<a tabindex = "-1" href=""  class="waves-effect waves-light" data-toggle="collapse" aria-expanded="false">
							<i class="icon ti-user"></i>
							<span class="text"><?php echo lang("module_customers") ?></span>
							<span class="dropdown pull-right-container">
                    <i class="icon ti-angle-down"></i>
                </span>
						</a>
					<ul class="collapse list-unstyled <?php echo $active_customers_class ? $active_customers_class :'' ?>" >
						
							<li><a href="<?php echo site_url('customers'); ?>"><i class="icon ti-user"></i><?php echo lang('reports_customers')?></a></li>
		
						
							<li><a href="<?php echo site_url('customer_subscriptions'); ?>"><i class="icon ti-loop"></i><?php echo lang('common_subscriptions')?></a></li>
					</ul>
				</li>
				
				<?php
				}elseif ($module->module_id == 'items') { ?>

				<?php } elseif($module->module_id != 'item_kits' && $module->module_id != 'customers' && $module->module_id != 'price_rules') {?>


					<li <?php echo $module->module_id==$this->uri->segment(1)  ? 'class="active ' . $module->module_id . '"' : 'class="' . $module->module_id . '"'; ?>
						<?php echo array_search($module->module_id, $disable_modules) === false ? '': 'style="display: none;"' ?>>
						<a tabindex = "-1" href="<?php echo site_url("$module->module_id");?>"  class="waves-effect waves-light">
							<i class="<?php echo $module->icon; ?>"></i>
							<span class="text"><?php echo lang("module_".$module->module_id) ?></span>
						</a>
					</li>
				<?php 
				} } 
				?>
				<?php
				if ($this->config->item('timeclock')) 
				{
				?>
					<li <?php echo 'timeclocks'==$this->uri->segment(1)  ? 'class="active"' : ''; ?>>
						<a tabindex = "-1" href="<?php echo site_url("timeclocks");?>">
							<i class="icon ti-alarm-clock"></i>
							<span class="text"><?php echo lang("employees_timeclock") ?></span>
						</a>
					</li>				
				<?php
				}
				?>
				
                <li>
					<?php
					if ($this->config->item('track_payment_types') && $this->Register->is_register_log_open()) {
						$continue = $this->config->item('timeclock') && !$this->Employee->get_logged_in_employee_info()->not_required_to_clock_in ? 'timeclocks' : 'logout';
						echo anchor("sales/closeregister?continue=$continue",'<i class="icon ti-power-off"></i><span class="text">'.lang("common_logout").'</span>', array('tabindex' => '-1'));
					} else {
						
						if (!$this->Employee->get_logged_in_employee_info()->not_required_to_clock_in && $this->config->item('timeclock') && $this->Employee->is_clocked_in())
						{
							echo anchor("timeclocks",'<i class="icon ti-power-off"></i><span class="text">'.lang("common_logout").'</span>', array('tabindex' => '-1'));
						}
						else
						{
							echo anchor("home/logout",'<i class="icon ti-power-off"></i><span class="text">'.lang("common_logout").'</span>', array('tabindex' => '-1'));
						}
					}
					?>

                </li>
			</ul>
		</div>
		<!-- left-bar -->

		<div class="content" id="content">
		<div class="overlay hidden-print"></div>			
			<div class="top-bar hidden-print">				
				<nav class="navbar navbar-default top-bar">
					<div class="menu-bar-mobile" id="open-left"><i class="ti-menu"></i></div>
					<div class="nav navbar-nav top-elements navbar-breadcrumb hidden-xs">
						 <?php 
						 $this->load->helper('breadcrumb');
						 echo create_breadcrumb(); ?>
					</div>	

					<ul class="nav navbar-nav navbar-right top-elements">	
					<!-- Quick Header Buttons -->
					<?php  
					$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;


					?>
					<?php 
						if($this->Employee->has_module_permission('sales', $employee_id) || $this->Employee->has_module_permission('work_orders', $employee_id) || $this->Employee->has_module_permission('receivings', $employee_id) || $this->Employee->has_module_action_permission('customers', 'add_update', $employee_id) || $this->Employee->has_module_action_permission('items', 'add_update', $employee_id)) {

					?>

					<li class="quick_button dropdown btn-sm btn-quick">
							<a href="" class="dropdown-toggle	" data-toggle="dropdown" role="button" aria-expanded="false">
								<span class="avatar_info visible-sm visible-md visible-lg">
									<span class="ion-plus"></span> <span><?php echo lang('common_add');?></span> 
								</span>
								<span class="avatar_info visible-xs visible-lg">
									<span class="ion-plus"></span> 
								</span>
							</a>
							<ul class="dropdown-menu user-dropdown animated fadeInUp wow neat_drop " data-wow-duration="1500ms" role="menu">
								<?php if($this->Employee->has_module_permission('sales', $employee_id)) { ?>
								<li>
									<a tabindex = "-1" id="support_link" href="<?php echo site_url('sales'); ?>">
										<span class="text"><?php echo lang('sales_new_sale');?></span>
									</a>
								</li>
								<?php } if($this->Employee->has_module_permission('work_orders', $employee_id)) { ?>
								<li>
									<a tabindex = "-1" id="support_link" href="<?php echo site_url('work_orders?new=1'); ?>">
										<span class="text"><?php echo lang('common_new_work_order');?></span>
									</a>
								</li>	
								<?php } if($this->Employee->has_module_permission('receivings', $employee_id)) { ?>
								<li>			
									<a tabindex = "-1" id="support_link" href="<?php echo site_url('receivings/po/'); ?>">
										<span class="text"><?php echo lang('common_new_purchase_order');?></span>
									</a>
								</li>
								<?php } if($this->Employee->has_module_action_permission('customers', 'add_update', $employee_id)) { ?>
								<li>			
									<a tabindex = "-1" id="support_link" href="<?php echo site_url('customers/view/-1/'); ?>">
										<span class="text"><?php echo lang('customers_new');?></span>
									</a>
								</li>
								<?php } if($this->Employee->has_module_action_permission('items', 'add_update', $employee_id)) { ?>
								<li>
									<a tabindex = "-1" id="support_link" href="<?php echo site_url('items/view/-1/'); ?>">
										<span class="text"><?php echo lang('items_new');?></span>
									</a>				
							</li>
							<?php } ?>
							</ul>
						</li>	
						<?php } ?>
					<!-- End Quick Header Button -->												
					<?php if ($this->config->item('show_clock_on_header')) { ?>
					<li>
						
						<?php
						$url = 'javascript:void(0);';
						
						if ($this->config->item('timeclock'))
						{
							$url = site_url('timeclocks');
						}
							
						?>
						<a href="<?php echo $url;?>" class="visible-lg">
							<?php echo date(get_time_format()); ?>
							<?php echo date(get_date_format()) ?>
						</a>
					</li>
					<?php } ?>
					<?php if(($this->uri->segment(1)=='sales' && $this->uri->segment(2) != 'receipt' && $this->uri->segment(2) != 'complete') || ($this->uri->segment(1)=='receivings' && $this->uri->segment(2) != 'receipt' && $this->uri->segment(2) != 'complete')) { ?>
						<li class="dropdown">
							<a tabindex = "-1" href="#" class="fullscreen" data-toggle="" role="button" aria-expanded="false"><i class="ion-arrow-expand  icon-notification"></i></a>
						</li>
						<li class="dropdown">
							<a tabindex = "-1" data-target="#" class="" data-toggle="" role="button" aria-expanded="false"><i class="ion-bag  icon-notification"></i><span class="badge info-number cart cart-number count">0</span></a>
						</li>

					<?php } ?>
					
					<?php if (count($authenticated_locations) > 1) { ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"> <i class="ion-ios-location icon-notification visible-xs"></i><span class="hidden-xs"><?php echo $authenticated_locations[$current_logged_in_location_id]; ?></span> <span class="drop-icon"><i class="ion ion-chevron-down"></i></span></a>
								<ul class="dropdown-menu animated fadeInUp wow locations-drop locations-drop neat_drop" data-wow-duration="1500ms" role="menu">
								<?php foreach ($authenticated_locations as $key => $value) { ?>
									<li><a class="set_employee_current_location_id" data-location-id="<?php echo $key; ?>" href="<?php echo site_url('home/set_employee_current_location_id/'.$key) ?>"><span class="badge" style="background-color:<?php echo $this->Location->get_info($key)->color; ?>">&nbsp;</span> <?php echo $value; ?> </a></li>
								<?php } ?>
								</ul>
							</li>	

					<?php } ?>
						<?php if (is_on_demo_host() || ($this->config->item('show_language_switcher') && $this->Employee->has_module_action_permission('employees','edit_profile',$this->Employee->get_logged_in_employee_info()->person_id))) { ?>
						<?php 
						$languages = array(
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
						);

						?>	
						<!-- redirect($_SERVER['HTTP_REFERER']);	 -->
						<li class="dropdown">
							<a tabindex = "-1" href="#" class="dropdown-toggle language-dropdown" data-toggle="dropdown" role="button" aria-expanded="false"><img class=
							"flag_img" src="<?php echo base_url(); ?>assets/assets/images/flags/<?php echo $user_info->language ? $user_info->language : "english";  ?>.png" alt=""> <span class="hidden-sm hidden-xs"> <?php echo $user_info->language ? $languages[$user_info->language] : $languages["english"];  ?></span><span class="drop-icon"><i class="ion ion-chevron-down"></i></span></a>
							<ul class="dropdown-menu animated fadeInUp wow language-drop neat_drop" data-wow-duration="1500ms" role="menu">
							<?php foreach ($languages as $key => $value) { 
								if($user_info->language!=$key){
							 	?>
								<li><a tabindex = "-1" href="<?php echo site_url('employees/set_language/') ?>" data-language-id="<?php echo $key; ?>" class="set_employee_language"><img class="flag_img" src="<?php echo base_url(); ?>assets/assets/images/flags/<?php echo $key; ?>.png" alt="flags"><?php echo $value; ?></a></li>
							<?php } } ?>
							</ul>
						</li>	
						<?php } ?>
							
						<?php if ($this->Employee->has_module_permission('messages', $user_info->person_id)) {?>
						
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="ion-ios-bell-outline  icon-notification"></i><span class="badge info-number count <?php echo $new_message_count > 0 ? 'bell': '';?>" id="unread_message_count"><?php echo $new_message_count; ?></span></a>
									<ul class="dropdown-menu dropdown-menu-right animated fadeInUp wow message_drop neat_drop" data-wow-duration="1500ms" role="menu">
									<?php foreach ($this->Employee->get_messages(4) as $key => $value) { ?>
										<li>
											<a href="<?php echo site_url('messages/view/'.$value['message_id']); ?>">
												<span class="avatar_left"><img src="<?php echo base_url(); ?>assets/assets/images/avatar-default.jpg" alt=""></span>
												<span class="text_info"><?php echo H($value['message']); ?></span> 
												<span class="time_info"><?php echo date(get_date_format().' '.get_time_format(), strtotime($value['created_at'])) ?> <i class="ion-record <?php echo !$value['message_read'] ? 'online' : ''?>"></i></span> 
											</a>
										</li>	
								 	<?php	} ?>
										<li class="bottom-links">
											<a href="<?php echo site_url('messages') ?>" class="last_info"><?php echo lang('common_see_all_notifications');?></a>
										</li>
										<?php if ($this->Employee->has_module_action_permission('messages','send_message',$this->Employee->get_logged_in_employee_info()->person_id)) {  ?>									
									
											<li class="bottom-links">
												<a href="<?php echo site_url('messages/sent_messages'); ?>" class="last_info"><?php echo lang('common_view_sent_message') ?></a>
											</li>
									
											<li class="bottom-links">
												<a href="<?php echo site_url('messages/send_message') ?>" class="last_info"><?php echo lang('common_new_message');?></a>
											</li>
										<?php } ?>
									</ul>
							</li>
							<?php } ?>
							
						<li class="dropdown">
							<a tabindex = "-1" href="#" class="dropdown-toggle avatar_width" data-toggle="dropdown" role="button" aria-expanded="false"><span class="avatar-holder">

							<?php echo $user_info->image_id ? img(array('src' => cacheable_app_file_url($user_info->image_id))) : img(array('src' => base_url('assets/assets/images/avatar-default.jpg'))); ?></span>

							<span class="avatar_info visible-sm visible-md visible-lg"><?php echo H($user_info->first_name." ".$user_info->last_name); ?></span></a>
							<ul class="dropdown-menu user-dropdown animated fadeInUp wow avatar_drop neat_drop" data-wow-duration="1500ms"  role="menu">
							<li>
									<a tabindex = "-1" id="support_link" target="_blank" href="https://support.<?php echo $this->config->item('branding')['domain']; ?>/"><i class="ion-help-buoy"></i><span class="text"><?php echo lang('common_support'); ?></span></a>									
							</li>
							
								<?php if ($this->Employee->has_module_permission('config', $user_info->person_id)) {?>
								
									<li><?php echo anchor("config",'<i class="ion-android-settings"></i><span class="text">'.lang("common_settings").'</span>', array('tabindex' => '-1')); ?></li>
								<?php } ?>
								
								<?php 
								$this->load->helper('update');
								if (is_on_phppos_host() && !is_on_demo_host() && !empty($cloud_customer_info)) {?>
								<li>
									<a tabindex = "-1" id="update_billing_link" target="_blank" href="https://<?php echo $this->config->item('branding')['domain']; ?>/update_billing.php?store_username=<?php echo $cloud_customer_info['username'];?>&username=<?php echo $this->Employee->get_logged_in_employee_info()->username; ?>&password=<?php echo $this->Employee->get_logged_in_employee_info()->password; ?>"><i class="ion-card"></i><span class="text"><?php echo lang('common_update_billing_info'); ?></span></a>									
								</li>
								
								<?php } ?>
								
								
								<?php if ($this->Location->get_info_for_key('blockchyp_api_key') && $this->Employee->has_module_action_permission('sales', 'view_edit_transaction_history', $this->Employee->get_logged_in_employee_info()->person_id)) {?>				
									<li>
									<a target="_blank" tabindex = "-1" title="" href="<?php echo site_url('sales/coreclear_portal')?>"><i class="ion-ios-world"></i><span class="text"><?php echo lang('sales_coreclear_portal'); ?></span></a>
									
									</li>
								<?php } ?>
								
								<li>
									<a tabindex = "-1" id="change_log_link" target="_blank" href="https://feedback.phppointofsale.com"><i class="ion-chatbox"></i><span class="text"><?php echo lang('common_feedback_website'); ?></span></a>
								</li>
								
								
								<li>
									<a tabindex = "-1" id="change_log_link" target="_blank" href="https://<?php echo $this->config->item('branding')['domain']; ?>/whats_new.php"><i class="ion-flash"></i><span class="text"><?php echo lang('common_change_log'); ?></span></a>
								</li>
								
								<li>
									<a tabindex = "-1" id="switch_user" href="<?php echo site_url('login/switch_user/'.($this->uri->segment(1) == 'sales' ? '0' : '1'));  ?>" data-toggle="modal" data-target="#myModalDisableClose"><i class="ion-ios-toggle-outline"></i><span class="text"><?php echo lang('common_switch_user'); ?></span></a>
								</li>
								
								
								<?php if ($this->Employee->has_module_action_permission('employees','edit_profile',$this->Employee->get_logged_in_employee_info()->person_id)) {  ?>									
								
								<li>
									<a tabindex = "-1" id="edit_profile" href="<?php echo site_url('employees/edit_profile_model/'.($this->uri->segment(1) == 'sales' ? '0' : '1'));  ?>" data-toggle="modal" data-target="#myModalDisableClose"><i class="ion-ios-toggle-outline"></i><span class="text"><?php echo lang('common_edit_profile'); ?></span></a>

								</li>
								<?php } ?>
								<?php
								if ($this->config->item('timeclock')) 
								{
								?>
					         		<li>
									<?php
										echo anchor("timeclocks",'<i class="ion-clock"></i>'.lang("employees_timeclock"), array('tabindex' => '-1'));
					 				?>
									</li>
								<?php
								}
								?>								
								<li>
								<?php
									if ($this->config->item('track_payment_types') && $this->Register->is_register_log_open()) {
										$continue = $this->config->item('timeclock') && !$this->Employee->get_logged_in_employee_info()->not_required_to_clock_in ? 'timeclocks' : 'logout';
										echo anchor("sales/closeregister?continue=$continue",'<i class="ion-power"></i><span class="text">'.lang("common_logout").'</span>',array('class'=>'logout_button','tabindex' => '-1'));
									} else {
										
										if ($this->config->item('timeclock') && !$this->Employee->get_logged_in_employee_info()->not_required_to_clock_in && $this->Employee->is_clocked_in())
										{
											echo anchor("timeclocks",'<i class="ion-power"></i><span class="text">'.lang("common_logout").'</span>',array('class'=>'logout_button','tabindex' => '-1'));
										}
										else
										{
											echo anchor("home/logout",'<i class="ion-power"></i><span class="text">'.lang("common_logout").'</span>',array('class'=>'logout_button','tabindex' => '-1'));
										}
									}
									?>
								</li>			
							</ul>
						</li>							
					</ul>
				</nav>
			</div>
			<!-- top-bar -->
			<div class="main-content">
