<?php
$this->load->view("partial/header_standalone");
?>

		<div class="panel panel-piluku invoice_body">
			<div class="panel-heading">
				<?php echo lang("invoices_basic_info"); ?>
			</div>
			<div class="spinner" id="grid-loader" style="display:none">
				<div class="rect1"></div>
				<div class="rect2"></div>
				<div class="rect3"></div>
			</div>
			<?php echo form_open("public_view/start_cc_processing_coreclear2",array('id'=>'invoice_save_form','class'=>'form-horizontal')); ?>
			
			<div class="panel-body">
				<div class="col-md-8 ">
					<div class="panel panel-info"> 
						<div class="panel-heading"> 
							<h3 class="panel-title"><?php echo lang('invoices_invoice_detail');?></h3> 
							<span class="label label-danger pull-right term"><?php echo lang('invoices_terms');?>: <?php echo $invoice_info->term_name?></span>
						</div> 
						<div class="panel-body"> 
							<?php echo lang('invoices_invoice_to');?>: <?php echo $invoice_info->person; ?><br>
							<?php echo lang('invoices_invoice_date');?>: <?php echo date(get_date_format(), strtotime($invoice_info->invoice_date))?><br>
							<?php echo lang('invoices_due_date')?>: <?php echo date(get_date_format(), strtotime($invoice_info->due_date))?>

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

				<div class="col-md-6">
					<label><?php echo lang('common_amount');?></label>:
					<input class="form form-control" type="text" name="amount" value="<?php echo to_currency_no_money($invoice_info->balance); ?>">
					<input type="hidden" name="total" value="<?php echo to_currency_no_money($invoice_info->balance); ?>">
					<input type="hidden" name="id" value="<?php echo $invoice_info->invoice_id; ?>">
					<input type="hidden" name="payment_type" value="<?php echo lang('common_credit'); ?>">
					<input type="hidden" name="register" value="2">

				</div>

					
				<div class="col-md-6">
					<div id="credit_card_payment_holder">
						<div id="manual_entry_holder">
							<label><?php echo lang('sales_credit_card_no');?></label>:
							<input type="text" id="cc_number" name = "cc_number" class="form-control" placeholder="<?php echo H(lang('sales_credit_card_no')); ?>" required>
							<label><?php echo H(lang('sales_exp_date').'(MM/YYYY)'); ?></label>
							<input type="text" id="cc_exp_date" name="cc_exp_date" class="form-control" placeholder="<?php echo H(lang('sales_exp_date').'(MM/YYYY)'); ?>" required>
							<label>CCV</label>
							<input type="text" id="cvv" name="cvv" class="form-control" placeholder="CVV" required>
						</div>
					</div>
					<input type="hidden" name="location_id" value="<?php echo $invoice_info->location_id;?>">
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
				<br>

				
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

				if ($this->session->userdata('card_error')) {
					$message = 'Card Charge FAILED!';
					echo "show_feedback('error', ".json_encode($message).", ".json_encode(lang('common_error')).");";
				}
			?>
		
			</script>
<?php
$this->load->view("partial/footer_standalone");
?>