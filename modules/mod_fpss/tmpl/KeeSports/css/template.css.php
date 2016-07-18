<?php
if(!isset($fpssTemplateIncluded)){
	header("Content-type: text/css; charset: utf-8");
	$width = (int) $_GET['width'];
	$height = (int) $_GET['height'];
	$sidebarWidth = (int) $_GET['sidebarWidth'];
	$thumbnailViewportWidth = (int) $_GET['thumbnailViewportWidth'];
	$thumbnailViewportHeight = (int) $_GET['thumbnailViewportHeight'];
	$timer = (bool) $_GET['timer'];
	$mid = (int) $_GET['mid'];
}
?>
/**
 * @version		$Id: template.css.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author    JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

/* --- Slideshow Container --- */
#fpssContainer<?php echo $mid; ?>.fpss-template-keesports {position:relative;width:<?php echo $width; ?>px;margin:0;padding:0;border-color:#ccc;overflow:hidden;}

/* --- Loader --- */
#fpssContainer<?php echo $mid; ?>.fpss-template-keesports .slide-loading {position:absolute;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;background:#000 url(../images/loading.gif) no-repeat center center;z-index:100;}

/* --- Timer (progress bar) --- */
<?php if($timer): ?>
#fpssContainer<?php echo $mid; ?>.fpss-template-keesports .fpssTimerContainer {position:absolute;bottom:8px;right:8px;width:30px;z-index:99;background:#000;opacity:0.7;}
#fpssContainer<?php echo $mid; ?>.fpss-template-keesports .fpssTimerContainer .fpssTimer {width:0;clear:both;height:3px;background-color:#c00;}
<?php else: ?>
#fpssContainer<?php echo $mid; ?>.fpss-template-keesports .fpssTimerContainer,
#fpssContainer<?php echo $mid; ?>.fpss-template-keesports .fpssTimerContainer .fpssTimer {display:none;}
<?php endif; ?>

/* --- Slide Containers --- */
#fpssContainer<?php echo $mid; ?>.fpss-template-keesports .slides-wrapper {width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;margin:0;padding:0;position:relative;overflow:hidden;background:#000;}
.fpss-template-keesports .slides {list-style:none;}
#fpssContainer<?php echo $mid; ?>.fpss-template-keesports .slide {width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;overflow:hidden;list-style:none;}
#fpssContainer<?php echo $mid; ?>.fpss-template-keesports .slidetext {position:absolute;bottom:10px;left:10px;width:70%;margin:0;padding:15px 20px;background:url(../images/transparent_bg_70.png);z-index:1;border-left:2px solid #00b2ef;}

/* --- Slide Content --- */
.fpss-template-keesports .slidetext h2,
.fpss-template-keesports .slidetext h2 a {font-family:"Trebuchet MS", Trebuchet, Arial, Verdana, sans-serif;font-size:26px;line-height:120%;margin:0;padding:0;color:#fff;}
.fpss-template-keesports .slidetext h2 a:hover {color:#00b2ef;text-decoration:none;}
.fpss-template-keesports .slidetext h3 {font-size:18px;margin:0;padding:4px 0;font-weight:normal;color:#eee;}
.fpss-template-keesports .slidetext h4 {font-size:11px;margin:0;padding:2px 0;font-weight:normal;color:#ccc;}
.fpss-template-keesports .slidetext p {margin:4px 0;padding:0;color:#fff;}
.fpss-template-keesports .slidetext a.fpssReadMore {font-family:"Trebuchet MS", Trebuchet, Arial, Verdana, sans-serif;display:block;margin:0;padding:1px 0;background:none;border:none;color:#fff;line-height:14px;}
.fpss-template-keesports .slidetext a.fpssReadMore:hover {margin:0;padding:1px 0;background:none;border:none;color:#f90;line-height:14px;}

/* --- Navigation --- */
.fpss-template-keesports .navigation-wrapper {padding:0;margin:0;position:absolute;top:40px;right:0;z-index:97;}
.fpss-template-keesports .navigation-wrapper .navigation {padding:0;margin:0;list-style:none;}
.fpss-template-keesports .navigation-wrapper .navigation li {list-style-type:none;display:block;margin:0 0 2px;padding:0;background:none;}
.fpss-template-keesports .navigation li.navigation-button a {display:block;width:28px;height:28px;color:#fff;text-align:center;background:#000;position:relative;padding:0;margin:0;}
.fpss-template-keesports .navigation li.navigation-button a:hover {text-decoration:none;background:#61646d;}
.fpss-template-keesports .navigation li.active a {text-decoration:none;background:#00b2ef;}
.fpss-template-keesports .navigation li a span.navigation-key {display:block;padding:8px 0 0 0;font-size:12px;line-height:normal;vertical-align:middle;}
#fpssContainer<?php echo $mid; ?>.fpss-template-keesports .navigation li a span.navigation-preview {display:none;position:absolute;right:40px;top:0;background:#000;padding:10px;text-align:left;width:<?php echo $thumbnailViewportWidth; ?>px;}
#fpssContainer<?php echo $mid; ?>.fpss-template-keesports .navigation li a:hover span.navigation-preview {display:block;}
.fpss-template-keesports .navigation li a span.navigation-preview span.navigation-thumbnail {}
#fpssContainer<?php echo $mid; ?>.fpss-template-keesports .navigation li a span.navigation-preview span.navigation-thumbnail img {width:<?php echo $thumbnailViewportWidth; ?>px;height:auto;display:block;margin:0 auto 4px;}
.fpss-template-keesports .navigation li a span.navigation-preview span.navigation-title {color:#eee;font-size:12px;font-weight:bold;}
.fpss-template-keesports .navigation li a span.navigation-preview span.navigation-tagline {color:#fff;font-size:11px;font-weight:normal;display:block;}
.fpss-template-keesports .navigation li a span.navigation-arrow {display:none;position:absolute;top:4px;right:32px;width:8px;height:15px;background:url(../images/arrow.png) no-repeat 0 0;}
.fpss-template-keesports .navigation li a:hover span.navigation-arrow {display:block;}

/* --- Generic Styling (highly recommended) --- */
.fpss-template-keesports a {cursor:pointer;}
.fpss-template-keesports a:active,
.fpss-template-keesports a:focus {outline:0;outline:expression(hideFocus='true');}
.fpss-template-keesports img {border:none;}
.fpss-template-keesports .slidetext img,
.fpss-template-keesports .slidetext p img {display:none;}
.fpss-clr {clear:both;float:none;height:0;line-height:0;margin:0;padding:0;border:0;}

/* --- IE Specific Styling (use body.fpssIsIE6, body.fpssIsIE7, body.fpssIsIE8 to target specific IEs) --- */
body.fpssIsIE6 .fpss-clr,
body.fpssIsIE7 .fpss-clr {display:none;}

/* --- End of stylesheet --- */
