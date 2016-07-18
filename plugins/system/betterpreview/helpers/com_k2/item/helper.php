<?php
/**
 * Helper class: com_k2.item
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

include_once JPATH_SITE . '/components/com_k2/helpers/route.php';

class HelperBetterPreviewHelperK2Item extends PlgSystemBetterPreviewHelper
{
	function getK2Item()
	{
		if (!JFactory::getApplication()->input->get('cid'))
		{
			return;
		}

		$item = $this->getItem(
			JFactory::getApplication()->input->get('cid'),
			'k2_items',
			array('name' => 'title', 'parent' => 'catid'),
			array('type' => 'K2_ITEM')
		);

		$item->url = K2HelperRoute::getItemRoute($item->id, $item->parent);

		return $item;
	}

	function getK2ItemParents($item)
	{
		if (empty($item)
			|| !JFactory::getApplication()->input->get('cid')
		)
		{
			return false;
		}

		$parents = $this->getParents(
			$item,
			'k2_categories',
			array(),
			array('type' => 'K2_CATEGORY')
		);

		foreach ($parents as &$parent)
		{
			$parent->url = K2HelperRoute::getCategoryRoute($parent->id);
		}

		return $parents;
	}
}
