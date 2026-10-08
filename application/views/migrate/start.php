<!DOCTYPE html>
<html class="<?php echo $this->config->item('language');?>">
<head>
	<meta charset="UTF-8" />
    <title><?php echo $this->config->item('branding')['name']; ?></title>
	<link rel="icon" href="<?php echo base_url();?>favicon_<?php echo $this->config->item('branding_code');?>.ico" type="image/x-icon"/>	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/> <!--320-->
	<base href="<?php echo base_url();?>" />
	
	<link rel="icon" href="<?php echo base_url();?>favicon_<?php echo $this->config->item('branding_code');?>.ico" type="image/x-icon"/>
	
	<?php 
	$this->load->helper('assets');
	foreach(get_css_files() as $css_file) { ?>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url().$css_file['path'].'?'.ASSET_TIMESTAMP;?>" />
	<?php } ?>
 		
	<script>
	var SITE_URL= "<?php echo site_url(); ?>";
	var BASE_URL= "<?php echo base_url(); ?>";
	</script>

  <script src="<?php echo base_url();?>assets/js/jquery.js?<?php echo ASSET_TIMESTAMP; ?>" type="text/javascript" language="javascript" charset="UTF-8"></script>
	
</head>
<body>
<div class="jumbotron">
	<div class="container">
		<div class="alert alert-warning" role="alert"><?php echo lang('migrate_warning'); ?>.</div>
	<div class="well">
  <p><?php echo $is_new ? lang('migrate_install_message') : lang('migrate_message'); ?></p>
		
  <p><a class="btn btn-primary btn-lg" href="javascript:void(0)" id="upgrade_database" role="button"><?php echo $is_new ? lang('migrate_install_database') : lang('migrate_upgrade_database');?></a></p>
	</div>
	<div class="progress" id="progress_container" style="display: none;">
	  <div class="progress-bar progress-bar-striped active" role="progressbar" id="progessbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
	    <span id="progress_percent">0</span>% <span id="progress_title"><?php echo lang('migrate_complete');?></span> <span id="progress_message"></span>
	  </div>
	</div>
	<div id="migration_error" class="alert alert-danger" role="alert" style="display:none;"></div>
	
  <a class="btn btn-default btn-lg pull-right" disabled href="<?php echo site_url('login'); ?>" id="login_to_pos" style="display:none;" role="button"><?php echo $is_new ? lang('migrate_login_to_new_pos') : lang('migrate_login_to_upgraded_pos');?> <span id="status_icon" class="glyphicon glyphicon-remove"></span></a>
	
</div>
<div id="all_messages_container" style="display: none;" class="text-center">
	<h4><?php echo lang('migrate_tasks_completed');?></h4>
<ul id="all_messages" class="list-group">
	
</ul>
</div>
</div>

</body>
</html>
<script type='text/javascript'>

var all_messages = [];

$("#upgrade_database").click(function()
{
	$(this).hide();
	set_progress(1,<?php echo json_encode(lang('migrate_starting')); ?>);
	migrate_one_step();
});

function migrate_one_step()
{
	$.getJSON(SITE_URL+'/migrate/migrate_one_step').done(function(response)
	{
		if (!response || typeof response.success !== 'boolean' || typeof response.percent_complete !== 'number')
		{
			migration_failed('La actualización recibió una respuesta inesperada. Revisa la petición migrate_one_step en F12 → Red.');
			return;
		}
		if (!response.success)
		{
			migration_failed(response.message || 'No se pudo completar la actualización de la base de datos.');
			return;
		}
		set_progress(response.percent_complete,response.message);
		all_messages.push(response.message);
		
		if (response.success && response.has_next_step)
		{
			migrate_one_step();
		}
		else
		{
			$("#login_to_pos").show();
			
			for(var k = 0;k< all_messages.length;k++)
			{
				$("#all_messages").append($('<li class="list-group-item">').text(all_messages[k]));
			}
			
			$("#all_messages_container").show();
		}
	}).fail(function(xhr, status)
	{
		var detail = xhr.status ? 'HTTP '+xhr.status : 'sin respuesta del servidor';
		if (status === 'parsererror') detail += ', respuesta no válida';
		migration_failed('La actualización se detuvo ('+detail+'). Revisa la petición migrate_one_step en F12 → Red y el registro de errores de cPanel. No vuelvas a iniciarla mientras haya una petición en curso.');
	});
}

function migration_failed(message)
{
	$('#progessbar').removeClass('active progress-bar-striped').addClass('progress-bar-danger');
	$('#migration_error').text(message).show();
	$('#login_to_pos').hide();
}

function set_progress(percent,message)
{
	$("#progress_container").show();
	$('#progessbar').attr('aria-valuenow', percent).css('width',percent+'%');
	$('#progress_percent').html(percent);
	if (message !='')
	{
		$("#progress_message").text('('+message+')');
	}
	else
	{
		$("#progress_message").html('');
	}
	
	if(percent == 100)
	{
		setTimeout(function(){ 
			$('.alert').slideToggle();
			$("#status_icon").removeClass("glyphicon-remove").addClass("glyphicon-ok");
			$("#login_to_pos").removeClass("btn-default").addClass("btn-success").removeAttr('disabled');
	},1000);
		
		
		
		
	}
}
</script>
