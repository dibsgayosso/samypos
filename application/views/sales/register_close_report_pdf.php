<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body{font-family:dejavusans,sans-serif;font-size:10pt;color:#222}
.header{border-bottom:2px solid #222;padding-bottom:12px;margin-bottom:18px}
.company{font-size:22pt;font-weight:bold}.branch{font-size:12pt;margin-top:4px}.title{font-size:15pt;font-weight:bold;margin-top:8px}
.logo{width:120px;max-height:70px;text-align:right}
.summary{width:100%;margin:14px 0;border-collapse:separate;border-spacing:8px}
.summary td{border:1px solid #ddd;padding:12px;text-align:center}.summary .value{font-size:16pt;font-weight:bold}
table.data{width:100%;border-collapse:collapse;margin-top:10px}table.data th{background:#eee;text-align:left;padding:7px;border:1px solid #ccc}table.data td{padding:7px;border:1px solid #ddd}
.section{font-size:12pt;font-weight:bold;margin-top:18px;margin-bottom:6px}
.result{border:2px solid #333;padding:14px;margin-top:18px;text-align:center}.result .amount{font-size:22pt;font-weight:bold}
.meta{width:100%;margin:10px 0}.meta td{padding:3px 8px}
.footer{text-align:center;font-size:8pt;color:#666;border-top:1px solid #ccc;padding-top:8px;margin-top:24px}
</style>
</head>
<body>
<?php
$row=$register_log[0];
$company=!empty($location->company)?$location->company:$this->config->item('company');
$logo = !empty($location->company_logo) ? base_url().'uploads/logos/'.$location->company_logo : '';
$status=abs($total_difference)<0.005?'CUADRADO':($total_difference>0?'SOBRANTE':'FALTANTE');
?>
<table class="header" width="100%"><tr><td>
<div class="company"><?php echo html_escape($company); ?></div>
<div class="branch">Sucursal: <?php echo html_escape($location->name); ?></div>
<div class="title">REPORTE ADMINISTRATIVO DE CIERRE DE CAJA</div>
</td><td width="140" align="right"><?php if($logo){ ?><img class="logo" src="<?php echo $logo; ?>"><?php } ?></td></tr></table>

<table class="meta">
<tr><td><strong>Folio:</strong> #<?php echo (int)$row->register_log_id; ?></td><td><strong>Caja:</strong> <?php echo html_escape($row->register_name); ?></td></tr>
<tr><td><strong>Empleado:</strong> <?php echo html_escape(trim($row->close_first_name.' '.$row->close_last_name)); ?></td><td><strong>Apertura:</strong> <?php echo date(get_date_format().' '.get_time_format(),strtotime($row->shift_start)); ?></td></tr>
<tr><td><strong>Fecha:</strong> <?php echo date(get_date_format(),strtotime($row->shift_end)); ?></td><td><strong>Cierre:</strong> <?php echo date(get_time_format(),strtotime($row->shift_end)); ?></td></tr>
</table>

<table class="summary"><tr>
<td><strong>VENTAS TOTALES</strong><div class="value"><?php echo to_currency($total_sales); ?></div></td>
<td><strong>OPERACIONES</strong><div class="value"><?php echo (int)$operations; ?></div></td>
<td><strong>GASTOS / RETIROS</strong><div class="value"><?php echo to_currency($total_subtractions); ?></div></td>
</tr></table>

<div class="section">Ventas y diferencias por método de pago</div>
<table class="data"><thead><tr><th>Método</th><th>Fondo inicial</th><th>Ventas</th><th>Entradas</th><th>Retiros/Gastos</th><th>Declarado</th><th>Diferencia</th></tr></thead><tbody>
<?php foreach($register_log as $p){ $name=strpos($p->payment_type,'common_')!==FALSE?lang($p->payment_type):$p->payment_type; ?>
<tr><td><?php echo html_escape($name); ?></td><td><?php echo to_currency($p->open_amount); ?></td><td><?php echo to_currency($p->payment_sales_amount); ?></td><td><?php echo to_currency($p->total_payment_additions); ?></td><td><?php echo to_currency($p->total_payment_subtractions); ?></td><td><?php echo to_currency($p->close_amount); ?></td><td><strong><?php echo to_currency($p->difference); ?></strong></td></tr>
<?php } ?>
</tbody></table>

<div class="section">Movimientos de caja</div>
<table class="data"><thead><tr><th>Fecha y hora</th><th>Empleado</th><th>Método</th><th>Importe</th><th>Nota</th></tr></thead><tbody>
<?php if($register_log_details){ foreach($register_log_details as $d){ ?>
<tr><td><?php echo date(get_date_format().' '.get_time_format(),strtotime($d['date'])); ?></td><td><?php echo html_escape($d['employee_name']); ?></td><td><?php echo html_escape(strpos($d['payment_type'],'common_')!==FALSE?lang($d['payment_type']):$d['payment_type']); ?></td><td><?php echo to_currency($d['amount']); ?></td><td><?php echo html_escape($d['note']); ?></td></tr>
<?php }} else { ?><tr><td colspan="5">Sin movimientos adicionales registrados.</td></tr><?php } ?>
</tbody></table>

<div class="result"><strong>RESULTADO DEL CORTE</strong><div class="amount"><?php echo to_currency($total_difference); ?></div><strong><?php echo $status; ?></strong></div>
<?php if(!empty($row->notes)){ ?><p><strong>Notas del corte:</strong> <?php echo nl2br(html_escape($row->notes)); ?></p><?php } ?>

<div class="footer">Generado automáticamente para SAMY · Programado por SECOYT</div>
</body></html>