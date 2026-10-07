<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h1 style="color:#243c64"><?php echo html_escape($this->config->item('company') ?: 'Samy'); ?></h1>
<h2>Informe de créditos · <?php echo html_escape($branch['name']); ?></h2>
<p>Corte del informe: <?php echo html_escape($report['captured'].' · '.($report['timezone'] ?? '')); ?><br>Referencia semanal: <?php echo html_escape($report['previous_date']); ?></p>
<table cellpadding="7" border="1"><tr><th>Saldo actual por cobrar</th><th>Saldo hace 7 días</th><th>Variación</th></tr><tr>
<td><?php echo to_currency($branch['total']); ?></td><td><?php echo $branch['previous']===NULL?'Sin historial suficiente':to_currency($branch['previous']); ?></td>
<td><?php echo $branch['analysis']['difference']===NULL?'No disponible':to_currency($branch['analysis']['difference']); ?><?php if ($branch['analysis']['percent']!==NULL) echo ' ('.html_escape($branch['analysis']['percent']).'%)'; ?></td></tr></table>
<p><strong><?php echo html_escape($branch['analysis']['text']); ?></strong> <?php if ($branch['previous']===0.0 || $branch['previous']===0) { ?>La base anterior es cero; no se calcula porcentaje.<?php } ?></p>
<h3>Cuentas de clientes · Saldos y liquidaciones de la semana</h3>
<table cellpadding="5" border="1"><thead><tr><th>Cliente</th><th>Saldo actual</th><th>Saldo anterior</th></tr></thead><tbody>
<?php foreach($branch['clients'] as $client) { ?><tr><td><?php echo html_escape($client['name']); ?></td><td><?php echo to_currency($client['balance']); ?></td><td><?php echo $client['previous']===NULL?'Sin historial':to_currency($client['previous']); ?></td></tr><?php } ?>
<?php if (!$branch['clients']) { ?><tr><td colspan="3">Sin saldos actuales ni anteriores registrados.</td></tr><?php } ?>
</tbody></table>
<h3>Documentos con más de 30 días de atraso</h3>
<?php if (!$branch['invoices_ready']) { ?><p>No hay registros de vencimiento disponibles.</p><?php } elseif (!$branch['overdue']) { ?><p>No se encontraron documentos abiertos con vencimiento registrado hace más de 30 días. Esto no acredita que todos los créditos estén al corriente.</p><?php } else { ?>
<table cellpadding="5" border="1"><thead><tr><th>Cliente / documento</th><th>Vencimiento</th><th>Días de atraso</th><th>Saldo del documento</th></tr></thead><tbody>
<?php foreach($branch['overdue'] as $invoice) { ?><tr><td><?php echo html_escape($invoice['name']); ?> · #<?php echo (int)$invoice['invoice_id']; ?></td><td><?php echo html_escape($invoice['due_date']); ?></td><td><?php echo (int)$invoice['days']; ?></td><td><?php echo to_currency($invoice['balance']); ?></td></tr><?php } ?>
</tbody></table><?php } ?>
<p style="font-size:8px">La cuenta usa los saldos de clientes asignados a la sucursal; la comparación se reconstruye del historial disponible de movimientos y puede verse afectada por reasignaciones de clientes. Los documentos vencidos corresponden a la sucursal del documento y sus saldos registrados: no se suman nuevamente a la cuenta. Un abono general debe aplicarse al documento correspondiente para actualizar su atraso. Los créditos sin vencimiento documentado requieren revisión; no se presume atraso únicamente por antigüedad.</p>
<hr><p style="font-size:8px">Generado automáticamente para Samy y programado por Secoyt.</p>
