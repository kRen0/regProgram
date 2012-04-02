<section class="title">
	<h4> Программы </h4>
</section>

<section class="item">
	
	<?php echo form_open('admin/program/settings', 'class="crud"'); ?>
	
		<div class="tabs">
	
			<?php echo lang('category'); ?>
			<div class="form_inputs" id="banners-settings">	
				<fieldset>
				<ul>
					<?php
					
						if(!empty($settings)):
							foreach($settings as $setting): ?>

							<li>
								<label for="categoryID"><?php echo lang('category'); ?> <span> * </span> </label>
								<div class="input"><?= form_dropdown('program_category',$categories,$setting->value);?> </div>
							</li>
						<?php
							endforeach;
						else:?>
							<li>
								<label for="categoryID"><?php echo lang('category'); ?> <span> * </span> </label>
								<div class="input"><?= form_dropdown('program_category',$categories);?> </div>
							</li>
						<?php endif;
					?>
				</ul>
				</fieldset>
			</div>
		</div>
	
		<div class="buttons align-right padding-top">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
		</div>
	
	<?php echo form_close(); ?>
	
</section>