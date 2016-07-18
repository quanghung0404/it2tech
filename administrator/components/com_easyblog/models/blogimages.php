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

class EasyBlogModelBlogImages extends EasyBlogAdminModel
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

	public function exists($title, $theme)
	{
		$db = JFactory::getDBO();

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_config_images');
		$query .= 'WHERE ' . $db->quoteName('title') . '=' . $db->Quote($title);
		$query .= 'AND ' . $db->quoteName('theme') . '=' . $db->Quote($theme);

		$db->setQuery($query);

		$exists = $db->loadResult() > 0 ? true : false;

		return $exists;
	}

	public function getBlogImages()
	{
		if (empty($this->_data)) {
			$query = $this->_buildQuery();

			$this->_data = $this->_getList($this->_buildQuery(), $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
	}

	public function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere();
		$orderby = $this->_buildQueryOrderBy();

		// Get the db
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_config_images');


		$query = implode(' ', $query);

		return $query;
	}

	public function _buildQueryWhere()
	{
		$db = EB::db();

		$filter_state = $this->app->getUserStateFromRequest( 'com_easyblog.blogs.filter_state', 'filter_state', '', 'word' );
		$filter_category = $this->app->getUserStateFromRequest( 'com_easyblog.blogs.filter_category', 'filter_category', '', 'int' );
		$filter_blogger = $this->app->getUserStateFromRequest( 'com_easyblog.blogs.filter_blogger' , 'filter_blogger' , '' , 'int' );
		$filter_language = $this->app->getUserStateFromRequest( 'com_easyblog.blogs.filter_language' , 'filter_language' , '' , '' );

		$search = $this->app->getUserStateFromRequest('com_easyblog.blogs.search', 'search', '', 'string');
		$search = JString::strtolower($search);
		$search = $db->getEscaped($search);

		// Filter by source
		$source = $this->input->get('filter_source', '-1', 'default');

		$where = array();

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = 'a.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
			}
			else if ($filter_state == 'U') {
				$where[] = 'a.`published` = ' . $db->Quote(EASYBLOG_POST_UNPUBLISHED);
			}
			else if ($filter_state == 'S') {
				$where[] = 'a.`published` = ' . $db->Quote(EASYBLOG_POST_SCHEDULED);
			} else if( $filter_state == 'T' ) {
				$where[] = 'a.`published` = ' . $db->Quote(EASYBLOG_POST_TRASHED);
			}
			else if ($filter_state == 'A') {
				$where[] = 'a.`published` = ' . $db->Quote(EASYBLOG_POST_ARCHIVED);
			}
		} else {
			$where[] = 'a.`published` IN (' . $db->Quote(EASYBLOG_POST_PUBLISHED) . ',' . $db->Quote(EASYBLOG_POST_UNPUBLISHED) . ',' . $db->Quote(EASYBLOG_POST_SCHEDULED) . ',' . $db->Quote(EASYBLOG_POST_ARCHIVED) . ')';
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

		$where[] 	= ' `ispending` = ' . $db->Quote('0');

		$where 		= count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' ;

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_order', 'filter_order', 	'a.id', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_order_Dir', 'filter_order_Dir',	'DESC', 'word' );

		$orderby 			= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', ordering';

		return $orderby;
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
			$this->_total	= $this->_getListCount( $this->_buildQuery() );
		}

		return $this->_total;
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
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
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
		if( count( $blogs ) > 0 )
		{
			$db		= EasyBlogHelper::db();

			$blogs	= implode( ',' , $blogs );

			$query	= 'UPDATE ' . $db->nameQuote( '#__easyblog_post' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $blogs . ')';
			$db->setQuery( $query );

			if( !$db->query() )
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * Purges the blog image cache
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function purge()
	{
		$folders = array();
		$folders[] = JPATH_ROOT . '/' . trim($this->config->get('main_image_path'));
		$folders[] = JPATH_ROOT . '/' . trim($this->config->get('main_shared_path'));

		$total = 0;

		foreach ($folders as $folder) {

			$pattern = EBLOG_BLOG_IMAGE_PREFIX . '*';

			// Find a list of images within the folder
			$images = JFolder::files($folder, $pattern, true, true);

			foreach ($images as $image) {
				JFile::delete($image);

				$total++;
			}
		}

		return $total;
	}
}
