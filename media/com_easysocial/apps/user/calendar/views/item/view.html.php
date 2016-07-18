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

/**
 * Dashboard view for the calendar app.
 *
 * @since	1.0
 * @access	public
 */
class CalendarViewItem extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 */
	public function display( $userId = null, $docType = null )
	{
		// Require user to be logged in
		FD::requireLogin();

		$id 		= JRequest::getVar( 'schedule_id' );

		// Get the user that's being accessed.
		$user 		= FD::user( $userId );

		$calendar = FD::table('Calendar');
		$calendar->load($id);

		if (!$calendar->id || !$id) {
			FD::info()->set( false , JText::_( 'APP_CALENDAR_CANVAS_INVALID_SCHEDULE_ID' ) , SOCIAL_MSG_ERROR );

			return $this->redirect( FD::profile( array( 'id' => $user->getAlias() ) , false ) );
		}

		$my = FD::user();
		$privacy = FD::privacy($my->id);

		$result = $privacy->validate('apps.calendar', $calendar->id, 'view', $user->id);

		if (!$result) {
			FD::info()->set(false, JText::_('APP_CALENDAR_NO_ACCESS'), SOCIAL_MSG_ERROR);
			JFactory::getApplication()->redirect(FRoute::dashboard());
		}

		FD::page()->title( $calendar->title );

		// Render the comments and likes
		$likes 			= FD::likes();
		$likes->get( $id , 'calendar', 'create', SOCIAL_APPS_GROUP_USER );

		// Apply comments on the stream
		$comments			= FD::comments( $id , 'calendar' , 'create', SOCIAL_APPS_GROUP_USER , array( 'url' => FRoute::albums( array( 'layout' => 'item', 'id' => $id ) ) ) );

		$params = $this->app->getParams();

		$this->set('params', $params);
		$this->set( 'likes'		, $likes );
		$this->set( 'comments'	, $comments );
		$this->set( 'calendar'	, $calendar );
		$this->set( 'user'		, $user );

		echo parent::display( 'canvas/item/default' );
	}
}
