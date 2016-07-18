<?php
/**
 * @version		$Id: template.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_ADMINISTRATOR.'/components/com_fpss/elements/base.php');

class FPSSElementTemplate extends FPSSElement
{

	function fetchElement($name, $value, &$node, $control_name)
	{

		jimport('joomla.filesystem.folder');
		$mainframe = JFactory::getApplication();
		$fieldName = (version_compare(JVERSION, '1.6.0', 'ge')) ? $name : $control_name.'['.$name.']';

		$moduleTemplatesPath = JPATH_SITE.DS.'modules'.DS.'mod_fpss'.DS.'tmpl';
		$moduleTemplatesFolders = JFolder::folders($moduleTemplatesPath);

		$db = JFactory::getDBO();
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
		}
		else
		{
			$query = "SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0";
		}

		$db->setQuery($query);
		$template = $db->loadResult();
		$templatePath = JPATH_SITE.DS.'templates'.DS.$template.DS.'html'.DS.'mod_fpss';

		if (JFolder::exists($templatePath))
		{
			$templateFolders = JFolder::folders($templatePath);
			$folders = @array_merge($templateFolders, $moduleTemplatesFolders);
			$folders = @array_unique($folders);
		}
		else
		{
			$folders = $moduleTemplatesFolders;
		}

		sort($folders);

		$options = array();
		foreach ($folders as $folder)
		{
			$options[] = JHTML::_('select.option', $folder, $folder);
		}

		return JHTML::_('select.genericlist', $options, $fieldName, 'class="inputbox"', 'value', 'text', $value);
	}

}

class JFormFieldTemplate extends FPSSElementTemplate
{
	var $type = 'template';
}

class JElementTemplate extends FPSSElementTemplate
{
	var $_name = 'template';
}
