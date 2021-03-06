<?php if ($is_mypage): ?>
<ul class="nav nav-pills">
	<li<?php if (!$is_draft): ?> class="disabled"<?php endif; ?>><?php echo Html::anchor(
		$is_draft ? 'note/member' : '#',
		term('form.published', 'note.plural'),
		$is_draft ? array() : array('onclick' => 'return false;')
	); ?></li>
	<li<?php if ($is_draft): ?> class="disabled"<?php endif; ?>><?php echo Html::anchor(
		$is_draft ? '#' : 'note/member?is_draft=1',
		term('form.draft'),
		$is_draft ? array('onclick' => 'return false;') : array()
	); ?></li>
</ul>
<?php endif; ?>
<?php echo render('_parts/list', array(
	'list' => $list,
	'page' => $page,
	'next_page' => $next_page,
	'member' => $member,
	'is_draft' => $is_draft,
	'liked_note_ids' => $liked_note_ids,
)); ?>
