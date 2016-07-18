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

class SocialEventAppGuests extends SocialAppItem
{
    public function getFavIcon()
    {
        $obj = new stdClass();
        $obj->color = '#6bccb4';
        $obj->icon = 'fa-user';
        $obj->label = 'APP_EVENT_GUESTS_STREAM_TOOLTIP';

        return $obj;
    }

    public function onNotificationLoad(SocialTableNotification &$item)
    {
        $allowed = array(
            'likes.item',
            'likes.involved',
            'comments.item',
            'comments.involved'
        );

        // Only guests.event.going, guests.event.notgoing, guests.event.makeadmin has stream item
        if (in_array($item->cmd, $allowed) && in_array($item->context_type, array('guests.event.going', 'guests.event.notgoing', 'guests.event.makeadmin'))) {
            $hook = $this->getHook('notification', $item->type);

            return $hook->execute($item);
        }

        $allowed = array(
            'events.guest.makeadmin',
            'events.guest.revokeadmin',
            'events.guest.reject',
            'events.guest.approve',
            'events.guest.remove',
            'events.guest.going',
            'events.guest.maybe',
            'events.guest.notgoing',
            'events.guest.request',
            'events.guest.withdraw',
            'events.guest.invited'
        );

        if (in_array($item->cmd, $allowed)) {
            $hook = $this->getHook('notification', 'guest');

            return $hook->execute($item);
        }
    }

    public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
    {
        // We only want to process related items
        if ($item->cluster_type !== SOCIAL_TYPE_EVENT || empty($item->cluster_id)) {
            return;
        }

        // Context are split into events and nodes
        // "events" context are stream items that are related to event item
        // "guests" context are stream items that are related to guests of the event
        // Only process "guests" context here
        // "events" context are processed in the app/event/events app

        if ($item->context !== 'guests') {
            return;
        }

        $event = FD::event($item->cluster_id);

        if (!$event->canViewItem()) {
            return;
        }

        if (!$this->getParams()->get('stream_' . $item->verb, true)) {
            return;
        }

        $item->display = SOCIAL_STREAM_DISPLAY_FULL;

        $item->color = '#6bccb4';
        $item->fonticon = 'fa fa-user';
        $item->label = FD::_('APP_EVENT_GUESTS_STREAM_TOOLTIP', true);

        $actor = $item->actor;

        $this->set('event', $event);
        $this->set('actor', $actor);

        // streams/going.title
        // streams/makeadmin.title
        // streams/notgoing.title
        $item->title = parent::display('streams/' . $item->verb . '.title');
        $item->content = '';

        // APP_EVENT_GUESTS_STREAM_OPENGRAPH_GOING
        // APP_EVENT_GUESTS_STREAM_OPENGRAPH_NOTGOING
        // APP_EVENT_GUESTS_STREAM_OPENGRAPH_MAKEADMIN
        // Append the opengraph tags
        $item->addOgDescription(JText::sprintf('APP_EVENT_GUESTS_STREAM_OPENGRAPH_' . strtoupper($item->verb), $actor->getName(), $event->getName()));
    }

