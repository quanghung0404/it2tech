<?php
/**
 * Helper class: com_content.article
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

include_once JPATH_SITE . '/components/com_content/helpers/route.php';

class HelperBetterPreviewHelperContentArticle extends PlgSystemBetterPreviewHelper
{
	function getArticle()
	{
		if (JFactory::getApplication()->input->get('layout', 'edit') != 'edit'
			|| !JFactory::getApplication()->input->get('id')
		)
		{
			return;
		}

		$item = $this->getItem(
			JFactory::getApplication()->input->get('id'),
			'content',
			array('name' => 'title', 'published' => 'state', 'language' => 'language', 'parent' => 'catid'),
			array('type' => 'NN_ARTICLE')
		);

		$item->url = ContentHelperRoute::getArticleRoute($item->id, $item->parent, $item->language);

		$default_menu_item = JFactory::getApplication()->getMenu('site')->getDefault($item->language);
		if (empty($default_menu_item))
		{
			$default_menu_item = JFactory::getApplication()->getMenu('site')->getDefault();
		}
		$default_menu_url = $default_menu_item->link . '&Itemid=' . $default_menu_item->id;

		if (!$this->params->use_home_menu_id && $item->url != $default_menu_url)
		{
			// Remove the home Itemid
			$item->url = preg_replace('#&(amp;)?Itemid=' . $default_menu_item->id . '$#', '', $item->url);
		}

		return $item;
	}

	function getArticleParents($item)
	{
		if (empty($item)
			|| JFactory::getApplication()->input->get('layout', 'edit') != 'edit'
			|| !JFactory::getApplication()->input->get('id')
		)
		{
			return false;
		}

		$parents = $this->getParents(
			$item,
			'categories',
			array('name' => 'title', 'parent' => 'parent_id', 'language' => 'language'),
			array('type' => 'JCATEGORY'),
			1
		);

		foreach ($parents as &$parent)
		{
			$parent->url = ContentHelperRoute::getCategoryRoute($parent->id, $item->language);
		}

		return $parents;
	}
}
