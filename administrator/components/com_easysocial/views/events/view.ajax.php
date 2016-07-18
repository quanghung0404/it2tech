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

// Include the main views class
FD::import('admin:/views/views');

class EasySocialViewEvents extends EasySocialAdminView
{
    public function createDialog()
    {
        $theme = FD::themes();

        $categories = FD::model('EventCategories')->getCategories(array('state' => SOCIAL_STATE_PUBLISHED, 'ordering' => 'ordering'));

        $theme->set('categories', $categories);

        $contents = $theme->output('admin/events/dialog.createEvent');

        return $this->ajax->resolve($contents);
    }

    /**
     * Displays the delete confirmation dialog
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function deleteDialog()
    {
        $theme = FD::themes();

        $contents = $theme->output('admin/events/dialog.deleteEvent');

        return $this->ajax->resolve($contents);
    }

    public function switchOwner()
    {
        $theme = FD::themes();

        $ids = $this->input->get('ids', '', 'var');

        $theme->set('ids', $ids);

        $contents = $theme->output('admin/events/dialog.switchOwner.browse');

        return $this->ajax->resolve($contents);
    }

    public function confirmSwitchOwner()
    {
        $userid = $this->input->getInt('userId');
        $user = FD::user($userid);

        $ids = $this->input->get('ids', '', 'var');

        $theme = FD::themes();

        $theme->set('user', $user);
        $theme->set('ids', $ids);

        $contents = $theme->output('admin/events/dialog.switchOwner.confirm');

        return $this->ajax->resolve($contents);
    }

    public function inviteGuests()
    {
        $theme = FD::themes();
        $contents = $theme->output('admin/events/dialog.inviteGuests.browse');

        return $this->ajax->resolve($contents);
    }

    public function confirmInviteGuests()
    {
        $theme = FD::themes();

        // $guests = $this->input->get('guests', '', 'var');
        $guests = JRequest::getVar('guests');

        if (empty($guests)) {
            $contents = $theme->output('admin/events/dialog.inviteGuests.empty');

            return $this->ajax->resolve($contents);
        }

        $theme->set('guests', $guests);

        $userids = array();

        foreach ($guests as $id => $guest) {
            $userids[] = $id;
        }

        $theme->set('userids', FD::json()->encode($userids));

        $eventid = $this->input->getInt('eventid');

        $theme->set('eventid', $eventid);

        $contents = $theme->output('admin/events/dialog.inviteGuests.confirm');

        return $this->ajax->resolve($contents);
    }

    public function deleteCategoryDialog()
    {
        $theme = FD::themes();

        $contents = $theme->output('admin/events/dialog.deleteEventCategory');

        return $this->ajax->resolve($contents);
    }

    public function removeCategoryAvatar()
    {
        return $this->ajax->resolve();
    }

    public function browse()
    {
        $callback = $this->input->get('jscallback');

        $theme = FD::themes();
        $theme->set('callback', $callback);
        $content = $theme->output('admin/events/dialog.browse');

        return $this->ajax->resolve( $content );
    }

    public function browseCategory()
    {
        $callback = $this->input->get('jscallback', '', 'cmd');

        $theme = FD::themes();
        $theme->set('callback', $callback);
        $content = $theme->output('admin/events/dialog.browse.category');

        return $this->ajax->resolve($content);
    }

    public function switchCategory()
    {
        $theme = FD::themes();

        $ids = $this->input->get('ids', '', 'var');

        $theme->set('ids', $ids);

        $categories = FD::model('EventCategories')->getCategories(array('state' => SOCIAL_STATE_PUBLISHED, 'ordering' => 'ordering'));

        $theme->set('categories', $categories);

        $contents = $theme->output('admin/events/dialog.switchCategory.browse');

        return $this->ajax->resolve($contents);
    }

    public function applyRecurringDialog()
    {
        $theme = FD::themes();

        $contents = $theme->output('admin/events/dialog.applyRecurring');

        return $this->ajax->resolve($contents);
    }

    public function store()
    {
        return $this->ajax->resolve();
    }

    public function createRecurring()
    {
        return $this->ajax->resolve();
    }
}
