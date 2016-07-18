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
 * Displays the event photos in a widget
 *
 * @since    1.3
 * @access   public
 */
class PhotosWidgetsEvents extends SocialAppsWidgets
{
    /**
     * Displays the action for albums
     *
     * @since   1.3
     * @access  public
     * @param   SocialEvent $event
     * @return
     */
    public function eventAdminStart($event)
    {
        if ($this->app->state == SOCIAL_STATE_UNPUBLISHED) {
            return;
        }

        $category = $event->getCategory();

        if (!$category->getAcl()->get('photos.enabled', true) || !$event->getParams()->get('photo.albums', true)) {
            return;
        }

        $this->set('event', $event);
        $this->set('app', $this->app);

        echo parent::display('widgets/widget.menu');
    }

    /**
     * Display user photos on the side bar
     *
     * @since   1.2
     * @access  public
     * @return
     */
    public function sidebarBottom($eventId, $event)
    {
        $event = FD::event($eventId);

        $category = $event->getCategory();

        if (!$category->getAcl()->get('photos.enabled', true) || !$event->getParams()->get('photo.albums', true)) {
            return;
        }

        // Get recent albums
        $albumsHTML = $this->getAlbums($event);

        echo $albumsHTML;
    }


    /**
     * Display the list of photo albums
     *
     * @since   1.2
     * @access  public
     * @param   SocialEvent The event object
     */
    public function getAlbums(&$event)
    {
        $params = $this->getParams();

        if (!$params->get('widgets_album', true)) {
            return;
        }

        // Get the album model
        $model  = FD::model('Albums');
        $albums = $model->getAlbums($event->id, SOCIAL_TYPE_EVENT);
        $options = array('core' => false, 'withCovers' => true, 'pagination' => 10);

        // Get the total number of albums
        $total = $model->getTotalAlbums(array('uid' => $event->id, 'type' => SOCIAL_TYPE_EVENT));

        $this->set('total', $total);
        $this->set('albums', $albums);
        $this->set('event', $event);

        return parent::display('widgets/widget.albums');
    }
}
