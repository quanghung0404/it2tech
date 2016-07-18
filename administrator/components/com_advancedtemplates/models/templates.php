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
 * Methods supporting a list of template extension records.
 */
class AdvancedTemplatesModelTemplates extends JModelList
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
				'name', 'a.name',
				'client_id', 'a.client_id',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Override parent getItems to add extra XML metadata.
	 *
	 * @return  array
	 */
	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as &$item)
		{
			$client        = JApplicationHelper::getClientInfo($item->client_id);
			$item->xmldata = AdvancedTemplatesHelper::parseXMLTemplateFile($client->path, $item->element);
		}

		return $items;
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.extension_id, a.name, a.element, a.client_id'
			)
		);
		$query->from($db->quoteName('#__extensions') . ' AS a');

		// Filter by extension type.
		$query->where($db->quoteName('type') . ' = ' . $db->quote('template'));

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
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.element LIKE ' . $search . ' OR a.name LIKE ' . $search . ')');
			}
		}

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.name')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		return $query;
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
		$id .= ':' . $this->getState('filter.client_id');

		return parent::getStoreId($id);
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

		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = trim($this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search'));
		$this->setState('filter.search', $search);

		$clientId = $this->getUserStateFromRequest($this->context . '.filter.client_id', 'filter_client_id', null);
		$this->setState('filter.client_id', $clientId);

		$view = $app->input->get('view', 'styles');
		if ($view != $app->getUserState($this->context . '.view'))
		{
			$app->setUserState($this->context . '.view', $view);
			$app->input->set('limitstart', 0);
		}
		$this->setState('stfilter.view', $view);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_advancedtemplates');
		$this->setState('params', $params);

		// Need to null the context.list to prevent issues with populateState
		JFactory::getApplication()->setUserState($this->context . '.list', null);

		// List state information.
		parent::populateState('a.element', 'asc');
	}
}
