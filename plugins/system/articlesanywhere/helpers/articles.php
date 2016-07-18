<?php
/**
 * Plugin Helper File: Articles
 *
 * @package         Articles Anywhere
 * @version         4.1.5PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class PlgSystemArticlesAnywhereHelperArticles
{
	var $helpers = array();
	var $params = null;
	var $aid = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemArticlesAnywhereHelpers::getInstance();
		$this->params  = $this->helpers->getParams();

		$this->aid = JFactory::getUser()->getAuthorisedViewLevels();
	}

	public function replace(&$string, &$match, $art = null)
	{
		// Check for categories...
		$groups = explode('|', $match['id']);

		$data    = array();
		$ignores = array();

		foreach ($groups as $group)
		{
			if (!$set_data = $this->getDataByGroup($group, $ignores))
			{
				continue;
			}
			$data[] = $set_data;
		}

		$ids = array();
		foreach ($data as $dat)
		{
			$ids = array_merge($ids, $this->getArticleIdsByData($dat, $ignores));
		}

		$html  = array();
		$total = count($ids);

		$this->helpers->get('tags')->data->article = $art;
		$this->helpers->get('tags')->data->total   = $total;

		// Process empty category to allow for {if:empty} result
		if (!$total)
		{
			$data       = $match;
			$data['id'] = 0;

			$html[] = $this->processMatch($string, $art, $data, $ignores);
		}

		// Process articles from category
		foreach ($ids as $i => $id)
		{
			$data       = $match;
			$data['id'] = trim($id);
			$count      = $i + 1;

			$this->helpers->get('tags')->data->count  = $count;
			$this->helpers->get('tags')->data->even   = !($count % 2);
			$this->helpers->get('tags')->data->uneven = $count % 2;
			$this->helpers->get('tags')->data->first  = $count == 1;
			$this->helpers->get('tags')->data->last   = $count >= $total;

			$html[] = $this->processMatch($string, $art, $data, $ignores);
		}

		$string = NNText::strReplaceOnce($match['0'], implode('', $html), $string);
	}

	public function processMatch($string, $art, $data, $ignores)
	{
		return $this->helpers->get('process')->processMatch($string, $art, $data, $ignores);
	}

	public function getDataByGroup($group, &$ignores)
	{
		if (strpos($group, '=') !== false)
		{
			$this->helpers->get('process')->getIgnoreSetting($ignores, $group);

			return;
		}

		$data = $this->getGroupData($group);

		return $data;
	}

	public function getGroupData($group)
	{
		$data = (object) array(
			'ordering' => '',
			'limit'    => 0,
			'offset'   => 0,
		);

		if (preg_match('#^cats?:#', $group))
		{
			$group = preg_replace('#^cats?:#', '', $group);

			$data->iscat = true;
		}

		if (preg_match('#^tags?:#', $group))
		{
			$group = preg_replace('#^tags?:#', '', $group);

			$data->istag = true;
		}

		if (!preg_match('#^((?:[^:]+\:)+)([^:]+)$#', $group, $params))
		{
			$data->ids = explode(',', $group);

			return $data;
		}

		$data->ids = explode(',', $params['2']);

		$params = explode(':', trim(trim($params['1']), ':'));
		foreach ($params as $param)
		{
			if (is_numeric($param))
			{
				$data->limit = (int) $param;
				continue;
			}

			if (preg_match('#^([0-9]+)-([0-9]+)$#', $param, $limit))
			{
				$data->limit  = (int) ($limit['2'] - $limit['1']) + 1;
				$data->offset = (int) $limit['1'] - 1;
				continue;
			}

			$data->ordering = trim($param);
		}

		return $data;
	}

	public function getArticleIdsByData($data, $ignores = array())
	{
		switch (true)
		{
			case (isset($data->iscat)) :
				return $this->getIdsByCategories($data, $ignores);

			case (isset($data->istag)) :
				return $this->getIdsByTags($data, $ignores);

			default:
				$ids = array();
				foreach ($data->ids as $id)
				{
					if (strpos($id, '*') === false)
					{
						$ids[] = $id;
						continue;
					}
					$ids = array_merge($ids, $this->getIdsByWildcard($id, $data, $ignores));
				}

				return $ids;
		}
	}

	public function getIdsByWildcard($id, $data, $ignores = array())
	{
		$db    = JFactory::getDbo();
		$limit = $data->limit ? $data->limit : ((int) $this->params->limit ? (int) $this->params->limit : 1000);

		$query = $this->getIdsQuery($data, $ignores);

		$id = str_replace('*', '%', $id);

		$query->where('(a.title LIKE ' . $db->quote($id) . ' OR a.alias LIKE ' . $db->quote($id) . ')');

		$db->setQuery($query, $data->offset, $limit);

		return $db->loadColumn();
	}

	public function getIdsByTags($data, $ignores = array())
	{
		if (empty($data->ids))
		{
			return array();
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		list($ids, $titles, $likes) = $this->getQueryIdLists($data->ids);

		if (!empty($titles) || !empty($likes))
		{
			$query->clear()
				->select('t.id')
				->from('#__tags AS t');

			if (!empty($titles))
			{
				$query->where('(t.title IN (' . implode(',', $titles) . ') OR t.alias IN (' . implode(',', $titles) . '))');
			}

			if (!empty($likes))
			{
				$wheres = array();
				foreach ($likes as $like)
				{
					$wheres[] = 't.title LIKE ' . $like;
					$wheres[] = 't.alias LIKE ' . $like;
				}
				$query->where('(' . implode(' OR ', $wheres) . ')');
			}

			$db->setQuery($query);
			$ids = array_merge($ids, $db->loadColumn());
		}

		if (empty($ids))
		{
			return array();
		}

		$query->clear()
			->select('m.content_item_id')
			->from('#__contentitem_tag_map AS m')
			->where('m.type_alias = ' . $db->quote('com_content.article'))
			->where('m.tag_id IN (' . implode(',', $ids) . ')');
		$db->setQuery($query);
		$ids = $db->loadColumn();

		$limit = $data->limit ? $data->limit : ((int) $this->params->limit ? (int) $this->params->limit : 1000);

		$query = $this->getIdsQuery($data, $ignores);

		$query->where('a.id IN (' . implode(',', $ids) . ')');

		$db->setQuery($query, $data->offset, $limit);

		return $db->loadColumn();
	}

	public function getIdsByCategories($data, $ignores = array())
	{
		if (empty($data->ids))
		{
			return array();
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		list($ids, $titles, $likes) = $this->getQueryIdLists($data->ids);

		if (!empty($titles) || !empty($likes))
		{
			$query->clear()
				->select('c.id')
				->from('#__categories AS c')
				->where('c.extension = ' . $db->quote('com_content'));

			if (!empty($titles))
			{
				$query->where('(c.title IN (' . implode(',', $titles) . ') OR c.alias IN (' . implode(',', $titles) . '))');
			}

			if (!empty($likes))
			{
				$wheres = array();
				foreach ($likes as $like)
				{
					$wheres[] = 'c.title LIKE ' . $like;
					$wheres[] = 'c.alias LIKE ' . $like;
				}
				$query->where('(' . implode(' OR ', $wheres) . ')');
			}

			$db->setQuery($query);
			$ids = array_merge($ids, $db->loadColumn());
		}

		if (empty($data->ids))
		{
			return array();
		}

		$limit = $data->limit ? $data->limit : ((int) $this->params->limit ? (int) $this->params->limit : 1000);

		$query = $this->getIdsQuery($data, $ignores);

		$query->where('a.catid IN (' . implode(',', $ids) . ')');

		$db->setQuery($query, $data->offset, $limit);

		return $db->loadColumn();
	}

	public function getQueryIdLists($ids)
	{
		$db = JFactory::getDbo();

		$numeric      = array();
		$not_nummeric = array();
		$likes        = array();

		foreach ($ids as &$id)
		{
			if (is_numeric($id))
			{
				$numeric[] = $id;
				continue;
			}

			if (strpos($id, '*') !== false)
			{
				$likes[] = $db->quote(str_replace('*', '%', $id));
				continue;
			}
			$not_nummeric[] = $db->quote($id);
		}

		return array($numeric, $not_nummeric, $likes);
	}

	public function getIdsQuery($data, $ignores = array())
	{
		$ordering = $data->ordering ? $data->ordering : $this->params->ordering;

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id')
			->from('#__content AS a');

		$ignore_language = isset($ignores['ignore_language']) ? $ignores['ignore_language'] : $this->params->ignore_language;
		if (!$ignore_language)
		{
			$query->where('a.language IN (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		$ignore_state = isset($ignores['ignore_state']) ? $ignores['ignore_state'] : $this->params->ignore_state;
		if (!$ignore_state)
		{
			$jnow     = JFactory::getDate();
			$now      = $jnow->toSql();
			$nullDate = $db->getNullDate();
			$query->where('a.state = 1')
				->where('( a.publish_up = ' . $db->quote($nullDate) . ' OR a.publish_up <= ' . $db->quote($now) . ' )')
				->where('( a.publish_down = ' . $db->quote($nullDate) . ' OR a.publish_down >= ' . $db->quote($now) . ' )');
		}

		$ignore_access = isset($ignores['ignore_access']) ? $ignores['ignore_access'] : $this->params->ignore_access;
		if (!$ignore_access)
		{
			$query->where('a.access IN(' . implode(', ', $this->aid) . ')');
		}

		if ($ordering == 'random')
		{
			$query->order('RAND()');
		}
		else
		{
			if (strpos($ordering, ' ASC') === false && strpos($ordering, ' DESC') === false)
			{
				$ordering .= ' ' . $this->params->ordering_direction;
			}
			$query->order('a.' . $ordering);
		}

		return $query;
	}
}
