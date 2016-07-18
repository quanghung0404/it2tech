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
class SocialUserAppKunena extends SocialAppItem
{
	/**
	 * Determines if Kunena is installed on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

		if (!JFile::exists($file)) {
			return false;
		}

		// Load Kunena's api file
		require_once($file);

		// Load Kunena's js file
		$doc = JFactory::getDocument();
		$doc->addScript(rtrim(JURI::root(), '/') . '/media/kunena/js/default.js');
		
		// Load Kunena's language
		KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');

		return true;
	}

	/**
	 * Processes notifications
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function onNotificationLoad(SocialTableNotification $notification)
	{
		$allowed = array('post.reply');

		if (!in_array($notification->cmd, $allowed)) {
			return;
		}

		$message = KunenaForumMessage::getInstance($notification->uid);
		$topic = $message->getTopic();
		$actor = $notification->getActor();

		$message->message = KunenaHtmlParser::parseBBCode($message->message, $topic, 80);
		$message->message = $this->formatContent($message->message);
		$message->message = strip_tags($message->message);

		$notification->title = JText::sprintf('APP_KUNENA_NOTIFICATION_NEW_REPLY', $actor->getName(), $topic->subject);
		$notification->content = $message->message;
	}

	public function createParent( $messageId = null )
	{
		$parent = new stdClass();
		$parent->forceSecure	= true;
		$parent->forceMinimal	= false;


		if ($messageId) {
			$message = KunenaForumMessage::getInstance( $messageId );
			$parent->attachments = $message->getAttachments();
		}

		return $parent;
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
		if( $item->context_type != 'kunena')
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
		$obj->color		= '#6f90b5';
		$obj->icon 		= 'fa fa-comments';
		$obj->label 	= 'APP_USER_KUNENA_STREAM_TITLE';

		return $obj;
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

		if (! $params->get('stream_reply', true)) {
			$excludeVerb[] = 'reply';
		}

		if (! $params->get('stream_thanked', true)) {
			$excludeVerb[] = 'thanked';
		}

		if ($excludeVerb !== false) {
			$exclude['kunena'] = $excludeVerb;
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

		if( $item->context != 'kunena' )
		{
			return;
		}

		// Test if Kunena exists;
		if( !$this->exists() )
		{
			return;
		}

		$verb	 	= $item->verb;

		// Decorate the stream
		$item->display 		= SOCIAL_STREAM_DISPLAY_FULL;
		$item->color 		= '#6f90b5';
		$item->fonticon		= 'fa-comments';
		$item->label 		= JText::_( 'APP_USER_KUNENA_STREAM_TITLE' );

		// Get app params
		$params		= $this->getParams();

		// New forum posts
		if( $verb == 'create' && $params->get( 'stream_create' , true ) )
		{
			$this->processNewTopic( $item , $includePrivacy );
		}

		if( $verb == 'reply' && $params->get( 'stream_reply' , true ) )
		{
			$this->processReply( $item , $includePrivacy );
		}

		if( $verb == 'thanked' && $params->get( 'stream_thanked' , true ) )
		{
			$this->processThanked( $item , $includePrivacy );
		}

		$element		= $item->context;
		$uid     		= $item->contextId;

		if( $includePrivacy )
		{
			$my 		= FD::user();
			$privacy 	= FD::privacy( $my->id );
			$item->privacy 	= $privacy->form( $uid, $element, $item->actor->id, 'core.view', false, $item->uid );
		}
	}

	/**
	 * Processes the stream item for new topics
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem	The stream item object
	 * @param	bool				Determine if we should include the privacy or not.
	 * @return
	 */
	private function processNewTopic( &$item , $includePrivacy = true )
	{
		$topic 		= KunenaForumTopicHelper::get( $item->contextId );
		$category 	= $topic->getCategory();

		if (!$category->authorise('read') || !$topic->authorise('read')) {
			// user not allow to view the content.
			return;
		}

		if ($topic->hold == 2 || $topic->hold == 3) {
			return;
		}

		// Apply likes on the stream
		$likes 				= FD::likes()->get( $item->contextId , 'kunena', 'create', SOCIAL_APPS_GROUP_USER, $item->uid );
		$item->likes		= $likes;

		// disable comments on the stream
		$item->comments 	= false;

		// Set the actor
		$actor 			= $item->actor;

		JFactory::getLanguage()->load( 'com_kunena' , JPATH_ROOT );

		$parent = $this->createParent($topic->first_post_id);

		$params = $this->getParams();
		$contentLength = $params->get('stream_content_length' , 0);

		$topic->message = KunenaHtmlParser::parseBBCode($topic->first_post_message, $parent, $contentLength);
		$topic->message = $this->formatContent($topic->message);

		$this->set( 'actor'	, $actor );
		$this->set( 'topic' , $topic );

		$item->title	= parent::display( 'streams/' . $item->verb . '.title' );
		$item->content	= parent::display( 'streams/' . $item->verb . '.content' );

		// Append the opengraph tags
		$item->addOgDescription($topic->message);
	}

