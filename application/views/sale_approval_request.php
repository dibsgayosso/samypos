<?php defined('BASEPATH') OR exit('No direct script access allowed'); $this->load->view('partial/header'); $details=json_decode($request['detail_json'],TRUE); ?>
<div class="panel panel-piluku"><div class="panel-heading"><h3>Solicitud de venta #<?php echo (int)$request['id']; ?></h3></div><div class="panel-body">
<p><?php echo html_escape($this->session->flashdata('sale_approval_result')); ?></p>
<p>Estado: <strong><?php echo html_escape(array('pending'=>'Pendiente','authorized'=>'Autorizada','rejected'=>'Rechazada')[$request['status']]); ?></strong> · <?php echo html_escape($request['sale_time']); ?></p>
<?php $capturer=$this->Employee->get_info($request['employee_id']); ?><p>Capturó: <?php echo html_escape(trim($capturer->first_name.' '.$capturer->last_name)); ?></p>
<table class="table table-striped"><thead><tr><th>Producto</th><th>Cantidad</th><th>Importe</th></tr></thead><tbody>
<?php foreach ($details['items'] as $line) { ?><tr><td><?php echo html_escape($line['name']); ?></td><td><?php echo html_escape($line['quantity']); ?></td><td><?php echo to_currency($line['total']); ?></td></tr><?php } ?></tbody></table>
<p>Total: <?php echo to_currency($request['total']); ?></p><h4>Pagos a verificar</h4>
<?php foreach ($details['payments'] as $payment) { ?><p><?php echo html_escape($payment['method']); ?>: <?php echo to_currency($payment['amount']); ?></p><?php } ?>
<?php if ($request['status']==='rejected') { ?><p>Motivo: <?php echo html_escape($request['rejection_reason']); ?></p><?php } ?>
<?php if ($request['status']==='authorized') { ?><p>Autorizó empleado #<?php echo (int)$request['authorized_by']; ?> · <?php echo html_escape($request['authorized_at']); ?></p><a class="btn btn-success" href="<?php echo site_url('sales/receipt/'.$request['sale_id']); ?>">Imprimir recibo autorizado</a><?php } ?>
<?php if ($request['status']==='pending') { ?><p>Verifica la recepción del pago antes de autorizar. El inventario no está reservado. Rechazar aquí no devuelve un cobro bancario ya realizado.</p>
<?php if ($can_authorize) { echo form_open('sales/decide_approval'); ?>
<input type="hidden" name="approval_token" value="<?php echo html_escape($approval_token); ?>"><input type="hidden" name="request_id" value="<?php echo (int)$request['id']; ?>">
<button class="btn btn-success" name="decision" value="authorize">Autorizar venta</button><?php echo form_close(); echo form_open('sales/decide_approval'); ?>
<input type="hidden" name="approval_token" value="<?php echo html_escape($approval_token); ?>"><input type="hidden" name="request_id" value="<?php echo (int)$request['id']; ?>">
<label for="sale-rejection">Motivo del rechazo</label><input id="sale-rejection" class="form-control" name="reason" required maxlength="1000"><button class="btn btn-danger" name="decision" value="reject">Rechazar</button><?php echo form_close(); } ?>
<script>setTimeout(function(){if(!document.hidden && !document.querySelector('#sale-rejection:focus') && (!document.querySelector('#sale-rejection') || !document.querySelector('#sale-rejection').value))window.location.reload();},30000);</script><?php } ?>
<p><a href="<?php echo site_url('sales/approval_requests'); ?>">Ver solicitudes de ventas</a></p></div></div><?php $this->load->view('partial/footer'); ?>
