<?php $this->load->view("partial/header"); ?>
	<div class="col-md-12 col-sm-4 col-xs-12 font-pt-sans">
		<ul class="list-unstyled invoice-address text-center visible-print" style="margin-bottom:2px;">
			<?php 
			$company = ($company = $this->Location->get_info_for_key('company', isset($override_location_id) ? $override_location_id : FALSE)) ? $company : $this->config->item('company');
			$company_logo = ($company_logo = $this->Location->get_info_for_key('company_logo', isset($override_location_id) ? $override_location_id : FALSE)) ? $company_logo : $this->config->item('company_logo');
			$tax_id = ($tax_id = $this->Location->get_info_for_key('tax_id', isset($override_location_id) ? $override_location_id : FALSE)) ? $tax_id : $this->config->item('tax_id');
			$website = ($website = $this->Location->get_info_for_key('website', isset($override_location_id) ? $override_location_id : FALSE)) ? $website : $this->config->item('website');
			
			if ($company_logo) { ?>

				<?php
				if (!(isset($standalone) && $standalone)) {
				?>

					<li class="invoice-logo">
						<?php echo img(array('src' => secure_app_file_url($company_logo))); ?>
					</li>
				<?php } ?>
			<?php } ?>

			<?php if ($this->Location->count_all() > 1) { ?>
				<li class="company-title"><?php echo H($company); ?></li>
				<?php if(!$this->config->item('hide_location_name_on_receipt')){ ?>
					<li><?php echo H($this->Location->get_info_for_key('name', isset($override_location_id) ? $override_location_id : FALSE)); ?></li>
				<?php } ?>
			<?php } else {
			?>
				<li class="company-title"><?php echo H($company); ?></li>
			<?php
			}
			?>

			<?php
			if ($tax_id) {
			?>
				<li class="tax-id-title"><?php echo lang('common_tax_id') . ': ' . H($tax_id); ?></li>
			<?php
			}
			?>

			<li class="nl2br"><?php echo H($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></li>
			<li><?php echo H($this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE)); ?></li>
			<?php if ($website) { ?>
				<li><?php echo H($website); ?></li>
			<?php } ?>
		</ul>
	</div>

	<div class="panel panel-piluku invoice_body">
		<div class="panel-heading hidden-print">
			<?php echo lang("invoices_invoice"); ?>
			<span class="pull-right">
					<?php echo anchor("invoices/index/$invoice_type",'&lt;- Back To Invoices', array('class'=>'hidden-print')); ?>
			</span>
		</div>
		
		<div class="panel-body" style="position:relative;">

			<?php if($invoice_info->balance <= 0){?>
			<style>
				.watermark{
					position: absolute;
					font-size: 150px;
					z-index: 1000;
					opacity: 0.2;
					width: 100%;
					text-align: center;
					pointer-events: none;
					text-transform: uppercase;
					transform: rotate(-20deg);
					margin-top: 50px;
				}
			</style>
			<div class="watermark"><?php echo lang('common_paid')?></div>
			<?php } ?>

			<div class="col-md-8 ">
				<div class="panel panel-info"> 
					<div class="panel-heading"> 
						<h3 class="panel-title">
							<?php echo lang('invoices_terms');?>: <?php echo $invoice_info->term_name?>
						</h3> 
						<span class="label label-danger pull-right term hidden-print">
							<?php echo lang('invoices_terms');?>: <?php echo $invoice_info->term_name?>
						</span>
					</div> 
					<div class="panel-body"> 
						<?php echo lang('invoices_invoice_date');?>: <?php echo date(get_date_format(), strtotime($invoice_info->invoice_date));?><br>
						<?php echo lang("invoices_$invoice_type");?>: <?php echo $invoice_info->person;?><br>
						
						<?php echo $invoice_info->term_description;?>
						<?php echo lang('invoices_due_date');?>: <?php echo date(get_date_format(), strtotime($invoice_info->due_date));?>
						<?php 
						if($invoice_info->{"$invoice_type".'_po'}) {
							echo '<br>';
							echo lang('invoices_po_'.$invoice_type).': ';
							echo $invoice_info->{"$invoice_type".'_po'};
						}
						?>
						
					</div> 
				</div>
			</div>
			<div class="col-md-2 hidden-print">
				<div class="panel panel-success"> 
					<div class="panel-heading"> 
						<h3 class="panel-title"><?php echo lang('common_total');?></h3> 
					</div> 
					<div class="panel-body"> <h3><?php echo to_currency($invoice_info->total)?></h3> </div> 
				</div>
			</div>

			<div class="col-md-2 hidden-print">
				<div class="panel panel-danger btn-cancel"> 
					<div class="panel-heading"> 
						<h3 class="panel-title"><?php echo lang('common_balance');?></h3> 
					</div> 
					<div class="panel-body"> <h3><?php echo to_currency($invoice_info->balance)?></h3> </div> 
				</div>
			</div>
			<div class="col-md-12 hidden-print">
				<div class="pull-right">
					<button class="btn btn-primary btn-lg hidden-print" id="print_button" onclick="window.print()" > <?php echo lang('common_print'); ?> </button>	
					<?php if (to_currency_no_money($invoice_info->balance) != '0.00') { ?>
						<button class="btn btn-primary btn-lg hidden-print" id="email_button" > 
							<?php echo lang('common_email'); ?> 
						</button>
					<?php } ?>	
							
				</div>
				<br><br><br>
			</div>
			
			<?php $this->load->view('partial/invoices/details', array('details' => $details,'can_edit' => FALSE,'type_prefix' => $type_prefix)); ?>
			
			
			<?php $this->load->view('partial/invoices/payments', array('payments' => $payments));?>

			
		</div> <!-- close pannel body -->
		
		<div class="row" style="padding:0px 22px; text-align: right;">
			<div class="pull-right  visible-print">
				<h4><?php echo lang('common_total');?>: <?php echo to_currency($invoice_info->total)?></h4>
				<h4><?php echo lang('common_balance');?>: <?php echo to_currency($invoice_info->balance)?></h4>
			</div>
		</div>
		<script type="text/javascript">
			$("#email_button").click(function(e)
			{
				e.preventDefault();
				$.get(<?php echo json_encode(site_url("invoices/email_invoice/$invoice_type/$invoice_id"));?>);
				show_feedback('success', <?php echo json_encode(lang('common_invoice_sent')); ?>, <?php echo json_encode(lang('common_success')); ?>);
				
			});
		</script>
<?php $this->load->view("partial/footer"); ?>