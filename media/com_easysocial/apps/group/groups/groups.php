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
 * Groups application for EasySocial
 * @since	1.2
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialGroupAppGroups extends SocialAppItem
{
	/**
	 * Class constructor
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function onBeforeStorySave($template, $stream)
	{
		if (!$template->cluster_id || !$template->cluster_type) {
			return;
		}

		if ($template->cluster_type != SOCIAL_TYPE_GROUP) {
			return;
		}

		$group = FD::group($template->cluster_id);
		$params = $group->getParams();
		$moderate = (bool) $params->get('stream_moderation', false);

		// If not configured to moderate, skip this altogether
		if (!$moderate) {
			return;
		}

		// If the current user is a site admin or group admin or group owner, we shouldn't moderate anything
		if ($group->isAdmin() || $group->isOwner() || $this->my->isSiteAdmin()) {
			return;
		}

		// When the script reaches here, we're assuming that the group wants to moderate stream items.		
		$template->setState(SOCIAL_STREAM_STATE_MODERATE);
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
		$obj->color		= '#303229';
		$obj->icon 		= 'fa fa-users';
		$obj->label 	= 'APP_GROUP_GROUPS_STREAM_TOOLTIP';

		return $obj;
	}

	/**
	 * Processes notification for users notification within the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		$allowed 	= array('groups.user.rejected', 'groups.promoted', 'groups.user.removed');

		if (!in_array($item->cmd, $allowed)) {
			return;
		}

		if ($item->cmd == 'groups.promoted') {
			$hook 	= $this->getHook('notification', 'group');

			return $hook->execute($item);
		}

		if ($item->cmd == 'groups.user.rejected') {
			$hook 	= $this->getHook('notification', 'group');

			return $hook->execute($item);
		}

		if ($item->cmd == 'groups.user.removed') {
			$hook 	= $this->getHook('notification', 'group');

			return $hook->execute($item);
		}
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
		if( $item->context_type != 'groups' )
		{
			return false;
		}

		// if this is a cluster stream, let check if user can view this stream or not.
		$params 	= FD::registry( $item->params );
		$group 		= FD::group( $params->get( 'group' ) );

		if( !$group )
		{
			return;
		}

		$item->cnt = 1;

		if (!$group->isPublic() && !$group->isMember()) {
			$item->cnt = 0;
		}

		return true;
	}

	/**
	 * Trigger for onPrepareStream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareStream( SocialStreamItem &$item, $includePrivacy = true )
	{
		// We do not want stream items to contain the repost link
		if ($item->cluster_id && $item->cluster_type) {

			$group 	= FD::group($item->cluster_id);

			if (!$group->isMember()) {
				$item->repost		= false;
				$item->commentLink	= false;
				$item->commentForm	= false;
			}

			// Only show Social sharing in public group
			if (!$group->isOpen()) {
				$item->sharing = false;
			}			
		}

		// We only want to process related items
		if ($item->context != 'groups') {
			return;
		}

		// Check for group access
		$params = FD::registry($item->params);
		$obj = $params->get('group');
		$group = FD::group($obj);

		// Check if the viewer can view items from this group
		if (!$group->canViewItem()) {
			return;
		}

		$item->display = SOCIAL_STREAM_DISPLAY_MINI;

		$item->color = '#303229';
		$item->fonticon = 'fa fa-users';
		$item->label = FD::_('APP_GROUP_GROUPS_STREAM_TOOLTIP', true);

		$appParams = $this->getParams();

		if ($item->verb == 'join' && $appParams->get('stream_join', true)) {
			$this->prepareJoinStream($item);
		}

		if ($item->verb == 'leave' && $appParams->get('stream_leave', true)) {
			$this->prepareLeaveStream($item);
		}

		if ($item->verb == 'makeadmin' && $appParams->get('stream_promoted', true)) {
			$this->prepareMakeAdminStream($item);
		}

		if ($item->verb == 'update' && $appParams->get('stream_update', true)) {
			$this->prepareUpdateStream($item);
		}

		if ($item->verb == 'create' && $appParams->get('stream_create', true)) {
			$this->prepareCreateStream($item);
		}
	}

	private function prepareLeaveStream(SocialStreamItem &$item)
	{
		$params 	= FD::registry( $item->params );
		$obj 		= $params->get( 'group' );

		$group 			= new SocialGroup();
		$group->bind( $obj );


		if (!$group) {
			return;
		}

		$actor	 = $item->actor;

		$this->set( 'group'	, $group );
		$this->set( 'actor'	, $actor );

		$item->title 	= parent::display( 'streams/leave.title' );

		// Append the opengraph tags
		$item->addOgDescription(JText::sprintf('APP_GROUP_GROUPS_STREAM_LEAVED_GROUP', $actor->getName(), $group->getName()));
	}

	private function prepareJoinStream(SocialStreamItem &$item)
	{
		$groupId = $item->contextId;
		$params = FD::registry($item->params);
		$obj = $params->get('group');

		$group = new SocialGroup();

		if ($obj) {
			$group->bind($obj);
		} else {
			$group = FD::group($item->cluster_id);

		}

		if (!$group) {
			return;
		}


		$actor	 = $item->actor;

		$this->set( 'group'	, $group );
		$this->set( 'actor'	, $actor );

		$item->title 	= parent::display( 'streams/join.title' );
		$item->content	= parent::display( 'streams/join.content' );

		// Append the opengraph tags
		$item->addOgDescription(JText::sprintf('APP_GROUP_GROUPS_STREAM_HAS_JOIN_GROUP', $actor->getName()));
	}

	private function prepareMakeAdminStream( SocialStreamItem &$item )
	{
		$groupId 	= $item->contextId;

		$params 	= FD::registry( $item->params );
		$group 		= FD::group( $params->get( 'group' ) );

		if( !$group )
		{
			return;
		}


		$actor	 = $item->actor;

		$this->set( 'group'	, $group );
		$this->set( 'actor'	, $actor );

		$item->title 	= parent::display( 'streams/makeadmin.title' );

		// Append the opengraph tags
		$item->addOgDescription(JText::sprintf('APP_GROUP_GROUPS_STREAM_PROMOTED_TO_BE_ADMIN', $actor->getName()));
	}

	private function prepareUpdateStream( SocialStreamItem &$item )
	{
		$groupId 	= $item->contextId;

		$params 	= FD::registry( $item->params );
		$group 		= FD::group( $params->get( 'group' ) );

		if( !$group )
		{
			return;
		}

		$actor	 = $item->actor;

		$this->set( 'group'	, $group );
		$this->set( 'actor'	, $actor );

		$item->title 	= parent::display( 'streams/update.title' );

		// Append the opengraph tags
		$item->addOgDescription(JText::sprintf('APP_GROUP_GROUPS_STREAM_UPDATED_GROUP', $actor->getName()));
	}

	private function prepareCreateStream( SocialStreamItem &$item )
	{
		$groupId 	= $item->contextId;
		$params 	= FD::registry( $item->params );
		$obj 		= $params->get( 'group' );

		$group 			= new SocialGroup();
		$group->bind( $obj );

		if( !$group )
		{
			return;
		}

		// We don't want to display groups that are invitation only.
		if( $group->type == SOCIAL_GROUPS_INVITE_TYPE )
		{
			return;
		}

		$actor	 = $item->actor;

		$this->set( 'group'	, $group );
		$this->set( 'actor'	, $actor );

		$item->title 	= parent::display( 'streams/create.title' );
		$item->content	= parent::display( 'streams/create.content' );

		// Append the opengraph tags
		$item->addOgDescription(JText::sprintf('APP_GROUP_GROUPS_STREAM_CREATED_GROUP', $actor->getName(), $group->getName()));
	}

}
