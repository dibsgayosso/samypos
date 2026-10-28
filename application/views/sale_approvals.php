<?php defined('BASEPATH') OR exit('No direct script access allowed'); $this->load->view('partial/header'); ?>
<div class="panel panel-piluku"><div class="panel-heading"><h3>Aprobación de ventas</h3></div><div class="panel-body">
<p>Las ventas pendientes no afectan inventario, caja ni reportes. Autoriza desde otra cuenta con permiso y en la misma sucursal.</p>
<table class="table table-striped"><thead><tr><th>Solicitud</th><th>Fecha</th><th>Total</th><th>Estado</th><th>Revisar</th></tr></thead><tbody>
<?php foreach ($requests as $row) { ?><tr><td>#<?php echo (int)$row['id']; ?></td><td><?php echo html_escape($row['sale_time']); ?></td><td><?php echo to_currency($row['total']); ?></td><td><?php echo html_escape(array('pending'=>'Pendiente','authorized'=>'Autorizada','rejected'=>'Rechazada')[$row['status']]); ?></td><td><a href="<?php echo site_url('sales/approval_request/'.$row['id']); ?>">Abrir solicitud</a></td></tr><?php } ?>
</tbody></table><?php if (!$requests) { ?><p>No hay solicitudes para mostrar.</p><?php } ?>
<a href="<?php echo site_url('home'); ?>">Volver al inicio</a></div></div>
<script>setTimeout(function(){if(!document.hidden)window.location.reload();},30000);</script>
<?php $this->load->view('partial/footer'); ?>
