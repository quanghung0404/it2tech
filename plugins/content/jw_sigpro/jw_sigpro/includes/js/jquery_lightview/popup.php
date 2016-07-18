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

$relName = 'lightview';
$extraClass = 'lightview';
$customLinkAttributes = 'data-lightview-group="'.$gal_id.'"';

$stylesheets = array('css/lightview/lightview.css?v=3.4.0');
$stylesheetDeclarations = array();
$scripts = array(
	'js/spinners/spinners.min.js?v=3.4.0',
	'js/lightview/lightview.js?v=3.4.0'
);
$scriptDeclarations = array();

if(!defined('PE_LIGHTVIEW_LOADED')){
	define('PE_LIGHTVIEW_LOADED', true);
	$legacyHeadIncludes = '<!--[if lt IE 9]><script type="text/javascript" src="'.$popupPath.'/js/excanvas/excanvas.js?v=3.4.0"></script><![endif]-->';
} else {
	$legacyHeadIncludes = '';
}
