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

require_once dirname(__DIR__) . '/assignment.php';

class NNFrameworkAssignmentsHikaShop extends NNFrameworkAssignment
{
	function passPageTypes()
	{
		if ($this->request->option != 'com_hikashop')
		{
			return $this->pass(false);
		}

		$type = $this->request->view;
		if (
			($type == 'product' && in_array($this->request->layout, array('contact', 'show')))
			|| ($type == 'user' && in_array($this->request->layout, array('cpanel')))
		)
		{
			$type .= '_' . $this->request->layout;
		}

		return $this->passSimple($type);
	}

	function passCategories()
	{
		if ($this->request->option != 'com_hikashop')
		{
			return $this->pass(false);
		}

		$pass = (
			($this->params->inc_categories
				&& ($this->request->view == 'category')
			)
			|| ($this->params->inc_items && $this->request->view == 'product')
		);

		if (!$pass)
		{
			return $this->pass(false);
		}

		$cats = $this->getCategories();

		$pass = $this->passSimple($cats, 'include');

		if ($pass && $this->params->inc_children == 2)
		{
			return $this->pass(false);
		}
		else if (!$pass && $this->params->inc_children)
		{
			foreach ($cats as $cat)
			{
				$cats = array_merge($cats, $this->getCatParentIds($cat));
			}
		}

		return $this->passSimple($cats);
	}

	function passProducts()
	{
		if (!$this->request->id || $this->request->option != 'com_hikashop' || $this->request->view != 'product')
		{
			return $this->pass(false);
		}

		return $this->passSimple($this->request->id);
	}

	function getCategories()
	{
		switch (true)
		{
			case ($this->request->view == 'category' && $this->request->id):
				return array($this->request->id);

			case ($this->request->view == 'category'):
				include_once JPATH_ADMINISTRATOR . '/components/com_hikashop/helpers/helper.php';
				$menuClass = hikashop_get('class.menus');
				$menuData  = $menuClass->get($this->request->Itemid);

				return $this->makeArray($menuData->hikashop_params['selectparentlisting']);

			case ($this->request->id):
				$query = $this->db->getQuery(true)
					->select('c.category_id')
					->from('#__hikashop_product_category AS c')
					->where('c.product_id = ' . (int) $this->request->id);
				$this->db->setQuery($query);
				$cats = $this->db->loadColumn();

				return $this->makeArray($cats);

			default:
				return array();
		}
	}

	function getCatParentIds($id = 0)
	{
		return $this->getParentIds($id, 'hikashop_category', 'category_parent_id', 'category_id');
	}
}
