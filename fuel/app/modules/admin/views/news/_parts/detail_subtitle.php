<ul class="list-inline mt10">
	<li><small><label><?php echo term(array('site.last', 'form.updated', 'site.datetime')); ?>:</label> <?php echo site_get_time($news->updated_at) ?></small></li>
	<?php if ($news->published_at): ?><li><small><label><?php echo term(array('form.publish', 'site.datetime')); ?>:</label> <?php echo site_get_time($news->published_at) ?></small></li><?php endif; ?>
</ul>

<?php
$publish_action = $news->is_published ? 'unpublish' : 'publish';
$menus = array(
	array('icon_term' => 'form.do_edit', 'href' => 'admin/news/edit/'.$news->id),
	array('icon_term' => 'form.do_'.$publish_action, 'attr' => array(
		'class' => 'js-simplePost',
		'data-uri' => sprintf('admin/news/%s/%d', $publish_action, $news->id),
	)),
	array('icon_term' => 'form.do_delete', 'attr' => array(
		'class' => 'js-simplePost',
		'data-uri' => 'admin/news/delete/'.$news->id,
	)),
);
echo btn_dropdown('edit', $menus, true, null, null, true, array('class' => 'edit'));
?>