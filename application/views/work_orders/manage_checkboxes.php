<?php $this->load->view("partial/header"); ?>

<?php if($redirect) { ?>
<div class="manage_buttons">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-10">
			<div class="buttons-list">
				<div class="pull-right-btn">
				<?php echo 
					anchor(site_url($redirect), ' ' . lang('common_done'), array('class'=>'btn btn-primary btn-lg ion-android-exit', 'title'=>''));
				?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>

 <div class="row <?php echo $redirect ? 'manage-table' :''; ?>"> 
	<div class="col-md-12 form-horizontal">
		<div class="panel panel-piluku">
			<div class="panel-heading"><?php echo lang("work_orders_manage_checkbox"); ?></div>
			<div class="panel-body">
				<div class="row">	
					<div class="col-md-12 col-sm-12 col-lg-12">
						<div class="table-responsive">
							<table id="modifiers" class="table">
								<thead>
									<tr>
										<th><?php echo lang('common_edit'); ?></th>
										<th><?php echo lang('common_name'); ?></th>
										<th><?php echo lang('work_orders_pre'); ?></th>
										<th><?php echo lang('work_orders_post'); ?></th>
										<th><?php echo lang('common_delete'); ?></th>
									</tr>
								</thead>
						
								<tbody>
									<?php foreach($checkbox_groups as $group) { ?>
										<tr data-id="<?php echo H($group->id); ?>">
											<td> <a class="edit_modifier" href="<?php echo site_url('work_orders/checkbox_group/'.$group->id); ?>"><?php echo lang('common_edit'); ?></a></td>	
											<td class="group_name"> <?php echo H($group->name); ?></td>
											<td class="pre_checkboxes"><?php echo $group->pre_checkboxes; ?></td>
											<td class="post_checkboxes"><?php echo $group->post_checkboxes; ?></td>
											<td style="cursor: pointer;"><a class="delete_checkbox_group"><?php echo lang('common_delete'); ?></a></td>	
										</tr>
									<?php } ?>
								</tbody>
							</table>

							<a href="<?php echo site_url('work_orders/checkbox_group'); ?>" class="add_checkbox" style="margin:10px"><?php echo lang('work_orders_add_checkbox'); ?></a>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

</div>
<script type='text/javascript'>
	$(document).on('click', '.delete_checkbox_group', function(e) {
		var $tr = $(this).closest("tr");
		var id = $tr.data('id');
		
		bootbox.confirm(<?php echo json_encode(lang('work_orders_checkbox_delete_confirmation')); ?>, function(res){
			if (res){
				$.post(<?php echo json_encode(site_url('work_orders/delete_checkbox'));?>,{group_id: id}, function(response){
					$tr.remove();
					show_feedback(response.success ? 'success' : 'error', response.message, response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
				}, "json");
			}
		});
	});
	
</script>
<?php $this->load->view('partial/footer'); ?>
