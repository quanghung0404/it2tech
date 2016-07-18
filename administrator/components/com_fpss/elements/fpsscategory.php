<?php
/**
 * @version		$Id: fpsscategory.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_ADMINISTRATOR.'/components/com_fpss/elements/base.php');

class FPSSElementFPSSCategory extends FPSSElement
{

	function fetchElement($name, $value, &$node, $control_name)
	{

		FPSSModel::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fpss'.DS.'models');
		$model = FPSSModel::getInstance('categories', 'FPSSModel');
		$model->setState('published', -1);
		$model->setState('limit', 0);
		$model->setState('limitstart', 0);
		$model->setState('ordering', 'name');
		$model->setState('orderingDir', 'ASC');
		$categories = $model->getData();
		$attributes = '';
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			if ((string)$node->attributes()->multiple)
			{
				$attributes .= ' multiple="multiple" style="width:99%;" size="6"';
			}
			$fieldName = $name;
		}
		else
		{
			$fieldName = $name.'[]';
			if ($node->attributes('multiple'))
			{
				$attributes .= ' multiple="multiple" style="width:99%;" size="6"';
				$fieldName = $control_name.'['.$name.'][]';
			}

		}
		return JHTML::_('select.genericlist', $categories, $fieldName, $attributes, 'id', 'name', $value);
	}

}

class JFormFieldFPSSCategory extends FPSSElementFPSSCategory
{
	var $type = 'fpsscategory';
}

class JElementFPSSCategory extends FPSSElementFPSSCategory
{
	var $_name = 'fpsscategory';
}
