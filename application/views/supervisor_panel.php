<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="panel panel-piluku">
 <div class="panel-heading"><h4>Supervisión de la sucursal · Hoy</h4></div>
 <div class="panel-body">
 <?php if ($message=$this->session->flashdata('supervisor_result')) { ?><p role="status"><?php echo html_escape($message); ?></p><?php } ?>
 <div class="row">
  <div class="col-sm-4"><strong>Última venta</strong><p><?php echo $supervisor_activity['last_sale'] ? 'Hace '.max(0,(int)floor((time()-strtotime($supervisor_activity['last_sale']))/60)).' minutos' : 'Sin ventas registradas'; ?></p></div>
  <div class="col-sm-4"><strong>Gastos de hoy</strong><p><?php echo to_currency($supervisor_activity['expenses']['amount']+$supervisor_activity['expenses']['tax']); ?> · <?php echo (int)$supervisor_activity['expenses']['operations']; ?> registros (incluye impuestos)</p></div>
  <div class="col-sm-4"><strong>Recepciones pendientes</strong><p aria-live="polite"><?php echo count($supervisor_pending); ?></p></div>
 </div>
 <?php if (!$supervisor_ready) { ?><div class="alert alert-warning">Aplica la migración de autorización de recepciones para activar este módulo.</div><?php } elseif ($supervisor_pending) { ?>
 <div class="alert alert-warning" role="status">Hay mercancía recibida pendiente de tu autorización.</div>
 <div class="table-responsive"><table class="table table-striped"><thead><tr><th>Recibo</th><th>Fecha</th><th>Importe</th><th>Recibió</th><th>Acción</th></tr></thead><tbody>
 <?php foreach ($supervisor_pending as $receipt) { ?>
 <tr><td><a href="<?php echo site_url('receivings/receipt/'.$receipt['receiving_id']); ?>">RECV <?php echo (int)$receipt['receiving_id']; ?></a></td>
 <td><?php echo html_escape($receipt['receiving_time']); ?></td><td><?php echo to_currency($receipt['total']); ?></td>
 <td><?php $employee=$this->Employee->get_info($receipt['employee_id']); echo html_escape(trim($employee->first_name.' '.$employee->last_name)); ?></td>
 <td><?php echo form_open('home/authorize_receiving'); ?><input type="hidden" name="supervisor_token" value="<?php echo html_escape($supervisor_token); ?>"><input type="hidden" name="receiving_id" value="<?php echo (int)$receipt['receiving_id']; ?>"><input type="hidden" name="revision" value="<?php echo (int)$receipt['supervisor_revision']; ?>"><button class="btn btn-success" type="submit">Autorizar</button><?php echo form_close(); ?></td></tr>
 <?php } ?></tbody></table></div>
 <?php } else { ?><p>No hay recepciones pendientes de autorización.</p><?php } ?>
 <small>Se actualiza cada 30 segundos. Autorizar registra la revisión del recibo; el inventario se actualiza al recibir mercancía.</small>
 </div>
</div>
