<div id="main_post_box">
<?php echo render('_parts/post_comment', array(
	'u' => $u,
	'size' => 'M',
	'button_attrs' => array('id' => 'btn_timeline'),
	'textarea_attrs' => array('class' => 'form-control autogrow input_timeline'),
	'with_public_flag_selector' => true,
	'with_uploader' => true,
	'uploader_selects' => array(
		'label'  => term('album', 'form.choice'),
		'name'  => 'album_id',
		'value' => 0,
		'options' => array(
			'0' => sprintf('%s用%s', term('timeline'), term('album')),
		),
		'atters' => array('id' => 'album_id'),
	),
	'public_flag' => $public_flag,
	'uri_for_update_public_flag' => 'member/api/update_config/timeline_public_flag.html',
)); ?>
</div>

<div id="timeline_setting" class="text-right">
	<span class="text-muted"><?php echo term('site.display', 'site.setting'); ?>:</span>
	<?php echo render('member/_parts/timeline_viewType_selecter', array('id' => $u->id, 'timeline_viewType' => $u->timeline_viewType)); ?>
</div>

<div id="article_list"></div>
