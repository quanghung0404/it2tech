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

class EasyBlogModelBlog extends EasyBlogAdminModel
{
	public $_total	= null;
	public $_pagination	= null;

	public function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		$limit		= ($mainframe->getCfg('list_limit') == 0) ? 5 : $mainframe->getCfg('list_limit');
		$limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');

		if( $limit != 0 )
		{
			$limitstart		= (int) floor( ( $limitstart / $limit ) * $limit );
		}
		else
		{
			$limitstart 	= 0;
		}

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Determines if the blog post is featured
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isFeatured($id)
	{
		$db 	= EB::db();

		$query 	= array();
		$query[]	= 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_featured');
		$query[]	= 'WHERE ' . $db->quoteName('content_id') . '=' . $db->Quote($id);
		$query[]	= 'AND ' . $db->quoteName('type') . '=' . $db->Quote('post');

		$query 	= implode(' ', $query);

		$db->setQuery($query);
		$count	= $db->loadResult();

		return $count > 0;
	}

	/**
	 * Computes the total number of hits for blog posts created by any specific user throughout the site.
	 *
	 * @access	public
	 * @param 	int 	$userId 	The user's id.
	 * @return 	int 	$total 		The total number of hits.
	 */
	public function getTotalHits( $userId )
	{
		$db		= EB::db();

		$query 	= 'SELECT SUM( `hits` ) FROM ' . $db->nameQuote( '#__easyblog_post' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'created_by' ) . '=' . $db->Quote( $userId );
		$db->setQuery( $query );

		$total 	= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves a list of comments from a blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBlogComment($id, $limitFrontEnd = 0, $sort = 'asc', $lite = false)
	{
		$db	= EB::db();
	    $config = EB::getConfig();
		$sort = $config->get('comment_sort','asc');

		if ($lite) {
			$query	= 'SELECT a.* FROM `#__easyblog_comment` a';
			$query	.= ' INNER JOIN #__users AS c ON a.`created_by` = c.`id`';
			$query	.= ' WHERE a.`post_id` = '.$db->Quote($id);
			$query	.= ' AND a.`published` = 1';
		} else {
			$query	= 'SELECT a.*, (count(b.id) - 1) AS `depth` FROM `#__easyblog_comment` AS a';
			$query	.= ' INNER JOIN `#__easyblog_comment` AS b';
			$query	.= ' LEFT JOIN `#__users` AS c ON a.`created_by` = c.`id`';
			$query	.= ' WHERE a.`post_id` = '.$db->Quote($id);
			$query	.= ' AND b.`post_id` = '.$db->Quote($id);
			$query	.= ' AND a.`published` = 1';
			$query	.= ' AND b.`published` = 1';
			$query	.= ' AND a.`lft` BETWEEN b.`lft` AND b.`rgt`';
			$query	.= ' GROUP BY a.`id`';
		}

		// prepare the query to get total comment
		$queryTotal	= 'SELECT COUNT(1) FROM (';
		$queryTotal	.= $query;
		$queryTotal	.= ') AS x';

		// continue the query.
		$limit		= $this->getState('limit');
		$limitstart = $this->getState('limitstart');

		switch( $sort )
		{
			case 'desc':
				$query	.= ' ORDER BY a.`rgt` desc';
			break;
			default:
				$query	.= ' ORDER BY a.`lft` asc';
			break;
		}

		if($limitFrontEnd > 0) {
			$query  .= ' LIMIT ' . $limitFrontEnd;
		} else {
			$query  .= ' LIMIT ' . $limitstart . ',' . $limit;
		}

		if($limitFrontEnd <= 0) {
			$db->setQuery($queryTotal);
			$this->_total	= $db->loadResult();

			jimport('joomla.html.pagination');

			$this->_pagination	= EB::pagination($this->_total, $limitstart , $limit);
		}

		// the actual content sql
		$db->setQuery($query);
		$result	= $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		// Format the comments
		$result	= EB::comment()->format($result);

		return $result;
	}

	/**
	 * Retrieves a list of featured posts from the site
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFeaturedBlog($categories = array(), $limit = null)
	{
		$my = JFactory::getUser();
		$db = EB::db();
		$max = is_null($limit) ? EBLOG_MAX_FEATURED_POST : $limit;

		// Determine if this is blogger mode
		$isBloggerMode = EBR::isBloggerMode();

		$query = array();

		$query[] = 'SELECT a.*, 1 as `featured` FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_featured') . ' AS c';
		$query[] = 'ON a.' . $db->quoteName('id') . ' = c.' . $db->quoteName('content_id');
		$query[] = 'AND c.' . $db->quoteName('type') . '=' . $db->Quote('post');
		$query[] = 'WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		// If this is blogger mode, we need to filter by author
		if ($isBloggerMode !== false) {
			$query[] = 'AND a.' . $db->quoteName('created_by') . '=' . $db->Quote($isBloggerMode);
		}

		// When language filter is enabled, we need to detect the appropriate contents
		$language = EB::getCurrentLanguage();

		if ($language) {
			$query[] = 'AND(';
			$query[] = 'a.' . $db->quoteName('language') . '=' . $db->Quote($language);
			$query[] = 'OR';
			$query[] = 'a.' . $db->quoteName('language') . '=' . $db->Quote('');
			$query[] = 'OR';
			$query[] = 'a.' . $db->quoteName('language') . '=' . $db->Quote('*');
			$query[] = ')';
		}

		// Explicitly include posts only from these categories
		if (!empty($categories)) {

			// To support both comma separated categories an array of categories
			if (!is_array($categories)) {
				$categories	= explode( ',' , $categories );
			}
		}

		// Privacy for blog
		if ($my->guest) {
			$query[] = 'AND a.' . $db->quoteName('access') . '=' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		}


		// category access
		// sql for category access
		$catLib = EB::category();

		$options = array();
		if ($categories) {
			$options['include'] = $categories;
		}

		$catAccessSQL = $catLib->genAccessSQL('a.`id`', $options);
		$query[] = 'AND (' . $catAccessSQL . ')';

		// Ordering
		$query[] = 'ORDER BY a.' . $db->quoteName('created') . ' DESC';

		if ($max > 0) {
			$query[] = 'LIMIT ' . $max;
		}

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Checks if
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function aliasExists($permalink, $id)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_post');
		$query[] = 'WHERE ' . $db->quoteName('permalink') . '=' . $db->Quote($permalink);

		if ($id != 0) {
			$query[] = 'AND ' . $db->quoteName('id') . '!=' . $db->Quote($id);
		}

		$query = implode(' ', $query);

		$db->setQuery($query);

		$exists = $db->loadResult() > 0 ? true : false;

		return $exists;
	}

	/**
	 * Retrieves the total number of blog posts pending moderation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalModeration()
	{
		$db = EB::db();

		$query = 'select count(1) from ' . $db->qn('#__easyblog_revisions') . ' as a';
		$query .= ' inner join ' . $db->qn('#__easyblog_post') . ' as b on a.' . $db->qn('post_id') . ' = b.' . $db->qn('id');
		$query .= ' where a.' . $db->qn('state') . ' = ' . $db->Quote(EASYBLOG_REVISION_PENDING);

		$db->setQuery($query);

		$total = $db->loadResult();

		return $total;
	}

	/**
	 * Get array of blogs defined by parameters
	 *
	 * @param	$type			str
	 * @param	$typeId			int
	 * @param	$sort			str
	 * @param	$max			int
	 * @param	$published		str
	 * @param	$search			bool
	 * @param	$frontpage		bool
	 * @param	$excludeBlogs	array
	 * @param	$pending		bool
	 * @param	$dashboard		bool
	 * @param	$protected		bool
	 * @param	$excludeCats	array
	 * @param	$includeCats	array
	 *
	*/
	public function getBlogsBy( $type,
								$typeId = 0,
								$sort = '',
								$max = 0 ,
								$published = EBLOG_FILTER_PUBLISHED ,
								$search = false,
								$frontpage = false,
								$excludeBlogs	= array(),
								$pending = false,
								$dashboard = false,
								$protected = true ,
								$excludeCats = array() ,
								$includeCats = array() ,
								$postType = null ,
								$limitType = 'listlength' ,
								$pinFeatured = true )
	{

		$db = EB::db();
		$my = JFactory::getUser();

		$config = EB::config();

		$queryPagination			= false;
		$queryWhere					= '';
		$queryOrder					= '';
		$queryLimit					= '';
		$queryWhere					= '';
		$queryExclude				= '';
		$queryExcludePending		= '';
		$queryExcludePrivateJSGrp	= '';

		// use in generating category access sql
		$catAccess = array();

		// Get excluded categories
		$excludeCats = !empty($excludeCats) ? $excludeCats : array();

		// Determines if the user is viewing a blogger mode menu item
		$isBloggerMode = EasyBlogRouter::isBloggerMode();

		// What is this for?
		$teamBlogIds = '';

		// What?
		$customOrdering = '';

		if (!empty($sort) && is_array($sort)) {
			$customOrdering = isset($sort[1]) ? $sort[1] : '';
			$sort = isset($sort[0]) ? $sort[0] : '';
		}


		// Sorting options
		$sort = empty($sort) ? $config->get('layout_postorder', 'latest') : $sort;

		$isJSGrpPluginInstalled	= false;
		$isJSGrpPluginInstalled	= JPluginHelper::isEnabled( 'system', 'groupeasyblog');
		$isEventPluginInstalled	= JPluginHelper::isEnabled( 'system' , 'eventeasyblog' );
		$isJSInstalled			= false; // need to check if the site installed jomsocial.

		$file	= JPATH_ROOT . '/components/com_community/libraries/core.php';
		$exists = JFile::exists($file);

		if ($exists) {
			$isJSInstalled = true;
		}

		$includeJSGrp	= ($type != 'teamblog' && !$dashboard && $isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent	= ($type != 'teamblog' && !$dashboard && $isEventPluginInstalled && $isJSInstalled ) ? true : false;

		$jsEventPostIds	= '';
		$jsGrpPostIds	= '';

		// contribution type sql
		$contributor = EB::contributor();
		$contributeSQL = ' AND ( (a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';

		if ($config->get('main_includeteamblogpost') || $type == 'teamblog' || $dashboard) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'a');
		}

		if ($includeJSEvent) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT, 'a');
		}

		if ($includeJSGrp) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP, 'a');
		}

		// Only process the contribution sql for EasySocial if EasySocial really exists.
		if ($type != 'teamblog' && !$dashboard && EB::easysocial()->exists()) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP, 'a');
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT, 'a');
		}

		$contributeSQL .= ')';

		//get teamblogs id.
		$query  = '';

		//check if the request come with statastic or not.
		$statType	= JRequest::getString('stat','');
		$statId		= '';

		if (!empty($statType)) {
			$statId = ($statType == 'tag') ? JRequest::getString('tagid','') : JRequest::getString('catid','');
		}

		if (!empty($excludeBlogs)) {

			$queryExclude .= ' AND a.`id` NOT IN (';

			for ($i = 0; $i < count($excludeBlogs); $i++) {

				$queryExclude	.= $db->Quote( $excludeBlogs[ $i ] );

				if( next( $excludeBlogs ) !== false ) {
					$queryExclude .= ',';
				}
			}
			$queryExclude	.= ')';
		}

		// Exclude postings from specific categories
		if (!empty($excludeCats)) {
			$catAccess['exclude'] = $excludeCats;
		}

		$queryInclude	= '';
		// Respect inclusion categories
		if (!empty($includeCats)) {
			$catAccess['include'] = $includeCats;
		}


		switch ($published) {
			case EBLOG_FILTER_PENDING:
				$queryWhere	= ' WHERE a.`published` = ' . $db->Quote(EASYBLOG_POST_PENDING);
				break;
			case EBLOG_FILTER_ALL:
				$queryWhere	= ' WHERE (a.`published` = 1 OR a.`published`=0 OR a.`published`=2 OR a.`published`=3) ';
				$queryWhere .= ' AND a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
				break;
			case EBLOG_FILTER_SCHEDULE:
				$queryWhere = ' WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_SCHEDULED);
				break;
			case EBLOG_FILTER_UNPUBLISHED:
				$queryWhere = ' WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_UNPUBLISHED);
				break;
			case EBLOG_FILTER_DRAFT:
				$queryWhere	= ' WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_DRAFT);
				break;
			case EBLOG_FILTER_PUBLISHED:
			default:
				$queryWhere = ' WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
				$queryWhere .= ' AND a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
				break;
		}


		//do not list out protected blog in rss
		if(JRequest::getCmd('format', '') == 'feed')
		{
			if($config->get('main_password_protect', true))
			{
				$queryWhere	.= ' AND a.`blogpassword`="" ';
			}
		}

		// Blog privacy setting
		// @integrations: jomsocial privacy
		$file		= JPATH_ROOT . '/components/com_community/libraries/core.php';

		$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

		if( $config->get( 'integrations_easysocial_privacy' ) && $easysocial->exists() && !EasyBlogHelper::isSiteAdmin() && $type != 'teamblog' && !$dashboard )
		{
			$esPrivacyQuery  = $easysocial->buildPrivacyQuery( 'a' );
			$queryWhere 	.= $esPrivacyQuery;

		}
		else if( $config->get( 'main_jomsocial_privacy' ) && JFile::exists( $file ) && !EasyBlogHelper::isSiteAdmin() && $type != 'teamblog' && !$dashboard)
		{
			require_once( $file );

			$my			= JFactory::getUser();
			$jsFriends	= CFactory::getModel( 'Friends' );
			$friends	= $jsFriends->getFriendIds( $my->id );
			array_push($friends, $my->id);

			// Insert query here.
			$queryWhere	.= ' AND (';
			$queryWhere	.= ' (a.`access`= 0 ) OR';
			$queryWhere	.= ' ( (a.`access` = 20) AND (' . $db->Quote( $my->id ) . ' > 0 ) ) OR';

			if( empty( $friends ) )
			{
				$queryWhere	.= ' ( (a.`access` = 30) AND ( 1 = 2 ) ) OR';
			}
			else
			{
				$queryWhere	.= ' ( (a.`access` = 30) AND ( a.' . $db->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
			}

			$queryWhere	.= ' ( (a.`access` = 40) AND ( a.' . $db->nameQuote( 'created_by' ) .'=' . $my->id . ') )';
			$queryWhere	.= ' )';
		}
		else
		{
			if( $my->id == 0)
			{
				$queryWhere .= ' AND a.`access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
			}
		}

		if($isBloggerMode !== false)
		{
			$queryWhere .= ' AND a.`created_by` = ' . $db->Quote($isBloggerMode);
		}

		$contentId	= '';
		$isIdArray	= false;
		if(is_array($typeId))
		{
			if(count($typeId) > 1)
			{
				for( $i = 0; $i < count($typeId ); $i++ )
				{
					if( $typeId[ $i ] )
					{
						$contentId	.= $typeId[ $i ];

						if( $i + 1 < count($typeId) )
						{
							$contentId .= ',';
						}
					}
				}
				$isIdArray  = true;
			}
			else
			{
				if(!empty($typeId))
				{
					$contentId	= $typeId[0];
				}
			}
		}
		else
		{
			$contentId  = $typeId;
		}

		if ($contentId) {

			switch ($type) {
				case 'category':

					$catAccess['type'] = $typeId;

					if($isBloggerMode === false)
					{
						$catBloggerId   = EasyBlogHelper::getCategoryMenuBloggerId();
						if( !empty($catBloggerId) )
						{
							$queryWhere	.= ' AND a.`created_by` = ' . $db->Quote($catBloggerId);
						}
					}

					break;
				case 'blogger':
					$queryWhere	.= ($isIdArray) ? ' AND a.`created_by` IN ('. $contentId .')' : ' AND a.`created_by` = ' . $db->Quote($contentId);
					break;
				case 'teamblog':
					$queryWhere .= ' AND (a.source_type = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);
					$queryWhere	.= ($isIdArray) ? ' AND a.source_id IN ('. $contentId .')' : ' AND a.`source_id` = ' . $db->Quote($contentId);
					$queryWhere .= ')';
					break;
				default :
					break;
			}
		}

		// @rule: Filter for `source` column type.
		if( !is_null( $postType ) )
		{
			switch( $postType )
			{
				case 'microblog':
					$queryWhere .= ' AND a.`posttype` != ' . $db->Quote( '' );
				break;
				case 'posts':
					$queryWhere .= ' AND a.`posttype` = ' . $db->Quote( '' );
				break;
				case 'quote':
					$queryWhere .= ' AND a.`posttype` = ' . $db->Quote( 'quote' );
				break;
				case 'link':
					$queryWhere .= ' AND a.`posttype` = ' . $db->Quote( 'link' );
				break;
				case 'photo':
					$queryWhere .= ' AND a.`posttype` = ' . $db->Quote( 'photo' );
				break;
				case 'video':
					$queryWhere .= ' AND a.`posttype` = ' . $db->Quote( 'video' );
				break;
				case 'twitter':
					$queryWhere .= ' AND a.`posttype` = ' . $db->Quote( 'twitter' );
				break;

			}
		}

		if ($type == 'blogger' || $type == 'teamblog') {

			if (! empty($statType)) {

				if($statType == 'category') {
					$catAccess['statType'] = $statId;
				} else {
					$queryWhere	.= ' AND t.`tag_id` = ' . $db->Quote($statId);
				}
			}
		}

		if ($search) {
			$queryWhere	.= ' AND a.`title` LIKE ' . $db->Quote( '%' . $search . '%' );
		}

		if ($frontpage) {
			$queryWhere	.= ' AND a.`frontpage` = ' . $db->Quote('1');
		}

		// @rule: When language filter is enabled, we need to detect the appropriate contents
		$filterLanguage 	= JFactory::getApplication()->getLanguageFilter();
		if ($filterLanguage) {
			$queryWhere	.= EBR::getLanguageQuery('AND', 'a.language');
		}

		if($protected == false)
		{
			$queryWhere	.= ' AND a.`blogpassword` = ""';
		}


		// category access here
		$catLib = EB::category();
		$catAccessSQL = $catLib->genAccessSQL( 'a.`id`', $catAccess);
		$queryWhere .= ' AND (' . $catAccessSQL . ')';


		// get the default sorting.
		$defaultSorting = ( $customOrdering ) ? $customOrdering : $config->get( 'layout_postsort', 'desc' );

		$queryOrder 	= ' ORDER BY ';

		$sortableItems 	= array('latest', 'published', 'popular', 'active', 'alphabet', 'modified', 'random');

		if ($frontpage && $pinFeatured) {
			$queryOrder .= ' f.`created` DESC ,';
		}

		switch( $sort )
		{
			case 'latest':
				$queryOrder	.= ' a.`created` ' . $defaultSorting;
				break;
			case 'published':
				$queryOrder	.= ' a.`publish_up` ' . $defaultSorting;
				break;
			case 'popular':
				$queryOrder	.= ' a.`hits` ' . $defaultSorting;
				break;
			case 'active':
				$queryOrder	.= ' a.`publish_down` ' . $defaultSorting;
				break;
			case 'alphabet':
				$queryOrder	.= ' a.`title` ' . $defaultSorting;
				break;
			case 'modified':
				$queryOrder	.= ' a.`modified` ' . $defaultSorting;
				break;
			case 'random':
				$queryOrder	.= ' `random_id` ';
				break;
			default :
				break;
		}

		if($max > 0)
		{
			$queryLimit	= ' LIMIT '.$max;
		}
		else
		{
			//set frontpage list length if it is detected to be the frontpage
			$view = JRequest::getCmd('view', '');

			$limit = EB::call('Pagination', 'getLimit', array($limitType));
			$limitstart = JRequest::getInt('limitstart', 0, 'REQUEST');

			// In case limit has been changed, adjust it
			$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

			if ($limitstart < 0) {
				$limitstart = 0;
			}

			$queryLimit	= ' LIMIT ' . $limitstart . ',' . $limit;

			$queryPagination = true;
		}

		// if($queryPagination)
		// {
		// 	$query	= 'SELECT COUNT(1) FROM `#__easyblog_post` AS a';

		// 	if( ($type == 'blogger' || $type == 'teamblog') && $statType == 'tag')
		// 	{
		// 		$query  .= ' LEFT JOIN `#__easyblog_post_tag` AS t ON a.id = t.post_id';
		// 	}

		// 	$query	.= $queryWhere;
		// 	$query	.= $contributeSQL;
		// 	$query	.= $queryExclude;
		// 	$query	.= $queryInclude;

		// 	// echo $query;exit;

		// 	$db->setQuery( $query );
		// 	$this->_total	= $db->loadResult();


		// 	$this->_pagination	= EB::pagination( $this->_total , $limitstart , $limit );
		// }


		$query	= 'SELECT SQL_CALC_FOUND_ROWS a.`id` AS key1, a.*';
		$query 	.= ', ifnull(f.`id`, 0) as `featured`';

		if ($sort == 'random') {
			$query .= ', floor( 1 + rand() * rd.`rid` ) as `random_id`';
		}

		$query .= ' FROM `#__easyblog_post` AS a';

		// if ($frontpage && $pinFeatured) {
			$query	.= ' LEFT JOIN `#__easyblog_featured` AS f';
			$query	.= ' 	ON a.`id` = f.`content_id` AND f.`type` = ' . $db->Quote('post');
		// }

		if( ($type == 'blogger' || $type == 'teamblog') && $statType == 'tag')
		{
			$query  .= ' LEFT JOIN `#__easyblog_post_tag` AS t ON a.`id` = t.`post_id`';
			$query  .= ' AND t.`tag_id` = ' . $db->Quote($statId);
		}

		if( $sort == 'random' )
		{
			$query .= ', (select max(tmp.`id`) - 1 as `rid` from `#__easyblog_post` as tmp ) as rd';
		}


		$query	.= $queryWhere;
		$query	.= $contributeSQL;
		$query	.= $queryExclude;
		$query	.= $queryInclude;
		$query	.= $queryOrder;
		$query	.= $queryLimit;


		// // Debugging
		// echo str_ireplace( '#__' , 'jos_' , $query );
		// exit;

		$db->setQuery($query);

		if ($db->getErrorNum() > 0) {
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		$result	= $db->loadObjectList();

		if($queryPagination) {
			// now execute found_row() to get the number of records found.
			$cntQuery = 'select FOUND_ROWS()';
			$db->setQuery( $cntQuery );
			$this->_total	= $db->loadResult();

			$this->_pagination	= EB::pagination( $this->_total , $limitstart , $limit );
		}



		return $result;
	}


	function getPending( $typeId = 0, $sort = 'latest', $max = 0 , $search = false, $frontpage = false )
	{
		$db	= EB::db();

		$queryPagination	= false;
		$queryWhere		= '';
		$queryOrder		= '';
		$queryLimit		= '';
		$queryWhere		= '';
		$queryExclude	= '';

		$queryWhere .= ' where a.' . $db->qn('state') . ' = ' . $db->Quote(EASYBLOG_REVISION_PENDING);


		if( $search )
		{
			$queryWhere	.= ' AND a.`title` LIKE ' . $db->Quote( '%' . $search . '%' );
		}

		if( ! empty( $typeId ) )
		{
			$queryWhere	.= ' AND a.`created_by` = ' . $db->Quote( $typeId );
		}

		switch( $sort )
		{
			case 'latest':
				$queryOrder	= ' ORDER BY a.`created` DESC';
				break;
			case 'active':
				$queryOrder	= ' ORDER BY a.`modified` DESC';
				break;
			case 'alphabet':
				$queryOrder	= ' ORDER BY a.`title` ASC';
				break;
			default :
				break;
		}

		if($max > 0)
		{
			$queryLimit	= ' LIMIT '.$max;
		}
		else
		{
			$limit		= $this->getState('limit');
			$limitstart = $this->getState('limitstart');

			//set frontpage list length if it is detected to be the frontpage
			$view		= JRequest::getCmd('view', '');

			if($view=='latest')
			{
				$config		= EasyBlogHelper::getConfig();
				$listlength = $config->get('layout_listlength', '0');

				if($listlength)
				{
					$limit = $listlength;
					$limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');

					// In case limit has been changed, adjust it
					$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
				}
			}

			$queryLimit	= ' LIMIT ' . $limitstart . ',' . $limit;

			$queryPagination = true;
		}

		if($queryPagination)
		{
			$query	= 'SELECT COUNT(1) FROM `#__easyblog_revisions` AS a';

			$query	.= $queryWhere;
			$query	.= $queryExclude;

			$db->setQuery( $query );
			$this->_total	= $db->loadResult();

			jimport('joomla.html.pagination');
			$this->_pagination	= EB::pagination( $this->_total , $limitstart , $limit );
		}


		$query	= 'SELECT a.* FROM `#__easyblog_revisions` AS a';

		$query	.= $queryWhere;
		$query	.= $queryExclude;
		$query	.= $queryOrder;
		$query	.= $queryLimit;

		// echo $query;exit;

		$db->setQuery($query);
		if($db->getErrorNum() > 0)
		{
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		$result	= $db->loadObjectList();
		return $result;
	}


	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		return $this->_pagination;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		return $this->_total;
	}

	/**
	 * Method to get total blogs post currently iregardless the status.
	 *
	 * @access public
	 * @return integer
	 */
	function getTotalBlogs( $userId	= 0 )
	{
		$db		= EB::db();
		$where	= array();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_post' );

		//blog privacy setting
		$my = JFactory::getUser();
		if($my->id == 0)
			$where[] = '`access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);

		if(! empty($userId) )
			$where[] = '`created_by` = ' . $db->Quote($userId);

		$extra	= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		$query	= $query . $extra;

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	function getTotalBlogSubscribers( $userId = 0 )
	{
		$db		= EB::db();
		$where	= array();

		$query	= 'select count(1) from `#__easyblog_subscriptions` as a';
		$query	.= '  inner join `#__easyblog_post` as b';
		$query	.= '    on a.`uid` = b.`id`';
		$query	.= '    WHERE a.`utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_ENTRY);

		if(! empty($userId))
		$query	.= '    and b.created_by = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Retrieves a list of blog posts associated with a particular tag
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTaggedBlogs($tagId = 0, $limit = false, $includeCatIds = '', $sorting = '')
	{
		if (!$tagId) {
			return false;
		}

		$my		= JFactory::getUser();
		$db		= EB::db();
		$config	= EasyBlogHelper::getConfig();
		$catAccess = array();

		if ($limit === false) {
			if ($config->get('layout_listlength') == 0) {
				$limit = $this->getState('limit');
			} else {
				$limit = $config->get('layout_listlength');
			}
		}

		$limitstart = $this->getState('limitstart');

		$isBloggerMode = EasyBlogRouter::isBloggerMode();
		$queryExclude = '';
		$excludeCats = array();


		$isJSGrpPluginInstalled	= false;
		$isJSGrpPluginInstalled	= JPluginHelper::isEnabled('system', 'groupeasyblog');
		$isEventPluginInstalled	= JPluginHelper::isEnabled( 'system' , 'eventeasyblog' );
		$isJSInstalled = false;

		if (JFile::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR .'core.php')) {
			$isJSInstalled = true;
		}


		$includeJSGrp	= ( $isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent	= ( $isEventPluginInstalled && $isJSInstalled ) ? true : false;
		$jsGrpPostIds	= '';
		$jsEventPostIds	= '';

	    // contribution type sql
	    $contributor = EB::contributor();
	    $contributeSQL = ' AND ( (b.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';
	    if ($config->get('main_includeteamblogpost')) {
	      $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'b');
	    }
	    if ($includeJSEvent) {
	      $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT, 'b');
	    }
	    if ($includeJSGrp) {
	      $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP, 'b');
	    }

	    if (EB::easysocial()->exists()) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP, 'b');
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT, 'b');
		}


	    $contributeSQL .= ')';


		//get teamblogs id.

		$query	= 'SELECT b.*';

		$query	.= ' FROM ' . $db->nameQuote( '#__easyblog_post_tag' ) . ' AS a ';
		$query	.= ' INNER JOIN ' . $db->nameQuote( '#__easyblog_post' ) . ' AS b ';
		$query	.= ' ON a.post_id=b.id ';

		$query	.= ' WHERE a.' . $db->quoteName('tag_id') . ' = ' . $db->Quote( $tagId );

		$query	.= ' AND b.' . $db->quoteName('published') . ' = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' AND b.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		$query .= $contributeSQL;

		// @rule: When language filter is enabled, we need to detect the appropriate contents
		$filterLanguage 	= JFactory::getApplication()->getLanguageFilter();
		if ($filterLanguage) {
			$query	.= EBR::getLanguageQuery('AND', 'b.language');
		}

		$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
		//blog privacy setting
		if( $config->get( 'main_jomsocial_privacy' ) && JFile::exists( $file ) && !EasyBlogHelper::isSiteAdmin())
		{
			require_once( $file );

			$jsFriends	= CFactory::getModel( 'Friends' );
			$friends	= $jsFriends->getFriendIds( $my->id );
			array_push($friends, $my->id);

			// Insert query here.
			$query	.= ' AND (';
			$query	.= ' (b.`access`= 0 ) OR';
			$query	.= ' ( (b.`access` = 20) AND (' . $db->Quote( $my->id ) . ' > 0 ) ) OR';

			if( empty( $friends ) )
			{
				$query	.= ' ( (b.`access` = 30) AND ( 1 = 2 ) ) OR';
			}
			else
			{
				$query	.= ' ( (b.`access` = 30) AND ( b.' . $db->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
			}

			$query	.= ' ( (b.`access` = 40) AND ( b.' . $db->nameQuote( 'created_by' ) .'=' . $my->id . ') )';
			$query	.= ' )';
		}
		else
		{
			if( $my->id == 0)
			{
				$query .= ' AND b.`access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
			}
		}

		if($isBloggerMode !== false)
			$query .= ' AND b.`created_by` = ' . $db->Quote($isBloggerMode);


		$includeCats	= array();
		$includeCatIds	= trim( $includeCatIds );
		if( !empty( $includeCatIds) )
		{
			$includeCats = explode( ',' , $includeCatIds );

			if( !empty( $includeCats ) )
			{
				$catAccess['include'] = $includeCats;
			}
		}

		// category access
		$catLib = EB::category();
		$catAccessSQL = $catLib->genAccessSQL( 'b.`id`', $catAccess);
		$query .= ' AND (' . $catAccessSQL . ')';


		$sort = $config->get( 'layout_postorder', 'latest' );
		$defaultSorting = $config->get( 'layout_postsort', 'desc' );

		if($sorting){
			$defaultSorting = $sorting;
		}

		  switch( $sort )
		  {
		   case 'latest':
		    $queryOrder = ' ORDER BY b.`created` ' . $defaultSorting;
		    break;
		   case 'published':
		    $queryOrder = ' ORDER BY b.`publish_up` ' . $defaultSorting;
		    break;
		   case 'popular':
		    $queryOrder = ' ORDER BY b.`hits` ' . $defaultSorting;
		    break;
		   case 'active':
		    $queryOrder = ' ORDER BY b.`publish_down` ' . $defaultSorting;
		    break;
		   case 'alphabet':
		    $queryOrder = ' ORDER BY b.`title` ' . $defaultSorting;
		    break;
		   case 'modified':
		    $queryOrder = ' ORDER BY b.`modified` ' . $defaultSorting;
		    break;
		   case 'random':
		    $queryOrder = ' ORDER BY RAND() ';
		    break;
		   default :
		    break;
		  }

 		 $query .= $queryOrder;

		//total tag's post sql
		$totalQuery	= 'SELECT COUNT(1) FROM (';
		$totalQuery	.= $query;
		$totalQuery	.= ') as x';



		$query	.= ' LIMIT ' . $limitstart . ',' . $limit;


		$db->setQuery( $query );
		$rows	= $db->loadObjectList();

		$db->setQuery( $totalQuery );
		$this->_total	= $db->loadResult();

		jimport('joomla.html.pagination');
		$this->_pagination = EB::pagination($this->_total , $limitstart , $limit );

		return $rows;
	}


	function isBlogSubscribedUser($blogId, $userId, $email)
	{
		$db	= EB::db();

		$query	= 'SELECT `id` FROM `#__easyblog_subscriptions`';
		$query	.= ' WHERE `uid` = ' . $db->Quote($blogId);
		$query	.= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_ENTRY);
		$query	.= ' AND (`user_id` = ' . $db->Quote($userId);
		$query	.= ' OR `email` = ' . $db->Quote($email) .')';

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function isBlogSubscribedEmail($blogId, $email)
	{
		$db	= EB::db();

		$query	= 'SELECT `id` FROM `#__easyblog_subscriptions`';
		$query	.= ' WHERE `uid` = ' . $db->Quote($blogId);
		$query	.= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_ENTRY);
		$query	.= ' AND `email` = ' . $db->Quote($email);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function addBlogSubscription($blogId, $email, $userId = '0', $fullname = '')
	{
		$config = EasyBlogHelper::getConfig();
		$acl = EB::acl();
		$my = JFactory::getUser();

		if ($acl->get('allow_subscription') || (empty($my->id) && $config->get('main_allowguestsubscribe'))) {
			$date		= EB::date();
			$subscriber	= EB::table('Subscriptions');

			$subscriber->uid	= $blogId;
			$subscriber->utype	= EBLOG_SUBSCRIPTION_ENTRY;

			$subscriber->email		= $email;
			if($userId != '0')
				$subscriber->user_id	= $userId;

			$subscriber->fullname	= $fullname;
			$subscriber->created	= $date->toMySQL();
			$state = $subscriber->store();

			if( $state )
			{
				$blog = EB::table('Blog');
				$blog->load( $blogId );

				// lets send confirmation email to subscriber.
				$helper 	= EasyBlogHelper::getHelper( 'Subscription' );
				$template 	= $helper->getTemplate();

				$template->uid 			= $subscriber->id;
				$template->utype 		= 'subscription';
				$template->user_id 		= $subscriber->user_id;
				$template->uemail 		= $email;
				$template->ufullname 	= $fullname;
				$template->ucreated 	= $subscriber->created;
				$template->targetname 	= $blog->title;
				$template->targetlink	= EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $blogId, false, true );

				if($blog->created_by != $subscriber->user_id)
				{
					$helper->addMailQueue( $template );
				}
			}

			return $state;

		}

		return false;
	}

	/**
	 * Converts a string into a valid permalink
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizePermalink($string, $postId = null)
	{
		$permalink = EBR::normalizePermalink($string);

		$i = 1;

		while ($this->permalinkExists($permalink, $postId)) {
			$permalink = $string . '-' . $i;
			$i++;
		}

		$permalink = EBR::normalizePermalink($permalink);

		return $permalink;
	}

	/**
	 * Determines if the post's permalink exists on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function permalinkExists($permalink, $postId = null)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_post');
		$query[] = 'WHERE ' . $db->qn('permalink') . '=' . $db->Quote($permalink);
		$query[] = 'AND ' . $db->qn('published') . '!=' . $db->Quote(EASYBLOG_POST_BLANK);

		if ($postId) {
			$query[] = 'AND ' . $db->qn('id') . '!=' . $db->Quote($postId);
		}

		$db->setQuery($query);

		$exists = $db->loadResult() > 0 ? true : false;

		return $exists;
	}

	public function updateBlogSubscriptionEmail($sid, $userid, $email)
	{
		$config = EasyBlogHelper::getConfig();
		$acl = EB::acl();
		$my = JFactory::getUser();

		if ($acl->get('allow_subscription') || (empty($my->id) && $config->get('main_allowguestsubscribe'))) {
			$subscriber = EB::table('Subscriptions');
			$subscriber->load($sid);
			$subscriber->user_id	= $userid;
			$subscriber->email		= $email;
			$subscriber->store();
		}
	}

	/**
	 * Retrieves the next post in line
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostNavigation(EasyBlogPost $post, $navigationType)
	{
		$db = EB::db();
		$my = JFactory::getUser();
		$config = EB::config();

		$keys = array('prev','next');
		$nav = new stdClass();

		// Get the active menu
		$active = JFactory::getApplication()->getMenu()->getActive();
		$catAccess = array();
		$queryInclude 	= '';

		$teamId = $post->getTeamAssociation();
		$author = $post->getAuthor();

		// // If there is an active menu for EasyBlog, check if there's any filtering by categories
		// if ($active) {

		// 	$cats = EB::getCategoryInclusion($active->params->get('inclusion'));

		// 	if ($cats && !is_array($cats)) {
		// 		$cats = array($cats);
		// 	}

		// 	$catAccess['include'] = $cats;
		// }

		// // sql for category access
		// $catLib = EB::category();
		// $catAccessSQL = $catLib->genAccessSQL( 'a.`id`', $catAccess);

		foreach ($keys as $key) {

			$query = array();

			$query[] = 'SELECT a.`id`, a.`title`';
			$query[] = ' FROM `#__easyblog_post` AS `a`';
			$query[] = ' WHERE a.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$query[] = ' AND a.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);

			// EasySocial integrations
			$query[] = EB::easysocial()->buildPrivacyQuery('a');

			// Jomsocial integrations
			$query[] = EB::jomsocial()->buildPrivacyQuery();

			// Blog privacy settings
			if ($my->guest) {
				$query[] = 'AND a.' . $db->qn('access') . '=' . $db->Quote(BLOG_PRIVACY_PUBLIC);
			}

			// Exclude private categories
			// $query[] = 'AND (' . $catAccessSQL . ')';

			// If the current menu is blogger mode, we need to respect this by only loading author related items
			$isBloggerMode = EBR::isBloggerMode();

			if ($isBloggerMode !== false) {
				$query[] = 'AND a.' . $db->qn('created_by') . '=' . $db->Quote($isBloggerMode);
				$query[] = 'AND a.' . $db->qn('source_type') . '=' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE);
			}

			// Filter the next / previous link by team
			if ($navigationType == 'team' && $teamId) {
				$query[] = 'AND (a.' . $db->qn('source_type') . '=' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM) . ' AND a.' . $db->qn('source_id') . '=' . $db->Quote($teamId) . ')';
			}

			// Filter the next / previous by author
			if ($navigationType == 'author') {
				$query[] = 'AND a.' . $db->qn('created_by') . '=' . $db->Quote($author->id);
				$query[] = 'AND a.' . $db->qn('source_type') . '=' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE);
			}

			// Filter the next / previous post items from category
			if ($navigationType == 'category') {
				$query[] = 'AND a.' . $db->qn('category_id') . '=' . $db->Quote($post->category_id);
				$query[] = 'AND a.' . $db->qn('source_type') . '=' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE);
			}

			// Filter the next / previous post items from site wide
			if ($navigationType == 'site') {
				$query[] = 'AND a.' . $db->qn('source_type') . '=' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE);
			}

			// When language filter is enabled, we need to detect the appropriate contents
			$filterLanguage = JFactory::getApplication()->getLanguageFilter();

			if ($filterLanguage) {
				$query[] = EBR::getLanguageQuery('AND', 'a.language');
			}

			if ($key == 'prev') {
				$query[] = ' AND a.`created` < ' . $db->Quote($post->created);
				$query[] = ' ORDER BY a.`created` DESC';
			}

			if ($key == 'next') {
				$query[] = ' AND a.`created` > ' . $db->Quote($post->created);
				$query[] = ' ORDER BY a.`created` ASC';
			}

			$query[] = 'LIMIT 1';

			$query = implode(' ', $query);
			$db->setQuery($query);
			$result = $db->loadObject();

			$nav->$key = $result;
		}

		return $nav;
	}

	function getCategoryName( $category_id )
	{
		$db = EB::db();

		if($category_id == 0)
			return JText::_('COM_EASYBLOG_UNCATEGORIZED');

		$query  = 'SELECT `title`, `id` FROM `#__easyblog_category` WHERE `id` = ' . $db->Quote($category_id);
		$db->setQuery($query);

		$result = $db->loadResult();
		return $result;
	}

	/**
	 * Retrieves a list of posts created by the user
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserPosts($userId, $options = array())
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_post');
		$query[] = 'WHERE ' . $db->qn('created_by') . '=' . $db->Quote($userId);
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND ' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		if (isset($options['exclude'])) {
			$query[] = 'AND ' . $db->qn('id') . ' !=' . $db->Quote($options['exclude']);
		}

		$query = implode(' ', $query);
		$db->setQuery($query);

		$items = $db->loadObjectList();

		return $items;
	}

	function getTrackback( $blogId )
	{
		$db = EB::db();

		$query	= 'SELECT * FROM `#__easyblog_trackback`';
		$query	.= ' WHERE `post_id` = ' . $db->Quote($blogId);
		$query	.= ' AND `published`=' . $db->Quote( 1 );
		$query	.= ' ORDER BY `created` DESC';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves posts that is related to the given blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRelatedPosts($id, $max = 0)
	{
		$db = EB::db();
		$config = EB::config();

		$result = array();

		$query = array();

		// Get a list of tags
		$query[] = 'SELECT ' . $db->quoteName('tag_id') . ' FROM ' . $db->quoteName('#__easyblog_post_tag');
		$query[] = 'WHERE ' . $db->quoteName('post_id') . '=' . $db->Quote($id);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$tags = $db->loadColumn();

		if (!$tags) {
			return $tags;
		}


		$query = array();

		$query[] = 'SELECT DISTINCT c.*, l.' . $db->quoteName('title')  . ' AS ' . $db->quoteName('category');
		$query[] = 'FROM ' . $db->quoteName('#__easyblog_post_tag') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_post_tag') . ' AS a1';
		$query[] = 'ON a.' . $db->quoteName('tag_id') . ' = a1.' . $db->quoteName('tag_id');
		$query[] = 'AND a1.' . $db->quoteName('post_id') . '=' . $db->Quote($id);

		$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_post') . ' AS c';
		$query[] = 'ON a.' . $db->quoteName('post_id') . ' = c.' . $db->quoteName('id');

		$query[] = 'LEFT JOIN ' . $db->quoteName('#__easyblog_post_category') . ' AS k';
		$query[] = 'ON k.' . $db->quoteName('post_id') . ' = c.' . $db->quoteName('id');

		$query[] = 'LEFT JOIN ' . $db->quoteName('#__easyblog_category') . ' AS l';
		$query[] = 'ON c.' . $db->quoteName('category_id') . ' = l.' . $db->quoteName('id');

		$query[] = 'WHERE a.' . $db->quoteName('post_id') . '!=' . $db->Quote($id);
		$query[] = 'AND c.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND c.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		// When language filter is enabled, we need to detect the appropriate contents
		$language = EB::getCurrentLanguage();

		if ($language) {
			$query[] = 'AND(';
			$query[] = 'c.' . $db->quoteName('language') . '=' . $db->Quote($language);
			$query[] = 'OR';
			$query[] = 'c.' . $db->quoteName('language') . '=' . $db->Quote('');
			$query[] = 'OR';
			$query[] = 'c.' . $db->quoteName('language') . '=' . $db->Quote('*');
			$query[] = ')';
		}

		// Get a random sort item. Need to use this instead of RAND() because of performance issue.
		$sortItems = array('title', 'category_id', 'created', 'revision_id');
		$sortItem = $sortItems[array_rand($sortItems)];

		// Get random acs/desc
		$orderItems = array('asc', 'desc');
		$orderItem = $orderItems[array_rand($orderItems)];

		$query[] = 'ORDER BY c.'. $db->quoteName($sortItem) . ' ' . $orderItem;
		$query[] = 'LIMIT ' . $max;

		$query = implode(' ', $query);
		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$posts = array();

		foreach ($result as $row) {

			$post = EB::post($row->id);

			$posts[] = $post;
		}

		return $posts;
	}

	/**
	 * Use EasyBlogModelBlogs->approveBlog instead.
	 *
	 * @deprecated	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function approveBlog($id)
	{
		$model = EB::model('Blogs');
		return $model->approveBlog($id);
	}

	/**
	 * Retrieves a list of templates created by the user
	 *
	 * @since	4.0
	 * @access	public
	 * @param	int		The user id to retrieve the templates from
	 * @return
	 */
	public function getTemplates($userId)
	{
		$db = EB::db();

		$query = 'SELECT * FROM ' . $db->quoteName('#__easyblog_post_templates');
		$query .= ' WHERE ' . $db->quoteName('user_id') . '=' . $db->Quote($userId);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$templates = array();

		foreach ($result as $row) {
			$template = EB::table('PostTemplate');
			$template->bind($row);

			$templates[] = $template;
		}

		return $templates;
	}

	/**
	 * Retrieves the latest post by author
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLatestPostByAuthor($authorId = null, $limit = 1)
	{
		$db = EB::db();
		$user = JFactory::getUser($authorId);

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_post');
		$query[] = 'WHERE ' . $db->qn('created_by') . '=' . $db->Quote($user->id);
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND ' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query[] = 'ORDER BY ' . $db->qn('created') . ' DESC';
		$query[] = 'LIMIT 0,' . (int) $limit;

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$posts = array();

		foreach ($result as $row) {
			$post = EB::post($row->id);

			$posts[] = $post;
		}

		return $posts;
	}

	/**
	 * Retrieves the meta id for a blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMetaId($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT a.' . $db->qn('id') . ' FROM ' . $db->qn('#__easyblog_meta') . ' AS a';
		$query[] = 'WHERE a.' . $db->qn('content_id') . '=' . $db->Quote($id);
		$query[] = 'AND a.' . $db->qn('type') . '=' . $db->Quote('post');

		$query = implode(' ', $query);
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieves a list of most commented posts created by the author
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMostCommentedPostByAuthor($authorId = null, $limit = 5)
	{
		$db = EB::db();
		$user = JFactory::getUser($authorId);

		$query = array();
		$query[] = 'SELECT a.*, COUNT(b.' . $db->qn('id') . ') AS ' . $db->qn('totalcomments') . ' FROM ' . $db->qn('#__easyblog_post') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__easyblog_comment') . ' AS b';
		$query[] = 'ON b.' . $db->qn('post_id') . ' = a.' . $db->qn('id');
		$query[] = 'WHERE a.' . $db->qn('created_by') . '=' . $db->Quote($user->id);
		$query[] = 'AND a.' . $db->qn('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query[] = 'AND b.' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = 'GROUP BY a.' . $db->qn('id');
		$query[] = 'ORDER BY ' . $db->qn('totalcomments') . ' DESC';
		$query[] = 'LIMIT 0,' . (int) $limit;

		$query = implode(' ', $query);
		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$posts = array();

		foreach ($result as $row) {
			$post = EB::post($row->id);
			$post->totalcomments = $row->totalcomments;

			$posts[] = $post;
		}

		return $posts;
	}

	public function getAssociationPosts($options = array())
	{

		$db = EB::db();

		$query = "select a.* from `#__easyblog_post` as a";
		$query .= " WHERE a." . $db->quoteName('published') . " = " . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= " AND a." . $db->quoteName('state') . " = " . $db->Quote(EASYBLOG_POST_NORMAL);

		if (isset($options['langcode']) && $options['langcode']) {
			$query .= " AND a." . $db->quoteName('language') . " = " . $db->Quote($options['langcode']);
		}

		if (isset($options['userid']) && $options['userid']) {
			$query .= " AND a." . $db->quoteName('created_by') . " = " . $db->Quote($options['userid']);
		}

		if (isset($options['search']) && $options['search']) {
			$query .= " AND a." . $db->quoteName('title') . " LIKE " . $db->Quote('%' . $options['search'] . '%');
		}

		// limits
		$limit = EB::call('Pagination', 'getLimit', array('listlength'));
		$limitstart = JRequest::getInt('limitstart', 0, 'REQUEST');

		// In case limit has been changed, adjust it
		$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		if ($limitstart < 0) {
			$limitstart = 0;
		}

		$queryLimit	= " LIMIT " . $limitstart . "," . $limit;

		// total count
		$queryCnt = "SELECT COUNT(1) from (";
		$queryCnt .= $query;
		$queryCnt .= ") as x";

		$db->setQuery( $queryCnt );
		$this->_total	= $db->loadResult();

		$this->_pagination	= EB::pagination( $this->_total , $limitstart , $limit );

		$query = $query . $queryLimit;

		$db->setQuery($query);

		$results = $db->loadObjectList();

		return $results;
	}
}
