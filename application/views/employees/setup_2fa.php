<div class="modal-dialog">
	<div class="modal-content customer-recent-sales">
		<div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span style = "font-size: 30px;" aria-hidden="true">&times;</span></button>
	        <h5 style = "font-size: 20px;" class="modal-title"><?php echo lang('common_setup_two_factor_authentication'); ?></h5>
		</div>
		<div class="modal-body">
            <div class="text-center">
				<h3><?php echo lang('common_scan_code_with_your_authenticator_app'); ?></h3>
				<?= $qr_code ?>
				<h4><?php echo lang('common_enter_the_code_from_your_app_to_complete_setup'); ?></h4>

				<?php echo form_open_multipart('employees/verify_2fa_code/',array('id'=>'setup_2fa_form')); ?>
					<div class="form-group">
						<?php echo form_input(array(
							'type'  => 'text',
							'name'  => 'security_code',
							'id'    => 'security_code',
							'class'=> 'form-control form-inps',
							'style'=> 'width:50%; display:unset;',
						)); ?>
					</div>
					
					<?php echo form_hidden('secret_key', $secret_key); ?>

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

<script>
	$('#security_code').focus();

	$("#setup_2fa_form").submit(function(e)
	{
		e.preventDefault();

		if($('#security_code').val() == ''){
			show_feedback('error', <?php echo json_encode(lang('common_please_enter_code')); ?>, <?php echo json_encode(lang('common_error')); ?>);
			$('#security_code').focus();
		}
		else{
			$(this).ajaxSubmit({ 
				success: function(response, statusText, xhr, $form){
					show_feedback(response.success ? 'success' : 'error', response.message, response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
					
					if(response.success){
						$('#myModal').modal('hide');
						setTimeout(function(){window.location.reload()},800);
					}
					else{
						$('#security_code').select();
					}		
				},
				dataType:'json',
			});
		}
	});
</script>