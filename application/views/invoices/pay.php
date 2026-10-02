<?php $this->load->view("partial/header"); ?>
		<div class="panel panel-piluku invoice_body">
			<div class="panel-heading">
				<?php echo lang("invoices_basic_info"); ?>

				<span class="pull-right">
						<?php echo anchor("invoices/index/$invoice_type",'&lt;- Back To Invoices', array('class'=>'hidden-print')); ?>
				</span>
			</div>
			<div class="spinner" id="grid-loader" style="display:none">
				<div class="rect1"></div>
				<div class="rect2"></div>
				<div class="rect3"></div>
			</div>
			<?php echo form_open("invoices/process_payment/$invoice_type/$invoice_id",array('id'=>'invoice_save_form','class'=>'form-horizontal')); ?>
			
			<div class="panel-body">
				<div class="col-md-8 ">
					<div class="panel panel-info"> 
						<div class="panel-heading"> 
							<h3 class="panel-title"><?php echo lang('common_invoice_id');?>: <?php echo $invoice_info->invoice_id?></h3> 
							<span class="label label-danger pull-right term"><?php echo lang('invoices_terms');?>: <?php echo $invoice_info->term_name?></span>
						</div> 
						<div class="panel-body"> 
							<?php echo lang('invoices_invoice_to');?>: <?php echo $invoice_info->person; ?><br>
							<?php echo lang('invoices_invoice_date');?>: <?php echo date(get_date_format(), strtotime($invoice_info->invoice_date))?><br>
							<?php echo lang('invoices_due_date')?>: <?php echo date(get_date_format(), strtotime($invoice_info->due_date))?>

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

				<div class="col-md-2">
					<div class="panel panel-success"> 
						<div class="panel-heading"> 
							<h3 class="panel-title"><?php echo lang('common_total');?></h3> 
						</div> 
						<div class="panel-body"> <h3><?php echo to_currency($invoice_info->total)?></h3> </div> 
					</div>
				</div>

				<div class="col-md-2">
					<div class="panel panel-danger btn-cancel"> 
						<div class="panel-heading"> 
							<h3 class="panel-title"><?php echo lang('common_balance');?></h3> 
						</div> 
						<div class="panel-body"> <h3><?php echo to_currency($invoice_info->balance)?></h3> </div> 
					</div>
				</div>
				<hr>
				<br>
				
				<?php if (to_currency_no_money($invoice_info->balance) != 0.00) { ?>
							
				<div class="col-md-6">
					<?php echo lang('common_amount');?>:
					<input class="form form-control" type="text" name="amount" value="<?php echo to_currency_no_money($invoice_info->balance); ?>">
				</div>

				<div class="col-md-6">
					<?php echo lang('reports_payment_type');?>:
					<?php echo form_dropdown('payment_type', $payment_types, '', 'class="form-control input_radius" id="payment_type"'); ?>
				</div>
				<div class="col-md-12">
					<div id="credit_card_payment_holder" style="display: none">
						Register:
						<?php echo form_dropdown('register', $registers, '', 'class="form-control input_radius" id="register"'); ?>
					
					
						<div id="manual_entry_holder" style="display: none;">
						
							<input type="text" id="cc_number" name = "cc_number" class="form-control" placeholder="<?php echo H(lang('sales_credit_card_no')); ?>">
							<input type="text" id="cc_exp_date" name="cc_exp_date" class="form-control" placeholder="<?php echo H(lang('sales_exp_date').'(MM/YYYY)'); ?>">
							<input type="text" id="cc_ccv" name="cc_ccv" class="form-control" placeholder="<?php echo H(lang('common_ccv')); ?>">
						</div>
					</div>
					
					<br /><br /><br><br>
						<?php
							echo form_submit(array(
								'name'	=>	'submitf',
								'id'	=>	'submitf',
								'value'	=>	lang('common_submit'),
								'class'	=>	'submit_button btn btn-primary pull-right')
							);
						?>
						<br><br>
				</div>
				<?php } ?>
				<br>
				
				<?php $this->load->view('partial/invoices/payments', array('payments' => $payments));?>
			</div> <!-- close pannel body -->
			<?php echo form_close(); ?>
			
			<script type="text/javascript">
			<?php
				if($this->input->get('success') === '1') 
				{
					$message = 'Card Charged Successfully';
					echo "show_feedback('success', ".json_encode($message).", ".json_encode(lang('common_success')).");";
				}
				elseif($this->input->get('success') === '0')
				{
					$message = 'Card Charge FAILED!';
					echo "show_feedback('error', ".json_encode($message).", ".json_encode(lang('common_error')).");";
				}
			?>
			<?php if ($invoice_type == 'customer' && $is_coreclear_processing) { ?>
				$('#register').change(function()
				{
					if ($(this).val() == -1) //Manual entry
					{
						$("#manual_entry_holder").show();
					}
					else
					{
						$("#manual_entry_holder").hide();
					}
				});
			
				function check_payment_type()
				{
					if ($("#payment_type").val() == <?php echo json_encode(lang('common_credit')) ?>)
					{
						$("#credit_card_payment_holder").show();
					}
					else
					{
						$("#credit_card_payment_holder").hide();
					}
				}
			
				$("#payment_type").change(check_payment_type);
				check_payment_type();
			<?php } ?>
			</script>
<?php $this->load->view("partial/footer"); ?>