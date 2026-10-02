<?php $this->load->view("partial/header"); ?>

<div class="row">
	
		<?php
		
	    $cur_location_info = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id());
	    if ($cur_location_info->coreclear_mx_merchant_id && $cur_location_info->coreclear_consumer_key && $cur_location_info->coreclear_secret_key) 
		{
			?>
			<div class="text-center">
		
				<?php
			if (!$this->session->userdata('use_backup_gateway'))
			{
			?>	
				<a href="<?php echo site_url('sales/set_session_var/use_backup_gateway/1/0');?>" class="gateway btn btn-primary" id="use_backup_gateway"><?php echo lang('sales_use_backup_gateway');?></a>
					
			<?php }
			else
			{
			?>
				<a href="<?php echo site_url('sales/set_session_var/use_backup_gateway/0/0');?>" class="gateway btn btn-danger" id="disable_backup_gateway"><?php echo lang('sales_disable_backup_gateway');?></a>
					
			<?php
			} ?>
			<br />
			<br />
			
			</div>
			
			<script>
			$(".gateway").click(function(e)
			{
				$.get($(this).attr('href'));
				$(this).remove();
				e.preventDefault();
				
				bootbox.alert(<?php echo json_encode(lang('common_success')); ?>);
				
			});
			</script>
		<?php
		}
		?>

	
	<div class="col-md-12">
		<div class="panel panel-piluku">
			<div class="panel-body relative">
				<div class="spinner" id="grid-loader" style="display:none">
				  <div class="rect1"></div>
				  <div class="rect2"></div>
				  <div class="rect3"></div>
				</div>
				<h2><?php echo lang('common_amount').': '?> <span class="text-success"><?php echo $cc_amount ?></span></h2>
				<div id="coreclear_checkout">
					<?php echo form_open('sales/start_cc_processing_coreclear2/',array('id'=>'coreclear_checkout_form','class'=>'form-horizontal', 'autocomplete'=> 'off'));  ?>
						<div id="cc_info">
							<ul id="error_message_box" class="text-danger"></ul>
							<input type="text" id="swipe" class="form-control" placeholder="<?php echo H(lang('sales_swipe_cc')); ?>">
							<br />
							<input type="text" id="cc_number" name = "cc_number" class="form-control" placeholder="<?php echo H(lang('sales_credit_card_no')); ?>">
							<input type="text" id="cc_exp_date" name="cc_exp_date" class="form-control" placeholder="<?php echo H(lang('sales_exp_date').'(MM/YYYY)'); ?>">
							<input type="text" id="cvv" name="cvv" class="form-control" placeholder="CVV">

							<?php 
							echo form_button(array(
							'name' => 'cancel',
							'id' => 'cancel',
							'class' => 'submit_button btn btn-danger',
							'value' => 'true',
							'content' => lang('common_cancel')
							));

							echo form_submit(array(
								'name'=>'submitf',
								'id'=>'submitf',
								'value'=>lang('common_save'),
								'class'=>'submit_button btn btn-primary ')); ?>
						</div>
					<?php echo form_close();?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-12 text-center cancel_process_btn_div m-t-10" style="display:none">
		<?php 
			echo form_button(array(
			'name' => 'cancel',
			'id' => 'cancel_process',
			'class' => 'submit_button btn btn-danger',
			'value' => 'true',
			'content' => lang('common_cancel')
			));
		?>					
	</div>	
<script src="<?php echo base_url().'assets/js/parse_cc_track.js'.'?'.ASSET_TIMESTAMP;?>" type="text/javascript" charset="UTF-8"></script>

<script type="text/javascript">
$(document).ready(function()
{
	var i = 0;
	$("#cancel").click(cancelCC);
	$("#cancel_process").click(cancel_process);
		
	$("#coreclear_checkout_form").submit(function()
	{

		$("#grid-loader").show();
		$(".cancel_process_btn_div").show();

		var $form = $('#coreclear_checkout_form');
		
		$form.get(0).submit();
		return false;
	});	
	
	
	$("#swipe").focus();
	
	$("#swipe").keypress(function(e)
	{
		var TrackData=$(this).val() ? $(this).val(): '';
		if(TrackData!='')
		{
			if(e.keyCode==13)
			{				
				e.preventDefault();
				parseSwipe(TrackData);
			}
		}
	});
	
});

function cancelCC()
{
	bootbox.confirm(<?php echo json_encode(lang('sales_cc_are_you_sure_cancel')); ?>, function(result)
	{
		if (result)
		{
			window.location = <?php echo json_encode(site_url('sales/cancel_cc_processing')); ?>;
		}
	});
}

function cancel_process()
{
	window.location = <?php echo json_encode(site_url('sales/cancel_cc_processing')); ?>;
}

function parseSwipe(TrackData)
{
	var p=new SwipeParserObj(TrackData);
	if(p.account)
	{
		$('#cc_number').val(p.account);
		$('#cc_exp_date').val(p.exp_month+'/'+p.exp_year);
		$('#cvv').val('');
		$("#swipe").val('');
		
		<?php if ($this->config->item('prompt_for_ccv_swipe')) { ?>
			$("#cvv").focus();
			<?php } else { ?>
			$("#submitf").click();
		<?php } ?>
	}
	else
	{
		$("#swipe").val('');
		bootbox.alert(<?php echo json_encode(lang("sales_invalid_swipe")); ?>);
	}
}


</script>
<?php $this->load->view("partial/footer"); ?>
