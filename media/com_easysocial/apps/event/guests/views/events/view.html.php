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
 * Profile view for article app
 *
 * @since    1.0
 * @access    public
 */
class GuestsViewEvents extends SocialAppsView
{
    /**
     * Displays the application output in the canvas.
     *
     * @since    1.0
     * @access    public
     * @param    int        The user id that is currently being viewed.
     */
    public function display($eventId = null, $docType = null)
    {
        // Load up the event
        $event = FD::event($eventId);

        // Get the event params
        $params = $event->getParams();

        // Load up the events model
        $model = FD::model('Events');

        $type = FD::input()->getString('type', 'going');

        $options = array();

        if ($type === 'going') {
            $options['state'] = SOCIAL_EVENT_GUEST_GOING;
        }

        if ($params->get('allowmaybe') && $type === 'maybe') {
            $options['state'] = SOCIAL_EVENT_GUEST_MAYBE;
        }

        if ($params->get('allownotgoingguest') && $type === 'notgoing') {
            $options['state'] = SOCIAL_EVENT_GUEST_NOT_GOING;
        }

        if ($event->isClosed() && $type === 'pending') {
            $options['state'] = SOCIAL_EVENT_GUEST_PENDING;
        }

        if ($type === 'admin') {
            $options['admin'] = 1;
        }

        $this->set('type', $type);

        $guests  = $model->getGuests($event->id, $options);
        $pagination = $model->getPagination();

        $this->set('event', $event);
        $this->set('guests', $guests);

        $eventAlias = $event->getAlias();
        $appAlias = $this->app->getAlias();

        $permalinks = array(
            'going' => '',
            'notgoing' => '',
            'maybe' => '',
            'admin' => '',
            'pending' => ''
        );

        // Avoid using $filter because it is a FRoute reserved word

        foreach ($permalinks as $key => &$value) {
            $value = FRoute::events(array(
                'layout' => 'item',
                'id' => $eventAlias,
                'appId' => $appAlias,
                'type' => $key
            ));
        }

        $this->set('permalinks', $permalinks);

        $myGuest = $event->getGuest();

        $this->set('myGuest', $myGuest);

        echo parent::display('events/default');
    }

}
