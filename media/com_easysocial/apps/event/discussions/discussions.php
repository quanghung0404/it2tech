<?php
/**
* @package        %PACKAGE%
* @subpackge    %SUBPACKAGE%
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
*
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialEventAppDiscussions extends SocialAppItem
{
    /**
     * Responsible to return the favicon object.
     *
     * @since   1.3
     * @access  public
     */
    public function getFavIcon()
    {
        $obj = new stdClass();

        $obj->color = '#69b598';
        $obj->icon = 'fa-comments';
        $obj->label = 'APP_EVENT_DISCUSSIONS_STREAM_TOOLTIP';

        return $obj;
    }

    /**
     * Performs clean up when a event is deleted.
     *
     * @since   1.3
     * @access  public
     * @param   SocialEvent  $event  The event object
     */
    public function onBeforeDelete(&$event)
    {
        // Delete all discussions from a event
        $model = FD::model('Discussions');
        $model->delete($event->id, SOCIAL_TYPE_EVENT);
    }

    /**
     * Processes likes notifications.
     *
     * @since   1.2
     * @access  public
     * @param   SocialTableLikes    $likes  The like object.
     */
    public function onAfterLikeSave(&$likes)
    {
        $allowed = array('discussions.event.create', 'discussions.event.reply');

        if (!in_array($likes->type, $allowed)) {
            return;
        }

        // Get the actor
        $actor = FD::user($likes->created_by);

        // Get the discussion object since it's tied to the stream
        $discussion = FD::table('Discussion');
        $discussion->load($likes->uid);

        list($element, $group, $verb) = explode('.', $likes->type);

        // APP_EVENT_DISCUSSIONS_EMAILS_CREATE_LIKE_ITEM_SUBJECT
        // APP_EVENT_DISCUSSIONS_EMAILS_CREATE_LIKE_INVOLVED_SUBJECT
        // APP_EVENT_DISCUSSIONS_EMAILS_REPLY_LIKE_ITEM_SUBJECT
        // APP_EVENT_DISCUSSIONS_EMAILS_REPLY_LIKE_INVOLVED_SUBJECT

        // apps/event/discussions/create.like.item
        // apps/event/discussions/create.like.involved
        // apps/event/discussions/reply.like.item
        // apps/event/discussions/reply.like.involved

        $emailOptions = array(
            'title' => 'APP_EVENT_DISCUSSIONS_EMAILS_' . strtoupper($verb) . '_LIKE_ITEM_SUBJECT',
            'template' => 'apps/event/discussions/' . $verb . '.like.item',
            'permalink' => FRoute::stream(array('layout' => 'item', 'id' => $likes->stream_id, 'external' => true)),
            'actor' => $actor->getName(),
            'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
            'actorLink' => $actor->getPermalink(true, true)
        );

         $systemOptions  = array(
            'context_type' => $likes->type,
            'url' => FRoute::stream(array('layout' => 'item', 'id' => $likes->stream_id, 'sef' => false)),
            'actor_id' => $likes->created_by,
            'uid' => $likes->uid,
            'aggregate' => true
        );

         // Notify the owner first
         if ($likes->created_by != $discussion->created_by) {
             FD::notify('likes.item', array($discussion->created_by), $emailOptions, $systemOptions);
         }

         // Get a list of recipients to be notified for this stream item
         // We exclude the owner of the discussion and the actor of the like here
         $recipients = $this->getStreamNotificationTargets($likes->uid, $element, $group, $verb, array(), array($discussion->created_by, $likes->created_by));

         $emailOptions['title'] = 'APP_EVENT_DISCUSSIONS_EMAILS_' . strtoupper($verb) . '_LIKE_INVOLVED_SUBJECT';
         $emailOptions['template'] = 'apps/event/discussions/' . $verb . '.like.involved';

         // Notify other participating users
         FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);
    }

    public function onAfterCommentSave($comment)
    {
        $allowed = array('discussions.event.create', 'discussions.event.reply');

        if (!in_array($comment->element, $allowed)) {
            return;
        }

        $stream = FD::table('Stream');
        $stream->load($comment->stream_id);
        $streamItems = $stream->getItems();

        // Since we have the stream, we can get the event id
        $event = FD::event($stream->cluster_id);

        // Get the actor
        $actor = FD::user($comment->created_by);

        // Get the discussion object since it's tied to the stream
        $discussion = FD::table('Discussion');
        $discussion->load($streamItems[0]->context_id);

        list($element, $group, $verb) = explode('.', $comment->element);

        // APP_EVENT_DISCUSSIONS_EMAILS_CREATE_COMMENT_ITEM_SUBJECT
        // APP_EVENT_DISCUSSIONS_EMAILS_CREATE_COMMENT_INVOLVED_SUBJECT
        // APP_EVENT_DISCUSSIONS_EMAILS_REPLY_COMMENT_ITEM_SUBJECT
        // APP_EVENT_DISCUSSIONS_EMAILS_REPLY_COMMENT_INVOLVED_SUBJECT

        // apps/event/discussions/create.comment.item
        // apps/event/discussions/create.comment.involved
        // apps/event/discussions/reply.comment.item
        // apps/event/discussions/reply.comment.involved

        $emailOptions = array(
            'title' => 'APP_EVENT_DISCUSSIONS_EMAILS_' . strtoupper($verb) . '_COMMENT_ITEM_SUBJECT',
            'template' => 'apps/event/discussions/' . $verb . '.comment.item',
            'permalink' => $stream->getPermalink(true, true),
            'actor' => $actor->getName(),
            'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
            'actorLink' => $actor->getPermalink(true, true),
            'event' => $event->getName(),            
            'comment' => $comment->comment
        );

        $systemOptions  = array(
            'context_type' => $comment->element,
            'context_ids' => $discussion->id,            
            'content' => $comment->comment,
            'url' => $stream->getPermalink(false, false, false),
            'actor_id' => $comment->created_by,
            'uid' => $comment->uid,
            'aggregate' => true
        );

         // Notify the owner first
        if ($comment->created_by != $discussion->created_by) {
            FD::notify('comments.item', array($discussion->created_by), $emailOptions, $systemOptions);
        }

         // Get a list of recipients to be notified for this stream item
         // We exclude the owner of the discussion and the actor of the comment here
        $recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb, array(), array($discussion->created_by, $comment->created_by));

        $emailOptions['title'] = 'APP_EVENT_DISCUSSIONS_EMAILS_' . strtoupper($verb) . '_COMMENT_INVOLVED_SUBJECT';
        $emailOptions['template'] = 'apps/event/discussions/' . $verb . '.comment.involved';

         // Notify other participating users
        FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
    }

    /**
     * Prepare notification items for discussions.
     *
     * @since   1.3
     * @access  public
     * @param   SocialTableNotification $item   The notification object.
     */
    public function onNotificationLoad(SocialTableNotification &$item)
    {
        $allowed = array('events.discussion.create', 'events.discussion.reply', 'events.discussion.answered', 'events.discussion.locked', 'likes.item', 'likes.involved', 'comments.item', 'comments.involved');

        if (!in_array($item->cmd, $allowed)) {
            return;
        }

        // Get the event information
        $event = FD::event($item->uid);
        $actor = FD::user($item->actor_id);

        // Process comments and likes notification
        if (in_array($item->cmd, array('likes.item', 'likes.involved', 'comments.item', 'comments.involved')) && in_array($item->context_type, array('discussions.event.create', 'discussions.event.reply'))) {
            $hook = $this->getHook('notification', $item->type);
            $hook->execute($item);
            return;
        }

        if ($item->cmd == 'events.discussion.create') {

            $discussion = FD::table('Discussion');
            $discussion->load($item->context_ids);

            $item->title = JText::sprintf('APP_EVENT_DISCUSSIONS_NOTIFICATIONS_CREATED_DISCUSSION', $actor->getName(), $event->getName());
            $item->content = $discussion->title;

            return $item;
        }

        if ($item->cmd == 'events.discussion.reply') {

            $item->title = JText::sprintf('APP_EVENT_DISCUSSIONS_NOTIFICATIONS_REPLED_DISCUSSION', $actor->getName(), $event->getName());

            return $item;
        }

        if ($item->cmd == 'events.discussion.answered') {
            $reply = FD::table('Discussion');
            $reply->load($item->context_ids);

            $discussion = FD::table('Discussion');
            $discussion->load($reply->parent_id);

            $item->title = JText::sprintf('APP_EVENT_DISCUSSIONS_NOTIFICATIONS_ACCEPTED_DISCUSSION', $discussion->title);

            return $item;
        }

        if ($item->cmd == 'events.discussion.locked') {

        }
    }

    /**
     * Triggered to validate the stream item whether should put the item as valid count or not.
     *
     * @since   1.3
     * @access  public
     * @param   SocialStreamItem    $item           The stream object.
     * @param   boolean             $includePrivacy True if privacy should be respected.
     * @return  boolean                             True if it should be counted.
     */
    public function onStreamCountValidation(&$item, $includePrivacy = true)
    {
        // If this is not it's context, we don't want to do anything here.
        if ($item->context_type != 'discussions') {
            return false;
        }

        // if this is a cluster stream, let check if user can view this stream or not.
        $params = FD::registry($item->params);
        $event = FD::event($params->get('event'));

        if (!$event) {
            return;
        }

        $item->cnt = 1;

        // If event is not open and the user is not a guest
        if (!$event->isOpen() && !$event->getGuest()->isGuest()) {
            $item->cnt = 0;
        }

        return true;
    }


    /**
     * Prepares the stream item.
     *
     * @since   1.3
     * @access  public
     * @param   SocialStreamItem    $item           The stream object.
     * @param   boolean             $includePrivacy True if privacy should be respected.
     */
    public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
    {
        if ($item->context != 'discussions') {
            return;
        }

        // Get the event object
        $event = FD::event($item->cluster_id);

        if (!$event) {
            return;
        }

        if (!$event->canViewItem()) {
            return;
        }

        // Define standard stream looks
        $item->display = SOCIAL_STREAM_DISPLAY_FULL;
        $item->color = '#69b598';
        $item->fonticon = 'fa fa-comments';
        $item->label = FD::_('COM_EASYSOCIAL_STREAM_CONTEXT_TITLE_DISCUSSIONS_TOOLTIP', true);

        $params = $this->getApp()->getParams();

        if ($params->get('stream_' . $item->verb, true) == false) {
            return;
        }

        // Do not allow user to repost discussions
        $item->repost = false;

        // Process likes and comments differently.
        $likes = FD::likes();
        $likes->get($item->uid, $item->context, $item->verb, SOCIAL_APPS_GROUP_EVENT, $item->uid);
        $item->likes = $likes;

        // Apply comments on the stream
        $comments = FD::comments($item->uid, $item->context, $item->verb, SOCIAL_APPS_GROUP_EVENT, array('url' => FRoute::stream(array('layout' => 'item', 'id' => $item->uid))), $item->uid);
        $item->comments = $comments;

        if ($item->verb == 'create') {
            $this->prepareCreateStream($item);
        }

        if ($item->verb == 'reply') {
            $this->prepareReplyStream($item);
        }

        if ($item->verb == 'answered') {
            $this->prepareAnsweredStream($item);
        }

        if ($item->verb == 'locked') {
            $this->prepareLockedStream($item);
        }
    }

    /**
     * Prepares the stream item for new discussion creation.
     *
     * @since   1.3
     * @access  public
     * @param   SocialStreamItem    $item   The stream item.
     */
    private function prepareCreateStream(&$item)
    {
        // Get the context params
        $params = FD::registry($item->params);
        $data = $params->get('event');

        if (!$data) {
            return;
        }

        $event = FD::event($data->id);

        $discussion = FD::table('Discussion');
        $discussion->load($item->contextId);

        // Determines if there are files associated with the discussion
        $files = $discussion->hasFiles();
        $permalink = FRoute::apps(array('layout' => 'canvas', 'customView' => 'item', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $this->getApp()->getAlias(), 'discussionId' => $discussion->id), false);

        $content = $this->formatContent($discussion);

        $this->set('files', $files);
        $this->set('actor', $item->actor);
        $this->set('permalink', $permalink);
        $this->set('discussion', $discussion);
        $this->set('content', $content);

        // Load up the contents now.
        $item->title = parent::display('streams/create.title');
        $item->content = parent::display('streams/create.content');
    }

    /**
     * Prepares the stream item for new discussion creation.
     *
     * @since   1.3
     * @access  public
     * @param   SocialStreamItem    $item   The stream item.
     */
    private function prepareReplyStream(&$item)
    {
        // Get the context params
        $params  = FD::registry($item->params);
        $data = $params->get('event');

        if (!$data) {
            return;
        }

        $event = FD::event($data->id);

        $discussion = FD::table('Discussion');
        $discussion->load($item->contextId);

        $reply = FD::table('Discussion');
        $reply->bind($params->get('reply'));

        $permalink = FRoute::apps(array('layout' => 'canvas', 'customView' => 'item', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $this->getApp()->getAlias(), 'discussionId' => $discussion->id), false);

        $content = $this->formatContent($reply);

        $this->set('actor', $item->actor);
        $this->set('permalink', $permalink);
        $this->set('discussion', $discussion);
        $this->set('reply', $reply);
        $this->set('content', $content);

        // Load up the contents now.
        $item->title = parent::display('streams/reply.title');
        $item->content = parent::display('streams/reply.content');
    }

    /**
     * Prepares the stream item for new discussion creation
     *
     * @since   1.3
     * @access  public
     * @param   SocialStreamItem    $item   The stream item.
     */
    private function prepareAnsweredStream(&$item)
    {
        // Get the context params
        $params = FD::registry($item->params);
        $data = $params->get('event');

        if (!$data) {
            return;
        }

        // Load the event object
        $event = FD::event($data->id);

        // Load the discussion
        $discussion = FD::table('Discussion');
        $discussion->bind($params->get('discussion'));

        $reply = FD::table('Discussion');
        $reply->bind($params->get('reply'));

        $permalink = FRoute::apps(array('layout' => 'canvas', 'customView' => 'item', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $this->getApp()->getAlias(), 'discussionId' => $discussion->id), false);

        $content = $this->formatContent($reply);

        // Get the reply author
        $reply->author = FD::user($reply->created_by);

        $this->set('actor', $item->actor);
        $this->set('permalink', $permalink);
        $this->set('discussion', $discussion);
        $this->set('reply', $reply);
        $this->set('content', $content);

        // Load up the contents now.
        $item->title = parent::display('streams/answered.title');
        $item->content = parent::display('streams/answered.content');

        // We want it to be SOCIAL_STREAM_DISPLAY_MINI but we also want the accepted answer to show as well.
        // Hence we leave the display to full but we disable comments, likes, sharing and repost
        $item->comments = false;
        $item->likes = false;
        $item->sharing = false;
    }

    /**
     * Prepares the stream item for new discussion creation
     *
     * @since   1.3
     * @access  public
     * @param   SocialStreamItem    $item   The stream item.
     */
    private function prepareLockedStream(&$item)
    {
        // Get the context params
        $params = FD::registry($item->params);
        $event = FD::event($params->get('event')->id);

        $discussion = FD::table('Discussion');
        $discussion->bind($params->get('discussion'));

        $permalink = FRoute::apps(array('layout' => 'canvas', 'customView' => 'item', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $this->getApp()->getAlias(), 'discussionId' => $discussion->id), false);

        $item->display = SOCIAL_STREAM_DISPLAY_MINI;

        $this->set('permalink', $permalink);
        $this->set('actor', $item->actor);
        $this->set('discussion', $discussion);

        // Load up the contents now.
        $item->title = parent::display('streams/locked.title');
    }

    /**
     * Formats the stream content.
     * @param  SocialTableDiscussion    $discussion The discussion table object.
     * @return string                               The formatted content.
     */
    public function formatContent($discussion)
    {
        // Reduce length based on the settings
        $params = $this->getParams();
        $max = $params->get('stream_length', 250);
        $content = $discussion->content;

        // Remove code blocks
        $content = FD::string()->parseBBCode($content, array('code' => true, 'escape' => false));

        // Remove [file] from contents
        $content = $discussion->removeFiles($content);

        if ($max) {

            // lets do a simple content truncation here.
            $content = strip_tags($content);
            $content = strlen($content) > $max ? JString::substr($content, 0, $max ) . JText::_('COM_EASYSOCIAL_ELLIPSES') : $content ;
        }

        return $content;
    }

    public function appListing($view, $eventId, $type)
    {
        $event = FD::event($eventId);

        return $event->getParams()->get('discussions', true);
    }
}
