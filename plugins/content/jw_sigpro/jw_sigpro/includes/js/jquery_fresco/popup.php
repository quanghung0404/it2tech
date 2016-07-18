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

$relName = 'fresco';
$extraClass = 'fresco';
$customLinkAttributes = 'data-fresco-group="'.$gal_id.'"';

$stylesheets = array('css/fresco/fresco.css?v=1.4.11');
$stylesheetDeclarations = array();
$scripts = array('js/fresco/fresco.js?v=1.4.11');
$scriptDeclarations = array();

if(!defined('PE_FRESCO_LOADED')){
	define('PE_FRESCO_LOADED', true);
	$legacyHeadIncludes = '<!--[if lt IE 9]><script type="text/javascript" src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script><![endif]-->';
} else {
	$legacyHeadIncludes = '';
}