    public function onAfterLikeSave($likes)
    {
        $segments = explode('.', $likes->type);

        if (count($segments) !== 3 || $segments[1] !== SOCIAL_TYPE_EVENT) {
            return;
        }

        list($element, $group, $verb) = explode('.', $likes->type);

        if ($element !== 'guests') {
            return;
        }

        // Get the actor
        $actor = FD::user($likes->created_by);

        // Verbs
        // makeadmin
        // going
        // notgoing

        $guest = FD::table('EventGuest');
        $guest->load($likes->uid);

        $event = FD::event($guest->cluster_id);

        $stream = FD::table('Stream');
        $stream->load($likes->stream_id);

        $owner = FD::user($stream->actor_id);

        // APP_USER_EVENTS_GUESTS_EMAILS_MAKEADMIN_LIKE_ITEM_SUBJECT
        // APP_USER_EVENTS_GUESTS_EMAILS_GOING_LIKE_ITEM_SUBJECT
        // APP_USER_EVENTS_GUESTS_EMAILS_NOTGOING_LIKE_ITEM_SUBJECT
        // APP_USER_EVENTS_GUESTS_EMAILS_MAKEADMIN_LIKE_INVOLVED_SUBJECT
        // APP_USER_EVENTS_GUESTS_EMAILS_GOING_LIKE_INVOLVED_SUBJECT
        // APP_USER_EVENTS_GUESTS_EMAILS_NOTGOING_LIKE_INVOLVED_SUBJECT

        // apps/user/events/guest.makeadmin.like.item
        // apps/user/events/guest.going.like.item
        // apps/user/events/guest.notgoing.like.item
        // apps/user/events/guest.makeadmin.like.involved
        // apps/user/events/guest.going.like.involved
        // apps/user/events/guest.notgoing.like.involved

        $emailOptions = array(
            'title' => 'APP_USER_EVENTS_GUESTS_EMAILS_' . strtoupper($verb) . '_LIKE_ITEM_SUBJECT',
            'template' => 'apps/user/events/guest.' . $verb . '.like.item',
            'permalink' => FRoute::stream(array('layout' => 'item', 'id' => $likes->stream_id, 'external' => true)),
            'actor' => $actor->getName(),
            'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
            'actorLink' => $actor->getPermalink(true, true)
        );

        $systemOptions = array(
            'context_type' => $likes->type,
            'url' => FRoute::stream(array('layout' => 'item', 'id' => $likes->stream_id, 'sef' => false)),
            'actor_id' => $likes->created_by,
            'uid' => $likes->uid,
            'aggregate' => true
        );

        // Notify the owner first
        if ($likes->created_by != $owner->id) {
            FD::notify('likes.item', array($owner->id), $emailOptions, $systemOptions);
        }

        // Get a list of recipients to be notified for this stream item
        // We exclude the guest and the actor of the like here
        $recipients = $this->getStreamNotificationTargets($likes->uid, $element, $group, $verb, array(), array($owner->id, $likes->created_by));

        $emailOptions['title'] = 'APP_USER_EVENTS_GUESTS_EMAILS_' . strtoupper($verb) . '_LIKE_INVOLVED_SUBJECT';
        $emailOptions['template'] = 'apps/user/events/guest.' . $verb . '.like.involved';

        // Notify other participating users
        FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);
    }

    public function onAfterCommentSave($comment)
    {
        $segments = explode('.', $comment->element);

        if (count($segments) !== 3 || $segments[1] !== SOCIAL_TYPE_EVENT) {
            return;
        }

        list($element, $group, $verb) = explode('.', $comment->element);

        if ($element !== 'guests') {
            return;
        }

        // Get the actor
        $actor = FD::user($comment->created_by);

        $guest = FD::table('EventGuest');
        $guest->load($comment->uid);

        $event = FD::event($guest->cluster_id);

        $stream = FD::table('Stream');
        $stream->load($comment->stream_id);

        $owner = FD::user($stream->actor_id);

        // APP_USER_EVENTS_GUESTS_EMAILS_MAKEADMIN_COMMENT_ITEM_SUBJECT
        // APP_USER_EVENTS_GUESTS_EMAILS_GOING_COMMENT_ITEM_SUBJECT
        // APP_USER_EVENTS_GUESTS_EMAILS_NOTGOING_COMMENT_ITEM_SUBJECT
        // APP_USER_EVENTS_GUESTS_EMAILS_MAKEADMIN_COMMENT_INVOLVED_SUBJECT
        // APP_USER_EVENTS_GUESTS_EMAILS_GOING_COMMENT_INVOLVED_SUBJECT
        // APP_USER_EVENTS_GUESTS_EMAILS_NOTGOING_COMMENT_INVOLVED_SUBJECT

        // apps/user/events/guest.makeadmin.comment.item
        // apps/user/events/guest.going.comment.item
        // apps/user/events/guest.notgoing.comment.item
        // apps/user/events/guest.makeadmin.comment.involved
        // apps/user/events/guest.going.comment.involved
        // apps/user/events/guest.notgoing.comment.involved

        $emailOptions = array(
            'title' => 'APP_USER_EVENTS_GUESTS_EMAILS_' . strtoupper($verb) . '_COMMENT_ITEM_SUBJECT',
            'template' => 'apps/user/events/guest.' . $verb . '.comment.item',
            'permalink' => $stream->getPermalink(true, true),
            'actor' => $actor->getName(),
            'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
            'actorLink' => $actor->getPermalink(true, true),
            'comment' => $comment->comment
        );

        $systemOptions  = array(
            'context_type' => $comment->element,
            'content' => $comment->comment,
            'url' => $stream->getPermalink(false, false, false),
            'actor_id' => $comment->created_by,
            'uid' => $comment->uid,
            'aggregate' => true
        );

         // Notify the owner first
        if ($comment->created_by != $owner->id) {
            FD::notify('comments.item', array($owner->id), $emailOptions, $systemOptions);
         }

         // Get a list of recipients to be notified for this stream item
         // We exclude the owner of the discussion and the actor of the comment here
        $recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb, array(), array($owner->id, $comment->created_by));

        $emailOptions['title'] = 'APP_USER_EVENTS_GUESTS_EMAILS_' . strtoupper($verb) . '_COMMENT_INVOLVED_SUBJECT';
        $emailOptions['template'] = 'apps/user/events/guest.' . $verb . '.comment.involved';

        // Notify other participating users
        FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
    }

    public function appListing($view, $eventId, $type)
    {
        return true;
    }
}
