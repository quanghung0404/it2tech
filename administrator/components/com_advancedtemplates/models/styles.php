<?php
/**
 * @package         Advanced Template Manager
 * @version         1.6.4PRO
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
 * Methods supporting a list of template style records.
 */
class AdvancedTemplatesModelStyles extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'color', 'a.color',
				'title', 'a.title',
				'client_id', 'a.client_id',
				'template', 'a.template',
				'home', 'a.home',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string $ordering  An optional ordering field.
	 * @param   string $direction An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Load the filter state.
		$search = trim($this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search'));
		$this->setState('filter.search', $search);

		$template = $this->getUserStateFromRequest($this->context . '.filter.template', 'filter_template');
		$this->setState('filter.template', $template);

		$clientId = $this->getUserStateFromRequest($this->context . '.filter.client_id', 'filter_client_id', null);
		$this->setState('filter.client_id', $clientId);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_advancedtemplates');
		$this->setState('params', $params);

		// Need to null the context.list to prevent issues with populateState
		JFactory::getApplication()->setUserState($this->context . '.list', null);

		// List state information.
		parent::populateState('a.template', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string $id A prefix for the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . trim($this->getState('filter.search'));
		$id .= ':' . $this->getState('filter.template');
		$id .= ':' . $this->getState('filter.client_id');

		return parent::getStoreId($id);
	}

	/**
	 * Returns an object list
	 *
	 * @param   string The query
	 * @param   int    Offset
	 * @param   int    The number of records
	 *
	 * @return  array
	 */
	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$ordering  = strtolower($this->getState('list.ordering', 'a.title'));
		$orderDirn = strtoupper($this->getState('list.direction', 'ASC'));

		if ($ordering != 'color')
		{
			$query->order($this->_db->quoteName($ordering) . ' ' . $orderDirn);

			return parent::_getList($query, $limitstart, $limit);
		}

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		$newresult = array();
		foreach ($result as $i => $row)
		{
			$params = json_decode($row->advancedparams);
			if (is_null($params))
			{
				$params = new stdClass;
			}

			$color                                              = isset($params->color) ? str_replace('#', '', $params->color) : 'none';
			$color                                              = empty($color) ? 'none' : $color;
			$newresult['_' . $color . '_' . (($i + 1) / 10000)] = $row;
		}

		if ($orderDirn == 'DESC')
		{
			krsort($newresult);
		}
		else
		{
			ksort($newresult);
		}

		$newresult                                  = array_values($newresult);
		$total                                      = count($newresult);
		$this->cache[$this->getStoreId('getTotal')] = $total;
		if ($total < $limitstart)
		{
			$limitstart = 0;
			$this->setState('list.start', 0);
		}

		return array_slice($newresult, $limitstart, $limit ? $limit : null);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();

		// Select the required fields from the table.
		$query = $db->getQuery(true)
			->select(
				$this->getState(
					'list.select',
					'a.id, a.template, a.title, a.home, a.client_id, l.title AS language_title, l.image as image'
				)
			);
		$query->from($db->quoteName('#__template_styles') . ' AS a');

		// Join on menus.
		$query->select('COUNT(m.template_style_id) AS assigned')
			->join('LEFT', '#__menu AS m ON m.template_style_id = a.id')
			->group('a.id, a.template, a.title, a.home, a.client_id, l.title, l.image, e.extension_id');

		// Join over the language
		$query->join('LEFT', '#__languages AS l ON l.lang_code = a.home');

		// Filter by extension enabled
		$query->select('extension_id AS e_id')
			->join('LEFT', '#__extensions AS e ON e.element = a.template AND e.client_id = a.client_id')
			->where('e.enabled = 1')
			->where('e.type=' . $db->quote('template'));

		// Filter by template.
		if ($template = $this->getState('filter.template'))
		{
			$query->where('a.template = ' . $db->quote($template));
		}

		// Filter by client.
		$clientId = $this->getState('filter.client_id');

		if (is_numeric($clientId))
		{
			$query->where('a.client_id = ' . (int) $clientId);
		}

		// Filter by search in title
		$search = trim($this->getState('filter.search'));

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . strtolower($search) . '%');
				$query->where('(' . ' LOWER(a.template) LIKE ' . $search . ' OR LOWER(a.title) LIKE ' . $search . ')');
			}
		}

		// Join advanced params
		$query->select('aa.params AS advancedparams')
			->join('LEFT', '#__advancedtemplates AS aa ON aa.styleid = a.id');

		// Add the list ordering clause.
		$ordering = $this->getState('list.ordering', 'a.title');
		if ($ordering == 'color')
		{
			$ordering = 'a.title';
		}

		$query->order($db->escape($ordering) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}
}
