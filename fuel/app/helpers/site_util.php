<?php

function is_editable($member_id)
{
	if (!$member_id) return false;
	if (!Auth::check()) return false;
	return Auth::get_member_id() == $member_id;
}

function site_get_current_page_id($delimitter = '_')
{
	$items = array();
	if ($module = Site_Util::get_module_name()) $items[] = $module;
	$items[] = Site_Util::get_controller_name();
	$items[] = Site_Util::get_action_name();

	return implode($delimitter, $items);
}

function site_get_form_id($delimitter = '_')
{
	$items = array(
		'form',
		Site_Util::get_controller_name(),
		Site_Util::get_action_name(),
	);

	return implode($delimitter, $items);
}

function site_htmltag_include_js_module()
{
	$returns = array();
	if (!$module = Site_Util::get_module_name()) return '';

	$assets_uri = sprintf('site/modules/%s/site.js', $module);
	$public_uri = 'assets/js/'.$assets_uri;
	if (file_exists(DOCROOT.'/'.$public_uri)) $returns[] = Asset::js($assets_uri);

	$assets_uri = sprintf('modules/%s/site.js', $module);
	$public_uri = 'assets/js/'.$assets_uri;
	if (file_exists(DOCROOT.'/'.$public_uri)) $returns[] = Asset::js($assets_uri);

	if (!$returns) return '';

	return implode(PHP_EOL, $returns);
}

function site_htmltag_include_js_action()
{
	$returns = array();
	$module = Site_Util::get_module_name();
	$controller = Site_Util::get_controller_name();
	$action = Site_Util::get_action_name();

	$assets_uri = sprintf('site/%s%s_%s.js', $module ? sprintf('modules/%s/', $module) : '', $controller, $action);
	$public_uri = 'assets/js/'.$assets_uri;
	if (file_exists(DOCROOT.'/'.$public_uri)) $returns[] = Asset::js($assets_uri);

	if ($module)
	{
		$assets_uri = sprintf('modules/%s/%s_%s.js', $module, $controller, $action);
		$public_uri = 'assets/js/'.$assets_uri;

		if (file_exists(DOCROOT.'/'.$public_uri)) $returns[] = Asset::js($assets_uri);
	}

	if (!$returns) return '';

	return implode(PHP_EOL, $returns);
}

function site_title($title = '', $subtitle = '')
{
	$default_title = FBD_SITE_DESCRIPTION.' '.FBD_SITE_NAME;

	if (!$title && !$subtitle)
	{
		return $default_title;
	}
	if (!$subtitle) $subtitle = $default_title;
	if (!$title) $title = FBD_SITE_NAME;

	return sprintf('%s | %s', $title, $subtitle);
}

function site_header_keywords($keywords = '')
{
	if (!$keywords) return FBD_HEADER_KEYWORDS_DEFAULT;

	if (is_array($keywords))
	{
		$keywords = implode(',', $keywords);
	}
	else
	{
		$keywords = trim($keywords, ',');
	}

	return $keywords.','.FBD_HEADER_KEYWORDS_DEFAULT;
}

/**
 * Return member name for view
 * 
 * @param   Mixed   $member                Model_Member object or null
 * @param   Mixed   $link_to               false not to link / 'member' or true to link to member page / 'profile' to link to profile page
 * @param   Boolean $with_additional_info  Add result of Site_Member::get_screen_name_additional_info
 * @return  String
 * @access  public
 */
function member_name($member, $link_to = false, $with_additional_info = false)
{
	if (empty($member)) return term('member.left');

	if (!empty($member->name))
	{
		$name = $member->name;
	}
	else
	{
		$name = 'ID:'.$member->id;
	}

	// for link
	if ($link_to === true || $link_to == 'member')
	{
		$name = Html::anchor('member/'.$member->id, $name);
	}
	elseif ($link_to == 'profile')
	{
		$name = Html::anchor('member/profile/'.$member->id, $name);
	}
	elseif ($link_to)
	{
		$name = Html::anchor($link_to, $name);
	}

	if (!$with_additional_info) return $name;
	if (!$additional_info = Site_Member::get_screen_name_additional_info($member->id)) return $name;

	return $name.' '.$additional_info;
}

