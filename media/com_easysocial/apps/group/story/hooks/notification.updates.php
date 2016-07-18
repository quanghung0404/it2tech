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

class SocialGroupAppStoryHookNotificationUpdates
{
    /**
     * Processes comment notifications
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function execute(SocialTableNotification &$item)
    {
        // Get the group item
        $group      = FD::group($item->context_ids);

        // Get the actor
        $actor      = FD::user($item->actor_id);

        // Format the title
        if ($item->context_type == 'story.group.create') {
            $item->title    = JText::sprintf('APP_GROUP_STORY_USER_POSTED_IN_GROUP', $actor->getName(), $group->getName());
            $item->image    = $group->getAvatar();

            // Ensure that the content is properly formatted
            $item->content  = JString::substr(strip_tags($item->content), 0, 80) . JText::_('COM_EASYSOCIAL_ELLIPSES');

            return $item;
        }

        if ($item->context_type == 'links.group.create') {

            $model = FD::model('Stream');
            $links = $model->getAssets($item->uid, SOCIAL_TYPE_LINKS);

            if (!$links) {
                return;
            }

            $link = FD::makeObject($links[0]->data);

            // Retrieve the image cache path
            $stream = FD::stream();
            $streamItem = $stream->getItem($item->uid);

            if (!$streamItem) {
                $item->exclude = true;
                return;
            }

            $streamItem = $streamItem[0];

            if (!$streamItem) {
                return;
            }
            
            $assets = $streamItem->getAssets();
            
            if ($assets) {
                $assets = $assets[0];
            }
    
            $app = FD::table('App');
            $app->load(array('element' => 'links', 'group' => SOCIAL_TYPE_GROUP));

            $params = $app->getParams();

            $image = FD::links()->getImageLink($assets, $params);

            $item->image    = $image;
            $item->content  = $link->link;
            $item->title    = JText::sprintf('APP_GROUP_STORY_USER_SHARED_LINK_IN_GROUP', $actor->getName(), $group->getName());
        }

        // Someone shared a file in a group
        if ($item->context_type == 'file.group.uploaded') {

            // Get the file object
            $file   = FD::table('File');
            $file->load($item->context_ids);

            $group  = FD::group($item->uid);

            $item->title    = JText::sprintf('APP_GROUP_STORY_USER_SHARED_FILE_IN_GROUP', $actor->getName(), $group->getName());
            $item->content  = $file->name;

            if ($file->hasPreview()) {
                $item->image    = $file->getPreviewURI();
            }

            return;
        }


        // Someone shared a photo in a group
        if ($item->context_type == 'photos.group.share') {

            // Based on the stream id, we need to get the stream item id.
            $stream     = FD::table('Stream');
            $stream->load($item->uid);

            // Get child items
            $streamItems    = $stream->getItems();

            // Since we got all the child of stream, we can get the correct count
            $count          = count($streamItems);

            if ($count && $count == 1) {

                $photo      = FD::table('Photo');
                $photo->load($streamItems[0]->id);

                $item->title    = JText::sprintf('APP_GROUP_STORY_USER_SHARED_SINGLE_PHOTO_IN_GROUP', $actor->getName(), $group->getName());
                $item->image    = $photo->getSource();
                $item->content  = '';

                return;
            }

            $item->title    = JText::sprintf('APP_GROUP_STORY_USER_SHARED_MULTIPLE_PHOTOS_IN_GROUP', $actor->getName(), $count, $group->getName());
            $item->content  = '';

            return;
        }

        return $item;
    }
}
