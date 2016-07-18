<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelBadges extends EasyDiscussAdminModel
{
	/**
	 * Blogs data array
	 *
	 * @var array
	 */
	public $_data = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	public $_pagination = null;

	/**
	 * Configuration data
	 *
	 * @var int	Total number of rows
	 **/
	public $_total;

	/**
	 * Parent ID
	 *
	 * @var integer
	 */
	public $_parent	= null;
	public $_isaccept	= null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	public function __construct()
	{
		parent::__construct();

		//get the number of events from database
		$limit = $this->app->getUserStateFromRequest('com_easydiscuss.badges.limit', 'limit', $this->app->getCfg('list_limit') , 'int');
		$limitstart = $this->input->get('limitstart', '0', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Delete badges based on user id
	 *
	 * @access public
	 *
	 * @param
	 * @return state
	 */
	public function removeBadges( $userId = null )
	{
		$db = DiscussHelper::getDBO();

		$query = 'DELETE FROM ' . $db->nameQuote( '#__discuss_badges_users' )
				. ' WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );

		$db->setQuery( $query );

		if( !$db->query() )
		{
			return false;
		}
		return true;
	}

	/**
	 * Retrieved a list of all badges available from the site.
	 * Retrieved a list of all user's achieved badges from the site.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	Array
	 * @return	Array	Array of DiscussBadges Object
	 */
	public function getSiteBadges($options = array())
	{
		$db = $this->db;
		$query = array();

		if (isset($options['user'])) {
			$query[] = 'SELECT a.*, b.`custom` FROM ' . $db->nameQuote('#__discuss_badges') . ' AS a';
			$query[] = 'INNER JOIN ' . $db->nameQuote('#__discuss_badges_users') . ' AS b';
			$query[] = 'ON b.`badge_id` = a.`id`';
			$query[] = 'AND b.`published` = ' . $db->Quote('1');
			$query[] = 'AND b.`user_id` = ' . $db->Quote($options['user']);
		} else {
			$query[] = 'SELECT a.* FROM ' . $db->nameQuote('#__discuss_badges') . ' AS a';
		}

		$query[] = 'WHERE a.`published` = ' . $db->Quote('1');

		$query = implode(' ', $query);
		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		// Binds the badges into badge table
		$badges = array();
		foreach ($result as $row) {
			$badge = ED::table('Badges');
			$badge->bind($row);

			// We'll need to re-assign the badge description if the custom message was set at the backend.
			$badge->description = isset($row->custom) ? ($row->custom != '') ? $row->custom : $badge->description : $badge->description;
			$badges[] = $badge;
		}

		return $badges;
	}

	public function getBadges( $exclusion = false )
	{
		if(empty($this->_data) )
		{
			$this->_data	= $this->_getList( $this->buildQuery( $exclusion ) , $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}

	private function buildQuery( $exclusion = false )
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere( $exclusion );
		$orderby	= $this->_buildQueryOrderBy();
		$db			= DiscussHelper::getDBO();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_badges' ) . ' AS a ';
		$query	.= $where . ' ';
		$query	.= $orderby;

		return $query;
	}

	public function _buildQueryWhere( $exclusion = false )
	{
		$db				= DiscussHelper::getDBO();

		$filter_state	= $this->app->getUserStateFromRequest( 'com_easydiscuss.badges.filter_state', 'filter_state', '', 'word' );
		$search			= $this->app->getUserStateFromRequest( 'com_easydiscuss.badges.search', 'search', '', 'string' );
		$search			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = $db->nameQuote( 'a.published' ) . '=' . $db->Quote( '1' );
			}
			else if ($filter_state == 'U' )
			{
				$where[] = $db->nameQuote( 'a.published' ) . '=' . $db->Quote( '0' );
			}
		}

		if ($search)
		{
			$where[] = ' LOWER( a.title ) LIKE \'%' . $search . '%\' ';
		}

		$exclusion	= trim( $exclusion );

		if( $exclusion )
		{
			$exclusion 	= explode( ',' , $exclusion );

			$query	= ' a.' . $db->nameQuote( 'id' ) . ' NOT IN(';

			for( $i = 0; $i < count( $exclusion); $i++ )
			{
				$query	.= $db->Quote( $exclusion[ $i ] );

				if( next( $exclusion ) !== false )
				{
					$query	.= ',';
				}
			}
			$query 	.= ')';

			$where[]	= $query;
		}

		$where 		= count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' ;

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$filter_order		= $this->app->getUserStateFromRequest( 'com_easydiscuss.badges.filter_order', 		'filter_order', 	'a.created', 'cmd' );
		$filter_order_Dir	= $this->app->getUserStateFromRequest( 'com_easydiscuss.badges.filter_order_Dir',	'filter_order_Dir',	'ASC', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}


	/**
	 * Get a list of user badge history
	 *
	 **/
	public function getBadgesHistory( $userId )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote('#__discuss_users_history') . ' '
				. 'WHERE ' . $db->nameQuote('user_id') . '=' . $db->Quote($userId) . ' '
				. 'ORDER BY ' . $db->nameQuote('id') . ' DESC '
				. 'LIMIT 0,30 ';

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		return $result;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	public function &getPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal() , $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to return the total number of rows
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Load total number of rows
		if( empty($this->_total) )
		{
			$this->_total	= $this->_getListCount( $this->buildQuery() );
		}

		return $this->_total;
	}

	public function getRules()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_rules' );
		$db->setQuery( $query );
		return $db->loadObjectList();
	}
}