	/**
	 * Processes the stream item for new topics
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem	The stream item object
	 * @param	bool				Determine if we should include the privacy or not.
	 * @return
	 */
	private function processReply( &$item , $includePrivacy = true )
	{
		$message 	= KunenaForumMessageHelper::get($item->contextId);

		// If the reply was unpublished do not display the item on the stream
		if ($message->hold == 2 || $message->hold == 3) {
			return;
		}

		$topic 		= $message->getTopic();

		if (! $topic->authorise('read')){
			// user not allow to view.
			return;
		}

		// If the topic was unpublished do not display the replies
		if ($topic->hold == 2 || $topic->hold == 3) {
			return;
		}

		// Apply likes on the stream
		$likes 			= FD::likes()->get( $item->contextId , 'kunena', 'reply', SOCIAL_APPS_GROUP_USER, $item->uid );
		$item->likes	= $likes;

		// disable comments on the stream
		$item->comments 	= false;

		// Set the actor
		$actor 			= $item->actor;
		$parent 		= $this->createParent( $message->id );

		$params 		= $this->getParams();
		$contentLength	= $params->get('stream_content_length' , 0);

		$message->message	= KunenaHtmlParser::parseBBCode( $message->message , $parent , $contentLength );
		$message->message = $this->formatContent($message->message);


		$this->set( 'actor'	, $actor );
		$this->set( 'topic' , $topic );
		$this->set( 'message' , $message );

		$item->title	= parent::display( 'streams/' . $item->verb . '.title' );
		$item->content	= parent::display( 'streams/' . $item->verb . '.content' );

		// Append the opengraph tags
		$item->addOgDescription($message->message);
	}

	/**
	 * Processes the stream item for new thanks
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem	The stream item object
	 * @param	bool				Determine if we should include the privacy or not.
	 * @return
	 */
	private function processThanked( &$item , $includePrivacy = true )
	{
		$message 	= KunenaForumMessageHelper::get( $item->contextId );
		$topic 		= $message->getTopic();
		$category 	= $topic->getCategory();

		if (!$category->authorise('read') || !$topic->authorise('read')) {
			// user not allow to view the content.
			return;
		}

		// Apply likes on the stream
		$likes 			= FD::likes()->get( $item->contextId , 'kunena', 'thank', SOCIAL_APPS_GROUP_USER, $item->uid );
		$item->likes	= $likes;

		// disable comments on the stream
		$item->comments 	= false;

		// Define standard stream looks
		$item->display 	= SOCIAL_STREAM_DISPLAY_MINI;
		$item->color 	= '#6f90b5';

		// Set the actor
		$actor 			= $item->actor;
		$target 		= $item->targets[0];

		$parent 		= $this->createParent( $message->id );
		$message->message	= KunenaHtmlParser::parseBBCode( $message->message , $parent , 250 );
		$message->message	= $this->filterContent( $message->message );

		$this->set( 'actor'	, $actor );
		$this->set( 'target', $target );
		$this->set( 'topic' , $topic );
		$this->set( 'message' , $message );

		$item->title	= parent::display( 'streams/' . $item->verb . '.title' );

		// Append the opengraph tags
		$item->addOgDescription($item->title);
	}

	private function filterContent( $content )
	{
		/*
		 * temporary fix to prevent email cloaking causing ajax to failed.
		 *
		 */
		$content = strip_tags( $content );

		return $content;
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
		if( $item->context != 'kunena' )
		{
			return;
		}

		// Test if Kunena exists;
		if( !$this->exists() )
		{
			return;
		}

		// Get the context id.
		$actor 	= $item->actor;
		$topic 	= KunenaForumTopicHelper::get( $item->contextId );


		if ($item->verb == 'thanked') {
			$message 	= KunenaForumMessageHelper::get( $item->contextId );
			$topic 		= $message->getTopic();

			$target = $item->targets[0];
			$this->set( 'target'	, $target );
			$this->set( 'message' , $message );
		}
		else if( $item->verb == 'reply' ) {
			$message 	= KunenaForumMessageHelper::get( $item->contextId );
			$topic 		= $message->getTopic();

			$this->set( 'message' , $message );

		}

		$this->set( 'topic'		, $topic );
		$this->set( 'actor'		, $actor );


		// Load up the contents now.
		$item->title 	= parent::display( 'streams/' . $item->verb . '.title' );
		$item->content 	= '';

	}

	/**
	 * Format's kunena contents
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function formatContent($content)
	{
		$base = JURI::base(true).'/';

		// To check for all unknown protocals (a protocol must contain at least one alpahnumeric fillowed by :
		$protocols = '[a-zA-Z0-9]+:';

		// Pattern to match links
		$regex = '#(src|href|poster)="(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
		
		$content = preg_replace($regex, "$1=\"$base\$2\"", $content);
		
		return $content;
	}


}
