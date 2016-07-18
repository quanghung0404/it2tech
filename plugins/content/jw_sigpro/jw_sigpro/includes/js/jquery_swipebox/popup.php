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

$relName = 'swipebox';
$extraClass = 'swipebox';

$stylesheets = array('src/css/swipebox.min.css?v=1.4.1');
$stylesheetDeclarations = array();
$scripts = array(
	'lib/ios-orientationchange-fix.js?v=1.4.1',
	'src/js/jquery.swipebox.min.js?v=1.4.1'
);

if(!defined('PE_SWIPEBOX_LOADED')){
	define('PE_SWIPEBOX_LOADED', true);
	$scriptDeclarations = array('
		jQuery.noConflict();
		jQuery(function($) {
			$("a.swipebox").swipebox({hideBarsDelay: 0});
		});
	');
} else {
	$scriptDeclarations = array();
}
