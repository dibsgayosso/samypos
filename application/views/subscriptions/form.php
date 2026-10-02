<?php $this->load->view("partial/header"); ?>
<div class="row" id="form">
	
	<div class="spinner" id="grid-loader" style="display:none">
	  <div class="rect1"></div>
	  <div class="rect2"></div>
	  <div class="rect3"></div>
	</div>
	<div class="col-md-12">
		 <?php echo form_open('customer_subscriptions/save/'.$subscription_info->id,array('id'=>'customer_subscriptions_form','class'=>'form-horizontal')); ?>
		<div class="panel panel-piluku">
			<div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="ion-edit"></i> <?php if(!$subscription_info->id) { echo lang('customer_subscriptions_new'); } else { echo lang('customer_subscriptions_update'); } ?>
								<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
	                </h3>
						 
            </div>
			<div class="panel-body">
				
				
				<div class="form-group">
					<?php echo form_label(lang('common_next_payment_date').':', 'interval',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<strong><?php echo date(get_date_format(),strtotime($this->Customer_subscription->get_next_payment_date($subscription_info->id))); ?></strong>
					</div>
				</div>
				
				
				<div class="form-group">
					<?php echo form_label(lang('common_card_on_file').':', 'interval',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<strong><?php echo $subscription_info->card_on_file_masked; ?></strong>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_recurring_amount').':', 'interval',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'recurring_charge_amount',
							'id'=>'recurring_charge_amount',
							'value'=>to_currency_no_money($subscription_info->recurring_charge_amount)));?>
					</div>
				</div>
								
					
				<div id="recurring_options">
											
				<div class="form-group">
					<?php echo form_label(lang('items_interval').':', 'interval',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('interval', array('weekly' => lang('items_weekly'),'monthly_on_day_of_month' => lang('items_monthly_on_day_of_month'),'monthly_on_day_of_week' => lang('items_monthly_on_day_of_week'), 'yearly_on_date' => lang('items_yearly_on_date'), 'yearly_on_month_on_day_of_week' => lang('items_yearly_on_month_on_day_of_week')), $subscription_info->interval,'class="form-control" id="interval"');?>
					</div>
				</div>
				
				
				
				<div class="form-group" id="month_container" style="display:none;">
					<?php echo form_label(lang('common_month').':', 'month',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('month', get_months('n'), $subscription_info->month,'class="form-control" id="month"');?>
					</div>
				</div>
				
				
				<?php
					$day_numbers = array();
					
					foreach(range(1,31) as $day)
					{
						$day_numbers[$day] = $day;
					}
				?>
				<div class="form-group" id="day_number_container" style="display:none;">
					<?php echo form_label(lang('items_day_number').':', 'day_number',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('day_number', $day_numbers, $subscription_info->day_number,'class="form-control" id="day_number"');?>
					</div>
				</div>
							
								
				<div class="form-group" id="day_container" style="display:none;">
					<?php echo form_label(lang('common_day').':', 'day',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('day', array(1=> 'First', 2 => 'Second',  3=> 'Third', 4 => 'Fourth', 5 =>'Last'), $subscription_info->day,'class="form-control" id="day"');?>
					</div>
				</div>
				
				<div class="form-group" id="weekday_container" style="display:none;">
					<?php echo form_label(lang('items_weekday').':', 'weekday',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('weekday', array('0' => lang('common_sunday'),'1' => lang('common_monday'),'2' => lang('common_tuesday'), '3' => lang('common_wednesday'), '4' => lang('common_thursday'), '5' => lang('common_friday'), '6' => lang('common_saturday')), $subscription_info->weekday,'class="form-control" id="weekday"');?>
					</div>
				</div>
					
				
				
				
				
				<script>
					
					function interval_calc()
					{						
						switch($("#interval").val()) 
						{
						  case 'weekly':
							$("#day_number_container").hide();
	  						$("#month_container").hide();
	  						$("#weekday_container").show();
	  						$("#day_container").hide();
							  break;
						  case 'monthly_on_day_of_month':
							  $("#day_number_container").show();
	  						  $("#month_container").hide();
	    					  $("#weekday_container").hide();
  	  						  $("#day_container").hide();
	
							  break;

						  case 'monthly_on_day_of_week':
							  $("#day_number_container").hide();
	  						  $("#month_container").hide();
	    					  $("#weekday_container").show();
  	  						  $("#day_container").show();

							  break;

					   	  case 'yearly_on_date':
							  
							  $("#day_number_container").show();
	  						  $("#month_container").show();
	    					  $("#weekday_container").hide();
  	  						  $("#day_container").hide();
							  
							  break;

					   	 case 'yearly_on_month_on_day_of_week':
							  
							$("#day_number_container").hide();
							$("#month_container").show();
							$("#weekday_container").show();
							$("#day_container").show();
							  break;
 						}
					}
					$("#interval").change(interval_calc);
					interval_calc();					
					
				</script>
				
				
			
				
				</div>
				
				
				<div class="form-group">
					<?php echo form_label(lang('sales_credit_card_no').':', 'interval',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'cc_number',
							'id'=>'cc_number',
							'value'=>''));?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('sales_exp_date').':', 'interval',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'cc_exp_date',
							'placeholder'=> 'MM/YYYY',
							'id'=>'cc_exp_date',
							'value'=>''));?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label('CVV'.':', 'cvv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'cvv',
							'placeholder'=> 'cvv',
							'id'=>'cvv',
							'value'=>''));?>
					</div>
				</div>
				
				
				
				

			<?php echo form_hidden('redirect', $redirect_code); ?>

			<div class="form-actions pull-right">
			<?php
				echo form_submit(array(
					'name'=>'submitf',
					'id'=>'submitf',
					'value'=>lang('common_save'),
					'class'=>'btn btn-primary btn-lg submit_button floating-button btn-large')
					);
			?>
			</div>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>
</div>
</div>

<script type='text/javascript'>
<?php $this->load->view("partial/common_js"); ?>
var submitting = false;
//validation and submit handling
$(document).ready(function()
{
		$('#category_id').selectize({
			create: true,
			render: {
				item: function(item, escape) {
						var item = '<div class="item">'+ escape($('<div>').html(item.text).text()) +'</div>';
						return item;
				},
				option: function(item, escape) {
						var option = '<div class="option">'+ escape($('<div>').html(item.text).text()) +'</div>';
						return option;
				},
				option_create: function(data, escape) {
						var add_new = <?php echo json_encode(lang('common_new_category')) ?>;
					return '<div class="create">'+escape(add_new)+' <strong>' + escape(data.input) + '</strong></div>';
				}
			}
		});
	        	
        $('#customer_subscriptions_form').validate({
		ignore: ':hidden:not([class~=selectized]),:hidden > .selectized, .selectize-control .selectize-input input',
		submitHandler:function(form)
		{
			$('#grid-loader').show();
			if (submitting) return;
			submitting = true;
			$(form).ajaxSubmit({
			error: function(data ) { 
				console.log(data); 
			},
			success:function(response)
			{
				$('#grid-loader').hide();
				submitting = false;
				
				show_feedback(response.success ? 'success' : 'error',response.message, response.success ? <?php echo json_encode(lang('common_success')); ?>  : <?php echo json_encode(lang('common_error')); ?>);
				
				if(response.redirect==1 && response.success)
				{ 
					$.post('<?php echo site_url("customer_subscriptions");?>', {subscription: response.id}, function()
					{
						window.location.href = '<?php echo site_url('customer_subscriptions'); ?>'
					});					
				}
				if(response.redirect==2 && response.success)
				{ 
					window.location.href = '<?php echo site_url('customer_subscriptions'); ?>'
				}

			},
			
			<?php if(!$subscription_info->id) { ?>
			resetForm: true,
			<?php } ?>
			dataType:'json'
		});

		},
		errorClass: "text-danger",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
		}
	});
});

