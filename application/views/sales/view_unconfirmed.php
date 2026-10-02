<?php $this->load->view("partial/header"); ?>

<div class="spinner" id="grid-loader" style="display:none">
	<div class="rect1"></div>
	<div class="rect2"></div>
	<div class="rect3"></div>
</div>
									
<div class="container-fluid">
	<div class="row manage-table">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					<?php echo lang('sales_unconfiemd_transactions')?>
					<?php
					$total_rows = count($transactions);
					?>
					<span title="<?php echo $total_rows; ?> total suspended sales" class="badge bg-primary tip-left" id="manage_total_items"><?php echo $total_rows; ?></span>
				</h3>

			</div>
			<div class="panel-body nopadding table_holder table-responsive" id="table_holder">
					<table class="tablesorter table table-hover" id="sortable_table">
						<thead>
							<tr>
								<th><?php echo lang('common_date'); ?></th>
								<th><?php echo lang('common_amount'); ?></th>
								<th><?php echo lang('sales_register'); ?></th>
								<th><?php echo lang('common_transaction_id'); ?></th>
								<th><?php echo lang('common_load_sale_and_confirm')?></th>
							</tr>
						</thead>
						
						<tbody>
							<?php
							foreach($transactions as $transaction)
							{
							?>
							<tr>
								<td><?php echo date(get_date_format().' '.get_time_format(), strtotime($transaction['time_of_charge']))?></td>
								<td><?php echo to_currency($transaction['amount'])?></td>
								<td><?php echo $this->Register->get_register_name($transaction['register_id_of_charge'])?></td>
								<td><?php echo $transaction['transaction_charge_id']?></td>
								<td><a href="<?php echo site_url('sales/load_unconfirmed/'.$transaction['id'])?>" class="btn btn-primary"><?php echo lang('common_load_sale_and_confirm');?></a></td>
							</tr>
							<?php	
							}
							?>
							
						</tbody>
					</table>
			</div>		
			
		</div>
	</div>
</div>



</div>
<?php $this->load->view("partial/footer"); ?>

<script>

</script>
