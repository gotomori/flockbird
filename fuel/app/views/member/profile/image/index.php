<?php echo render('_parts/member_profile', array(
	'is_mypage' => $is_mypage,
	'member' => $member,
	'member_profiles' => $member_profiles,
	'access_from' => $access_from,
	'display_type' => 'summery',
	'with_image_upload_form' => true,
)); ?>

<?php if (Module::loaded('album') && Config::get('site.upload.types.img.types.m.save_as_album_image')): ?>
<?php echo render('album::image/_parts/list', array('list' => $images, 'is_simple_view' => true, 'is_setting_profile_image' => true)); ?>
<?php endif; ?>