function member_image($member, $size = 'M', $link_to = 'member', $is_link2raw_file = false, $is_responsive = true)
{
	$link_uri = '';
	if ($member)
	{
		if ($link_to == 'member')
		{
			$link_uri = 'member/'.$member->id;
		}
		elseif ($link_to == 'profile')
		{
			$link_uri = 'member/profile/'.$member->id;
		}
		elseif ($link_to)
		{
			$link_uri = $link_to;
		}
	}

	return img($member ? $member->get_image() : 'm', $size, $link_uri, $is_link2raw_file, member_name($member), true, $is_responsive);
}

function term()
{
	$keys = func_get_args();

	if (count($keys) === 1 && is_array($keys[0])) $keys = $keys[0];
	$delimitter = count($keys) > 1 ? t('common.delimitter.words') : '';

	$return = '';
	foreach ($keys as $key)
	{
		if ($return) $return .= $delimitter;
		$return .= Config::get('term.'.$key, $key);
	}

	return $return;	
}

function t($line, $params = array(), $lang = null)
{
	$conf_file = 'term';
	if ($lang && $lang != 'ja')
	{
		$conf_file = 'term_'.$lang;
	}

	return Str::tr(Config::get($conf_file.'.'.$line, $line), $params);
}

function symbol($key)
{
	return term('symbol.'.$key);	
}

function symbol_bool($bool)
{
	return $bool ? symbol('bool.true') : symbol('bool.false');
}

function img_uri($filename = '', $size_key = '', $is_profile = false, $is_return_file_info = false)
{
	if (strlen($filename) <= conf('upload.file_category_max_length'))
	{
		$file_cate = $filename;
		$filename = '';
	}
	else
	{
		$file_cate = Site_Upload::get_file_cate_from_filename($filename);
	}

	$additional_table = '';
	if ($is_profile)
	{
		if (conf('upload.types.img.types.m.save_as_album_image') && $filename != '' && $file_cate != 'm')
		{
			$size_key = 'P_'.$size_key;
			$additional_table = 'profile';
			if (!$file_cate) $file_cate = 'ai';
		}
	}
	if (!$size = img_size($file_cate, $size_key, $additional_table)) $size = $size_key;
	$file_path = Site_Upload::get_uploaded_file_path($filename, $size, 'img', false, true);
	if ($is_return_file_info) return array($file_path, $filename, $file_cate, $size);

	return $file_path;
}

function img($filename = '', $size_key = '', $link_uri = '', $is_link2raw_file = false, $alt = '', $is_profile = false, $is_responsive = false, $anchor_attr = array(), $img_attr = array())
{
	list($uri_path, $filename, $file_cate, $size) = img_uri($filename, $size_key, $is_profile, true);

	if (!isset($img_attr['class'])) $img_attr['class'] = '';
	if ($is_responsive)
	{
		if (!empty($img_attr['class'])) $img_attr['class'] .= ' ';
		$img_attr['class'] .= 'img-responsive';
	}
	if ($is_profile)
	{
		if (!empty($img_attr['class'])) $img_attr['class'] .= ' ';
		$img_attr['class'] .= 'profile_image';
	}

	if (empty($filename))
	{
		$noimage_tag = Site_Util::get_noimage_tag($size, $file_cate, $img_attr);
		if ($link_uri) return Html::anchor($link_uri, $noimage_tag, $anchor_attr);

		return $noimage_tag;
	}
	if ($alt) $img_attr['alt'] = $alt;
	$image_tag = Html::img($uri_path, $img_attr);

	if ($link_uri) return Html::anchor($link_uri, $image_tag, $anchor_attr);

	if ($is_link2raw_file)
	{
		$anchor_attr['target'] = '_blank';
		$uri_path = Site_Upload::get_uploaded_file_path($filename, 'raw', 'img', false, true);

		return Html::anchor(Site_Util::get_media_uri($uri_path), $image_tag, $anchor_attr);
	}

	return $image_tag;
}

