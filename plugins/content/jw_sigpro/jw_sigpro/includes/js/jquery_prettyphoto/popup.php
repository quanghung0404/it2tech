<?php
/**
 * @version     3.1.x
 * @package     Simple Image Gallery Pro
 * @author      JoomlaWorks - http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$relName = 'prettyPhoto';

$stylesheets = array('css/prettyPhoto.css?v=3.1.6');
$stylesheetDeclarations = array();
$scripts = array('js/jquery.prettyPhoto.js?v=3.1.6');

if(!defined('PE_PRETTYPHOTO_LOADED')){
	define('PE_PRETTYPHOTO_LOADED', true);
	$scriptDeclarations = array('
		jQuery.noConflict();
		jQuery(function($) {
			$("a[rel^=\'prettyPhoto\']").prettyPhoto({
				animation_speed:\'fast\',
				slideshow:5000,
				autoplay_slideshow:false,
				opacity:0.80,
				show_title:false,
				allow_resize:true,
				default_width:500,
				default_height:344,
				counter_separator_label:\'/\',
				theme:\'pp_default\',
				horizontal_padding:20,
				hideflash:false,
				wmode:\'opaque\',
				autoplay:true,
				modal:false,
				deeplinking:true,
				overlay_gallery:true,
				keyboard_shortcuts:true,
				ie6_fallback:true,
				custom_markup:\'\',
				social_tools:\'<div class="pp_social"><iframe src="//www.facebook.com/plugins/like.php?href=\'+encodeURIComponent(location.href)+\'&amp;send=false&amp;layout=standard&amp;width=320&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=35&amp;appId=551498084861981" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:320px; height:35px;" allowTransparency="true"></iframe></div>\'
			});
		});
	');
} else {
	$scriptDeclarations = array();
}
