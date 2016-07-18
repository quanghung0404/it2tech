<?php
/**
 * Link Helper class: com_menus.article
 *
 * @package         Better Preview
 * @version         4.1.2PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class HelperBetterPreviewLinkMenusItem extends HelperBetterPreviewLink
{
	function getLinks()
	{
		if (!JFactory::getApplication()->input->get('id'))
		{
			return;
		}

		$item = $this->getItem(
			JFactory::getApplication()->input->get('id'),
			'menu',
			array('name' => 'title', 'parent' => 'parent_id', 'url' => 'link', 'type' => 'type')
		);

		$parents = $this->getParents(
			$item,
			'menu',
			array('name' => 'title', 'parent' => 'parent_id', 'url' => 'link', 'type' => 'type'),
			array(),
			1
		);

		$model = new MenusModelMenutypes;
		$model->getTypeOptions();
		$this->types = $model->getReverseLookup();

		$this->setParams($item);

		foreach ($parents as &$parent)
		{
			$this->setParams($parent);
		}

		return array_merge(array($item), $parents);
	}

	function setParams(&$item)
	{
		if ($item->type == 'alias')
		{
			$name = $item->name;

			$this->db = JFactory::getDbo();
			$this->q->clear()
				->select('m.params')
				->from('#__menu as m')
				->where('m.id = ' . (int) $item->id);
			$this->db->setQuery($this->q);
			$params = json_decode($this->db->loadResult());
			if (is_null($params))
			{
				$params = new stdClass;
			}

			$item = $this->getItem(
				$params->aliasoptions,
				'menu',
				array('name' => 'title', 'parent' => 'parent_id', 'url' => 'link', 'type' => 'type', 'home' => false)
			);
			$this->setParams($item);
			$item->name = $name . ' &rarr; ' . $item->name;
		}

		switch ($item->type)
		{
			case 'url':
				$item->type = JText::_('COM_MENUS_TYPE_EXTERNAL_URL');
				break;

			case 'separator':
				$item->type      = JText::_('COM_MENUS_TYPE_SEPARATOR');
				$item->url       = '';
				$item->published = 0;
				break;

			default:
				$item->type = $this->getType($item);
				$item->url .= '&Itemid=' . $item->id;
				break;
		}
	}
}
