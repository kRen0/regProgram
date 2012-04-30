<section class="title">
	<h4><?php echo lang('program_title'); //Тест?></h4>
</section>

<section class="item">			
	<div class="form_inputs">	
		<?php if (!empty($program)): ?>
		
			<table border="0" class="table-list">
				<thead>
					<tr>
						<th><?= lang('program.title'); ?></th>
						<th><?= lang('program.opened'); ?> </th>
						<th><?= lang('program.limit'); ?><br />
						<span class="required-icon tooltip">*</span></label><?= lang('program.limit.warn');?>
						</th>
						<th><?= lang('program.p_count'); ?> </th>
						<th><?= lang('date.in_wait_list'); ?> </th>
						<th>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach( $program as $p ): ?>
					<tr>
						<td><a href="<?='admin/program/view/'.$p->id; ?>"><?= $p->title; ?> </a></td>
						<td><?= ($p->open+0)?lang('program.opened.y'):lang('program.opened.n') ?></td>
						<td><?= ($p->LIMIT+0)*!($p->unlimited+0) ?></td>
						<td><a href="<?='admin/program/participants/'.$p->id; ?>"><?= ($p->p_count+0) ?></a></td>
						<td><a href="<?='admin/program/participants/'.$p->id; ?>"><?= ($p->w_p_count+0) ?></a></td>
						<td class="align-center buttons buttons-small">
							<?php echo anchor('admin/program/view/'.$p->id, lang('program.edit'), 'class="button" '); ?>
							<?php echo anchor('admin/program/participants/'.$p->id, lang('program.p_view'), 'class="button" '); ?>
							<?php echo anchor('admin/program/reset/'.$p->id, lang('program.p_reset'), 'class="button" '); ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else: ?>
			<div class="blank-slate">
				<div class="no_data">
					<?php echo lang('program.none'); ?>
				</div>
			</div>
		<?php endif;?>
	</div>
</section>