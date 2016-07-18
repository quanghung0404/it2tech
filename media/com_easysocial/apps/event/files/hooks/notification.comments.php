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
defined('_JEXEC') or die('Unauthorized Access');

class SocialEventAppFilesHookNotificationComments
{
    public function execute(&$item)
    {
        // Get comment participants
        $model = FD::model('Comments');
        $users = $model->getParticipants($item->uid, $item->context_type);

        // Merge to include actor, diff to exclude self, unique to remove dups, and values to reset the index
        $users = array_values(array_unique(array_diff(array_merge($users, array($item->actor_id)), array(FD::user()->id))));

        // Convert the names to stream-ish
        $names = FD::string()->namesToNotifications($users);

        // When someone likes on the photo that you have uploaded in a event
        if ($item->context_type == 'files.event.uploaded') {

            $file = FD::table('File');
            $file->load($item->uid);

            // Get the event
            $event = FD::event($file->uid);

            // Set the content
            if ($file->hasPreview()) {
                $item->image = $file->getPreviewURI();
            }

            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if ($file->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $item->title = JText::sprintf(FD::string()->computeNoun('APP_EVENT_FILES_USER_COMMENTED_ON_YOUR_FILE', count($users)), $names);

                return $item;
            }

            // This is for 3rd party viewers
            $item->title = JText::sprintf(FD::string()->computeNoun('APP_EVENT_FILES_USER_COMMENTED_ON_USERS_FILE', count($users)), $names, FD::user($file->user_id)->getName());

            return;
        }

        return;
    }

}
