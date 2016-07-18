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

require_once(__DIR__ . '/model.php');

class EasyBlogModelOauth extends EasyBlogAdminModel
{
	public $_data	= null;
	public $_total = null;
	public $_pagination = null;

	public function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		$limit		= ($mainframe->getCfg('list_limit') == 0) ? 5 : $mainframe->getCfg('list_limit');
	    $limitstart = JRequest::getInt('limitstart', 0, 'REQUEST');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination	= EB::pagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	function _buildQuery( $userId )
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere( $userId );
		$orderby	= $this->_buildQueryOrderBy();
		$db			= EB::db();

		$query		= 'SELECT a.* FROM ' . $db->nameQuote( '#__easyblog_oauth' ) . ' AS a '
					. $where . ' '
					. $orderby;

		return $query;
	}

	function _buildQueryWhere( $userId )
	{
		$mainframe			= JFactory::getApplication();
		$db					= EB::db();

		$where[]	= 'a.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );

		$where		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}


	function _buildQueryOrderBy()
	{

		$orderby 	= ' ORDER BY a.`id`';

		return $orderby;
	}

	/**
	 * Method to get teamblog item data
	 *
	 * @access public
	 * @return array
	 */
	function getConsumers( $userId )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery( $userId );

			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Determines if a post has been shared before previously.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isShared($postId, $oauthId)
	{
		$db = EB::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_oauth_posts');
		$query .= ' WHERE ' . $db->quoteName('oauth_id') . '=' . $db->Quote($oauthId);
		$query .= ' AND ' . $db->quoteName('post_id') . '=' . $db->Quote($postId);

	    $db->setQuery($query);
		$result = $db->loadResult();

		return $result > 0;
	}
	/**
	 * Determines if a specific client has been associated with the system
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */
	public function isAssociated($client)
	{
		$db 		= EB::db();
		$query		= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_oauth') . ' '
					. 'WHERE ' . $db->nameQuote('system') . '=' . $db->Quote( 1 ) . ' '
					. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote($client) . ' '
					. 'AND ' . $db->nameQuote( 'access_token' ) . ' !=""';
		$db->setQuery( $query );

		return $db->loadResult() > 0;
	}

	/**
	 * Retrieves a list of Twitter oauth accesses on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTwitterAccounts()
	{
		// Find all oauth accounts
		$db = EB::db();

		$query 	= 'SELECT * FROM ' . $db->quoteName('#__easyblog_oauth');
		$query	.= ' WHERE ' . $db->quoteName('type') . '=' . $db->Quote('twitter');
		$query 	.= ' AND ' . $db->quoteName('system') . '=' . $db->Quote(0);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Method to get the total nr of the team
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Retrieves a list of oauth linked accounts for a particular user
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserClients($userId = null)
	{
		$user = JFactory::getUser($userId);

		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_oauth');
		$query[] = 'WHERE ' . $db->qn('user_id') . '=' . $db->Quote($user->id);
		$query[] = 'AND ' . $db->qn('system') . '=' . $db->Quote(0);

		$db->setQuery($query);

		$clients = $db->loadObjectList();

		return $clients;
	}

	/**
	 * Retrieves a list of oauth linked accounts for a particular user
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSystemClients($type = null)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_oauth');
		$query[] = 'WHERE ' . $db->qn('system') . '=' . $db->Quote(1);

		if ($type) {
			$query[] = 'AND ' . $db->qn("type") . '=' . $db->Quote($type);
		}

		$db->setQuery($query);

		$clients = $db->loadObjectList();

		return $clients;
	}

	/**
	 * Determines if the blog post is associated with a twitter previously
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLastTweetImport($oauthId)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT ' . $db->quoteName('id_str') . ' FROM ' . $db->quoteName('#__easyblog_twitter_microblog');
		$query[] = 'WHERE ' . $db->quoteName('oauth_id') . ' = ' . $db->Quote($oauthId);
		$query[] = 'ORDER BY ' . $db->quoteName('created') . ' DESC';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}
}
