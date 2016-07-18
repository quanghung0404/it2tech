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

define('JSN_PAGEBUILDER_DEPENDENCY', '[{"type":"plugin","folder":"system","name":"jsnframework","identified_name":"ext_framework","site":"site","publish":"1","lock":"1","title":"JSN Extension Framework System Plugin"},{"type":"plugin","folder":"system","dir":"plugins\/system\/pagebuilder","name":"pagebuilder","client":"site","publish":"1","title":"pagebuilder","params":null},{"type":"plugin","folder":"editors-xtd","dir":"plugins\/editors-xtd\/pagebuilder","name":"pagebuilder","client":"site","publish":"1","title":"pagebuilder","params":null},{"type":"plugin","folder":"jsnpagebuilder","dir":"plugins\/jsnpagebuilder\/defaultelements","name":"defaultelements","client":"site","publish":"1","lock":"1","title":"defaultelements","params":null},{"type":"plugin","folder":"search","dir":"plugins\/search\/jsnpagebuildersearch","name":"jsnpagebuildersearch","client":"site","publish":"1","lock":"1","title":"jsnpagebuildersearch","params":null},{"type":"plugin","folder":"search","dir":"plugins\/search\/jsnpagebuilderk2search","name":"jsnpagebuilderk2search","client":"site","publish":"1","lock":"1","title":"jsnpagebuilderk2search","params":null}]');

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
