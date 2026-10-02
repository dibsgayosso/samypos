
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
					<?php $person_id = $person_info->person_id ? $person_info->person_id : '';?>
					<?php echo form_open($controller_name.'/quick_save/'.$person_id,array('id'=>$controller_name.'_form','class'=>'form-horizontal')); ?>
					<?php if($controller_name == 'suppliers') { ?>
					<div class="form-group">	
						<?php echo form_label(lang('common_company').':', 'company_name',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<?php echo form_input(array(
							'name'	=>	'company_name',
							'id'	=>	'company_name',
							'class'	=>	'company_names form-control',
							'value'	=>	$person_info->company_name)
							);?>
						</div>
					</div>
					<?php } ?>

					<div class="form-group">
						<?php 
						$required = ($controller_name == "suppliers") ? "" : "required";
						echo form_label(lang('common_first_name').':', 'first_name',array('class'=>$required.' col-sm-3 col-md-3 col-lg-3 control-label  ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<div class="input-group" style="width:100%">
								<div class="input-group-btn" style="width:4rem">
									<?php  
									$titles = array(
										"" 		=> '',
										"Mr." 		=> lang('common_mr.'),
										"Mrs." 		=> lang('common_mrs.'),
										"Dr." 		=> lang('common_dr.'),
										"Miss." 	=> lang('common_miss'),
										"Ms." 		=> lang('common_ms'),
										"Hon." 		=> lang('common_hon.'),
										"Prof." 	=> lang('common_prof.'),
										"Rev." 		=> lang('common_rev.'),
										"Rt.Hon." 	=> lang('common_rt_hon.'),
										"Sr." 		=> lang('common_sr.'),
										"Jr." 		=> lang('common_jr.'),
										"St." 		=> lang('common_st.'),

										);
									?>
									<?php echo form_dropdown('title', $titles,$person_info->title, 'class="form-control form-control-sm form-inps" id="title"');?>
							    </div>
								<?php echo form_input(array(
									'class'	=>	'form-control',
									'name'	=>	'first_name',
									'id'	=>	'first_name',
									'value'	=>	$person_info->first_name)
								);?>
							</div>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_last_name').':', 'last_name',array('class'=>' col-sm-3 col-md-3 col-lg-3 control-label  ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'class'	=>	'form-control',
							'name'	=>	'last_name',
							'id'	=>	'last_name',
							'value'	=>	$person_info->last_name)
						);?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_email').':', 'email',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label  '.($controller_name == 'employees' || $controller_name == 'login' ? 'required' : 'not_required'))); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'class'	=>	'form-control',
							'name'	=>	'email',
							'type'	=>	'text',
							'id'	=>	'email',
							'value'	=>	$person_info->email)
							);?>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('common_phone_number').':', 'phone_number',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label  ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'class'	=>	'form-control',
							'name'	=>	'phone_number',
							'id'	=>	'phone_number',
							'value'	=>	format_phone_number($person_info->phone_number)));?>
						</div>
					</div>

					<div class="form-group">	
						<?php echo form_label(lang('common_address_1').':', 'address_1',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label  ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'class'	=>	'form-control',
							'name'	=>	'address_1',
							'id'	=>	'address_1',
							'value'	=>	$person_info->address_1));?>
						</div>
					</div>

					<div class="form-group">	
						<?php echo form_label(lang('common_address_2').':', 'address_1',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label  ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'class'	=>	'form-control',
							'name'	=>	'address_2',
							'id'	=>	'address_2',
							'value'	=>	$person_info->address_2));?>
						</div>
					</div>

					<div class="form-group">	
						<?php echo form_label(lang('common_city').':', 'city',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label  ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'class'	=>	'form-control ',
							'name'	=>	'city',
							'id'	=>	'city',
							'value'	=>	$person_info->city));?>
						</div>
					</div>
					<?php if($controller_name == 'customers') { ?>
					<div class="form-group">	
						<?php echo form_label(lang('common_company').':', 'company_name',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<?php echo form_input(array(
							'name'	=>	'company_name',
							'id'	=>	'company_name',
							'class'	=>	'company_names form-control',
							'value'	=>	$person_info->company_name)
							);?>
						</div>
					</div>
					

					<div class="form-group">	
						<?php echo form_label(lang('common_taxable').':', 'taxable',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<?php echo form_checkbox('taxable', '1', $person_info->taxable == '' ? TRUE : (boolean)$person_info->taxable,'id="taxable"');?>
							<label for="taxable"><span></span></label>
						</div>
					</div>
					
					<?php } ?>

					<?php					
					if($this->config->item('customers_store_accounts') && $this->Employee->has_module_action_permission('customers', 'edit_store_account_balance', $this->Employee->get_logged_in_employee_info()->person_id)) 
					{
					?>
					<div class="form-group">	
						<?php echo form_label(lang('common_store_account_balance').':', 'balance',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label  ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<?php echo form_input(array(
								'name'	=>	'balance',
								'id'	=>	'balance',
								'class'	=>	'form-control balance',
								'value'	=>	$person_info->balance ? to_currency_no_money($person_info->balance) : '0.00')
								);?>
							</div>
						</div>


					<?php if($controller_name == 'customers') { ?>
					<div class="form-group">	
						<?php echo form_label(lang('common_credit_limit').':', 'credit_limit',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label  ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'name'	=>	'credit_limit',
							'id'	=>	'credit_limit',
							'class'	=>	'form-control credit_limit',
							'value'	=>	$person_info->person_id ? ($person_info->credit_limit ? to_currency_no_money($person_info->credit_limit) : '') : ($this->config->item('default_credit_limit') ? to_currency_no_money($this->config->item('default_credit_limit')): ''))
							);?>
						</div>
					</div>
					<?php } ?>	
					<?php
					}
					elseif($this->config->item('customers_store_accounts'))
					{
						echo form_hidden('credit_limit', $person_info->person_id ? ($person_info->credit_limit ? to_currency_no_money($person_info->credit_limit) : '') : ($this->config->item('default_credit_limit') ? to_currency_no_money($this->config->item('default_credit_limit')): ''));
					?>
					<div class="form-group quantity-input">
						<?php echo form_label(lang('common_store_account_balance').':', '', array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label  wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<h5><?php echo $person_info->balance ? to_currency($person_info->balance) : to_currency(0); ?></h5>
						</div>
					</div>
					
					<?php if($controller_name == 'customers') { ?>
					<div class="form-group quantity-input">
						<?php echo form_label(lang('common_credit_limit').':', '', array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label  wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<h5><?php echo $person_info->credit_limit ? to_currency($person_info->credit_limit) : lang('common_none'); ?></h5>
						</div>
					</div>
					<?php } ?>	
					<?php
					}
					?>
					<hr>
					<?php echo form_hidden('redirect_code', $redirect_code); ?>
					<div class="modal-footer" style="padding:0px;">
						<div class="form-acions">
							<?php  
							if ($redirect_code == 1 && $person_id == 0) {
								$site_url = site_url($controller_name.'/view/-1/1');
							} elseif($redirect_code == 1 && $person_id >= 0) {
								$site_url = site_url($controller_name.'/view/'.$person_info->person_id.'/1');
							} elseif($redirect_code == 0 && $person_id == 0) {
								$site_url = site_url($controller_name.'/view/-1/');
							} else {
								$site_url = site_url($controller_name.'/view/'.$person_info->person_id.'/2');
							}

							if ($redirect_code == 1) { ?>
								<a href="<?php echo $site_url;?>" class="pull-left submit_button btn btn-primary"><?php echo lang('common_edit'); ?></a>
							<?php } else { ?>
								<a href="<?php echo $site_url;?>" class="pull-left submit_button btn btn-primary"><?php echo lang('common_edit'); ?></a>
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
					
//validation and submit handling
$(document).ready(function()
{
    setTimeout(function(){$(":input:visible:first","#employee_form").focus();},100);
    setTimeout(function(){$(":input:visible:first","#suppliers_form").focus();},100);
    setTimeout(function(){$(":input:visible:first","#customers_form").focus();},100);


	$('#employee_form').validate({
		submitHandler:function(form)
		{

			doEmployeeSubmit(form);
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
			first_name: "required",

			username:
			{
				required:true,
				minlength: 1
			},

			password:
			{
				minlength: 1
			},	
			repeat_password:
			{
 				equalTo: "#password"
			},
    		email: {
				"required": true
			}
		},
		messages: 
		{
     		first_name: <?php echo json_encode(lang('common_first_name_required')); ?>,
     		last_name: <?php echo json_encode(lang('common_last_name_required')); ?>,
     		username:
     		{
     			required: <?php echo json_encode(lang('common_username_required')); ?>,
     			minlength: <?php echo json_encode(lang('common_username_minlength')); ?>
     		},
			password:
			{
				minlength: <?php echo json_encode(lang('common_password_minlength')); ?>
			},
			repeat_password:
			{
				equalTo: <?php echo json_encode(lang('common_password_must_match')); ?>
     		},
     		email: <?php echo json_encode(lang('common_email_invalid_format')); ?>
		}
	});

	$('#suppliers_form').validate({
		submitHandler:function(form)
		{
			doEmployeeSubmit(form,'supplier');
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
			company_name: "required",
		},
		messages: 
		{
     		company_name: <?php echo json_encode(lang('suppliers_company_name_required')); ?>
		}
	});

	$('#customers_form').validate({
		submitHandler:function(form)
		{

			$.post('<?php echo site_url("customers/check_duplicate");?>', {name: $('#first_name').val()+' '+$('#last_name').val(), email: $("#email").val() ,phone_number: $("#phone_number").val()},function(data) {
					<?php if(!$person_info->person_id) { ?>
						if(data.duplicate)
						{
							bootbox.confirm(<?php echo json_encode(lang('customers_duplicate_exists'));?>, function(result)
							{
								if (result)
								{
									doEmployeeSubmit(form,'customer');
								}
							});
						}
						else
						{
							doEmployeeSubmit(form,'customer');
						}
					<?php } else { ?>
						doEmployeeSubmit(form,'customer');
					<?php } ?>
					} , "json")
				.error(function() { 
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
			first_name: "required",
		},
		messages: 
		{
     		first_name: <?php echo json_encode(lang('common_first_name_required')); ?>,
		}
	});
});

var submitting = false;

function doEmployeeSubmit(form,type = null)
{
	$('#grid-loader').show();
	if (submitting) return;
	submitting = true;

	$(form).ajaxSubmit({
		success:function(response)
		{

			$('#grid-loader').hide();
			window.location.reload(true);
			submitting = false;
			$('#myModalDisableClose').modal('hide');
			if (response.success)
			{
				if (type == 'customer') {
					$.post('<?php echo site_url("sales/select_customer");?>', {customer: response.person_id + '|FORCE_PERSON_ID|'}, function()
					{
						window.location.href = '<?php echo site_url('sales/index/1'); ?>';
					});
				}

				if (type == 'supplier') {
					$.post('<?php echo site_url("receivings/select_supplier");?>', {supplier: response.person_id}, function()
					{
						window.location.href = '<?php echo site_url('receivings'); ?>';
					});
				}
				show_feedback('success', response.message, <?php echo json_encode(lang('common_success')); ?>+' #' + response.person_id);

			}
			else
			{
				show_feedback('error', response.message, <?php echo json_encode(lang('common_error')); ?>);
			}
			
		},
		dataType:'json'
	});
}
</script>
