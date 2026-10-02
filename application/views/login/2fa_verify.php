<!DOCTYPE html>
<html>

<head>
	<title><?php
			$this->load->helper('demo');
			echo !is_on_demo_host() ?  $this->config->item('company') . ' -- ' . lang('common_powered_by') . ' PHP Point Of Sale' : 'Demo - PHP Point Of Sale | Easy to use Online POS Software' ?></title>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<base href="<?php echo base_url(); ?>" />
	<link rel="icon" href="<?php echo base_url(); ?>favicon.ico" type="image/x-icon" />

	<?php
	$this->load->helper('assets');
	foreach (get_css_files() as $css_file) { ?>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url() . $css_file['path'] . '?' . ASSET_TIMESTAMP; ?>" />
	<?php } ?>

	<script src="<?php echo base_url(); ?>assets/js/jquery.js?<?php echo ASSET_TIMESTAMP; ?>" type="text/javascript" language="javascript" charset="UTF-8"></script>
	<style type="text/css">
		body {
			padding: 5px;
		}
	</style>

	<script type="text/javascript">
		$(document).ready(function() {
			$("#security_code").focus();
		});
	</script>
	<?php
	$this->load->helper('demo');
	if (is_on_demo_host()) { ?>
		<script src="//phppointofsale.com/js/iframeResizer.contentWindow.min.js"></script>
	<?php } ?>
</head>

<body>
	<div class="flip-container">
		<div class="flipper">
			<div class="front">
				<!-- front content -->
				<div class="holder">

					<h1 class="heading">
						<?php echo img(
							array(
								'src' => $this->Appconfig->get_logo_image(),
								'style' => 'width: auto;max-width: 180px',
							)
						); ?>
					</h1>
					<?php echo form_open('login/do_verify_2fa/' . (isset($key) ? $key : ""), array('class' => 'form verify_2fa_form')) ?>

					<?php echo form_hidden('username', $username); ?>
					<p><?php echo lang('common_enter_code_to_login'); ?></p>

					<?php if (isset($error_message)) { ?>
						<div class="alert alert-danger">
							<?php echo $error_message; ?>
						</div>
					<?php } ?>

					<?php echo form_input(array(
						'name' => 'security_code',
						'id' => 'security_code',
						'class' => 'form-control',
						'size' => '20'
					)); ?>
					<div class="bottom_info">
						<?php echo anchor('login', lang('login_login')); ?>
					</div>
					<div class="clearfix"></div>
					<button type="submit" class="btn btn-primary btn-block"><?php echo lang('common_verify'); ?></button>
					<?php echo form_close() ?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>