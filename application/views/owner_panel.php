<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if (!empty($owner_dashboard['error'])) {
    echo '<div class="alert alert-danger" role="alert">No se pudo cargar el panel del propietario. Revisa el registro de errores y que estén aplicadas las migraciones. El resto de Home sigue disponible.</div>';
    return;
}
$totals=$owner_dashboard['totals'];
$elapsed=function($stamp) { return $stamp===NULL ? 'Sin ventas registradas' : 'Hace '.number_format(max(0,(int)floor((time()-$stamp)/60)),0,'.',',').' min'; };
?>
<style>
.owner-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;margin-bottom:18px}
.owner-kpi{background:#f4f7fb;border:1px solid #e0e6ed;border-radius:8px;padding:16px}
.owner-kpi strong{display:block;font-size:22px;margin:8px 0;color:#243c64}
.owner-kpi small,.owner-branch-note{display:block;color:#666}
.owner-table th{white-space:nowrap}.owner-table td{vertical-align:top!important;min-width:140px}
.owner-table td:first-child{min-width:170px}.owner-details{padding:14px;border:1px solid #e0e6ed;border-radius:8px;margin-top:10px}
.owner-details summary{cursor:pointer;font-weight:600}.owner-details table{margin-top:12px}.owner-positive{color:#226b40}.owner-negative{color:#a12d28}
@media print{.owner-details{break-inside:avoid}.owner-kpis{grid-template-columns:repeat(3,1fr)}}
</style>
<div class="panel panel-piluku">
 <div class="panel-heading"><h3>Panel del propietario</h3><span id="owner-dashboard-refresh" role="status" aria-live="polite">Se actualiza cada 30 segundos</span></div>
 <div class="panel-body">
 <p>Hoy · <?php echo count($owner_dashboard['branches']); ?> sucursal(es) autorizadas · Última venta: <strong><?php echo $elapsed($owner_dashboard['last_sale']); ?></strong></p>
 <?php if (!$owner_dashboard['branches']) { ?><p>No hay sucursales disponibles con permiso para este panel.</p><?php } else { ?>
 <div class="owner-kpis">
  <div class="owner-kpi">Ventas de hoy<strong><?php echo to_currency($totals['sales_total']); ?></strong><small><?php echo (int)$totals['sales_count']; ?> operaciones únicas</small></div>
  <div class="owner-kpi">Entradas de mercancía<strong><?php echo to_currency($totals['receiving_total']); ?></strong><small><?php echo (int)$totals['receiving_count']; ?> recepciones aplicadas hoy</small></div>
  <div class="owner-kpi">Pendientes de autorizar<strong><?php echo $owner_dashboard['receiving_ready'] ? (int)$totals['pending_count'] : 'Por activar'; ?></strong><small><?php echo $owner_dashboard['receiving_ready'] ? to_currency($totals['pending_total']).' sin aplicar al inventario' : 'Aplica la migración de autorizaciones'; ?></small></div>
  <div class="owner-kpi">Créditos por cobrar<strong><?php echo to_currency($totals['credit_balance']); ?></strong><small><?php echo (int)$totals['credit_customers']; ?> clientes con deuda pendiente</small></div>
  <div class="owner-kpi">Gastos de hoy<strong><?php echo to_currency($totals['expenses_total']); ?></strong><small><?php echo (int)$totals['expenses_count']; ?> gastos · incluye impuestos</small></div>
 </div>
 <h4>Ventas de hoy por método de pago · Consolidado</h4>
 <div class="table-responsive"><table class="table table-striped">
  <thead><tr><th scope="col">Método configurado</th><th scope="col" class="text-right">Operaciones</th><th scope="col" class="text-right">Monto</th></tr></thead>
  <tbody><?php foreach ($owner_dashboard['payments'] as $payment) { ?>
   <tr><th scope="row"><?php echo html_escape($payment['label']); ?></th><td class="text-right"><?php echo (int)$payment['operations']; ?></td><td class="text-right"><?php echo to_currency($payment['total']); ?></td></tr>
  <?php } ?></tbody>
 </table></div>
 <p class="text-muted">Las ventas incluyen devoluciones y excluyen ventas eliminadas, suspendidas y abonos a créditos. Una venta combinada cuenta en cada método utilizado, y una sola vez en operaciones únicas.</p>
 <h4>Control por sucursal</h4>
 <div class="table-responsive"><table class="table table-striped owner-table">
  <thead><tr><th scope="col">Sucursal</th><th scope="col">Ventas de hoy</th><th scope="col">Entradas del día</th><th scope="col">Último corte de caja</th><th scope="col">Última venta</th><th scope="col">Créditos por cobrar</th><th scope="col">Gastos de hoy</th></tr></thead>
  <tbody><?php foreach ($owner_dashboard['branches'] as $branch) { ?>
  <tr>
   <th scope="row"><?php echo html_escape($branch['name']); ?><small class="owner-branch-note"><?php echo html_escape($branch['day'].' · '.$branch['timezone']); ?></small></th>
   <td><strong><?php echo to_currency($branch['sales_total']); ?></strong><small class="owner-branch-note"><?php echo (int)$branch['sales_count']; ?> operaciones</small></td>
   <td><strong><?php echo to_currency($branch['receiving_total']); ?></strong><small class="owner-branch-note"><?php echo (int)$branch['receiving_count']; ?> recepciones aplicadas</small><?php if ($owner_dashboard['receiving_ready']) { ?><span class="label label-<?php echo $branch['pending_count'] ? 'warning' : 'default'; ?>"><?php echo (int)$branch['pending_count']; ?> pendientes</span><?php } ?></td>
   <td><?php if (!$branch['can_view_difference']) { ?>Sin permiso para ver diferencias<?php } elseif (!$branch['last_close']) { ?>Sin corte cerrado<?php } else { $close=$branch['last_close']; $difference=(float)$close['difference']; ?>
    <strong class="<?php echo $difference<-.005 ? 'owner-negative' : 'owner-positive'; ?>"><?php echo to_currency($difference); ?></strong>
    <small class="owner-branch-note"><?php echo $difference<-.005 ? 'Faltante' : ($difference>.005 ? 'Sobrante' : 'Sin diferencia'); ?></small>
    <small class="owner-branch-note"><?php echo html_escape($close['shift_end']); ?></small>
    <small class="owner-branch-note"><?php echo html_escape($close['employee_name'].' · '.$close['register_name']); ?></small>
   <?php } ?></td>
   <td><?php echo $elapsed($branch['last_sale']); ?></td>
   <td><strong><?php echo to_currency($branch['credit_balance']); ?></strong><small class="owner-branch-note"><?php echo (int)$branch['credit_customers']; ?> clientes</small><?php if ($branch['credit_in_favor']>0) { ?><small class="owner-branch-note">Saldo a favor de clientes: <?php echo to_currency($branch['credit_in_favor']); ?></small><?php } ?></td>
   <td><strong><?php echo to_currency($branch['expenses_total']); ?></strong><small class="owner-branch-note"><?php echo (int)$branch['expenses_count']; ?> gastos</small></td>
  </tr>
  <?php } ?></tbody>
 </table></div>
 <?php foreach ($owner_dashboard['branches'] as $branch) { ?>
 <details class="owner-details" id="owner-branch-<?php echo (int)$branch['id']; ?>"><summary>Ver métodos de pago y detalle de gastos · <?php echo html_escape($branch['name']); ?></summary>
 <h5>Ventas por método de pago</h5><div class="table-responsive"><table class="table table-striped"><thead><tr><th scope="col">Método</th><th scope="col" class="text-right">Operaciones</th><th scope="col" class="text-right">Monto</th></tr></thead><tbody>
 <?php foreach ($branch['payments'] as $payment) { ?><tr><th scope="row"><?php echo html_escape($payment['label']); ?></th><td class="text-right"><?php echo (int)$payment['operations']; ?></td><td class="text-right"><?php echo to_currency($payment['total']); ?></td></tr><?php } ?>
 </tbody></table></div>
 <h5>Gastos por categoría · Incluye impuestos</h5>
 <?php if (!$branch['expenses']) { ?><p>Sin gastos registrados hoy.</p><?php } else { ?>
 <div class="table-responsive"><table class="table table-striped"><thead><tr><th scope="col">Categoría</th><th scope="col" class="text-right">Gastos</th><th scope="col" class="text-right">Monto</th></tr></thead><tbody>
 <?php foreach ($branch['expenses'] as $expense) { ?><tr><th scope="row"><?php echo html_escape($expense['category']); ?></th><td class="text-right"><?php echo (int)$expense['operations']; ?></td><td class="text-right"><?php echo to_currency($expense['amount']); ?></td></tr><?php } ?>
 </tbody></table></div><?php } ?>
 </details>
 <?php } ?>
 <p class="text-muted" style="margin-top:15px">Créditos: saldo actual de clientes asignados a cada sucursal; los saldos a favor se muestran por separado. Entradas: mercancía recibida y aplicada con fecha de hoy; las pendientes abarcan todas las fechas. El último corte corresponde al cierre más reciente de cada sucursal y puede ser de un día anterior.</p>
 <?php } ?>
 </div>
</div>
