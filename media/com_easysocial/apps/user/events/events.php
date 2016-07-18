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

FD::import('admin:/includes/apps/apps');

/**
 * Events application for EasySocial.
 *
 * @since   1.3
 * @author  Mark Lee <mark@stackideas.com>
 */
class SocialUserAppEvents extends SocialAppItem
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
        $obj->color = '#f06050';
        $obj->icon = 'fa-calendar';
        $obj->label = 'APP_USER_EVENTS_STREAM_TOOLTIP';

        return $obj;
    }

    /**
     * Prepares the stream item.
     *
     * @since   1.3
     * @access  public
     */
    public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
    {
        // We only want to process related items
        if ($item->cluster_type !== SOCIAL_TYPE_EVENT || empty($item->cluster_id)) {
            return;
        }

        // If the event is a invite-only event, then we do not want the stream to show on user's stream.
        // The stream still has to be generated because the same stream item is displayed on both user and event's stream.

        $event = FD::event($item->cluster_id);

        // Only show Social sharing in public event
        if ($event->type != SOCIAL_EVENT_TYPE_PUBLIC) {
            $item->sharing = false;
        }

        // If the event is pending and is a new item, this means this event is created from the story form, and we want to show a message stating that the event is in pending
        if ($event->isPending() && !empty($item->isNew)) {
            $item->title = JText::_('APP_USER_EVENTS_STREAM_EVENT_PENDING_APPROVAL');
            $item->display = SOCIAL_STREAM_DISPLAY_MINI;
            return;
        }

        // If event is not published, we do not want to render the stream.
        if (!$event->isPublished()) {
            return;
        }

        if (!$event->isGroupEvent()) {
            if ($event->isInviteOnly() && !$event->getGuest()->isGuest()) {
                return;
            }
        }

        if (!in_array($item->context, array('events', 'guests', 'tasks', 'discussions'))) {
            return;
        }

        $item->display = SOCIAL_STREAM_DISPLAY_FULL;
        $item->color = '#f06050';
        $item->fonticon = 'fa fa-calendar';
        $item->label = FD::_('APP_USER_EVENTS_STREAM_TOOLTIP', true);

        if ($event->isGroupEvent()) {
            $item->label = FD::_('APP_USER_EVENTS_GROUP_EVENT_STREAM_TOOLTIP', true);
        }

        if ($item->context === 'events' || $item->context === 'guests') {
            // Context are split into events and guests
            // "events" context are stream items that are related to event item
            // "guests" context are stream items that are related to guests of the event

            // From events
            // stream_feature
            // stream_create
            // stream_update

            // From guests
            // stream_makeadmin
            // stream_going
            // stream_notgoing
            if (!$this->getParams()->get('stream_' . $item->verb, true)) {
                return;
            }

            // Event stream items should just be mini
            // $item->display = SOCIAL_STREAM_DISPLAY_MINI;

            // This goes to user/events/streams in accordance to verb
            // $this->processStream($item);

            $this->set('event', $event);
            $this->set('actor', $item->actor);

            if ($event->isGroupEvent()) {
                $this->set('group', $event->getGroup());
            }

            // streams/create.title
            // streams/feature.title
            // streams/makeadmin.title
            // streams/going.title
            // streams/notgoing.title
            // streams/update.title
            $item->title = parent::display('streams/events/' . $item->verb . '.title');
            $item->content = parent::display('streams/events/content');

            // APP_USER_EVENTS_STREAM_OPENGRAPH_CREATE
            // APP_USER_EVENTS_STREAM_OPENGRAPH_FEATURE
            // APP_USER_EVENTS_STREAM_OPENGRAPH_MAKEADMIN
            // APP_USER_EVENTS_STREAM_OPENGRAPH_UPDATE
            // APP_USER_EVENTS_STREAM_OPENGRAPH_GOING
            // APP_USER_EVENTS_STREAM_OPENGRAPH_NOTGOING
            // Append the opengraph tags
            $item->addOgDescription(JText::sprintf('APP_USER_EVENTS_STREAM_OPENGRAPH_' . strtoupper($item->verb), $item->actor->getName(), $event->getName()));

            return;
        }

        if ($item->context === 'discussions') {
            $this->processDiscussionStream($item, $includePrivacy);
            return;
        }

        if ($item->context === 'tasks') {
            $this->processTaskStream($item, $includePrivacy);
            return;
        }
    }

    private function processDiscussionStream(SocialStreamItem &$item, $includePrivacy)
    {
        $app = FD::table('App');
        $app->load(array('group' => SOCIAL_TYPE_EVENT, 'type' => SOCIAL_TYPE_APPS, 'element' => 'discussions'));

        $event = FD::event($item->cluster_id);

        $params = FD::registry($item->params);

        $discussion = FD::table('Discussion');
        $discussion->load($item->contextId);

        $permalink = FRoute::apps(array(
            'layout' => 'canvas',
            'customView' => 'item',
            'uid' => $event->getAlias(),
            'type' => SOCIAL_TYPE_EVENT,
            'id' => $app->getAlias(),
            'discussionId' => $discussion->id
        ));

        $this->set('actor', $item->actor);
        $this->set('permalink', $permalink);
        $this->set('discussion', $discussion);
        $this->set('event', $event);

        $files = $discussion->hasFiles();

        $this->set('files', $files);

        // Do not allow user to repost discussions
        $item->repost = false;

        $content = '';

        if ($item->verb === 'create') {

            $content = $this->formatContent($discussion);
            $this->set('content', $content);
        }

        if ($item->verb === 'reply' || $item->verb === 'answered') {
            $reply = FD::table('Discussion');
            $reply->load($params->get('reply')->id);

            $reply->author = FD::user($reply->created_by);

            $content = $this->formatContent($reply);

            $this->set('reply', $reply);

            $this->set('content', $content);
        }

        if ($item->verb === 'answered') {
            // We want it to be SOCIAL_STREAM_DISPLAY_MINI but we also want the accepted answer to show as well.
            // Hence we leave the display to full but we disable comments, likes, sharing and repost
            $item->comments = false;
            $item->likes = false;
            $item->sharing = false;
        }

        if ($item->verb === 'locked') {
            $item->display = SOCIAL_STREAM_DISPLAY_MINI;
        }

        $item->title = parent::display('streams/discussions/' . $item->verb . '.title');
        $item->content = parent::display('streams/discussions/' . $item->verb . '.content');

        // Append the opengraph tags
        $item->addOgDescription(JText::sprintf('APP_USER_EVENTS_STREAM_DISCUSSION_OPENGRAPH_' . strtoupper($item->verb), $item->actor->getName(), $event->getName()));
    }


    /**
     * Internal method to format the discussions
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    private function formatContent( $item )
    {
        // Get the app params so that we determine which stream should be appearing
        $app = $this->getApp();
        $params = $app->getParams();

        $content = $item->content;

        $content = FD::string()->parseBBCode( $content , array( 'code' => true , 'escape' => false ) );

        // Remove [file] from contents
        $content = $item->removeFiles( $content );

        $maxlength = $params->get('stream_discussion_maxlength', 250);
        if ($maxlength) {
            // lets do a simple content truncation here.
            $content = strip_tags($content);
            $content = strlen($content) > $maxlength ? JString::substr($content, 0, $maxlength ) . JText::_('COM_EASYSOCIAL_ELLIPSES') : $content ;
        }

        return $content;
    }

    private function processTaskStream(SocialStreamItem &$item, $includePrivacy)
    {
        $app = FD::table('App');
        $app->load(array('group' => SOCIAL_TYPE_EVENT, 'type' => SOCIAL_TYPE_APPS, 'element' => 'tasks'));

        $event = FD::event($item->cluster_id);

        $params = FD::registry($item->params);

        // Get the milestone
        $milestone = FD::table('Milestone');
        $milestone->bind($params->get('milestone'));

        $permalink = FRoute::apps(array(
            'layout' => 'canvas',
            'customView' => 'item',
            'uid' => $event->getAlias(),
            'type' => SOCIAL_TYPE_EVENT,
            'id' => $app->getAlias(),
            'milestoneId' => $milestone->id
        ));

        // Do not allow reposting on milestone items
        $item->repost = false;

        if ($item->verb == 'createTask') {
            $items = $params->get('tasks');
            $tasks = array();

            foreach ($items as $i) {
                $task = FD::table('Task');

                // We don't do bind here because we need to latest state from the database
                // THe cached params might be an old data.
                $task->load($i->id);

                $tasks[] = $task;
            }

            $this->set('tasks', $tasks);
            $this->set('total', count($tasks));
        }

        $this->set('event', $event);
        $this->set('stream', $item);

        $this->set('milestone', $milestone);
        $this->set('permalink', $permalink);

        $this->set('actor', $item->actor);

        // streams/tasks/createTask.title
        // streams/tasks/createTask.content
        // streams/tasks/createMilestone.title
        // streams/tasks/createMilestone.content

        $item->title = parent::display('streams/tasks/' . $item->verb . '.title');
        $item->content = parent::display('streams/tasks/' . $item->verb . '.content');

        if ($item->verb === 'createMilestone') {
            // Append the opengraph tags
            $item->addOgDescription(JText::sprintf('APP_USER_EVENTS_TASKS_STREAM_OPENGRAPH_CREATED_MILESTONE', $item->actor->getName(), $milestone->title, $event->getName()));
        }

        if ($item->verb === 'createTask') {
            // Append the opengraph tags
            $item->addOgDescription(JText::sprintf(FD::string()->computeNoun('APP_USER_EVENTS_TASKS_STREAM_OPENGRAPH_ADDED_TASK', count($tasks)), $item->actor->getName(), count($tasks), $milestone->title, $event->getName()));
        }
    }

    /**
     * Prepares what should appear on user's story form.
     *
     * @since  1.3
     * @access public
     */
    public function onPrepareStoryPanel($story)
    {
        // We only allow event creation on dashboard, which means if the story target and current logged in user is different, then we don't show this
        // Empty target is also allowed because it means no target.
        if (!empty($story->target) && $story->target != FD::user()->id) {
            return;
        }

        $params = $this->getParams();

        // Determine if we should attach ourselves here.
        if (!$params->get('story_event', true)) {
            return;
        }

        // Ensure that the user has access to create events
        if (!$this->my->getAccess()->get('events.create')) {
            return;
        }

        // Ensure that events is enabled
        if (!FD::config()->get('events.enabled')) {
            return;
        }

        // Create plugin object
        $plugin = $story->createPlugin('event', 'panel');

        // Get the theme class
        $theme = FD::themes();

        // Get the available event category
        $categories = FD::model('EventCategories')->getCreatableCategories(FD::user()->getProfile()->id);

        $theme->set('categories', $categories);

        $plugin->button->html = $theme->output('apps/user/events/story/panel.button');
        $plugin->content->html = $theme->output('apps/user/events/story/panel.content');

        $script = FD::get('Script');
        $plugin->script = $script->output('apps:/user/events/story');

        return $plugin;
    }

    public function onBeforeStorySave(&$template, &$stream, &$content)
    {
        $params = $this->getParams();

        // Determine if we should attach ourselves here.
        if (!$params->get('story_event', true)) {
            return;
        }

        $in = FD::input();

        $title = $in->getString('event_title');
        $description = $in->getString('event_description');
        $categoryid = $in->getInt('event_category');
        $start = $in->getString('event_start');
        $end = $in->getString('event_end');
        $timezone = $in->getString('event_timezone');

        // If no category id, then we don't proceed
        if (empty($categoryid)) {
            return;
        }

        // Perhaps in the future we use FD::model('Event')->createEvent() instead.
        // For now just hardcode it here to prevent field triggering and figuring out how to punch data into the respective field data because the form is not rendered through field trigger.

        $my = FD::user();

        $event = FD::event();

        $event->title = $title;

        $event->description = $description;

        // Set a default params for this event first
        $event->params = '{"photo":{"albums":true},"news":true,"discussions":true,"allownotgoingguest":false,"allowmaybe":true,"guestlimit":0}';

        $event->type = SOCIAL_EVENT_TYPE_PUBLIC;
        $event->creator_uid = $my->id;
        $event->creator_type = SOCIAL_TYPE_USER;
        $event->category_id = $categoryid;
        $event->cluster_type = SOCIAL_TYPE_EVENT;
        $event->alias = FD::model('Events')->getUniqueAlias($title);
        $event->created = FD::date()->toSql();
        $event->key = md5($event->created . $my->password . uniqid());

        $event->state = SOCIAL_CLUSTER_PENDING;

        if ($my->isSiteAdmin() || !$my->getAccess()->get('events.moderate')) {
            $event->state = SOCIAL_CLUSTER_PUBLISHED;
        }

        // Trigger apps
        FD::apps()->load(SOCIAL_TYPE_USER);

        $dispatcher  = FD::dispatcher();
        $triggerArgs = array(&$event, &$my, true);

        // @trigger: onEventBeforeSave
        $dispatcher->trigger(SOCIAL_TYPE_USER, 'onEventBeforeSave', $triggerArgs);

        $state = $event->save();

        // Notifies admin when a new event is created
        if ($event->state === SOCIAL_CLUSTER_PENDING || !$my->isSiteAdmin()) {
            FD::model('Events')->notifyAdmins($event);
        }

        // Set the meta for start end timezone
        $meta = $event->meta;
        $meta->cluster_id = $event->id;
        $meta->start = FD::date($start)->toSql();
        $meta->end = FD::date($end)->toSql();
        $meta->timezone = $timezone;

        $meta->store();

        // Recreate the event object
        $event = FD::event($event->id);

        // Create a new owner object
        $event->createOwner($my->id);

        // @trigger: onEventAfterSave
        $triggerArgs = array(&$event, &$my, true);
        $dispatcher->trigger(SOCIAL_TYPE_USER, 'onEventAfterSave' , $triggerArgs);

        // Due to inconsistency, we don't use SOCIAL_TYPE_EVENT.
        // Instead we use "events" because app elements are named with 's', namely users, groups, events.
        $template->context_type = 'events';

        $template->context_id = $event->id;
        $template->cluster_access = $event->type;
        $template->cluster_type = $event->cluster_type;
        $template->cluster_id = $event->id;

        $params = array(
            'event' => $event
        );

        $template->setParams(FD::json()->encode($params));
    }

    public function onAfterLikeSave($likes)
    {
        $segments = explode('.', $likes->type);

        if (count($segments) !== 3 || $segments[1] !== SOCIAL_TYPE_EVENT) {
            return;
        }

        list($element, $group, $verb) = explode('.', $likes->type);

        // Get the actor
        $actor = FD::user($likes->created_by);

        if ($element === 'events') {
            // Verbs
            // feature
            // create
            // update

            $event = FD::event($likes->uid);

            $stream = FD::table('Stream');
            $stream->load($likes->stream_id);

            $owner = FD::user($stream->actor_id);

            // APP_USER_EVENTS_EMAILS_FEATURE_LIKE_ITEM_SUBJECT
            // APP_USER_EVENTS_EMAILS_CREATE_LIKE_ITEM_SUBJECT
            // APP_USER_EVENTS_EMAILS_UPDATE_LIKE_ITEM_SUBJECT
            // APP_USER_EVENTS_EMAILS_FEATURE_LIKE_INVOLVED_SUBJECT
            // APP_USER_EVENTS_EMAILS_CREATE_LIKE_INVOLVED_SUBJECT
            // APP_USER_EVENTS_EMAILS_UPDATE_LIKE_INVOLVED_SUBJECT

            // apps/user/events/feature.like.item
            // apps/user/events/create.like.item
            // apps/user/events/update.like.item
            // apps/user/events/feature.like.involved
            // apps/user/events/create.like.involved
            // apps/user/events/update.like.involved

            $emailOptions = array(
                'title' => 'APP_USER_EVENTS_EMAILS_' . strtoupper($verb) . '_LIKE_ITEM_SUBJECT',
                'template' => 'apps/user/events/' . $verb . '.like.item',
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

            $emailOptions['title'] = 'APP_USER_EVENTS_EMAILS_' . strtoupper($verb) . '_LIKE_INVOLVED_SUBJECT';
            $emailOptions['template'] = 'apps/user/events/' . $verb . '.like.involved';

            // Notify other participating users
            FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);
        }

        if ($element === 'guests') {
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

        if ($element === 'discussions') {

            // Uses app/event/discussions onAfterLikeSave logic and language strings since it is the same

            // Get the discussion object since it's tied to the stream
            $discussion = FD::table('Discussion');
            $discussion->load($likes->uid);

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

        if ($element === 'tasks') {
            // Uses app/event/tasks onAfterLikeSave logic and language strings since it is the same

            $identifier = $verb == 'createMilestone' ? 'milestone' : 'task';

            // Get the milestone/task table
            $table = FD::table($identifier);
            $table->load($likes->uid);

            // Get the owner
            $owner = FD::user($table->owner_id);

            // Get the event
            $event = FD::event($table->uid);

            $recipients = $this->getStreamNotificationTargets($likes->uid, $element, $group, $verb, array(), array($owner->id, $likes->created_by));

            // APP_EVENT_TASKS_EMAILS_LIKE_YOUR_MILESTONE_SUBJECT
            // APP_EVENT_TASKS_EMAILS_LIKE_YOUR_TASK_SUBJECT
            // APP_EVENT_TASKS_EMAILS_LIKE_A_MILESTONE_SUBJECT
            // APP_EVENT_TASKS_EMAILS_LIKE_A_TASK_SUBJECT

            // apps/event/tasks/like.milestone
            // apps/event/tasks/like.task
            // apps/event/tasks/like.milestone.involved
            // apps/event/tasks/like.task.involved

            $emailOptions = array(
                'title' => 'APP_EVENT_TASKS_EMAILS_LIKE_YOUR_' . strtoupper($identifier) . '_SUBJECT',
                'template' => 'apps/event/tasks/like.' . $identifier,
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
            // We exclude the owner of the note and the actor of the like here
            $recipients = $this->getStreamNotificationTargets($likes->uid, $element, $group, $verb, array(), array($owner->id, $likes->created_by));

            $emailOptions['title'] = 'APP_EVENT_TASKS_EMAILS_LIKE_A_' . strtoupper($identifier) . '_SUBJECT';
            $emailOptions['template'] = 'apps/event/tasks/like.' . $identifier . '.involved';

            // Notify other participating users
            FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);
        }
    }

    public function onAfterCommentSave($comment)
    {
        $segments = explode('.', $comment->element);

        if (count($segments) !== 3 || $segments[1] !== SOCIAL_TYPE_EVENT) {
            return;
        }

        list($element, $group, $verb) = explode('.', $comment->element);

        // Get the actor
        $actor = FD::user($comment->created_by);

        if ($element === 'events') {
            $event = FD::event($comment->uid);

            $stream = FD::table('Stream');
            $stream->load($comment->stream_id);

            $owner = FD::user($stream->actor_id);

            // APP_USER_EVENTS_EMAILS_FEATURE_COMMENT_ITEM_SUBJECT
            // APP_USER_EVENTS_EMAILS_CREATE_COMMENT_ITEM_SUBJECT
            // APP_USER_EVENTS_EMAILS_UPDATE_COMMENT_ITEM_SUBJECT
            // APP_USER_EVENTS_EMAILS_FEATURE_COMMENT_INVOLVED_SUBJECT
            // APP_USER_EVENTS_EMAILS_CREATE_COMMENT_INVOLVED_SUBJECT
            // APP_USER_EVENTS_EMAILS_UPDATE_COMMENT_INVOLVED_SUBJECT

            // apps/user/events/feature.comment.item
            // apps/user/events/create.comment.item
            // apps/user/events/update.comment.item
            // apps/user/events/feature.comment.involved
            // apps/user/events/create.comment.involved
            // apps/user/events/update.comment.involved

            $emailOptions = array(
                'title' => 'APP_USER_EVENTS_EMAILS_' . strtoupper($verb) . '_COMMENT_ITEM_SUBJECT',
                'template' => 'apps/user/events/' . $verb . '.comment.item',
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

             $emailOptions['title'] = 'APP_USER_EVENTS_EMAILS_' . strtoupper($verb) . '_COMMENT_INVOLVED_SUBJECT';
             $emailOptions['template'] = 'apps/user/events/' . $verb . '.comment.involved';

             // Notify other participating users
             FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
        }

        if ($element === 'guests') {
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

        if ($element === 'discussions') {

            // Uses app/event/discussions onAfterCommentSave logic and language strings since it is the same

            $stream = FD::table('Stream');
            $stream->load($comment->stream_id);

            // Get the discussion object since it's tied to the stream
            $discussion = FD::table('Discussion');
            $discussion->load($comment->uid);

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

        if ($element === 'tasks') {
            // Uses app/event/tasks onAfterCommentSave logic and language strings since it is the same

            $identifier = $verb == 'createMilestone' ? 'milestone' : 'task';

            // Get the milestone/task table
            $table = FD::table($identifier);
            $table->load($comment->uid);

            // Get the owner
            $owner = FD::user($table->owner_id);

            // Get the event
            $event = FD::event($table->uid);

            $recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb, array(), array($owner->id, $comment->created_by));

            // APP_EVENT_TASKS_EMAILS_COMMENTED_ON_YOUR_MILESTONE_SUBJECT
            // APP_EVENT_TASKS_EMAILS_COMMENTED_ON_YOUR_TASK_SUBJECT
            // APP_EVENT_TASKS_EMAILS_COMMENTED_ON_A_MILESTONE_SUBJECT
            // APP_EVENT_TASKS_EMAILS_COMMENTED_ON_A_TASK_SUBJECT

            // apps/event/tasks/comment.milestone
            // apps/event/tasks/comment.task
            // apps/event/tasks/comment.milestone.involved
            // apps/event/tasks/comment.task.involved

            $emailOptions = array(
                'title' => 'APP_EVENT_TASKS_EMAILS_COMMENTED_ON_YOUR_' . strtoupper($identifier) . '_SUBJECT',
                'template' => 'apps/event/tasks/comment.' . $identifier,
                'permalink' => FRoute::stream(array('layout' => 'item', 'id' => $comment->stream_id, 'external' => true)),
                'actor' => $actor->getName(),
                'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
                'actorLink' => $actor->getPermalink(true, true),
                'comment' => $comment->comment
            );

            $systemOptions = array(
                'context_type' => $comment->element,
                'content' => $comment->element,
                'url' => FRoute::stream(array('layout' => 'item', 'id' => $comment->stream_id, 'sef' => false)),
                'actor_id' => $comment->created_by,
                'uid' => $comment->uid,
                'aggregate' => true
            );

            // Notify the owner first
            if ($comment->created_by != $owner->id) {
                FD::notify('comments.item', array($owner->id), $emailOptions, $systemOptions);
            }

            // Get a list of recipients to be notified for this stream item
            // We exclude the owner of the note and the actor of the like here
            $recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb, array(), array($owner->id, $comment->created_by));

            $emailOptions['title'] = 'APP_EVENT_TASKS_EMAILS_COMMENTED_ON_A_' . strtoupper($identifier) . '_SUBJECT';
            $emailOptions['template'] = 'apps/event/tasks/comment.' . $identifier . '.involved';

            // Notify other participating users
            FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
        }
    }

    public function onBeforeGetStream(array &$options, $view = '')
    {
        if ($view != 'dashboard') {
            return;
        }

        $allowedContext = array('events','story','photos', 'tasks', 'discussions', 'guests');

        if (is_array($options['context']) && in_array('events', $options['context'])){
            // we need to make sure the stream return only cluster stream.
            $options['clusterType'] = SOCIAL_TYPE_EVENT;
        } else if ($options['context'] === 'events') {
            $options['context']     = $allowedContext;
            $options['clusterType'] = SOCIAL_TYPE_EVENT;
        }
    }

    public function onStreamVerbExclude(&$exclude)
    {
        $params = $this->getParams();

        $excludeVerb = array();

        // From events
        // stream_feature
        // stream_create
        // stream_update

        if (!$params->get('stream_feature', true)) {
            $excludeVerb[] = 'feature';
        }

        if (!$params->get('stream_create', true)) {
            $excludeVerb[] = 'create';
        }

        if (!$params->get('stream_update', true)) {
            $excludeVerb[] = 'update';
        }

        if (!empty($excludeVerb)) {
            $exclude['events'] = $excludeVerb;
        }

        $excludeVerb = array();

        // From guests
        // stream_makeadmin
        // stream_going
        // stream_notgoing

        if (!$params->get('stream_makeadmin', true)) {
            $excludeVerb[] = 'makeadmin';
        }

        if (!$params->get('stream_going', true)) {
            $excludeVerb[] = 'going';
        }

        if (!$params->get('stream_notgoing', true)) {
            $excludeVerb[] = 'notgoing';
        }

        if (!empty($excludeVerb)) {
            $exclude['guests'] = $excludeVerb;
        }
    }
}
