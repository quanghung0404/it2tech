<?php
/**
 * Plugin Helper File: Articles K2
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

require_once __DIR__ . '/articles.php';

class PlgSystemArticlesAnywhereHelperArticlesK2 extends PlgSystemArticlesAnywhereHelperArticles
{
	public function processMatch($string, $art, $data, $ignores)
	{
		return $this->helpers->get('process')->processMatch($string, $art, $data, $ignores, 'k2');
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
				->from('#__k2_tags AS t');

			if (!empty($titles))
			{
				$query->where('t.name IN (' . implode(',', $titles) . ')');
			}

			if (!empty($likes))
			{
				$wheres = array();
				foreach ($likes as $like)
				{
					$wheres[] = 't.name LIKE ' . $like;
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
			->select('m.itemID')
			->from('#__k2_tags_xref AS m')
			->where('m.tagID IN (' . implode(',', $ids) . ')');
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
				->from('#__k2_categories AS c');

			if (!empty($titles))
			{
				$query->where('(c.name IN (' . implode(',', $titles) . ') OR c.alias IN (' . implode(',', $titles) . '))');
			}

			if (!empty($likes))
			{
				$wheres = array();
				foreach ($likes as $like)
				{
					$wheres[] = 'c.name LIKE ' . $like;
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

	public function getIdsQuery($data, $ignores = array())
	{
		$ordering = $data->ordering ? $data->ordering : $this->params->ordering;

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id')
			->from('#__k2_items AS a');

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
			$query->where('a.published = 1')
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
