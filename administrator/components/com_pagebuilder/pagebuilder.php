<?php
/**
 * @version    $Id$
 * @package    JSN_PageBuilder
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file.
defined('_JEXEC') || die('Restricted access');

define('JSN_PAGEBUILDER_DEPENDENCY', '[{"type":"plugin","folder":"system","name":"jsnframework","identified_name":"ext_framework","site":"site","publish":"1","lock":"1","title":"JSN Extension Framework System Plugin"},{"type":"plugin","folder":"system","dir":"plugins\/system\/pagebuilder","name":"pagebuilder","client":"site","publish":"1","title":"pagebuilder","params":"{\"pagebuilder\":\"pagebuilder\",\"shortcodes\":\"pb_accordion:accordion|pb_accordion_item:element|pb_alert:alert|pb_articlelist:articlelist|pb_audio:audio|pb_button:button|pb_buttonbar:buttonbar|pb_buttonbar_item:element|pb_carousel:carousel|pb_carousel_item:element|pb_divider:divider|pb_easyslider:easyslider|pb_googlemap:googlemap|pb_googlemap_item:element|pb_heading:heading|pb_image:image|pb_imageshow:imageshow|pb_list_item:element|pb_list:list|pb_market_item:element|pb_market:market|pb_module:module|pb_pricingtable_item:element|pb_pricingtable:pricingtable|pb_progressbar_item:element|pb_progressbar:progressbar|pb_progresscircle:progresscircle|pb_promobox:promobox|pb_qrcode:qrcode|pb_socialicon_item:element|pb_socialicon:socialicon|pb_tab_item:element|pb_tab:tab|pb_table_item:element|pb_table:table|pb_testimonial_item:element|pb_testimonial:testimonial|pb_text:text|pb_tooltip:tooltip|pb_uniform:uniform|pb_video:video|pb_weather:weather|pb_helper_item:helpers|pb_html_item:helpers|pb_articles_item:models|pb_authors_item:models|pb_categories_item:models|pb_easyblogarticles_item:models|pb_easyblogcategories_item:models|pb_k2articles_item:models|pb_k2categories_item:models|pb_validate_file_item:helpers|pb_pricingtable_item_item:element|pb_pricingtableattr_item:pricingtableattr|pb_column:column|pb_row:row\"}"},{"type":"plugin","folder":"editors-xtd","dir":"plugins\/editors-xtd\/pagebuilder","name":"pagebuilder","client":"site","publish":"1","title":"pagebuilder","params":"{\"pagebuilder\":\"pagebuilder\"}"},{"type":"plugin","folder":"jsnpagebuilder","dir":"plugins\/jsnpagebuilder\/defaultelements","name":"defaultelements","client":"site","publish":"1","lock":"1","title":"defaultelements","params":"{\"pagebuilder\":\"pagebuilder\"}"},{"type":"plugin","folder":"search","dir":"plugins\/search\/jsnpagebuildersearch","name":"jsnpagebuildersearch","client":"site","publish":"1","lock":"1","title":"jsnpagebuildersearch","params":"{\"pagebuilder\":\"pagebuilder\"}"},{"type":"plugin","folder":"search","dir":"plugins\/search\/jsnpagebuilderk2search","name":"jsnpagebuilderk2search","client":"site","publish":"1","lock":"1","title":"jsnpagebuilderk2search","params":"{\"pagebuilder\":\"pagebuilder\"}"}]');

// Get application object
$app = JFactory::getApplication();

// Get input object
$input = $app->input;

// Access check
if ( ! JFactory::getUser()->authorise('core.manage', JRequest::getCmd('option', 'com_pagebuilder')))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
require_once dirname(__FILE__) . '/defines.pagebuilder.php';
// Import JoomlaShine base MVC classes
require_once dirname(__FILE__) . '/libraries/joomlashine/base/model.php';
require_once dirname(__FILE__) . '/libraries/joomlashine/base/view.php';
require_once dirname(__FILE__) . '/libraries/joomlashine/base/controller.php';  

// Initialize common assets
require_once JPATH_COMPONENT_ADMINISTRATOR . '/bootstrap.php';

// Check if all dependency is installed
require_once JPATH_COMPONENT_ADMINISTRATOR . '/dependency.php';

// Require base shorcode element
// TODO: under included files will be packed in a loader class
require_once dirname(__FILE__) . '/libraries/innotheme/shortcode/element.php';
require_once dirname(__FILE__) . '/libraries/innotheme/shortcode/parent.php';
require_once dirname(__FILE__) . '/libraries/innotheme/shortcode/child.php';

// Check if JoomlaShine extension framework is exists?
if ( $framework->extension_id ) {
	// Autoload all helper classes.
	JSN_Loader::register(dirname(__FILE__) , 'JSNPagebuilder');
	
	// Autoload all shortcode
	JSN_Loader::register(dirname(__FILE__) . '/helpers/shortcode' , 'JSNPBShortcode');
	//JSN_Loader::register(JPATH_ROOT . '/plugins/pagebuilder/' , 'JSNPBShortcode');
	//JSN_Loader::register(JPATH_ROOT . '/administrator/components/com_pagebuilder/elements/' , 'JSNPBShortcode');
	JSN_Loader::register(JPATH_ROOT . '/plugins/jsnpagebuilder/defaultelements/' , 'JSNPBShortcode');
	// Store all PageBuilder's shortcode into an object.
	global $JSNPbElements;
	$JSNPbElements		= new JSNPagebuilderHelpersElements();
}

if (strpos('installer + update + upgrade', $input->getCmd('view')) !== false OR JSNVersion::isJoomlaCompatible(JSN_PAGEBUILDER_REQUIRED_JOOMLA_VER))
{
	// Get the appropriate controller
	$controller = JSNBaseController::getInstance('JSNPagebuilder');

	// Perform the request task
	$controller->execute($input->getCmd('task'));

	// Redirect if set by the controller
	$controller->redirect();
}
