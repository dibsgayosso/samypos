<?php $this->load->view("partial/header_standalone"); ?>
<h1 class="text-center"><?php echo lang('common_success');?>...<?php echo lang('common_reloading')?></h1>

<script>
setTimeout(function()
{
	window.location.href = window.location.href;
},5000);
</script>
<?php $this->load->view("partial/footer_standalone"); ?>