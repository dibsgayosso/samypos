<style type="text/css">
	.selectize-dropdown-content {
		background: #FFF;
	}
</style>
<div class="modal-dialog">
	<div class="modal-content customer-recent-sales">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true" class="ti-close"></span></button>
			<h4 class="modal-title"> <?php echo $title; ?></h4>
		</div>
		<div class="modal-body ">
			<div class="row" id="form">
				
				<div class="spinner" id="grid-loader" style="display:none">
				  <div class="rect1"></div>
				  <div class="rect2"></div>
				  <div class="rect3"></div>
				</div>
				<div class="col-md-12">
					<?php $item_id = $item_info->item_id ? $item_info->item_id : '';?>
					<?php echo form_open($controller_name.'/quick_save/'.$item_id,array('id'=>$controller_name.'_form','class'=>'form-horizontal')); ?>
					<div class="form-group">
						<?php echo form_label(lang('common_item_name').':', 'name',array('class'=>' col-sm-3 col-md-3 col-lg-3 control-label required ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'class'	=>	'form-control',
							'name'	=>	'name',
							'id'	=>	'name',
							'value'	=>	$item_info->name)
						);?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_category').':', 'category_id',array('class'=>' col-sm-3 col-md-3 col-lg-3 control-label required ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<?php echo form_dropdown('category_id', $categories,$item_info->category_id, 'class="form-control" id="category_id"');?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_supplier').':', 'supplier_id',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label wide ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<?php echo form_dropdown('supplier_id', $suppliers, $selected_supplier,'class="form-control" id="supplier_id"');?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_item_number_expanded').':', 'item_number',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<?php echo form_input(array(
								'name'	=>	'item_number',
								'id'	=>	'item_number',
								'class'	=>	'form-control form-inps',
								'value'	=>	$item_info->item_number)
							);?>
						</div>
					</div>


					<?php if ($this->Employee->has_module_action_permission('items','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
						<div class="form-group">
							<?php echo form_label(lang('common_cost_price').' ('.lang('common_without_tax').')'.':', 'cost_price',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label required wide')); ?>
							<div class="col-sm-9 col-md-9 col-lg-9">
								<div class="input-group">
									<span class="input-group-addon bg"><span class=""><?php echo $this->config->item("currency_symbol") ? $this->config->item("currency_symbol") : '$';?></span></span>
									<?php echo form_input(array(
										'name'	=>	'cost_price',
										'size'	=>	'8',
										'id'	=>	'cost_price',
										'class'	=>	'form-control form-inps',
										'value'	=>	$item_info->cost_price ? to_currency_no_money($item_info->cost_price,10) : '')
									);?>
								</div>
							</div>
						</div>
					<?php } else {
						echo form_hidden('cost_price', $item_info->cost_price);
					} ?>
								
					<?php if ($this->Employee->has_module_action_permission('items','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id) or $item_info->name=="") { ?>
					<?php if ($this->config->item('enable_markup_calculator')) { ?>
						<div class="form-group">
							<?php echo form_label(lang('common_markup').':', 'margin',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
						    <div class="col-sm-9 col-md-9 col-lg-10">
								<div class="input-group">
								    <?php echo form_input(array(
										'type'			=>	'number',
										'min'			=>	'0',
										'max'			=> 	'',
							        	'name'			=>	'markup',
							        	'size'			=>	'8',
										'class'			=>	'form-control',
							        	'id'			=>	'markup',
							        	'value'			=>	'',
										'placeholder' 	=> 	lang('common_enter_markup_percent'),
										)
								    );?>
									<span class="input-group-addon bg"><span class="">%</span></span>
								</div>
						    </div>
						</div>
					<?php } }?>

					<div class="form-group">
						<?php echo form_label(lang('common_unit_price').':', 'unit_price',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label required wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<div class="input-group">
								<span class="input-group-addon bg"><span class=""><?php echo $this->config->item("currency_symbol") ? $this->config->item("currency_symbol") : '$';?></span></span>
								<?php echo form_input(array(
									'name'=>'unit_price',
									'size'=>'8',
									'id'=>'unit_price',
											'class'=>'form-control form-inps',
									'value'=>$item_info->unit_price ? to_currency_no_money($item_info->unit_price, 10) : '')
								);?>
							</div>
						</div>
					</div>


					<div class="form-group is-service-toggle <?php if ($item_info->is_service){ echo 'hidden';} ?>">
						<?php echo form_label(lang('items_reorder_level').':', 'reorder_level',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-9">
							<?php echo form_input(array(
								'name'=>'reorder_level',
								'id'=>'reorder_level',
								'class'=>'form-control form-inps',
								'value'=>$item_info->reorder_level || $item_info->item_id ? to_quantity($item_info->reorder_level, FALSE) : $this->config->item('default_reorder_level_when_creating_items'))
							);?>
						</div>
					</div>	

					<hr>
					<input type="hidden" name="quick_form" value="1">
					<div class="modal-footer" style="padding: 0px;">
						<div class="form-acions">
							<a href="<?php echo site_url($controller_name.'/view/'.$item_id);?>" class="pull-left submit_button btn btn-primary">
								<?php echo lang('common_edit'); ?>
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
		</div>
	</div>
</div>

<?php 
echo form_close();
?>

<script type='text/javascript'>
					
//validation and submit handling
$(document).ready(function()
{
	$('#supplier_id').selectize();
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

    setTimeout(function(){$(":input:visible:first","#items_form").focus();},100);

	$('#items_form').validate({
		submitHandler:function(form)
		{
			$.post('<?php echo site_url("items/check_duplicate");?>', {term: $('#name').val()},function(data) {
			<?php if(!$item_info->item_id) {  ?>
				if(data.duplicate)
				{
					bootbox.confirm(<?php echo json_encode(lang('common_items_duplicate_exists'));?>, function(result)
					{
						if(result)
						{
							doItemSubmit(form);
						}
					});
				}
				else
				{
					doItemSubmit(form);
				}
				<?php } else { ?>
					doItemSubmit(form);
				<?php } ?>
				} , "json");

		},
		errorClass: "text-danger",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
		},

		rules: 
		{
			<?php if(!$item_info->item_id) {  ?>
			item_number:
			{
				remote: 
		    { 
					url: "<?php echo site_url('items/item_number_exists');?>", 
					type: "post"
		    } 
			},
		<?php } ?>
			name:"required",
			category_id:"required",
			cost_price:"required",
			unit_price:"required",

		},
		messages: 
		{
			<?php if(!$item_info->item_id) {  ?>
			item_number:
			{
				remote: function()
				{
					var link = <?php echo json_encode('<a id="item_number_validation_link" target="_blank" href="#">'.lang('common_item_info').'</a>')?>;
					
					$.post(<?php echo json_encode(site_url('items/find_item_info')); ?>,{scan_item_number: $("#item_number").val()}, function(response)
					{
						$("#item_number_validation_link").attr('href',response.link);
					},'json');
					return <?php echo json_encode(lang('items_item_number_exists')); ?>+' '+link;
				}
				   
			},
			<?php } ?>
     		name:<?php echo json_encode(lang('common_item_name_required')); ?>,
			category_id:<?php echo json_encode(lang('common_category_required')); ?>,
			cost_price:
			{
				required:<?php echo json_encode(lang('items_cost_price_required')); ?>,
				number:<?php echo json_encode(lang('common_cost_price_number')); ?>
			},
			unit_price:
			{
				required:<?php echo json_encode(lang('items_unit_price_required')); ?>,
				number:<?php echo json_encode(lang('common_unit_price_number')); ?>
			},
		}
	});

});

var submitting = false;

function doItemSubmit(form)
{
$('#grid-loader').show();
	if (submitting) return;
	submitting = true;

	$(form).ajaxSubmit({
	success:function(response)
		{
$('#grid-loader').hide();
			submitting = false;
			$('#myModal').modal('hide');
			if (response.success)
			{

				window.location.href = '<?php echo site_url('items'); ?>';
				show_feedback('success', response.message, <?php echo json_encode(lang('common_success')); ?>+' #' + response.person_id);
			}
			else
			{
				show_feedback('error', response.message, <?php echo json_encode(lang('common_error')); ?>);
			}
			
		},
		dataType:'json'
	});
}
</script>
