<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewTeamBlog extends EasyBlogView
{
    /**
     * Displays the confirmation to join the team
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function join()
    {
        // Require user to be logged in
        EB::requireLogin();

        // Get the team object
        $id = $this->input->get('id', 0, 'int');
        $team = EB::table('TeamBlog');
        $team->load($id);

        if (!$id || !$team->id) {
            return $this->ajax->reject(JText::_('COM_EASYBLOG_TEAMBLOG_INVALID_TEAM_ID_PROVIDED'));
        }

        // Return url
        $return = $this->input->get('return', '', 'default');

        $template = EB::template();
        $template->set('team', $team);
        $template->set('return', $return);

        $output = $template->output('site/teamblogs/dialog.join');

        return $this->ajax->resolve($output);
    }

    /**
     * Displays the confirmation to leave the team
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function leave()
    {
        // Require user to be logged in
        EB::requireLogin();

        // Get the team object
        $id = $this->input->get('id', 0, 'int');
        $team = EB::table('TeamBlog');
        $team->load($id);

        if (!$id || !$team->id) {
            return $this->ajax->reject(JText::_('COM_EASYBLOG_TEAMBLOG_INVALID_TEAM_ID_PROVIDED'));
        }

        // Return url
        $return = $this->input->get('return', '', 'default');

        $template = EB::template();
        $template->set('team', $team);
        $template->set('return', $return);

        $output = $template->output('site/teamblogs/dialog.leave');

        return $this->ajax->resolve($output);
    }


    /**
     * Displays all members from a team
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function viewMembers()
    {
        $id = $this->input->get('id', 0, 'int');

        if (! $id) {
            return $this->ajax->reject(JText::_('COM_EASYBLOG_TEAMBLOG_INVALID_TEAM_ID_PROVIDED'));
        }

        $model = EB::model('TeamBlogs');
        $members = $model->getAllMembers($id);

        $template = EB::template();
        $template->set('members', $members);
        $output = $template->output('site/teamblogs/dialog.members');

        return $this->ajax->resolve($output);
    }


}
