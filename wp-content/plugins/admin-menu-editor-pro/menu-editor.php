<?php
/*
Plugin Name: Admin Menu Editor Pro
Plugin URI: http://adminmenueditor.com/
Description: به شما امکان می دهد مستقیماً منوی مدیریت وردپرس را ویرایش کنید. می توانید منوهای موجود را مجدداً سفارش دهید، پنهان یا تغییر نام دهید، منوهای سفارشی و موارد دیگر را اضافه کنید.
Version: 2.18
Author: نوین وردپرس
Author URI: wpnovin.com
Slug: admin-menu-editor-pro
*/


require 'novin-update/wpnovin-atuo-update.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'http://dl3.wpnovin.com/Source/Plugins/admin-menu-editor-pro/SD54R5sd.json',
	__FILE__,
	'admin-menu-editor-pro'
);


if ( include(dirname(__FILE__) . '/includes/version-conflict-check.php') ) {
	return;
}

//Load the plugin
require_once dirname(__FILE__) . '/includes/basic-dependencies.php';
global $wp_menu_editor;
$wp_menu_editor = new WPMenuEditor(__FILE__, 'ws_menu_editor_pro');

//Load Pro version extras
$ws_me_extras_file = dirname(__FILE__).'/extras.php';
if ( file_exists($ws_me_extras_file) ){
	include $ws_me_extras_file;
}

if ( defined('AME_TEST_MODE') ) {
	require dirname(__FILE__) . '/tests/helpers.php';
	ameTestUtilities::init();
}
