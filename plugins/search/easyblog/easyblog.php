<?php
/**
* @package		Eblog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Eblog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

//JPlugin::loadLanguage( 'plg_search_easyblog' );

class plgSearchEasyblog extends JPlugin
{

	public function __construct(&$subject, $params)
	{
		parent::__construct($subject, $params);
	}

	/** 1.6 **/
	public function onContentSearchAreas()
	{
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		$areas = array(
			'blogs' => JText::_( 'PLG_EASYBLOG_SEARCH_BLOGS' )
			);
			return $areas;
	}

	/** 1.5 **/
	public function onSearchAreas()
	{
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		$areas = array(
			'blogs' => JText::_( 'PLG_EASYBLOG_SEARCH_BLOGS' )
		);
		return $areas;
	}

	/** 1.6 **/
	public function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		return $this->onSearch( $text, $phrase, $ordering, $areas );
	}

	/** 1.5 **/
	public function onSearch( $text, $phrase='', $ordering='', $areas=null )
	{
	 	$plugin	= JPluginHelper::getPlugin('search', 'easyblog');
	 	$params = EB::registry($plugin->params);

		if( !plgSearchEasyblog::exists() )
		{
			return array();
		}

		if (is_array( $areas )) {
			if (!array_intersect( $areas, array_keys( plgSearchEasyblog::onContentSearchAreas() ) )) {
				return array();
			}
		}

		$text = trim( $text );
		if ($text == '') {
			return array();
		}

		$result	= plgSearchEasyblog::getResult( $text , $phrase , $ordering );

		if( !$result )
			return array();

		// require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );

		foreach($result as $row)
		{
			$row->section	= plgSearchEasyblog::getCategory( $row->category_id );
			$row->section	= JText::sprintf( 'PLG_EASYBLOG_SEARCH_BLOGS_SECTION', $row->section);
			$row->href		= EBR::_( 'index.php?option=com_easyblog&view=entry&id=' . $row->id );

			$blog 			= EB::table('Blog');
			$blog->bind( $row );

			if( $blog->getImage() )
			{
				$row->image 	= $blog->getImage( 'frontpage' );
			}
		}

		return $result;
	}

	public function exists()
	{
		$path	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'easyblog.xml';

		jimport( 'joomla.filesystem.file' );
		return JFile::exists( $path );
	}

	public function getCategory( $categoryId )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT `title` FROM #__easyblog_category WHERE id=' . $db->Quote( $categoryId );
		$db->setQuery( $query );

		return $db->loadResult();
	}

	public function getResult( $text , $phrase , $ordering )
	{
		$config	= EasyBlogHelper::getConfig();
		$my     = JFactory::getUser();

		$db		= EasyBlogHelper::db();
		$where	= array();
		$where2	= array();

		// used for privacy
		$queryWhere             = '';
		$queryExclude			= '';
		$queryExcludePending    = '';
		$excludeCats			= array();

		switch ($phrase)
		{
			case 'exact':
				$where[]	= 'a.`title` LIKE ' . $db->Quote( '%'. $db->escape($text, true) .'%', false );
				$where[]	= 'a.`content` LIKE ' . $db->Quote( '%'. $db->escape($text, true) .'%', false );
				$where[]	= 'a.`intro` LIKE ' . $db->Quote( '%'. $db->escape($text, true) .'%', false );

				$where2		= '( t.title LIKE ' . $db->Quote( '%'. $db->escape($text, true) .'%', false ) . ')';
				$where 		= '(' . implode( ') OR (', $where ) . ')';
				break;
			case 'all':
			case 'any':
			default:
				$words		= explode( ' ', $text );
				$wheres		= array();
				$where2		= array();
				$wheres2	= array();

				foreach ($words as $word)
				{
					$word		= $db->Quote( '%'. $db->escape($word, true) .'%', false );

					$where[]	= 'a.`title` LIKE ' . $word;
					$where[]	= 'a.`content` LIKE ' . $word;
					$where[]	= 'a.`intro` LIKE ' . $word;

					$where2[]	= 't.title LIKE ' . $word;

					$wheres[] 	= implode( ' OR ', $where );
					$wheres2[]	= implode( ' OR ' , $where2	);
				}
				$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				$where2	= '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres2 ) . ')';
				break;
		}

		$isJSGrpPluginInstalled	= false;
		$isJSGrpPluginInstalled	= JPluginHelper::isEnabled( 'system', 'groupeasyblog');
		$isEventPluginInstalled	= JPluginHelper::isEnabled( 'system' , 'eventeasyblog' );
		$isJSInstalled			= false; // need to check if the site installed jomsocial.

		if(JFile::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR .'core.php'))
		{
			$isJSInstalled = true;
		}

		$includeJSGrp	= ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent	= ($isEventPluginInstalled && $isJSInstalled ) ? true : false;


	    //get teamblogs id.
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


		$query	= 'SELECT a.*, CONCAT(a.`content` , a.`intro`) AS text , "2" as browsernav';
		$query	.= ' FROM `#__easyblog_post` as a USE INDEX (`easyblog_post_searchnew`) ';

		$query	.= ' WHERE (' . $where;
		$query	.= ' OR a.`id` IN( ';
		$query	.= '		SELECT tp.`post_id` FROM `#__easyblog_tag` AS t ';
		$query	.= '		INNER JOIN `#__easyblog_post_tag` AS tp ON tp.`tag_id` = t.`id` ';
		$query	.= '		WHERE ' . $where2;
		$query	.= '))';


		$my = JFactory::getUser();
		if($my->id == 0)
		{
		    //guest should only see public post.
		    $query    .= ' AND a.`access` = ' . $db->Quote('0');
		}

		//do not show unpublished post
		$query    .= ' AND a.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query    .= ' AND a.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);


		$query  .= $queryWhere;

		switch( $ordering )
		{
			case 'oldest':
				$query 	.= ' ORDER BY a.`created` ASC';
			break;
			case 'newest':
				$query 	.= ' ORDER BY a.`created` DESC';
			break;
		}

		$db->setQuery( $query );
		return $db->loadObjectList();
	}

}
