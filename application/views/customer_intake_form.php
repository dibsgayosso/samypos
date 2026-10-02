<?php $this->load->view("partial/header_standalone"); ?>
<form action="" method="POST">
<div class="container">
<h1 class="text-center"><?php echo lang('common_customer_intake_form');?></h1>
<div class="row">
	<div class="col-md-12">
		<div class="form-group">
			<?php 
			echo form_label(lang('common_first_name').':', 'first_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">
				<div class="input-group" style="width:100%">
					<div class="input-group-btn" style="width:4rem">
						<?php  
						$titles = array(
							"" 		=> '',
							"Mr." 		=> lang('common_mr.'),
							"Mrs." 		=> lang('common_mrs.'),
							"Dr." 		=> lang('common_dr.'),
							"Hon." 		=> lang('common_hon.'),
							"Prof." 	=> lang('common_prof.'),
							"Rev." 		=> lang('common_rev.'),
							"Rt.Hon." 	=> lang('common_rt_hon.'),
							"Sr." 		=> lang('common_sr.'),
							"Jr." 		=> lang('common_jr.'),
							"St." 		=> lang('common_st.'),

							);
						?>
						<?php echo form_dropdown('title', $titles,'', 'class="form-control form-control-sm form-inps" id="title"');?>
				    </div>
					<?php echo form_input(array(
						'class'=>'form-control',
						'name'=>'first_name',
						'id'=>'first_name',
						'value'=>'')
					);?>
				</div>
			</div>
		</div>

		<div class="form-group">
			<?php echo form_label(lang('common_last_name').':', 'last_name',array('class'=>' col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">
			<?php echo form_input(array(
				'class'=>'form-control',
				'name'=>'last_name',
				'id'=>'last_name',
				'value'=>'')
			);?>
			</div>
		</div>

		<div class="form-group">
			<?php echo form_label(lang('common_email').':', 'email',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">
			<?php echo form_input(array(
				'class'=>'form-control',
				'name'=>'email',
				'type'=>'text',
				'id'=>'email',
				'value'=>'')
				);?>
			</div>
		</div>
		<div class="form-group">	
			<?php echo form_label(lang('common_phone_number').':', 'phone_number',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">
			<?php echo form_input(array(
				'class'=>'form-control',
				'name'=>'phone_number',
				'id'=>'phone_number',
				'value'=>''));?>
			</div>
		</div>

<div class="form-group">	
<?php echo form_label(lang('common_address_1').':', 'address_1',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
	<div class="col-sm-9 col-md-9 col-lg-10">
	<?php echo form_input(array(
		'class'=>'form-control',
		'name'=>'address_1',
		'id'=>'address_1',
		'value'=>''));?>
	</div>
</div>

			<div class="form-group">	
<?php echo form_label(lang('common_address_2').':', 'address_2',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
	<div class="col-sm-9 col-md-9 col-lg-10">
	<?php echo form_input(array(
		'class'=>'form-control',
		'name'=>'address_2',
		'id'=>'address_2',
		'value'=>''));?>
	</div>
</div>

			<div class="form-group">	
<?php echo form_label(lang('common_city').':', 'city',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
	<div class="col-sm-9 col-md-9 col-lg-10">
	<?php echo form_input(array(
		'class'=>'form-control ',
		'name'=>'city',
		'id'=>'city',
		'value'=>''));?>
	</div>
</div>

			<div class="form-group">	
<?php echo form_label(lang('common_state').':', 'state',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
	<div class="col-sm-9 col-md-9 col-lg-10">
	<?php echo form_input(array(
		'class'=>'form-control ',
		'name'=>'state',
		'id'=>'state',
		'value'=>''));?>
	</div>
</div>

			<div class="form-group">	
<?php echo form_label(lang('common_zip').':', 'zip',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
	<div class="col-sm-9 col-md-9 col-lg-10">
	<?php echo form_input(array(
		'class'=>'form-control ',
		'name'=>'zip',
		'id'=>'zip',
		'value'=>''));?>
	</div>
</div>

			<div class="form-group">	
<?php echo form_label(lang('common_country').':', 'country',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
	<div class="col-sm-9 col-md-9 col-lg-10">
	<?php echo form_input(array(
		'class'=>'form-control ',
		'name'=>'country',
		'id'=>'country',
		'value'=>''));?>
	</div>
</div>
<div class="form-group pull-right">	
	<br />
	<input type="submit" class="btn btn-primary">
</div>
</div>

</div><!-- /col-md-12 -->
</div><!-- /row -->
</div>
</form>
<?php $this->load->view("partial/footer_standalone"); ?>