<?php
/**
 * @version    $Id: update.php 16551 2012-10-01 03:44:56Z haonv $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
class ImageShowControllerUpdate extends JSNUpdateController
{
	function __construct($config = array())
	{
		parent::__construct($config);
	}

	function display($cachable = false, $urlparams = false)
	{
		//JRequest::setVar('hidemainmenu', 1);
		$layout = JRequest::getString('layout', 'default');
		JRequest::setVar('layout', $layout);
		JRequest::setVar('view', 'update');
		parent::display();
	}
}
