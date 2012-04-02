<h1 id="page_title"><?php echo $category->title; ?></h1>
<?php if (!empty($blog)): ?>
<?php $i = 1; ?>
<?php foreach ($blog as $post): ?>
	<div  <?php if($i==1){ echo "id=first_post"; $i+=1; }; ?> class="blog_post">
		<h2> <?php echo anchor('program/view/'.$post->slug, $post->title); ?> </h2>
		<div class="post_body">
			<?php echo $post->intro; ?>
		</div>
		<p class="post-footer">
			<?php echo anchor('program/view/'.$post->slug,'Читать дальше',array('class'=>'readmore')); ?>
			<?php echo anchor('program/view/'.$post->slug,'Комментариев ( '.$post->comments_count.' )',array('class'=>'comments')); ?>
			<span class="date"> <?php echo date("m d, Y",$post->created_on); ?> </span>	
		<?php if(isset($is_program_cat)): ?>	
			<br /><?php echo anchor('program/view/'.$post->slug.'#programreg','Зарегистрировано ( '.$post->p_count.' ) Лимит ('.($post->LIMIT+0?$post->LIMIT:'не ограничен').')',array('class'=>'comments')); ?>
			<?php if($post->open && ($post->LIMIT == '0' OR $post->p_count + 0 < $post->LIMIT + 0)): ?>
			<?php echo anchor('program/view/'.$post->slug.'#programreg','Регистрация (<font color="#008000">вкл</font>)',array('class'=>'register')); ?>
			<?php else: ?>
			<?php echo anchor('program/view/'.$post->slug.'#programreg','Регистрация (<font color="#FF0000">выкл</font>)',array('class'=>'register')); ?>
			<?php endif; ?>
		<?php endif; ?>
		</p>
	</div>
<?php endforeach; ?>

<?php echo $pagination['links']; ?>

<?php else: ?>
	<p><?php echo lang('blog_currently_no_posts');?></p>
<?php endif; ?>