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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

/**
 * Displays the canvas view for news app
 *
 * @since    1.3
 * @access   public
 */
class NewsViewItem extends SocialAppsView
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

        // Get the article item
        $news = FD::table('EventNews');
        $news->load($this->input->get('newsId', 0, 'int'));

        // Check if the user is really allowed to view this item
        if (!$event->canViewItem()) {
            return $this->redirect($event->getPermalink(false));
        }

        // Get the author of the article
        $author = FD::user($news->created_by);

        // Get the url for the article
        $url = FRoute::apps(array('layout' => 'canvas', 'customView' => 'item', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $this->app->getAlias(), 'articleId' => $news->id), false);

        // Apply comments for the article
        $comments = FD::comments($news->id, 'news', 'create', SOCIAL_APPS_GROUP_EVENT, array('url' => $url));

        // Apply likes for the article
        $likes = FD::likes()->get($news->id, 'news', 'create', SOCIAL_APPS_GROUP_EVENT);

        // Set the page title
        FD::page()->title($news->get('title'));

        // Retrieve the params
        $params = $this->app->getParams();

        $this->set('app', $this->app);
        $this->set('params', $params);
        $this->set('event', $event);
        $this->set('likes', $likes);
        $this->set('comments', $comments);
        $this->set('author', $author);
        $this->set('news', $news);

        echo parent::display('canvas/item');
    }
}
