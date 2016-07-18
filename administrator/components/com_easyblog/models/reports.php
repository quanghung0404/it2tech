<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelReports extends EasyBlogAdminModel
{
	public $total = null;
	public $pagination = null;
	public $data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.reports.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (!$this->total) {
			$query = $this->_buildQuery(false, true);
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (!$this->pagination) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery($publishedOnly = false, $totalOnly = false)
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere($publishedOnly);

		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_reports') . ' AS a';
		$query[] = $where;

		if (! $totalOnly) {
			$orderby = $this->_buildQueryOrderBy();
			$query[] = $orderby;
		}

		$query = implode(' ', $query);

		return $query;
	}

	/**
	 * Builds the where clause
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function _buildQueryWhere()
	{
		$db = EB::db();
		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.reports.filter_state', 'filter_state', '', 'word');
		$search	= $this->app->getUserStateFromRequest('com_easyblog.reports.search', 'search', '', 'string');
		$search	= $db->getEscaped(trim(JString::strtolower($search)));

		$where = array();

		if ($search) {
			$where[] = ' LOWER( `reason` ) LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where) ? ' WHERE ' . implode (' AND ', $where) : '');

		return $where;
	}

	/**
	 * Builds the order by clause
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function _buildQueryOrderBy()
	{
		$order = $this->app->getUserStateFromRequest('com_easyblog.reports.filter_order', 'filter_order', 'a.created', 'cmd');
		$direction = $this->app->getUserStateFromRequest('com_easyblog.reports.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		$orderby = 'ORDER BY ' . $order . ' ' . $direction;

		return $orderby;
	}

	/**
	 * Delete reports for the given id and type
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteReports($id, $type)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'DELETE FROM ' . $db->quoteName('#__easyblog_reports');
		$query[] = 'WHERE ' . $db->quoteName('obj_id') . '=' . $db->Quote($id);
		$query[] = 'AND ' . $db->nameQuote('obj_type') . '=' . $db->Quote($type);

		$query = implode(' ', $query);
		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData($usePagination = true)
	{
		if (!$this->data) {
			$query = $this->_buildQuery();

			if ($usePagination) {
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			} else {
				$this->_data = $this->_getList($query);
			}
		}


		return $this->_data;
	}
}
