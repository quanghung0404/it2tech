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

/**
 * Displays the canvas view for news app
 *
 * @since    1.2
 * @access    public
 */
class NewsViewForm extends SocialAppsView
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
        $event = FD::event($uid);
        $editor = JFactory::getEditor();

        $guest = $event->getGuest();

        // Only allow group admin to create or edit news
        if (!$guest->isAdmin() && !$this->my->isSiteAdmin()) {
            FD::info()->set(false, JText::_('COM_EASYSOCIAL_EVENTS_ONLY_GUEST_ARE_ALLOWED'), SOCIAL_MSG_ERROR);
            return $this->redirect($event->getPermalink(false));
        }

        // Determines if this item is being edited
        $id = $this->input->get('newsId', 0, 'int');
        $news = FD::table('EventNews');
        $news->load($id);

        FD::page()->title(JText::_('APP_EVENT_NEWS_FORM_UPDATE_PAGE_TITLE'));

        // Determine if this is a new record or not
        if (!$id) {
            $news->comments = true;
            FD::page()->title(JText::_('APP_EVENT_NEWS_FORM_CREATE_PAGE_TITLE'));
        }

        // Get app params
        $params = $this->app->getParams();

        $this->set('params', $params);
        $this->set('news', $news);
        $this->set('editor', $editor);
        $this->set('event', $event);

        echo parent::display('canvas/form');
    }
}
