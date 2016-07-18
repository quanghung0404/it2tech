<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialEventAppVideos extends SocialAppItem
{
    public $appListing = false;

    public function __construct( $options = array() )
    {
        parent::__construct($options);
    }

    /**
     * Determines if videos should be enabled for a given event
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function enabled(SocialEvent &$event)
    {
        $params = $event->getParams();

        if (!$params->get('videos', true)) {
            return false;
        }

        return true;
    }

    /**
     * Determines if the app has stream filter
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function hasStreamFilter()
    {
        $id = $this->input->get('id', 0, 'int');

        if (!$id) {
            return parent::hasStreamFilter();
        }
        
        $event = ES::event($id);

        if (!$this->enabled($event)) {
            return false;
        }

        return parent::hasStreamFilter();
    }

    /**
     * Responsible to return the excluded verb from this app context
     * @since   1.4
     * @access  public
     * @param   array
     */
    public function onStreamVerbExclude(&$exclude)
    {
        // Get app params
        $params = $this->getParams();

        $excludeVerb = false;

        if (!$params->get('uploadVideos', true)) {
            $excludeVerb[] = 'create';
        }

        if (!$params->get('featuredVideos', true)) {
            $excludeVerb[] = 'featured';
        }

        if ($excludeVerb !== false) {
            $exclude['videos'] = $excludeVerb;
        }
    }


    /**
     * Triggered to validate the stream item whether should put the item as valid count or not.
     *
     * @since   1.4
     * @access  public
     * @param   jos_social_stream, boolean
     * @return  0 or 1
     */
    public function onStreamCountValidation(&$item, $includePrivacy = true)
    {
        // If this is not it's context, we don't want to do anything here.
        if ($item->context_type != 'videos') {
            return false;
        }

        $params = ES::registry($item->params);
        $event = ES::event($params->get('event'));

        if (!$event) {
            return;
        }

        $item->cnt = 1;

        if (!$event->isOpen() && !$event->isMember($this->my->id)) {
            $item->cnt = 0;
        }

        return true;
    }

    /**
     * Generates the stream item for videos
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function onPrepareStream(SocialStreamItem &$stream, $includePrivacy = true)
    {
        if ($stream->context != SOCIAL_TYPE_VIDEOS) {
            return;
        }

        // Determines if the viewer can view the stream item from this group
        $event = $stream->getCluster();

        if (!$event) {
            return;
        }

        if (!$event->canViewItem()) {
            return;
        }

        // Decorate the stream item with the neccessary design
        $stream->color = '#5580BE';
        $stream->fonticon = 'ies-play';
        $stream->label = JText::_('COM_EASYSOCIAL_STREAM_TITLE_VIDEOS', true);
        $stream->display = SOCIAL_STREAM_DISPLAY_FULL;

        if (!$this->enabled($event)) {
            return;
        }

        // Get the video
        $video = ES::video($stream->cluster_id, SOCIAL_TYPE_EVENT, $stream->contextId);

        // Ensure that the video is really published
        if (!$video->isPublished()) {
            return;
        }

        // Get the actor
        $actor = $stream->getActor();

        $this->set('stream', $stream);
        $this->set('video', $video);
        $this->set('actor', $actor);

        // Update the stream title
        $stream->title = parent::display('themes:/site/videos/stream/event/title.' . $stream->verb);
        $stream->content = parent::display('themes:/site/videos/stream/stream.content');

        // Assign the comments library
        $stream->comments = $video->getComments($stream->verb, $stream->uid);

        // Assign the likes library
        $stream->likes = $video->getLikes($stream->verb, $stream->uid);
    }

    /**
     * Generates the story form for videos
     *
     * @since   1.4
     * @access  public
     * @param   string
     */
    public function onPrepareStoryPanel(SocialStory $story)
    {
        // Ensure that videos is enabled
        if (!$this->config->get('video.enabled')) {
            return;
        }

        // If uploading and embedding is disabled, there is no point showing the form at all
        if (!$this->config->get('video.uploads') && !$this->config->get('video.embeds')) {
            return;
        }
        
        // Get the event id
        $eventId = $story->cluster;

        // Ensure that the event allows users to upload videos
        $event = ES::event($eventId);

        $eventHasVideos = $event->getParams()->get('videos');

        if (!$eventHasVideos) {
            return;
        }

        // Get the video adapter
        $adapter = ES::video($eventId, SOCIAL_TYPE_EVENT);

        // Ensure that video creation is allowed
        if (!$adapter->allowCreation()) {
            return;
        }

        // Get a list of video categories
        $model = ES::model('Videos');
        $categories = $model->getCategories();

        // Create a new plugin for this video
        $plugin = $story->createPlugin('videos', 'panel');

        // Get the maximum upload filesize allowed
        $uploadLimit = $adapter->getUploadLimit();

        $theme = ES::themes();
        $theme->set('categories', $categories);
        $theme->set('uploadLimit', $uploadLimit);

        $button = $theme->output('site/videos/story/button');
        $form = $theme->output('site/videos/story/form');

        $script = ES::script();
        $script->set('uploadLimit', $uploadLimit);
        $script->set('type', SOCIAL_TYPE_EVENT);
        $script->set('uid', $adapter->id);

        $plugin->setHtml($button, $form);
        $plugin->setScript($script->output('site/videos/story/plugin'));

        return $plugin;
    }

    /**
     * Processes after a story is saved on the site. When the story is stored, we need to create the necessary video
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function onBeforeStorySave(SocialStreamTemplate &$template, SocialStream &$stream, $content)
    {
        if ($template->context_type != 'videos') {
            return;
        }

        // Check if user is really allowed to do this?
        $cluster = ES::cluster($template->cluster_type, $template->cluster_id);

        if (!$cluster->isMember() && !$this->my->isSiteAdmin()) {
            JError::raiseError(500, JText::_('COM_EASYSOCIAL_CLUSTER_NOT_ALLOWED_TO_POST_UPDATE'));
            return;
        }

        // Determine the type of the video
        $data = array();
        $data['source'] = $this->input->get('videos_type', '', 'word');
        $data['title'] = $this->input->get('videos_title', '', 'default');
        $data['description'] = $this->input->get('videos_description', '', 'default');
        $data['link'] = $this->input->get('videos_link', '', 'default');
        $data['category_id'] = $this->input->get('videos_category', 0, 'int');
        $data['uid'] = $template->cluster_id;
        $data['type'] = $template->cluster_type;

        // Save options for the video library
        $saveOptions = array();

        // If this is a link source, we just load up a new video library
        if ($data['source'] == 'link') {
            $video = ES::video($template->cluster_id, SOCIAL_TYPE_EVENT);
        }

        // If this is a video upload, the id should be provided because videos are created first.
        if ($data['source'] == 'upload') {
            $id = $this->input->get('videos_id', 0, 'int');

            $video = ES::video($template->cluster_id, SOCIAL_TYPE_EVENT, $id);

            // Video library needs to know that we're storing this from the story
            $saveOptions['story'] = true;

            // We cannot publish the video if auto encoding is disabled
            if ($this->config->get('video.autoencode')) {
                $data['state'] = SOCIAL_VIDEO_PUBLISHED;
            }
        }

        // Check if user is really allowed to upload videos
        if ($video->id && !$video->canEdit()) {
            return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_EDITING'));
        }

        // Try to save the video
        $state = $video->save($data, array(), $saveOptions);
        
        // We need to update the context
        $template->context_type = SOCIAL_TYPE_VIDEOS;
        $template->context_id = $video->id;

        $options = array();
        $options['userId'] = $this->my->id;
        $options['title'] = $video->title;
        $options['description'] = $video->getDescription();
        $options['permalink'] = $video->getPermalink();
        $options['id'] = $video->id;

        // Notify group members when a video is uploaded on the site
        $cluster->notifyMembers('video.create', $options);
    }

    public function onAfterStorySave(&$stream, &$streamItem)
    {
        // Determine the type of the video
        $data = array();
        $data['source'] = $this->input->get('videos_type', '', 'word');

        // If this is a video upload, the id should be provided because videos are created first.
        if ($data['source'] == 'upload' && !$this->config->get('video.autoencode')) {
            $streamItem->hidden = true;
        }
    }
    
    /**
     * Triggers when unlike happens
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function onAfterLikeDelete(&$likes)
    {
        if (!$likes->type) {
            return;
        }

        // Deduct points when the user unliked a video
        if ($likes->type == 'videos.event.create' || $likes->type == 'videos.event.featured') {
             ES::points()->assign('video.unlike', 'com_easysocial', $this->my->id);
        }
    }


    /**
     * Triggers after a like is saved
     *
     * @since   1.0
     * @access  public
     * @param   object  $params     A standard object with key / value binding.
     *
     * @return  none
     */
    public function onAfterLikeSave(&$likes)
    {
        $allowed = array('videos.event.create', 'videos.event.featured');

        if (!in_array($likes->type, $allowed)) {
            return;
        }

        // Get the actor of the likes
        $actor = ES::user($likes->created_by);

        // Set the email options
        $emailOptions = array(
            'template' => 'apps/event/videos/like.video.item',
            'actor' => $actor->getName(),
            'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
            'actorLink' => $actor->getPermalink(true, true)
        );

        $systemOptions = array(
            'context_type' => $likes->type,
            'actor_id' => $likes->created_by,
            'uid' => $likes->uid,
            'aggregate' => true
        );

        // Standard email subject
        $ownerTitle = 'COM_EASYSOCIAL_VIDEOS_EMAILS_CLUSTER_EMAILS_LIKE_VIDEO_OWNER_SUBJECT';
        $involvedTitle = 'COM_EASYSOCIAL_VIDEOS_EMAILS_CLUSTER_EMAILS_LIKE_VIDEO_INVOLVED_SUBJECT';

        $videoTable = ES::table('Video');
        $videoTable->load($likes->uid);

        $video = ES::video($videoTable);

        // Get the permalink to the photo
        $systemOptions['context_ids'] = $video->id;
        $emailOptions['permalink'] = $video->getPermalink(true);
        $systemOptions['url'] = $video->getPermalink(false);

        // For single photo items on the stream
        if ($likes->type == 'videos.event.create') {
            $verb = 'create';
        }

        if ($likes->type == 'videos.event.featured') {
            $verb = 'featured';
        }

        // Default title
        $emailOptions['title'] = $ownerTitle;

        // @points: photos.like
        // Assign points for the author for liking this item
        ES::points()->assign('video.like', 'com_easysocial', $likes->created_by);


        // Notify the owner of the photo first
        if ($likes->created_by != $video->user_id) {
            ES::notify('likes.item', array($video->user_id), $emailOptions, $systemOptions);
        }

        $element = 'videos';
        $verb = 'create';
        // // Get additional recipients since photos has tag
        // $additionalRecipients = array();
        // $this->getTagRecipients($additionalRecipients, $video);

        // Get a list of recipients to be notified for this stream item
        // We exclude the owner of the note and the actor of the like here
        $recipients = $this->getStreamNotificationTargets($likes->uid, $element, 'group', $verb, array(), array($video->user_id, $likes->created_by));

        $emailOptions['title'] = $involvedTitle;
        $emailOptions['template'] = 'apps/event/videos/like.video.involved';

        // Notify other participating users
        ES::notify('likes.involved', $recipients, $emailOptions, $systemOptions);

        return;
    }

    /**
     * Renders the notification item
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function onNotificationLoad(SocialTableNotification &$item)
    {
        $allowed = array('event.video.create', 'comments.item', 'comments.involved', 'likes.item');

        if (!in_array($item->cmd, $allowed)) {
            return;
        }

        if ($item->cmd == 'event.video.create') {
            $hook = $this->getHook('notification', 'updates');
            $hook->execute($item);

            return;
        }

        // Someone posted a comment on the video
        if ($item->cmd == 'comments.item' || $item->cmd == 'comments.involved') {
            $hook = $this->getHook('notification', 'comments');
            $hook->execute($item);

            return;
        }

        // Someone likes a video
        if ($item->cmd == 'likes.item') {
            $hook = $this->getHook('notification', 'likes');
            $hook->execute($item);

            return;
        }  

        return;
    }

    /**
     * Triggered after a comment is deleted
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function onAfterDeleteComment(SocialTableComments &$comment)
    {
        $allowed = array('videos.event.create', 'videos.event.featured');

        if (!in_array($comment->element, $allowed)) {
            return;
        }

        // Assign points when a comment is deleted for a video
        ES::points()->assign('video.comment.remove', 'com_easysocial', $comment->created_by);
    }

    /**
     * Triggered when a comment save occurs
     *
     * @since   1.4
     * @access  public
     * @param   SocialTableComments The comment object
     * @return
     */
    public function onAfterCommentSave(&$comment)
    {
        $allowed = array('videos.event.create', 'videos.event.featured');

        if (!in_array($comment->element, $allowed)) {
            return;
        }

        // Get the actor of the likes
        $actor = ES::user($comment->created_by);

        // Set the email options
        $emailOptions   = array(
            'template' => 'apps/event/videos/comment.video.item',
            'actor' => $actor->getName(),
            'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
            'actorLink' => $actor->getPermalink(true, true),
            'comment' => $comment->comment
        );

        $systemOptions  = array(
            'context_type' => $comment->element,
            'context_ids' => $comment->uid,
            'actor_id' => $comment->created_by,
            'uid' => $comment->id,
            'aggregate' => true
        );

        // Standard email subject
        $ownerTitle = 'COM_EASYSOCIAL_VIDEOS_CLUSTER_COMMENT_VIDEO_OWNER_SUBJECT';
        $involvedTitle = 'COM_EASYSOCIAL_VIDEOS_CLUSTER_COMMENT_VIDEO_INVOLVED_SUBJECT';

        $videoTable = ES::table('Video');
        $videoTable->load($comment->uid);

        $video = ES::video($videoTable);

        $emailOptions['permalink'] = $video->getPermalink(true, true);
        $systemOptions['url'] = $video->getPermalink(false, false, 'item', false);

        $element = 'videos';
        $verb = 'create';

        // Default email title should be for the owner
        $emailOptions['title'] = $ownerTitle;

        // Assign points for the author for posting a comment
        ES::points()->assign('videos.comment.add', 'com_easysocial', $comment->created_by);

        // Notify the owner of the photo first
        if ($video->user_id != $comment->created_by) {
            FD::notify('comments.item', array($video->user_id), $emailOptions, $systemOptions);
        }

        // Get a list of recipients to be notified for this stream item
        // We exclude the owner of the note and the actor of the like here
        $recipients = $this->getStreamNotificationTargets($comment->uid, $element, 'event', $verb, array(), array($video->user_id, $comment->created_by));

        $emailOptions['title'] = $involvedTitle;
        $emailOptions['template'] = 'apps/event/videos/comment.video.involved';

        // Notify other participating users
        ES::notify('comments.involved', $recipients, $emailOptions, $systemOptions);

        return;
    }

}
