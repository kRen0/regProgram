<section class="title">
	<h4><?php echo isset($program->title)?$program->title:lang('participants.action_error'); ?></h4>
	<h5><?php echo lang('participants.title'); ?></h5>
</section>

<section class="item">			
	<div class="form_inputs">	
		<?php echo form_open('admin/program/participants_del');?>
		<?php if (!empty($participants)): ?>
		
			<table border="0" class="table-list">
				<thead>
					<tr>
						<th width="30"><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
						<th><?= lang('program.email'); ?></th>
						<th><?= lang('program.firstname'); ?></th>
						<th><?= lang('program.lastname'); ?> </th>
						<th><?= lang('program.sex'); ?> </th>
						<th><?= lang('program.phone'); ?> </th>
						<th>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach( $participants as $participant ): ?>
					<tr>
						<td><?=  form_checkbox('action_to[]', $participant->id); ?></td>
						<td><a href="mailto:<?= $participant->email ?>"><?= $participant->email ?></a></td>
						<td><?= $participant->FirstName ?> </td>
						<td><?= $participant->LastName ?> </td>
						<td><?= $participant->sex=='1'?lang('program.sex.m'):$participant->sex=='2'?lang('program.sex.f'):lang('program.sex.n')?></td>
						<td><?= $participant->phone ?> </td>
						<td class="align-center buttons buttons-small">
							<?php echo anchor('admin/program/participants_del/'.$participant->id.'?return='.$program->id, lang('global:delete'), array('class'=>'confirm button delete')); ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		
			<div class="table_action_buttons">
				<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete') )); ?>
			</div>
		<input type="hidden" name="record_id" value="<?=$program->id; ?>">
		<?= form_close(); ?>
		<?php else: ?>
			<div class="blank-slate">
				<div class="no_data">
					<?php echo lang('program.p_none'); ?>
				</div>
			</div>
		<?php endif;?>
	</div>
</section>