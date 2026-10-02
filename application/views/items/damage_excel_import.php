<?php $this->load->view("partial/header"); ?>
<div class="row" id="form">
	<div class="spinner" id="grid-loader" style="display:none">
	  <div class="rect1"></div>
	  <div class="rect2"></div>
	  <div class="rect3"></div>
	</div>
	<div class="col-md-12">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<?php echo lang('common_mass_import_from_excel'); ?>
			</div>
			<div class="panel-body">
				<center>
				<h3>
					<strong>
						<?php echo lang('items_upload_excel_file_to_update_damage_products_inventory')?>
					</strong>
				</h3>
				<a class="btn btn-green btn-sm " href="<?php echo site_url('items/damage_excel'); ?>">
					<?php echo lang('items_download_excel_template')?>
				</a>
				<br>
				<img src="<?php echo base_url('assets/img/damage-product.png') ?>" width="200">
				</center>
				<?php echo form_open_multipart('items/do_damage_excel_import/',array('id'=>'supplier_form','class'=>'form-horizontal')); ?>
					<div class="form-group">
						<ul class="text-danger" id="error_message_box"></ul>
						<?php echo form_label(lang('common_file_path').':', 'file_path',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<ul class="list-inline">
								<li>
									<input type="file" name="file_path" id="file_path" class="filestyle" data-icon="false" >
								</li>
								<li>
									<?php echo form_submit(array(
									'name'=>'submitf',
									'id'=>'submitf',
									'value'=>lang('common_save'),
									'class'=>'btn btn-primary')
									); ?>
								</li>
							</ul>
						</div>
					</div>
				<?php echo form_close() ?>
			</div>
		</div>	
	</div>
</div>
</div>
		
<script type='text/javascript'>
	//validation and submit handling
	$(document).ready(function()
	{	
		var submitting = false;
		$('#supplier_form').validate({
			submitHandler:function(form)
			{
				if (submitting) return;
				submitting = true;
$('#grid-loader').show();
				$(form).ajaxSubmit({
					success:function(response)
					{
$('#grid-loader').hide();
						if(!response.success)
						{ 
							show_feedback('error', response.message, <?php echo json_encode(lang('common_error')); ?>,{timeOut:0, extendedTimeOut:0});
						}
						else
						{
							show_feedback('success', response.message, <?php echo json_encode(lang('common_success')); ?>,{timeOut:0, extendedTimeOut:0});
						}
						submitting = false;
					},
					dataType:'json',
					resetForm: true
				});
			},
			errorLabelContainer: "#error_message_box",
	 		wrapper: "li",
			highlight:function(element, errorClass, validClass) {
				$(element).parents('.form-group').addClass('error');
			},
			unhighlight: function(element, errorClass, validClass) {
				$(element).parents('.form-group').removeClass('error');
				$(element).parents('.form-group').addClass('success');
			},
			rules: 
			{
				file_path:"required"
	   		},
			messages: 
			{
	   			file_path:<?php echo json_encode(lang('common_full_path_to_excel_required')); ?>
			}
		});
	});
</script>
<?php $this->load->view("partial/footer"); ?>