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

class DiscussionsViewItem extends SocialAppsView
{
    /**
     * Displays the application output in the canvas.
     *
     * @since    1.0
     * @access    public
     * @param    int        The user id that is currently being viewed.
     */
    public function display($uid = null, $docType = null)
    {
        FD::requireLogin();

        $event = FD::event($uid);

        if (!$event->canViewItem()) {
            return $this->redirect($event->getPermalink(false));
        }

        // Load up the app params
        $params = $this->app->getParams();

        // Get the discussion item
        $id = $this->input->get('discussionId', 0, 'int');
        $discussion = FD::table('Discussion');
        $discussion->load($id);

        // Get the author of the article
        $author     = FD::user($discussion->created_by);

        // Get the url for the article
        $url         = FRoute::apps(array('layout' => 'canvas', 'customView' => 'item', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $this->app->getAlias(), 'discussionId' => $discussion->id), false);

        // Set the page title
        FD::page()->title($discussion->get('title'));

        // Increment the hits for this discussion item
        $discussion->addHit();

        // Get a list of other news
        $model             = FD::model('Discussions');
        $replies         = $model->getReplies($discussion->id, array('ordering' => 'created'));

        $participants     = $model->getParticipants($discussion->id);

        // Get the answer
        $answer         = false;

        if ($discussion->answer_id) {
            $answer = FD::table('Discussion');
            $answer->load($discussion->answer_id);

            $answer->author = FD::user($answer->created_by);
        }

        // Determines if we should allow file sharing
        $access        = $event->getAccess();
        $files         = $access->get('files.enabled', true);

        $this->set('app', $this->app);
        $this->set('files', $files);
        $this->set('params', $params);
        $this->set('answer', $answer);
        $this->set('participants', $participants);
        $this->set('discussion', $discussion);
        $this->set('event', $event);
        $this->set('replies', $replies);
        $this->set('author', $author);

        echo parent::display('canvas/item');
    }
}
