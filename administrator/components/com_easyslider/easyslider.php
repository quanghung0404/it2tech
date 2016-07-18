<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get application object
$app = JFactory::getApplication();

// Get input object
$input = $app->input;

// Access check
if ( ! JFactory::getUser()->authorise('core.manage', $input->getCmd('option')))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Initialize common assets
require_once JPATH_COMPONENT_ADMINISTRATOR . '/bootstrap.php';

// Check if all dependency is installed
require_once JPATH_COMPONENT_ADMINISTRATOR . '/dependency.php';

//load Utils class
//require_once JPATH_ROOT . '/administrator/components/com_easyslider/classes/jsn.easyslider.utils.php';

// Register helper class
JLoader::register('JSNEasySliderHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/easyslider.php');

if (strpos('installer + update + upgrade', $input->getCmd('view')) !== false OR JSNVersion::isJoomlaCompatible(JSN_EASYSLIDER_REQUIRED_JOOMLA_VER))
{
	// Get the appropriate controller
	$controller = JSNBaseController::getInstance('JSNEasySlider');

	// Perform the request task
	$controller->execute($input->getCmd('task'));

	// Redirect if set by the controller
	$controller->redirect();
}
