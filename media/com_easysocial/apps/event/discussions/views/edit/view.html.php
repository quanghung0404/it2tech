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

class DiscussionsViewEdit extends SocialAppsView
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

        // Set the page title
        FD::page()->title(JText::_('APP_EVENT_DISCUSSIONS_PAGE_TITLE_EDIT'));

        // Get the discussion item
        $id = $this->input->get('discussionId', 0, 'int');

        $discussion = FD::table('Discussion');
        $discussion->load($id);

        if ($discussion->created_by != $this->my->id && !$event->getGuest()->isAdmin() && !$this->my->isSiteAdmin()) {
            return $this->redirect($event->getPermalink(false));
        }

        // Determines if we should allow file sharing
        $access = $event->getAccess();
        $files  = $access->get('files.enabled', true);

        $this->set('files', $files);
        $this->set('discussion', $discussion);
        $this->set('event', $event);

        echo parent::display('canvas/form');
    }
}
