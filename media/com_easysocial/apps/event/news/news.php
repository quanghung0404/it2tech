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

class SocialEventAppNews extends SocialAppItem
{
    /**
     * Responsible to return the favicon object
     *
     * @since   1.2
     * @access  public
     */
    public function getFavIcon()
    {
        $obj = new stdClass();
        $obj->color = '#F6C362';
        $obj->icon = 'fa-bullhorn';
        $obj->label = 'APP_EVENT_NEWS_STREAM_TOOLTIP';

        return $obj;
    }

    /**
     * Displays notifications from the event
     *
     * @since   1.2
     * @access  public
     */
    public function onNotificationLoad(SocialTableNotification &$item)
    {

        // Processes notifications when someone posts a new update in a event
        // context_type: event.news
        // type: events
        if ($item->cmd == 'events.news') {
            $hook = $this->getHook('notification', 'news');
            $hook->execute($item);
            return;
        }

        if ($item->type == 'likes' && $item->context_type == 'news.event.create') {

            $hook     = $this->getHook('notification', 'likes');
            $hook->execute($item);

            return;
        }

        if ($item->type == 'comments' && $item->context_type == 'news.event.create') {

            $hook     = $this->getHook('notification', 'comments');
            $hook->execute($item);

            return;
        }
    }

    /**
     * Processes after someone comments on an announcement
     *
     * @since   1.2
     * @access  public
     */
    public function onAfterCommentSave(&$comment)
    {
        $allowed = array('news.event.create');

        if (!in_array($comment->element, $allowed)) {
            return;
        }


        if ($comment->element == 'news.event.create') {

            // Get the stream object
            $news = FD::table('ClusterNews');
            $news->load($comment->uid);

            list($element, $group, $verb) = explode('.', $comment->element);

            // Get the comment actor
            $actor = FD::user($comment->created_by);

            $emailOptions = array(
                'title' => 'APP_EVENT_NEWS_EMAILS_COMMENT_ITEM_TITLE',
                'template' => 'apps/event/news/comment.news.item',
                'comment' => $comment->comment,
                'permalink' => $news->getPermalink(true, true),
                'actorName' => $actor->getName(),
                'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
                'actorLink' => $actor->getPermalink(true, true)
           );

            $systemOptions = array(
                'content' => $comment->comment,
                'context_type' => $comment->element,
                'context_ids' => $news->cluster_id,
                'url' => $news->getPermalink(false, false, false),
                'actor_id' => $comment->created_by,
                'uid' => $comment->uid,
                'aggregate' => true
           );


            // Notify the note owner
            if ($comment->created_by != $news->created_by) {
                FD::notify('comments.item', array($news->created_by), $emailOptions, $systemOptions);
            }

            // Get a list of recipients to be notified for this stream item.
            // We exclude the owner of the note and the actor of the like here
            $recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb, array(), array($news->created_by, $comment->created_by));

            $emailOptions['title'] = 'APP_EVENT_NEWS_EMAILS_COMMENT_ITEM_INVOLVED_TITLE';
            $emailOptions['template'] = 'apps/event/news/comment.news.involved';

            // Notify participating users
            FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);

