<?php $this->load->view("partial/header"); ?>

<!-- Css Loader  -->
<div class="spinner hidden" id="ajax-loader" style="width:100vw;  height:100vh; padding:15%; top:0px; position: fixed;">
	<div class="rect1"></div>
	<div class="rect2"></div>
	<div class="rect3"></div>
</div>

<div class="row manage-table">

	<div class="col-md-12">
		<p id="zatca_message"></p>
	</div>

	<?php
		echo form_open_multipart('invoices/zatca_generate_ccsid_pcsid', array('id' => 'generate_ccsid_pcsid_form', 'class' => 'form-horizontal', 'style'=>'display:none', 'autocomplete' => 'off'));  ?>

		<input type="hidden" name="sale_id" value="<?php echo $sale_id; ?>">

		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					ZATCA Onboarding
				</div>

				<div class="panel-body">
					<div class="form-group">
						<p style="padding-left:20px;">
							Organization Information:
						</p>
					</div>
					<div class="form-group">
						<?php echo form_label('OTP' . ':', 'zatca_otp', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
							<?php echo form_input(array(
								'class' => 'form-control form-inps',
								'name' => 'zatca_otp',
								'id' => 'zatca_otp',
								'placeholder' => '123345',
								'value' => ""
							)); ?>
						</div>
					</div>
					<div class="form-group">
						<?php echo form_label('VAT No' . ':', 'zatca_otp', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
							<?php echo form_input(array(
								'class' => 'form-control form-inps',
								'disabled' => true,
								'value' => $location_zatca_config && $location_zatca_config['csr_organization_identifier']
							)); ?>
						</div>
					</div>
					<div class="form-group">
						<?php echo form_label('Organization Name' . ':', 'zatca_otp', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
							<?php echo form_input(array(
								'class' => 'form-control form-inps',
								'disabled' => true,
								'value' => $location_zatca_config && $location_zatca_config['csr_organization_unit_name']
							)); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="form-actions" style="padding-right: 50px;">
							<?php
							if($location_zatca_config){
								echo form_submit(array(
									'name' => 'submitf',
									'id' => 'generate_ccsid_pcsid_btn',
									'value' => "Generate CCSID & PCSID",
									'class' => 'submit_button btn btn-primary btn-lg pull-right'
								));
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>

	<?php
		echo form_close();
	?>

	<?php
		echo form_open_multipart('invoices/zatca_submit_cert', array('id' => 'zatca_submit_cert_form', 'class' => 'form-horizontal', 'style'=>'display:none', 'autocomplete' => 'off'));
	?>
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<div style="display:flex; justify-content:space-between;">
						<div>
							Certificate Submission
						</div>
						<div class="form-actions">
							<?php
							echo form_submit(array(
								'name' => 'submitf',
								'id' => 'close_view_cert_form',
								'value' => "Hide Cert",
								'class' => 'pull-right',
								'style' => "background-color: white; border-color: #8080808a; border-radius: 5px; margin-left:20px;"
							));
							?>
						</div>
					</div>					
					
				</div>
				<div class="panel-body">
					<div class="form-group">
						<?php echo form_label('Cert(cert.pem)' . ':', 'zatca_cert', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
							<?php 
								echo form_textarea(array(
								'name'=>'zatca_cert',
								'id'=>'zatca_cert',
								'class'=>'form-control text-area',
								'style'=>'height:300px;',
								'rows'=>'10',
								'cols'=>'30',
							'value'=>$cert));?>
						</div>
					</div>
					<div class="form-group">
						<?php echo form_label('Private Key(ec-secp256k1-priv-key.pem)' . ':', 'zatca_private_key', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
							<?php 
								echo form_textarea(array(
								'name'=>'zatca_private_key',
								'id'=>'zatca_private_key',
								'class'=>'form-control text-area',
								'rows'=>'5',
								'cols'=>'30',
							'value'=>$private_key));?>
						</div>
					</div>
					<div class="form-group">
						<div class="form-actions" style="padding-right: 50px;">
							<?php
							echo form_submit(array(
								'name' => 'submitf',
								'id' => 'cert_submit',
								'value' => "Submit Cert",
								'class' => 'submit_button btn btn-primary btn-lg pull-right'
							));
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
		echo form_close();
	?>

	<div class="col-md-12" id="view_ccsid_form" style="display:none;">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<div style="display:flex; justify-content:space-between;">
					<div>
						CCSID Response
					</div>
					<div class="form-actions" style="padding-right: 50px;">
						<?php
						echo form_submit(array(
							'name' => 'submitf',
							'id' => 'close_view_ccsid_form',
							'value' => "Hide CCSID",
							'class' => 'pull-right',
							'style' => "background-color: white; border-color: #8080808a; border-radius: 5px;"
						));
						?>
					</div>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<p style="padding:20px; word-break: break-all;" id="saudi_ccsid_response">
						<?php
						echo $ccsid;
						?>
					</p>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-md-12" id="view_pcsid_form" style="display:none;">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<div style="display:flex; justify-content:space-between;">
					<div>
						PCSID Response
					</div>
					<div class="form-actions">
						<?php
						echo form_submit(array(
							'name' => 'submitf',
							'id' => 'renew_pcsid_form',
							'value' => "Renew PCSID",
							'class' => 'pull-right',
							'style' => "background-color: white; border-color: #8080808a; border-radius: 5px; margin-left:20px;"
						));

						echo form_submit(array(
							'name' => 'submitf',
							'id' => 'close_view_pcsid_form',
							'value' => "Hide PCSID",
							'class' => 'pull-right',
							'style' => "background-color: white; border-color: #8080808a; border-radius: 5px; margin-left:20px;"
						));
						?>
						<input type="hidden" name="" id="pcsid_sale_id" value="">
					</div>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<p style="padding:20px; word-break: break-all;" id="saudi_pcsid_response">
						<?php echo $pcsid; ?>
					</p>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-12 invoice_date_range" style="display:none;">
		<p> <?php echo lang('zatca_invoice_date_range'); ?>: </p>
	</div>
	<?php 
		echo form_open_multipart('sales/receipt_zatca_date_range', array('id' => 'date_range_form', 'class' => 'form-horizontal invoice_date_range', 'style' => 'display:none', 'autocomplete' => 'off'));
	?>
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="col-12">			
				<?php
					$date_range = array(
						'TODAY' => lang('zatca_invoice_today'),
						'YESTERDAY' => lang('zatca_invoice_yesterday'),
						'LAST_7' => lang('zatca_invoice_last7'),
						'LAST_30' => lang('zatca_invoice_last30'),
					);
					echo form_dropdown('invoice_date_range', $date_range, $select_range, 'id="invoice_date_range" class="form-control"');
				?>
				</div>
			</div>
		</div>
	<?php
		echo form_close();
	?>

	<div class="col-md-12">
		<?php echo form_open_multipart('', array('id' => 'zatca_integration_form', 'style' => 'display:none', 'class' => 'form-horizontal', 'autocomplete' => 'off'));  ?>

		<div class="panel panel-piluku">
			<div class="panel-heading">
				<div style="display:flex; justify-content:space-between;">
					<div>
						ZATCA <?php echo lang('zatca_invoice_integration'); ?>
					</div>
					<div class="form-actions" style="display:none;">
						<?php
						echo form_submit(array(
							'name' => 'submitf',
							'id' => 'view_cert',
							'value' => "Show Certificate",
							'class' => 'pull-right',
							'style' => "background-color: white; border-color: #8080808a; border-radius: 5px; margin-left:20px;"
						));
						echo form_submit(array(
							'name' => 'submitf',
							'id' => 'view_ccsid',
							'value' => "Show CCSID",
							'class' => 'pull-right',
							'style' => "background-color: white; border-color: #8080808a; border-radius: 5px; margin-left:20px;"
						));
						echo form_submit(array(
							'name' => 'submitf',
							'id' => 'view_pcsid',
							'value' => "Show PCSID",
							'class' => 'pull-right',
							'style' => "background-color: white; border-color: #8080808a; border-radius: 5px; margin-left:20px;"
						));
						?>
					</div>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group" style="margin-top:20px;">
					<?php echo form_label("Invoice List" . ':', 'zatca_invoice_list', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 input-field">
						<select name="invoice_sale_list" class="form-control" id="zatca_select_invoice_id" multiple="multiple" style="height:150px;">
						<?php
						$invoice_sale_list = array();
						foreach ($sale_ids as $id) {
							$sale_zatca_status_id = 0;
							$sale_zatca_status = "";

							$sale_info = $this->Sale->get_info($id)->row_array();
							if($sale_info['was_estimate']){
								continue;
							}
							$zatca_invoice_data = $this->Invoice->get_zatca_invoice_by_sale_id($id);
							
							$invoice_type = "";
							if(
								$zatca_invoice_data &&
								$zatca_invoice_data['invoice_subtype'] &&
								$zatca_invoice_data['invoice_subtype'][0] == 0 &&
								$zatca_invoice_data['invoice_subtype'][1] == 1
							){// standard tax invoice
								$invoice_type = "BTB";
							}else if(
								$zatca_invoice_data &&
								$zatca_invoice_data['invoice_subtype'] &&
								$zatca_invoice_data['invoice_subtype'][0] == 0 &&
								$zatca_invoice_data['invoice_subtype'][1] == 2
							){// simplified tax invoice
								$invoice_type = "BTC";
							} else {
								$customer_id = $sale_info['customer_id'];
								$customer_info = $this->Customer->get_info($customer_id);
								if(strlen(trim($customer_info->company_name)) > 0){
									// "standard";
									$invoice_type = "BTB";
								}else{
									// "simplified";
									$invoice_subtype[0] = 0;
									$invoice_subtype[1] = 2;
									$invoice_type = "BTC";
								}
							}

							if(isset($sale_zatca_ids[$id])){
								if($sale_zatca_ids[$id] == 1){
									$sale_zatca_status_id = 1;
									$sale_zatca_status = "Reported";
								} else if($sale_zatca_ids[$id] == 2){
									$sale_zatca_status_id = 2;
									$sale_zatca_status = "Cleared";
								} else {
									$sale_zatca_status_id = 0;
									$sale_zatca_status = lang("common_sale");
								}
							} else {
								$sale_zatca_status_id = 0;
								$sale_zatca_status = lang("common_sale");
							}

							$invoice_sale_list[$id] = "#" . $id . " ( ".$sale_zatca_status." ) ";
							$invoice_sale_time = $sale_info['sale_time'];
							$invoice_sale_type = $invoice_type;
							$invoice_sale_status = $sale_zatca_status;
							$invoice_text = $invoice_sale_time." #".$id." ( ".$invoice_type. ($invoice_type ? " : " : ""). $invoice_sale_status. " )";
							echo '<option value="'.$id.'" data-time="'.$invoice_sale_time.'" data-status="'.$sale_zatca_status.'" data-status-id="'.$sale_zatca_status_id.'" data-type="'.$invoice_type.'">'.$invoice_text.'</option>';
						}
						?>
						</select>
					</div>
				</div>
				<div class="form-group" style="marign-top:20px;">
					<div class="form-actions" style="padding-right: 15px;">
					<?php
						echo form_submit(array(
							'name' => 'submitf',
							'id' => 'zatca_integration_sync',
							'value' => "Sync All Sales (past 7days)",
							'class' => 'submit_button btn btn-warning btn-lg pull-right',
							'style' => 'margin-right:20px'
						));
						?>
						<?php
						echo form_submit(array(
							'name' => 'submitf',
							'id' => 'zatca_integration_cancel',
							'value' => "Cancel",
							'class' => 'submit_button btn btn-danger btn-lg pull-right',
							'style' => 'margin-right:20px'
						));
						?>
						<?php
						echo form_submit(array(
							'name' => 'submitf',
							'id' => 'zatca_integration_submit',
							'value' => "Zatca Integration (one by one manual)",
							'class' => 'submit_button btn btn-primary btn-lg pull-right',
							'style' => 'margin-right:20px'
						));
						?>
					</div>
				</div>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>

	<style>
		.zatca_integration_mesage .detail_actions{
			display:none;
		}
		.zatca_integration_mesage .detail_actions .detail_action_show,
		.zatca_integration_mesage .detail_actions .detail_action_hide
		{
			cursor: pointer;
		}

		.zatca_integration_mesage .detail_content{
			display:none;
		}
	</style>

	<div class="col-md-12 zatca_integration_mesage">
		<p class="action_name" id="zatca_invoice_generate_xml_message"></p>
		<p class="detail_actions"><?php echo lang("zatca_invoice_view_details") ?>:<u class="detail_action_show"><?php echo lang("common_show") ?></u> <u class="detail_action_hide"><?php echo lang("zatca_invoice_hide") ?></u></p>
		<p class="detail_content" id="zatca_invoice_generate_xml_content" style="word-break:break-all;"></p>
	</div>
	<div class="col-md-12 zatca_integration_mesage">
		<p class="action_name" id="zatca_invoice_sign_message"></p>
		<p class="detail_actions"><?php echo lang("zatca_invoice_view_details") ?>:<u class="detail_action_show"><?php echo lang("common_show") ?></u> <u class="detail_action_hide"><?php echo lang("zatca_invoice_hide") ?></u></p>
		<p class="detail_content" id="zatca_invoice_sign_content" style="word-break:break-all;"></p>
	</div>
	<div class="col-md-12 zatca_integration_mesage">
		<p class="action_name" id="zatca_invoice_validate_message"></p>
		<p class="detail_actions"><?php echo lang("zatca_invoice_view_details") ?>:<u class="detail_action_show"><?php echo lang("common_show") ?></u> <u class="detail_action_hide"><?php echo lang("zatca_invoice_hide") ?></u></p>
		<p class="detail_content" id="zatca_invoice_validate_content" style="word-break:break-all;"></p>
	</div>
	<div class="col-md-12 zatca_integration_mesage">
		<p class="action_name" id="zatca_invoice_generate_request_message"></p>
		<p class="detail_actions"><?php echo lang("zatca_invoice_view_details") ?>:<u class="detail_action_show"><?php echo lang("common_show") ?></u> <u class="detail_action_hide"><?php echo lang("zatca_invoice_hide") ?></u></p>
		<p class="detail_content" id="zatca_invoice_generate_request_content" style="word-break:break-all;"></p>
	</div>
	<div class="col-md-12 zatca_integration_mesage">
		<p class="action_name" id="zatca_invoice_compliance_message"></p>
		<p class="detail_actions"><?php echo lang("zatca_invoice_view_details") ?>:<u class="detail_action_show"><?php echo lang("common_show") ?></u> <u class="detail_action_hide"><?php echo lang("zatca_invoice_hide") ?></u></p>
		<p class="detail_content" id="zatca_invoice_compliance_content" style="word-break:break-all;"></p>
	</div>
	<div class="col-md-12 zatca_integration_mesage">
		<p class="action_name" id="zatca_invoice_report_message"></p>
		<p class="detail_actions"><?php echo lang("zatca_invoice_view_details") ?>:<u class="detail_action_show"><?php echo lang("common_show") ?></u> <u class="detail_action_hide"><?php echo lang("zatca_invoice_hide") ?></u></p>
		<p class="detail_content" id="zatca_invoice_report_content" style="word-break:break-all;"></p>
	</div>
	<div class="col-md-12 zatca_integration_mesage">
		<p class="action_name" id="zatca_invoice_clearance_message"></p>
		<p class="detail_actions"><?php echo lang("zatca_invoice_view_details") ?>:<u class="detail_action_show"><?php echo lang("common_show") ?></u> <u class="detail_action_hide"><?php echo lang("zatca_invoice_hide") ?></u></p>
		<p class="detail_content" id="zatca_invoice_clearance_content" style="word-break:break-all;"></p>
	</div>
	<div class="col-md-12 zatca_integration_mesage">
		<p class="action_name" id="zatca_invoice_report2_message"></p>
		<p class="detail_actions"><?php echo lang("zatca_invoice_view_details") ?>:<u class="detail_action_show"><?php echo lang("common_show") ?></u> <u class="detail_action_hide"><?php echo lang("zatca_invoice_hide") ?></u></p>
		<p class="detail_content" id="zatca_invoice_report2_content" style="word-break:break-all;"></p>
	</div>

	<div class="col-md-12 zatca_integration_mesage">
		<p class="action_name" id="zatca_invoice_result_message"></p>
	</div>

	<div class="modal fade renew-pcsid-opt" id="renew_pcsid_otp_dlg" role="dialog" aria-labelledby="lookUpReceipt" aria-hidden="true">
		<div class="modal-dialog customer-recent-sales">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Renew PCSID</h4>
				</div>
				<div class="modal-body clearfix">
					<?php echo form_open("invoices/zatca_renew_pcsid", array('class' => 'renew-pcsid-opt-form', 'id'=>"renew-pcsid-opt-form", 'autocomplete' => 'off')); ?>
					<span class="text-danger text-center has-error renew-pcsid-opt-error" id="renew-pcsid-opt-error"></span>
					<input type="text" required class="form-control text-center" name="renew_opt" id="renew_opt" placeholder="Please input one time password generated from Fatoora portal.">
					<?php echo form_submit('submit_renew_pcsid_form', "Renew PCSID", 'id="renew_pcsid_form_submit" class="btn btn-block btn-primary"'); ?>
					<?php echo form_close(); ?>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</div>
<script type='text/javascript'>

	$(document).ready(function(){
		init_form();
	});

	function init_form(){
		<?php
		if(!$ccsid || !$pcsid){
		?>
			$("#generate_ccsid_pcsid_form").show();
		<?php
		}else if(!$cert){
		?>
			$("#zatca_submit_cert_form").show();
		<?php
		}else{
		?>
			$("#generate_invoice_xml_form").show();
			$("#check_compliance_send_zatca_form").show();
			$(".invoice_date_range").show();
			$("#zatca_integration_form").show();
		<?php
		}
		?>
	}

	// step-1: geneate ccsid and pcsid
	$("#generate_ccsid_pcsid_btn").on('click', function() {
		event.preventDefault();

		$('#ajax-loader').removeClass('hidden');
		$("#zatca_message").text('');

		$("#generate_ccsid_pcsid_form").ajaxSubmit({
			success: function(response) {
				$('#ajax-loader').addClass('hidden');
				$("#zatca_message").text(response.message);

				if (response.state == 1) {
					$("#zatca_submit_cert_form").show();
					$("#generate_ccsid_pcsid_form").hide();
					$("#saudi_ccsid_response").text(response.data.ccsid);
					$("#saudi_pcsid_response").text(response.data.pcsid);
				}

				// location.reload();
			},
			dataType: 'json',
			resetForm: false
		});
	});

	// step-2: input certificate
	$("#cert_submit").on('click', function(){
		event.preventDefault();

		$('#ajax-loader').removeClass('hidden');
		$("#zatca_message").text('');

		$("#zatca_submit_cert_form").ajaxSubmit({
			success: function(response) {
				$('#ajax-loader').addClass('hidden');
				$("#zatca_message").text(response.message);

				if (response.state == 1) {
					$("#zatca_submit_cert_form").hide();
					$("#generate_invoice_xml_form").show();
					$("#check_compliance_send_zatca_form").show();
					$("#saudi_ccsid_response").text(JSON.stringify(response.csr));
					$("#zatca_submit_cert_form").hide();
					$(".invoice_date_range").show();
				}
				location.reload();
			},
			dataType: 'json',
			resetForm: false
		});
	});

	// cancel zatca integration
	$('#zatca_integration_cancel').on("click", function(){
		event.preventDefault();
		bootbox.confirm("Would you like to cancel processing zatca integration?", function(result) {
			if (result) {
				clear_msg();

				var href = '<?php echo site_url("zatca/cancel");?>';
				$("#zatca_invoice_result_message").text("--- Proessing : Cancel ... ---");
				$.ajax({
					type: "POST",
					url: href,
					dataType: 'json',
					data: {
					},
					success: function(ret) {
						if(ret.state == 1)
							$("#zatca_invoice_result_message").text("--- Done ---");
						else
							$("#zatca_invoice_result_message").text("--- Failed ---");
					},
					error: function(jqXHR, error, errorThrown){
						$("#zatca_invoice_result_message").text("--- Failed ---");
					}
				});
			}
		});
	});

	let sync_progress_interval = 0;
	function get_manual_sync_progress_state(){
		var href = '<?php echo site_url("zatca/get_sync_progress");?>';
		$.ajax({
			type: "POST",
			url: href,
			dataType: 'json',
			data: {
			},
			success: function(ret) {
				if(ret.percent_complete < 100){
					$("#zatca_invoice_result_message").text("--- Proessing _ Sync All Location ID: " + ret.location_id + ", Percent: " + ret.percent_complete + "%, Sale ID: " + ret.sale_id + ", message: " + ret.message);
				}
			},
			error: function(jqXHR, error, errorThrown){
				$("#zatca_invoice_result_message").text("--- Failed ---");
			}
		});
	}

	// manual sync zatca integration
	$('#zatca_integration_sync').on("click", function(){
		event.preventDefault();
		bootbox.confirm("Do you want to sync all sales with zatca integration?", function(result) {
			if (result) {
				clear_msg();

				var href = '<?php echo site_url("zatca/manual_sync");?>';
				$("#zatca_invoice_result_message").text("--- Proessing : Sync All ... ");
				$.ajax({
					type: "POST",
					url: href,
					dataType: 'text',
					data: {
					},
					success: function(ret) {
						clearInterval(sync_progress_interval);
						$("#zatca_invoice_result_message").text(ret);
					},
					error: function(jqXHR, error, errorThrown){
						$("#zatca_invoice_result_message").text("--- Failed ---");
					}
				});
				sync_progress_interval = setInterval(() => {
					get_manual_sync_progress_state();
				}, 500);
			}
		});
	});

	// integrate with ZATCA
	$("#zatca_integration_submit").on("click", function(){
		event.preventDefault();
		clear_msg();
		zatca_integration();
	});

	$(".zatca_integration_mesage .detail_actions .detail_action_show").on('click', function(){
		$(this).parent().next().show();
	});
	$(".zatca_integration_mesage .detail_actions .detail_action_hide").on('click', function(){
		$(this).parent().next().hide();
	});

	function clear_msg(){
		$(".zatca_integration_mesage .action_name").text("");
		$(".zatca_integration_mesage .detail_actions").hide();
		$(".zatca_integration_mesage .detail_content").hide();
	}

	function zatca_integration(){
		let sale_id = $("#zatca_select_invoice_id").val();
		zatca_generate_invoice_xml(sale_id);
	}

	function zatca_generate_invoice_xml(sale_id){

		$("#zatca_select_invoice_id").prop("disabled", true);
		$("#zatca_integration_submit").prop("disabled", true);
		$("#zatca_invoice_generate_xml_message").text("<?php echo lang("zatca_invoice_generating_xml"); ?>");

		var href = '<?php echo site_url("zatca/invoice_generate_xml");?>';
		$.ajax({
			type: "POST",
			url: href,
			dataType: 'json',
			data: {
				'sale_id': sale_id
			},
			success: function(result) {
				$("#zatca_select_invoice_id").prop("disabled", false);
				$("#zatca_integration_submit").prop("disabled", false);

				$("#zatca_invoice_generate_xml_message").text(result.message);
				if (result.state == 1) {
					let invoice_time = $("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('time');
					let invoice_id = result.sale_id;
					let invoice_type = $("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('type');
					let invoice_status = $("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('status');
					let invoice_text = invoice_time + " #" + invoice_id + " ( "+ invoice_type + (invoice_type? " : " : "") + invoice_status + " )" + " _ process...";

					$("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").text(invoice_text);

					if(result.data && result.data.length > 0){
						$("#zatca_invoice_generate_xml_content").text(result.data);
						$("#zatca_invoice_generate_xml_content").hide();
						$("#zatca_invoice_generate_xml_message").next().show();
					}
					//automate next step
					zatca_invoice_sign_times = 0;
					zatca_invoice_sign(result.sale_id);
				}else{
					if(result.data && result.data.length > 0){
						$("#zatca_invoice_generate_xml_content").text(result.data);
						$("#zatca_invoice_generate_xml_content").hide();
						$("#zatca_invoice_generate_xml_message").next().show();
					}
					$("#zatca_invoice_result_message").text("--- Failed ---");
				}
			},
			error: function(jqXHR, error, errorThrown){
				$("#zatca_select_invoice_id").prop("disabled", false);
				$("#zatca_integration_submit").prop("disabled", false);

				$("#zatca_invoice_generate_xml_content").text(jqXHR.statusText);
				$("#zatca_invoice_generate_xml_content").hide();
				$("#zatca_invoice_generate_xml_message").next().show();
				$("#zatca_invoice_result_message").text("--- Failed ---");
			}
		});
	}

	var zatca_invoice_sign_times = 0;
	function zatca_invoice_sign(sale_id){
		if(zatca_invoice_sign_times >= 3) {
			$("#zatca_invoice_sign_content").text("Please try again after a few minutes.");
			$("#zatca_invoice_sign_content").hide();
			$("#zatca_invoice_sign_message").next().show();
			$("#zatca_invoice_result_message").text("--- Failed ---");
			return;
		};
		$("#zatca_select_invoice_id").prop("disabled", true);
		$("#zatca_integration_submit").prop("disabled", true);
		$("#zatca_invoice_sign_message").text("<?php echo lang("zatca_invoice_signing_xml"); ?>");

		var href = '<?php echo site_url("zatca/invoice_sign");?>';
		$.ajax({
			type: "POST",
			url: href,
			dataType: 'json',
			data: {
				'sale_id': sale_id
			},
			success: function(result) {
				$("#zatca_select_invoice_id").prop("disabled", false);
				$("#zatca_integration_submit").prop("disabled", false);

				$("#zatca_invoice_sign_message").text(result.message);
				if (result.state == 1) {
					if(result.data && result.data.length > 0){
						$("#zatca_invoice_sign_content").text(result.data);
						$("#zatca_invoice_sign_content").hide();
						$("#zatca_invoice_sign_message").next().show();
					}
					//automate next step
					zatca_invoice_validate(sale_id);
				}else if(result.state == 2){
					setTimeout((sale_id) => {
						zatca_invoice_sign_times ++;
						zatca_invoice_sign(sale_id);
					}, (3000), result.sale_id);
				}else if(result.state == 0){
					if(result.data && result.data.length > 0){
						$("#zatca_invoice_sign_content").text(result.data);
						$("#zatca_invoice_sign_content").hide();
						$("#zatca_invoice_sign_message").next().show();
					}
					$("#zatca_invoice_result_message").text("--- Failed ---");
				}
			},
			error: function(jqXHR, error, errorThrown){
				$("#zatca_select_invoice_id").prop("disabled", false);
				$("#zatca_integration_submit").prop("disabled", false);

				$("#zatca_invoice_sign_content").text(jqXHR.statusText);
				$("#zatca_invoice_sign_content").hide();
				$("#zatca_invoice_sign_message").next().show();
				$("#zatca_invoice_result_message").text("--- Failed ---");
			}
		});
	}

	function zatca_invoice_validate(sale_id){
		$("#zatca_select_invoice_id").prop("disabled", true);
		$("#zatca_integration_submit").prop("disabled", true);
		$("#zatca_invoice_validate_message").text("<?php echo lang("zatca_invoice_validating_xml"); ?>");

		var href = '<?php echo site_url("zatca/invoice_validate");?>';
		$.ajax({
			type: "POST",
			url: href,
			dataType: 'json',
			data: {
				'sale_id': sale_id
			},
			success: function(result) {
				console.log("zatca invoice xml validate");
				$("#zatca_select_invoice_id").prop("disabled", false);
				$("#zatca_integration_submit").prop("disabled", false);

				$("#zatca_invoice_validate_message").text(result.message);
				if (result.state == 1) {
					if(result.data && result.data.length > 0){
						$("#zatca_invoice_validate_content").text(result.data);
						$("#zatca_invoice_validate_content").hide();
						$("#zatca_invoice_validate_message").next().show();
					}
					//automate next step
					zatca_invoice_generate_request(sale_id);
				}else if(result.state == 2){
					setTimeout((sale_id) => {
						zatca_invoice_validate(sale_id);
					}, (3000), result.sale_id);
				}else if(result.state == 0){
					if(result.data && result.data.length > 0){
						$("#zatca_invoice_validate_content").text(result.data);
						$("#zatca_invoice_validate_content").hide();
						$("#zatca_invoice_validate_message").next().show();
					}
					$("#zatca_invoice_result_message").text("--- Failed ---");
				}
			},
			error: function(jqXHR, error, errorThrown){
				$("#zatca_select_invoice_id").prop("disabled", false);
				$("#zatca_integration_submit").prop("disabled", false);

				$("#zatca_invoice_validate_content").text(jqXHR.statusText);
				$("#zatca_invoice_validate_content").hide();
				$("#zatca_invoice_validate_message").next().show();
				$("#zatca_invoice_result_message").text("--- Failed ---");
			}
		});
	}

	function zatca_invoice_generate_request(sale_id){
		$("#zatca_select_invoice_id").prop("disabled", true);
		$("#zatca_integration_submit").prop("disabled", true);
		$("#zatca_invoice_generate_request_message").text("<?php echo lang("zatca_invoice_generating_request"); ?>");

		var href = '<?php echo site_url("zatca/invoice_generate_request");?>';
		$.ajax({
			type: "POST",
			url: href,
			dataType: 'json',
			data: {
				'sale_id': sale_id
			},
			success: function(result) {
				console.log("zatca invoice xml request");
				$("#zatca_select_invoice_id").prop("disabled", false);
				$("#zatca_integration_submit").prop("disabled", false);

				$("#zatca_invoice_generate_request_message").text(result.message);
				if (result.state == 1) {
					if(result.data && result.data.length > 0){
						$("#zatca_invoice_generate_request_content").text(result.data);
						$("#zatca_invoice_generate_request_content").hide();
						$("#zatca_invoice_generate_request_message").next().show();
					}
					//automate next step
					zatca_invoice_compliance(sale_id);
				}else if(result.state == 2){
					setTimeout((sale_id) => {
						zatca_invoice_generate_request(sale_id);
					}, (3000), result.sale_id);
				}else if(result.state == 0){
					if(result.data && result.data.length > 0){
						$("#zatca_invoice_generate_request_content").text(result.data);
						$("#zatca_invoice_generate_request_content").hide();
						$("#zatca_invoice_generate_request_message").next().show();
					}
					$("#zatca_invoice_result_message").text("--- Failed ---");
				}
			},
			error: function(jqXHR, error, errorThrown){
				$("#zatca_select_invoice_id").prop("disabled", false);
				$("#zatca_integration_submit").prop("disabled", false);

				$("#zatca_invoice_generate_request_content").text(jqXHR.statusText);
				$("#zatca_invoice_generate_request_content").hide();
				$("#zatca_invoice_generate_request_message").next().show();
				$("#zatca_invoice_result_message").text("--- Failed ---");
			}
		});
	}

	function zatca_invoice_compliance(sale_id){
		$("#zatca_select_invoice_id").prop("disabled", true);
		$("#zatca_integration_submit").prop("disabled", true);
		$("#zatca_invoice_compliance_message").text("<?php echo lang("zatca_invoice_check_compliance"); ?>");

		var href = '<?php echo site_url("zatca/invoice_compliance");?>';
		$.ajax({
			type: "POST",
			url: href,
			dataType: 'json',
			data: {
				'sale_id': sale_id
			},
			success: function(result) {
				console.log("zatca invoice xml request");
				$("#zatca_select_invoice_id").prop("disabled", false);
				$("#zatca_integration_submit").prop("disabled", false);

				$("#zatca_invoice_compliance_message").text(result.message);
				if (result.state == 1) {
					if(result.data && result.data.length > 0){
						$("#zatca_invoice_compliance_content").text(result.data);
						$("#zatca_invoice_compliance_content").hide();
						$("#zatca_invoice_compliance_message").next().show();
					}
					//automate next step
					if(
						result.zatca_invoice['invoice_subtype'] &&
						result.zatca_invoice['invoice_subtype'][0] == 0 &&
						result.zatca_invoice['invoice_subtype'][1] == 1
					){// standard tax invoice
						zatca_invoice_clearance(sale_id);
					}else if(
						result.zatca_invoice['invoice_subtype'] &&
						result.zatca_invoice['invoice_subtype'][0] == 0 &&
						result.zatca_invoice['invoice_subtype'][1] == 2
					){// simplified ax invoice
						zatca_invoice_report(sale_id);
					}
				}else if(result.state == 0){
					if(result.data && result.data.length > 0){
						$("#zatca_invoice_compliance_content").text(result.data);
						$("#zatca_invoice_compliance_content").hide();
						$("#zatca_invoice_compliance_message").next().show();
					}
					$("#zatca_invoice_result_message").text("--- Failed ---");
				}
			},
			error: function(jqXHR, error, errorThrown){
				$("#zatca_select_invoice_id").prop("disabled", false);
				$("#zatca_integration_submit").prop("disabled", false);

				$("#zatca_invoice_compliance_content").text(jqXHR.statusText);
				$("#zatca_invoice_compliance_content").hide();
				$("#zatca_invoice_compliance_message").next().show();
				$("#zatca_invoice_result_message").text("--- Failed ---");
			}
		});
	}

	function zatca_invoice_report(sale_id, clearance_status = 1){
		$("#zatca_select_invoice_id").prop("disabled", true);
		$("#zatca_integration_submit").prop("disabled", true);
		if(clearance_status == 1){
			$("#zatca_invoice_report_message").text("<?php echo lang("zatca_invoice_reporting"); ?>");
		} else {
			$("#zatca_invoice_report2_message").text("<?php echo lang("zatca_invoice_reporting"); ?>");
		}

		var href = '<?php echo site_url("zatca/invoice_report");?>';
		$.ajax({
			type: "POST",
			url: href,
			dataType: 'json',
			data: {
				'sale_id': sale_id,
				'clearance_status': clearance_status,
			},
			success: function(result) {
				console.log("zatca invoice xml request");
				$("#zatca_select_invoice_id").prop("disabled", false);
				$("#zatca_integration_submit").prop("disabled", false);

				if(clearance_status == 1){
					$("#zatca_invoice_report_message").text(result.message);
					if (result.state == 1) {
						if(result.data && result.data.length > 0){
							$("#zatca_invoice_report_content").text(result.data);
							$("#zatca_invoice_report_content").hide();
							$("#zatca_invoice_report_message").next().show();
						}
						$("#zatca_invoice_result_message").text("--- " + "<?php echo lang("common_success") ?>" + " ---");

						let invoice_time = $("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('time');
						let invoice_id = result.sale_id;
						$("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('type', 'BTC');
						let invoice_type = $("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('type');
						$("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('status', "Reported");
						let invoice_status = $("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('status');
						let invoice_text = invoice_time + " #" + invoice_id + " ( "+ invoice_type + (invoice_type? " : " : "") + invoice_status + " )";
						
						$("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").text(invoice_text);
						//done
					}else if(result.state == 0){
						if(result.data && result.data.length > 0){
							$("#zatca_invoice_report_content").text(result.data);
							$("#zatca_invoice_report_content").hide();
							$("#zatca_invoice_report_message").next().show();
						}
						$("#zatca_invoice_result_message").text("--- Failed ---");
					}
				}else if(clearance_status == 0){
					$("#zatca_invoice_report2_message").text(result.message);
					if (result.state == 1) {
						if(result.data && result.data.length > 0){
							$("#zatca_invoice_report2_content").text(result.data);
							$("#zatca_invoice_report2_content").hide();
							$("#zatca_invoice_report2_message").next().show();
						}
						$("#zatca_invoice_result_message").text("--- " + "<?php echo lang("common_success") ?>" + " (Clearance)---");

						let invoice_time = $("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('time');
						let invoice_id = result.sale_id;
						$("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('type', 'BTB');
						let invoice_type = $("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('type');
						$("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('status', "Reported");
						let invoice_status = $("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('status');
						let invoice_text = invoice_time + " #" + invoice_id + " ( "+ invoice_type + (invoice_type? " : " : "") + invoice_status + " )";

						$("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").text(invoice_text);

						//done
					}else if(result.state == 0){
						if(result.data && result.data.length > 0){
							$("#zatca_invoice_report2_content").text(result.data);
							$("#zatca_invoice_report2_content").hide();
							$("#zatca_invoice_report2_message").next().show();
						}
						$("#zatca_invoice_result_message").text("--- Failed (Clearance)---");
					}
				}
			},
			error: function(jqXHR, error, errorThrown){
				$("#zatca_select_invoice_id").prop("disabled", false);
				$("#zatca_integration_submit").prop("disabled", false);

				if(clearance_status == 0){
					$("#zatca_invoice_report2_content").text(jqXHR.statusText);
					$("#zatca_invoice_report2_content").hide();
					$("#zatca_invoice_report2_message").next().show();
					$("#zatca_invoice_result2_message").text("--- Failed ---");
				}else{
					$("#zatca_invoice_report_content").text(jqXHR.statusText);
					$("#zatca_invoice_report_content").hide();
					$("#zatca_invoice_report_message").next().show();
					$("#zatca_invoice_result_message").text("--- Failed ---");
				}
			}
		});
	}

	function zatca_invoice_clearance(sale_id){

		$("#zatca_select_invoice_id").prop("disabled", true);
		$("#zatca_integration_submit").prop("disabled", true);
		$("#zatca_invoice_clearance_message").text("<?php echo "Generating zatca invoice clearance..."; ?>");

		var href = '<?php echo site_url("zatca/invoice_clearance");?>';
		$.ajax({
			type: "POST",
			url: href,
			dataType: 'json',
			data: {
				'sale_id': sale_id
			},
			success: function(result) {
				$("#zatca_select_invoice_id").prop("disabled", false);
				$("#zatca_integration_submit").prop("disabled", false);

				$("#zatca_invoice_clearance_message").text(result.message);
				if (result.state == 1) {
					if(result.data && result.data.length > 0){
						$("#zatca_invoice_clearance_content").text(result.data);
						$("#zatca_invoice_clearance_content").hide();
						$("#zatca_invoice_clearance_message").next().show();
					}
					$("#zatca_invoice_result_message").text("--- " + "<?php echo lang("common_success") ?>" + " ---");

					let invoice_time = $("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('time');
					let invoice_id = result.sale_id;
					$("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('type', 'BTB');
					let invoice_type = $("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('type');
					$("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('status', "Cleared");
					let invoice_status = $("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").data('status');
					let invoice_text = invoice_time + " #" + invoice_id + " ( "+ invoice_type + " : " + invoice_status + " )";

					$("#zatca_select_invoice_id option[value='"+ result.sale_id +"']").text(invoice_text);

				}else{
					if(result.data && result.data.length > 0){
						$("#zatca_invoice_clearance_content").text(result.data);
						$("#zatca_invoice_clearance_content").hide();
						$("#zatca_invoice_clearance_message").next().show();
					}
					if(result.state == 303){
						zatca_invoice_report( sale_id, 0 );
					}else{
						$("#zatca_invoice_result_message").text("--- Failed ---");
					}
				}
			},
			error: function(jqXHR, error, errorThrown){
				$("#zatca_select_invoice_id").prop("disabled", false);
				$("#zatca_integration_submit").prop("disabled", false);

				$("#zatca_invoice_clearance_content").text(jqXHR.statusText);
				$("#zatca_invoice_clearance_content").hide();
				$("#zatca_invoice_clearance_message").next().show();
				$("#zatca_invoice_result_message").text("--- Failed ---");
			}
		});
	}

	$("#view_ccsid").on("click", function() {
		event.preventDefault();
		$("#view_ccsid_form").show();
	});

	$("#close_view_ccsid_form").on("click", function() {
		event.preventDefault();
		$("#view_ccsid_form").hide();
	});

	$("#view_pcsid").on("click", function() {
		event.preventDefault();
		$("#view_pcsid_form").show();
	});

	$("#close_view_pcsid_form").on("click", function() {
		event.preventDefault();
		$("#view_pcsid_form").hide();
	});

	$("#renew_pcsid_form").on("click", function() {
		event.preventDefault();
		$("#renew_opt").val("");
		$('#renew_pcsid_otp_dlg').modal('show');
	});

	$("#renew_pcsid_form_submit").on("click", function(){
		event.preventDefault();

		let renew_opt = $("#renew_opt").val();
		if(renew_opt == ""){
			$("#renew-pcsid-opt-error").text("OTP is required field.");
			return;
		}

		$('#ajax-loader').removeClass('hidden');
		$("#zatca_message").text('');
		// $("#saudi_pcsid_response").text('');

		$("#renew-pcsid-opt-form").ajaxSubmit({
			success: function(result) {
				$('#ajax-loader').addClass('hidden');
				$("#zatca_message").text(result.message);

				if (result.state == 1) {
					$("#saudi_pcsid_response").text(result.data);
					$("#view_pcsid_form").show();
					$('#renew_pcsid_otp_dlg').modal('hide');
				}else{
					$("#renew-pcsid-opt-error").text(result.message);
				}
			},
			dataType: 'json',
			resetForm: false
		});				
	});

	$("#view_cert").on("click", function() {
		event.preventDefault();
		$("#zatca_submit_cert_form").show();
	});

	$("#close_view_cert_form").on("click", function() {
		event.preventDefault();
		$("#zatca_submit_cert_form").hide();
	});
	
	$("#invoice_date_range").on("change", function(){
		var href = '<?php echo site_url("invoices/zatca_invoice/"); ?>' + $("#invoice_date_range").val();
		location.href = href;
	})
</script>
<?php $this->load->view('partial/footer'); ?>
