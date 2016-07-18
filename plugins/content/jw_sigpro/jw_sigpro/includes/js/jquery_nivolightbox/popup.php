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

$relName = 'nivoLightbox';
$customLinkAttributes = 'data-lightbox-gallery="nvlb'.$gal_id.'"';

$stylesheets = array(
	'nivo-lightbox.css?v=1.2.0',
	'themes/default/default.css?v=1.2.0'
);
$stylesheetDeclarations = array();
$scripts = array('nivo-lightbox.min.js?v=1.2.0');

if(!defined('PE_NIVOLIGHTBOX_LOADED')){
	define('PE_NIVOLIGHTBOX_LOADED', true);
	$scriptDeclarations = array('
		jQuery.noConflict();
		jQuery(function($) {
			$("a[rel^=\'nivoLightbox\']").nivoLightbox({
				effect: \'fade\',                           // The effect to use when showing the lightbox. Options are: fade, fadeScale, slideLeft, slideRight, slideUp, slideDown, fall
				theme: \'default\',                         // The lightbox theme to use
				keyboardNav: true,                          // Enable/Disable keyboard navigation (left/right/escape)
				clickOverlayToClose: true,                  // If false clicking the "close" button will be the only way to close the lightbox
				onInit: function(){},                       // Callback when lightbox has loaded
				beforeShowLightbox: function(){},           // Callback before the lightbox is shown
				afterShowLightbox: function(lightbox){},    // Callback after the lightbox is shown
				beforeHideLightbox: function(){},           // Callback before the lightbox is hidden
				afterHideLightbox: function(){},            // Callback after the lightbox is hidden
				onPrev: function(element){},                // Callback when the lightbox gallery goes to previous item
				onNext: function(element){},                // Callback when the lightbox gallery goes to next item
				errorMessage: \'The requested content cannot be loaded. Please try again later.\' // Error message when content can\'t be loaded
			});
		});
	');
} else {
	$scriptDeclarations = array();
}
