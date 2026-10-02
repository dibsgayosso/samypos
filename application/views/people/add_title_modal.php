<div class="modal fade title-input-data" id="title-input-data" tabindex="-1" role="dialog" aria-labelledby="titleData" aria-hidden="true">
    <div class="modal-dialog customer-recent-sales">
      	<div class="modal-content">
	        <div class="modal-header">
	          	<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true">&times;</span></button>
	          	<h4 class="modal-title" id="titleModalDialogTitle">&nbsp;</h4>
	        </div>
	        <div class="modal-body">
				<!-- Form -->
				<?php echo form_open_multipart('customers/add_title/',array('id'=>'titles_form','class'=>'form-horizontal')); ?>
				
					<div class="form-group">
						<?php echo form_label(lang('common_title').' '. lang('common_name') .':', 'new_title_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<?php echo form_input(array(
								'type'  => 'text',
								'name'  => 'new_title_name',
								'id'    => 'new_title_name',
								'value' => '',
								'class'=> 'form-control form-inps',
							)); ?>
						</div>
					</div>

					<div class="form-actions">
						<?php
							echo form_submit(array(
								'name'=>'submitf',
								'id'=>'submitf',
								'value'=>lang('common_save'),
								'class'=>'submit_button pull-right btn btn-primary')
							);
						?>
						<div class="clearfix">&nbsp;</div>
					</div>
			
				<?php echo form_close(); ?>
	        </div>
    	</div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
	$("#add_title").on("click", function(){
        // init disalog
        $("#titleModalDialogTitle").html(<?php echo json_encode(lang('common_add').' '.lang('common_title')); ?>);
        $("#new_title_name").val("");

        //show
        $("#title-input-data").modal('show');
	});

	$("#titles_form").on("submit", function(){
		event.preventDefault();

		let new_title_name = $("#new_title_name").val();
		if(new_title_name.trim().length == 0)
			return;

		let href = $("#titles_form").attr("action");
        $("#grid-loader").show();
		$.ajax({
			type: "POST",
			url: href,
			dataType: 'json',
			data: { title: new_title_name },
			success: function(result) {

                $("#grid-loader").hide();
				if(result.success == false){
					show_feedback('error', result.message, "<?php echo lang("common_error");?>");
				}else{
					show_feedback('success', result.message, "<?php echo lang("common_success");?>");
					$("#title-input-data").modal('hide');
					$("#title").append($('<option>', {
						value: result.value,
						text: new_title_name
					}));
					$("#title").val(result.value);
				}
			}
		});
	});

</script>
