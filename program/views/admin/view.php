<script type="text/javascript">
$(document).ready(function(){

    var i = 0;

    $('#add').click(function() {
        $('\
		<div class="for_date" id="date'+ i +'"><input type="text" class="data_field" name="date[]" placeholder="<? echo lang('program.sdate');?>"/>\
		<input type="text" class="field" name="limit[]" placeholder="<? echo lang('program.slimit');?>"/>\
		<input type="button" value="<? echo lang('program.date.delete');?>" onClick="del(\'date'+ i +'\')"></div>'
		).fadeIn('slow').appendTo('.inputs');
        i++;
		$('.data_field').simpleDatepicker();
    });
	$('#clear').click(function() {
	    $('.for_date').remove();
    });
 $('.data_field').live('click', function(){
            $(this).datepicker();
			$(this).datepicker( "show" )
       });
});
    function del(objectId) {
		$('#'+objectId).remove();
    }
</script>
<section class="title">
	<h4><?php echo $program->title ?></h4>
    <h5><?php echo lang('program.setup'); ?></h5>
</section>
<section class="item">
<?php echo form_open('admin/program/view/'.$program->id, 'id="program"'); ?>
<div class="form_inputs">
    <fieldset>
	<span class="required-icon tooltip">*</span></label><? echo lang('program.limit.warn');?><br />
	<br />
		<input type="button" value="<? echo lang('program.date.add');?>" id="add">
		<input type="button" value="<? echo lang('program.clear');?>" id="clear">
		<div class="inputs"> 
		<? if(empty($post)): ?>
		<?foreach ($dates AS $date) :?>
			<div class="for_date" id="u_date<?echo $date->id?>">
			<? echo form_input('date[]', $date->day.'.'.$date->month.'.'.$date->year, 'class="data_field"');
			echo form_input('limit[]', $date->LIMIT);?>
			<input type="button" value="<? echo lang('program.date.delete');?>" onClick="del('u_date<?echo $date->id?>')">
			</div>
		<? endforeach;?>
		<?elseif(isset($post['date'])) :?>
		<? for ($i=0; $i < count($post['date']); ++$i) :?>
			<div class="for_date" id="u_date<?echo ($i)?>">
			<? echo form_input('date[]', $post['date'][$i], 'class="data_field"');
			echo form_input('limit[]', $post['limit'][$i]);?>
			<input type="button" value="<? echo lang('program.date.delete');?>" onClick="del('u_date<?echo $i?>')">
			</div>
		<? endfor;?>
		<?endif; ?>
		</div>
    </fieldset>
</div>
<div class="buttons">
<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
</div>
<?php echo form_close(); ?>
</section>