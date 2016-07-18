<?php
/**
 * @version		$Id: language.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_ADMINISTRATOR.'/components/com_fpss/elements/base.php');

class FPSSElementLanguage extends FPSSElement
{

	function fetchElement($name, $value, &$node, $control_name)
	{
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$extension = (string)$node->attributes()->extension;
		}
		else
		{
			$extension = $node->attributes('extension');
		}
		$language = JFactory::getLanguage();
		$language->load($extension, JPATH_SITE);
		$language->load($extension, JPATH_ADMINISTRATOR);
	}

	function fetchTooltip($label, $description, &$node, $control_name, $name)
	{
		return;
	}

}

class JFormFieldLanguage extends FPSSElementLanguage
{
	var $type = 'language';
}

class JElementLanguage extends FPSSElementLanguage
{
	var $_name = 'language';
}
