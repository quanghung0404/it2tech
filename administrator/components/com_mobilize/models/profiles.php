<?php

/**
 * @version     $Id: forms.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Models
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

/**
 * JSNUniform model Forms
 *
 * @package     Models
 * @subpackage  Forms
 * @since       1.6
 */
class JSNMobilizeModelProfiles extends JModelList
{

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   11.1
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array('p.profile_id','p.profile_title,p.ordering');
		}
		parent::__construct($config);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return string An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		// Select some fields
		$query->select('p.*');

		$query->from('#__jsn_mobilize_profiles AS p');

		// Filter by search in title
		$search = $this->getState('filter.search');
		// Filter by published state
		$state = $this->getState('filter.state');
		if (is_numeric($state))
		{
			$query->where('p.profile_state = ' . (int) $state, 'AND');
		}
		elseif ($state === '')
		{
			$query->where('(p.profile_state IN (0,1))');
		}
		// Filter by search in title
		if (!empty($search))
		{
			if (stripos($search, 'profile_id:') === 0)
			{
				$query->where('p.profile_id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('((p.profile_title LIKE ' . $search . ' ) OR (p.profile_description LIKE ' . $search . ' ))', ' AND ');
			}
		}
		$query->order($db->escape($this->getState('list.ordering', 'p.ordering')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * 
	 * to be called on the first call to the getState() method unless the model
	 * 
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		$state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState('p.ordering', 'ASC');
	}

}
