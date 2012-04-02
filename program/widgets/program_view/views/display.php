 <div class="menu">
 <span>Каталог программ</span>
<?php if(is_array($categories)): ?>
<ul>
	<?php foreach($categories as $category): ?>
	<li>
		<?php echo anchor("program/view/{$category->slug}", '<strong>'.$category->title.'</strong/>'.$category->intro); ?>
	</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
</div>
