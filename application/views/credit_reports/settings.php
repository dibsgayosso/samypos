<?php $this->load->view('partial/header'); ?>
<div class="panel panel-piluku"><div class="panel-heading"><h3>Informe periódico de créditos</h3></div><div class="panel-body">
<?php if ($notice=$this->session->flashdata('credit_report_result')) { ?><div class="alert alert-info" role="status"><?php echo html_escape($notice); ?></div><?php } ?>
<p>Recibe un PDF con los créditos de tus sucursales autorizadas, variación frente a 7 días antes y documentos con más de 30 días de atraso.</p>
<p>Correo del propietario: <strong><?php echo html_escape($email ?: 'Configura tu correo en Empleados'); ?></strong>. El informe se enviará al correo registrado en tu cuenta.</p>
<?php if (!$ready) { ?><div class="alert alert-warning">Aplica la migración de informes de crédito para programar los envíos.</div><?php } else { ?>
<?php echo form_open('creditreports/save'); ?>
<input type="hidden" name="token" value="<?php echo html_escape($token); ?>">
<div class="checkbox"><label><input type="checkbox" name="enabled" value="1" <?php echo !empty($schedule['enabled'])?'checked':''; ?>> Enviar informes automáticamente</label></div>
<div class="row">
<div class="col-sm-3"><label for="credit-weekday">Día de envío</label><select id="credit-weekday" class="form-control" name="weekday">
<?php foreach (array(1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado',7=>'Domingo') as $value=>$label) { ?><option value="<?php echo $value; ?>" <?php echo (int)($schedule['weekday'] ?? 1)===$value?'selected':''; ?>><?php echo $label; ?></option><?php } ?></select></div>
<div class="col-sm-3"><label for="credit-frequency">Periodicidad</label><select id="credit-frequency" class="form-control" name="interval_weeks">
<?php for($weeks=1;$weeks<=4;$weeks++) { ?><option value="<?php echo $weeks; ?>" <?php echo (int)($schedule['interval_weeks'] ?? 1)===$weeks?'selected':''; ?>><?php echo $weeks===1?'Cada semana':'Cada '.$weeks.' semanas'; ?></option><?php } ?></select></div>
<div class="col-sm-3"><label for="credit-hour">Hora</label><input id="credit-hour" class="form-control" type="time" name="send_time" required value="<?php echo html_escape($schedule['send_time'] ?? '08:00'); ?>"></div>
<div class="col-sm-3"><label for="credit-zone">Zona horaria</label><select id="credit-zone" class="form-control" name="timezone"><?php foreach(timezone_identifiers_list() as $zone) { ?><option value="<?php echo html_escape($zone); ?>" <?php echo ($schedule['timezone'] ?? $timezone)===$zone?'selected':''; ?>><?php echo html_escape($zone); ?></option><?php } ?></select></div>
</div>
<p style="margin-top:20px"><button class="btn btn-primary" type="submit">Guardar programación</button> <a class="btn btn-default" href="<?php echo site_url('creditreports/preview'); ?>">Descargar muestra en PDF</a> <a class="btn btn-default" href="<?php echo site_url('home'); ?>">Volver a Home</a></p>
<?php echo form_close(); ?>
<?php if (!empty($schedule['enabled'])) { ?><p>Próximo envío: <?php $next=(new DateTimeImmutable($schedule['next_run'],new DateTimeZone('UTC')))->setTimezone(new DateTimeZone($schedule['timezone'])); echo html_escape($next->format('d/m/Y H:i').' · '.$schedule['timezone']); ?></p><?php } ?>
<?php if ($job) { ?><p>Último informe: <?php echo html_escape(array('pending'=>'Pendiente de envío','sent'=>'Enviado','failed'=>'No entregado; revisa SMTP','canceled'=>'Cancelado por cambios de permisos o programación')[$job['status']] ?? $job['status']); ?></p><?php } ?>
<?php } ?>
<p class="text-muted">La comparación siempre es contra 7 días antes, independientemente de la periodicidad elegida. Sin historial suficiente se indicará que no se puede calcular. Créditos sin fecha de vencimiento no se marcan automáticamente como atrasados.</p>
</div></div>
<?php $this->load->view('partial/footer'); ?>
