<div class="blog_post">
</div>
<script type="text/javascript">
$(document).ready(function(){
var events = [ 	        
						<? foreach($dates as $date): ?>
							{ "EventID": <?= $date->dateID;?>, "StartDateTime": "<?= $date->date;?>", "Title": "<?= $date->title;?>", "URL": "#", "Description": "<?= str_replace(array("\r","\n"),"",strip_tags($date->intro));?>", "programID":"<?= $date->id;?>" },
						<? endforeach; ?>
			];
$.jMonthCalendar.Initialize({ 	
	onEventLinkClick: function(event) { 
		$('#event').modal();
		$('#ev-id').attr('value',event.programID);
		$('#ev-date').attr('value',event.EventID);
    },
	height: 650,
	width: 680,
	firstDayOfWeek: 1,
	navLinks: {
		enableToday: true,
		enableNextYear: true,
		enablePrevYear: true,
		p:'&lsaquo;Предыдущий', 
		n:'Следующий&rsaquo;', 
		t:'Сегодня'
	},
	locale: {
				days: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье"],
				months: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
			}
}, events);
var options = { 
    success: showRegResponse,
    timeout: 3000
  };
  $('#programRegister').submit(function() { 
    $(this).ajaxSubmit(options); 
    return false;
  }); 
});
	function showRegResponse(response, statusText)  { 
		$.ajax({dataType:"json"});
		for(var i in response.data){
                if(response.data[i])
                  noty({text:response.data[i],animateOpen:{opacity:'show'},animateClose:{opacity:'hide'},layout:'topRight',theme:'noty_theme_default',type:'error',timeout:10000});  
                
              }
		if((response.status) == 'success')
			$("#programRegister").resetForm();
		noty({text:response.message,animateOpen:{opacity:'show'},animateClose:{opacity:'hide'},layout:'topRight',theme:'noty_theme_default',type:response.status,timeout:10000});
	}
	function HideError()  { 

		$('#output').attr('style','display:none');
		$('#name_error_output').html('');
		$('#email_error_output').html('');
		$('#question_error_output').html('');
		$('#captcha_error_output').html('');
			
	}
</script>
<div id = "jMonthCalendar">

</div>

 <div class="overlay overlay_event" id="event">
    <a class="close simplemodal-close"></a>
	<form id="programRegister" action="<?php echo site_url(); ?>/program/register/" method="post">
			<input type="hidden" name="id" id="ev-id">
            <input type="hidden" name="date" id="ev-date">
			<div class="form_first_name">
				<label for="first_name"><?=lang('program.firstname')?>:</label>
				<input type="text" name="first_name" id="first_name" maxlength="40" value="<?=($profile != NULL?$profile->first_name:'')?>" />
			</div>
			<div class="form_last_name">
				<label for="last_name"><?=lang('program.lastname')?>:</label>
				<input type="text" name="last_name" id="last_name" maxlength="40" value="<?=($profile != NULL?$profile->last_name:'')?>" />
			</div>
			<div class="form_email">
				<label for="email"><?=lang('program.email')?>:</label>
				<input type="text" name="email" maxlength="40" value="<?=($profile != NULL?$profile->email:'')?>" />
			</div>
			<div class="form_phone">
				<label for="email"><?=lang('program.phone')?>:</label>
				<input type="text" name="phone" maxlength="40" value="<?=($profile != NULL?$profile->phone:'')?>" />
			</div>
			<div class="form_sex">
				<label for="sex"><?=lang('program.sex');?>:</label>
				<input type="radio" name="sex" value="1"<?=($profile != NULL?$profile->gender=='m'?'checked':'':'')?>><?=lang('program.sex.m')?></input><br />
				<input type="radio" name="sex" value="2"<?=($profile != NULL?$profile->gender=='f'?'checked':'':'')?>><?=lang('program.sex.f')?></input>
			</div>
			<div class="form_submit">
				<input type="submit" name="submit" value="<?=lang('program.reg_submit')?>"  /></div>
	</form>
</div>
