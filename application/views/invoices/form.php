<?php $this->load->view("partial/header"); ?>
<style type="text/css">
	
</style>
	
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
	<?php echo form_open("invoices/save/$invoice_type/$invoice_id",array('id'=>'invoice_save_form','class'=>'form-horizontal')); ?>
	
	<div class="panel-body">
		<div class="col-md-12">
			<div id="invoice_date_field" class="form-group">
				<?php echo form_label(lang('invoices_invoice_date').':', 'invoice_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10">
					<div class="input-group date" data-date="<?php echo $invoice_info->invoice_date ? date(get_date_format(), strtotime($invoice_info->invoice_date)) : ''; ?>">
						<span class="input-group-addon bg"><i class="ion ion-ios-calendar-outline"></i></span>
						<?php echo form_input(array(
							'name'	=>	'invoice_date',
							'id'	=>	'invoice_date',
							'class'	=>	'form-control datepicker',
							'value'	=>	$invoice_info->invoice_date ? date(get_date_format().' '.get_time_format(), strtotime($invoice_info->invoice_date)) : date(get_date_format())
						));?> 
					</div>
				</div>
			</div>
			
			<div id="invoice_date_field" class="form-group">
				<?php echo form_label(lang('invoices_po_'.$invoice_type).':', 'invoice_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
				<div class="col-sm-9 col-md-9 col-lg-3">
					<div class="input-group date">
						<?php echo form_input(array(
							'name'	=>	"$invoice_type".'_po',
							'id'	=> 	"$invoice_type".'_po',
							'class'	=>	'form-control col-lg-2',
							'value' => 	$invoice_info->{"$invoice_type".'_po'},
						));?> 
					</div>
				</div>
			</div> 
			
			
			<div class="form-group">
				<?php echo form_label(lang("invoices_$invoice_type"), '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10">
					<?php
					if ($invoice_id == -1)
					{
						echo form_input(array(
							'name'		=> 	"$invoice_type".'_id',
							'id'		=> 	"$invoice_type".'_id',
							'size'		=>	'10',
							'value' 	=> 	$invoice_info->{"$invoice_type".'_id'}));
					} else {
						echo form_input(array(
							'name'		=> "$invoice_type".'_name',
							'id'		=> 	"",
							'size'		=>	'10',
							'class' 	=> 	'form-control',
							'disabled' 	=> 	'disabled',
							'value' 	=> 	$invoice_info->person));
					?>
					<input type="hidden" name="<?php echo "$invoice_type".'_id';?>" value="<?php echo $invoice_info->{"$invoice_type".'_id'};?>">	
					<?php } ?>	
				</div>
			</div>
			
			<div class="form-group">
				<?php echo form_label(lang("invoices_terms"),'',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10">
					<?php
					echo form_dropdown('term_id', $terms, $invoice_info->term_id, 'class="form-control input_radius" id="term_id"');
					?>	
				</div>
			</div>
			
			<div id="due_date_field" class="form-group">
				<?php echo form_label(lang('invoices_due_date').':', 'due_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10">
					<div class="input-group date" data-date="<?php echo $invoice_info->due_date ? date(get_date_format(), strtotime($invoice_info->due_date)) : ''; ?>">
						<span class="input-group-addon bg"><i class="ion ion-ios-calendar-outline"></i></span>
						<?php echo form_input(array(
							'name'	=>	'due_date',
							'id'	=>	'due_date',
							'class'	=>	'form-control datepicker',
							'value'	=>	$invoice_info->due_date ? date(get_date_format().' '.get_time_format(), strtotime($invoice_info->due_date)) : ''
						));?> 
					</div>
				</div>
			</div>		
					
			<div class="form-controls form-actions">	
				<ul class="list-inline pull-right">
					<li>
						<?php
							echo form_submit(array(
								'name'	=>	'submitf',
								'id'	=>	'submitf',
								'value'	=>	lang('common_save'),
								'class'	=>	'submit_button btn btn-primary')
							);
						?>
					</li>
				</ul>
			</div>
		</div>
		
		<?php
		
		if($invoice_info->invoice_id > 0)
		{
		?>
		<div class="row ">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="col-xs-6 col-sm-3 col-md-2 pull-right">
					<div class="panel panel-success"> 
						<div class="panel-heading"> 
							<h3 class="panel-title"><?php echo lang('common_total');?></h3> 
						</div> 
						<div class="panel-body"> <h3><?php echo to_currency($invoice_info->total)?></h3> </div> 
					</div>
				</div>
				<div class="col-xs-6 col-sm-3 col-md-2 pull-right">
					<div class="panel panel-danger"> 
						<div class="panel-heading"> 
							<h3 class="panel-title"><?php echo lang('common_balance');?></h3> 
						</div> 
						<div class="panel-body"> <h3><?php echo to_currency($invoice_info->balance)?></h3> </div> 
					</div>
				</div>
			</div>
		</div>
		
		<div>
			<a id="add_line_item" href="javascript:void(0);" class="btn btn-primary">
				<?php echo lang('invoices_add_invoice_line_item');?>
			</a>
			<a id="add_credit_memo" href="javascript:void(0);" class="btn btn-primary">
				<?php echo lang('invoices_add_credit_memo');?>
			</a>
		</div>
		
		<?php } ?>
		<?php
		if(isset($orders) && !empty($orders))
		{
			$type_prefix = $invoice_type == 'customer' ? 'sale' : 'receiving';
		?>
		<Br>
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3><strong><?php echo lang('invoices_recent_unpaid_orders');?></strong></h3>
			</div>
			<div class="panel-body" style="padding:0px !important;">
				<div class="" id="invoice_details">
					<table class="table table-bordered">
						<tr class="payment_heading">
							<th><?php echo lang('common_id');?></th>
							<th><?php echo lang('common_time');?></</th>
							<th><?php echo lang('common_amount_due');?></th>
							<th><?php echo lang('common_comment');?></th>
							<th><?php echo lang('invoices_add_to_invoice');?></th>
						</tr>
				
						<?php foreach($orders as $order) { ?>
						<tr>
							<td><?php echo $order[$type_prefix.'_id'];?></td>
							<td><?php echo date(get_date_format().' '.get_time_format(),strtotime($order[$type_prefix.'_time']));?></td>
							<td><?php echo to_currency($order['payment_amount']);?></td>
							<td><?php echo $order['comment'] ? $order['comment'] : lang('common_none');?></td>
							<td>
								<?php if (!$this->Invoice->is_order_in_invoice($invoice_type,$order[$type_prefix.'_id'])) { ?>
									<a href="<?php echo site_url("invoices/add_to_invoice/$invoice_type/$invoice_id/".$order[$type_prefix.'_id']);?>" class="btn btn-primary"><?php echo lang('invoices_add_to_invoice');?></a>
								<?php } else { ?>
								<?php echo lang('invoices_already_invoiced');?>
								<?php } ?>
							</td>
						</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>

		<?php } ?>
		<br />
		<!-- Load Invoice Details -->
		<?php $this->load->view('partial/invoices/details', array('details' => isset($details) ? $details : NULL,'can_edit' => TRUE,'type_prefix' => $type_prefix)); ?>
		<!-- Load Invoice Payments -->
		<?php $this->load->view('partial/invoices/payments', array('payments' => $payments));
		 
		
		if($invoice_id > 0 && (float)$invoice_info->balance > 0)
		{
			echo anchor("invoices/pay/$invoice_type/$invoice_id", lang('common_pay'),array('class' => 'btn btn-primary pull-left'));
		}
		?>
		
		
		
	</div> <!-- close pannel body -->
	<?php echo form_close(); ?>
	<?php $this->load->view('partial/invoices/invoice_detail_modal', array('modal_id' => 'invoice-modal','action' => "invoices/add_to_invoice_manual/$invoice_type/$invoice_id", 'invoice_type' => $invoice_type,'invoice_id' => $invoice_id));?>
	<?php $this->load->view('partial/invoices/invoice_detail_modal', array('modal_id' => 'invoice-modal-memo','action' => "invoices/add_to_invoice_credit_memo/$invoice_type/$invoice_id", 'invoice_type' => $invoice_type,'invoice_id' => $invoice_id));?>
	
	<script type="text/javascript">
		
		$(".delete-invoice-detail").click(function(e)
		{
			var $that = $(this);
			e.preventDefault();

			bootbox.confirm('Are you you sure you want to delete this invoice item?', function(result)
			{
				if (result)
				{
					window.location = $that.attr('href');
				}
			});
		});
		
		$("#add_line_item").click(function()
		{
			$("#invoice-modal").modal('show');				
		});
		
		$("#add_credit_memo").click(function()
		{
			$("#invoice-modal-memo").modal('show');				
		});
		
		
	    $('.xeditable').editable({
	    	validate: function(value) {
	            if ($.isNumeric(value) == '' && $(this).data('validate-number')) {
						return <?php echo json_encode(lang('common_only_numbers_allowed')); ?>;
	            }
	        },
	    	success: function(response, newValue) {
			}
	    });
		
	    $('.xeditable').on('shown', function(e, editable) {

			$(this).closest('.table-responsive').css('overflow-x','hidden');

	    	editable.input.postrender = function() {
					//Set timeout needed when calling price_to_change.editable('show') (Not sure why)
					setTimeout(function() {
		         editable.input.$input.select();
				}, 200);
		    };
		});
		
		
		date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);
		
		
		
		$("#<?php echo $invoice_type;?>_id").select2(
		{
			width : '100%',
			placeholder: <?php echo json_encode(lang('common_search')); ?>,
			ajax: {
				url: <?php echo json_encode(site_url("invoices/suggest_$invoice_type")); ?>,
				dataType: 'json',
			   data: function(term, page) 
				{
			      return {
			          'term': term
			      };
			    },
				results: function(data, page) {
					return {results: data};
				}
			},
			id: function (suggestion) { return suggestion.value },
			formatSelection: function(suggestion) {
				return suggestion.label;
			},
			formatResult: function(suggestion) {
				return suggestion.label;
			}
		});
		
		$("#term_id").change(function(e)
		{
			var url = '<?php echo site_url("invoices/get_term_default_due_date"); ?>'+'/'+$(this).val();
			$.getJSON(url,function(json)
			{	
				var term_default_due_date = json.term_default_due_date;
				$("#due_date").val(term_default_due_date);
			
			});	
		});
		
		$("#<?php echo $invoice_type;?>_id").change(function(e)
		{
			var url = '<?php echo site_url("invoices/get_default_terms/".$invoice_type); ?>'+'/'+$(this).val();
			$.getJSON(url,function(json)
			{	
				var default_term_id = json.default_term_id;
				$("#term_id").val(default_term_id);
				$("#term_id").trigger('change');
			
			});	
		});
		$('#invoice_save_form').ajaxForm({
		success:function(response)
		{
			var response = JSON.parse(response);
			$('#grid-loader').hide();
			submitting = false;
		
			show_feedback(response.success ? 'success' : 'error',response.message, response.success ? <?php echo json_encode(lang('common_success')); ?>  : <?php echo json_encode(lang('common_error')); ?>);

			if(response.reload==1 && response.success)
			{
				window.location.reload();
			}
			else if(response.redirect==1 && response.success)
			{ 
				window.location.href = '<?php echo site_url('invoices/index/'.$invoice_type); ?>';
			}
			else if(response.redirect==2 && response.success)
			{ 
				window.location.href = '<?php echo site_url('invoices/view/'.$invoice_type.'/'); ?>'+response.invoice_id;
			}

		}});
	</script>
<?php $this->load->view("partial/footer"); ?>