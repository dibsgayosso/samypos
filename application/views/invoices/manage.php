<?php $this->load->view("partial/header"); ?>


<div class="spinner" id="grid-loader" style="display:none">
	<div class="rect1"></div>
	<div class="rect2"></div>
	<div class="rect3"></div>
</div>

<script type="text/javascript">

	function reload_invoice_table()
	{
		clearSelections();
		$("#table_holder").load(<?php echo json_encode(site_url("$controller_name/reload_invoice_table/$invoice_type")); ?>);
	}

	$(document).ready(function()
	{
		$("#sortable").sortable({
			items : '.sort',
			containment: "#sortable",
			cursor: "move",
			handle: ".handle",
			revert: 100,
			update: function( event, ui ) {
				$input = ui.item.find("input[type=checkbox]");
				$input.trigger('change');
			}
		});

		$("#sortable").disableSelection();

		$("#config_columns a").on("click", function(e) {
			e.preventDefault();

			if($(this).attr("id") == "reset_to_default")
			{
				//Send a get request wihtout columns will clear column prefs
				$.get(<?php echo json_encode(site_url("$controller_name/save_column_prefs/$invoice_type")); ?>, function()
				{
					reload_invoice_table();
					var $checkboxs = $("#config_columns a").find("input[type=checkbox]");
					$checkboxs.prop("checked", false);

					<?php foreach($default_columns as $default_col) { ?>
							$("#config_columns a").find('#'+<?php echo json_encode($default_col);?>).prop("checked", true);
					<?php } ?>
				});
			}

			if(!$(e.target).hasClass("handle"))
			{
				var $checkbox = $(this).find("input[type=checkbox]");

				if($checkbox.length == 1)
				{
					$checkbox.prop("checked", !$checkbox.prop("checked")).trigger("change");
				}
			}

			return false;
		});


		$("#config_columns input[type=checkbox]").change(
			function(e) {
				var columns = $("#config_columns input:checkbox:checked").map(function(){
      		return $(this).val();
    		}).get();

				$.post(<?php echo json_encode(site_url("$controller_name/save_column_prefs/$invoice_type")); ?>, {columns:columns}, function(json)
				{
					reload_invoice_table();
				});

		});


		enable_sorting("<?php echo site_url("$controller_name/sorting/$invoice_type"); ?>");
		enable_select_all();
		enable_checkboxes();
		enable_row_selection();
		enable_search('<?php echo site_url("$controller_name/suggest/$invoice_type");?>',<?php echo json_encode(lang("common_confirm_search"));?>);

		<?php if(!$deleted) { ?>
			enable_delete(<?php echo json_encode(lang($controller_name."_confirm_delete"));?>,<?php echo json_encode(lang($controller_name."_none_selected"));?>);
		<?php } else { ?>
			enable_delete(<?php echo json_encode(lang($controller_name."_confirm_undelete"));?>,<?php echo json_encode(lang($controller_name."_none_selected"));?>);
		<?php } ?>

	});
</script>

<div class="status_box text-center" style="margin-bottom:30px;">
	<button class="btn btn-lg days_past_due_btn <?php echo $days_past_due == 'current'?'selected_days_past_due':''; ?>" data-past_due="current" style="background-color: white; padding: 0 !important;"> <span class="total_number" style="background-color: #69a3a1; padding:30px;  border-radius: 7px 0 0 7px; display: inherit;"><?php echo lang('invoices_current'); ?></span> <span class="total_number" style="background-color: white; color: #69a3a1; padding:30px; border-radius: 0 7px 7px 0; display: inherit;"><?php echo to_currency($this->Invoice->get_balance_past_due($invoice_type,'current'),2,false); ?></span></button>

	<?php
	foreach (range(30, 120, 30) as $days_past_due_option)
	{
	?>
		<button class="btn btn-lg days_past_due_btn <?php echo $days_past_due_option == $days_past_due ?'selected_days_past_due':''; ?>" data-past_due="<?php echo $days_past_due_option; ?>" style="background-color: white; padding: 0 !important;"> <span class="total_number" style="background-color: #69a3a1; padding:30px;  border-radius: 7px 0 0 7px; display: inherit;"><?php echo $days_past_due_option; ?></span> <span class="total_number" style="background-color: white; color: #69a3a1; padding:30px; border-radius: 0 7px 7px 0; display: inherit;"><?php echo to_currency($this->Invoice->get_balance_past_due($invoice_type,$days_past_due_option),2,false); ?></span></button>
	<?php } ?>
</div>

<div class="manage_buttons">
<!-- Css Loader  -->
<div class="spinner" id="ajax-loader" style="display:none">
	<div class="rect1"></div>
	<div class="rect2"></div>
	<div class="rect3"></div>
</div>
<div class="manage-row-options hidden">
	<div class="email_buttons invoices text-center">

	<?php if(!$deleted) { ?>
		<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
		<?php echo anchor("$controller_name/delete/$invoice_type",
			'<span class="ion-trash-a"></span> <span class="hidden-xs">'.lang('common_delete').'</span>'
			,array('id'=>'delete', 'class'=>'btn btn-red btn-lg disabled delete_inactive ','title'=>lang("common_delete"))); ?>
		<?php } ?>

		<a href="#" class="btn btn-lg btn-clear-selection btn-warning"><span class="ion-close-circled"></span> <span class="hidden-xs"><?php echo lang('common_clear_selection'); ?></span></a>

		<?php } else { ?>
			<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
			<?php echo anchor("$controller_name/undelete/$invoice_type",
					'<span class="ion-trash-a"></span> '.'<span class="hidden-xs">'.lang("common_undelete").'</span>',
					array('id'=>'delete','class'=>'btn btn-green btn-lg disabled delete_inactive','title'=>lang("common_undelete"))); ?>
			<?php } ?>

			<a href="#" class="btn btn-lg btn-clear-selection btn-warning"><span class="ion-close-circled"></span> <?php echo lang('common_clear_selection'); ?></a>
	<?php } ?>

	</div>
