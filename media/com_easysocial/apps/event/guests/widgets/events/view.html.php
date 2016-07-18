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
 * Attendees widget for event
 *
 * @since    1.3
 * @access   public
 */
class GuestsWidgetsEvents extends SocialAppsWidgets
{
    /**
     * Display users attending this event
     *
     * @since    1.3
     * @access   public
     * @param    string
     * @return
     */
    public function sidebarBottom($eventId)
    {
        // Load up the event object
        $event = FD::event($eventId);

        $params = $this->app->getParams();

        if ($params->get('show_guests', true)) {
            echo $this->getGuests($event);
        }

        if ($params->get('show_online', true)) {
            echo $this->getOnlineUsers($event);
        }

        if ($params->get('show_friends', true)) {
            echo $this->getFriends($event);
        }
    }

    /**
     * Displays the total attendees
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function afterCategory($eventId)
    {
        $theme = FD::themes();

        // Get the event object
        $event = FD::event($eventId);

        $permalink  = FRoute::events(array('layout'=> 'item', 'id' => $event->getAlias(), 'appId' => $this->app->getAlias()));

        $theme->set('miniheader', false);
        $theme->set('permalink', $permalink);
        $theme->set('event', $event);

        echo $theme->output('themes:/apps/event/guests/widgets/widget.header');
    }

    /**
     * Displays the attendees in mini header
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function miniEventStatsEnd($eventId)
    {
        $theme = FD::themes();

        // Get the event object
        $event = FD::event($eventId);

        $permalink  = FRoute::events(array('layout'=> 'item', 'id' => $event->getAlias(), 'appId' => $this->app->getAlias()));

        $theme->set('miniheader', true);
        $theme->set('permalink', $permalink);
        $theme->set('event', $event);

        echo $theme->output('themes:/apps/event/guests/widgets/widget.header');
    }

    private function getGuests($event)
    {
        $theme = FD::themes();

        $totalGoing = count($event->going);

        $ids = array();
        $goingGuests = array();

        if ($totalGoing > 0) {
            // Guests are already in $event->guests property
            // Going guests are also in $event->going property
            // Use php random to pick the id out from $event->going, then map it back to $event->guests
            $ids = (array) array_rand($event->going, min($totalGoing, 20));

            foreach ($ids as $id) {
                $guest = $event->guests[$event->going[$id]];
                $goingGuests[] = $guest;
            }
        }

        $theme->set('event', $event);
        $theme->set('totalGoing', $totalGoing);
        $theme->set('goingGuests', $goingGuests);

        $params = $event->getParams();

        $allowMaybe = $params->get('allowmaybe', true);

        $theme->set('allowMaybe', $allowMaybe);

        if ($allowMaybe) {
            $totalMaybe = count($event->maybe);

            $theme->set('totalMaybe', $totalMaybe);

            if ($totalMaybe > 0) {
                $ids = (array) array_rand($event->maybe, min($totalMaybe, 20));

                $maybeGuests = array();

                foreach ($ids as $id) {
                    $guest = $event->guests[$event->maybe[$id]];
                    $maybeGuests[] = $guest;
                }

                $theme->set('maybeGuests', $maybeGuests);
            }
        }

        $allowNotGoing = $params->get('allownotgoingguest', true);

        $theme->set('allowNotGoing', $allowNotGoing);

        if ($allowNotGoing) {
            $totalNotGoing = count($event->notgoing);

            $theme->set('totalNotGoing', $totalNotGoing);

            if ($totalNotGoing > 0) {
                $ids = (array) array_rand($event->notgoing, min($totalNotGoing, 20));

                $notGoingGuests = array();

                foreach ($ids as $id) {
                    $guest = $event->guests[$event->notgoing[$id]];
                    $notGoingGuests[] = $guest;
                }

                $theme->set('notGoingGuests', $notGoingGuests);
            }
        }

        $link = FRoute::events(array(
            'id' => $event->getAlias(),
            'appId' => $this->app->getAlias(),
            'layout' => 'item'
        ));

        $theme->set('link', $link);

        echo $theme->output('themes:/apps/event/guests/widgets/widget.guests');
    }

    private function getFriends($event)
    {
        $theme = FD::themes();

        $my = FD::user();

        $options = array();
        $options['userId'] = $my->id;
        $options['randomize'] = true;
        $options['limit'] = 5;
        $options['published'] = true;

        $model = FD::model('Events');
        $friends = $model->getFriendsInEvent($event->id, $options);

        $theme->set('friends', $friends);

        return $theme->output('themes:/apps/event/guests/widgets/widget.friends');
    }

    private function getOnlineUsers($event)
    {
        $model = FD::model('Events');
        $users = $model->getOnlineGuests($event->id);

        $theme = FD::themes();
        $theme->set('users', $users);

        return $theme->output('themes:/apps/event/guests/widgets/widget.online');
    }
}
