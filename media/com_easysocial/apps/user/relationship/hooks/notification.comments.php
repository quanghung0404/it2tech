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

class SocialUserAppRelationshipHookNotificationComments
{
    public function execute($item)
    {
        $contexts = explode('.', $item->context_type);

        $streamItem = FD::table('StreamItem');
        $streamItem->load(array('context_id' => $item->uid, 'context_type' => $contexts[0], 'verb' => $contexts[2]));

        $stream = FD::table('Stream');
        $stream->load($streamItem->uid);

        // Get likes participants
        $model = FD::model('Comments');
        $users = $model->getParticipants($item->uid, $item->context_type);

        // Include the notification actor
        $users[] = $item->actor_id;

        $users = array_values(array_unique(array_diff($users, array(FD::user()->id))));

        // Convert the names to stream-ish
        $names  = FD::string()->namesToNotifications($users);

        $plurality = count($users) > 1 ? '_PLURAL' : '_SINGULAR';

        if ($stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
            $item->title = JText::sprintf('APP_USER_RELATIONSHIP_USER_COMMENTED_ON_YOUR_RELATIONSHIP_STATUS' . $plurality, $names);

            return $item;
        }

        if ($stream->actor_id == $item->actor_id && count($users) == 1) {
            $item->title = JText::sprintf('APP_USER_RELATIONSHIP_OWNER_COMMENTED_ON_RELATIONSHIP_STATUS' . FD::user($stream->actor_id)->getGenderLang(), $names);

            return $item;
        }

        $item->title = JText::sprintf('APP_USER_RELATIONSHIP_USER_COMMENTED_ON_USER_RELATIONSHIP_STATUS' . $plurality, $names, FD::user($stream->actor_id)->getName());

        return $item;
    }
}
