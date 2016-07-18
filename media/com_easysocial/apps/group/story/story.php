<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

class SocialGroupAppStory extends SocialAppItem
{
    /**
     * Responsible to return the favicon object
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function getFavIcon()
    {
        $obj            = new stdClass();
        $obj->color     = '#6E9545';
        $obj->icon      = 'fa fa-pencil';
        $obj->label     = 'APP_USER_STORY_UPDATES_STREAM_TOOLTIP';

        return $obj;
    }

    /**
     * event onLiked on story
     *
     * @since   1.0
     * @access  public
     * @param   object  $params     A standard object with key / value binding.
     *
     * @return  none
     */
    public function onAfterLikeSave(&$likes)
    {
        if (!$likes->type) {
            return;
        }

        // Set the default element.
        $uid = $likes->uid;
        $data = explode( '.', $likes->type);
        $element = $data[0];
        $group = $data[1];
        $verb = $data[2];

        if ($element != 'story') {
            return;
        }

        // Get the owner of the post.
        $stream = FD::table('Stream');
        $stream->load($uid);

        // Get the actor
        $actor = FD::user($likes->created_by);

        $emailOptions   = array(
            'title'     => 'APP_GROUP_STORY_EMAILS_LIKE_ITEM_SUBJECT',
            'template'  => 'apps/group/story/like.item',
            'permalink' => $stream->getPermalink(true, true),
            'actor'     => $actor->getName(),
            'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
            'actorLink'     => $actor->getPermalink(true, true)
        );

        $systemOptions  = array(
            'context_type'  => $likes->type,
            'url'           => $stream->getPermalink(false, false, false),
            'actor_id'      => $likes->created_by,
            'uid'           => $likes->uid,
            'aggregate'     => true
        );

        // Notify the owner first
        if ($actor->id != $stream->actor_id) {
            FD::notify('likes.item', array($stream->actor_id), $emailOptions, $systemOptions);
        }

        // Get a list of recipients to be notified for this stream item
        // We exclude the owner of the note and the actor of the like here
        $recipients = $this->getStreamNotificationTargets($likes->uid, $element, $group, $verb, array(), array($stream->actor_id, $likes->created_by));

        if (!$recipients) {
            return;
        }

        $emailOptions['title']      = 'APP_USER_NOTES_EMAILS_LIKE_INVOLVED_TITLE';
        $emailOptions['template']   = 'apps/group/story/like.involved';

        // Notify other participating users
        FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);
    }

    /**
	 * Triggered before comments notify subscribers
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableComments	The comment object
	 * @return
	 */
	public function onAfterCommentSave( &$comment )
	{
		$allowed 	= array('story.group.create', 'links.group.create');

		if(!in_array( $comment->element, $allowed )) {
			return;
		}

		$segments 	= explode( '.' , $comment->element );
		$element 	= $segments[0];
        $group      = $segments[1];
        $verb       = $segments[2];

		// Load up the stream object
		$stream 		= FD::table( 'Stream' );
		$stream->load($comment->uid);

        // Get the comment actor
        $actor          = FD::user($comment->created_by);

        // If the actor is the owner of the story item, skip this
        if ($actor->id == $stream->actor_id) {
            return;
        }

        $emailOptions   = array(
            'title'     => 'APP_GROUP_STORY_EMAILS_COMMENT_ITEM_TITLE',
            'template'  => 'apps/group/story/comment.item',
            'comment'   => $comment->comment,
            'permalink' => $stream->getPermalink(true, true),
            'posterName'    => $actor->getName(),
            'posterAvatar'  => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
            'posterLink'    => $actor->getPermalink(true, true)
        );

        $systemOptions  = array(
            'content'       => $comment->comment,
            'context_type'  => $comment->element,
            'url'           => $stream->getPermalink(false, false, false),
            'actor_id'      => $comment->created_by,
            'uid'           => $comment->uid,
            'aggregate'     => true
        );

        // Notify the story owner
        FD::notify('comments.item', array($stream->actor_id), $emailOptions, $systemOptions);

        // Get a list of recipients to be notified for this stream item.
        // We exclude the owner of the note and the actor of the like here
        $recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb, array(), array($stream->actor_id, $comment->created_by));

        // If there's no recipients, skip this altogether
        if (!$recipients) {
            return;
        }

        $emailOptions['title']      = 'APP_GROUP_STORY_EMAILS_COMMENT_ITEM_INVOLVED_TITLE';
        $emailOptions['template']   = 'apps/group/story/comment.involved';

        // Notify participating users
        FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
	}

    /**
     * Processes notifications
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function onNotificationLoad(SocialTableNotification &$item)
    {
        // Process notifications when someone likes your post
        // context_type: stream.group.create, links.create
        // type: likes
        $allowed = array('story.group.create', 'links.create', 'photos.group.share');
        if ($item->type == 'likes' && in_array($item->context_type, $allowed)) {
            $hook = $this->getHook('notification', 'likes');
            $hook->execute($item);

            return;
        }

        // Process notifications when someone posts a comment on your status update
        // context_type: stream.group.create
        // type: comments
        $allowed    = array('story.group.create', 'links.group.create', 'photos.group.share');
        if ($item->type == 'comments' && in_array($item->context_type, $allowed)) {

            $hook   = $this->getHook('notification', 'comments');
            $hook->execute($item);

            return;
        }

        // Processes notifications when someone posts a new update in a group
        // context_type: story.group.create, links.group.create
        // type: groups
        $allowed    = array('story.group.create', 'links.group.create', 'photos.group.share', 'file.group.uploaded');

        if ($item->cmd == 'groups.updates' && (in_array($item->context_type, $allowed))) {

            $hook   = $this->getHook('notification', 'updates');
            $hook->execute($item);

            return;
        }
    }

	/**
     * Process notifications for urls
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function processLinksNotifications( &$item )
    {
        // Get the stream id.
        $streamId   = $item->uid;

        // We don't want to process notification for likes here.
        if( $item->type == 'likes' )
        {
            return;
        }

        // Get the links that are posted for this stream
        $model      = FD::model( 'Stream' );
        $links      = $model->getAssets( $streamId , SOCIAL_TYPE_LINKS );

        if( !isset( $links[ 0 ] ) )
        {
            return;
        }

        // Initialize default values
        $link   = $links[ 0 ];
        $actor  = FD::user( $item->actor_id );
        $meta   = FD::registry( $link->data );

        if( $item->cmd == 'story.tagged' )
        {
            $item->title    = JText::_( 'APP_GROUP_STORY_POSTED_LINK_TAGGED' );
        }
        else
        {
            $item->title    = JText::sprintf( 'APP_GROUP_STORY_POSTED_LINK_ON_YOUR_TIMELINE' , $meta->get( 'link' ) );
        }
    }

    public function processPhotosNotifications( &$item )
    {
        if( $item->context_ids )
        {
            // If this is multiple photos, we just show the last one.
            $ids    = FD::json()->decode( $item->context_ids );
            $id     = $ids[ count( $ids ) - 1 ];

            $photo          = FD::table( 'Photo' );
            $photo->load( $id );

            $item->image    = $photo->getSource();

            $actor          = FD::user( $item->actor_id );

            $title          = JText::sprintf( 'APP_GROUP_STORY_POSTED_PHOTO_ON_YOUR_TIMELINE' , $actor->getName() );
            if( count( $ids ) > 1 )
            {
                $title          = JText::sprintf( 'APP_GROUP_STORY_POSTED_PHOTO_ON_YOUR_TIMELINE_PLURAL' , $actor->getName(), count( $ids ) );
            }

            $item->title    = $title;

        }

    }

    /**
     * Triggered to validate the stream item whether should put the item as valid count or not.
     *
     * @since   1.2
     * @access  public
     * @param   jos_social_stream, boolean
     * @return  0 or 1
     */
    public function onStreamCountValidation( &$item, $includePrivacy = true )
    {
        // If this is not it's context, we don't want to do anything here.
        if( $item->context_type != 'story' )
        {
            return false;
        }

        // if this is a cluster stream, let check if user can view this stream or not.
        $params     = FD::registry( $item->params );
        $group      = FD::group( $params->get( 'group' ) );

        if( !$group )
        {
            return;
        }

        $item->cnt = 1;

        if( $group->type != SOCIAL_GROUPS_PUBLIC_TYPE )
        {
            if( !$group->isMember( FD::user()->id ) )
            {
                $item->cnt = 0;
            }
        }

        return true;
    }


    /**
     * We need to notify group members when someone posts a new story in the group
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function onAfterStorySave(SocialStream &$stream , SocialTableStreamItem &$streamItem, SocialStreamTemplate &$template )
    {
        // Determine if this is for a group
        if (!$template->cluster_id) {
            return;
        }

        // Now we only want to allow specific context
        $context    = $template->context_type . '.' . $template->verb;
        $allowed    = array('story.create', 'links.create', 'photos.share');

        if (!in_array($context, $allowed)) {
            return;
        }

        // When a user posts a new story in a group, we need to notify the group members
        $group  = FD::group($template->cluster_id);

        // Get the actor
        $actor  = FD::user($streamItem->actor_id);

        // Get a list of group members
        $model      = FD::model('Groups');
        $targets    = $model->getMembers($group->id, array('exclude' => $actor->id, 'state' => SOCIAL_STATE_PUBLISHED));

        // If there's nothing to send skip this altogether.
        if (!$targets) {
            return;
        }

        // Get the item's permalink
        $permalink      = FRoute::stream(array('id' => $streamItem->uid, 'layout' => 'item', 'external' => true), true);

        // Prepare the email params
        $mailParams     = array();
        $mailParams['actor']        = $actor->getName();
        $mailParams['posterAvatar'] = $actor->getAvatar(SOCIAL_AVATAR_SQUARE);
        $mailParams['posterLink']   = $actor->getPermalink(true, true);

        $contents = $template->content;

        // break the text and images
        if (strpos($template->content, '<img') !== false) {
            preg_match('#(<img.*?>)#', $template->content, $results);

            $img = "";
            if ($results) {
                $img = $results[0];
            }

            $segments = explode('<img', $template->content);
            $contents = $segments[0];

            if ($img) {
                $contents = $contents . '<br /><div style="text-align:center;">' . $img . "</div>";
            }
        }

        $mailParams['message']      = $contents;
        $mailParams['group']        = $group->getName();
        $mailParams['groupLink']    = $group->getPermalink(true, true);
        $mailParams['permalink']    = FRoute::stream(array('id' => $streamItem->uid, 'layout' => 'item', 'external' => true), true);
        $mailParams['title']        = 'APP_GROUP_STORY_EMAILS_NEW_POST_IN_GROUP';
        $mailParams['template']     = 'apps/group/story/new.post';

        // Prepare the system notification params
        $systemParams                   = array();
        $systemParams['context_type']   = $template->context_type . '.group.' . $template->verb;
        $systemParams['url']            = FRoute::stream(array('id' => $streamItem->uid, 'layout' => 'item', 'sef' => false));
        $systemParams['actor_id']       = $actor->id;
        $systemParams['uid']            = $streamItem->uid;
        $systemParams['context_ids']    = $group->id;
        $systemParams['content']        = $template->content;

        // Try to send the notification
        $state  = FD::notify('groups.updates', $targets, $mailParams, $systemParams);
    }

    /**
     * Triggered to prepare the stream item
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function onPrepareStream(SocialStreamItem &$item)
    {
        // If this is not it's context, we don't want to do anything here.
        if ($item->context != 'story') {
            return;
        }

        // Get the event object
        $group = $item->getCluster();

        if (!$group) {
            return;
        }

        if (!$group->canViewItem()) {
            return;
        }

        // Allow editing of the stream item
        $item->editable = $this->my->isSiteAdmin() || $group->isAdmin() || $item->actor->id == $this->my->id;

        // Get the actor
        $actor = $item->getActor();

        // Decorate the stream
        $item->fonticon = 'fa fa-pencil';
        $item->color = '#6E9545';
        $item->label = FD::_( 'APP_GROUP_STORY_STREAM_TOOLTIP', true);
        $item->display = SOCIAL_STREAM_DISPLAY_FULL;

        $this->set('group', $group);
        $this->set('actor' , $actor);
        $this->set('stream', $item);

        $item->title        = parent::display( 'streams/title.' . $item->verb );
        $item->content  =  parent::display( 'streams/content.' . $item->verb );

        // Apply likes on the stream
        $likes              = FD::likes();
        $likes->get( $item->uid , $item->context, $item->verb, SOCIAL_APPS_GROUP_GROUP, $item->uid );

        $item->likes      = $likes;

        // If this update is posted in a group, the comments should be linked to the group item
        $comments           = FD::comments($item->uid , $item->context, $item->verb, SOCIAL_APPS_GROUP_GROUP , array( 'url' => FRoute::stream( array( 'layout' => 'item', 'id' => $item->uid ) ) ), $item->uid );
        $item->comments     = $comments;


        return true;
    }

}
