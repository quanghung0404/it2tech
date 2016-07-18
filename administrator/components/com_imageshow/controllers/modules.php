<?php
/**
 * @version    $Id: modules.php 16647 2012-10-03 10:06:41Z giangnd $
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

class ImageShowControllerModules extends JControllerLegacy
{
	function __construct($config = array())
	{
		parent::__construct($config);
	}

	function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/modules.php';

		switch($this->getTask())
		{
			default:
				JRequest::setVar('layout', 'default');
				JRequest::setVar('view', 'modules');
				JRequest::setVar('model', 'modules');
		}
		parent::display();
	}
}
