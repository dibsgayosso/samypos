<?php
 defined('BASEPATH') OR exit('No direct script access allowed');
 if (!empty($supervisor_dashboard['error'])) { ?><div class="alert alert-danger" role="alert">No se pudo cargar Supervisión. Revisa el registro de errores del servidor.</div><?php return; }
 $branches=$supervisor_dashboard['branches']; $pending=0; $expenses=0; $sales=0; $operations=0; $last=NULL;
 foreach ($branches as $branch) {
  $pending+=count($branch['pending']); $expenses+=$branch['activity']['expenses']['amount']+$branch['activity']['expenses']['tax'];
  $sales+=$branch['payments']['total']; $operations+=$branch['payments']['operations'];
  if ($branch['activity']['last_sale'] && ($last===NULL || $branch['activity']['last_sale_at']>$last)) $last=$branch['activity']['last_sale_at'];
 }
 $ago=function($stamp) { return $stamp ? 'Hace '.number_format(max(0,(int)floor((time()-$stamp)/60)),0,'.',',').' min' : 'Sin ventas registradas'; };
 $colors=array('#2563eb','#7c3aed','#0891b2','#db2777','#d97706','#15803d');
?>
<style>
.sv-dashboard{font-family:inherit;margin:18px 0 26px;color:#172554}.sv-dashboard *{box-sizing:border-box}
.sv-hero{background:linear-gradient(120deg,#172554,#4338ca);color:#fff;border-radius:18px;padding:24px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}.sv-hero h2{color:#fff!important;margin:4px 0 9px;font-size:26px;font-weight:700}.sv-hero p{margin:0;color:#e0e7ff}.sv-eyebrow{font-size:12px;letter-spacing:1.5px;text-transform:uppercase}.sv-live{background:#ffffff24;border:1px solid #ffffff40;padding:9px 13px;border-radius:30px;font-size:12px}.sv-live:before{content:'';display:inline-block;width:8px;height:8px;background:#86efac;border-radius:50%;margin-right:7px}
.sv-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin:16px 0}.sv-kpi{padding:18px;border-radius:16px;border:1px solid #dbeafe;background:#eff6ff;overflow-wrap:anywhere}.sv-kpi strong{display:block;font-size:27px;font-weight:700;margin:8px 0}.sv-kpi small{display:block}.sv-kpi .sv-symbol{float:right;font-size:22px}.sv-violet{background:#f5f3ff;border-color:#ddd6fe;color:#5b21b6}.sv-amber{background:#fffbeb;border-color:#fde68a;color:#92400e}.sv-rose{background:#fff1f2;border-color:#fecdd3;color:#9f1239}
.sv-branches{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.sv-branch{border:1px solid #e2e8f0;border-radius:18px;background:#fff;box-shadow:0 5px 16px #17255408;padding:20px}.sv-branch-head{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap}.sv-branch h3{font-size:20px;font-weight:700;margin:0;color:#172554}.sv-date{font-size:12px;color:#64748b;margin:6px 0 18px}.sv-status{border-radius:25px;font-size:12px;padding:7px 11px;font-weight:600;background:#dcfce7;color:#166534}.sv-status.pending{background:#fef3c7;color:#92400e}.sv-pair{display:flex;gap:16px;justify-content:space-between;border-bottom:1px solid #e2e8f0;padding-bottom:16px;margin-bottom:18px}.sv-pair strong{display:block;font-size:23px;margin:5px 0}.sv-operations{color:#2563eb!important;text-decoration:none!important;border-radius:10px;padding:6px 12px;background:#eff6ff}.sv-operations:hover,.sv-operations:focus{background:#dbeafe;outline:2px solid #2563eb}.sv-pair small{color:#64748b}.sv-chart{display:flex;align-items:center;gap:16px}.sv-chart svg{width:116px;height:116px;flex-shrink:0}.sv-legend{flex:1;min-width:0}.sv-method{margin-bottom:14px}.sv-method-top{display:flex;justify-content:space-between;gap:10px;font-size:13px;flex-wrap:wrap}.sv-method-label{font-weight:600;overflow-wrap:anywhere}.sv-bar{height:6px;border-radius:8px;background:#f1f5f9;margin-top:6px;overflow:hidden}.sv-bar span{display:block;height:100%;border-radius:8px}.sv-small{font-size:11px;color:#64748b;margin:9px 0 0}.sv-foot{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:14px;background:#f8fafc;border-radius:12px;margin-top:17px}.sv-foot strong{display:block;margin-top:4px}.sv-requests{margin-top:16px;border-top:1px solid #e2e8f0;padding-top:14px}.sv-requests summary{font-weight:600;cursor:pointer;color:#92400e}.sv-request{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f1f5f9}.sv-request small{display:block;color:#64748b}.sv-review{background:#4338ca;color:white!important;border-radius:9px;padding:9px 12px;text-decoration:none!important;font-weight:600;white-space:nowrap}.sv-review:hover{background:#312e81}.sv-note{font-size:12px;color:#64748b;margin-top:15px}.sv-dashboard .alert{border-radius:12px}
@media(max-width:900px){.sv-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}.sv-branches{grid-template-columns:1fr}}@media(max-width:400px){.sv-kpi strong{font-size:22px}.sv-chart{align-items:flex-start;gap:10px}.sv-chart svg{width:82px;height:82px}.sv-branch{padding:15px}}
</style>
<section class="sv-dashboard" aria-label="Panel del supervisor">
 <div class="sv-hero"><div><span class="sv-eyebrow">Control de tus sucursales</span><h2>Panel del supervisor</h2><p>Transferencias, gastos y mercancía por revisar, en un solo lugar.</p></div><span class="sv-live">Actualización cada 30 s</span></div>
 <div class="sv-kpis">
  <div class="sv-kpi"><span class="sv-symbol" aria-hidden="true">↗</span>Transferencias de hoy<strong><?php echo to_currency($sales); ?></strong><small><?php echo (int)$operations; ?> operaciones por método</small></div>
  <div class="sv-kpi sv-violet"><span class="sv-symbol" aria-hidden="true">◷</span>Última venta<strong style="font-size:22px"><?php echo $ago($last); ?></strong><small>Entre tus sucursales autorizadas</small></div>
  <div class="sv-kpi sv-amber"><span class="sv-symbol" aria-hidden="true">▣</span>Por autorizar<strong><?php echo $supervisor_dashboard['ready'] ? $pending : 'Por activar'; ?></strong><small><?php echo $pending ? 'Revisa la mercancía pendiente' : 'Sin solicitudes pendientes'; ?></small></div>
  <div class="sv-kpi sv-rose"><span class="sv-symbol" aria-hidden="true">↓</span>Gastos de hoy<strong><?php echo to_currency($expenses); ?></strong><small>Incluye impuestos</small></div>
 </div>
 <?php if (!$supervisor_dashboard['ready']) { ?><div class="alert alert-warning">Aplica la migración de autorizaciones para consultar mercancía pendiente.</div><?php } ?>
 <h3 style="font-size:18px;margin:22px 0 14px">Tus sucursales · <?php echo count($branches); ?></h3>
 <div class="sv-branches">
 <?php foreach ($branches as $branch) {
  $rows=$branch['payments']['rows']; $positive=0; foreach ($rows as $row) $positive+=max(0,(float)$row['total']); $offset=0;
 ?>
 <article class="sv-branch">
  <div class="sv-branch-head"><h3><?php echo html_escape($branch['name']); ?></h3><span class="sv-status <?php echo $branch['pending'] ? 'pending' : ''; ?>"><?php echo $branch['pending'] ? count($branch['pending']).' por autorizar' : ($supervisor_dashboard['ready'] ? 'Al día' : 'Por activar'); ?></span></div>
  <p class="sv-date">Hoy · <?php echo html_escape($branch['day']); ?> · <?php echo $ago($branch['activity']['last_sale'] ? $branch['activity']['last_sale_at'] : NULL); ?></p>
  <div class="sv-pair"><div>Transferencias de hoy<strong><?php echo to_currency($branch['payments']['total']); ?></strong></div><a class="sv-operations" href="<?php echo site_url('home/supervisor_transfers/'.(int)$branch['id']); ?>" aria-label="Ver ventas por transferencia de <?php echo html_escape($branch['name']); ?>">Operaciones<strong><?php echo (int)$branch['payments']['operations']; ?></strong><small>Ver resumen →</small></a></div>
  <h4 style="font-size:14px;font-weight:600">Transferencias por método configurado</h4>
  <div class="sv-chart">
   <svg viewBox="0 0 120 120" role="img" aria-label="Distribución de importes positivos de transferencias"><circle cx="60" cy="60" r="45" fill="none" stroke="#e2e8f0" stroke-width="15"/>
    <?php foreach ($rows as $i=>$row) { $ratio=$positive>0 ? max(0,(float)$row['total'])/$positive : 0; $length=$ratio*282.743;
     if ($ratio>0) { ?><circle cx="60" cy="60" r="45" fill="none" stroke="<?php echo $colors[$i%count($colors)]; ?>" stroke-width="15" stroke-dasharray="<?php echo number_format($length,3,'.','').' '.number_format(282.743-$length,3,'.',''); ?>" stroke-dashoffset="<?php echo number_format(-$offset,3,'.',''); ?>" transform="rotate(-90 60 60)"/><?php } $offset+=$length;
    } ?><text x="60" y="57" text-anchor="middle" fill="#172554" font-size="16" font-weight="bold"><?php echo (int)$branch['payments']['operations']; ?></text><text x="60" y="74" text-anchor="middle" fill="#64748b" font-size="9">transferencias</text>
   </svg>
   <div class="sv-legend"><?php foreach ($rows as $i=>$row) { $percent=$positive>0 ? max(0,(float)$row['total'])/$positive*100 : 0; ?>
    <div class="sv-method"><div class="sv-method-top"><span class="sv-method-label"><?php echo html_escape($row['label']); ?></span><span><?php echo to_currency($row['total']); ?> · <?php echo (int)$row['operations']; ?> op.</span></div><div class="sv-bar"><span style="width:<?php echo number_format($percent,2,'.',''); ?>%;background:<?php echo $colors[$i%count($colors)]; ?>"></span></div></div>
   <?php } ?></div>
  </div>
  <p class="sv-small">Solo transferencias. Los montos incluyen devoluciones. Una operación con varios métodos de transferencia cuenta en cada uno.</p>
  <div class="sv-foot"><div>◷ Última venta<strong><?php echo $ago($branch['activity']['last_sale'] ? $branch['activity']['last_sale_at'] : NULL); ?></strong></div><div>↓ Gastos de hoy<strong><?php echo to_currency($branch['activity']['expenses']['amount']+$branch['activity']['expenses']['tax']); ?></strong><small><?php echo (int)$branch['activity']['expenses']['operations']; ?> registros</small></div></div>
  <?php if ($branch['pending']) { ?><details class="sv-requests" data-branch="<?php echo (int)$branch['id']; ?>" open><summary>Mercancía pendiente · <?php echo count($branch['pending']); ?></summary>
   <?php foreach ($branch['pending'] as $receipt) { ?><div class="sv-request"><div><strong>Solicitud #<?php echo (int)$receipt['receiving_id']; ?> · <?php echo to_currency($receipt['total']); ?></strong><small><?php echo html_escape($receipt['receiving_time']); ?></small><small><?php $employee=$this->Employee->get_info($receipt['employee_id']); echo html_escape(trim($employee->first_name.' '.$employee->last_name)); ?></small></div><?php if ($branch['can_authorize']) { ?><a class="sv-review" href="<?php echo site_url('home/receiving_request/'.(int)$receipt['receiving_id']); ?>" aria-label="Revisar solicitud <?php echo (int)$receipt['receiving_id']; ?>">Revisar →</a><?php } else { ?><small>Sin permiso para autorizar</small><?php } ?></div><?php } ?>
  </details><?php } ?>
 </article>
 <?php } ?></div>
 <p class="sv-note">Solo ves las sucursales con permiso para este panel. Autorizar mercancía requiere su permiso independiente. Cada una usa su fecha local. Al revisar una solicitud de otra sucursal, el sistema cambia a esa sucursal. El inventario y los costos se actualizan al autorizar.</p>
</section>
