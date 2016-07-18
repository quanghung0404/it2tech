<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'admin:/includes/apps/apps' );

/**
 * Friends application for EasySocial.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialGroupAppMembers extends SocialAppItem
{
	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		// We need the router
		require_once( JPATH_ROOT . '/components/com_content/helpers/route.php' );

		parent::__construct();
	}

	/**
	 * Prepares the stream item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem	The stream object.
	 * @param	bool				Determines if we should respect the privacy
	 */
	public function onPrepareStream( SocialStreamItem &$item, $includePrivacy = true )
	{
		if( $item->context != 'article' )
		{
			return;
		}

		// Define standard stream looks
		$item->display 	= SOCIAL_STREAM_DISPLAY_FULL;
		$item->color 	= '#1616d3';
		$item->icon 	= '<i class="fa fa-copy" data-original-title="' . FD::_( 'APP_ARTICLE_STREAM_TOOLTIP', true ) . '" data-es-provide="tooltip"></i>';

		if( $item->verb == 'create' )
		{
			$this->prepareCreateArticleStream( $item );
		}

		if( $item->verb == 'update' )
		{
			$this->prepareUpdateArticleStream( $item );
		}

		if( $item->verb == 'read' )
		{
			$this->prepareReadArticleStream( $item );
		}
	}

	/**
	 * Prepares the stream item for new article creation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem	The stream item.
	 * @return
	 */
	private function prepareCreateArticleStream( &$item )
	{
		// Load up the article dataset
		$article 	= JTable::getInstance( 'Content' );
		$article->load( $item->contextId );

		// Load up the category dataset
		$category	= JTable::getInstance( 'Category' );
		$category->load( $article->catid );

		// Get the actor
		$actor 		= $item->actor;

		// Get the permalink
		$permalink	= ContentHelperRoute::getArticleRoute( $article->id . ':' . $article->alias , $article->catid . ':' . $category->alias );

		// Get the creation date
		$date 		= FD::date( $article->created );

		// Get the category permalink
		$categoryPermalink 	= ContentHelperRoute::getCategoryRoute( $category->id . ':' . $category->alias );

		// Get the content
		$content 	= $article->introtext;

		if( empty( $content ) )
		{
			$content 	= $article->fulltext;
		}

		$this->set( 'content'	, $content );
		$this->set( 'categoryPermalink' , $categoryPermalink );
		$this->set( 'date'		, $date );
		$this->set( 'permalink'	, $permalink );
		$this->set( 'article'	, $article );
		$this->set( 'category'	, $category );
		$this->set( 'actor'		, $actor );

		// Load up the contents now.
		$item->title 	= parent::display( 'streams/create.title' );
		$item->content 	= parent::display( 'streams/create.content' );
	}

	/**
	 * Prepares the stream item for new article creation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem	The stream item.
	 * @return
	 */
	private function prepareUpdateArticleStream( &$item )
	{
		// Load up the article dataset
		$article 	= JTable::getInstance( 'Content' );
		$article->load( $item->contextId );

		// Load up the category dataset
		$category	= JTable::getInstance( 'Category' );
		$category->load( $article->catid );

		// Get the actor
		$actor 		= $item->actor;

		// Get the permalink
		$permalink	= ContentHelperRoute::getArticleRoute( $article->id . ':' . $article->alias , $article->catid . ':' . $category->alias );

		// Get the creation date
		$date 		= FD::date( $article->created );

		// Get the category permalink
		$categoryPermalink 	= ContentHelperRoute::getCategoryRoute( $category->id . ':' . $category->alias );

		// Get the content
		$content 	= $article->introtext;

		if( empty( $content ) )
		{
			$content 	= $article->fulltext;
		}

		$this->set( 'content'	, $content );
		$this->set( 'categoryPermalink' , $categoryPermalink );
		$this->set( 'date'		, $date );
		$this->set( 'permalink'	, $permalink );
		$this->set( 'article'	, $article );
		$this->set( 'category'	, $category );
		$this->set( 'actor'		, $actor );

		// Load up the contents now.
		$item->title 	= parent::display( 'streams/update.title' );
		$item->content 	= parent::display( 'streams/update.content' );
	}

	/**
	 * Prepares the stream item when an article is being read
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem	The stream item.
	 * @return
	 */
	private function prepareReadArticleStream( &$item )
	{
		// Load up the article dataset
		$article 	= JTable::getInstance( 'Content' );
		$article->load( $item->contextId );

		// Load up the category dataset
		$category	= JTable::getInstance( 'Category' );
		$category->load( $article->catid );

		// Get the actor
		$actor 		= $item->actor;

		// Get the permalink
		$permalink	= ContentHelperRoute::getArticleRoute( $article->id . ':' . $article->alias , $article->catid . ':' . $category->alias );

		// Get the creation date
		$date 		= FD::date( $article->created );

		// Get the category permalink
		$categoryPermalink 	= ContentHelperRoute::getCategoryRoute( $category->id . ':' . $category->alias );

		// Get the content
		$content 	= $article->introtext;

		if( empty( $content ) )
		{
			$content 	= $article->fulltext;
		}

		$this->set( 'content'	, $content );
		$this->set( 'categoryPermalink' , $categoryPermalink );
		$this->set( 'date'		, $date );
		$this->set( 'permalink'	, $permalink );
		$this->set( 'article'	, $article );
		$this->set( 'category'	, $category );
		$this->set( 'actor'		, $actor );

		// Load up the contents now.
		$item->title 	= parent::display( 'streams/read.title' );
		$item->content 	= parent::display( 'streams/read.content' );
	}
}