function img_size($file_cate, $size, $additional_table = '')
{
	if ($additional_table) return Config::get(sprintf('site.upload.types.img.types.%s.additional_sizes.%s.%s', $file_cate, $additional_table, $size));

	return Config::get(sprintf('site.upload.types.img.types.%s.sizes.%s', $file_cate, $size));
}

function site_get_time($mysql_datetime, $display_type = 'relative', $format_suffix = 'full', $display_both_term_days = 7)
{
	$accept_display_types = array('relative', 'normal', 'both');
	if (!in_array($display_type, $accept_display_types)) throw new InvalidArgumentException('Second parameter is invalid.');

	$is_disp_gmt = conf('date.isForceDispGMT', 'i18n', false);
	$format = Site_Lang::get_date_format($format_suffix);
	if ($is_disp_gmt) $format .= '_tz';

	$day_seconds = 60 * 60 * 24;
	$target_time = strtotime($mysql_datetime);
	$target_obj = Date::forge($target_time, $is_disp_gmt ? 'Europe/London' : null);
	$current_obj = Date::forge();
	$current_time = $current_obj->get_timestamp();

	if ($display_type == 'normal') return $target_obj->format($format);

	$past_time_tag = sprintf('<span data-livestamp="%s"></span>', date(DATE_ISO8601, $target_time));
	$display = '';
	if ($display_type == 'both'
		&& (is_null($display_both_term_days)
			|| !is_null($display_both_term_days) && $current_time < ($target_time + $display_both_term_days * $day_seconds)))
	{
		$display = sprintf('%s (%s)', $target_obj->format($format), $past_time_tag);
	}
	else
	{
		if ($target_time < ($current_time - $day_seconds * 1))
		{
			$display = $target_obj->format($format);
		}
		elseif ($target_time >= ($current_time - $day_seconds * 1) && $target_time < ($current_time - 60 * 60))
		{
			$past_hours = Util_toolkit::get_past_time($target_time);
			$display = t('common.about_hours_ago', array('num' => $past_hours));
		}
		else
		{
			$display = $past_time_tag;
		}
	}

	return $display;
}

function get_public_flag_label($public_flag, $view_icon_only = false, $return_type = 'array', $is_hidden_xs = false)
{
	if (!in_array($return_type, array('array', 'icon_term', 'label'))) throw new InvalidArgumentException('Second parameter is invalid.');

	$public_flag_key = 'public_flag.options.'.$public_flag;
	$icon = icon_label($public_flag_key, 'icon', $is_hidden_xs, null, 'fa fa-', 'i');
	$name = $view_icon_only ? '' : icon_label($public_flag_key, 'label', $is_hidden_xs, null, 'fa fa-', 'i');
	if ($return_type == 'icon_term') return $icon.$name;

	$color = Site_Util::get_public_flag_coloer_type($public_flag);
	if ($return_type == 'label') return html_tag('span', array('class' => 'label label-'.$color), $icon.$name);

	return array($name, $icon, 'btn-'.$color);
}

function get_csrf_query_str($delimitter = '?')
{
	return sprintf('%s%s=%s', $delimitter, Config::get('security.csrf_token_key'), Util_security::get_csrf());
}

function conv_data_atter($list = array(), $is_html = false)
{
	$output = $is_html ? '' : array();
	foreach ($list as $key => $value)
	{
		if ($is_html)
		{
			$output .= sprintf(' data-%s="%s"', $key, $value);
		}
		else
		{
			$output['data-'.$key] = $value;
		}
	}

	return $output;
}

function check_public_flag($public_flag, $access_from = '')
{
	switch ($public_flag)
	{
		case FBD_PUBLIC_FLAG_PRIVATE:
			if ($access_from == 'self') return true;
			break;
		//case FBD_PUBLIC_FLAG_FRIEND:
		//	if (in_array($access_from, array('self', 'friend'))) return true;
		//	break;
		case FBD_PUBLIC_FLAG_MEMBER:
			if (in_array($access_from, array('self', 'friend', 'member'))) return true;
			break;
		case FBD_PUBLIC_FLAG_ALL:
			return true;
			break;
	}

	return false;
}

