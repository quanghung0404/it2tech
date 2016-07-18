<?php
/**
 * @version		$Id: popup.php 2829 2013-04-12 14:20:40Z joomlaworks $
 * @package		Simple Image Gallery Pro
 * @author		JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$relName = 'yoxview';
$extraWrapperClass = 'yoxview';

$stylesheets = array('yoxview.css');
$stylesheetDeclarations = array();
$scripts = array('jquery.yoxview-2.21.min.js');

if(!defined('PE_YOXVIEW_LOADED')){
	define('PE_YOXVIEW_LOADED', true);
	$scriptDeclarations = array('
		jQuery.noConflict();
		jQuery(function($) {
			$(".yoxview").yoxview();
		});
	');
} else {
	$scriptDeclarations = array();
}
