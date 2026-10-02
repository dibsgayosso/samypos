
<div class="modal-dialog">
	<div class="modal-content customer-recent-sales">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true" class="ti-close"></span></button>
			<h4 class="modal-title"> <?php echo $title; ?></h4>
		</div>
		<div class="modal-body ">
			<div class="row" id="form">
				
				<div class="spinner" id="grid-loader" style="display:none">
				  <div class="rect1"></div>
				  <div class="rect2"></div>
				  <div class="rect3"></div>
				</div>
				<div class="col-md-12">
					<?php $person_id = $expense_info->id ? $expense_info->id : '';?>
					<?php echo form_open($controller_name.'/quick_save/'.$person_id,array('id'=>$controller_name.'_form','class'=>'form-horizontal')); ?>

					<div class="form-group p-lr-15">
						<?php echo form_label(lang('expenses_date').':', 'expenses_date_input', array('class'=>'required col-sm-3 col-md-3 col-lg-3 control-label')); ?>
					  	<div class="input-group date">
					    	<span class="input-group-addon"><i class="ion-calendar"></i></span>
					    	<?php echo form_input(array(
					      		'name'=>'expenses_date',
								'id'=>'expenses_date_input',
								'class'=>'form-control form-inps datepicker',
								'value'=>$expense_info->expense_date ? date(get_date_format(), strtotime($expense_info->expense_date)) : date(get_date_format()))
					    	);?> 
					    </div>  
					</div>

					<div class="form-group">
						<?php echo form_label(lang('expenses_amount').':', 'expenses_amount_input', array('class'=>'required col-sm-3 col-md-3 col-lg-3 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9 cmp-inps">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'name'=>'expenses_amount',
								'id'=>'expenses_amount_input',
								'value'=>$expense_info->expense_amount? to_currency_no_money($expense_info->expense_amount) : '')
							);?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_payment').':', 'expense_payment_type_input', array('class'=>'required col-sm-3 col-md-3 col-lg-3 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9 cmp-inps">
							<?php echo form_dropdown('expense_payment_type', $payment_types,$expense_info->expense_payment_type,'class="form-control"');
						?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_tax').':', 'expenses_tax_input', array('class'=>'required col-sm-3 col-md-3 col-lg-3 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9 cmp-inps">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'name'=>'expenses_tax',
								'id'=>'expenses_tax_input',
								'value'=>$expense_info->expense_tax? to_currency_no_money($expense_info->expense_tax) : to_currency_no_money(0))
							);?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('expenses_description').':', 'expenses_description_input', array('class'=>'required col-sm-3 col-md-3 col-lg-3 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9 cmp-inps">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'name'=>'expenses_description',
								'id'=>'expenses_description_input',
								'value'=>$expense_info->expense_description)
							);?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_type').':', 'expenses_type_input', array('class'=>'required col-sm-3 col-md-3 col-lg-3 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9 cmp-inps">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'name'=>'expenses_type',
								'id'=>'expenses_type_input',
								'value'=>$expense_info->expense_type)
							);?>
						</div>
					</div>
					
					
					<div class="form-group">
						<?php echo form_label(lang('common_reason').':', 'expenses_reason_input', array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9 cmp-inps">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'name'=>'expense_reason',
								'id'=>'expenses_reason_input',
								'value'=>$expense_info->expense_reason)
							);?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_category').':', 'category_id',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label  required wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<?php echo form_dropdown('category_id', $categories,$expense_info->category_id, 'class="form-control form-inps" id ="category_id"');?>	
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('expenses_recipient_name').':', 'employee_id', array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9 cmp-inps">
							<?php echo form_dropdown('employee_id',$employees, $expense_info->employee_id ? $expense_info->employee_id : $logged_in_employee_id , 'id="employee_id" class="form-control"'); ?>
						</div>
					</div>


					<div class="form-group">
						<?php echo form_label(lang('common_approved_by').':', 'approved_employee_id', array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9 cmp-inps">
							<?php echo form_dropdown('approved_employee_id',$employees, $expense_info->approved_employee_id ? $expense_info->approved_employee_id : $logged_in_employee_id , 'id="approved_employee_id" class="form-control"'); ?>
						</div>
					</div>

					<?php if ($this->config->item('track_payment_types') && !$expense_info->id) { ?>	
			
						<div class="form-group">
						<?php echo form_label(lang('common_remove_cash_from_register').':', 'cash_register_id', array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9 cmp-inps">
								<?php echo form_dropdown('cash_register_id', $registers, '' , 'id="cash_register_id" class="form-control"'); ?>
							</div>
						</div>
					<?php } ?>

					<hr>
					<?php echo form_hidden('redirect_code', $redirect_code); ?>
					<div class="modal-footer">
						<div class="form-acions">
							<?php  

							if ($redirect_code == 1) { ?>x
								<a href="<?php echo site_url($controller_name.'/view/'.$person_id.'/1');?>" class="pull-left submit_button btn btn-primary"><?php echo lang('common_edit'); ?></a>
							<?php } else { ?>
								<a href="<?php echo site_url($controller_name.'/view/'.$person_id.'/2');?>" class="pull-left submit_button btn btn-primary"><?php echo lang('common_edit'); ?></a>
							<?php } ?>
							<?php
							echo form_submit(array(
								'name'	=>	'submit',
								'id'	=>	'submit',
								'value'	=>	lang('common_save'),
								'class'	=>'	submit_button btn btn-success')
							);
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php 
echo form_close();
?>

<script type='text/javascript'>
var submitting = false;
//validation and submit handling
$(document).ready(function()
{
	$('#category_id').selectize({
		create: true,
		render: {
			item: function(item, escape) {
					var item = '<div class="item">'+ escape($('<div>').html(item.text).text()) +'</div>';
					return item;
			},
			option: function(item, escape) {
					var option = '<div class="option">'+ escape($('<div>').html(item.text).text()) +'</div>';
					return option;
			},
			option_create: function(data, escape) {
					var add_new = <?php echo json_encode(lang('common_new_category')) ?>;
				return '<div class="create">'+escape(add_new)+' <strong>' + escape(data.input) + '</strong></div>';
			}
		}
	});
	        	
        $('#expenses_form').validate({
		ignore: ':hidden:not([class~=selectized]),:hidden > .selectized, .selectize-control .selectize-input input',
		submitHandler:function(form)
		{
			$('#grid-loader').show();
			// window.location.reload(true);
			if (submitting) return;
			submitting = true;
			$(form).ajaxSubmit({
			error: function(data ) { 
			},
			success:function(response)
			{
				$('#grid-loader').hide();
				submitting = false;
				if (response.success)
				{
					window.location.href = '<?php echo site_url('expenses'); ?>';
					show_feedback('success', response.message, <?php echo json_encode(lang('common_success')); ?>);
				}
				else
				{
					show_feedback('error', response.message, <?php echo json_encode(lang('common_error')); ?>);
				}
			},
			
			<?php if(!$expense_info->id) { ?>
			resetForm: true,
			<?php } ?>
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
        expenses_type: "required",
        expenses_description: "required",
        expenses_date: "required",
		expenses_amount: {
			required:true,
			number:true				
		},
		expenses_tax:"number",
        expenses_recipient_name: "required",
        category_id: "required"
		},
		messages: 
		{
     		expenses_type: <?php echo json_encode(lang('expenses_type_required')); ?>,
     		expenses_description: <?php echo json_encode(lang('expenses_description_required')); ?>,
     		expenses_date: <?php echo json_encode(lang('expenses_date_required')); ?>,
     		expenses_amount: 
			{
				required: <?php echo json_encode(lang('expenses_amount_required')); ?>,
				number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
			},
			expenses_tax: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>,
     		expenses_recipient_name: <?php echo json_encode(lang('expenses_recipient_name_required')); ?>,
     		category_id: <?php echo json_encode(lang('common_category_required')); ?>
		}
	});
});

date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);



</script>