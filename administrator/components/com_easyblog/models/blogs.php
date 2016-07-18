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

class EasyBlogModelBlogs extends EasyBlogAdminModel
{
	public $_data = null;
	public $_pagination = null;
	public $_total;

	public function __construct()
	{
		parent::__construct();

		// Get the number of events from database
		$limit = $this->app->getUserStateFromRequest('com_easyblog.blogs.limit', 'limit', $this->app->getCfg('list_limit') , 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves a list of blog posts created on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getData($userId = null)
	{
		if (!$this->_data) {
			$query = $this->_buildDataQuery($userId);
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Builds the query for the blogs listing
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function _buildDataQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildDataQueryWhere();

		// Get the db
		$db = EB::db();

		$query	= 'SELECT DISTINCT a.*';
		$query	.= ' FROM ' . $db->nameQuote( '#__easyblog_post' ) . ' AS a ';

		// Get the current state
		$state = $this->app->getUserStateFromRequest( 'com_easyblog.blogs.filter_state', 'filter_state', '', 'word' );

		if ($state == 'F') {
			$query 	.= ' INNER JOIN #__easyblog_featured AS `featured`';
			$query	.= ' ON a.`id` = featured.`content_id` AND featured.`type` = "post"';
		}

		// Always join with the category table
		$query .= ' LEFT JOIN ' . $db->quoteName('#__easyblog_post_category') . ' AS cat';
		$query .= ' ON a.' . $db->quoteName('id') . ' = cat.' . $db->quoteName('post_id');

		// Filter by tags
		$tag = $this->input->get('tagid', 0, 'int');

		if ($tag) {
			$query	.= ' INNER JOIN #__easyblog_post_tag AS b ';
			$query	.= 'ON a.`id`=b.`post_id` AND b.`tag_id`=' . $db->Quote($tag);
		}

		$query	.= ' LEFT JOIN #__easyblog_featured AS f ';
		$query	.= ' ON a.`id` = f.`content_id` AND f.`type`="post"';

		$query	.= $where;

		$ordering = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order', 'filter_order', 'a.id', 'cmd');
		$direction = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order_Dir', 'filter_order_Dir',	'DESC', 'word');

		$query .= ' ORDER BY '. $ordering .' ' . $direction .', ordering';

		return $query;
	}

	/**
	 * Builds the where statement
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function _buildDataQueryWhere()
	{
		$db = EB::db();

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_state', 'filter_state', '', 'word' );
		$filter_category = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_category', 'filter_category', '', 'int' );
		$filter_blogger = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_blogger' , 'filter_blogger' , '' , 'int' );
		$filter_language = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_language' , 'filter_language' , '' , '' );

		$search = $this->app->getUserStateFromRequest('com_easyblog.blogs.search', 'search', '', 'string');
		$search = JString::strtolower($search);
		$search = $db->getEscaped($search);

		// Filter by source
		$source = $this->input->get('filter_source', '-1', 'default');

		$where = array();

		switch($filter_state) {
			case 'U':
				// Unpublished posts
				$where[] = 'a.`published` = ' . $db->Quote(EASYBLOG_POST_UNPUBLISHED);
				$where[] = 'a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
				break;

			case 'S':
				// Scheduled posts
				$where[] = 'a.`published` = ' . $db->Quote(EASYBLOG_POST_SCHEDULED);
				$where[] = 'a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
				break;

			case 'T':
				// trashed posts
				$where[] = 'a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_TRASHED);
				break;

			case 'A':
				// archived posts
				$where[] = 'a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_ARCHIVED);
				break;

			case 'P':
				// Published posts only
				$where[] = 'a.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
				$where[] = 'a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
				break;

			default:
				$where[] = 'a.' . $db->qn('published') . ' IN (' . $db->Quote(EASYBLOG_POST_PUBLISHED) . ',' . $db->Quote(EASYBLOG_POST_UNPUBLISHED) . ',' . $db->Quote(EASYBLOG_POST_SCHEDULED) . ')';
				$where[] = 'a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
				break;
		}

		if ($source != '-1') {
			$where[]	= 'a.' . $db->nameQuote( 'source' ) . '=' . $db->Quote( $source );
		}

		if ($filter_category) {
		    $where[] = ' cat.`category_id` = ' . $db->Quote($filter_category);
		}

		if( $filter_blogger )
		{
			$where[] = ' a.`created_by` = ' . $db->Quote( $filter_blogger );
		}

		if( $filter_language && $filter_language != '*')
		{
			$where[]	= ' a.`language`= ' . $db->Quote( $filter_language );
		}

		if ($search)
		{
			$where[] = ' LOWER( a.title ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' ;

		return $where;
	}

	/**
	 * Method to return the total number of rows
	 *
	 * @access public
	 * @return integer
	 */
	public function getDataTotal()
	{
		// Load total number of rows
		if( empty($this->_total) )
		{
			$this->_total	= $this->_getListCount( $this->_buildDataQuery() );
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	public function &getDataPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getDataTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/*
	 * common method used in frontend and backend
	 */
	public function _buildQueryOrderBy()
	{
		$ordering = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order', 'filter_order', 'a.id', 'cmd');
		$direction = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order_Dir', 'filter_order_Dir',	'DESC', 'word');

		$query = ' ORDER BY '. $ordering .' ' . $direction .', ordering';

		return $query;
	}

	/**
	 * Retrieves a list of scheduled posts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getScheduledPosts($limit = 5)
	{
		$db = EB::db();

		// Get the current date
		$date = EB::date();

		$query 	= array();
		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_post');
		$query[] = 'WHERE ' . $db->quoteName('publish_up') . '<=' . $db->Quote($date->toSql());
		$query[] = 'AND ' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_SCHEDULED);
		$query[] = 'AND ' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query[] = 'ORDER BY ' . $db->quoteName('id');

		if ($limit) {
			$query[] = 'LIMIT ' . $limit;
		}

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$posts = array();

		foreach ($result as $row) {
			$blog = EB::table('Blog');
			$blog->bind($row);

			$posts[] = $blog;
		}

		return $posts;
	}

	/**
	 * Unpublishes blog posts that are scheduled to be unpublished
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unpublishScheduledPosts()
	{
		$db = EB::db();
		$date = EB::date();

		$query = array();

		$query[] = 'UPDATE ' . $db->quoteName('#__easyblog_post');
		$query[] = 'SET ' . $db->quoteName('published') . ' = ' . $db->Quote(EASYBLOG_POST_UNPUBLISHED);
		$query[] = 'WHERE ' . $db->quoteName('publish_down') . ' > ' . $db->quoteName('publish_up');
		$query[] = 'AND ' . $db->quoteName('publish_down') . ' <= ' . $db->Quote($date->toSql());
		$query[] = 'AND ' . $db->quoteName('publish_down') . ' != ' . $db->Quote('0000-00-00 00:00:00');
		$query[] = 'AND ' . $db->quoteName('published') . ' = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND ' . $db->quoteName('state') . ' = ' . $db->Quote(EASYBLOG_POST_NORMAL);


		$query = implode(' ', $query);
		$db->setQuery($query);

		return $db->Query();
	}

	public function publish( &$blogs = array(), $publish = 1 )
	{
		if (count( $blogs ) > 0) {
			$db		= EasyBlogHelper::db();

			$blogs	= implode( ',' , $blogs );

			$query	= 'UPDATE ' . $db->nameQuote( '#__easyblog_post' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $blogs . ')';
			$db->setQuery( $query );
			$state = $db->query();

			if (! $state) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
		return false;
	}

	public function getTotalPublished($uid)
	{
		$db		= EB::db();
		$query	= 'SELECT COUNT(1) AS `total`' .
				  ' FROM ' . $db->nameQuote( '#__easyblog_post' ) .
				  ' WHERE ' . $db->nameQuote( 'created_by' ) . '=' . $db->Quote( $uid ) .
				  ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED) .
				  ' AND ' . $db->nameQuote( 'state' ) . '=' . $db->Quote(EASYBLOG_POST_NORMAL);


		//blog privacy setting
		$my = JFactory::getUser();
		if ($my->id == 0) {
		    $query .= ' AND `access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		}

		$db->setQuery( $query );

		$result	= $db->loadResult();
		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Retrieves the total number of pending posts from the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalPending()
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_post');
		$query[] = 'WHERE ' . $db->qn('published') . '=' . $db->Quote(EASYBLOG_POST_PENDING);
		$query[] = ' AND ' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		$query = implode(' ', $query);
		$db->setQuery($query);

		$total = $db->loadResult();

		return $total;
	}

	public function getTotalUnpublished( $uid )
	{
		$db		= EB::db();
		$query	= 'SELECT COUNT(1) AS `total`' .
				  ' FROM ' . $db->nameQuote( '#__easyblog_post' ) .
				  ' WHERE ' . $db->nameQuote( 'created_by' ) . '=' . $db->Quote( $uid ) .
				  ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote(EASYBLOG_POST_UNPUBLISHED) .
				  ' AND ' . $db->nameQuote( 'state' ) . '=' . $db->Quote(EASYBLOG_POST_NORMAL);


		//blog privacy setting
		$my = JFactory::getUser();
		if($my->id == 0)
		    $query .= ' AND `access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/*
	 * ****************************************
	 * merged from frontend.
	 * ****************************************
	 */

	/**
	 * Retrieves a list of blog posts on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBlogs($userId = null, $options = array())
	{
		$query = $this->_buildQuery($userId);

		// Apply limit for the blogs
		$query	.= ' LIMIT ' . $this->getState('limitstart') . ',' . $this->getState('limit');

		$db = JFactory::getDBO();
		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$posts = array();

		foreach ($result as $row) {
			$post = EB::table('Blog');

			$post->bind($row);

			$posts[] = $post;
		}

		return $posts;
	}

	public function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere();
		$db = EB::db();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_post' )
				. $where;

		$ordering = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order', 'filter_order', 'id', 'cmd');
		$direction = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order_Dir', 'filter_order_Dir',	'DESC', 'word');

		$query .= ' ORDER BY '. $ordering .' ' . $direction .', ordering';

		return $query;
	}

	public function _buildQueryWhere()
	{
		$mainframe			= JFactory::getApplication();
		$db					= EB::db();

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		//blog privacy setting
		$my = JFactory::getUser();
		if ($my->id == 0) {
			$where[] = $db->nameQuote('access') . '=' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		}

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = $db->nameQuote( 'published' ) . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);

			} else if ($filter_state == 'U') {
				$where[] = $db->nameQuote( 'published' ) . '=' . $db->Quote(EASYBLOG_POST_UNPUBLISHED);

			}
		}

		if ($search) {
			$where[] = ' LOWER( title ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
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
		if (empty($this->_total)) {
			$db 			= JFactory::getDBO();
			$query			= 'SELECT COUNT(1) FROM `#__easyblog_post`';
			$db->setQuery($query);

			$this->_total 	= $db->loadResult();
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination	= EB::pagination(  $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

}
