<?php defined('BASEPATH') OR exit('No direct script access allowed'); $this->load->view('partial/header'); ?>
<div class="panel panel-piluku"><div class="panel-heading"><h3>Solicitud de mercancía #<?php echo (int)$request['id']; ?></h3></div><div class="panel-body">
<p><strong>Estado:</strong> <?php echo html_escape($request['status']); ?> · <?php echo html_escape($request['receiving_time']); ?></p>
<p>Revisa físicamente la mercancía antes de autorizar. Pendiente significa que todavía no afecta inventario.</p>
<table class="table table-striped"><thead><tr><th>Producto</th><th>Cantidad / equivalencia</th><th>Costo unitario</th><th>Importe</th><th>Serie / caducidad</th></tr></thead><tbody>
<?php foreach (json_decode($request['detail_json'],TRUE) ?: array() as $line) { ?><tr><td><?php echo html_escape($line['name']); ?></td><td><?php echo html_escape($line['quantity'].(!empty($line['unit_quantity']) ? ' × '.$line['unit_quantity'].' piezas' : ''));  ?></td><td><?php echo to_currency($line['unit_price']); ?></td><td><?php echo to_currency($line['total']); ?></td><td><?php echo html_escape($line['serialnumber'].' / '.$line['expire_date']); ?></td></tr><?php } ?>
</tbody></table><p>Total: <?php echo to_currency($request['total']); ?></p>
<?php if ($request['status']==='pending') { echo form_open('receivings/authorize_pending'); ?>
<input type="hidden" name="supervisor_token" value="<?php echo html_escape($supervisor_token); ?>"><input type="hidden" name="receiving_id" value="<?php echo (int)$request['id']; ?>"><input type="hidden" name="revision" value="<?php echo (int)$request['supervisor_revision']; ?>"><button type="submit" class="btn btn-success">Autorizar y aplicar mercancía</button><?php echo form_close(); echo form_open('receivings/authorize_pending'); ?>
<input type="hidden" name="decision" value="reject"><input type="hidden" name="supervisor_token" value="<?php echo html_escape($supervisor_token); ?>"><input type="hidden" name="receiving_id" value="<?php echo (int)$request['id']; ?>"><label for="rejection-reason">Motivo del rechazo</label><input class="form-control" id="rejection-reason" name="reason" required maxlength="1000"><button type="submit" class="btn btn-danger">Rechazar solicitud</button><?php echo form_close(); } ?>
<p><a href="<?php echo site_url('home'); ?>">Volver al inicio</a></p>
</div></div><?php $this->load->view('partial/footer'); ?>
