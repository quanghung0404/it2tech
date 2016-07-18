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

require_once dirname(__DIR__) . '/helpers/groupfield.php';

class JFormFieldNN_K2 extends NNFormGroupField
{
	public $type = 'K2';

	protected function getInput()
	{
		if ($error = $this->missingFilesOrTables(array('categories', 'items', 'tags')))
		{
			return $error;
		}

		return $this->getSelectList();
	}

	function getCategories()
	{
		$state_field = NN_K2_VERSION == 3 ? 'state' : 'published';

		$query = $this->db->getQuery(true)
			->select('COUNT(c.id)')
			->from('#__k2_categories AS c')
			->where('c.' . $state_field . ' > -1');
		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		if ($total > $this->max_list_count)
		{
			return -1;
		}

		$parent_field   = NN_K2_VERSION == 3 ? 'parent_id' : 'parent';
		$title_field    = NN_K2_VERSION == 3 ? 'title' : 'name';
		$ordering_field = NN_K2_VERSION == 3 ? 'lft' : 'ordering';

		$query->clear('select')
			->select('c.id, c.' . $parent_field . ' AS parent_id, c.' . $title_field . ' AS title, c.' . $state_field . ' AS published');
		if (!$this->get('getcategories', 1))
		{
			$query->where('c.' . $parent_field . ' = 0');
		}
		$query->order('c.' . $ordering_field . ', c.' . $title_field);
		$this->db->setQuery($query);
		$items = $this->db->loadObjectList();

		return $this->getOptionsTreeByList($items);
	}

	function getTags()
	{
		$state_field = NN_K2_VERSION == 3 ? 'state' : 'published';

		$query = $this->db->getQuery(true)
			->select('t.name as id, t.name as name')
			->from('#__k2_tags AS t')
			->where('t.' . $state_field . ' = 1')
			->order('t.name');
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		return $this->getOptionsByList($list);
	}

	function getItems()
	{
		$state_field = NN_K2_VERSION == 3 ? 'state' : 'published';

		$query = $this->db->getQuery(true)
			->select('COUNT(i.id)')
			->from('#__k2_items AS i')
			->where('i.' . $state_field . ' > -1');
		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		if ($total > $this->max_list_count)
		{
			return -1;
		}

		$cat_title_field = NN_K2_VERSION == 3 ? 'title' : 'name';

		$query->clear('select')
			->select('i.id, i.title as name, c.' . $cat_title_field . ' as cat, i.' . $state_field . ' as published')
			->join('LEFT', '#__k2_categories AS c ON c.id = i.catid')
			->group('i.id')
			->order('i.title, i.ordering, i.id');
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		return $this->getOptionsByList($list, array('cat', 'id'));
	}
}
