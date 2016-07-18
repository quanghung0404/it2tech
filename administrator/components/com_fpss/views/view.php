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
	jimport('legacy.view.legacy');

	class FPSSView extends JViewLegacy
	{
	}

}
else
{
	jimport('joomla.application.component.view');

	class FPSSView extends JView
	{
	}

}
