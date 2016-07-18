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
 * Feeds application for EasySocial
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialUserAppCalendar extends SocialAppItem
{
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
		$obj->color		= '#FF8265';
		$obj->icon 		= 'fa fa-calendar';
		$obj->label 	= 'APP_USER_CALENDAR_STREAM_TOOLTIP';

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
		if( $item->context_type != 'calendar' )
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
		if ($item->context != 'calendar') {
			return;
		}

		// Get the context id.
		$calendar	= $this->getTable( 'Calendar' );
		$calendar->load($item->contextId);

		$permalink 	= $calendar->getPermalink();

		$this->set('permalink', $permalink);
		$this->set('calendar', $calendar);

		$item->title 	= parent::display('logs/' . $item->verb . '.title');
		$item->content	= '';
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

		if ($excludeVerb !== false) {
			$exclude['calendar'] = $excludeVerb;
		}
	}

	/**
	 * Triggers when an event is created
	 *
	 * @since	1.3
	 * @access	public
	 * @param	SocialEvent	The event object
	 * @param	SocialUser	The user object
	 * @param	bool		Determines if the event is a new event
	 * @return
	 */
	public function onEventAfterSave(SocialEvent &$event, SocialUser &$author, $isNew)
	{
		// When a new event is created, we want to ensure that it's stored in the user's calendar
		if ($isNew) {

			$eventstart = $event->getEventStart();
			$eventend = $event->getEventEnd();

			// Ensure that the start and end date is set
			if (!$eventstart && !$eventend) {
				return;
			}

			$calendar = FD::table('Calendar');

			// Get the start and end date
			$calendar->title = $event->getName();
			$calendar->description = $event->description;
			$calendar->uid = $event->id;
			$calendar->type = SOCIAL_TYPE_EVENT;
			$calendar->date_start = $eventstart->toSql();
			$calendar->date_end = $eventend->toSql();
			$calendar->user_id = $author->id;

			$calendar->store();
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
	public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
	{
		if ($item->context !== 'calendar') {
			return;
		}

		// Determine if we should display create stream
		$params = $this->getParams();

		if ($item->verb == 'create' && !$params->get('stream_create', true)) {
			return;
		}

		if ($item->verb == 'update' && !$params->get('stream_update', true)) {
			return;
		}

		$calendar = FD::table('Calendar');
		$calendar->load($item->contextId);

		if (!$calendar->id) {
			return;
		}

		// Respect user's calendar privacy
		$privacy = ES::privacy($this->my->id);

		// We need to check for the calendar privacy
		if ($includePrivacy && !$privacy->validate('apps.calendar', $calendar->id, 'apps', $item->actor->id)) {
			return;
		}

		// Format the likes for the stream
		$likes = FD::likes();
		$likes->get( $item->contextId , 'calendar', $item->verb, SOCIAL_APPS_GROUP_USER, $item->uid );
		$item->likes = $likes;

		// Apply comments on the stream
		$comments = FD::comments( $item->contextId , 'calendar' , $item->verb, SOCIAL_APPS_GROUP_USER, array('url' => $calendar->getPermalink() ), $item->uid );
		$item->comments = $comments;

		// Set a color for the calendar
		$item->color = '#FF8265';
		$item->fonticon	= 'fa-calendar';
		$item->label = FD::_('APP_USER_CALENDAR_STREAM_TOOLTIP', true );

		$app = $this->getApp();

		// Get the term to be displayed
		$genderValue = $item->actor->getFieldData('GENDER');
		$gender = 'THEIR';

		if ($genderValue == 1) {
			$gender = 'MALE';
		}

		if ($genderValue == 2) {
			$gender = 'FEMALE';
		}

		$this->set('gender', $gender);
		$this->set('app', $app);
		$this->set('calendar', $calendar);
		$this->set('actor', $item->actor);
		$this->set('params', $params);

		$item->display = SOCIAL_STREAM_DISPLAY_FULL;
		$item->title = parent::display('streams/' . $item->verb . '.title');
		$item->content = parent::display('streams/' . $item->verb . '.content');
	}

	/**
	 * Sends notification on new comments
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  SocialTableComments    $comment Comment table object
	 */
	public function onAfterCommentSave($comment)
	{
		// calendar.user.create
		// calendar.user.update

		$allowed = array('calendar.user.create', 'calendar.user.update');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		$segments = explode('.', $comment->element);
		$verb = $segments[2];

		// Stream is passing context id to comment, hence comment uid is the calendar item id directly.

		$calendar = $this->getTable('calendar');
		$calendar->load($comment->uid);

		$emailOptions = array(
			'title'		=> 'APP_USER_CALENDAR_EMAILS_COMMENT_ITEM_TITLE',
			'template'	=> 'apps/user/calendar/comment.item',
			'comment'	=> $comment->comment,
			'permalink'	=> $calendar->getPermalink(true, true)
		);

		$systemOptions 	= array(
			'title' 		=> '',
			'content'		=> $comment->comment,
			'context_type' 	=> $comment->element,
			'url' 			=> $calendar->getPermalink(false, false, false),
			'actor_id' 		=> $comment->created_by,
			'uid'			=> $comment->uid,
			'aggregate'		=> true
		);

		if ($calendar->user_id != $comment->created_by) {
			FD::notify('comments.item', array($calendar->user_id), $emailOptions, $systemOptions);
		}

		$recipients = $this->getStreamNotificationTargets($comment->uid, 'calendar', 'user', $verb, array(), array($calendar->user_id, $comment->created_by));

		$emailOptions['title'] = 'APP_USER_CALENDAR_EMAILS_COMMENT_INVOLVED_TITLE';
		$emailOptions['template'] = 'apps/user/calendar/comment.involved';

		FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
	}

	/**
	 * Sends notification on new likes
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  object    $likes Likes object
	 */
	public function onAfterLikeSave($likes)
	{
		// calendar.user.create
		// calendar.user.update

		$allowed = array('calendar.user.create', 'calendar.user.update');

		if (!in_array($likes->type, $allowed)) {
			return;
		}

		$segments = explode('.', $likes->type);
		$verb = $segments[2];

		// Stream is passing context id to likes, hence likes uid is the calendar item id directly.

		$calendar = $this->getTable('calendar');
		$calendar->load($likes->uid);

		$emailOptions = array(
			'title'		=> 'APP_USER_CALENDAR_EMAILS_LIKE_ITEM_TITLE',
			'template'	=> 'apps/user/calendar/like.item',
			'permalink'	=> $calendar->getPermalink(true)
		);

		$systemOptions 	= array(
			'title' 		=> '',
			'context_type' 	=> $likes->type,
			'url' 			=> $calendar->getPermalink(false, false, false),
			'actor_id' 		=> $likes->created_by,
			'uid'			=> $likes->uid,
			'aggregate'		=> true
		);

		if ($calendar->user_id != $likes->created_by) {
			FD::notify('likes.item', array($calendar->user_id), $emailOptions, $systemOptions);
		}

		$recipients = $this->getStreamNotificationTargets($likes->uid, 'calendar', 'user', $verb, array(), array($calendar->user_id, $likes->created_by));

		$emailOptions['title'] = 'APP_USER_CALENDAR_EMAILS_LIKE_INVOLVED_TITLE';
		$emailOptions['template'] = 'apps/user/calendar/like.involved';

		FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);
	}

	/**
	 * Processes notifications
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialTableNotification
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		$allowed = array('calendar.user.create', 'calendar.user.update');

		if (!in_array($item->context_type, $allowed)) {
			return;
		}

		$calendar = $this->getTable('calendar');
		$state = $calendar->load($item->uid);

		if (!$state) {
			return;
		}

		$hook = $this->getHook('notification', $item->type);
		$hook->execute($item, $calendar);
	}
}
