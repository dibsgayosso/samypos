<?php $this->load->view("partial/header"); ?>

<div class="row" id="form">

	<!--Disable 2FA Modal -->
	<div class="modal fade" id="disable_2fa_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content customer-recent-sales">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span style = "font-size: 30px;" aria-hidden="true">&times;</span></button>
					<h5 style = "font-size: 20px;" class="modal-title"><?php echo lang('common_disable_2fa'); ?></h5>
				</div>
				<div class="modal-body">
					<div class="text-center">
						<h4><?php echo lang('common_enter_code_to_disable_2fa'); ?></h4>

						<?php echo form_open_multipart('employees/disable_2fa/',array('id'=>'disable_2fa_form')); ?>
							<div class="form-group">
								<?php echo form_input(array(
									'type'  => 'text',
									'name'  => 'security_code_to_disable',
									'id'    => 'security_code_to_disable',
									'class'=> 'form-control form-inps',
									'style'=> 'width:50%; display:unset;',
								)); ?>
							</div>

							<div class="form-actions">
								<?php
									echo form_submit(array(
										'name'=>'submitf',
										'value'=>lang('common_verify'),
										'class'=>'submit_button pull-right btn btn-primary btn-lg')
									);
								?>
								<div class="clearfix">&nbsp;</div>
							</div>
						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="spinner" id="grid-loader" style="display:none">
		<div class="rect1"></div>
		<div class="rect2"></div>
		<div class="rect3"></div>
	</div>
	<div class="col-md-12">
		<div class="panel panel-piluku">
			<div class="panel-heading">
                <h3 class="panel-title">
                    <i class="ion-edit"></i> 
                    <?php echo lang('common_edit_profile');?>			
                    <small><?php echo lang('common_fields_required_message');?></small>

                </h3>
	        </div>
	        <div class="panel-body">
	        	<?php
					echo form_open('employees/do_edit_profile', array('id' => 'employee_form', 'class' => 'form-horizontal'));
				?>
				<?php $this->load->view("people/form_basic_info"); ?>


				<legend class="page-header text-info"> &nbsp; &nbsp; <?php echo lang("common_login_info"); ?></legend>
				<div class="form-group">
					<?php echo form_label(lang('common_username') . ':', 'username', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name' => 'username',
							'id' => 'username',
							'class' => 'form-control',
							'value' => $person_info->username
						)); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('common_password') . ':', 'password', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_password(array(
							'name' => 'password',
							'id' => 'password',
							'class' => 'form-control',
							'autocomplete' => 'off',
						)); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('common_repeat_password') . ':', 'repeat_password', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_password(array(
							'name' => 'repeat_password',
							'id' => 'repeat_password',
							'class' => 'form-control',
							'autocomplete' => 'off',
						)); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('common_language') . ':', 'language', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown(
							'language',
							array(
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
							$person_info->language ? $person_info->language : $this->Appconfig->get_raw_language_value(),
							'class="form-control"'
						);
						?>
					</div>
				</div>

				<?php if ($this->config->item('allow_employees_to_use_2fa')) { ?>
					<div class="form-group">
						<?php echo form_label(lang('common_two_factor_authentication') . ':', '', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-8 col-lg-9">
							<?php if ($person_info->secret_key_2fa) { ?>
								<div class="row">
									<div class="col-sm-5 col-md-3 col-lg-2">
										<div class="alert alert-success">
											<?php echo lang('common_2fa_is_active'); ?>
										</div>
									</div>
									<div class="col-sm-5 col-md-4 col-lg-3">
										<a class="btn btn-primary btn-lg input_radius" id="disable_2fa_btn" style="margin-top:7px;"><?php echo lang('common_disable_2fa'); ?></a>
									</div>
								</div>
							<?php } else { ?>
								<a tabindex="-1" title="" href="<?php echo site_url('employees/setup_2fa') ?>" class="btn btn-primary btn-lg input_radius" data-toggle="modal" data-target="#myModal">
									<?php echo lang('common_setup_2fa'); ?>
								</a>
							<?php } ?>
						</div>
					</div>
				<?php } ?>

				<div class="modal-footer">
					<div class="form-acions">
						<?php
						echo form_submit(
							array(
								'name' => 'submitf',
								'id' => 'submitf',
								'value' => lang('common_save'),
								'class' => 'btn btn-primary btn-lg submit_button floating-button btn-large'
							)
						);

						?>
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
	$('#image_id').imagePreview({
		selector: '#avatar'
	}); // Custom preview container

	//validation and submit handling
	$(document).ready(function() {
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
				} else {
					show_feedback('error', response.message, <?php echo json_encode(lang('common_error')); ?>);
				}

			},
			dataType: 'json'
		});
	}

	$("#disable_2fa_btn").click(function(e){
		e.preventDefault();
		$("#disable_2fa_modal").modal('show');

		$("#disable_2fa_modal").on('shown.bs.modal', function (e) {
			$('#security_code_to_disable').focus();
		});
	});

	$("#disable_2fa_form").submit(function(e)
	{
		e.preventDefault();

		if($('#security_code_to_disable').val() == ''){
			show_feedback('error', <?php echo json_encode(lang('common_please_enter_code')); ?>, <?php echo json_encode(lang('common_error')); ?>);
			$('#security_code_to_disable').focus();
		}
		else{
			$(this).ajaxSubmit({ 
				success: function(response, statusText, xhr, $form){
					show_feedback(response.success ? 'success' : 'error', response.message, response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
					
					if(response.success){
						$('#disable_2fa_modal').modal('hide');
						setTimeout(function(){window.location.reload()},800);
					}
					else{
						$('#security_code_to_disable').select();
					}		
				},
				dataType:'json',
			});
		}
	});

</script>

<?php $this->load->view("partial/footer"); ?>