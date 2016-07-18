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

class EasyBlogModelFeeds extends EasyBlogAdminModel
{
	public $_total = null;
	public $_pagination = null;
	public $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.feeds.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
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
		if (!$this->_total) {
			$query = $this->_buildQuery();
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
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere();
		$orderby = $this->_buildQueryOrderBy();
		$db = EasyBlogHelper::db();

		$query	= 'SELECT * FROM ' . $db->nameQuote('#__easyblog_feeds')
				. $where . ' '
				. $orderby;

		return $query;
	}

	public function _buildQueryWhere()
	{
		$mainframe = JFactory::getApplication();
		$db = EasyBlogHelper::db();

		$filter_state = $mainframe->getUserStateFromRequest('com_easyblog.feeds.filter_state', 'filter_state', '', 'word');
		$search = $mainframe->getUserStateFromRequest('com_easyblog.feeds.search', 'search', '', 'string');
		$search = $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = $db->nameQuote('published') . '=' . $db->Quote('1');
			} else if ($filter_state == 'U') {
				$where[] = $db->nameQuote('published') . '=' . $db->Quote('0');
			}
		}

		if ($search) {
			$where[] = ' LOWER( title ) LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where)? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easyblog.feeds.filter_order', 		'filter_order', 	'created', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easyblog.feeds.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', id';

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData( $usePagination = true )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			if($usePagination)
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			else
			    $this->_data = $this->_getList($query);
		}

		return $this->_data;
	}

	/**
	 * Determines if a feed item has already been imported
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isFeedItemImported($feedId, $uid)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_feeds_history');
		$query[] = 'WHERE ' . $db->quoteName('feed_id') . '=' . $db->Quote($feedId);
		$query[] = 'AND ' . $db->quoteName('uid') . '=' . $db->Quote($uid);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$exists = $db->loadResult() > 0;

		return $exists;
	}

	/**
	 * Retrieves a list of feed items that needs to be imported
	 *
	 * @since	4.0
	 * @access	public
	 * @param	int		The total number of items to retrieve
	 * @return
	 */
	public function getPendingFeeds($limit = 1, $debug = false)
	{
		$limit = (int) $limit;

		$db = EB::db();
		$now = EB::date();
		$query = array();

		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_feeds');
		$query[] = 'WHERE ' . $db->quoteName('cron') . '=' . $db->quote(1);
		$query[] = 'AND ' . $db->quoteName('flag') . '=' . $db->quote(0);
		$query[] = 'AND ' . $db->quoteName('published') . '=' . $db->quote(EASYBLOG_POST_PUBLISHED);

		if (!$debug) {
			$query[] = 'AND (';
			$query[] = $db->quote($now->toSql()) . '>= DATE_ADD(' . $db->quoteName('last_import') . ', INTERVAL ' . $db->quoteName('interval') . ' MINUTE)';
			$query[] = 'OR';
			$query[] = $db->quoteName('last_import') . '=' . $db->Quote('0000-00-00 00:00:00');
			$query[] = ')';
		}

		$query[] = 'ORDER BY ' . $db->quoteName('last_import');
		$query[] = 'LIMIT ' . $limit;

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$feeds = array();

		foreach ($result as $row) {
			$feed = EB::table('Feed');
			$feed->bind($row);

			$feeds[] = $feed;
		}

		return $feeds;
	}

	/**
	 * Publishes feeds
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function publish( &$feeds = array(), $publish = 1 )
	{
		if( count( $feeds ) > 0 )
		{
			$db		= EasyBlogHelper::db();

			$query	= 'UPDATE ' . $db->nameQuote( '#__easyblog_feeds' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (';

			for( $i = 0; $i < count( $feeds );$i++ )
			{
				$query	.= $db->Quote( $feeds[ $i ] );

				if( next( $feeds ) !== false )
				{
					$query	.= ',';
				}
			}
			$query	.= ')';

			$db->setQuery( $query );

			if( !$db->query() )
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
	}
}