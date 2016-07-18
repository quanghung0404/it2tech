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

$relName = 'magnificpopup';
$extraClass = 'magnificpopup';

$stylesheets = array('magnific-popup.css?v=0.9.9');
$stylesheetDeclarations = array();
$scripts = array('jquery.magnific-popup.min.js?v=0.9.9');

if(!defined('PE_MAGNIFICPOPUP_LOADED')){
	define('PE_MAGNIFICPOPUP_LOADED', true);
	$scriptDeclarations = array('
		jQuery.noConflict();
		jQuery(function($) {
			$(\'.sigProContainer\').each(function() {
		        $(this).find(\'a.magnificpopup\').magnificPopup({
		          type: \'image\',
		          tLoading: \'Loading image #%curr%...\',
		          gallery: {
		            enabled: true,
		            navigateByImgClick: true,
		            preload: [0,1] // Will preload 0 - before current, and 1 after the current image
		          },
		          image: {
		            tError: \'<a href="%url%">The image #%curr%</a> could not be loaded.\',
		            titleSrc: function(item) {
		              return item.el.attr(\'title\');
		            }
		          }
		        });
			});
		});
	');
} else {
	$scriptDeclarations = array();
}
