<?php
/**
 * @version		$Id: header.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_ADMINISTRATOR.'/components/com_fpss/elements/base.php');

class FPSSElementHeader extends FPSSElement
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		// Output
		return '
		<div style="font-weight:normal;font-size:12px;color:#fff;padding:4px;margin:0;background:#0B55C4;clear:both;">
			'.JText::_($value).'
		</div>
		';
	}

	function fetchTooltip($label, $description, &$node, $control_name, $name)
	{
		return;
	}

}

class JFormFieldHeader extends FPSSElementHeader
{
	var $type = 'header';
}

class JElementHeader extends FPSSElementHeader
{
	var $_name = 'header';
}