            return;
        }
    }

    /**
     * Processes after someone likes an announcement
     *
     * @since   1.2
     * @access  public
     */
    public function onAfterLikeSave(&$likes)
    {
        $allowed = array('news.event.create');

        if (!in_array($likes->type, $allowed)) {
            return;
        }


        if ($likes->type == 'news.event.create') {

            // Get the stream object
            $news = FD::table('ClusterNews');
            $news->load($likes->uid);

            // Get the likes actor
            $actor = FD::user($likes->created_by);

            $emailOptions = array(
                'title' => 'APP_EVENT_NEWS_EMAILS_LIKE_ITEM_SUBJECT',
                'template' => 'apps/event/news/like.news.item',
                'permalink' => $news->getPermalink(true, true),
                'actor' => $actor->getName(),
                'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
                'actorLink' => $actor->getPermalink(true, true)
           );

            $systemOptions = array(
                'context_type' => $likes->type,
                'context_ids' => $news->cluster_id,
                'url' => $news->getPermalink(false, false, false),
                'actor_id' => $likes->created_by,
                'uid' => $likes->uid,
                'aggregate' => true
           );

            // Notify the owner first
            if ($news->created_by != $likes->created_by) {
                FD::notify('likes.item', array($news->created_by), $emailOptions, $systemOptions);
            }

            // Get a list of recipients to be notified for this stream item
            // We exclude the owner of the note and the actor of the like here
            $recipients = $this->getStreamNotificationTargets($likes->uid, 'news', 'event', 'create', array(), array($news->created_by, $likes->created_by));

            $emailOptions['title'] = 'APP_EVENT_NEWS_EMAILS_LIKE_INVOLVED_SUBJECT';
            $emailOptions['template'] = 'apps/event/news/like.news.involved';

            // Notify other participating users
            FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);

            return;
        }
    }

    /**
     * Prepares the stream item for events
     *
     * @since   1.2
     * @access  public
     * @param   SocialStreamItem    The stream object.
     * @param   bool                Determines if we should respect the privacy
     */
    public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
    {
        if ($item->context != 'news') {
            return;
        }

        // event access checking
        $event = FD::event($item->cluster_id);

        if (!$event) {
            return;
        }

        if (!$event->canViewItem()) {
            return;
        }

        // Define standard stream looks
        $item->display = SOCIAL_STREAM_DISPLAY_FULL;
        $item->color = '#F6C362';
        $item->fonticon = 'fa fa-bullhorn';
        $item->label = FD::_('APP_EVENT_NEWS_STREAM_TOOLTIP', true);

        // Do not allow user to repost an announcement
        $item->repost = false;

        if ($item->verb == 'create') {
            $this->prepareCreateStream($item, $event);
        }
    }

    private function prepareCreateStream(SocialStreamItem &$item, $event)
    {
        if (!$event->canViewItem()) {
            return;
        }

        $params = FD::registry($item->params);

        $data = $params->get('news');

        // Load the event
        $event = FD::event($data->cluster_id);

        // Load the news data
        $news = FD::table('ClusterNews');
        $news->load($data->id);

        // Get the permalink
        $permalink = FRoute::apps(array('layout' => 'canvas', 'customView' => 'item', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $this->getApp()->getAlias(), 'newsId' => $news->id), false);

        // Get the app params
        $appParams = $this->getApp()->getParams();

        // Format the content
        $this->format($news, $appParams->get('stream_length'));

        // Attach actions to the stream
        $this->attachActions($item, $news, $permalink, $appParams);

        $this->set('event', $event);
        $this->set('appParams', $appParams);
        $this->set('permalink', $permalink);
        $this->set('news', $news);
        $this->set('actor', $item->actor);

        // Load up the contents now.
        $item->title = parent::display('streams/create.title');
        $item->content = parent::display('streams/create.content');
    }

    private function format(&$news, $length = 0)
    {
        if ($length == 0) {
            return;
        }

        $news->content = JString::substr(strip_tags($news->content), 0, $length) . ' ' . JText::_('COM_EASYSOCIAL_ELLIPSES');
    }

    private function attachActions(&$item, &$news, $permalink, $appParams)
    {
        // We need to link the comments to the news
        $item->comments = FD::comments($news->id, 'news', 'create', SOCIAL_APPS_GROUP_EVENT, array('url' => $permalink), $item->uid);

        // The comments for the stream item should link to the news itself.
        if (!$appParams->get('allow_comments') || !$news->comments) {
            $item->comments = false;
        }

        // The likes needs to be linked to the news itself
        $likes = FD::likes();
        $likes->get($news->id, 'news', 'create', SOCIAL_APPS_GROUP_EVENT, $item->uid);

        $item->likes = $likes;
    }

    public function appListing($view, $eventId, $type)
    {
        $event = FD::event($eventId);

        return $event->getParams()->get('news', true);
    }
}
