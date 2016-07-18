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
 * Relationship application for EasySocial
 *
 * @since   1.2
 * @author  Jason Rey <jasonrey@stackideas.com>
 */
class SocialUserAppRelationship extends SocialAppItem
{
    /**
     * Responsible to return the favicon object
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function getFavIcon()
    {
        $obj            = new stdClass();
        $obj->color     = '#DC554F';
        $obj->icon      = 'fa fa-heart';
        $obj->label     = 'APP_USER_RELATIONSHIP_STREAM_TOOLTIP';

        return $obj;
    }

    public function onPrepareActivityLog(SocialStreamItem &$stream, $includePrivacy = true)
    {
        if ($stream->context != 'relationship') {
            return ;
        }

        $params = $this->getParams();
        if (!$params->get('stream_approve', true)) {
            return;
        }

        $registry = FD::registry($stream->params);

        $this->set('type', $registry->get('type'));
        $this->set('actor', $stream->actor);
        $this->set('target', $stream->targets[0]);

        $stream->title = parent::display('streams/' . $stream->verb . '.title');

        if ($includePrivacy) {
            $my = FD::user();
            $privacy = FD::privacy($my->id);
            $stream->privacy = $privacy->form($stream->contextId, 'relationship', $stream->actor->id, 'core.view', false, $stream->aggregatedItems[0]->uid);
        }

        return true;
    }

    public function onPrepareStream(SocialStreamItem &$stream, $includePrivacy = true)
    {
        if ($stream->context != 'relationship') {
            return;
        }

        $params = $this->getParams();

        if (!$params->get('stream_approve', true)) {
            return;
        }

        // Get the actor
        $actor          = $stream->actor;

        // check if the actor is ESAD profile or not, if yes, we skip the rendering.
        if (! $actor->hasCommunityAccess()) {
            $stream->title = '';
            return;
        }

        $my = FD::user();
        $privacy = FD::privacy($my->id);

        if ($includePrivacy && !$privacy->validate('core.view', $stream->contextId, 'relationship', $stream->actor->id)) {
            return;
        }

        $stream->color = '#DC554F';
        $stream->fonticon = 'fa fa-heart';
        $stream->label = FD::_('APP_USER_RELATIONSHIP_STREAM_TOOLTIP', true);
        $stream->display = SOCIAL_STREAM_DISPLAY_FULL;

        $registry = FD::registry($stream->params);

        $this->set('type', $registry->get('type'));
        $this->set('actor', $stream->actor);
        $this->set('target', $stream->targets[0]);

        $stream->title = parent::display('streams/' . $stream->verb . '.title');

        if ($includePrivacy) {
            $stream->privacy = $privacy->form($stream->contextId, 'relationship', $stream->actor->id, 'core.view', false, $stream->uid);
        }

        return true;
    }

    public function onNotificationLoad(&$item)
    {
        $action = '';

        // For relationship request, approve, deny
        if ($item->type == 'relationship') {
            // $contexts = explode('.', $item->context_type);

            // $action = !empty($contexts[3]) ? $contexts[3] : '';
            $item->title = JText::_($item->title);

            return;
        }

        // Likes and comments notification from the generated stream after relationship approval
        if (in_array($item->type, array('likes', 'comments')) && $item->context_type == 'relationship.user.approve') {
            $action = $item->type;
        }

        if (!empty($action)) {
            $hook = $this->getHook('notification', $action);
            return $hook->execute($item);
        }
    }

    public function onAfterCommentSave(&$comment)
    {
        if ($comment->element != 'relationship.user.approve') {
            return;
        }

        $table = $this->getTableObject($comment->uid);

        if (!$table) {
            return;
        }

        $streamItem = FD::table('streamitem');
        $streamItem->load(array('context_type' => 'relationship', 'context_id' => $comment->uid, 'verb' => 'approve'));

        // Get a list of recipients to be notified for this stream item.
        $recipients = $this->getStreamNotificationTargets($comment->uid, 'relationship', 'user', 'approve', array($table->actor, $table->target), array($comment->created_by));

        FD::notify(
            'comments.item',
            $recipients,
            array(
                'comment' => $comment->comment,
                'link' => $streamItem->getPermalink(true, true)
            ),
            array(
                'type' => 'comments',
                'uid' => $comment->uid,
                'context_type' => $comment->element,
                'url' => $streamItem->getPermalink(false, false, false)
            )
        );
    }

    public function onAfterLikeSave($likes)
    {
        if ($likes->type != 'relationship.user.approve') {
            return;
        }

        $table = $this->getTableObject($likes->uid);

        if (!$table) {
            return;
        }

        $recipients = $this->getStreamNotificationTargets($likes->uid, 'relationship', 'user', 'approve', array($table->actor, $table->target), array($likes->created_by));

        $streamItem = FD::table('streamitem');
        $streamItem->load(array('context_type' => 'relationship', 'context_id' => $likes->uid, 'verb' => 'approve'));

        FD::notify(
            'likes.item',
            $recipients,
            array(
                'link' => $streamItem->getPermalink(true, true)
            ),
            array(
                'type' => 'likes',
                'uid' => $likes->uid,
                'context_type' => $likes->type,
                'url' => $streamItem->getPermalink(false, false, false)
            )
        );
    }

    private function getTableObject($uid)
    {
        static $loaded = false;

        if (!$loaded) {
            $base = SOCIAL_FIELDS . '/user/relationship/tables';

            JTable::addIncludePath($base);

            $loaded = true;
        }

        $table = JTable::getInstance('relations', 'SocialFieldTableUser');

        $state = $table->load($uid);

        if (!$state) {
            return false;
        }

        return $table;
    }
}
