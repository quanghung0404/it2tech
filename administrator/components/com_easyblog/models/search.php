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

class EasyBlogModelSearch extends EasyBlogAdminModel
{
	public $_data = null;
	public $_pagination = null;
	public $_total = null;

	public function __construct()
	{
		parent::__construct();

		$mainframe 	= JFactory::getApplication();

		//get the number of events from database
		$limit       	= $mainframe->getUserStateFromRequest('com_easyblog.blogs.limit', 'limit', $mainframe->getCfg('list_limit') , 'int');
		$limitstart		= JRequest::getInt('limitstart', 0, '' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination	= EB::pagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	public function getTotal()
	{
		// Load total number of rows
		if( empty($this->_total) )
		{
			$this->_total	= $this->_getListCount( $this->_buildQuery() );
		}

		return $this->_total;
	}

	public function _buildQuery()
	{
		$db			= EB::db();
		$my			= JFactory::getUser();
		$config     = EasyBlogHelper::getConfig();

		// used for privacy
		$queryWhere             = '';
		$queryExclude			= '';
		$queryExcludePending    = '';
		$excludeCats			= array();
		$isBloggerMode  		= EasyBlogRouter::isBloggerMode();

		$where		= array();
		$where2		= array();
		$text		= JRequest::getVar( 'query' );

		$words		= explode( ' ', $text );
		$wheres		= array();

		foreach ($words as $word)
		{
			$word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );

			$where[]	= 'a.`title` LIKE ' . $word;
			$where[]	= 'a.`content` LIKE ' . $word;
			$where[]	= 'a.`intro` LIKE ' . $word;

			$where2[]	= 't.title LIKE ' . $word;
			$wheres2[]	= implode( ' OR ' , $where2	);

			$wheres[] 	= implode( ' OR ', $where );
		}
		$where	= '(' . implode( ') OR (' , $wheres ) . ')';
		$where2	= '(' . implode( ') OR (' , $wheres2 ) . ')';

	    $isJSGrpPluginInstalled = false;
	    $isJSGrpPluginInstalled = JPluginHelper::isEnabled( 'system', 'groupeasyblog');
	    $isEventPluginInstalled = JPluginHelper::isEnabled( 'system' , 'eventeasyblog' );
	    $isJSInstalled      = false; // need to check if the site installed jomsocial.

	    if(JFile::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR .'core.php'))
	    {
	      $isJSInstalled = true;
	    }

	    $includeJSGrp = ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
	    $includeJSEvent = ($isEventPluginInstalled && $isJSInstalled ) ? true : false;

	    $query  		= '';

	    // contribution type sql
	    $contributor = EB::contributor();
	    $contributeSQL = ' AND ( (a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';
	    if( $config->get( 'main_includeteamblogpost' )) {
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

		// category access here

		$catLib = EB::category();
		$catAccessSQL = $catLib->genAccessSQL('a.`id`');
		$queryWhere .= ' AND (' . $catAccessSQL . ')';


		if( $isBloggerMode )
		{
			$queryWhere .= ' AND a.`created_by`=' . $db->Quote( $isBloggerMode );
		}

		$query	= 'SELECT a.*, CONCAT(a.`content` , a.`intro`) AS text';
		$query	.= ' FROM `#__easyblog_post` as a USE INDEX (`easyblog_post_searchnew`)';

		// Always inner join with jos_users and a.created_by so that only valid blogs are loaded
		$query .= ' INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS c ON a.`created_by`=c.`id`';

		$query	.= ' WHERE (' . $where;

		$query	.= ' OR a.`id` IN( ';
		$query	.= '		SELECT tp.`post_id` FROM `#__easyblog_tag` AS t ';
		$query	.= '		INNER JOIN `#__easyblog_post_tag` AS tp ON tp.`tag_id` = t.`id` ';
		$query	.= '		WHERE ' . $where2;
		$query	.= ') )';

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
			$friends	= $jsFriends->getFriendIds( $my->id );

			// Insert query here.
			$privateBlog	.= ' AND (';
			$privateBlog	.= ' (a.`access`= 0 ) OR';
			$privateBlog	.= ' ( (a.`access` = 20) AND (' . $db->Quote( $my->id ) . ' > 0 ) ) OR';

			if( empty( $friends ) )
			{
				$privateBlog	.= ' ( (a.`access` = 30) AND ( 1 = 2 ) ) OR';
			}
			else
			{
				$privateBlog	.= ' ( (a.`access` = 30) AND ( a.' . $db->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
			}

			$privateBlog	.= ' ( (a.`access` = 40) AND ( a.' . $db->nameQuote( 'created_by' ) .'=' . $my->id . ') )';
			$privateBlog	.= ' )';
		}
		else
		{
			if( $my->id == 0)
			{
				$privateBlog .= ' AND a.`access` = ' . $db->Quote(0);
			}
		}

		if ($privateBlog) {
			$query    .= $privateBlog;
		}



		//do not show unpublished post
		$query	.= ' AND a.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query	.= ' AND a.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);

		// @rule: When language filter is enabled, we need to detect the appropriate contents
		$filterLanguage 	= JFactory::getApplication()->getLanguageFilter();
		if ($filterLanguage) {
			$query	.= EBR::getLanguageQuery('AND', 'a.language');
		}


		$query	.= $queryWhere;
		$query	.= ' ORDER BY a.`created` DESC';

		// echo '<br><br><br>' . $query;

		return $query;
	}

	public function getData()
	{
		if (empty($this->_data)) {
			$query = $this->_buildQuery();

			$this->_data	= $this->_getList( $query , $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}

	public function searchtext($text)
	{
		if(empty($text)) {
			return false;
		}

		JRequest::setVar('query', $text);

		return $this->getData();
	}
}
