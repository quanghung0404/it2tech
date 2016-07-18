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
 * K2 application for EasySocial.
 *
 * @since	1.2
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialUserAppK2 extends SocialAppItem
{
	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct( $options = array() )
	{
		// We need the router
		require_once( JPATH_ROOT . '/components/com_content/helpers/route.php' );

		parent::__construct( $options );
	}


	/**
	 * Triggered to validate the stream item whether should put the item as valid count or not.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	jos_social_stream, boolean
	 * @return  0 or 1
	 */
	public function onStreamCountValidation( &$item, $includePrivacy = true )
	{
		// If this is not it's context, we don't want to do anything here.
		if( $item->context_type != 'k2')
		{
			return false;
		}

		$item->cnt = 1;

		if( $includePrivacy )
		{
			$uid		= $item->id;
			$my         = FD::user();
			$privacy	= FD::privacy( $my->id );

			$sModel = FD::model( 'Stream' );
			$aItem 	= $sModel->getActivityItem( $item->id, 'uid' );

			if( $aItem )
			{
				$uid 	= $aItem[0]->id;

				if( !$privacy->validate( 'core.view', $uid , SOCIAL_TYPE_ACTIVITY , $item->actor_id ) )
				{
					$item->cnt = 0;
				}
			}
		}

		return true;
	}

	/**
	 * Prepares the activity log item
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialStreamItem	The stream object.
	 * @param	bool				Determines if we should respect the privacy
	 */
	public function onPrepareActivityLog( SocialStreamItem &$item, $includePrivacy = true )
	{
		if( $item->context != 'k2' )
		{
			return;
		}

		// Get the context id.
		$actor 		= $item->actor;
		$article 	= $this->getArticle( $item );
		$category	= $this->getCategory( $item , $article );
		$permalink	= $this->getPermalink( $item , $article , $category );

		$this->set( 'permalink'	, $permalink );
		$this->set( 'article'	, $article );
		$this->set( 'actor'		, $actor );

		// Load up the contents now.
		$item->title 	= parent::display( 'streams/' . $item->verb . '.title' );
		$item->content 	= '';

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
		if( $item->context != 'k2' )
		{
			return;
		}

		// Decorate the stream item
		$item->display 		= SOCIAL_STREAM_DISPLAY_FULL;
		$item->color 		= '#415457';
		$item->fonticon 	= 'fa-list';

		$item->label 	= FD::_('APP_USER_K2_STREAM_TOOLTIP', true);

		// Get application params
		$params 	= $this->getParams();

		if ($item->verb == 'create' && $params->get('stream_create', true)) {
			$this->prepareCreateArticleStream($item);
		}

		if ($item->verb == 'update' && $params->get('stream_update', true)) {
			$this->prepareUpdateArticleStream($item);
		}

		if ($item->verb == 'read' && $params->get('stream_read', true)) {
			$this->prepareReadArticleStream($item);
		}
	}

	/**
	 * Determines if the viewer has access to view this article
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canView(&$article)
	{
		$my = Foundry::user();
		$viewLevels = $my->getAuthorisedViewLevels();

		if (!in_array($article->access, $viewLevels) || !in_array($article->category->access, $viewLevels)) {
			return false;
		}

		return true;
	}

	/**
	 * Prepares the stream item for new article creation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem	The stream item.
	 * @return
	 */
	private function prepareCreateArticleStream(&$item)
	{
		// Retrieve initial data
		$article = $this->getArticle($item);
		$category = $this->getCategory($item, $article);
		$permalink = $this->getPermalink($item, $article, $category);
		$categoryPermalink = $this->getCategoryPermalink($item, $category);

		// Ensure that the user can really view this article
		if (!$this->canView($article)) {
			return;
		}

		// Get the actor
		$actor = $item->actor;

		// Get the creation date
		$date 		= FD::date( $article->created );

		// Get the content
		$content = $article->introtext;

		if (empty($content)) {
			$content = $article->fulltext;
		}

		$content = preg_replace( '/\{.*\}/i', '', $content );

		// Limit the content length
		$params 		= $this->getParams();
		$contentLength	= $params->get('stream_content_length');

		if ($contentLength) {
			$content = JString::substr(strip_tags($content), 0, $contentLength) . JText::_('COM_EASYSOCIAL_ELLIPSES');
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

		// Append the opengraph tags
		$item->addOgDescription($content);
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
		// Retrieve initial data
		$article 			= $this->getArticle( $item );
		$category			= $this->getCategory( $item , $article );
		$permalink			= $this->getPermalink( $item , $article , $category );
		$categoryPermalink	= $this->getCategoryPermalink( $item , $category );

		// Ensure that the user can really view this article
		if (!$this->canView($article)) {
			return;
		}

		// Get the creation date
		$date 		= FD::date( $article->created );

		// Get the actor
		$actor 		= $item->actor;

		// Get the content
		$content 	= $article->introtext;

		if( empty( $content ) )
		{
			$content 	= $article->fulltext;
		}

		$content = preg_replace( '/\{.*\}/i', '', $content );

		// Limit the content length
		$params 		= $this->getParams();
		$contentLength	= $params->get('stream_content_length');

		if ($contentLength) {
			$content 	= JString::substr(strip_tags($content), 0, $contentLength) . JText::_('COM_EASYSOCIAL_ELLIPSES');
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

		// Append the opengraph tags
		$item->addOgDescription($content);
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
		// Retrieve initial data
		$article 			= $this->getArticle( $item );
		$category			= $this->getCategory( $item , $article );
		$permalink			= $this->getPermalink( $item , $article , $category );
		$categoryPermalink	= $this->getCategoryPermalink( $item , $category );

		// Ensure that the user can really view this article
		if (!$this->canView($article)) {
			return;
		}

		// Get the actor
		$actor 		= $item->actor;

		// Only proceed if the actor is not a guest.
		if (!$item->actor->id) {
			return;
		}

		// Get the creation date
		$date 		= FD::date( $article->created );

		// Get the content
		$content 	= $article->introtext;

		if( empty( $content ) )
		{
			$content 	= $article->fulltext;
		}

		$content = preg_replace( '/\{.*\}/i', '', $content );

		// Limit the content length
		$params 		= $this->getParams();
		$contentLength	= $params->get('stream_content_length');

		if ($contentLength) {
			$content 	= JString::substr(strip_tags($content), 0, $contentLength) . JText::_('COM_EASYSOCIAL_ELLIPSES');
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

		// Append the opengraph tags
		$item->addOgDescription($content);
	}

	/**
	 * Retrieves the row data from K2
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getArticle($item)
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__k2_items');
		$sql->where('id', $item->contextId);
		$sql->limit(1);

		$db->setQuery($sql);
		$article = $db->loadObject();

		if (!$article->id) {
			return false;
		}

		// Include K2's table
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_k2/tables');
		$category = JTable::getInstance('K2Category', 'Table');
		$category->load($article->catid);

		$article->category = $category;

		return $article;
	}

	private function getCategory( $item , $article )
	{
		// Load up the category dataset
		JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_k2/tables' );
		$category = JTable::getInstance('K2Category', 'Table');

		if( $item->params )
		{
			$registry 	= FD::registry( $item->params );

			if( $registry->get( 'category' ) )
			{
				$category->bind( (array) $registry->get( 'category' ) );
			}
		}
		else
		{
			$category->load( $article->catid );
		}

		return $category;
	}

	private function getPermalink( $item , $article , $category )
	{
		if( $item->params )
		{
			$registry 		= FD::registry( $item->params );
			$permalink 		= $registry->get( 'permalink' );

			// we need to jroute the link or else the link will not be in sef format when content retrive via ajax.
			$permalink 		= JRoute::_($permalink);
		}
		else
		{
			// Get the permalink
			$permalink	= ContentHelperRoute::getArticleRoute( $article->id . ':' . $article->alias , $article->catid . ':' . $category->alias );
		}

		return $permalink;
	}

	private function getCategoryPermalink( $item , $category )
	{
		if( $item->params )
		{
			$registry 	= FD::registry( $item->params );

			$categoryPermalink	= $registry->get( 'categoryPermalink' );

			// we need to jroute the link or else the link will not be in sef format when content retrive via ajax.
			$categoryPermalink 		= JRoute::_($categoryPermalink);
		}
		else
		{
			// Get the category permalink
			$categoryPermalink 	= ContentHelperRoute::getCategoryRoute( $category->id . ':' . $category->alias );
		}

		return $categoryPermalink;
	}

	public function onAfterCommentSave($comment)
	{
		$allowed = array('k2.user.create', 'k2.user.update', 'k2.user.read');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		list($element, $group, $verb) = explode('.', $comment->element);

		$streamItem = FD::table('streamitem');
		$state = $streamItem->load(array('context_type' => $element, 'actor_type' => $group, 'verb' => $verb, 'context_id' => $comment->uid));

		if (!$state) {
			return;
		}

		$owner = $streamItem->actor_id;

		$emailOptions = array(
			'title' => 'APP_USER_K2_EMAILS_COMMENT_ITEM_TITLE',
			'template' => 'apps/user/k2/comment.item',
			'permalink' => $streamItem->getPermalink(true, true)
		);

		$systemOptions = array(
			'title' => '',
			'content' => $comment->comment,
			'context_type' => $comment->element,
			'url' => $streamItem->getPermalink(false, false, false),
			'actor_id' => $comment->created_by,
			'uid' => $comment->uid,
			'aggregate' => true
		);

		if ($comment->created_by != $owner) {
			FD::notify('comments.item', array($owner), $emailOptions, $systemOptions);
		}

		$recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb, array(), array($owner, $comment->created_by));

		$emailOptions['title'] = 'APP_USER_K2_EMAILS_COMMENT_INVOLVED_TITLE';
		$emailOptions['template'] = 'apps/user/k2/comment.involved';

		FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
	}

	public function onAfterLikeSave($like)
	{
		$allowed = array('k2.user.create', 'k2.user.update', 'k2.user.read');

		if (!in_array($like->type, $allowed)) {
			return;
		}

		$segments = $like->type;

		list($element, $group, $verb) = explode('.', $segments);

		$streamItem = FD::table('streamitem');
		$state = $streamItem->load(array('context_type' => $element, 'actor_type' => $group, 'verb' => $verb, 'context_id' => $likes->uid));

		if (!$state) {
			return;
		}

		$owner = $streamItem->actor_id;

		$emailOptions = array(
			'title' => 'APP_USER_K2_EMAILS_LIKE_ITEM_TITLE',
			'template' => 'apps/user/k2/like.item',
			'permalink' => $streamItem->getPermalink(true, true)
		);

		$systemOptions = array(
			'title' => '',
			'context_type' => $likes->type,
			'url' => $streamItem->getPermalink(false, false, false),
			'actor_id' => $likes->created_by,
			'uid' => $likes->uid,
			'aggregate' => true
		);

		if ($likes->created_by != $owner) {
			FD::notify('likes.item', array($owner), $emailOptions, $systemOptions);
		}

		$recipients = $this->getStreamNotificationTargets($likes->uid, $element, $group, $verb, array(), array($owner, $likes->created_by));

		$emailOptions['title'] = 'APP_USER_K2_EMAILS_LIKE_INVOLVED_TITLE';
		$emailOptions['template'] = 'apps/user/k2/like.involved';

		FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);
	}

	public function onNotificationLoad(SocialTableNotification &$item)
	{
		// k2.user.create
		// k2.user.update
		// k2.user.read

		$allowed = array('k2.user.create', 'k2.user.update', 'k2.user.read');

		if (!in_array($item->context_type, $allowed)) {
			return;
		}

		$hook = $this->getHook('notification', $item->type);
		$hook->execute($item);

		return;
	}
}
