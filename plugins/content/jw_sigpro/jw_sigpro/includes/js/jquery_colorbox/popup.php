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

$relName = 'colorbox';
$extraClass = 'sigProColorbox';

$stylesheets = array('example1/colorbox.css?v=1.5.10');
$stylesheetDeclarations = array();
$scripts = array('jquery.colorbox-min.js?v=1.5.10');

if(!defined('PE_COLORBOX_LOADED')){
	define('PE_COLORBOX_LOADED', true);
	$scriptDeclarations = array('
		jQuery.noConflict();
		jQuery(document).ready(function(){
			jQuery(".sigProColorbox").colorbox({
				current: "Image {current} of {total}",
				//previous: "previous", // Text or HTML for the previous button while viewing a group
				//next: "next", // Text or HTML for the next button while viewing a group
				//close: "close", // Text or HTML for the close button - the \'esc\' key will also close Colorbox
				maxWidth: "90%",
				maxHeight: "90%",
				slideshow: true, // If true, adds an automatic slideshow to a content group / gallery
				slideshowSpeed: 2500, // Sets the speed of the slideshow, in milliseconds
				slideshowAuto: false, // If true, the slideshow will automatically start to play
				slideshowStart: "start slideshow", // Text for the slideshow start button
				slideshowStop: "stop slideshow" // Text for the slideshow stop button
			});
		});
	');
} else {
	$scriptDeclarations = array();
}
