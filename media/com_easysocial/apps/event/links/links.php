<?php
/**
* @package        EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

FD::import('apps:/event/links/helper');

class SocialEventAppLinks extends SocialAppItem
{
    /**
     * Responsible to return the favicon object
     *
     * @since   1.2
     * @access  public
     * @return
     */
    public function getFavIcon()
    {
        $obj = new stdClass();
        $obj->color = '#5580BE';
        $obj->icon = 'fa-link';
        $obj->label = 'APP_EVENT_LINKS_STREAM_TOOLTIP';

        return $obj;
    }

    /**
     * Fixed legacy issues where the app is displayed on apps list of a event.
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function appListing($view, $id, $type)
    {
        return false;
    }

    /**
     * Processes a saved story.
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function onAfterStorySave(&$stream, &$streamItem, &$template)
    {
        // Get the link information from the request
        $link = JRequest::getVar('links_url', '');
        $title = JRequest::getVar('links_title', '');
        $content = JRequest::getVar('links_description', '');
        $image = JRequest::getVar('links_image', '');
        $video = JRequest::getVar('links_video', '');

        // If there's no data, we don't need to store in the assets table.
        if (empty($title) && empty($content) && empty($image)) {
            return;
        }

        $registry = FD::registry();
        $registry->set('title', $title);
        $registry->set('content', $content);
        $registry->set('image', $image);
        $registry->set('link', $link);

        return true;
    }

    /**
     * Generates the stream title of event.
     *
     * @since    1.0
     * @access    public
     * @param    object    $params        A standard object with key / value binding.
     *
     * @return    none
     */
    public function onPrepareStream(SocialStreamItem &$stream, $includePrivacy = true)
    {
        if ($stream->context != 'links') {
            return;
        }

        // event access checking
        $event    = FD::event($stream->cluster_id);

        if (!$event || !$event->canViewItem()) {
            return;
        }

        //get links object, in this case, is the stream_item
        $uid = $stream->uid;

        $stream->color = '#5580BE';
        $stream->fonticon = 'fa fa-link';
        $stream->label = FD::_('APP_EVENT_LINKS_STREAM_TOOLTIP', true);

        // Apply likes on the stream
        $likes = FD::likes();
        $likes->get($stream->uid, $stream->context, $stream->verb, SOCIAL_APPS_GROUP_EVENT, $stream->uid);
        $stream->likes = $likes;

        // Apply comments on the stream
        $comments = FD::comments($stream->uid, $stream->context, $stream->verb, SOCIAL_APPS_GROUP_EVENT, array('url' => FRoute::stream(array('layout' => 'item', 'id' => $stream->uid))), $stream->uid);
        $stream->comments = $comments;

        // Apply repost on the stream
        $stream->repost = FD::get('Repost', $stream->uid, SOCIAL_TYPE_STREAM, SOCIAL_APPS_GROUP_EVENT);

        $my = FD::user();
        $privacy = FD::privacy($my->id);

        if ($includePrivacy && !$privacy->validate('story.view', $uid, SOCIAL_TYPE_LINKS, $stream->actor->id)) {
            return;
        }

        $actor = $stream->actor;
        $target = count($stream->targets) > 0 ? $stream->targets[0] : '';

        $stream->display = SOCIAL_STREAM_DISPLAY_FULL;

        $assets = $stream->getAssets();

        if (empty($assets)) {
            return;
        }

        $assets = $assets[0];
        $videoHtml = '';

        // Retrieve the link that is stored.
        $hash = md5($assets->get('link'));

        $link = FD::table('Link');
        $link->load(array('hash' => $hash));

        $linkObj = FD::json()->decode($link->data);

        // Determine if there's any embedded object
        $oembed = isset($linkObj->oembed) ? $linkObj->oembed : '';

        // Get app params
        $params = $this->getParams();

        // Need to use this function when use image cache link feature
        $image = FD::links()->getImageLink($assets, $params);

        $this->set('image', $image);
        $this->set('event', $event);
        $this->set('params', $params);
        $this->set('oembed', $oembed);
        $this->set('assets', $assets);
        $this->set('actor', $actor);
        $this->set('target', $target);
        $this->set('stream', $stream);

        $stream->title = parent::display('streams/title.' . $stream->verb);
        $stream->preview = parent::display('streams/preview.' . $stream->verb);

        return true;
    }


    /**
     * Responsible to generate the activity logs.
     *
     * @since    1.0
     * @access    public
     * @param    object    $params        A standard object with key / value binding.
     *
     * @return    none
     */
    public function onPrepareActivityLog(SocialStreamItem &$item, $includePrivacy = true)
    {
        if ($item->context != 'links') {
            return;
        }

        //get story object, in this case, is the stream_item
        $tbl = FD::table('StreamItem');
        $tbl->load($item->uid); // item->uid is now streamitem.id

        $uid = $tbl->uid;

        //get story object, in this case, is the stream_item
        $my = FD::user();
        $privacy = FD::privacy($my->id);

        $actor = $item->actor;
        $target = count($item->targets) > 0 ? $item->targets[0] : '';

        $assets = $item->getAssets($uid);

        if (empty($assets)) {
            return;
        }

        $assets = $assets[ 0 ];

        $this->set('assets', $assets);
        $this->set('actor', $actor);
        $this->set('target', $target);
        $this->set('stream', $item);


        $item->display = SOCIAL_STREAM_DISPLAY_MINI;
        $item->title = parent::display('logs/' . $item->verb);

        return true;

    }

    /**
     * Prepares what should appear in the story form.
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function onPrepareStoryPanel($story)
    {
        $params = $this->getParams();

        // Determine if we should attach ourselves here.
        if (!$params->get('story_links', true)) {
            return;
        }

        // Create plugin object
        $plugin = $story->createPlugin('links', 'panel');

        // We need to attach the button to the story panel
        $theme = ES::themes();

        $button = $theme->output('site/links/story/button');
        $form = $theme->output('site/links/story/form');

        // Attach the scripts
        $script = ES::script();
        $scriptFile = $script->output('site/links/story/plugin');

        $plugin->setHtml($button, $form);
        $plugin->setScript($scriptFile);

        return $plugin;
    }
}
