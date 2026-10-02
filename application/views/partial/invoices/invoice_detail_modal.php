<div class="modal fade" id="<?php echo $modal_id; ?>" tabindex="-1" role="dialog" aria-labelledby="invoiceData" aria-hidden="true">
    <div class="modal-dialog customer-recent-sales">
		<div class="modal-content">
	        <div class="modal-header">
	          	<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true">&times;</span></button>
	          	<h4 class="modal-title" id="invoiceModalDialogTitle"><?php echo H(lang('invoices_invoice_detail'));?></h4>
	        </div>
	        <div class="modal-body">
				<!-- Form -->
				<?php echo form_open($action,array('id'=>'invoices_form','class'=>'form-horizontal')); ?>
				
				<div class="form-group">
					<?php echo form_label(lang('common_description').':', 'description',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'type'  => 'text',
							'name'  => 'description',
							'id'    => 'description',
							'value' => '',
							'class'=> 'form-control form-inps',
						)); ?>
					</div>
				</div>
				
				
				<div class="form-group">
					<?php echo form_label(lang('common_amount').':', 'total',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'type'  => 'text',
							'name'  => 'total',
							'id'    => 'total',
							'value' => '',
							'class'=> 'form-control form-inps',
						)); ?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_account').':', 'account',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'type'  => 'text',
							'name'  => 'account',
							'id'    => 'account',
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