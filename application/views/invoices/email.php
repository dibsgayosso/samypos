
<h1><?php echo lang("invoices_invoice"); ?></h1>

<p><?php echo lang('invoices_invoice_date');?>: <?php echo date(get_date_format(), strtotime($invoice_info->invoice_date));?></p>
<p><?php echo lang('invoices_po_'.$invoice_type);?>: <?php echo $invoice_info->{"$invoice_type".'_po'};?></p>
<p><?php echo lang("invoices_$invoice_type");?>: <?php echo $invoice_info->person;?></p>
<p><?php echo lang("invoices_terms");?>: <?php echo $invoice_info->term_name;?></p>
<p><?php echo $invoice_info->term_description;?>
<p><?php echo lang('invoices_due_date');?>: <?php echo date(get_date_format(), strtotime($invoice_info->due_date));?></p>
	
	<?php $this->load->view('partial/invoices/details', array('details' => $details,'can_edit' => FALSE,'type_prefix' => $type_prefix)); ?>
	
	
	<?php $this->load->view('partial/invoices/payments', array('payments' => $payments));?>
	
	
	<div>
		<h4>Total: <?php echo to_currency($invoice_info->total)?></h4>
		<h4>Balance: <?php echo to_currency($invoice_info->balance)?></h4>
	</div>
	
	<br />
	<br />
	<?php
	if ($invoice_type == 'customer' && $this->Location->get_info_for_key('credit_card_processor') == 'coreclear2')
	{
		echo anchor($this->Invoice->get_coreclear_payment_link($invoice_id), lang('common_pay'), array('style' => 'background: #3498db;background-image: linear-gradient(to bottom, #3498db, #2980b9);-webkit-border-radius: 28;-moz-border-radius: 28;border-radius: 28px;font-family: Arial;color: #ffffff;padding: 10px;text-decoration: none;font-weight: normal;'));

	}
	?>