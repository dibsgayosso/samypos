<?php $this->load->view("partial/header"); ?>

<?php if(isset($redirect)) { ?>
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

<?php echo form_open('invoices/save_term/',array('id'=>'term_form','class'=>'form-horizontal')); ?>
<div class="row <?php echo $redirect ? 'manage-table' :''; ?>">
	<div class="col-md-12">
		<div class="panel panel-piluku">
			<div class="panel-heading"><?php echo lang("invoices_manage_terms"); ?></div>
			<div class="panel-body">
				<a href="javascript:void(0);" class="add_term" data-term_id="0">[<?php echo lang('invoices_add_term'); ?>]</a>
					<div id="term_list" class="term-tree">
						<?php echo $term_list; ?>
					</div>
				<a href="javascript:void(0);" class="add_term" data-term_id="0">[<?php echo lang('invoices_add_term'); ?>]</a>
			</div>
		</div>
	</div>
</div><!-- /row -->

<?php  echo form_close(); ?>
</div>


<?php $this->load->view('partial/terms/term_modal', array('term' => isset($term_id) ? $term_id : NULL));?>

			
<script type='text/javascript'>

$(document).on('click', ".edit_term",function()
{
	
	$("#term-modal").modal('show');
	var term_id = $(this).data('term_id');
	var name = $(this).data('name');
	var description = $(this).data('description');
	var days_due = $(this).data('days_due');
	
	$("#term-modal #term_id").val(term_id);
	$("#term-modal #name").val(name);
	$("#term-modal #description").val(description);
	$("#term-modal #days_due").val(days_due);
	$("#term-modal").prop('checked', false);	
});



$("#terms_form").submit(function(event)
{
	event.preventDefault();
	$(this).ajaxSubmit({ 
		success: function(response, statusText, xhr, $form){
			show_feedback(response.success ? 'success' : 'error', response.message, response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
			if(response.success)
			{
				$("#term-modal").modal('hide');
				$('#term_list').load("<?php echo site_url("invoices/term_list"); ?>");
			}		
		},
		dataType:'json',
	});
});

$(document).on('click', ".add_term",function()
{
	$("#term-modal").modal('show');
	
	$("#term-modal #term_id").val('');
	$("#term-modal #name").val('');
	$("#term-modal").prop('checked', false);
	
});

$(document).on('click', ".delete_term",function()
{
	var term_id = $(this).data('term_id');
	if (term_id)
	{
		bootbox.confirm(<?php echo json_encode(lang('invoices_term_delete_confirmation')); ?>, function(result)
		{
			if (result)
			{
				$.post('<?php echo site_url("invoices/delete_term");?>', {term_id : term_id},function(response) {
				
					show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);

					//Refresh tree if success
					if (response.success)
					{
						$('#term_list').load("<?php echo site_url("invoices/term_list"); ?>");
					}
				}, "json");
			}
		});
	}
	
});

</script>
<?php $this->load->view('partial/footer'); ?>