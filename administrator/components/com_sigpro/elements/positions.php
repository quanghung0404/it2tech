<?php
/**
 * @version     3.1.x
 * @package     Simple Image Gallery Pro
 * @author      JoomlaWorks - http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

require_once (dirname(__FILE__).'/base.php');

class JWElementPositions extends JWElement
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			return $this->getPositions($value);
		}

		$db = JFactory::getDBO();

		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$query = 'SELECT DISTINCT(template) AS text, template AS value FROM #__template_styles WHERE client_id = 0';
			$db->setQuery($query);
		}
		else
		{
			$query = 'SELECT DISTINCT(template) AS text, template AS value FROM #__templates_menu WHERE client_id = 0';
			$db->setQuery($query);
		}

		$templates = $db->loadObjectList();
		$query = 'SELECT DISTINCT(position) FROM #__modules WHERE client_id = 0';
		$db->setQuery($query);

		$positions = $db->loadResultArray();
		$positions = (is_array($positions)) ? $positions : array();

		for ($i = 0, $n = count($templates); $i < $n; $i++)
		{
			$path = JPATH_SITE.DS.'templates'.DS.$templates[$i]->value;

			$xml = JFactory::getXMLParser('Simple');
			if ($xml->loadFile($path.DS.'templateDetails.xml'))
			{
				$p = $xml->document->getElementByPath('positions');
				if (is_a($p, 'JSimpleXMLElement') && count($p->children()))
				{
					foreach ($p->children() as $child)
					{
						if (!in_array($child->data(), $positions))
							$positions[] = $child->data();
					}
				}
			}

		}

		if (defined('_JLEGACY') && _JLEGACY == '1.0')
		{
			$positions[] = 'left';
			$positions[] = 'right';
			$positions[] = 'top';
			$positions[] = 'bottom';
			$positions[] = 'inset';
			$positions[] = 'banner';
			$positions[] = 'header';
			$positions[] = 'footer';
			$positions[] = 'newsflash';
			$positions[] = 'legals';
			$positions[] = 'pathway';
			$positions[] = 'breadcrumb';
			$positions[] = 'user1';
			$positions[] = 'user2';
			$positions[] = 'user3';
			$positions[] = 'user4';
			$positions[] = 'user5';
			$positions[] = 'user6';
			$positions[] = 'user7';
			$positions[] = 'user8';
			$positions[] = 'user9';
			$positions[] = 'advert1';
			$positions[] = 'advert2';
			$positions[] = 'advert3';
			$positions[] = 'debug';
			$positions[] = 'syndicate';
		}

		$positions = array_unique($positions);
		sort($positions);

		$options[] = JHTML::_('select.option', '', JText::_('COM_SIGPRO___NONE_SELECTED__'), 'id', 'title');
		foreach ($positions as $position)
		{
			if ($position)
				$options[] = JHTML::_('select.option', $position, $position, 'id', 'title');
		}

		$fieldName = version_compare(JVERSION, '1.6.0', 'ge') ? $name : $control_name.'['.$name.']';

		$output = JHTML::_('select.genericlist', $options, $fieldName, 'class="inputbox"', 'id', 'title', $value);

		return $output;

	}

	protected function getPositions($active)
	{

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('element, name, enabled');
		$query->from('#__extensions');
		$query->where('client_id = 0');
		$query->where('type = '.$db->quote('template'));
		$query->where('enabled = 1');
		$db->setQuery($query);
		$templates = $db->loadObjectList();

		require_once JPATH_ADMINISTRATOR.'/components/com_templates/helpers/templates.php';
		$options = array();
		$group = array();
		$group['value'] = '';
		$group['text'] = '';
		$group['items'] = array();
		$option = new stdClass;
		$option->value = '';
		$option->text = JText::_('COM_SIGPRO___NONE_SELECTED__');
		$group['items'][] = $option;
		$options[] = $group;
		foreach ($templates as $template)
		{
			$group = array();
			$group['value'] = $template->name;
			$group['text'] = $template->name;
			$group['items'] = array();
			$positions = TemplatesHelper::getPositions(0, $template->element);
			foreach ($positions as $position)
			{
				$option = new stdClass;
				$option->value = $position;
				$option->text = $position;
				$group['items'][] = $option;
			}
			$options[] = $group;
		}

		// Build field
		$attributes = array(
			'list.select' => $active,
			'list.attr' => 'class="chzn-custom-value input-xlarge"'
		);

		return JHtml::_('select.groupedlist', $options, $this->name, $attributes);

	}

}

class JFormFieldPositions extends JWElementPositions
{
	var $type = 'positions';
}

class JElementPositions extends JWElementPositions
{
	var $_name = 'positions';
}
