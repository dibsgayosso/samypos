<?php echo form_open('employees/do_edit_profile',array('id'=>'employee_form','class'=>'form-horizontal')); ?>
<div class="modal-dialog" id="employee_form_model" tabindex="-2" style="z-index:1">
	<div class="modal-content">
		<div class="modal-header">	
			<button type="button" class="close" data-dismiss="modal" id="close-modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true" class="ti-close"></span></button>
			
			<h4 class="modal-title"> <?php echo lang('common_edit_profile'); ?></h4>
		</div>
		<div class="modal-body ">

			<div class="row">
				<div class="col-md-12">
					<i id="spin" class="fa fa-spinner fa fa-spin  hidden"></i>
					<span id="error_message" class="text-danger">&nbsp;</span>
					<input type="hidden" name="edit_profile_model" value="1">
					<div class="form-group">
						<?php 
						$required = ($controller_name == "suppliers") ? "" : "required";
						echo form_label(lang('common_first_name').':', 'first_name',array('class'=>$required.' col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<?php echo form_input(array(
								'class'	=>	'form-control form-control-sm',
								'name'	=>	'first_name',
								'id'	=>	'first_name',
								'value'	=>	$person_info->first_name)
							);?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_last_name').':', 'last_name',array('class'=>' col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
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
						<?php echo form_label(lang('common_email').':', 'email',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label '.($controller_name == 'employees' || $controller_name == 'login' ? 'required' : 'not_required'))); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'class'	=>'	form-control',
							'name'	=>	'email',
							'type'	=>	'text',
							'id'	=>	'email',
							'value'	=>	$person_info->email)
							);?>
						</div>
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('common_phone_number').':', 'phone_number',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'class'	=>	'form-control',
							'name'	=>	'phone_number',
							'id'	=>	'phone_number',
							'value'	=>	format_phone_number($person_info->phone_number)));?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_username') . ':', 'username', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<?php echo form_input(array(
								'name' 	=> 'username',
								'id' 	=> 'username',
								'class' => 'form-control',
								'value' => $person_info->username
							)); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_password') . ':', 'password', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<?php echo form_password(array(
								'name' 			=> 'password',
								'id' 			=> 'password',
								'class' 		=> 'form-control',
								'autocomplete' 	=> 'off',
							)); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_repeat_password') . ':', 'repeat_password', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<?php echo form_password(array(
								'name' 			=> 'repeat_password',
								'id' 			=> 'repeat_password',
								'class' 		=> 'form-control',
								'autocomplete' 	=> 'off',
							)); ?>
						</div>
					</div>
					<hr>

					<div class="text-center font-pt-sans">
						<?php if ($this->config->item('allow_employees_to_use_2fa')) { ?> 
							<strong><?php echo lang('common_two_factor_authentication');?></strong><br>

							<div class="temp_show_active_status" style="display:none">
								<div class="row">
									<div class="col-sm-5 col-md-3 col-lg-12 text-center">
										<div class="alert alert-success">
											<?php echo lang('common_2fa_is_active'); ?>
										</div>
									</div>
									<div class="col-sm-5 col-md-4 col-lg-12">
										<a class="btn btn-danger btn-lg input_radius disable_2fa_btn" id="" style="margin-top:7px;"><?php echo lang('common_disable_2fa'); ?></a>
									</div>
								</div>
							</div>

							<button type="button" id="" class="btn btn-primary btn-lg input_radius temp_show_2fa" style="display: none">
								<?php echo lang('common_setup_2fa'); ?>
							</button>


						<?php if ($person_info->secret_key_2fa) { ?>
							<div class="row 2fa_disable">
								<div class="col-sm-5 col-md-3 col-lg-12 text-center">
									<div class="alert alert-success">
										<?php echo lang('common_2fa_is_active'); ?>
									</div>
								</div>
								<div class="col-sm-5 col-md-4 col-lg-12">
									<a class="btn btn-danger btn-lg input_radius disable_2fa_btn" id="" style="margin-top:7px;"><?php echo lang('common_disable_2fa'); ?></a>
								</div>
							</div>
						<?php } else { ?>
							<button type="button" id="" class="btn btn-primary btn-lg input_radius show_2fa">
								<?php echo lang('common_setup_2fa'); ?>
							</button>
						<?php } ?>
					<?php } ?>
					</div>	
					
				</div>

				<!-- Enable 2FA -->
				<div class="text-center font-pt-sans" id="2fa_data" style="display: none;" >
					<h4 style="padding-top:5px;">
						<strong><?php echo lang('common_scan_code_with_your_authenticator_app'); ?></strong>
					</h4>
					<?= $qr_code ?>
					
						<div class="form-group">
							<?php echo form_input(array(
								'type'  => 'text',
								'name'  => 'security_code',
								'id'    => 'security_code',
								'class'	=> 'form-control form-inps',
								'style'	=> 'width:50%; display:unset;',
							)); ?>
							<br>
							<span><?php echo lang('common_enter_the_code_from_your_app_to_complete_setup'); ?></span>
						</div>
						
						<?php echo form_hidden('secret_key', $secret_key); ?>
						<button type="button" class="submit_button btn btn-primary btn-lg" id="2fa_submit">
							<?php echo lang('common_verify');?>
						</button>
	            </div>
	            <!-- End Enable 2FA -->

	            <!-- Disable 2FA -->
	            <div class="text-center font-pt-sans" id="disable_2fa_form" style="display:none">
					<h4><strong><?php echo lang('common_enter_code_to_disable_2fa'); ?></strong></h4>
					<?php echo form_open_multipart('employees/disable_2fa/',array('id'=>'disable_2fa_form')); ?>
						<div class="form-group">
							<?php echo form_input(array(
								'type'  => 'text',
								'name'  => 'security_code_to_disable',
								'id'    => 'security_code_to_disable',
								'class'	=> 'form-control form-inps',
								'style'	=> 'width:50%; display:unset;',
							)); ?>
						</div>
						<button type="button" class="submit_button btn btn-primary btn-lg" id="disable_2fa_button">
							<?php echo lang('common_verify');?>
						</button>
						
					<?php echo form_close(); ?>
				</div>
				<!-- End Dsiable 2FA -->
			</div>
		</div>
		
		<hr>
		<div class="modal-footer">
			<div class="form-acions">
				<a href="<?php echo site_url('employees/edit_profile');?>" class="pull-left submit_button btn btn-primary">
					<?php echo lang('common_edit_profile'); ?>
				</a>
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
	
