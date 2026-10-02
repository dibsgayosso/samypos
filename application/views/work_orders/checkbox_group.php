<?php $this->load->view("partial/header"); ?>

<div class="row" id="form">
	<div class="spinner" id="grid-loader" style="display:none">
        <div class="rect1"></div>
        <div class="rect2"></div>
        <div class="rect3"></div>
	</div>
	<div class="col-md-12">
		<?php echo form_open('work_orders/save_checkbox/'.$group_info->id, array('id'=>'work_order_checkbox_form','class'=>'form-horizontal')); ?>
		    <div class="panel panel-piluku">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="ion-edit"></i> <?php if(!$group_info->id) { echo lang('work_orders_new_work_order_checkbox'); } else { echo lang('work_orders_update_work_order_checkbox'); } ?>
                        <small>(<?php echo lang('common_fields_required_message'); ?>)</small>
                    </h3>
                </div>

                <div class="panel-body">
                    <div class="form-group">
                        <div class="col-sm-8 col-md-8 col-lg-8">
                            <?php echo form_label(lang('work_orders_group_name').':', 'group_name', array('class'=>'required control-label')); ?>
                            <?php echo form_input(array(
                                'class'=>'form-control form-inps',
                                'name'=>'group_name',
                                'id'=>'group_name',
                                'value'=>$group_info->name)
                            );?>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <?php echo form_label(lang('work_orders_sort_order').':', 'group_sort_order', array('class'=>'control-label')); ?>
                            <?php echo form_input(array(
                                'class'=>'form-control form-inps',
                                'name'=>'group_sort_order',
                                'id'=>'group_sort_order',
                                'value'=>$group_info->sort_order)
                            );?>
                        </div>
                    </div>
                    
                    <div class="form-group no-padding-right">	
                        <?php echo form_label(lang('work_orders_pre').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
                        <div class="col-md-9 col-sm-9 col-lg-10">
                            <div class="table-responsive">
                                <table id="pre_checkboxes_table" class="table">
                                    <thead>
                                        <tr>
                                            <th><?php echo lang('common_name'); ?></th>
                                            <th><?php echo lang('common_description'); ?></th>
                                            <th style="width:80px;"><?php echo lang('common_delete'); ?></th>
											<th style="width:80px;"><?php echo lang('common_sort'); ?></th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        <?php $add_pre_index=0; foreach($pre_checkboxes as $pre_key => $checkbox) { $add_pre_index = $pre_key; ?>																		
                                            <tr>
                                                <td>
                                                    <input type="hidden" class="checkbox_items_id form-control" name="pre_checkbox_items[<?php echo $pre_key; ?>][id]" value="<?php echo $checkbox['id']; ?>" />
                                                    <input type="hidden" class="checkbox_items_sort form-control" name="pre_checkbox_items[<?php echo $pre_key; ?>][sort_order]" value="<?php echo $pre_key; ?>" />
                                                    <input type="text" class="checkbox_items_name form-control" name="pre_checkbox_items[<?php echo $pre_key; ?>][name]" value="<?php echo H($checkbox['name']); ?>" />
                                                </td>
                                                <td><input type="text" class="checkbox_items_description form-control" name="pre_checkbox_items[<?php echo $pre_key; ?>][description]" value="<?php echo H($checkbox['description'] !== NULL ? $checkbox['description'] : '' ); ?>" /></td>
                                                <td><a class="delete_checkbox_item" href="javascript:void(0);" data-checkbox_items-id='<?php echo $checkbox['id']; ?>'><?php echo lang('common_delete'); ?></a></td>
                                                <td style="cursor: ns-resize;"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            
                                <a href="javascript:void(0);" id="add_pre_checkbox_item" class="btn btn-sm btn-primary"><?php echo lang('common_add'); ?></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group no-padding-right">	
                        <?php echo form_label(lang('work_orders_post').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
                        <div class="col-md-9 col-sm-9 col-lg-10">
                            <div class="table-responsive">
                                <table id="post_checkboxes_table" class="table">
                                    <thead>
                                        <tr>
                                            <th><?php echo lang('common_name'); ?></th>
                                            <th><?php echo lang('common_description'); ?></th>
                                            <th style="width:80px;"><?php echo lang('common_delete'); ?></th>
                                            <th style="width:80px;"><?php echo lang('common_sort'); ?></th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        <?php $add_post_index=0; foreach($post_checkboxes as $post_key => $checkbox) { $add_post_index = $post_key; ?>																		
                                            <tr>
                                                <td>
                                                    <input type="hidden" class="checkbox_items_id form-control" name="post_checkbox_items[<?php echo $post_key; ?>][id]" value="<?php echo $checkbox['id']; ?>" />
                                                    <input type="hidden" class="checkbox_items_sort form-control" name="post_checkbox_items[<?php echo $post_key; ?>][sort_order]" value="<?php echo $post_key; ?>" />
                                                    <input type="text" class="checkbox_items_name form-control" name="post_checkbox_items[<?php echo $post_key; ?>][name]" value="<?php echo H($checkbox['name']); ?>" />
                                                </td>
                                                <td><input type="text" class="checkbox_items_description form-control" name="post_checkbox_items[<?php echo $post_key; ?>][description]" value="<?php echo H($checkbox['description'] !== NULL ? $checkbox['description'] : ''); ?>" /></td>
                                                <td><a class="delete_checkbox_item" href="javascript:void(0);" data-checkbox_items-id='<?php echo $checkbox['id']; ?>'><?php echo lang('common_delete'); ?></a></td>
                                                <td style="cursor: ns-resize;"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            
                                <a href="javascript:void(0);" id="add_post_checkbox_item" class="btn btn-sm btn-primary"><?php echo lang('common_add'); ?></a>
                            </div>
                        </div>
                    </div>

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
    var html = '';
    var add_pre_index = <?php echo ($add_pre_index+1); ?>;
    var add_post_index = <?php echo ($add_post_index+1); ?>;

    $("#pre_checkboxes_table tbody").sortable();
    $("#post_checkboxes_table tbody").sortable();

    $( "#pre_checkboxes_table" ).on( "sortupdate", function( event, ui ) {
        add_pre_index = set_element_order("#pre_checkboxes_table > tbody > tr");
    });

    $( "#post_checkboxes_table" ).on( "sortupdate", function( event, ui ) {
        add_post_index = set_element_order("#post_checkboxes_table > tbody > tr");
    });

	$(document).on("click", ".delete_checkbox_item", function(){

        if(!$(this).data('checkbox_items-id')){
            $(this).parent().parent().remove();
            return true;
        }

		$("#work_order_checkbox_form").append('<input type="hidden" name="checkbox_items_to_delete[]" value="'+$(this).data('checkbox_items-id')+'" />');
		$(this).parent().parent().remove();
	});
	
	$("#add_pre_checkbox_item").click(function(){
        html = '<tr>';
            html += '<td>';
                html += '<input type="hidden" class="checkbox_items_id form-control" name="pre_checkbox_items['+add_pre_index+'][id]" value=""/>';
                html += '<input type="hidden" class="checkbox_items_sort form-control" name="pre_checkbox_items['+add_pre_index+'][sort_order]" value="'+add_pre_index+'"/>';
                html += '<input type="text" class="checkbox_items_name form-control" name="pre_checkbox_items['+add_pre_index+'][name]" value="" />';
            html += '</td>';
            html += '<td><input type="text" class="checkbox_items_description form-control" name="pre_checkbox_items['+add_pre_index+'][description]" value=""/></td>';
            html += '<td><a class="delete_checkbox_item" href="javascript:void(0);"><?php echo lang('common_delete'); ?></a></td>';
            html += '<td style="cursor: ns-resize;"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>';
        html += '</tr>';
		$("#pre_checkboxes_table tbody").append(html);
		add_pre_index++;
	});

    $("#add_post_checkbox_item").click(function(){
        html = '<tr>';
            html += '<td>';
                html += '<input type="hidden" class="checkbox_items_id form-control" name="post_checkbox_items['+add_post_index+'][id]" value=""/>';
                html += '<input type="hidden" class="checkbox_items_sort form-control" name="post_checkbox_items['+add_post_index+'][sort_order]" value="'+add_post_index+'"/>';
                html += '<input type="text" class="checkbox_items_name form-control" name="post_checkbox_items['+add_post_index+'][name]" value="" />';
            html += '</td>';
            html += '<td><input type="text" class="checkbox_items_description form-control" name="post_checkbox_items['+add_post_index+'][description]" value=""/></td>';
            html += '<td><a class="delete_checkbox_item" href="javascript:void(0);"><?php echo lang('common_delete'); ?></a></td>';
            html += '<td style="cursor: ns-resize;"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>';
        html += '</tr>';
		$("#post_checkboxes_table tbody").append(html);
		add_post_index++;
	});

    function set_element_order(table_rows){
        var total = [];
        $(table_rows).each(function(index, tr) { 
            $(this).find(".checkbox_items_sort").val(index+1);
            total[index] = index;
        });
        return total.length+1;
    }
	
</script>
<?php $this->load->view('partial/footer')?>
