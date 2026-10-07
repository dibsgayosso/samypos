<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="panel panel-piluku" id="samypos-push"
 data-config="<?php echo html_escape(site_url('push/config')); ?>"
 data-api="<?php echo html_escape(site_url('push/action')); ?>"
 data-worker="<?php echo html_escape(base_url('upup.sw.min.js').'?'.BUILD_TIMESTAMP); ?>"
 data-scope="<?php echo html_escape(base_url()); ?>">
 <div class="panel-heading"><h4>Avisos de mercancía en tu teléfono</h4></div>
 <div class="panel-body">
  <p>Recibe un aviso para revisar las recepciones pendientes. En iPhone, agrega SAMYPOS a la pantalla de inicio y ábrelo desde allí.</p>
  <button type="button" class="btn btn-primary" data-action="subscribe" disabled>Activar en este teléfono</button>
  <button type="button" class="btn btn-default" data-action="test" disabled>Enviar prueba</button>
  <button type="button" class="btn btn-default" data-action="unsubscribe" disabled>Desactivar</button>
  <button type="button" class="btn btn-primary" data-action="setup" style="display:none" hidden disabled>Activar servicio push</button>
  <p role="status" aria-live="polite">Comprobando servicio…</p>
 </div>
</div>
<script src="<?php echo base_url('assets/js/samypos-push.js'); ?>"></script>
