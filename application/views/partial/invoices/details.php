<?php
if(isset($details) && !empty($details))
{
?>
<style type="text/css">
	.payment_heading {
		background: #f4f8fb;
	}
</style>
<div class="panel panel-piluku">
	<div class="panel-heading">
		<h3><strong><?php echo lang('invoices_invoice_details').' - '.lang('invoices_charges')?></strong></h3>
	</div>
	<div class="panel-body" style="padding:0px !important;">
		<div class="" id="invoice_details">
			<table class="table table-bordered">
				<tr class="payment_heading">
					<?php
					if ($can_edit)
					{
					?>
					<th><?php echo lang('common_delete');?></th>
					<?php } ?>
					<th><?php echo lang('common_order_id');?></th>
					<th><?php echo lang('common_total');?></th>
					<th><?php echo lang('common_description');?></th>
					<th><?php echo lang('common_account');?></th>
				</tr>
		
				<?php foreach($details as $detail) {
					
					//We don't want to show credits here
					if ($detail['total'] < 0)
					{
						continue;
					} 
					
					$invoice_details_id = $detail['invoice_details_id'];
					?>
				<tr>
					<?php
					if ($can_edit)
					{
					?>				
						<td>
							<a class="delete-invoice-detail" href="<?php echo site_url("invoices/delete_detail/$invoice_type/$invoice_details_id"); ?>"><?php echo lang('common_delete');?></a>
						</td>
					<?php } ?>
					
					<td><?php echo $detail[$type_prefix.'_id'];?></td>
					
					<?php
					if ($can_edit)
					{
					?>
					<td>
						<a href="#" id="total_<?php echo $invoice_details_id;?>" class="xeditable xeditable-total" data-validate-number="true" data-type="text" data-value="<?php echo H(to_currency_no_money($detail['total'])); ?>" data-pk="1" data-name="total" data-url="<?php echo site_url("invoices/edit_detail/$invoice_type/$invoice_details_id"); ?>" data-title="<?php echo H(lang('common_total')); ?>" data-invoice_details_id="<?php echo $invoice_details_id; ?>"><?php echo to_currency($detail['total']); ?></a>
					</td>
					<?php }
					else
					{
					?>
						<td><?php echo to_currency($detail['total']);?></td>						
					<?php
					} 
					?>
					
					
					<?php
					if ($can_edit)
					{
					?>
					<td>
						<a href="#" id="description_<?php echo $invoice_details_id;?>" class="xeditable xeditable-description" data-type="textarea" data-value="<?php echo H($detail['description']); ?>" data-pk="1" data-name="description" data-url="<?php echo site_url("invoices/edit_detail/$invoice_type/$invoice_details_id"); ?>" data-title="<?php echo H(lang('common_description')); ?>" data-invoice_details_id="<?php echo $invoice_details_id; ?>"><?php echo $detail['description']; ?></a>
					</td>
					<?php }
					else
					{
					?>
						<td><?php echo $detail['description'];?></td>
					<?php
					} 
					?>
					
					<?php
					if ($can_edit)
					{
					?>
					<td>
						<a href="#" id="account_<?php echo $invoice_details_id;?>" class="xeditable xeditable-account" data-type="text" data-value="<?php echo H($detail['account']); ?>" data-pk="1" data-name="account" data-url="<?php echo site_url("invoices/edit_detail/$invoice_type/$invoice_details_id"); ?>" data-title="<?php echo H(lang('common_account')); ?>" data-invoice_details_id="<?php echo $invoice_details_id; ?>"><?php echo $detail['account']; ?></a>
					</td>
					<?php }
					else
					{
					?>
					<td><?php echo $detail['account'];?></td>
					<?php
					} 
					?>
					
				</tr>
						<?php
						if ($detail[$type_prefix.'_id'])
						{
							$the_cart = NULL;
						
							if ($type_prefix == 'sale')
							{
								$the_cart = PHPPOSCartSale::get_instance_from_sale_id($detail[$type_prefix.'_id']);
							}
							else
							{
								$the_cart = PHPPOSCartRecv::get_instance_from_recv_id($detail[$type_prefix.'_id']);							
							}
						
							echo '<tr><td colspan="100">';
						
							echo '<table class="table table-bordered">';
							echo '<tr><th>'.lang('common_name').'</th><th>'.lang('common_quantity').'</th></tr>';
							foreach($the_cart->get_items() as $item)
							{
								echo '<tr><td>'.$item->name.'</td><td>'.to_quantity($item->quantity).'</td></tr>';
							}
						
							echo '</table></td></tr>'
							?>
						
						<?php } ?>
				<?php } ?>
			</table>
		</div>
	</div>
	
	
	
	
	<div class="panel-heading">
		<h3><strong><?php echo lang('invoices_invoice_details').' - '.lang('invoices_credits')?></strong></h3>
	</div>
	<div class="panel-body" style="padding:0px !important;">
		<div class="" id="invoice_details">
			<table class="table table-bordered">
				<tr class="payment_heading">
					<?php
					if ($can_edit)
					{
					?>
					<th><?php echo lang('common_delete');?></th>
					<?php } ?>
					<th><?php echo lang('common_order_id');?></th>
					<th><?php echo lang('common_total');?></th>
					<th><?php echo lang('common_description');?></th>
					<th><?php echo lang('common_account');?></th>
				</tr>
		
				<?php foreach($details as $detail) {
					
					//We don't want to show charges here
					if ($detail['total'] > 0)
					{
						continue;
					} 
					
					$invoice_details_id = $detail['invoice_details_id'];
					?>
				<tr>
					<?php
					if ($can_edit)
					{
					?>				
						<td>
							<a class="delete-invoice-detail" href="<?php echo site_url("invoices/delete_detail/$invoice_type/$invoice_details_id"); ?>"><?php echo lang('common_delete');?></a>
						</td>
					<?php } ?>
					
					<td><?php echo $detail[$type_prefix.'_id'];?></td>
					
					<?php
					if ($can_edit)
					{
					?>
					<td>
						<a href="#" id="total_<?php echo $invoice_details_id;?>" class="xeditable xeditable-total" data-validate-number="true" data-type="text" data-value="<?php echo H(to_currency_no_money($detail['total'])); ?>" data-pk="1" data-name="total" data-url="<?php echo site_url("invoices/edit_detail/$invoice_type/$invoice_details_id"); ?>" data-title="<?php echo H(lang('common_total')); ?>" data-invoice_details_id="<?php echo $invoice_details_id; ?>"><?php echo to_currency($detail['total']); ?></a>
					</td>
					<?php }
					else
					{
					?>
						<td><?php echo to_currency($detail['total']);?></td>						
					<?php
					} 
					?>
					
					
					<?php
					if ($can_edit)
					{
					?>
					<td>
						<a href="#" id="description_<?php echo $invoice_details_id;?>" class="xeditable xeditable-description" data-type="textarea" data-value="<?php echo H($detail['description']); ?>" data-pk="1" data-name="description" data-url="<?php echo site_url("invoices/edit_detail/$invoice_type/$invoice_details_id"); ?>" data-title="<?php echo H(lang('common_description')); ?>" data-invoice_details_id="<?php echo $invoice_details_id; ?>"><?php echo $detail['description']; ?></a>
					</td>
					<?php }
					else
					{
					?>
						<td><?php echo $detail['description'];?></td>
					<?php
					} 
					?>
					
					<?php
					if ($can_edit)
					{
					?>
					<td>
						<a href="#" id="account_<?php echo $invoice_details_id;?>" class="xeditable xeditable-account" data-type="text" data-value="<?php echo H($detail['account']); ?>" data-pk="1" data-name="account" data-url="<?php echo site_url("invoices/edit_detail/$invoice_type/$invoice_details_id"); ?>" data-title="<?php echo H(lang('common_account')); ?>" data-invoice_details_id="<?php echo $invoice_details_id; ?>"><?php echo $detail['account']; ?></a>
					</td>
					<?php }
					else
					{
					?>
					<td><?php echo $detail['account'];?></td>
					<?php
					} 
					?>
					
				</tr>
						<?php
						if ($detail[$type_prefix.'_id'])
						{
							$the_cart = NULL;
						
							if ($type_prefix == 'sale')
							{
								$the_cart = PHPPOSCartSale::get_instance_from_sale_id($detail[$type_prefix.'_id']);
							}
							else
							{
								$the_cart = PHPPOSCartRecv::get_instance_from_recv_id($detail[$type_prefix.'_id']);							
							}
						
							echo '<tr><td colspan="100">';
						
							echo '<table class="table table-bordered">';
							echo '<tr><th>'.lang('common_name').'</th><th>'.lang('common_quantity').'</th></tr>';
							foreach($the_cart->get_items() as $item)
							{
								echo '<tr><td>'.$item->name.'</td><td>'.to_quantity($item->quantity).'</td></tr>';
							}
						
							echo '</table></td></tr>'
							?>
						
						<?php } ?>
				<?php } ?>
			</table>
		</div>
	</div>
</div>

<?php } ?>