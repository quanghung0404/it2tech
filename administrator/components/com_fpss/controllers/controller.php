<?php
/**
 * @version		$Id$
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die ;

if (version_compare(JVERSION, '3.0', 'ge'))
{
	jimport('legacy.controller.legacy');

	class FPSSController extends JControllerLegacy
	{
		public function display($cachable = false, $urlparams = array())
		{
			parent::display($cachable, $urlparams);
		}

	}

}
else if (version_compare(JVERSION, '2.5', 'ge'))
{
	jimport('joomla.application.component.controller');

	class FPSSController extends JController
	{
		public function display($cachable = false, $urlparams = false)
		{
			parent::display($cachable, $urlparams);
		}

	}

}
else
{
	jimport('joomla.application.component.controller');

	class FPSSController extends JController
	{
		public function display($cachable = false)
		{
			parent::display($cachable);
		}

	}

}
