<?php
/**
 * @package         NoNumber Framework
 * @version         16.1.9037
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once dirname(__DIR__) . '/helpers/field.php';

class JFormFieldNN_Components extends NNFormField
{
	public $type = 'Components';

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$options = $this->getComponents();

		if (empty($options))
		{
			return '';
		}

		$size = (int) $this->get('size');

		require_once dirname(__DIR__) . '/helpers/html.php';

		return NNHtml::selectlistsimple($options, $this->name, $this->value, $this->id, $size, 1);
	}

	function getComponents()
	{
		$frontend = $this->get('frontend', 1);
		$admin    = $this->get('admin', 1);

		if (!$frontend && !$admin)
		{
			return array();
		}

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$query = $this->db->getQuery(true)
			->select('e.name, e.element')
			->from('#__extensions AS e')
			->where('e.type = ' . $this->db->quote('component'))
			->where('e.name != ""')
			->where('e.element != ""')
			->group('e.element')
			->order('e.element, e.name');
		$this->db->setQuery($query);
		$components = $this->db->loadObjectList();

		$comps = array();
		$lang  = JFactory::getLanguage();

		foreach ($components as $i => $component)
		{
			// return if there is no main component folder
			if (!($frontend && JFolder::exists(JPATH_SITE . '/components/' . $component->element))
				&& !($admin && JFolder::exists(JPATH_ADMINISTRATOR . '/components/' . $component->element))
			)
			{
				continue;
			}

			// return if there is no views folder
			if (!($frontend && JFolder::exists(JPATH_SITE . '/components/' . $component->element . '/views'))
				&& !($admin && JFolder::exists(JPATH_ADMINISTRATOR . '/components/' . $component->element . '/views'))
			)
			{
				continue;
			}
			if (!empty($component->element))
			{
				// Load the core file then
				// Load extension-local file.
				$lang->load($component->element . '.sys', JPATH_BASE, null, false, false)
				|| $lang->load($component->element . '.sys', JPATH_ADMINISTRATOR . '/components/' . $component->element, null, false, false)
				|| $lang->load($component->element . '.sys', JPATH_BASE, $lang->getDefault(), false, false)
				|| $lang->load($component->element . '.sys', JPATH_ADMINISTRATOR . '/components/' . $component->element, $lang->getDefault(), false, false);
			}
			$component->name                                                                        = JText::_(strtoupper($component->name));
			$comps[preg_replace('#[^a-z0-9_]#i', '', $component->name . '_' . $component->element)] = $component;
		}
		ksort($comps);

		$options = array();

		foreach ($comps as $component)
		{
			$options[] = JHtml::_('select.option', $component->element, $component->name);
		}

		return $options;
	}
}
