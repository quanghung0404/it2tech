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

class EasyBlogModelArchive extends EasyBlogAdminModel
{
	public $_total = null;
	public $_pagination = null;

	public function __construct()
	{
		parent::__construct();

		$limit = ($this->app->getCfg('list_limit') == 0) ? 5 : $this->app->getCfg('list_limit');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}


	/**
	 * Retrieve a list of blog posts from a specific list of categories
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPosts($categories = array(), $limit = null)
	{
		$db = EB::db();
		$my = JFactory::getUser();
		$config	= EB::config();

		$catAccess = array();
		$query = array();


		if ($categories) {
			$catAccess['include'] = $categories;
		}

		$query[] = 'SELECT a.* FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';

		// Build the WHERE clauses
		$query[] = 'WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_ARCHIVED);

		// If user is a guest, ensure that they can really view the blog post
		if ($this->my->guest) {
			$query[] = 'AND a.' . $db->quoteName('access') . '=' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		}

		// Ensure that blogger mode is respected
		// Determines if this current request is standalone mode
		$blogger = EB::isBloggerMode();

		if ($blogger !== false) {
			$query[] = 'AND a.' . $db->quoteName('created_by') . '=' . $db->Quote($blogger);
		}

		// Ensure that the blog posts is available site wide
		$query[] = 'AND a.' . $db->quoteName('source_id') . '=' . $db->Quote(0);

		// Filter by language
		$language = EB::getCurrentLanguage();

		if ($language) {
			$query[] = 'AND (a.' . $db->quoteName('language') . '=' . $db->Quote($language) . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('*') . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('') . ')';
		}

		// sql for category access
		$catLib = EB::category();
		$catAccessSQL = $catLib->genAccessSQL( 'a.`id`', $catAccess);
		$query[] = 'AND (' . $catAccessSQL . ')';


		// Ordering options
		$ordering = $config->get('layout_postsort', 'DESC');

		// Order the posts
		$query[] = 'ORDER BY a.' . $db->quoteName('created') . ' ' . $ordering;


		// Set the pagination
		if (!is_null($limit)) {
			$query[] = 'LIMIT ' . $limit;

			$limit = ($limit == 0) ? $this->getState('limit') : $limit;
			$limitstart = $this->app->get('limitstart', $this->getState('limitstart'), 'int');

			$this->_pagination = EB::pagination(0, $limitstart, $limit);
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
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		return $this->_pagination;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		return $this->_total;
	}

    public function getArchiveMinMaxYear()
	{
		$db 	= EB::db();
		$user	= JFactory::getUser();

		$query	= 'SELECT YEAR(MIN( '.$db->nameQuote('created').' )) AS minyear, '
				. 'YEAR(MAX( '.$db->nameQuote('created').' )) AS maxyear '
				. 'FROM '.$db->nameQuote('#__easyblog_post').' '
				. 'WHERE '.$db->nameQuote('published').' = '.$db->Quote(EASYBLOG_POST_PUBLISHED) .' '
				. 'AND '.$db->nameQuote('state').' = '.$db->Quote(EASYBLOG_POST_NORMAL) .' ';


		if(empty($user->id))
		{
			$query .= 'AND '.$db->nameQuote('access').' = '.$db->Quote('0') . ' ';
		}

		$db->setQuery($query);
		$row = $db->loadAssoc();

		if(empty($row['minyear']) || empty($row['maxyear']))
		{
			$year = array();
		}
		else
		{
			$year = $row;
		}

		return $year;
	}

	public function getArchivePostCount($yearStart='', $yearStop='0', $excludeCats = '')
	{
		$result = self::getArchivePostCounts($yearStart, $yearStop, $excludeCats, '');
		return $result;
	}

	public function getArchivePostCounts($yearStart='', $yearStop='0', $excludeCats = '', $includeCats = '', $filter = '', $filterId = '')
	{
		$db 	= EB::db();
		$user	= JFactory::getUser();
		$config = EB::config();

		$catAccess = array();

		if(empty($yearStart))
		{
			$year		= $this->getArchiveMinMaxYear();
			$yearStart	= $year['maxyear'];
		}

		if(!empty($yearStop))
		{
			$fr = $yearStart - 1;
			$to	= $yearStop + 1;
		}
		else
		{
			$fr = $yearStart - 1;
			$to	= $yearStart + 1;
		}

		if( !is_array( $excludeCats ) && !empty( $excludeCats ) )
		{
			$excludeCats	= explode( ',' , $excludeCats );
		}
		else if( !is_array( $excludeCats ) && empty( $excludeCats ) )
		{
			$excludeCats    = array();
		}


		if( !is_array( $includeCats ) && !empty( $includeCats ) )
		{
			$includeCats	= explode( ',' , $includeCats );
		}
		else if( !is_array( $includeCats ) && empty( $includeCats ) )
		{
			$includeCats    = array();
		}

		$includeCats    = array_diff( $includeCats, $excludeCats );


		if( !empty( $excludeCats ) && count( $excludeCats ) >= 1 )
		{
			$catAccess['exclude'] = $excludeCats;
		}

		if( !empty( $includeCats ) && count( $includeCats ) >= 1 )
		{
			$catAccess['include'] = $includeCats;
		}

		//blog privacy setting
		// @integrations: jomsocial privacy
		$privateBlog = '';

		$file		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';

		$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

		if( $config->get( 'integrations_easysocial_privacy' ) && $easysocial->exists() && !EasyBlogHelper::isSiteAdmin() )
		{
			$esPrivacyQuery  = $easysocial->buildPrivacyQuery( 'a' );
			$privateBlog 	.= $esPrivacyQuery;
		}
		else if( $config->get( 'main_jomsocial_privacy' ) && JFile::exists( $file ) && !EasyBlogHelper::isSiteAdmin() )
		{
			require_once( $file );

			$jsFriends	= CFactory::getModel( 'Friends' );
			$friends	= $jsFriends->getFriendIds( $user->id );

			// Insert query here.
			$privateBlog	.= ' AND (';
			$privateBlog	.= ' (a.`access`= 0 ) OR';
			$privateBlog	.= ' ( (a.`access` = 20) AND (' . $db->Quote( $user->id ) . ' > 0 ) ) OR';

			if( empty( $friends ) )
			{
				$privateBlog	.= ' ( (a.`access` = 30) AND ( 1 = 2 ) ) OR';
			}
			else
			{
				$privateBlog	.= ' ( (a.`access` = 30) AND ( a.' . $db->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
			}

			$privateBlog	.= ' ( (a.`access` = 40) AND ( a.' . $db->nameQuote( 'created_by' ) .'=' . $user->id . ') )';
			$privateBlog	.= ' )';
		}
		else
		{
			if( $user->id == 0)
			{
				$privateBlog .= ' AND a.`access` = ' . $db->Quote(0);
			}
		}

		$joinTeam = '';

		$FilterSQL = '';
		if ($filter != ''){

			$FilterSQL = '';
			switch( $filter )
			  {
			   case 'blogger':
			    $FilterSQL = 'AND a.'.$db->nameQuote('created_by').' = '.$db->Quote($filterId);
			    break;
			   case 'team':
			    $FilterSQL = 'AND (a.' . $db->quoteName('source_type') . ' = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM) . ' and a.'.$db->quoteName('source_id').' = '.$db->Quote($filterId) . ')';
			    break;
			   default :
			    break;
			  }
		}
		$languageFilterSQL = '';

		// @rule: When language filter is enabled, we need to detect the appropriate contents
		$filterLanguage 	= JFactory::getApplication()->getLanguageFilter();
		if ($filterLanguage) {
			$languageFilterSQL	.= EBR::getLanguageQuery('AND', 'a.language');
		}

		$catLib = EB::category();
		$catAccessSQL = $catLib->genAccessSQL( 'a.`id`', $catAccess);

		$query	= 'SELECT COUNT(1) as count, MONTH( a.'.$db->nameQuote('created').' ) AS month, YEAR( a.'.$db->nameQuote('created').' ) AS year '
				. 'FROM '.$db->nameQuote('#__easyblog_post').' AS a '
				. $joinTeam.' '
				. 'WHERE a.'.$db->nameQuote('published') .'=' . $db->Quote(EASYBLOG_POST_PUBLISHED) . ' '
				. 'AND a.'.$db->nameQuote('state') .'=' . $db->Quote(EASYBLOG_POST_NORMAL) . ' '
				. $privateBlog.' '
				. $languageFilterSQL. ' '
				. $FilterSQL. ' '
				. 'AND ( a.'.$db->nameQuote('created').' > '.$db->Quote($fr.'-12-31 23:59:59').' AND a.'.$db->nameQuote('created').' < '.$db->Quote($to.'-01-01 00:00:00').') '
				. 'AND (' . $catAccessSQL . ') '
				. 'GROUP BY year, month DESC '
				. 'ORDER BY a.'.$db->nameQuote('created').' DESC ';

		$db->setQuery($query);
		$row = $db->loadAssocList();

		if(empty($row))
		{
			return false;
		}

		$postCount = new stdClass();
		foreach($row as $data)
		{
			if(!isset($postCount->{$data['year']}))
				$postCount->{$data['year']} = new stdClass();
			$postCount->{$data['year']}->{$data['month']} = $data['count'];
		}

		return $postCount;
	}


	public function getArchivePostCountByMonth($month='', $year='', $showPrivate=true)
	{
		$db 	= EB::db();
		$user	= JFactory::getUser();

		$privateBlog = $showPrivate? '' : 'AND '.$db->nameQuote('access').' = '. $db->Quote('0');

		$tzoffset   = EasyBlogDateHelper::getOffSet( true );
		$query	= 'SELECT COUNT(1) as count, DAY( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS day,';
		$query	.= ' MONTH( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS month,';
		$query	.= ' YEAR( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS year ';
		$query	.= ' FROM '.$db->nameQuote('#__easyblog_post');
		$query	.= ' WHERE '.$db->nameQuote('published').' = '.$db->Quote(EASYBLOG_POST_PUBLISHED);
		$query	.= ' AND '.$db->nameQuote('state').' = '.$db->Quote(EASYBLOG_POST_NORMAL);
		$query	.= ' ' . $privateBlog;
		$query	.= ' AND ('.$db->nameQuote('created').' > '.$db->Quote($year.'-'.$month.'-01 00:00:00').' AND '.$db->nameQuote('created').' < '.$db->Quote($year.'-'.$month.'-31 23:59:59').')';
		$query	.= ' GROUP BY day, year, month ';
		$query	.= ' ORDER BY '.$db->nameQuote('created').' ASC ';

		$db->setQuery($query);
		$row = $db->loadAssocList();

		$postCount = new stdClass();

		for($i=1; $i<=31; $i++)
		{
			$postCount->{$year}->{$month}->{$i} = 0;
		}

		if(!empty($row))
		{
			foreach($row as $data)
			{
				$postCount->{$year}->{$month}->{$data['day']} = $data['count'];
			}
		}

		return $postCount;
	}

	/**
	 * Retrieves a simple list of blog posts by specific month / year
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getArchivePostListByMonth($month='', $year='', $showPrivate = false, $category = '')
	{
		$db = EB::db();
		$user = JFactory::getUser();
		$config = EB::config();

		// used for privacy
		$queryWhere = '';
		$queryExclude = '';
		$queryExcludePending = '';
		$excludeCats = array();

		if( $user->id == 0) {
			$showPrivate = false;
		}

		// Blog privacy setting
		// @integrations: jomsocial privacy
		$privateBlog = '';

		if (EB::easysocial()->exists() && $config->get('integrations_easysocial_privacy') && !EB::isSiteAdmin()) {
			$esPrivacyQuery = EB::easysocial()->buildPrivacyQuery('a');
			$privateBlog .= $esPrivacyQuery;
		} else if ($config->get('main_jomsocial_privacy') && EB::jomsocial()->exists() && !EB::isSiteAdmin()) {

			$friendsModel = CFactory::getModel('Friends');
			$friends = $friendsModel->getFriendIds( $user->id );

			// Insert query here.
			$privateBlog .= ' AND (';
			$privateBlog .= ' (a.`access`= 0 ) OR';
			$privateBlog .= ' ( (a.`access` = 20) AND (' . $db->Quote( $user->id ) . ' > 0 ) ) OR';

			if (!$friends) {
				$privateBlog .= ' ( (a.`access` = 30) AND ( 1 = 2 ) ) OR';
			} else {
				$privateBlog .= ' ( (a.`access` = 30) AND ( a.' . $db->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
			}

			$privateBlog .= ' ( (a.`access` = 40) AND ( a.' . $db->nameQuote( 'created_by' ) .'=' . $user->id . ') )';
			$privateBlog .= ' )';
		} else {

			if ($user->id == 0) {
				$privateBlog .= ' AND a.`access` = ' . $db->Quote(0);
			}
		}

		// Join the query ?
		$privateBlog = $showPrivate? '' : $privateBlog;


		$isJSGrpPluginInstalled	= false;
		$isJSGrpPluginInstalled	= JPluginHelper::isEnabled( 'system', 'groupeasyblog');
		$isEventPluginInstalled	= JPluginHelper::isEnabled( 'system' , 'eventeasyblog' );
		$isJSInstalled			= false; // need to check if the site installed jomsocial.

		if (EB::jomsocial()->exists()) {
			$isJSInstalled = true;
		}

		$includeJSGrp	= ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent	= ($isEventPluginInstalled && $isJSInstalled ) ? true : false;

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

		if (EB::easysocial()->exists()) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP, 'a');
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT, 'a');
		}


		$contributeSQL .= ')';

		$queryWhere .= $contributeSQL;

	    //get teamblogs id.
	    $query  		= '';

		$extraSQL   = '';

		// If this is on blogger mode, we need to only pick items from the blogger.
		$blogger = EBR::isBloggerMode();
		if ($blogger !== false) {
		    $extraSQL = ' AND a.`created_by` = ' . $db->Quote($blogger);
		}

		$tzoffset = EB::date()->getOffSet(true);

		$query	= 'SELECT a.*, DAY( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS day,';
		$query	.= ' MONTH( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS month,';
		$query	.= ' YEAR( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS year ';
		$query  .= ' FROM '.$db->nameQuote('#__easyblog_post') . ' as a';
		$query  .= ' WHERE a.`published` = '.$db->Quote(EASYBLOG_POST_PUBLISHED).' ';
		$query  .= ' AND a.' . $db->quoteName('state') . ' = '.$db->Quote(EASYBLOG_POST_NORMAL).' ';
		$query  .= $privateBlog.' ';
		$query  .= ' AND (a.`created` > ' . $db->Quote($year.'-'.$month.'-01 00:00:00') . ' AND a.`created` < ' . $db->Quote($year.'-'.$month.'-31 23:59:59').') ';

		// If do not display private posts, we need to append additional queries here.
		if (!$showPrivate) {
			// sql for category access
			$catLib = EB::category();
			$options = array();
			
			if ($category) {
				$categories	= explode( ',' , $category );
				$options['include'] = $categories;
			}

			$catAccessSQL = $catLib->genAccessSQL('a.`id`', $options);
			$query .= ' AND (' . $catAccessSQL . ')';
		}


		$query  .= $extraSQL . ' ';
		$query	.= $queryWhere;
		$query  .= ' ORDER BY a.`created` ASC ';


		// echo $query;exit;

		$db->setQuery($query);
		$posts = $db->loadObjectList();

		return $posts;
	}

	/**
	 * Retrieves a list of blog posts by specific month
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getArchivePostByMonth($month='', $year='', $showPrivate = false, $category = '')
	{
		$db = EB::db();
		$user = JFactory::getUser();
		$config = EB::config();

		// used for privacy
		$queryWhere = '';
		$queryExclude = '';
		$queryExcludePending = '';
		$excludeCats = array();

		if( $user->id == 0) {
			$showPrivate = false;
		}

		// Blog privacy setting
		// @integrations: jomsocial privacy
		$privateBlog = '';

		if (EB::easysocial()->exists() && $config->get('integrations_easysocial_privacy') && !EB::isSiteAdmin()) {
			$esPrivacyQuery = EB::easysocial()->buildPrivacyQuery('a');
			$privateBlog .= $esPrivacyQuery;
		} else if ($config->get('main_jomsocial_privacy') && EB::jomsocial()->exists() && !EB::isSiteAdmin()) {

			$friendsModel = CFactory::getModel('Friends');
			$friends = $friendsModel->getFriendIds( $user->id );

			// Insert query here.
			$privateBlog .= ' AND (';
			$privateBlog .= ' (a.`access`= 0 ) OR';
			$privateBlog .= ' ( (a.`access` = 20) AND (' . $db->Quote( $user->id ) . ' > 0 ) ) OR';

			if (!$friends) {
				$privateBlog .= ' ( (a.`access` = 30) AND ( 1 = 2 ) ) OR';
			} else {
				$privateBlog .= ' ( (a.`access` = 30) AND ( a.' . $db->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
			}

			$privateBlog .= ' ( (a.`access` = 40) AND ( a.' . $db->nameQuote( 'created_by' ) .'=' . $user->id . ') )';
			$privateBlog .= ' )';
		} else {

			if ($user->id == 0) {
				$privateBlog .= ' AND a.`access` = ' . $db->Quote(0);
			}
		}

		// Join the query ?
		$privateBlog = $showPrivate? '' : $privateBlog;


		$isJSGrpPluginInstalled	= false;
		$isJSGrpPluginInstalled	= JPluginHelper::isEnabled( 'system', 'groupeasyblog');
		$isEventPluginInstalled	= JPluginHelper::isEnabled( 'system' , 'eventeasyblog' );
		$isJSInstalled			= false; // need to check if the site installed jomsocial.

		if (EB::jomsocial()->exists()) {
			$isJSInstalled = true;
		}

		$includeJSGrp	= ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent	= ($isEventPluginInstalled && $isJSInstalled ) ? true : false;

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

		if (EB::easysocial()->exists()) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP, 'a');
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT, 'a');
		}


		$contributeSQL .= ')';

		$queryWhere .= $contributeSQL;

	    //get teamblogs id.
	    $query  		= '';

		$extraSQL   = '';

		// If this is on blogger mode, we need to only pick items from the blogger.
		$blogger = EBR::isBloggerMode();
		if ($blogger !== false) {
		    $extraSQL = ' AND a.`created_by` = ' . $db->Quote($blogger);
		}

		$tzoffset = EB::date()->getOffSet(true);

		$query	= 'SELECT a.*, DAY( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS day,';
		$query	.= ' MONTH( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS month,';
		$query	.= ' YEAR( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS year ';
		$query  .= ' FROM '.$db->nameQuote('#__easyblog_post') . ' as a';
		$query  .= ' WHERE a.`published` = '.$db->Quote(EASYBLOG_POST_PUBLISHED).' ';
		$query  .= ' AND a.' . $db->quoteName('state') . ' = '.$db->Quote(EASYBLOG_POST_NORMAL).' ';
		$query  .= $privateBlog.' ';
		$query  .= ' AND (a.`created` > ' . $db->Quote($year.'-'.$month.'-01 00:00:00') . ' AND a.`created` < ' . $db->Quote($year.'-'.$month.'-31 23:59:59').') ';

		// If do not display private posts, we need to append additional queries here.
		if (!$showPrivate) {
			// sql for category access
			$catLib = EB::category();
			$options = array();
			
			if ($category) {
				$categories	= explode( ',' , $category );
				$options['include'] = $categories;
			}

			$catAccessSQL = $catLib->genAccessSQL('a.`id`', $options);
			$query .= ' AND (' . $catAccessSQL . ')';
		}


		$query  .= $extraSQL . ' ';
		$query	.= $queryWhere;
		$query  .= ' ORDER BY a.`created` ASC ';


		// echo $query;exit;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$postCount = new EasyblogCalendarObject($month, $year);

		if (!empty($result)) {
			foreach ($result as $row) {

				$post = EB::post($row->id);
				// $post->bind($row);

				$post = EB::formatter('entry', $post);


				// var_dump($row);exit;

				if ($postCount->{$year}->{$month}->{$row->day} == 0) {
					$postCount->{$year}->{$month}->{$row->day} = array($post);
				} else {
					array_push($postCount->{$year}->{$month}->{$row->day}, $post);
				}
			}
		}

		return $postCount;
	}

}

class EasyblogCalendarObject
{
	public function __construct( $month, $year )
	{
		$this->{$year} = new stdClass();
		$this->{$year}->{$month} = new stdClass();

		for($i=1; $i<=31; $i++)
		{
			$this->{$year}->{$month}->{$i} = 0;
		}
	}
}
