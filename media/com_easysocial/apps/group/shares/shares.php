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

FD::import('admin:/includes/themes/themes');
FD::import('admin:/includes/apps/apps');

class SocialGroupAppShares extends SocialAppItem
{
	/**
	 * Process notifications
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
        // Processes notifications when someone repost another person's item
        $allowed    = array('add.stream');

        if (!in_array($item->context_type, $allowed)) {
            return;
        }

        // We should only process items from group here.
        $share      = FD::table('Share');
        $share->load($item->context_ids);

        if ($share->element != 'stream.group') {
            return;
        }


        if ($item->type == 'repost') {

            $hook   = $this->getHook('notification', 'repost');
            $hook->execute($item);

            return;
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
		if( $item->context_type != 'shares' )
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
		$obj->color		= '#e74c3c';
		$obj->icon 		= 'fa fa-refresh';
		$obj->label 	= 'APP_GROUP_REPOST_STREAM_TITLE';

		return $obj;
	}

    /**
     * Notify the owner of the stream
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function onAfterStreamSave(SocialStreamTemplate &$streamTemplate)
    {
        // We only want to process shares
        if ($streamTemplate->context_type != SOCIAL_TYPE_SHARE || !$streamTemplate->cluster_type) {
            return;
        }

        $allowed    = array('add.stream');

        if (!in_array($streamTemplate->verb, $allowed)) {
            return;
        }

        // Because the verb is segmented with a ., we need to split this up
        $namespace  = explode('.', $streamTemplate->verb);
        $verb       = $namespace[0];
        $type       = $namespace[1];

        // Add a notification to the owner of the stream
        $stream     = FD::table('Stream');
        $stream->load($streamTemplate->target_id);

        // If the person that is reposting this is the same as the actor of the stream, skip this altogether.
        if ($streamTemplate->actor_id == $stream->actor_id) {
            return;
        }

        // Get the group
        $group      = FD::group($streamTemplate->cluster_id);

        // Get the actor
        $actor      = FD::user($streamTemplate->actor_id);

        // Get the share object
        $share      = FD::table('Share');
        $share->load($streamTemplate->context_id);

        // Prepare the email params
        $mailParams     = array();
        $mailParams['actor']        = $actor->getName();
        $mailParams['actorLink']	= $actor->getPermalink(true, true);
        $mailParams['actorAvatar']	= $actor->getAvatar(SOCIAL_AVATAR_SQUARE);
        $mailParams['group']        = $group->getName();
        $mailParams['groupLink']    = $group->getPermalink(true, true);
        $mailParams['permalink']    = FRoute::stream(array('layout' => 'item', 'id' => $share->uid, 'external' => true), true);
        $mailParams['title']        = 'APP_GROUP_SHARES_EMAILS_USER_REPOSTED_YOUR_POST_SUBJECT';
        $mailParams['template']     = 'apps/group/shares/stream.repost';

        // Prepare the system notification params
        $systemParams                   = array();
        $systemParams['context_type']   = $streamTemplate->verb;
        $systemParams['url']            = FRoute::stream(array('layout' => 'item', 'id' => $share->uid, 'sef' => false));
        $systemParams['actor_id']       = $actor->id;
        $systemParams['uid']            = $group->id;
        $systemParams['context_ids']    = $share->id;

        FD::notify('repost.item', array($stream->actor_id), $mailParams, $systemParams);
    }

	/**
	 *
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getHelper( SocialStreamItem $item , SocialTableShare $share )
	{
		$source 	= explode( '.', $share->element );
		$element 	= $source[0];

		$file 		= dirname( __FILE__ ) . '/helpers/' . $element .'.php';
		require_once( $file );

		// Get class name.
		$className 	= 'SocialGroupSharesHelper' . ucfirst( $element );

		// Instantiate the helper object.
		$helper			= new $className( $item, $share );

		return $helper;
	}

	/**
	 * Responsible to generate the stream contents.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onPrepareStream( SocialStreamItem &$item, $includePrivacy = true )
	{
		// Only process this if the stream type is shares
		if ($item->context != 'shares' || !$item->cluster_type) {
			return;
		}

		// Get the single context id
		$id 		= $item->contextId;

		// We only need the single actor.
		// Load the profiles table.
		$share	= FD::table( 'Share' );
		$share->load( $id );

		// If shared item no longer exist, exit here.
		if (!$share->id) {
			return;
		}

		// Get the current logged in user
		$my         = FD::user();

		// Break down the shared element
		$segments 	= explode('.', $share->element);
		$element 	= $segments[0];
		$group 		= $segments[1];

		// We only want to process items from albums, photos and stream
		$allowed 	= array('albums', 'photos', 'stream');

		if (!in_array($element, $allowed)) {
			return;
		}

		// Get the repost helper
		$helper 		= $this->getHelper($item , $share);

		// We want the likes and comments to be associated with the "stream" rather than the shared item
		$uid 		= $item->uid;
		$element 	= 'story';
		$verb 		= 'create';

		// Load up custom likes
		$likes 			= FD::likes();
		$likes->get($uid, $element, $verb, SOCIAL_APPS_GROUP_GROUP, $item->uid);
		$item->likes	= $likes;

		// Attach comments to the stream
		$comments 		= FD::comments($uid, $element, $verb, SOCIAL_APPS_GROUP_GROUP, array('url' => $helper->getLink()), $item->uid);
		$item->comments = $comments;

		// Share app does not allow reposting itself.
		$item->repost	= false;

		// Get the content of the repost
		$item->content 	= $helper->getContent();

		// If the content is a false, there could be privacy restrictions.
		if ($item->content === false) {
			return;
		}

		// Decorate the stream item
		$item->fonticon 	= 'fa-refresh';
		$item->color 		= '#e74c3c';
		$item->label 		= JText::_('APP_GROUP_REPOST_STREAM_TITLE');
		$item->title 		= $helper->getTitle();

		// Set stream display mode.
		$item->display	= SOCIAL_STREAM_DISPLAY_FULL;
	}

}
