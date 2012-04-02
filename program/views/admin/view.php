<section class="title">
	<h4><?php echo $program->title ?></h4>
    <h5><?php echo lang('program.setup'); ?></h5>
</section>
<section class="item">
<?php echo form_open('admin/program/view/'.$program->id, 'id="program"'); ?>
<div class="form_inputs">
    <fieldset>
        <ul>
            <li>
                <label for="status"><?php echo lang('program.opened'); ?></label>
                <div class="input">
                    <?php echo form_dropdown('status', $status, $program->open + 0); ?>
                </div>
            </li>
			<li>
                <label for="limit"><?php echo lang('program.limit').'<br /><span class="required-icon tooltip">*</span>'.lang('program.limit.warn'); ?>
                <div class="input">
                    <input name="limit" type="text" value="<?php echo ($program->LIMIT + 0); ?>" />
                </div>
            </li>
        </ul>
    </fieldset>
</div>
<div class="buttons">
<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
</div>
<?php echo form_close(); ?>
</section>