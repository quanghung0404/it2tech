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
	jimport('legacy.model.legacy');

	class FPSSModel extends JModelLegacy
	{
		public static function addIncludePath($path = '', $prefix = '')
		{
			return parent::addIncludePath($path, $prefix);
		}

	}

}
else if (version_compare(JVERSION, '2.5', 'ge'))
{
	jimport('joomla.application.component.model');

	class FPSSModel extends JModel
	{
		public static function addIncludePath($path = '', $prefix = '')
		{
			return parent::addIncludePath($path, $prefix);
		}

	}

}
else
{
	jimport('joomla.application.component.model');

	class FPSSModel extends JModel
	{
		public function addIncludePath($path = '', $prefix = '')
		{
			return parent::addIncludePath($path);
		}

	}

}
