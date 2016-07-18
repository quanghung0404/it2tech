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

class SocialUserAppVideos extends SocialAppItem
{
    public function __construct($options = array())
    {
        JFactory::getLanguage()->load('app_videos', JPATH_ROOT);

        parent::__construct($options);
    }

    /**
     * Responsible to return the excluded verb from this app context
     *
     * @since   1.4
     * @access  public
     * @param   array
     */
    public function onStreamVerbExclude(&$exclude)
    {
        // Get app params
        $params = $this->getParams();

        $excludeVerb = false;

        if(! $params->get('uploadVideos', true)) {
            $excludeVerb[] = 'create';
        }

        if (! $params->get('featuredVideos', true)) {
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
    public function onStreamCountValidation( &$item, $includePrivacy = true )
    {
        // If this is not it's context, we don't want to do anything here.
        if ($item->context_type != 'videos') {
            return false;
        }

        $item->cnt = 1;

        if ($includePrivacy) {
            $my         = Foundry::user();
            $privacy    = Foundry::privacy( $my->id );

            $sModel = Foundry::model( 'Stream' );
            $aItem  = $sModel->getActivityItem( $item->id, 'uid' );

            $uid        = $aItem[0]->context_id;
            $rule       = 'videos.view';
            $context    = 'videos';

            if (!$privacy->validate($rule, $uid, $context, $item->actor_id)) {
                $item->cnt = 0;
            }
        }

        return true;
    }

    /**
     * Responsible to generate the activity logs.
     *
     * @since   1.4
     * @access  public
     * @param   object  $params     A standard object with key / value binding.
     *
     * @return  none
     */
    public function onPrepareActivityLog( SocialStreamItem &$item, $includePrivacy = true )
    {
        if ($item->context != 'videos') {
            return;
        }

        // we only process video upload activity log since 'featured' can only be done by admin.
        if ($item->verb != 'create') {
            return;
        }

        // Get the context id.
        $id = $item->contextId;

        // Load the videos
        $video = ES::video($this->my->id, SOCIAL_TYPE_USER);
        $video->load($id);

        // Get the actor
        $actor      = $item->actor;
        $target     = false;

        // Determines if the photo is shared on another person's timeline
        if ($item->targets ) {
            $target     = $item->targets[0];
        }


        $term   = $this->getGender( $item->actor );

        $this->set( 'term'  , $term );
        $this->set( 'actor' , $actor );
        $this->set( 'target', $target );
        $this->set( 'video' , $video );
        $this->set( 'stream' , $item );

        $count          = count( $item->contextIds );
        $this->set( 'count' , $count );

        $file = 'user/';

        if( $item->cluster_id && $item->cluster_type == SOCIAL_TYPE_GROUP)
        {
            $file = 'group/';

            $group = Foundry::group( $item->cluster_id );
            $this->set( 'cluster' , $group );

        } else if ($item->cluster_id && $item->cluster_type == SOCIAL_TYPE_EVENT) {
            $file = 'event/';

            $event = Foundry::event( $item->cluster_id );
            $this->set( 'cluster' , $event );
        }

        $file   .= 'title.create';

        // var_dump($file);exit;

        $item->display  = SOCIAL_STREAM_DISPLAY_MINI;
        $item->title = parent::display('themes:/site/videos/stream/' . $file);
        $item->content  = parent::display( 'streams/activity_content' );

        $privacyRule = 'videos.view';

        if( $includePrivacy )
        {
            $my         = Foundry::user();

            $sModel = Foundry::model('Stream');
            $aItem  = $sModel->getActivityItem( $item->aggregatedItems[0]->uid, 'uid' );

            $streamId = count($aItem) > 1 ? '' : $item->aggregatedItems[0]->uid;
            $item->privacy = Foundry::privacy( $my->id )->form( $video->id, 'videos', $item->actor->id, $privacyRule, false, $streamId );
        }
    }


    /**
     * Retrieves the Gender representation of the language string
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    private function getGender(SocialUser $user)
    {
        // Get the term to be displayed
        $value = $user->getFieldData('GENDER');

        $term = 'NOGENDER';

        if ($value == 1) {
            $term = 'MALE';
        }

        if ($value == 2) {
            $term = 'FEMALE';
        }

        return $term;
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

        $my = FD::user();
        $privacy = $my->getPrivacy();

        // privacy validation
        if ($includePrivacy && !$privacy->validate('videos.view', $stream->contextId, SOCIAL_TYPE_VIDEOS, $stream->actor->id)) {
            return;
        }

        // Perform necessary checks on the stream
        $accessible = $this->setStreamIcon($stream);

        if ($accessible === false) {
            return;
        }

        // If there's a cluster id, we know this is not a user group
        $group = $stream->cluster_type ? $stream->cluster_type : SOCIAL_TYPE_USER;

        // Get the actor
        $actor = $stream->getActor();

        // Get the video
        $video = null;
        if ($group == SOCIAL_TYPE_USER) {
            $video = ES::video();
            $video->load($stream->contextId);
        } else {
            $video = ES::video($stream->cluster_id, $group, $stream->contextId);
        }

        // Ensure that the video is really published
        if (!$video->isPublished()) {
            return;
        }

        $target = count($stream->targets) > 0 ? $stream->targets[0] : '';

        // Get the cluster
        $cluster = $stream->getCluster();

        if ($cluster) {
            $target = $cluster;
        }

        $this->set('target', $target);
        $this->set('stream', $stream);
        $this->set('video', $video);
        $this->set('actor', $actor);
        $this->set('cluster', $cluster);

        // Update the stream title
        $stream->title = parent::display('themes:/site/videos/stream/' . $group . '/title.' . $stream->verb);
        $stream->content = parent::display('themes:/site/videos/stream/stream.content');

        // Assign the comments library
        $stream->comments = $video->getComments($stream->verb, $stream->uid);

        // Assign the likes library
        $stream->likes = $video->getLikes($stream->verb, $stream->uid);

        if ($includePrivacy) {
            $stream->privacy  = $privacy->form($stream->contextId, $stream->context, $stream->actor->id, 'videos.view', false, $stream->uid );
        }

        // If the video has a thumbnail, add the opengraph tags
        $thumbnail = $video->getThumbnail();

        if ($thumbnail) {
            $stream->addOgImage($thumbnail);
        }

        return true;
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

        // Ensure that the user really has access to create videos
        $video = ES::video();

        if (!$video->allowCreation()) {
            return;
        }

        // Get a list of video categories
        $model = ES::model('Videos');

        // Get a list of video categories
        $options = array();

        if (!$this->my->isSiteAdmin()) {
            $options = array('respectAccess' => true, 'profileId' => $this->my->getProfile()->id);
        }

        $categories = $model->getCategories($options);

        // Create a new plugin for this video
        $plugin = $story->createPlugin('videos', 'panel');

        // Get the maximum file size allowed for video uploads
        $uploadLimit = $video->getUploadLimit();

        $theme = ES::themes();
        $theme->set('categories', $categories);
        $theme->set('uploadLimit', $uploadLimit);
        $button = $theme->output('site/videos/story/button');
        $form = $theme->output('site/videos/story/form');

        $script = ES::script();
        $script->set('uploadLimit', $uploadLimit);
        $script->set('type', SOCIAL_TYPE_USER);
        $script->set('uid', $this->my->id);

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
        // Only process videos
        if ($template->context_type != 'videos') {
            return;
        }

        // Determine the type of the video
        $data = array();
        $data['source'] = $this->input->get('videos_type', '', 'word');
        $data['title'] = $this->input->get('videos_title', '', 'default');
        $data['description'] = $this->input->get('videos_description', '', 'default');
        $data['category_id'] = $this->input->get('videos_category', 0, 'int');

        // We need to format the link first.
        $link = $this->input->get('videos_link', '', 'default');

        // Save options for the video library
        $saveOptions = array();

        // If this is a link source, we just load up a new video library
        if ($data['source'] == 'link') {
            $video = ES::video();
            $data['link'] = $video->format($link);
        }

        // If this is a video upload, the id should be provided because videos are created first.
        if ($data['source'] == 'upload') {
            $id = $this->input->get('videos_id', 0, 'int');

            $video = ES::video();
            $video->load($id);

            // Video library needs to know that we're storing this from the story
            $saveOptions['story'] = true;

            // We cannot publish the video if auto encoding is disabled
            if ($this->config->get('video.autoencode')) {
                $data['state'] = SOCIAL_VIDEO_PUBLISHED;
            }
        }

        $data['uid'] = $this->my->id;
        $data['type'] = SOCIAL_TYPE_USER;

        // Check if user is really allowed to upload videos
        if ($video->id && !$video->canEdit()) {
            return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_EDITING'));
        }

        // Try to save the video
        $state = $video->save($data, array(), $saveOptions);

        // We need to update the context
        $template->context_type = SOCIAL_TYPE_VIDEOS;
        $template->context_id = $video->id;
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
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function onAfterLikeDelete( &$likes )
    {
        if( !$likes->type )
        {
            return;
        }

        if ($likes->type == 'videos.user.create') {
             ES::points()->assign('video.unlike', 'com_easysocial', Foundry::user()->id);
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
        // @legacy
        // photos.user.add should just be photos.user.upload since they are pretty much the same
        $allowed = array('videos.user.create', 'videos.user.featured');

        if (!in_array($likes->type, $allowed)) {
            return;
        }

        if (in_array($likes->type, $allowed)) {

            // Get the actor of the likes
            $actor  = Foundry::user($likes->created_by);

            // Set the email options
            $emailOptions   = array(
                'template'      => 'apps/user/videos/like.video.item',
                'actor'         => $actor->getName(),
                'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
                'actorLink'     => $actor->getPermalink(true, true)
            );

            $systemOptions  = array(
                'context_type'  => $likes->type,
                'actor_id'      => $likes->created_by,
                'uid'           => $likes->uid,
                'aggregate'     => true
            );

            // Standard email subject
            $ownerTitle     = 'APP_USER_VIDEOS_EMAILS_LIKE_VIDEO_ITEM_SUBJECT';
            $involvedTitle  = 'APP_USER_VIDEOS_EMAILS_LIKE_VIDEO_INVOLVED_SUBJECT';

            // Load the video item
            $table = ES::table("Video");
            $table->load($likes->uid);

            $video  = ES::video($table);

            $systemOptions['context_ids'] = $video->id;

            // Get the permalink to the photo
            $emailOptions['permalink'] = $video->getPermalink(true);
            $systemOptions['url'] = $video->getPermalink(false);

            $element = 'videos';

            // For single photo items on the stream
            if ($likes->type == 'videos.user.create') {
                $verb = 'create';
            }

            if ($likes->type == 'videos.user.featured') {
                $verb = 'featured';
            }

            $emailOptions['title'] = $ownerTitle;

            // @points: photos.like
            // Assign points for the author for liking this item
            ES::points()->assign('video.like', 'com_easysocial', $likes->created_by);

            // Notify the owner of the photo first
            if ($likes->created_by != $video->user_id) {
                ES::notify('likes.item', array($video->user_id), $emailOptions, $systemOptions);
            }

            // Get additional recipients since photos has tag
            $additionalRecipients   = array();
            $this->getTagRecipients($additionalRecipients, $video);

            // Get a list of recipients to be notified for this stream item
            // We exclude the owner of the note and the actor of the like here
            $recipients     = $this->getStreamNotificationTargets($likes->uid, $element, 'user', $verb, $additionalRecipients, array($video->user_id, $likes->created_by));

            $emailOptions['title']      = $involvedTitle;
            $emailOptions['template']   = 'apps/user/videos/like.video.involved';

            // Notify other participating users
            Foundry::notify('likes.involved', $recipients, $emailOptions, $systemOptions);

            return;
        }

    }


    /**
     * Retrieves a list of tag recipients on a video
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    private function getTagRecipients(&$recipients, SocialVideo &$video, $exclusion = array())
    {
        return array();
    }

    /**
     * Renders the notification item
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function onNotificationLoad( SocialTableNotification &$item )
    {
        $allowed = array('comments.item', 'comments.involved', 'likes.item', 'likes.involved', 'video.tagged',
                            'likes.likes' , 'comments.comment.add' );

        if (!in_array($item->cmd, $allowed)) {
            return;
        }

        // When user likes a single photo
        $allowedContexts = array('videos.user.create', 'videos.user.featured');

        if (($item->cmd == 'comments.item' || $item->cmd == 'comments.involved') && in_array($item->context_type, $allowedContexts)) {
            $hook = $this->getHook('notification', 'comments');
            $hook->execute($item);

            return;
        }

        // When user likes a single photo
        $allowedContexts    = array('videos.user.create', 'videos.user.featured');
        if (($item->cmd == 'likes.item' || $item->cmd == 'likes.involved') && in_array($item->context_type, $allowedContexts)) {

            $hook   = $this->getHook('notification', 'likes');
            $hook->execute($item);

            return;
        }

        // When user is tagged in a photo
        if ($item->cmd == 'video.tagged' && $item->context_type == 'tagging') {

            $hook   = $this->getHook('notification', 'tagging');
            $hook->execute($item);
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
        $allowed = array('videos.user.create', 'videos.user.featured');

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
        $allowed = array('videos.user.create', 'videos.user.featured');

        if (!in_array($comment->element, $allowed)) {
            return;
        }

        // Get the actor of the likes
        $actor  = ES::user($comment->created_by);

        // Set the email options
        $emailOptions   = array(
            'template'      => 'apps/user/videos/comment.video.item',
            'actor'         => $actor->getName(),
            'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
            'actorLink'     => $actor->getPermalink(true, true),
            'comment'       => $comment->comment
        );

        $systemOptions  = array(
            'context_type'  => $comment->element,
            'context_ids'   => $comment->uid,
            'actor_id'      => $comment->created_by,
            'uid'           => $comment->id,
            'aggregate'     => true
        );

        // Standard email subject
        $ownerTitle     = 'APP_USER_VIDEOS_EMAILS_COMMENT_VIDEO_ITEM_SUBJECT';
        $involvedTitle  = 'APP_USER_VIDEOS_EMAILS_COMMENT_VIDEO_INVOLVED_SUBJECT';


        // For single video item on the stream
        $table = ES::table("Video");
        $table->load($comment->uid);

        $video = ES::video($table);

        // Get the permalink to the photo
        $emailOptions['permalink'] = $video->getPermalink(true);
        $systemOptions['url'] = $video->getPermalink(false);

        $element = 'videos';

        if ($comment->element == 'videos.user.create') {
            $verb = 'create';
        }

        if ($comment->element == 'videos.user.featured') {
            $verb = 'featured';
        }

        $emailOptions['title'] = $ownerTitle;

        // Assign points for the author for liking this item
        ES::points()->assign('video.comment.add', 'com_easysocial', $comment->created_by);

        // Notify the owner of the photo first
        if ($video->user_id != $comment->created_by) {
            ES::notify('comments.item', array($video->user_id), $emailOptions, $systemOptions);
        }

        // Get additional recipients since videos has tag
        $additionalRecipients   = array();
        $this->getTagRecipients($additionalRecipients, $video);

        // Get a list of recipients to be notified for this stream item
        // We exclude the owner of the note and the actor of the like here
        $recipients     = $this->getStreamNotificationTargets($comment->uid, $element, 'user', $verb, $additionalRecipients, array($video->user_id, $comment->created_by));

        $emailOptions['title']      = $involvedTitle;
        $emailOptions['template']   = 'apps/user/videos/comment.video.involved';

        // Notify other participating users
        ES::notify('comments.involved', $recipients, $emailOptions, $systemOptions);

        return;
    }

    /**
     * Sets the icon of the stream
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function setStreamIcon(SocialStreamItem &$stream)
    {
        // Decorate the stream item with the neccessary design
        $stream->color = '#5580BE';
        $stream->fonticon = 'ies-play';
        $stream->label = JText::_('COM_EASYSOCIAL_VIDEOS', true);
        $stream->display = SOCIAL_STREAM_DISPLAY_FULL;


        // If this is a cluster, we need to prepare accordingly.
        if ($stream->isCluster()) {
            $cluster = $stream->getCluster();

            if (!$cluster) {
                return false;
            }

            // Check if the user can really view items from a cluster
            if ($cluster->getType() == SOCIAL_TYPE_GROUP && !$cluster->isOpen() && !$cluster->isMember()) {
                return false;
            }

            if ($cluster->getType() == SOCIAL_TYPE_EVENT && !$cluster->isOpen() && !$cluster->getGuest()->isGuest()) {
                return false;
            }

            // If the cluster is not public, sharing should be disabled
            if (!$cluster->isOpen()) {
                $stream->sharing = false;
            }

            // We need a different label for group items
            if ($cluster->getType() == SOCIAL_TYPE_GROUP) {
                $stream->color = '#303229';
                $stream->fonticon = 'ies-users';
            }

            if ($cluster->getType() == SOCIAL_TYPE_EVENT) {
                $stream->color = '#f06050';
                $stream->fonticon = 'ies-calendar';
            }

            $stream->label = FD::_($cluster->getType(), true);
        }

        return true;
    }   
}