</div>

	<div class="row">
		<div class="col-md-9 col-sm-10 col-xs-10">
			<?php echo form_open("$controller_name/search/$invoice_type",array('id'=>'search_form', 'autocomplete'=> 'off')); ?>
				<div class="search no-left-border">
					<ul class="list-inline">
						<li>
							<input type="text" class="form-control" name ='search' id='search' value="<?php echo H($search); ?>" placeholder="<?php echo $deleted ? lang('common_search_deleted') : lang('common_search'); ?> <?php echo lang('module_'.$controller_name); ?>"/>
						</li>
						<li class="hidden-xs">
							<?php echo form_dropdown('status', $invoice_status,$status, 'class="form-control" id="status"'); ?>
						</li>
						<li>
							<button type="submit" class="btn btn-primary btn-lg"><span class="ion-ios-search-strong"></span><span class="hidden-xs hidden-sm"> <?php echo lang("common_search"); ?></span></button>
						</li>

						<li>
							<div class="clear-block <?php echo ($search=='' && $days_past_due == '') ? 'hidden' : ''  ?>">
								<a class="clear" href="<?php echo site_url("invoices/clear_state/$invoice_type"); ?>">
									<i class="ion ion-close-circled"></i>
								</a>
							</div>
						</li>

					</ul>
				</div>

				<input type="hidden" name="days_past_due" id="days_past_due" value="<?php echo $days_past_due; ?>">

			</form>
		</div>
		<div class="col-md-3 col-sm-2 col-xs-2">
			<div class="buttons-list">
				<div class="pull-right-btn">
					<!-- right buttons-->
					<?php if ($this->Employee->has_module_action_permission($controller_name, 'edit', $this->Employee->get_logged_in_employee_info()->person_id) && !$deleted) {?>
					<?php echo anchor("invoices/view/$invoice_type/-1",
						'<span class="ion-plus"> '.lang('invoices_new').'</span>',
						array('id' => 'new_invoice_btn', 'class'=>'btn btn-primary btn-lg hidden-sm hidden-xs', 'title'=>lang('invoices_new')));
					}
					?>


					<div class="piluku-dropdown btn-group">
						<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							<span class="hidden-xs ion-android-more-horizontal"> </span>
							<i class="visible-xs ion-android-more-vertical"></i>
						</button>
						<ul class="dropdown-menu" role="menu">

							<li>
								<?php echo anchor("$controller_name/manage_terms", '<span class="ion-ios-download-outline"> '.lang($controller_name."_manage_terms").'</span>',
									array('class'=>'','title'=> lang($controller_name."_manage_terms"))); ?>
							</li>
						</ul>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<div class="container-fluid">
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<div class="panel-heading">
				<h3 class="panel-title">
					<?php echo ($deleted ? lang('common_deleted').' ' : '').lang('module_'.$controller_name); ?>
					<span title="<?php echo $total_rows; ?> total invoices" class="badge bg-primary tip-left" id="manage_total_items"><?php echo $total_rows; ?></span>


					<form id="config_columns">
						<div class="piluku-dropdown btn-group table_buttons pull-right">
							<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<i class="ion-gear-a"></i>
							</button>

							<ul id="sortable" class="dropdown-menu dropdown-menu-left col-config-dropdown" role="menu">
									<li class="dropdown-header"><a id="reset_to_default" class="pull-right"><span class="ion-refresh"></span> <?php echo lang('common_reset'); ?></a> <?php echo lang('common_column_configuration'); ?></li>

									<?php foreach($all_columns as $col_key => $col_value) {
										$checked = '';

										if (isset($selected_columns[$col_key]))
										{
											$checked = 'checked ="checked" ';
										}
										?>
										<li class="sort"><a><input <?php echo $checked; ?> name="selected_columns[]" type="checkbox" class="columns" id="<?php echo $col_key; ?>" value="<?php echo $col_key; ?>"><label class="sortable_column_name" for="<?php echo $col_key; ?>"><span></span><?php echo H($col_value['label']); ?></label><span class="handle ion-drag"></span></a></li>
									<?php } ?>
								</ul>
						</div>
					</form>

					<span class="panel-options custom">
							<div class="pagination pagination-top hidden-print  text-center" id="pagination_top">
								<?php echo $pagination;?>
							</div>
					</span>
				</h3>
			</div>
				<div class="panel-body nopadding table_holder table-responsive" id="table_holder">
					<?php echo $manage_table; ?>
				</div>
		</div>
		<div class="text-center">
		<div class="pagination hidden-print alternate text-center" id="pagination_bottom" >
			<?php echo $pagination;?>
		</div>
		</div>
	</div>
</div>
<script type="text/javascript">

	$(".days_past_due_btn").click(function(){
		$(".days_past_due_btn").removeClass('selected_days_past_due');
		$(this).addClass('selected_days_past_due');
		$("#days_past_due").val($(this).data('past_due'));
		$("#search_form").submit();
	});

	$(document).ready(function()
	{
		<?php if ($this->session->flashdata('success')) { ?>
		show_feedback('success', <?php echo json_encode($this->session->flashdata('success')); ?>, <?php echo json_encode(lang('common_success')); ?>);
		<?php } ?>

		<?php if ($this->session->flashdata('error')) { ?>
		show_feedback('error', <?php echo json_encode($this->session->flashdata('error')); ?>, <?php echo json_encode(lang('common_error')); ?>);
		<?php } ?>

	});
</script>

<?php $this->load->view("partial/footer"); ?>

