<div class="blog_post">
	<!-- Post heading -->
	<div class="post_heading">
		<h1><?php echo $post->title; ?></h1>
	</div>
	<div class="post_body">
		<?php echo $post->body; ?>
	</div>
</div>

<?php if (isset($is_program) && $is_program) : ?>
<?php if ($open) : ?>
	<h4><?=lang('program.reg_title')?></h4>
	<?php echo validation_errors(); ?>
	<form id="programRegister" action="<?php echo site_url(); ?>/program/register/<?=$slug?>" method="post">
			<div class="form_first_name">
				<label for="first_name"><?=lang('program.firstname')?>:</label>
				<input type="text" name="first_name" id="first_name" maxlength="40" value="<?=($profile != NULL?$profile->first_name:'').set_value('first_name')?>" />
			</div>
			<div class="form_last_name">
				<label for="last_name"><?=lang('program.lastname')?>:</label>
				<input type="text" name="last_name" id="last_name" maxlength="40" value="<?=($profile != NULL?$profile->last_name:'').set_value('last_name')?>" />
			</div>
			<div class="form_email">
				<label for="email"><?=lang('program.email')?>:</label>
				<input type="text" name="email" maxlength="40" value="<?=($profile != NULL?$profile->email:'').set_value('email')?>" />
			</div>
			<div class="form_sex">
				<label for="sex"><?=lang('program.sex');?>:</label>
				<input type="radio" name="sex" value="1"<?=($profile != NULL?$profile->gender=='m'?'checked':'':'').set_radio('sex', '1')?>><?=lang('program.sex.m')?></input><br />
				<input type="radio" name="sex" value="2"<?=($profile != NULL?$profile->gender=='f'?'checked':'':'').set_radio('sex', '2')?>><?=lang('program.sex.f')?></input>
			</div>
			<input type="hidden" name="id" value="<?=$post->id;?>">
			<div class="form_submit">
				<input type="submit" name="submit" value="<?=lang('program.reg_submit')?>"  /></div>
	</form>
	<span class="already_registered"><?=lang('program.p_count.for_user').': <font color=\"#FF8000\">'.$count.'</font>. '.lang('program.limit.for_user').': <font color=\"#FF8000\">'.$limit.'</font>.' ?></span>
<?php else: ?> 
	<span class="programm_reg_closed"><?=lang('program.reg_closed')?></span>
<?php endif; ?> 
<?php endif; ?> 
<?php if ($post->comments_enabled): ?>
	<?php echo display_comments($post->id); ?>
<?php endif; ?>