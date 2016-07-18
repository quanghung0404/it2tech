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

class EasyBlogModelCategory extends EasyBlogAdminModel
{
	/**
	 * Category total
	 *
	 * @public integer
	 */
	public $_total = null;

	/**
	 * Pagination object
	 *
	 * @public object
	 */
	public $_pagination = null;

	/**
	 * Category data array
	 *
	 * @public array
	 */
	public $_data = null;

	public function __construct()
	{
		parent::__construct();


		$mainframe	= JFactory::getApplication();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.categories.limit', 'limit', $this->app->getCfg('list_limit') , 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

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
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
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
	public function _buildQuery( $publishedOnly = false )
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere( $publishedOnly );
		$orderby	= $this->_buildQueryOrderBy();
		$db			= EB::db();

		$query	= 'SELECT a.*, ';
		$query	.= '( SELECT COUNT(id) FROM ' . $db->nameQuote( '#__easyblog_category' ) . ' ';
		$query	.= 'WHERE lft < a.lft AND rgt > a.rgt AND a.lft != ' . $db->Quote( 0 ) . ' ) AS depth ';
		$query	.= 'FROM ' . $db->nameQuote( '#__easyblog_category' ) . ' AS a ';
		$query	.= $where;

		if( $publishedOnly )
		{
		    $query  .= ' AND a.published = ' . $db->Quote('1');
			$query  .= ' AND a.`parent_id` NOT IN (SELECT `id` FROM `#__easyblog_category` AS e WHERE e.`published` = ' . $db->Quote( '0' ) . ' AND e.`parent_id` = ' . $db->Quote( '0' ) . ' )';
		}

		$query	.= $orderby;

		//echo $query;exit;

		return $query;
	}

	public function _buildQueryWhere()
	{
		$mainframe			= JFactory::getApplication();
		$db					= EB::db();

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		$where[]            = $db->nameQuote( 'lft' ) . '!=' . $db->Quote( 0 );
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

	public function _buildQueryOrderBy()
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
	public function getData( $usePagination = true, $publishedOnly = false )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery( $publishedOnly );
			if($usePagination)
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			else
			    $this->_data = $this->_getList($query);
		}