function check_public_flags($public_flags, $access_from, $strict_cond = true)
{
	foreach ($public_flags as $public_flag)
	{
		if (!$strict_cond && check_public_flag($public_flag, $access_from)) return true;
		if ($strict_cond && !check_public_flag($public_flag, $access_from)) return false;
	}

	return $strict_cond ? true : false;
}

function check_display_type($contents_display_type, $page_display_type_str = 'detail')
{
	$page_display_type = conf('member.profile.display_type.'.$page_display_type_str, null, 0);

	return $contents_display_type >= $page_display_type;
}

function profile_value(Model_MemberProfile $member_profile)
{
	switch ($member_profile->profile->form_type)
	{
		case 'checkbox':
		case 'select':
		case 'radio':
			return $member_profile->profile_option->label;
			break;
		case 'input':
			if ($member_profile->profile->value_type == 'url')
			{
				return anchor($member_profile->value, $member_profile->value);
			}
			return $member_profile->value;
			break;
		case 'textarea':
			return convert_body($member_profile->value, array('is_truncate' => false));
			break;
	}

	return $member_profile->value;
}

function render_module($view_file_path, $data = array(), $module_name = null)
{
	if ($module_name) $view_file_path = $module_name.'::'.$view_file_path;

	return render($view_file_path, $data);
}

function check_acl($acl_path, $method = 'GET', $is_convert_acl_path = false)
{
	if ($is_convert_acl_path) $acl_path = Site_Util::get_acl_path($acl_path);

	return Auth::has_access($acl_path.'.'.$method);
}

function get_uid()
{
	if (!Auth::check()) return 0;
	list(, $member_id) = Auth::get_user_id();

	return (int)$member_id;
}

function check_uid($member_id)
{
	if (!Auth::check()) return false;
	if (!$member_id) return false;

	return $member_id == get_uid();
}

function check_original_user($user_id, $is_admin = false)
{
	return $user_id == conf(sprintf('original_user_id.%s', $is_admin ? 'admin' : 'site'));
}

function isset_datatime($datetime)
{
	if (empty($datetime)) return false;
	if ($datetime == '0000-00-00 00:00:00') return false;

	return true;
}

function check_and_get_datatime($datetime, $type = null, $default_value = '')
{
	if (!isset_datatime($datetime)) return $default_value;

	if (is_null($type)) $type = 'datetime';
	if (!in_array($type, array('date', 'datetime', 'datetime_minutes')))
	{
		throw new InvalidArgumentException('Parameter type is invalid.');
	}

	switch ($type)
	{
		case 'date':
			$length = 10;
			break;
		case 'datetime_minutes':
			$length = 16;
			break;
		case 'datetime':
		default :
			$length = 0;
			break;
	}
	if (!$length) return $datetime;

	return substr($datetime, 0, $length);
}

function label_is_secure($value, $view_icon_only = false, $attrs = array())
{
	list($name, $icon_tag, $type) = Site_Util::get_is_secure_label_parts($value);
	$label_name = $view_icon_only ? $icon_tag : $icon_tag.' '.$name;

	if ($view_icon_only)
	{
		$attrs['data-toggle']    = 'tooltip';
		$attrs['data-placement'] = 'top';
		$attrs['title']          = $name;
	}

	return label($label_name, $type, $attrs);
}

function is_enabled_map($action = null, $module = null)
{
	if (!$action) return conf('map.isEnabled');
	$action_key = str_replace('/', '.', $action);

	return conf('map.isEnabled') && conf('display_setting.'.$action_key.'.displayMap', $module, false);
}

function check_current_uris($check_uris, $is_internal_uri = false)
{
	return in_array(current_uri($is_internal_uri), Util_Array::trim_values($check_uris, '/'));
}

function check_current_uri($check_uri, $is_internal_uri = false)
{
	return current_uri($is_internal_uri) == trim($check_uri, '/');
}

function current_uri($is_internal_uri = false)
{
	if ($is_internal_uri) return trim(Site_Util::get_action_path(), '/');

	return trim(Uri::string(), '/');
}

function get_member_lang($member_id)
{
	return Site_Member::get_lang_setting($member_id);
}

function conv_honorific_name($name, $lang = null)
{
	if (! $lang) $lang = get_default_lang();
	if ($lang != 'ja') return $name;

	return $name.'さん';
}

