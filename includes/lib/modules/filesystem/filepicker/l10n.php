<?php
/*
Program Name: File Picker
Program URI: http://code.google.com/p/file-picker/
Description: Display and choose files from your website.

Copyright (c) 2008 Hpyer (hpyer[at]yahoo.cn)
Dual licensed under the MIT (MIT-LICENSE.txt)
and GPL (GPL-LICENSE.txt) licenses.
*/

define('L10N_CLASS_ROOT', dirname(__FILE__) . '/classes/gettext');
require_once(L10N_CLASS_ROOT . '/streams.php');
require_once(L10N_CLASS_ROOT . '/gettext.php');

function load_textdomain($path = './languages', $domain = 'main'){
	global $l10n;

	$lang = get_lang();
	$mofile = '';
	$mofile .= $path;
	if (isset($domain) && $domain != 'main'){
		$mofile .= '/' . $domain;
	}
	$mofile .= '/' . $lang . '.mo';
	if (is_readable($mofile)){
		$input = new CachedFileReader($mofile);
	} else {
		return ;
	}

	$l10n[$domain] = new gettext_reader($input);
}

function get_lang(){
	global $lang;

	if (isset($lang)){
		return $lang;
	}
	if (empty($lang)){
		$lang = 'en';
	}
	return $lang;
}

function __($text, $domain = 'main'){
	global $l10n;
	if (isset($l10n[$domain])){
		return $l10n[$domain]->translate($text);
	} else {
		return $text;
	}
}

function _e($text, $domain = 'main'){
	echo __($text, $domain);
}

?>