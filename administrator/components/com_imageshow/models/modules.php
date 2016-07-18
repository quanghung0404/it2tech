<?php
/**
 * @version    $Id: modules.php 16647 2012-10-03 10:06:41Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');

class ImageShowModelModules extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering',
				'module', 'a.module',
				'language_title',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'position', 'a.position',
				'pages',
				'name', 'e.name',
			);
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$position = $this->getUserStateFromRequest($this->context.'.filter.position', 'filter_position', '', 'string');
		$this->setState('filter.position', $position);

		$module = $this->getUserStateFromRequest($this->context.'.filter.module', 'filter_module', '', 'string');
		$this->setState('filter.module', $module);

		// List state information.
		parent::populateState('position', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.state');
		$id	.= ':'.$this->getState('filter.position');
		$id	.= ':'.$this->getState('filter.module');

		return parent::getStoreId($id);
	}

	protected function _getList($query, $limitstart=0, $limit=0)
	{
		$ordering = $this->getState('list.ordering', 'ordering');
		if (in_array($ordering, array('pages', 'name'))) {
			$this->_db->setQuery($query);
			$result = $this->_db->loadObjectList();
			$this->translate($result);
			$lang = JFactory::getLanguage();
			JArrayHelper::sortObjects($result,$ordering, $this->getState('list.direction') == 'desc' ? -1 : 1, true, $lang->getLocale());
			$total = count($result);
			$this->cache[$this->getStoreId('getTotal')] = $total;
			if ($total < $limitstart) {
				$limitstart = 0;
				$this->setState('list.start', 0);
			}
			return array_slice($result, $limitstart, $limit ? $limit : null);
		}
		else {
			if ($ordering == 'ordering') {
				$query->order('position ASC');
			}
			$query->order($this->_db->quoteName($ordering) . ' ' . $this->getState('list.direction'));
			if ($ordering == 'position') {
				$query->order('ordering ASC');
			}
			$result = parent::_getList($query, $limitstart, $limit);
			$this->translate($result);
			return $result;
		}
	}

	protected function translate(&$items)
	{
		$lang = JFactory::getLanguage();
		$client = $this->getState('filter.client_id') ? 'administrator' : 'site';
		foreach($items as $item) {
			$extension = $item->module;
			$source = constant('JPATH_' . strtoupper($client)) . "/modules/$extension";
			$lang->load("$extension.sys", constant('JPATH_' . strtoupper($client)), null, false, false)
			||	$lang->load("$extension.sys", $source, null, false, false)
			||	$lang->load("$extension.sys", constant('JPATH_' . strtoupper($client)), $lang->getDefault(), false, false)
			||	$lang->load("$extension.sys", $source, $lang->getDefault(), false, false);
			$item->name = JText::_($item->name);
			if (is_null($item->pages)) {
				$item->pages = JText::_('JNONE');
			} else if ($item->pages < 0) {
				$item->pages = JText::_('COM_MODULES_ASSIGNED_VARIES_EXCEPT');
			} else if ($item->pages > 0) {
				$item->pages = JText::_('COM_MODULES_ASSIGNED_VARIES_ONLY');
			} else {
				$item->pages = JText::_('JALL');
			}
		}
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
		$this->getState(
				'list.select',
				'a.id, a.title, a.note, a.position, a.module, a.language,' .
				'a.checked_out, a.checked_out_time, a.published, a.access, a.ordering, a.publish_up, a.publish_down'
				)
				);
				$query->from('#__modules AS a');

				// Join over the language
				$query->select('l.title AS language_title');
				$query->join('LEFT', '#__languages AS l ON l.lang_code = a.language');

				// Join over the users for the checked out user.
				$query->select('uc.name AS editor');
				$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

				// Join over the asset groups.
				$query->select('ag.title AS access_level');
				$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

				// Join over the module menus
				$query->select('MIN(mm.menuid) AS pages');
				$query->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = a.id');
				$query->group('a.id');

				// Join over the extensions
				$query->select('e.name AS name');
				$query->join('LEFT', '#__extensions AS e ON e.element = a.module');
				$query->group('a.id');

				// Filter by access level.
				if ($access = $this->getState('filter.access')) {
					$query->where('a.access = '.(int) $access);
				}

				// Filter by published state
				$state = $this->getState('filter.state');
				if (is_numeric($state)) {
					$query->where('a.published = '.(int) $state);
				}
				else if ($state === '') {
					$query->where('(a.published IN (0, 1))');
				}

				// Filter by position
				$position = $this->getState('filter.position');
				if ($position) {
					$query->where('a.position = '.$db->Quote($position));
				}

				// Filter by module
				$module = $this->getState('filter.module');
				if ($module) {
					$query->where('a.module = '.$db->Quote($module));
				}
				$query->where('a.client_id = 0');

				// Filter by search in title
				$search = $this->getState('filter.search');
				if (!empty($search))
				{
					if (stripos($search, 'id:') === 0) {
						$query->where('a.id = '.(int) substr($search, 3));
					}
					else
					{
						$search = $db->Quote('%'.$db->escape($search, true).'%');
						$query->where('('.'a.title LIKE '.$search.' OR a.note LIKE '.$search.')');
					}
				}

				return $query;
	}
}
