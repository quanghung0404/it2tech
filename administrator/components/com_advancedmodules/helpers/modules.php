<?php
/**
 * @package         Advanced Module Manager
 * @version         5.3.6PRO-revPRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Modules component helper.
 */
abstract class ModulesHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string $vName The name of the active view.
	 *
	 * @return  void
	 */
	public static function addSubmenu($vName)
	{
		// Not used in this component.
	}

	/**
	 * Get a list of filter options for the state of a module.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 */
	public static function getStateOptions()
	{
		// Build the filter options.
		$options   = array();
		$options[] = JHtml::_('select.option', '1', JText::_('JPUBLISHED'));
		$options[] = JHtml::_('select.option', '0', JText::_('JUNPUBLISHED'));
		$options[] = JHtml::_('select.option', '-2', JText::_('JTRASHED'));
		$options[] = JHtml::_('select.option', '*', JText::_('JALL'));

		return $options;
	}

	/**
	 * Get a list of filter options for the application clients.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 */
	public static function getClientOptions()
	{
		// Build the filter options.
		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('JSITE'));
		$options[] = JHtml::_('select.option', '1', JText::_('JADMINISTRATOR'));

		return $options;
	}

	/**
	 * Get a list of modules positions
	 *
	 * @param   integer $clientId      Client ID
	 * @param   boolean $editPositions Allow to edit the positions
	 *
	 * @return  array  A list of positions
	 */
	public static function getPositions($clientId, $editPositions = false)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('DISTINCT(position)')
			->from('#__modules')
			->where($db->quoteName('client_id') . ' = ' . (int) $clientId)
			->order('position');

		$db->setQuery($query);

		try
		{
			$positions = $db->loadColumn();
			$positions = is_array($positions) ? $positions : array();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());

			return;
		}

		// Build the list
		$options = array();

		foreach ($positions as $position)
		{
			if (!$position && !$editPositions)
			{
				$options[] = JHtml::_('select.option', 'none', ':: ' . JText::_('JNONE') . ' ::');
			}
			else
			{
				$options[] = JHtml::_('select.option', $position, $position);
			}
		}

		return $options;
	}

	/**
	 * Return a list of templates
	 *
	 * @param   integer $clientId Client ID
	 * @param   string  $state    State
	 * @param   string  $template Template name
	 *
	 * @return  array  List of templates
	 */
	public static function getTemplates($clientId = 0, $state = '', $template = '')
	{
		$db = JFactory::getDbo();

		// Get the database object and a new query object.
		$query = $db->getQuery(true);

		// Build the query.
		$query->select('e.element, e.name, e.enabled')
			->from('#__extensions as e');

		if ($template != '')
		{
			$query->where('e.element = ' . $db->quote($template));
		}

		$query->where('e.type = ' . $db->quote('template'))
			->where('e.client_id = ' . (int) $clientId);

		if ($state != '')
		{
			$query->where('e.enabled = ' . $db->quote($state));
		}

		// Set the query and load the templates.
		$db->setQuery($query);
		$templates = $db->loadObjectList('element');

		return $templates;
	}

	/**
	 * Get a list of the unique modules installed in the client application.
	 *
	 * @param   int $clientId The client id.
	 *
	 * @return  array  Array of unique modules
	 */
	public static function getModules($clientId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('e.element AS value, e.name AS text')
			->from('#__extensions as e')
			->join('LEFT', '#__modules as m ON m.module=e.element AND m.client_id=e.client_id')
			->where('e.type = ' . $db->quote('module'))
			->where('e.client_id = ' . (int) $clientId)
			->where('m.module IS NOT NULL')
			->group('e.element, e.name');

		$db->setQuery($query);
		$modules = $db->loadObjectList();
		$lang    = JFactory::getLanguage();

		foreach ($modules as $i => $module)
		{
			$extension = $module->value;
			$path      = $clientId ? JPATH_ADMINISTRATOR : JPATH_SITE;
			$source    = $path . "/modules/$extension";
			$lang->load("$extension.sys", $path, null, false, true)
			|| $lang->load("$extension.sys", $source, null, false, true);
			$modules[$i]->text = JText::_($module->text);
		}

		JArrayHelper::sortObjects($modules, 'text', 1, true, true);

		return $modules;
	}

	/**
	 * Get a list of the menu item assignment options for modules.
	 *
	 * @param   int $clientId The client id.
	 *
	 * @return  array
	 */
	public static function getMenuItemAssignmentOptions($clientId)
	{
		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('JALL'));
		$options[] = JHtml::_('select.option', '-', JText::_('JNONE'));

		if ($clientId != 0)
		{
			return $options;
		}

		$options[] = JHtml::_('select.option', '-1', JText::_('COM_MODULES_ASSIGNED_VARIES_EXCEPT'));
		$options[] = JHtml::_('select.option', '-2', JText::_('COM_MODULES_ASSIGNED_VARIES_ONLY'));

		require_once realpath(JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');
		$items = MenusHelper::getMenuLinks();

		foreach ($items as $type)
		{
			$options[] = JHtml::_('select.option', '<OPTGROUP>', $type->title);

			foreach ($type->links as $item)
			{
				$options[] = JHtml::_('select.option', $item->value, str_repeat('- ', $item->level) . $item->text);
			}

			$options[] = JHtml::_('select.option', '</OPTGROUP>');
		}

		return $options;
	}

	/**
	 * Return a translated module position name
	 *
	 * @param   integer $clientId Application client id 0: site | 1: admin
	 * @param   string  $template Template name
	 * @param   string  $position Position name
	 *
	 * @return  string  Return a translated position name
	 */
	public static function getTranslatedModulePosition($clientId, $template, $position)
	{
		// Template translation
		$lang = JFactory::getLanguage();
		$path = $clientId ? JPATH_ADMINISTRATOR : JPATH_SITE;

		$lang->load('tpl_' . $template . '.sys', $path, null, false, false)
		|| $lang->load('tpl_' . $template . '.sys', $path . '/templates/' . $template, null, false, false)
		|| $lang->load('tpl_' . $template . '.sys', $path, $lang->getDefault(), false, false)
		|| $lang->load('tpl_' . $template . '.sys', $path . '/templates/' . $template, $lang->getDefault(), false, false);

		$langKey = strtoupper('TPL_' . $template . '_POSITION_' . $position);
		$text    = JText::_($langKey);

		// Avoid untranslated strings
		if (!self::isTranslatedText($langKey, $text))
		{
			// Modules component translation
			$langKey = strtoupper('COM_MODULES_POSITION_' . $position);
			$text    = JText::_($langKey);

			// Avoid untranslated strings
			if (!self::isTranslatedText($langKey, $text))
			{
				// Try to humanize the position name
				$text = ucfirst(preg_replace('/^' . $template . '\-/', '', $position));
				$text = ucwords(str_replace(array('-', '_'), ' ', $text));
			}
		}

		return $text;
	}

	/**
	 * Check if the string was translated
	 *
	 * @param   string $langKey Language file text key
	 * @param   string $text    The "translated" text to be checked
	 *
	 * @return  boolean  Return true for translated text
	 */
	public static function isTranslatedText($langKey, $text)
	{
		return $text !== $langKey;
	}

	/**
	 * Create and return a new Option
	 *
	 * @param   string $value The option value [optional]
	 * @param   string $text  The option text [optional]
	 *
	 * @return  object  The option as an object (stdClass instance)
	 */
	public static function createOption($value = '', $text = '')
	{
		if (empty($text))
		{
			$text = $value;
		}

		$option        = new stdClass;
		$option->value = $value;
		$option->text  = $text;

		return $option;
	}

	/**
	 * Create and return a new Option Group
	 *
	 * @param   string $label   Value and label for group [optional]
	 * @param   array  $options Array of options to insert into group [optional]
	 *
	 * @return  array  Return the new group as an array
	 */
	public static function createOptionGroup($label = '', $options = array())
	{
		$group          = array();
		$group['value'] = $label;
		$group['text']  = $label;
		$group['items'] = $options;

		return $group;
	}
}
