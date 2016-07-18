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

FD::import('site:/controllers/controller');

class EasySocialControllerExplorer extends EasySocialController
{
    /**
     * Service Hook for explorer
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function hook()
    {
        // Check for request forgeries
        FD::checkToken();

        // Require the user to be logged in
        FD::requireLogin();

        // Get the event object
        $uid = $this->input->get('uid', 0, 'int');
        $type = $this->input->get('type', '', 'cmd');

        // Load up the explorer library
        $explorer = FD::explorer($uid, $type);

        // Determine if the viewer can really view items
        if (!$explorer->hook('canViewItem')) {
            return $this->view->call(__FUNCTION__);
        }

        // Get the hook
        $hook = $this->input->get('hook', '', 'cmd');

        // Get the result
        $result = $explorer->hook($hook);

        $exception = FD::exception('Folder retrieval successful', SOCIAL_MSG_SUCCESS);

        return $this->view->call(__FUNCTION__, $exception, $result);
    }
}
