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
class SocialUserAppArticle extends SocialAppItem
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
	 * Responsible to return the favicon object
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFavIcon()
	{
		$obj 			= new stdClass();
		$obj->color		= '#FCCD1B';
		$obj->icon 		= 'fa fa-list';
		$obj->label 	= 'APP_USER_ARTICLE_STREAM_TOOLTIP';

		return $obj;
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
		if( $item->context_type != 'article')
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
		if( $item->context != 'article' )
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
	 * Responsible to return the excluded verb from this app context
	 * @since	1.2
	 * @access	public
	 * @param	array
	 */
	public function onStreamVerbExclude( &$exclude )
	{
		// Get app params
		$params		= $this->getParams();

		$excludeVerb = false;

		if(! $params->get('stream_create', true)) {
			$excludeVerb[] = 'create';
		}

		if (! $params->get('stream_update', true)) {
			$excludeVerb[] = 'update';
		}

		if (! $params->get('stream_read', true)) {
			$excludeVerb[] = 'read';
		}

		if ($excludeVerb !== false) {
			$exclude['article'] = $excludeVerb;
		}
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
		if ($item->context != 'article') {
			return;
		}

		// Decorate the stream item
		$item->display 	= SOCIAL_STREAM_DISPLAY_FULL;
		$item->color 	= '#FCCD1B';
		$item->fonticon	= 'fa-list';
		$item->label 	= FD::_( 'APP_USER_ARTICLE_STREAM_TOOLTIP', true );

		// Get application params
		$params 	= $this->getParams();

		if ($item->verb == 'create' && $params->get('stream_create', true)) {
			$this->prepareCreateArticleStream( $item );
		}

		if ($item->verb == 'update' && $params->get('stream_update', true)) {
			$this->prepareUpdateArticleStream( $item );
		}

		if ($item->verb == 'read' && $params->get('stream_read', true)) {
			$this->prepareReadArticleStream( $item );
		}
	}

	/**
	 * Determines if an article is viewable by the user
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return	
	 */
	private function canViewArticle($article)
	{
		// If no access filter is set, the layout takes some responsibility for display of limited information.
		$my = Foundry::user();

		// Get viewing levels
		$groups = $my->getAuthorisedViewLevels();

		if (in_array($article->access, $groups)) {
			return true;
		}

		return false;
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
		// Retrieve initial data
		$article 			= $this->getArticle($item);

		if (!$article->state) {
			return;
		}

		$category			= $this->getCategory( $item , $article );
		$permalink			= $this->getPermalink( $item , $article , $category );
		$categoryPermalink	= $this->getCategoryPermalink( $item , $category );

		// Get the actor
		$actor 		= $item->actor;

		// Get the creation date
		$date 		= FD::date( $article->created );

		if (!$this->canViewArticle($article)) {
			return;
		}

		// Get the content
		$content 	= $article->introtext;

		if( empty( $content ) )
		{
			$content 	= $article->fulltext;
		}

		$image		= $this->processContentImage($content);
		$content	= $this->processContentLength($content);

		$this->set( 'content'	, $content );
		$this->set( 'categoryPermalink' , $categoryPermalink );
		$this->set( 'date'		, $date );
		$this->set( 'permalink'	, $permalink );
		$this->set( 'article'	, $article );
		$this->set( 'category'	, $category );
		$this->set( 'actor'		, $actor );
		$this->set( 'image'		, $image );

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

		if (!$article->state) {
			return;
		}

		if (!$this->canViewArticle($article)) {
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

		$image		= $this->processContentImage($content);
		$content	= $this->processContentLength($content);


		$this->set( 'content'	, $content );
		$this->set( 'categoryPermalink' , $categoryPermalink );
		$this->set( 'date'		, $date );
		$this->set( 'permalink'	, $permalink );
		$this->set( 'article'	, $article );
		$this->set( 'category'	, $category );
		$this->set( 'actor'		, $actor );
		$this->set( 'image'		, $image );

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

		if (!$article->state) {
			return;
		}

		if (!$this->canViewArticle($article)) {
			return;
		}
		
		// Get the actor
		$actor 		= $item->actor;

		// Get the creation date
		$date 		= FD::date( $article->created );

		// Get the content
		$content 	= $article->introtext;

		if( empty( $content ) )
		{
			$content 	= $article->fulltext;
		}

		$image		= $this->processContentImage($content);
		$content	= $this->processContentLength($content);

		$this->set( 'content'	, $content );
		$this->set( 'categoryPermalink' , $categoryPermalink );
		$this->set( 'date'		, $date );
		$this->set( 'permalink'	, $permalink );
		$this->set( 'article'	, $article );
		$this->set( 'category'	, $category );
		$this->set( 'actor'		, $actor );
		$this->set( 'image'		, $image );

		// Load up the contents now.
		$item->title 	= parent::display( 'streams/read.title' );
		$item->content 	= parent::display( 'streams/read.content' );

		// Append the opengraph tags
		$item->addOgDescription($content);
	}

	private function getArticle( $item )
	{
		// Load up the article dataset
		$article 	= JTable::getInstance( 'Content' );

		if( $item->params )
		{
			$registry 	= FD::registry( $item->params );

			if( $registry->get( 'article' ) )
			{
				$article->bind( (array) $registry->get( 'article' ) );
			}
		}
		else
		{
			// Load the items manually
			$article->load( $item->contextId );
		}

		return $article;
	}

	private function processContentImage( $content )
	{
		// @rule: Match images from content
		$pattern	= '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
		preg_match( $pattern , $content , $matches );

		$image		= '';
		if( $matches )
		{
			$image		= isset( $matches[1] ) ? $matches[1] : '';

			if( JString::stristr( $matches[1], 'https://' ) === false && JString::stristr( $matches[1], 'http://' ) === false && !empty( $image ) )
			{
				$image	= rtrim(JURI::root(), '/') . '/' . ltrim( $image, '/');
			}
		}
		return $image;
	}

	private function processContentLength( $content )
	{
		// $content = strip_tags($content);

		// Limit the content length
		$params 	= $this->getParams();
		$contentLength	= $params->get( 'stream_content_length' );

		if( $contentLength )
		{
			$content 	= JString::substr( strip_tags( $content ) , 0 , $contentLength ).'... ';
		} else {
			$base = JURI::base(true).'/';
			$protocols	= '[a-zA-Z0-9]+:'; //To check for all unknown protocals (a protocol must contain at least one alpahnumeric fillowed by :
			$regex		= '#(src|href|poster)="(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
			$content = preg_replace($regex, "$1=\"$base\$2\"", $content);
		}

		return $content;
	}

	private function getCategory( $item , $article )
	{
		// Load up the category dataset
		$category	= JTable::getInstance( 'Category' );

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
		if ($item->params) {
			$registry 		= FD::registry( $item->params );
			$permalink 		= $registry->get( 'permalink' );
		} else {
			// Get the permalink
			$permalink	= ContentHelperRoute::getArticleRoute( $article->id . ':' . $article->alias , $article->catid . ':' . $category->alias );
		}

		return JRoute::_($permalink);
	}

	private function getCategoryPermalink( $item , $category )
	{
		if ($item->params) {
			$registry 	= FD::registry( $item->params );

			$categoryPermalink	= $registry->get( 'categoryPermalink' );
		} else {
			// Get the category permalink
			$categoryPermalink 	= ContentHelperRoute::getCategoryRoute( $category->id . ':' . $category->alias );
		}

		return JRoute::_($categoryPermalink);
	}

	public function onAfterLikeSave(&$like)
	{
		// article.user.create
		// article.user.update
		// article.user.read

		$allowed = array('article.user.create', 'article.user.update', 'article.user.read');

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
			'title' => 'APP_USER_ARTICLE_EMAILS_LIKE_ITEM_TITLE',
			'template' => 'apps/user/article/like.item',
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

		$emailOptions['title'] = 'APP_USER_ARTICLE_EMAILS_LIKE_INVOLVED_TITLE';
		$emailOptions['template'] = 'apps/user/article/like.involved';

		FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);
	}

	public function onBeforeCommentSave(&$comment)
	{
		// This is so that comment posted on the article can be linked back to the stream item.
		$allowed = array('article.user.create', 'article.user.update', 'article.user.read');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		$segments = $comment->element;

		list($element, $group, $verb) = explode('.', $segments);

		$streamItem = Foundry::table('streamitem');
		$state = $streamItem->load(array('context_type' => $element, 'actor_type' => $group, 'verb' => $verb, 'context_id' => $comment->uid));

		if (!$state) {
			return;
		}

		if (empty($comment->stream_id)) {
			$comment->stream_id = $streamItem->uid;
		}

		return true;
	}

	public function onAfterCommentSave(&$comment)
	{
		// article.user.create
		// article.user.update
		// article.user.read

		$allowed = array('article.user.create', 'article.user.update', 'article.user.read');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		$segments = $comment->element;

		list($element, $group, $verb) = explode('.', $segments);

		$streamItem = FD::table('streamitem');
		$state = $streamItem->load(array('context_type' => $element, 'actor_type' => $group, 'verb' => $verb, 'context_id' => $comment->uid));

		if (!$state) {
			return;
		}

		$owner = $streamItem->actor_id;

		$permalink = $comment->getPermalink();

		$emailOptions = array(
			'title' => 'APP_USER_ARTICLE_EMAILS_COMMENT_ITEM_TITLE',
			'template' => 'apps/user/apps/comment.item',
			// 'permalink' => $streamItem->getPermalink(true, true)
			'permalink' => FRoute::external($permalink)
		);

		$systemOptions = array(
			'title' => '',
			'content' => $comment->comment,
			'context_type' => $comment->element,
			// 'url' => $streamItem->getPermalink(false, false, false),
			'url' => $permalink,
			'actor_id' => $comment->created_by,
			'uid' => $comment->uid,
			'aggregate' => true
		);

		if ($comment->created_by != $owner) {
			FD::notify('comments.item', array($owner), $emailOptions, $systemOptions);
		}

		$recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb, array(), array($owner, $comment->created_by));

		$emailOptions['title'] = 'APP_USER_ARTICLE_EMAILS_COMMENT_INVOLVED_TITLE';
		$emailOptions['template'] = 'apps/user/apps/comment.involved';

		FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
	}

	public function onNotificationLoad(SocialTableNotification &$item)
	{
		// article.user.create
		// article.user.update
		// article.user.read

		$allowed = array('article.user.create', 'article.user.update', 'article.user.read');

		if (!in_array($item->context_type, $allowed)) {
			return;
		}

		$hook = $this->getHook('notification', $item->type);
		$hook->execute($item);

		return;
	}
}