<?php echo form_close(); ?>

<script type='text/javascript'>
	$('#image_id').imagePreview({
		selector: '#avatar'
	}); // Custom preview container

	//validation and submit handling
	$(document).ready(function() {

		$(".show_2fa, .temp_show_2fa").click(function(){
		   $("#2fa_data").toggle();
		});

		$(".disable_2fa_btn").click(function(){
		   $("#disable_2fa_form").toggle();
		});



		setTimeout(function() {
			$(":input:visible:first", "#employee_form").focus();
		}, 100);

		$('#employee_form').validate({
			submitHandler: function(form) {
				doEmployeeSubmit(form);
			},
			errorClass: "text-danger",
			errorElement: "span",
			highlight: function(element, errorClass, validClass) {
				$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
			},
			unhighlight: function(element, errorClass, validClass) {
				$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
			},
			rules: {
				first_name: "required",

				username: {
					required: true,
					minlength: 1
				},

				password: {
					minlength: 1
				},
				repeat_password: {
					equalTo: "#password"
				},
				email: {
					"required": true
				}
			},
			messages: {
				first_name: <?php echo json_encode(lang('common_first_name_required')); ?>,
				last_name: <?php echo json_encode(lang('common_last_name_required')); ?>,
				username: {
					required: <?php echo json_encode(lang('common_username_required')); ?>,
					minlength: <?php echo json_encode(lang('common_username_minlength')); ?>
				},
				password: {
					minlength: <?php echo json_encode(lang('common_password_minlength')); ?>
				},
				repeat_password: {
					equalTo: <?php echo json_encode(lang('common_password_must_match')); ?>
				},
				email: <?php echo json_encode(lang('common_email_invalid_format')); ?>
			}
		});
	});

	var submitting = false;

	function doEmployeeSubmit(form) {
		$('#grid-loader').show();
		if (submitting) return;
		submitting = true;

		$(form).ajaxSubmit({
			success: function(response) {
				$('#grid-loader').hide();
				submitting = false;
				$('#myModal').modal('hide');
				if (response.success) {
					show_feedback('success', response.message, <?php echo json_encode(lang('common_success')); ?> + ' #' + response.person_id);
					document.getElementById('close-modal').click();


				} else {
					show_feedback('error', response.message, <?php echo json_encode(lang('common_error')); ?>);
				}

			},
			dataType: 'json'
		});
	}


	$("#2fa_submit").click(function(){
		if($('#security_code').val() == '') {
			show_feedback('error', <?php echo json_encode(lang('common_please_enter_code')); ?>, <?php echo json_encode(lang('common_error')); ?>);
			$('#security_code_to_disable').focus();
		} else {
			var url = '<?php echo site_url().'/employees/verify_2fa_code/';?>';
			$(this).ajaxSubmit({ 
				type: "POST",
        		url: url,
        		data: {
		            'security_code':  $('#security_code').val(),
		            'secret_key':  $(`[name="${'secret_key'}"]`).val(),
		        },
				success: function(response, statusText, xhr, $form){
					show_feedback(response.success ? 'success' : 'error', response.message, response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
					
					if(response.success) {
						$("#2fa_data").hide();
						$(".2fa_disable").hide();
						$(".temp_show_2fa").hide();
						$(".show_2fa").hide();
						
						$(".temp_show_active_status").show();
						
						 
					} else {
						$('#security_code_to_disable').select();
					}		
				},
				dataType:'json',
			});
		}
	});


	$("#disable_2fa_button").click(function(){
		if($('#security_code_to_disable').val() == '') {
			show_feedback('error', <?php echo json_encode(lang('common_please_enter_code')); ?>, <?php echo json_encode(lang('common_error')); ?>);
			$('#security_code_to_disable').focus();
		} else {
			var url = '<?php echo site_url().'/employees/disable_2fa/';?>';
			$(this).ajaxSubmit({ 
				type: "POST",
        		url: url,
        		data: {
		            'security_code_to_disable':  $('#security_code_to_disable').val(),
		        },
				success: function(response, statusText, xhr, $form){
					show_feedback(response.success ? 'success' : 'error', response.message, response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
					
					if(response.success) {
						$("#disable_2fa_form").hide();
						$(".2fa_disable").hide();
						$(".temp_show_active_status").hide();
						$(".temp_show_2fa").show();
					} else {
						$('#security_code_to_disable').select();
					}		
				},
				dataType:'json',
			});
		}
	});

	$('#security_code').focus();	
</script>