date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);

$("#employee_id").select2();
$("#approved_employee_id").select2();
$("#cash_register_id").select2();

// added for subscription category

$(document).on('click', "#add_category",function()
{
	$("#categoryModalDialogTitle").html(<?php echo json_encode(lang('common_add_category')); ?>);
	var parent_id = $("#category_id").val();
	
	$parent_id_select = $('#parent_id');
	$parent_id_select[0].selectize.setValue(parent_id, false);
	
	$("#categories_form").attr('action',SITE_URL+'/customer_subscriptions/save_category');
	
	//Clear form
	$(":file").filestyle('clear');
	$("#categories_form").find('#category_name').val("");

	
	//show
	$("#category-input-data").modal('show');
});

$("#categories_form").submit(function(event)
{
	event.preventDefault();

	$(this).ajaxSubmit({ 
		success: function(response, statusText, xhr, $form){
			show_feedback(response.success ? 'success' : 'error', response.message, response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
			if(response.success)
			{
				$("#category-input-data").modal('hide');
				
				var category_id_selectize = $("#category_id")[0].selectize
				category_id_selectize.clearOptions();
				category_id_selectize.addOption(response.categories);		
				category_id_selectize.addItem(response.selected, true);			
			}		
		},
		dataType:'json',
	});
});

	$('#subscription_image_id').imagePreview({ selector : '#avatar' }); // Custom preview container

	$('.delete_file').click(function(e)
	{
		e.preventDefault();
		var $link = $(this);
		bootbox.confirm(<?php echo json_encode(lang('common_confirm_file_delete')); ?>, function(response)
		{
			if (response)
			{
				$.get($link.attr('href'), function()
				{
					$link.parent().fadeOut();
				});
			}
		});
		
	});
</script>
<?php $this->load->view('partial/footer')?>
