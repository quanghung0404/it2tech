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

class EasyBlogModelCategories extends EasyBlogAdminModel
{
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;

	public function __construct()
	{
		parent::__construct();


		$mainframe	= JFactory::getApplication();

		$limit			= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart		= (int) JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves the custom field group properties
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCustomFieldGroup($id)
	{
		$db = EB::db();

		$query = 'SELECT ' . $db->quoteName('group_id') . ' FROM ' . $db->quoteName('#__easyblog_category_fields_groups');
		$query .= ' WHERE ' . $db->quoteName('category_id') . '=' . $db->Quote($id);

		$db->setQuery($query);

		$id = $db->loadResult();

		$group = EB::table('FieldGroup');
		$group->load($id);

		return $group;
	}

	/**
	 * Retrieves the custom fields
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCustomFields($id)
	{
		$db = EB::db();

		$query = 'SELECT a.* FROM ' . $db->quoteName('#__easyblog_fields') . ' AS a';
		$query .= ' INNER JOIN ' . $db->quoteName('#__easyblog_category_fields_groups') . ' AS b';
		$query .= ' ON a.' . $db->quoteName('group_id') . '= b.' . $db->quoteName('group_id');
		$query .= ' WHERE b.' . $db->quoteName('category_id') . '=' . $db->Quote($id);
		$query .= ' AND a.' . $db->quoteName('state') . '=' . $db->Quote(1);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		// Get the fields library
		$lib = EB::fields();

		// Initialize the default value
		$fields = array();

		foreach ($result as $row) {
			$field = EB::table('Field');
			$field->bind($row);

			$fields[] = $field;
		}

		return $fields;
	}

	/**
	 * Method to get the total nr of the categories
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
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		$db			= EB::db();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_category' )
				. $where . ' '
				. $orderby;

		return $query;
	}

	function _buildQueryWhere()
	{
		$mainframe			= JFactory::getApplication();
		$db					= EB::db();

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' );
			}
			else if ($filter_state == 'U' )
			{
				$where[] = $db->nameQuote( 'published' ) . '=' . $db->Quote( '0' );
			}
		}

		if ($search)
		{
			$where[] = ' LOWER( title ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.filter_order', 		'filter_order', 	'lft', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', ordering';

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getData( $usePagination = true )
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
	 * Method to publish or unpublish categories
	 *
	 * @access public
	 * @return array
	 */
	public function publish(&$categories, $publish = 1)
	{
		if (!$categories) {
			return false;
		}

		$db = EB::db();

		$categories = implode(',', $categories);

		$query	= 'UPDATE ' . $db->nameQuote( '#__easyblog_category' ) . ' '
				. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote($publish) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $categories . ')';


		if (!$publish) {
			$query .= 'AND ' . $db->nameQuote('default') . '=' . $db->Quote(0);
		}

		$db->setQuery($query);

		if (!$db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Retrieves the list of categories associated with a particular blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	int
	 * @return
	 */
	public function getBlogCategories($id)
	{
		$db = EB::db();

		$query = 'SELECT a.* FROM ' . $db->nameQuote('#__easyblog_post_category') . 'as a';
		$query .= ' INNER JOIN '. $db->nameQuote('#__easyblog_category') . 'as b';
		$query .= ' on a.' . $db->nameQuote('category_id') . ' = b.' . $db->nameQuote('id') ;
		$query .= ' WHERE a.' . $db->nameQuote('post_id') . '=' . $db->Quote($id);
		$query .= ' ORDER BY b.' . $db->nameQuote('lft') . ' asc';

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return array();
		}


		$categories = array();

		foreach ($result as $row) {

			$category = EB::table('Category');
			$category->load($row->category_id);
			$category->primary = $row->primary;

			$categories[] = $category;
		}

		return $categories;
	}

	/**
	 * Returns the number of blog entries created within this category.
	 *
	 * @return int	$result	The total count of entries.
	 * @param boolean	$published	Whether to filter by published.
	 */
	function getUsedCount( $categoryId , $published = false )
	{
		$db			= EB::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_post' ) . ' as a '
				. ' inner join ' . $db->nameQuote('#__easyblog_post_category') . ' as b '
				. ' on a.id = b.post_id'
				. ' WHERE b.' . $db->nameQuote( 'category_id' ) . '=' . $db->Quote( $categoryId );

		if( $published )
		{
			$query	.= ' AND a.' . $db->nameQuote( 'published' ) . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		}

		$query	.= ' AND a.' . $db->nameQuote( 'state' ) . '=' . $db->Quote(EASYBLOG_POST_NORMAL);


		//blog privacy setting
		$my = JFactory::getUser();
		if($my->id == 0)
		    $query .= ' AND a.`access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return $result;
	}


	/**
	 * *********************************************************************
	 * These part of codes will used in dashboard categories.
	 * *********************************************************************
	 */


	function _buildQueryByBlogger( $bloggerId = 0 , $ordering = '', $search = '')
	{
		$db			= EB::db();
	    $config 	= EasyBlogHelper::getConfig();
		$sortConfig = $config->get('layout_sorting_category','latest');

	    $query	= 	'select a.`id`, a.`title`, a.`alias`, a.`created` , a.`avatar` , count(cp.`id`) as `post_count`, a.created_by';
		$query	.=  ' from `#__easyblog_category` as a';
		$query	.=  '    left join `#__easyblog_post_category` as cp on a.`id` = cp.`category_id`';

		if ($bloggerId) {
			$query	.=  ' where a.`created_by` = ' . $db->Quote($bloggerId);
		}

		if ($search) {
			$query	.=  ' and a.`title` LIKE ' . $db->Quote('%'.$search.'%');
		}

		$query	.=  ' group by (a.`id`)';

		if( !empty( $ordering ) )
		{
			$sortConfig	= $ordering;
		}

		switch( $sortConfig )
		{
			case 'count':
				$orderBy = ' ORDER BY `post_count` DESC';
				break;
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`ordering` ASC';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}
		$query  .= $orderBy;

		return $query;
	}

	/**
	 * Retrieves a list of categories created by a specific author.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCategoriesByBlogger($id = 0, $ordering = '', $search = '')
	{
		$db = EB::db();

		$query = $this->_buildQueryByBlogger($id, $ordering, $search);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$categories = array();

		foreach ($result as $row) {

			$category = EB::table('Category');
			$category->bind($row);

			$categories[] = $category;
		}

		return $categories;
	}


	/**
	 * Retrieves a list of categories created by a specific author.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCategoriesUsedByBlogger($userId)
	{
		$db = EB::db();

		$query = "select distinct a.* from `#__easyblog_category` as a";
		$query .= " inner join `#__easyblog_post_category` as b on a.`id` = b.`category_id`";
		$query .= " inner join `#__easyblog_post` as c on b.`post_id` = c.`id`";
		$query .= " where c.`created_by` = " . $db->Quote($userId);

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}


	function getPaginationByBlogger($bloggerId = 0, $search = '')
	{
		jimport('joomla.html.pagination');
		$this->_pagination	= EB::pagination( $this->getTotalByBlogger($bloggerId, $search), $this->getState('limitstart'), $this->getState('limit') );
		return $this->_pagination;
	}

	function getTotalByBlogger($bloggerId = 0, $search = '')
	{
		// Lets load the content if it doesn't already exist
		$query = $this->_buildQueryByBlogger($bloggerId, $search);
		$total = $this->_getListCount($query);

		return $total;
	}

	/**
	 * Retrieves the category params
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getParams()
	{
		static $result = array();

		if (!isset($result[$this->id])) {
			$result[$this->id] = new JRegistry($this->params);
		}

		return $result[$this->id];
	}

	/**
	 * Retrieves a list of parent categories from the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getParentCategories($contentId, $type = 'all', $isPublishedOnly = false, $isFrontendWrite = false , $exclusion = array())
	{
	    $db 	= EB::db();
	    $my     = JFactory::getUser();

	    // Get the sorting options
		$sortConfig = $this->config->get('layout_sorting_category','latest');

		$query = 'SELECT a.' . $db->quoteName('id') . ', a.' . $db->quoteName('title') . ', a.' . $db->quoteName('alias') . ', a.' . $db->quoteName('private') . ', a.' . $db->quoteName('default');
		$query .= ' FROM ' . $db->quoteName('#__easyblog_category') . ' AS a';
		$query .= ' WHERE a.' . $db->quoteName('parent_id') . '=' . $db->Quote(0);

		if ($type == 'blogger') {
			$query .= ' AND a.' . $db->quoteName('created_by') . '=' . $db->Quote($contentId);
		} else if($type == 'category') {

			$query .= ' AND a.' . $db->quoteName('id') . '=' . $db->Quote($contentId);
		}

		if ($isPublishedOnly) {
			$query .= ' AND a.' . $db->quoteName('published') . '=' . $db->Quote(1);
		}

		if( $isFrontendWrite )
		{
			$gid	= EasyBlogHelper::getUserGids();
			$gids   = '';
			if( count( $gid ) > 0 )
			{
			    foreach( $gid as $id)
			    {
			        $gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
			    }
			}

			$query .= ' and a.id not in (';
			$query .= ' select id from `#__easyblog_category` as c';
			$query .= ' where not exists (';
			$query .= '		select b.category_id from `#__easyblog_category_acl` as b';
			$query .= '			where b.category_id = c.id and b.`acl_id` = '. $db->Quote( CATEGORY_ACL_ACTION_SELECT );
			$query .= '			and b.type = ' . $db->Quote('group');
			$query .= '			and b.content_id IN (' . $gids . ')';
			$query .= '      )';
			$query .= ' and c.`private` = ' . $db->Quote( CATEGORY_PRIVACY_ACL );
			$query .= ' and c.`parent_id` = ' . $db->Quote( '0' );
			$query .= ')';
		}

		// @task: Process exclusion list.
		if( !empty( $exclusion ) )
		{
			$excludeQuery	= 'AND a.`id` NOT IN (';
			for( $i = 0 ; $i < count( $exclusion ); $i++ )
			{
				$id		= $exclusion[ $i ];

				$excludeQuery	.= $db->Quote( $id );

				if( next( $exclusion ) !== false )
				{
					$excludeQuery	.= ',';
				}
			}

			$excludeQuery	.= ')';

			$query			.= $excludeQuery;
		}

		switch($sortConfig)
		{
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`lft` ASC';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}

		$query  .= $orderBy;

	    $db->setQuery($query);
	    $result = $db->loadObjectList();

		return $result;
	}

	function getChildCategories($parentId , $isPublishedOnly = false, $isFrontendWrite = false , $exclusion = array() )
	{
	    $db 	= EB::db();
	    $my     = JFactory::getUser();
	    $config = EasyBlogHelper::getConfig();

		$sortConfig = $config->get('layout_sorting_category','latest');

		$category = EB::table('Category');
		$category->load($parentId);

	    $query = 'select a.`id`, a.`title`, a.`alias`, a.`private`, a.`parent_id`';
		$query .= ' from `#__easyblog_category` as a';
		$query .= ' WHERE a.`lft` > ' . $db->Quote($category->lft);
		$query .= ' AND a.`lft` < ' . $db->Quote($category->rgt);

		if ($isPublishedOnly) {
		    $query	.=  ' and a.`published` = ' . $db->Quote('1');
		}

		$acl = CATEGORY_ACL_ACTION_VIEW;
		if ($isFrontendWrite) {
			$acl = CATEGORY_ACL_ACTION_SELECT;
		}

		$catLib = EB::category();
		$catAccess = $catLib::genCatAccessSQL( 'a.`private`', 'a.`id`', $acl);
		$query .= ' AND (' . $catAccess . ')';


		// @task: Process exclusion list.
		if (!empty($exclusion)) {
			$excludeQuery	= 'AND a.`id` NOT IN (';
			for ($i = 0 ; $i < count( $exclusion ); $i++) {
				$id		= $exclusion[ $i ];

				$excludeQuery	.= $db->Quote( $id );

				if (next( $exclusion ) !== false) {
					$excludeQuery	.= ',';
				}
			}

			$excludeQuery	.= ')';
			$query			.= $excludeQuery;
		}

		switch ($sortConfig) {
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`lft` ASC';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}

		$query  .= $orderBy;

		// echo $query;
		// echo '<br /><br />';

	    $db->setQuery($query);
	    $result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves the primary category for the blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPrimaryCategory($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT b.* FROM ' . $db->qn('#__easyblog_post_category') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__easyblog_category') . ' AS b';
		$query[] = 'ON a.' . $db->qn('category_id') . ' = b.' . $db->qn('id');
		$query[] = 'WHERE a.' . $db->qn('primary') . '=' . $db->Quote(1);
		$query[] = 'AND a.' . $db->qn('post_id') . '=' . $db->Quote($id);

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadObject();

		if (!$result) {
			return false;
		}

		$category = EB::table('Category');
		$category->bind($result);

		return $category;
	}

	function getPrivateCategories()
	{
	    $db 	= EB::db();

	    $query	= 	'select a.`id`';
		$query	.=  ' from `#__easyblog_category` as a';
		$query	.=  ' where a.`private` = ' . $db->Quote('1');

	    $db->setQuery($query);
	    $result = $db->loadObjectList();

		return $result;
	}

	function getChildCount( $categoryId , $published = false )
	{
		$db			= EB::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_category' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $categoryId );

		if( $published )
		{
			$query	.= ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		}

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return $result;
	}

	public function getCategoriesHierarchy($useLimit = true)
	{
	    $db = EB::db();

		$limit		= '10';
		$limitstart = $this->getState('limitstart');

		$search = JRequest::getVar( 'search', '' );

		$gid	= EasyBlogHelper::getUserGids();
		$gids   = '';
		if( count( $gid ) > 0 )
		{
		    foreach( $gid as $id)
		    {
		        $gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
		    }
		}

	    $query  = 'SELECT a.*, ( SELECT COUNT(id) FROM `#__easyblog_category` WHERE `lft` < a.`lft` AND `rgt` > a.`rgt`) AS depth';
		$query  .= ' FROM `#__easyblog_category` AS a';
		$query  .= ' WHERE a.`published` = ' . $db->Quote( '1' );
		if( !empty($search) )
		    $query  .= ' AND a.`title` LIKE ' . $db->Quote( '%' . $search . '%' );

		$query .= ' and a.id not in (';
		$query .= ' select id from `#__easyblog_category` as c';
		$query .= ' where not exists (';
		$query .= '		select b.category_id from `#__easyblog_category_acl` as b';
		$query .= '			where b.category_id = c.id and b.`acl_id` = '. $db->Quote( CATEGORY_ACL_ACTION_SELECT );
		$query .= '			and b.type = ' . $db->Quote('group');
		$query .= '			and b.content_id IN (' . $gids . ')';
		$query .= '      )';
		$query .= ' and c.`private` = ' . $db->Quote( CATEGORY_PRIVACY_ACL );
		$query .= ')';

		$query  .= ' AND a.`parent_id` NOT IN (SELECT `id` FROM `#__easyblog_category` AS e WHERE e.`published` = ' . $db->Quote( '0' ) . ' AND e.`parent_id` = ' . $db->Quote( '0' ) . ' )';


		$query  .= ' ORDER BY a.`lft`';

		if ($useLimit) {
			$this->_total = $this->_getListCount($query);

			jimport('joomla.html.pagination');
			$this->_pagination	= EB::pagination( $this->_total , $limitstart , $limit );
			$query  .= ' LIMIT ' . $limitstart . ', ' . $limit;
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Removes any association of a blog post with a category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteAssociation($blogId)
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->nameQuote('#__easyblog_post_category');
		$query .= ' WHERE ' . $db->nameQuote('post_id') . '=' . $db->Quote($blogId);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * This method reduces the number of query hit on the server
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadUserSubscription($catIds, $email)
	{
		$db = EB::db();

		$query  = 'SELECT `uid` FROM `#__easyblog_subscriptions`';
		$query .= ' WHERE `uid` IN (' . implode(',', $catIds) . ')';
        $query .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY);
		$query .= ' AND `email` = ' . $db->Quote($email);

		$db->setQuery($query);
		$results = $db->loadColumn();

		$cats = array();

		if ($results) {
			foreach($results as $cat) {
				$cats[$cat] = '1';
			}
		}

		return $cats;
	}

	public function preloadPosts($catIds)
	{
		$db = EB::db();
		$config = EB::config();

		$limit = EB::call('Pagination', 'getLimit', array(EBLOG_PAGINATION_CATEGORIES));

		// Determines if this is currently on blogger mode
		$isBloggerMode = EasyBlogRouter::isBloggerMode();

		$query = array();

		$i = 1;
		foreach($catIds as $cid => $cIds) {

			$p = 'p'.$i;
			$a = 'a'.$i;
			$f = 'f'.$i;

		    $isJSGrpPluginInstalled = false;
		    $isJSGrpPluginInstalled = JPluginHelper::isEnabled( 'system', 'groupeasyblog');
		    $isEventPluginInstalled = JPluginHelper::isEnabled( 'system' , 'eventeasyblog' );
		    $isJSInstalled      = false; // need to check if the site installed jomsocial.

		    if (EB::jomsocial()->exists()) {
		    	$isJSInstalled = true;
		    }

		    $includeJSGrp = ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		    $includeJSEvent = ($isEventPluginInstalled && $isJSInstalled ) ? true : false;

		    // contribution type sql
		    $contributor = EB::contributor();
		    $contributeSQL = " AND ( ($p.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ") ";
		    if ($config->get('main_includeteamblogpost')) {
		      $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, $p);
		    }
		    if ($includeJSEvent) {
		      $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT, $p);
		    }
		    if ($includeJSGrp) {
		      $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP, $p);
		    }

		    // Test if easysocial exists on the site
			if (EB::easysocial()->exists()) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP, $p);
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT, $p);
			}

		    $contributeSQL .= ")";



			$tmp = "(select distinct $p.*, " . $db->Quote($cid) . " as `category_id`, $f.`id` as `featured`";
			$tmp .= "	from `#__easyblog_post` as $p";
			$tmp .= "		inner join `#__easyblog_post_category` as $a on $p.`id` = $a.`post_id`";
			$tmp .= " LEFT JOIN `#__easyblog_featured` AS $f";
			$tmp .= " 	ON $p.`id` = $f.`content_id` AND $f.`type` = " . $db->Quote('post');
			if (count($cIds) == 1) {
				$tmp .= " where $a.`category_id` = " . $db->Quote($cIds[0]);
			} else {
				$tmp .= " where $a.`category_id` IN (" . implode(',', $cIds) . ")";
			}
			$tmp .= " and $p.`published` = " . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$tmp .= " and $p.`state` = " . $db->Quote(EASYBLOG_POST_NORMAL);

			if ($isBloggerMode !== false) {
				$tmp .= " AND $p." . $db->qn('created_by') . " = " . $db->Quote($isBloggerMode);
			} else {

				// Get the author id based on the category menu
				$authorId = EB::getCategoryMenuBloggerId();

				if ($authorId) {
					$tmp .= " AND $p." . $db->qn('created_by') . " = " . $db->Quote($authorId);
				}
			}

			// If user is a guest, ensure that they can really view the blog post
			if ($this->my->guest) {
				$tmp .= " AND $p." . $db->qn('access') . " = " . $db->Quote(BLOG_PRIVACY_PUBLIC);
			}

			// Ensure that the blog posts is available site wide
			$tmp .= $contributeSQL;
			// $tmp .= " AND $p." . $db->qn('source_id') . " = " . $db->Quote("0");

			// Filter by language
			$language = EB::getCurrentLanguage();

			if ($language) {
				$tmp .= " AND ($p." . $db->qn('language') . "=" . $db->Quote($language) . " OR $p." . $db->qn('language') . "=" . $db->Quote('*') . " OR $p." . $db->qn('language') . "=" . $db->Quote('') . ")";
			}


			$tmp .= " order by $p.`created` desc";
			$tmp .= " limit " . $limit . ")";

			$query[] = $tmp;

			$i++;
		}

		$query = implode(' UNION ALL ', $query);

		// echo $query;exit;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		$posts = array();

		if ($results) {
			foreach($results as $row) {
				$posts[$row->category_id][] = $row;
			}
		}

		return $posts;
	}


	public function preloadActiveAuthors($catIds)
	{
		$db = EB::db();

		$query = "select distinct b.`category_id`, u.id";
		$query .= " from `#__easyblog_users` as u";
		$query .= "   inner join `#__easyblog_post` as a";
		$query .= "		on u.`id` = a.`created_by`";
		$query .= "   inner join `#__easyblog_post_category` as b";
		$query .= " 	on a.`id` = b.`post_id`";
		$query .= " where b.`category_id` in (" . implode(',', $catIds) . ")";

		$db->setQuery($query);
		$results = $db->loadObjectList();

		$members = array();

		if ($results) {

			//preload users
			$ids = array();
			foreach($results as $row) {
				$ids[] = $row->id;
			}

			EB::user($ids);

			foreach($results as $row) {
				$tbl = EB::user($row->id);
				$members[$row->category_id][] = $tbl;
			}
		}

		return $members;
	}

	public function preloadCategoryChilds($catIds)
	{
		$db = EB::db();
		$language = EB::getCurrentLanguage();

		$query = "select a.id as category_id, b.`id`, b.`title`, b.`alias`, b.`private`, b.`parent_id`";
		$query .= " from `#__easyblog_category` as a";
		$query .= " inner join `#__easyblog_category` as b on a.`lft` < b.`lft` and a.`rgt` > b.`lft`";
		$query .= " where a.`id` in (" . implode(',', $catIds) . ")";
		$query .=  " and a.`published` = " . $db->Quote('1');

		if ($language) {
			$query .= ' AND(';
			$query .= ' b.' . $db->quoteName('language') . '=' . $db->Quote($language);
			$query .= ' OR';
			$query .= ' b.' . $db->quoteName('language') . '=' . $db->Quote('');
			$query .= ' OR';
			$query .= ' b.' . $db->quoteName('language') . '=' . $db->Quote('*');
			$query .= ')';
		}

		$catLib = EB::category();
		$catAccess = $catLib::genCatAccessSQL( 'b.`private`', 'b.`id`');

		$query .= " AND ($catAccess)";

		$db->setQuery($query);

		$results = $db->loadObjectList();

		$childs = array();

		if ($results) {
			foreach($results as $child) {
				$childs[$child->category_id][] = $child;
			}
		}

		return $childs;
	}



	/**
	 * This method reduces the number of query hit on the server
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preload($postIds = array())
	{
		if (!$postIds) {
			return $postIds;
		}

		$db = EB::db();

		$query = array();
		$query = array();
		$query[] = 'SELECT a.`post_id`, b.* FROM ' . $db->qn('#__easyblog_post_category') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__easyblog_category') . ' AS b';
		$query[] = 'ON a.' . $db->qn('category_id') . ' = b.' . $db->qn('id');
		$query[] = 'WHERE a.' . $db->qn('primary') . '=' . $db->Quote(1);
		$query[] = 'AND a.' . $db->qn('post_id') . ' IN(' . implode(',', $postIds) . ')';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$primaryCategories = array();


		foreach ($result as $item) {
			$category = EB::table('Category');
			$category->bind($item);

			$primaryCategories[$item->post_id] = $category;
		}

		return $primaryCategories;
	}

	public function getCategoryTree( $sort = 'latest' )
	{
		$db     = EB::db();
		$my 	= JFactory::getUser();

        $config     = EasyBlogHelper::getConfig();

		$queryExclude	= '';
		$excludeCats	= array();


		$query	= 'SELECT a.*, ';
		$query	.= ' ( SELECT COUNT(id) FROM ' . $db->nameQuote( '#__easyblog_category' );
		$query	.= ' WHERE lft < a.lft AND rgt > a.rgt AND a.lft != ' . $db->Quote( 0 ) . ' ) AS depth ';
		$query	.= ' FROM ' . $db->nameQuote( '#__easyblog_category' ) . ' AS a ';
		$query	.= ' WHERE a.`published`=' . $db->Quote( '1' );

		// category access here
		$catLib = EB::category();
		$catAccess = $catLib::genCatAccessSQL( 'a.`private`', 'a.`id`');

		$query .= ' AND (' . $catAccess . ')';


		switch( $sort )
		{
		    case 'ordering':
		 		$query	.= ' ORDER BY `lft`, `ordering`';
		 		break;
			case 'alphabet':
		 		$query	.= ' ORDER BY `title`, `lft`';
		 		break;
		    case 'latest':
		    default:
		        $query	.= ' ORDER BY `rgt` DESC';
				break;
		}

		// echo $query;

		$db->setQuery( $query );

		$rows		= $db->loadObjectList();
		$total		= count( $rows );
		$categories = array();

		for( $i = 0; $i < $total; $i++ )
		{
			$category 	= EB::table('Category');
			$category->bind( $rows[ $i ] );
			$category->depth	= $rows[ $i ]->depth;
			$categories[]		= $category;
		}
		return $categories;
	}
}