		return $this->_data;
	}

	/**
	 * Retrieves a list of parent categories on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getParentCategories($contentId, $type = 'all', $isPublishedOnly = false, $isFrontendWrite = false , $exclusion = array() )
	{
	    $db 	= EB::db();
	    $my     = JFactory::getUser();
	    $config = EasyBlogHelper::getConfig();

		$sortConfig = $config->get('layout_sorting_category','latest');

	    $query	= 	'select a.`id`, a.`title`, a.`alias`, a.`private`';
		$query	.=  ' from `#__easyblog_category` as a';
		$query	.=  ' where a.parent_id = ' . $db->Quote('0');

		if($type == 'blogger')
		{
			$query	.=  ' and a.created_by = ' . $db->Quote($contentId);
		}
		else if($type == 'category')
		{
		    $query	.=  ' and a.`id` = ' . $db->Quote($contentId);
		}

		if( $isPublishedOnly )
		{
		    $query	.=  ' and a.`published` = ' . $db->Quote('1');
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

	/**
	 * Retrieves a list of categories from a specific parent category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getChildCategories($parentId, $isPublishedOnly = false, $isFrontendWrite = false , $exclusion = array())
	{
	    $db 	= EB::db();
	    $my     = JFactory::getUser();
	    $config = EasyBlogHelper::getConfig();

		$sortConfig = $config->get('layout_sorting_category','latest');

	    $query	= 	'select a.`id`, a.`title`, a.`alias`, a.`private`';

		$query .= ", (select count(1) from `#__easyblog_post_category` as `pcat`";
		$query .= " INNER JOIN `#__easyblog_post` as p on pcat.`post_id` = p.`id` and p.`published` = " . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= " 	and p.`state` = " . $db->Quote(EASYBLOG_POST_NORMAL);
		$query .= " 		where pcat.`category_id` IN (select c.`id` from `#__easyblog_category` as c where (c.`id` = a.`id` OR c.`parent_id` = a.`id`))";
		$query .= " ) as cnt";

		$query	.=  ' from `#__easyblog_category` as a';
		$query	.=  ' where a.parent_id = ' . $db->Quote($parentId);

		if ($isPublishedOnly) {
		    $query	.=  ' and a.`published` = ' . $db->Quote('1');
		}

		// Check the current site language
		$language = EB::getCurrentLanguage();

		if ($language) {
			$query .= 'AND(';
			$query .= ' a.' . $db->quoteName('language') . '=' . $db->Quote($language);
			$query .= ' OR';
			$query .= ' a.' . $db->quoteName('language') . '=' . $db->Quote('');
			$query .= ' OR';
			$query .= ' a.' . $db->quoteName('language') . '=' . $db->Quote('*');
			$query .= ')';
		}

		if ($isFrontendWrite) {
			$gid = EB::getUserGids();
			$gids = '';

			if(count($gid) > 0) {
			    foreach ($gid as $id) {
			        $gids .= (empty($gids)) ? $db->Quote($id) : ',' . $db->Quote($id);
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
			$query .= ' and c.`parent_id` = ' . $db->Quote( $parentId );
			$query .= ')';
		}

		// @task: Process exclusion list.
		if (!empty($exclusion)) {
			$excludeQuery = 'AND a.`id` NOT IN (';
			for ($i = 0; $i < count($exclusion); $i++ ) {
				$id = $exclusion[ $i ];

				$excludeQuery .= $db->Quote( $id );

				if (next($exclusion) !== false) {
					$excludeQuery .= ',';
				}
			}

			$excludeQuery .= ')';

			$query .= $excludeQuery;
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

	/**
	 * Method to publish or unpublish categories
	 *
	 * @access public
	 * @return array
	 */
	public function publish( &$categories = array(), $publish = 1 )
	{
		if( count( $categories ) > 0 )
		{
			$db		= EB::db();

			$tags	= implode( ',' , $categories );

			$query	= 'UPDATE ' . $db->nameQuote( '#__easyblog_category' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $tags . ')';
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
	 * Returns the number of blog entries created within this category.
	 *
	 * @return int	$result	The total count of entries.
	 * @param boolean	$published	Whether to filter by published.
	 */
	public function getUsedCount( $categoryId , $published = false )
	{
		$db			= EB::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_post' ) . ' as a '
				. ' inner join ' . $db->nameQuote('#__easyblog_post_category') . ' as b '
				. ' on a.`id` = b.`post_id`'
				. ' WHERE b.' . $db->nameQuote( 'category_id' ) . '=' . $db->Quote( $categoryId );

		if( $published )
		{
			$query	.= ' AND a.' . $db->nameQuote( 'published' ) . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		}

		$query	.= ' AND a.' . $db->nameQuote( 'state' ) . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return $result;
	}

	public function getChildCount( $categoryId , $published = false )
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

	public function getAllCategories($parentOnly = false)
	{
		$db = EB::db();

	    $query = 'SELECT `id`, `title` FROM `#__easyblog_category`';

	    if ($parentOnly) {
	    	$query .= ' WHERE `parent_id`=' . $db->Quote(0);
	    }

		$query .= ' ORDER BY `title`';

	    $db->setQuery($query);

	    $result = $db->loadObjectList();

	    return $result;
	}

	/**
	 * Delete existing group custom field mapping with the category id
	 *
	 * @since	4.0
	 * @access	public
	 * @param	int		The category id
	 * @return
	 */
	public function deleteExistingFieldMapping($categoryId)
	{
		$db 	= EB::db();

		$query 	= 'DELETE FROM ' . $db->quoteName('#__easyblog_category_fields_groups') . ' WHERE ' . $db->quoteName('category_id') . '=' . $db->Quote($categoryId);
		$db->setQuery($query);

		return $db->Query();
	}


    public function getCategorySubscribers($categoryId)
    {
        $db = EB::db();

        $query  = "SELECT *, 'categorysubscription' as `type` FROM `#__easyblog_subscriptions`";
        $query .= " WHERE `uid` = " . $db->Quote($categoryId);
        $query .= " AND `utype` = " . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY);

        //echo $query . '<br/><br/>';

        $db->setQuery($query);
        $result = $db->loadObjectList();

        return $result;
    }

	/**
	 * Ensure that there are no other categories that are default.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
    public function resetDefault()
    {
        $db = EB::db();

        $query = array();
        $query[] = 'UPDATE ' . $db->qn('#__easyblog_category');
        $query[] = 'SET ' . $db->qn('default') . '=' . $db->Quote(0);
        $query[] = 'WHERE ' . $db->qn('default') . '=' . $db->Quote(1);
        $query = implode(' ', $query);

        $db->setQuery($query);
        return $db->Query();
    }

	public function getDefaultCategoryId()
	{
		$db = EB::db();

		$gid  = EB::getUserGids();
		$gids = '';

		if (count($gid) > 0) {
			foreach ($gid as $id) {
				$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
			}
		}

		$query	= 'SELECT a.id';
		$query	.= ' FROM `#__easyblog_category` AS a';
		$query	.= ' WHERE a.`published` = ' . $db->Quote( '1' );
		$query	.= ' AND a.`default` = ' . $db->Quote( '1' );
		$query	.= ' and a.id not in (';
		$query	.= ' 	select id from `#__easyblog_category` as c';
		$query	.= ' 	where not exists (';
		$query	.= '			select b.category_id from `#__easyblog_category_acl` as b';
		$query	.= '				where b.category_id = c.id and b.`acl_id` = '. $db->Quote( CATEGORY_ACL_ACTION_SELECT );
		$query	.= '				and b.type = ' . $db->Quote('group');
		$query	.= '				and b.content_id IN (' . $gids . ')';
		$query	.= '		)';
		$query	.= '	and c.`private` = ' . $db->Quote( CATEGORY_PRIVACY_ACL );
		$query	.= '	)';
		$query	.= ' AND a.`parent_id` NOT IN (SELECT `id` FROM `#__easyblog_category` AS e WHERE e.`published` = ' . $db->Quote( '0' ) . ' AND e.`parent_id` = ' . $db->Quote( '0' ) . ' )';
		$query	.= ' ORDER BY a.`lft` LIMIT 1';

		$db->setQuery( $query );
		$result = $db->loadResult();

		return ( empty( $result ) ) ? '0' : $result ;
	}

	public function getTeamBlogCount( $catId )
	{
		$db = EB::db();
		$isBloggerMode  = EasyBlogRouter::isBloggerMode();

		$query = 'select count(1) from `#__easyblog_post` as a';
		$query .= '  inner join `#__easyblog_post_category` as b';
		$query .= '    on a.`id` = b.`post_id`';

		$query .= ' where b.category_id = ' . $db->Quote($catId);
		$query .= '  and a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);

		if($isBloggerMode !== false)
			$query	.= '  and a.`created_by` = ' . $db->Quote($isBloggerMode);

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? '0' : $result;
	}

	/**
	 * Retrieve the total number of posts in a category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalPostCount($ids, $options = array())
	{
		$db	= EB::db();

		// Determines if this is currently on blogger mode
		$isBloggerMode = EasyBlogRouter::isBloggerMode();

		if (!$ids || empty($ids)) {
			return false;
		}

		// Ensure that the id's is an array
		if (!is_array($ids)) {
			$ids = array($ids);
		}

		$bloggerId = isset($options['bloggerId']) && $options['bloggerId'] ? $options['bloggerId'] : '';

		// Since the ids passed in is always an array, we need to implode it
		$categoryId = implode(',', $ids);

		// Build the query to count the posts
		$query = 'SELECT COUNT(1) AS ' . $db->quoteName('cnt');

		$query .= ' FROM ' . $db->quoteName('#__easyblog_post_category') . ' AS ' . $db->quoteName('a');
		$query .= ' INNER JOIN ' . $db->quoteName('#__easyblog_post') . ' AS ' . $db->quoteName('b');
		$query .= ' ON a.' . $db->quoteName('post_id') . ' = b.' . $db->quoteName('id');
		$query .= ' AND b.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);

		$query .= ' INNER JOIN ' . $db->quoteName('#__easyblog_category') . ' AS ' . $db->quoteName('c');
		$query .= ' ON a.' . $db->quoteName('category_id') . ' = c.' . $db->quoteName('id');

		// If the user is a guest, ensure that we only fetch public posts
		if ($this->my->guest && !$bloggerId) {
			$query .= ' AND b.' . $db->quoteName('access') . '=' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		}

		// If this is on blogger mode, fetch items created by the current author only
		if ($isBloggerMode !== false) {
			$query .= ' AND b.' . $db->quoteName('created_by') . '=' . $db->Quote($isBloggerMode);
		} else if ($bloggerId) {
			$query .= ' AND b.' . $db->quoteName('created_by') . '=' . $db->Quote($bloggerId);
		} else {

			// Get the author id based on the category menu
			$authorId = EB::getCategoryMenuBloggerId();

			if ($authorId) {
				$query .= ' AND b.' . $db->quoteName('created_by') . '=' . $db->Quote($authorId);
			}
		}

		// We only want to retrieve the category provided and its child cats
		$query .= ' WHERE ( c.' . $db->quoteName('id') . ' IN (' . $categoryId . ') or c.' . $db->quoteName('parent_id') . ' IN (' . $categoryId . '))';


		$query .= ' AND b.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);


		// If multi lingual is enabled, we should only fetch posts with the correct language
		$language = EB::getCurrentLanguage();

		if ($language) {
			$query	.= ' AND (';
			$query	.= ' b.`language`=' . $db->Quote($language);
			$query	.= ' OR b.`language`=' . $db->Quote( '' );
			$query	.= ' OR b.`language`=' . $db->Quote( '*' );
			$query	.= ' )';
		}


		// echo $query;

		$db->setQuery($query);
		$result = $db->loadResultArray();

		if (!$result) {
			return 0;
		}

		return array_sum($result);
	}

	/**
	 * Methods for frontend.
	 */

	/**
	 * Retrieves a list of parent categories with posts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getParentCategoriesWithPost($accessible = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();

		// Build the initial query
		$query = 'SELECT * FROM ' . $db->quoteName('#__easyblog_category');
		$query .= ' WHERE ' . $db->quoteName('published') . '=' . $db->Quote(1);
		$query .= ' AND ' . $db->quoteName('parent_id') . '=' . $db->Quote(0);

		// If caller provides us with specific category id's that are accessible by the user
		if (!empty($accessible)) {

			$tmp = '';

			foreach ($accessible as $category) {

				$tmp .= $db->Quote($category->id);

				if (next($accessible) !== false) {
					$tmp .= ',';
				}
			}

			$query .= ' AND ' . $db->quoteName('id') . ' IN(' . $tmp . ')';
		}

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return array();
		}

		$categories = array();

		foreach ($result as &$row) {

			$row->childs = null;

			// Build the childs for this category
			EB::buildNestedCategories($row->id, $row);

			// Emm... what is this?
			$catIds   = array();
			$catIds[] = $row->id;
			EB::accessNestedCategoriesId($row, $catIds);

			// Get the total number of posts for this category
			$row->cnt = $this->getTotalPostCount($catIds);

			if ($row->cnt > 0) {
				$categories[] = $row->id;
			}
		}

		return $categories;
	}

	public function _getParentIdsWithPost( $accessibleCatsIds = array() )
	{
		$db	= EB::db();
		$my = JFactory::getUser();

		$query	= 'select * from `#__easyblog_category`';
		$query	.= ' where `published` = 1';
		$query	.= ' and `parent_id` = 0';

		if( ! empty( $accessibleCatsIds ) )
		{
			$catAccessQuery	= ' `id` IN(';

			if( !is_array( $accessibleCatsIds ) )
			{
				$accessibleCatsIds	= array( $accessibleCatsIds );
			}

			for( $i = 0; $i < count( $accessibleCatsIds ); $i++ )
			{
				$catAccessQuery	.= $db->Quote( $accessibleCatsIds[ $i ]->id );

				if( next( $accessibleCatsIds ) !== false )
				{
					$catAccessQuery	.= ',';
				}
			}
			$catAccessQuery .= ')';

			$query	.= ' and ' . $catAccessQuery;
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$validCat   = array();

		if(count($result) > 0)
		{
			for($i = 0; $i < count($result); $i++)
			{
				$item =& $result[$i];

				$item->childs = null;
				EasyBlogHelper::buildNestedCategories($item->id, $item);

				$catIds     = array();
				$catIds[]   = $item->id;
				EasyBlogHelper::accessNestedCategoriesId($item, $catIds);

				$item->cnt   = $this->getTotalPostCount($catIds);

				if($item->cnt > 0)
				{
					$validCat[] = $item->id;
				}

			}
		}

		return $validCat;
	}

	/**
	 * Retrieves a list of categories
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCategories($sort = 'latest', $hideEmptyPost = true, $limit = 0 , $inclusion = array(), $pagination = true)
	{
		$db	= EB::db();

		//blog privacy setting
		$my = JFactory::getUser();

		// Determines if the current access is on blogger mode.
		$isBloggerMode = EasyBlogRouter::isBloggerMode();

		$orderBy = '';
		$limit = ($limit == 0) ? $this->getState('limit') : $limit;
		$limitstart = $this->input->get('limitstart', $this->getState('limitstart'), 'int');

		$limitSQL = '';

		if ($pagination) {
			$limitSQL = ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$cond = array();

		// Respect inclusion categories
		if (!empty($inclusion)) {

			$inclusionQuery	= ' AND a.`id` IN(';

			if (!is_array( $inclusion)) {
				$inclusion	= array( $inclusion );
			}

			$inclusion	= array_values($inclusion);

			for ($i = 0; $i < count( $inclusion ); $i++) {
				$inclusionQuery	.= $inclusion[ $i ];

				if (next( $inclusion ) !== false) {
					$inclusionQuery	.= ',';
				}
			}
			$inclusionQuery	.= ')';

			$cond[] = $inclusionQuery;
		}

		// If the request is on blogger mode, only retrieve entries created by the specific author
		if ($isBloggerMode !== false) {
			$cond[] = ' AND a.' . $db->quoteName('created_by') . '=' . $db->Quote($isBloggerMode);
		}

		// Get the current language
		$language = EB::getCurrentLanguage();
		if ($language) {
			$cond[] = ' AND (a.' . $db->quoteName('language') . '=' . $db->Quote($language) . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('*') . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('') . ')';
		}

		//sorting
		switch($sort)
		{
			case 'popular' :
				$orderBy	= ' ORDER BY `cnt` DESC';
				break;
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

		// sql for category access
		$catLib = EB::category();
		$catAccess = $catLib::genCatAccessSQL( 'a.`private`', 'a.`id`');
		// $catAccess = '';

		// conditions
		$condQuery = ' WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$condQuery .= ' and a.`parent_id` = 0';
		$condQuery .= ' and (';
		$condQuery .= $catAccess;
		$condQuery .= ' )';


		$language = EB::getCurrentLanguage();

		if ($language) {
			$condQuery .= 'AND(';
			$condQuery .= ' a.' . $db->quoteName('language') . '=' . $db->Quote($language);
			$condQuery .= ' OR';
			$condQuery .= ' a.' . $db->quoteName('language') . '=' . $db->Quote('');
			$condQuery .= ' OR';
			$condQuery .= ' a.' . $db->quoteName('language') . '=' . $db->Quote('*');
			$condQuery .= ')';
		}

		// joining the ANDs condition
		$tmp = implode(' ', $cond );
		$condQuery .= $tmp;

		$totalSQL = "";
		$mainSQL = "";

		// header for main query here.
		$headSQL = "select SQL_CALC_FOUND_ROWS a.*, count(pcat.`id`) as `cnt`";
		$headSQL .= " from `#__easyblog_category` as a";
		$headSQL .= " 	inner join `#__easyblog_category` as c on a.`lft` <= c.`lft` and a.`rgt` >= c.`rgt`";
		$headSQL .= " 	left join `#__easyblog_post_category` as `pcat` on c.`id` = pcat.`category_id`";
		$headSQL .= " 					and exists (select p.`id` from `#__easyblog_post` as p where pcat.`post_id` = p.`id` and p.`published` = '1' and p.`state` = '0')";

		$mainSQL = $headSQL . $condQuery;

		// do not show categories that has empty post.
		// we need to wrap the main sql so that we can filter by the post count.
		if ($hideEmptyPost) {
			$mainSQL .= " GROUP BY a.`id` having (count(pcat.`id`) > 0)";

		} else {
			$mainSQL .= " GROUP BY a.`id`";
		}

		// main query execution.
		$mainSQL = $mainSQL . $orderBy . $limitSQL;
		$db->setQuery($mainSQL);
		$result	= $db->loadObjectList();

		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}

		$cntQuery = 'select FOUND_ROWS()';
		$db->setQuery( $cntQuery );
		$this->_total	= $db->loadResult();

		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = EB::pagination( $this->_total , $limitstart , $limit );
		}

		return $result;
	}

	/**
	 * Retrieve a list of blog posts from a specific list of categories
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPosts($categories, $limit = null)
	{
		$db = EB::db();
		$my = JFactory::getUser();
		$config	= EB::config();

		// Determines if this is currently on blogger mode
		$isBloggerMode = EasyBlogRouter::isBloggerMode();

		// use in generating category access sql
		$catAccess = array();
		$catAccess['include'] = $categories;


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
	    $contributeSQL = ' AND ( (a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';
	    if ($config->get('main_includeteamblogpost')) {
	      $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'a');
	    }
	    if ($includeJSEvent) {
	      $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT, 'a');
	    }
	    if ($includeJSGrp) {
	      $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP, 'a');
	    }

	    // Test if easysocial exists on the site
		if (EB::easysocial()->exists()) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP, 'a');
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT, 'a');
		}

	    $contributeSQL .= ')';


		$query = array();

		$query[] = 'SELECT a.* FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';

		// Build the WHERE clauses
		$query[] = 'WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		// If this is on blogger mode, fetch items created by the current author only
		if ($isBloggerMode !== false) {
			$query[] = ' AND a.' . $db->quoteName('created_by') . '=' . $db->Quote($isBloggerMode);
		} else {

			// Get the author id based on the category menu
			$authorId = EB::getCategoryMenuBloggerId();

			if ($authorId) {
				$query[] = ' AND a.' . $db->quoteName('created_by') . '=' . $db->Quote($authorId);
			}
		}

		//sql for blog contribution
		$query[] = $contributeSQL;

		// sql for category access
		$catLib = EB::category();
		$catAccessSQL = $catLib->genAccessSQL( 'a.`id`', $catAccess);
		$query[] = 'AND (' . $catAccessSQL . ')';

		// If user is a guest, ensure that they can really view the blog post
		if ($this->my->guest) {
			$query[] = 'AND a.' . $db->quoteName('access') . '=' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		}

		// Ensure that the blog posts is available site wide
		// $query[] = 'AND a.' . $db->quoteName('source_id') . '=' . $db->Quote('0');

		// Filter by language
		$language = EB::getCurrentLanguage();

		if ($language) {
			$query[] = 'AND (a.' . $db->quoteName('language') . '=' . $db->Quote($language) . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('*') . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('') . ')';
		}

		// Ordering options
		$ordering = $config->get('layout_postsort', 'DESC');

		// Order the posts
		$query[] = 'ORDER BY a.' . $db->quoteName('created') . ' ' . $ordering;

		// Set the pagination
		if (!is_null($limit)) {

			// Glue back the sql queries into a single string.
			$queryCount = implode(' ', $query);
			$queryCount = str_ireplace('SELECT a.*', 'SELECT COUNT(1)', $queryCount);

			$db->setQuery($queryCount);
			$count = $db->loadResult();

			$limit = ($limit == 0) ? $this->getState('limit') : $limit;
			$limitstart = $this->input->get('limitstart', $this->getState('limitstart'), 'int');

			// Set the limit
			$query[] = 'LIMIT ' . $limitstart . ',' . $limit;

			$this->_pagination = EB::pagination($count, $limitstart, $limit);
		}

		// Glue back the sql queries into a single string.
		$query = implode(' ', $query);

		// Debug
		// echo str_ireplace('#__', 'jos_', $query);exit;

		$db->setQuery($query);

		if ($db->getErrorNum() > 0) {
			JError::raiseError($db->getErrorNum(), $db->getErrorMsg() . $db->stderr());
		}

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves a list of active authors for a category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getActiveAuthors($categoryId)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT DISTINCT(a.' . $db->quoteName('created_by') . ') FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_post_category') . ' AS b';
		$query[] = 'ON a.' . $db->quoteName('id') . ' = b.' . $db->quoteName('post_id');
		$query[] = 'where b.' . $db->quoteName('category_id') . ' = ' . $db->Quote($categoryId);

		// Glue back the queries into a single string
		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadColum();

		if (!$result) {
			return $result;
		}

		$authors = array();

		// preload users.
		EB::user($result);

		foreach ($result as $id) {
			$author = EB::user($id);
			$authors[] = $author;
		}

		return $authors;
	}

	/**
	 * Method to get total category created so far iregardless the status.
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotalCategory( $userId = 0 )
	{
		$db		= EB::db();
		$where	= array();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_category' );

		if ($userId) {
			$where[]  = '`created_by` = ' . $db->Quote($userId);
		}


		$extra = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$query = $query . $extra;

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Determine if the category exists on the site.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isExist($categoryName, $excludeCatIds='0')
	{
		$db = EB::db();

		$query  = 'SELECT COUNT(1) FROM #__easyblog_category';
		$query  .= ' WHERE `title` = ' . $db->Quote($categoryName);
		if ($excludeCatIds != '0') {
			$query  .= ' AND `id` != ' . $db->Quote($excludeCatIds);
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	public function isCategorySubscribedUser($categoryId, $userId, $email)
	{
		$db	= EB::db();

		$query  = 'SELECT `id` FROM `#__easyblog_subscriptions`';
		$query  .= ' WHERE `uid` = ' . $db->Quote($categoryId);
        $query .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY);

		$query  .= ' AND (`user_id` = ' . $db->Quote($userId);
		$query  .= ' OR `email` = ' . $db->Quote($email) .')';

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	public function isCategorySubscribedEmail($categoryId, $email)
	{
		$db	= EB::db();

		// lets check if this item already cached or not
		if (EB::cache()->exists($categoryId, 'cats')) {
			$data = EB::cache()->get($categoryId, 'cats');

			if (isset($data['subs'])) {
				return true;
			} else {
				return false;
			}
		}

		$query  = 'SELECT `id` FROM `#__easyblog_subscriptions`';
		$query  .= ' WHERE `uid` = ' . $db->Quote($categoryId);
        $query .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY);

		$query  .= ' AND `email` = ' . $db->Quote($email);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	public function addCategorySubscription($categoryId, $email, $userId = '0', $fullname = '')
	{
		$config = EasyBlogHelper::getConfig();
		$acl = EB::acl();
		$my = JFactory::getUser();

		if ($acl->get('allow_subscription') || (empty($my->id) && $config->get('main_allowguestsubscribe'))) {
			$date       = EB::date();
			$subscriber = EB::table('Subscriptions');

			$subscriber->utype 	= EBLOG_SUBSCRIPTION_CATEGORY;
			$subscriber->uid 	= $categoryId;

			$subscriber->email    		= $email;
			if($userId != '0')
				$subscriber->user_id    = $userId;

			$subscriber->fullname	= $fullname;
			$subscriber->created  	= $date->toMySQL();
			$state = $subscriber->store();

			if ($state) {
				$category = EB::table('Category');
				$category->load( $categoryId );

				// lets send confirmation email to subscriber.
				$helper 	= EasyBlogHelper::getHelper( 'Subscription' );
				$template 	= $helper->getTemplate();

				$template->uid 			= $subscriber->id;
				$template->utype 		= 'categorysubscription';
				$template->user_id 		= $subscriber->user_id;
				$template->uemail 		= $email;
				$template->ufullname 	= $fullname;
				$template->ucreated 	= $subscriber->created;
				$template->targetname 	= $category->title;
				$template->targetlink	= EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $categoryId, false, true );

				$helper->addMailQueue( $template );
			}

			return $state;
		}
	}

	public function updateCategorySubscriptionEmail($sid, $userid, $email)
	{
		$config = EasyBlogHelper::getConfig();
		$acl = EB::acl();
		$my = JFactory::getUser();

		if ($acl->get('allow_subscription') || (empty($my->id) && $config->get('main_allowguestsubscribe'))) {

			$subscriber = EB::table('Subscriptions');
			$subscriber->load($sid);
			$subscriber->user_id  = $userid;
			$subscriber->email    = $email;
			$subscriber->store();
		}
	}

	public function allowAclCategory( $catId = 0 )
	{
		$db = EB::db();

		$gid	= EasyBlogHelper::getUserGids();
		$gids	= '';

		if (count($gid) > 0) {
			$temp = array();
			foreach ($gid as $id) {
				$temp[] = $db->quote($id);
			}

			$gids = implode( ',', $temp );
		}

		$query  = 'SELECT COUNT(1) FROM `#__easyblog_category_acl`';
		$query .= ' WHERE `acl_id` = ' . $db->quote('1');
		$query .= ' AND `status` = ' . $db->quote('1');
		$query .= ' AND `category_id` = ' . $db->quote($catId);
		if ($gids) {
			$query .= ' AND `content_id` IN (' . $gids . ')';
		}

		$db->setQuery( $query );

		return $db->loadResult();
	}


	public function preloadByPosts(array $postIds)
	{
		$db = EB::db();

		$query = 'select a.*, b.`post_id`, b.`primary` from `#__easyblog_category` as a';
		$query .= ' inner join `#__easyblog_post_category` as b on a.`id` = b.`category_id`';
		$query .= ' where b.`post_id` IN ( ' . implode(',' , $postIds) . ')';

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}


}
