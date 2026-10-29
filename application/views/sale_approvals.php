<?php defined('BASEPATH') OR exit('No direct script access allowed'); $this->load->view('partial/header');
$states=array('pending'=>'Pendiente de aprobación','authorized'=>'Aprobada','rejected'=>'Rechazada');
$styles=array('pending'=>'warning','authorized'=>'success','rejected'=>'danger'); ?>
<div class="panel panel-piluku"><div class="panel-heading"><h3>Estado de solicitudes de ventas</h3></div><div class="panel-body">
<p>Consulta las últimas 100 solicitudes de esta sucursal. La lista se actualiza cada 30 segundos. Las ventas pendientes requieren autorización de otra cuenta con permiso.</p>
<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Solicitud</th><th>Fecha de envío</th><th>Total</th><th>Estado</th><th>Resultado</th><th>Acciones</th></tr></thead><tbody>
<?php foreach ($requests as $row) { $approved=$row['status']==='authorized' && (int)$row['sale_id']>0; ?>
<tr><td>#<?php echo (int)$row['id']; ?></td><td><?php echo html_escape($row['sale_time']); ?></td><td><?php echo to_currency($row['total']); ?></td>
<td><span class="label label-<?php echo isset($styles[$row['status']]) ? $styles[$row['status']] : 'default'; ?>"><?php echo html_escape(isset($states[$row['status']]) ? $states[$row['status']] : $row['status']); ?></span></td>
<td><?php if ($approved) { ?>Venta #<?php echo (int)$row['sale_id']; ?><br><?php echo html_escape($row['authorized_at']); ?><?php } elseif ($row['status']==='rejected') { echo html_escape($row['rejection_reason']); } else { ?>Esperando autorización. La impresión se habilita al aprobar.<?php } ?></td>
<td><a class="btn btn-default btn-sm" href="<?php echo site_url('sales/approval_request/'.(int)$row['id']); ?>">Ver detalle</a>
<?php if ($approved) { ?><a class="btn btn-success btn-sm" target="_blank" rel="noopener" href="<?php echo site_url('sales/approval_receipt/'.(int)$row['id']); ?>">Imprimir venta</a><?php } ?></td></tr>
<?php } ?>
</tbody></table></div><?php if (!$requests) { ?><div class="alert alert-info">No hay solicitudes de ventas para mostrar en esta sucursal.</div><?php } ?>
<p><a class="btn btn-default" href="<?php echo site_url('sales/approval_requests'); ?>">Actualizar estados</a> <a href="<?php echo site_url('home'); ?>">Volver al inicio</a></p></div></div>
<script>setTimeout(function(){if(!document.hidden)window.location.reload();},30000);</script>
<?php $this->load->view('partial/footer'); ?>
