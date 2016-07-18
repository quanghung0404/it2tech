<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

FD::import('site:/views/views');

class EasySocialViewEvents extends EasySocialSiteView
{
    /**
     * Renders the calendar via ajax
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function renderCalendar()
    {
        $unix = $this->input->getString('date', FD::date()->toUnix());

        $day = date('d', $unix);
        $month = date('m', $unix);
        $year = date('Y', $unix);

        // Create a calendar object
        $calendar = new stdClass();

        $calendar->year = $year;
        $calendar->month = $month;

        // Configurable start of week
        $startOfWeek = $this->config->get('events.startofweek');

        // Here we generate the first day of the month
        $calendar->first_day = mktime(0, 0, 0, $month, 1, $year);

        // This gets us the month name
        $calendar->title = date('F', $calendar->first_day);

        // Sets the calendar header
        $date = FD::date($unix);
        $calendar->header = $date->format(JText::_('COM_EASYSOCIAL_DATE_MY'));

        // Here we find out what day of the week the first day of the month falls on
        $calendar->day_of_week = date('D', $calendar->first_day) ;

        // Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
        $dayOfWeek = 0;

        switch ($calendar->day_of_week) {
            case "Sun":
                $dayOfWeek = 0;
                break;
            case "Mon":
                $dayOfWeek = 1;
                break;
            case "Tue":
                $dayOfWeek = 2;
                break;
            case "Wed":
                $dayOfWeek = 3;
                break;
            case "Thu":
                $dayOfWeek = 4;
                break;
            case "Fri":
                $dayOfWeek = 5;
                break;
            case "Sat":
                $dayOfWeek = 6;
                break;
        }

        // Day of week is dependent on the start of the week
        if ($dayOfWeek < $startOfWeek) {
            $calendar->blank = 7 - $startOfWeek + $dayOfWeek;
        } else {
            $calendar->blank = $dayOfWeek - $startOfWeek;
        }

        // Previous month
        $calendar->previous = strtotime('-1 month', $calendar->first_day);

        // Next month
        $calendar->next = strtotime('+1 month', $calendar->first_day);

        // Determine how many days are there in the current month
        $calendar->days_in_month = date('t', $calendar->first_day);

        // Create a date range to retrieve all the events
        $start = $year . '-' . $month . '-' . '01 00:00:00';
        $end = $year . '-' . $month . '-' . $calendar->days_in_month . ' 23:59:59';

        $events = FD::model('Events')->getEvents(array(
            'state' => SOCIAL_STATE_PUBLISHED,
            'type' => $this->my->isSiteAdmin() ? 'all' : 'user',
            'ordering' => 'start',
            'start-after' => $start,
            'start-before' => $end
        ));

        // This array groups the events by days
        $days = array();

        foreach($events as $event) {
            $days[$event->getEventStart()->format('j', true)][] = $event;
        }

        // Compute the start of week
        $weekdays = $this->getWeekdays();

        $theme = FD::themes();
        $theme->set('weekdays', $weekdays);
        $theme->set('calendar', $calendar);
        $theme->set('days', $days);
        $theme->set('events', $events);

        $today = FD::date()->format('Y-m-d', true);
        $tomorrow = FD::date()->modify('+1 day')->format('Y-m-d', true);

        $theme->set('today', $today);
        $theme->set('tomorrow', $tomorrow);

        $output = $theme->output('site/events/default.calendar');

        return $this->ajax->resolve($output);
    }

    public function getWeekdays()
    {
        $weekdays = array(JText::_('SUN'), JText::_('MON'), JText::_('TUE'), JText::_('WED'), JText::_('THU'), JText::_('FRI'), JText::_('SAT'));

        // Configurable option
        $startOfWeek = $this->config->get('events.startofweek');

        if ($startOfWeek > 0) {
            $spliced = array_splice($weekdays, $startOfWeek);
            $weekdays = array_merge($spliced, $weekdays);
        }

        return $weekdays;
    }

    public function getEvents($filter, $events, $pagination, $activeCategory, $featuredEvents)
    {
        $theme = FD::themes();

        if ($this->hasErrors()) {
            return $this->ajax->reject($this->getMessage());
        }

        $theme->set('filter', $filter);
        $theme->set('events', $events);
        $theme->set('pagination', $pagination);
        $theme->set('activeCategory', $activeCategory);
        $theme->set('featuredEvents', $featuredEvents);

        $ordering = $this->input->get('ordering', 'start', 'word');

        $theme->set('ordering', $ordering);

        $includePast = $this->input->getInt('includePast', 0);

        $theme->set('includePast', $includePast);

        $routeOptions = array(
            'option' => SOCIAL_COMPONENT_NAME,
            'view' => 'events'
        );

        if ($activeCategory) {
            $routeOptions['categoryid'] = $activeCategory->getAlias();
        } else {
            if ($filter) {
                $routeOptions['filter'] = $filter;
            }
        }

        $resolveOptions = array();

        // Get the Guest App to generate the guest listing link for each event
        $guestApp = FD::table('App');
        $guestApp->load(array('type' => SOCIAL_TYPE_APPS, 'group' => SOCIAL_TYPE_EVENT, 'element' => 'guests'));

        $theme->set('guestApp', $guestApp);

        // Check if this is sort only
        $sort = $this->input->getBool('sort', false);

        // Various theme settings
        $showSorting = true;
        $showPastFilter = true;
        $showDistance = false;
        $showDistanceSorting = false;

        // If filter is date, then we set the date
        if ($filter === 'date') {
            $now = FD::date();

            list($nowYMD, $nowHMS) = explode(' ', $now->toSql(true));

            $input = $this->input->getString('date');

            // We need segments to be populated. If no input is passed, then it is today, and we use today as YMD then
            if (empty($input)) {
                $input = $nowYMD;
            }

            if (!empty($input)) {
                $routeOptions['date'] = $input;
            }

            $segments = explode('-', $input);

            $isToday = false;
            $isTomorrow = false;
            $isCurrentMonth = false;
            $isCurrentYear = false;

            $start = $nowYMD;

            $nextLink = '';
            $prevLink = '';
            $nextDate = '';
            $prevDate = '';
            $nextTitle = '';
            $prevTitle = '';

            $mode = count($segments);

            switch ($mode) {
                case 1:
                    $start = $segments[0] . '-01-01';
                    $end = $segments[0] . '-12-31';

                    $dateFormat = 'COM_EASYSOCIAL_DATE_Y';

                    $currentYear = $now->format('Y', true);

                    $isCurrentYear = $input == $currentYear;

                    $nextDate = $segments[0] + 1;
                    $prevDate = $segments[0] - 1;

                    $nextLink = FRoute::events(array('filter' => 'date', 'date' => $nextDate));
                    $prevLink = FRoute::events(array('filter' => 'date', 'date' => $prevDate));

                    if ($nextDate == $currentYear) {
                        $nextTitle = JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_YEAR');
                    } else {
                        $nextTitle = JText::sprintf('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_DATE', FD::date($nextDate . '-01-01')->format(JText::_($dateFormat), true));
                    }

                    if ($prevDate == $currentYear) {
                        $prevTitle = JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_YEAR');
                    } else {
                        $prevTitle = JText::sprintf('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_DATE', FD::date($prevDate . '-01-01')->format(JText::_($dateFormat), true));
                    }
                break;

                case 2:
                    $start = $segments[0] . '-' . $segments[1] . '-01';
                    // Need to get the month's maximum day
                    $monthDate = FD::date($start);
                    $maxDay = $monthDate->format('t');

                    $end = $segments[0] . '-' . $segments[1] . '-' . str_pad($maxDay, 2, '0', STR_PAD_LEFT);

                    $dateFormat = 'COM_EASYSOCIAL_DATE_MY';

                    $currentMonth = $now->format('Y-m', true);

                    $isCurrentMonth = $input == $currentMonth;

                    $nextDate = FD::date($start, false)->modify('+1 month')->format('Y-m');
                    $prevDate = FD::date($start, false)->modify('-1 month')->format('Y-m');

                    $nextLink = FRoute::events(array('filter' => 'date', 'date' => $nextDate));
                    $prevLink = FRoute::events(array('filter' => 'date', 'date' => $prevDate));

                    if ($nextDate == $currentMonth) {
                        $nextTitle = JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_MONTH');
                    } else {
                        $nextTitle = JText::sprintf('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_DATE', FD::date($nextDate . '-01', false)->format(JText::_($dateFormat)));
                    }

                    if ($prevDate == $currentMonth) {
                        $prevTitle = JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_MONTH');
                    } else {
                        $prevTitle = JText::sprintf('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_DATE', FD::date($prevDate . '-01', false)->format(JText::_($dateFormat)));
                    }
                break;

                default:
                case 3:
                    $start = $segments[0] . '-' . $segments[1] . '-' . $segments[2];
                    $end = $segments[0] . '-' . $segments[1] . '-' . $segments[2];

                    $dateFormat = 'COM_EASYSOCIAL_DATE_DMY';

                    $currentDay = $now->format('Y-m-d', true);

                    $nextDay = $now->modify('+1 day')->format('Y-m-d', true);

                    $isToday = $input == $currentDay;

                    $isTomorrow = $input == $nextDay;

                    $nextDate = FD::date($start, false)->modify('+1 day')->format('Y-m-d');
                    $prevDate = FD::date($start, false)->modify('-1 day')->format('Y-m-d');

                    $nextLinkOptions = array('filter' => 'date');
                    $prevLinkOptions = array('filter' => 'date');

                    if ($nextDate == $currentDay) {
                        $nextTitle = JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_TODAY') . ' - ' . FD::date($nextDate, false)->format(JText::_($dateFormat));
                    } elseif ($nextDate == $nextDay) {
                        $nextTitle = JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_TOMORROW') . ' - ' . FD::date($nextDate, false)->format(JText::_($dateFormat));

                        $nextLinkOptions['date'] = $nextDate;
                    } else {
                        $nextTitle = JText::sprintf('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_DATE', FD::date($nextDate, false)->format(JText::_($dateFormat)));

                        $nextLinkOptions['date'] = $nextDate;
                    }

                    $nextLink = FRoute::events($nextLinkOptions);

                    if ($prevDate == $currentDay) {
                        $prevTitle = JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_TODAY') . ' - ' . FD::date($prevDate, false)->format(JText::_($dateFormat));
                    } elseif ($prevDate == $nextDay) {
                        $prevTitle = JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_TOMORROW') . ' - ' . FD::date($prevDate, false)->format(JText::_($dateFormat));

                        $prevLinkOptions['date'] = $prevDate;
                    } else {
                        $prevTitle = JText::sprintf('COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_DATE', FD::date($prevDate, false)->format(JText::_($dateFormat)));

                        $prevLinkOptions['date'] = $prevDate;
                    }

                    $prevLink = FRoute::events($prevLinkOptions);
                break;
            }

            $theme->set('dateFormat', $dateFormat);

            $theme->set('date', FD::date($start, false));

            $theme->set('nextLink', $nextLink);
            $theme->set('prevLink', $prevLink);
            $theme->set('nextDate', $nextDate);
            $theme->set('prevDate', $prevDate);
            $theme->set('nextTitle', $nextTitle);
            $theme->set('prevTitle', $prevTitle);

            $theme->set('isToday', $isToday);
            $theme->set('isTomorrow', $isTomorrow);

            $showSorting = false;
            $showPastFilter = false;

            $resolveOptions['isToday'] = $isToday;
            $resolveOptions['isTomorrow'] = $isTomorrow;
            $resolveOptions['isCurrentMonth'] = $isCurrentMonth;
            $resolveOptions['isCurrentYear'] = $isCurrentYear;
        }

        if ($filter === 'past') {
            $showPastFilter = false;
        }
        if ($filter === 'ongoing') {
            $showPastFilter = false;
        }
        if ($filter === 'upcoming') {
            $showPastFilter = false;
        }
        if ($filter === 'week1') {
            $showPastFilter = false;
        }
        if ($filter === 'week2') {
            $showPastFilter = false;
        }
        if ($filter === 'nearby') {
            $showSorting = false;
            $showDistance = true;
            $showDistanceSorting = true;

            $distance = $this->input->getString('distance');

            if (!empty($distance) && $distance != 10) {
                $routeOptions['distance'] = $distance;
            } else {
                $distance = 10;
            }

            $theme->set('distance', $distance);

            $distanceUnit = FD::config()->get('general.location.proximity.unit');

            $theme->set('distanceUnit', $distanceUnit);

            $resolveOptions['title'] = JText::sprintf('COM_EASYSOCIAL_EVENTS_IN_DISTANCE_RADIUS', $distance, $distanceUnit);
        }

        $theme->set('showSorting', $showSorting);
        $theme->set('showPastFilter', $showPastFilter);
        $theme->set('showDistance', $showDistance);
        $theme->set('showDistanceSorting', $showDistanceSorting);

        $theme->set('delayed', false);

        $hrefs = array();

        // We use start as key because order is always start by default, and it is the page default link
        $hrefs['start'] = array(
            'nopast' => FRoute::events($routeOptions, false)
        );

        if ($showSorting) {
            // Only need to create the "order by created" link.
            $hrefs['created'] = array(
                'nopast' => FRoute::events(array_merge($routeOptions, array('ordering' => 'created')), false)
            );
        }

            // Only need to create the "order by distance" link.
        if ($showDistanceSorting) {
            $hrefs['distance'] = array(
                'nopast' => FRoute::events(array_merge($routeOptions, array('ordering' => 'distance')), false)
            );
        }

        if ($showPastFilter) {
            // If past filter is displayed on the page, then we need to generate the past links counter part
            $hrefs['start']['past'] = FRoute::events(array_merge($routeOptions, array('includePast' => 1)), false);

            if ($showSorting) {
                // Only need to create the "order by created" link.
                $hrefs['created']['past'] = FRoute::events(array_merge($routeOptions, array('ordering' => 'created', 'includePast' => 1)), false);
            }

            if ($showDistanceSorting) {
                // Only need to create the "order by distance" link.
                $hrefs['distance']['past'] = FRoute::events(array_merge($routeOptions, array('ordering' => 'distance', 'includePast' => 1)), false);
            }
        }

        $theme->set('hrefs', $hrefs);

        $resolveOptions['hrefs'] = $hrefs;

        // Check if this is coming from group
        $group = $this->input->getInt('group');

        if (!empty($group)) {
            $theme->set('isGroupOwner', true);
        }

        if ($sort) {
            $content = $theme->output('site/events/default.list.items');
        } else {
            $content = $theme->output('site/events/default.list');
        }

        return $this->ajax->resolve($content, $resolveOptions);
    }

    /**
     * Displays the promote guest confirmation dialog
     *
     * @since   1.3
     * @access  public
     */
    public function confirmPromoteGuest()
    {
        // Only logged in users are allowed here.
        FD::requireLogin();

        // Get the guest object.
        $id = $this->input->getInt('id', 0);
        $guest = FD::table('EventGuest');
        $guest->load($id);

        // Get the user object.
        $user = FD::user($guest->uid);

        // Get the current user
        $my = FD::user();

        // Get the current user as a guest object in the same event
        $myGuest = FD::table('EventGuest');
        $myGuest->load(array('uid' => $my->id, 'type' => SOCIAL_TYPE_USER, 'cluster_id' => $guest->cluster_id));

        $theme = FD::themes();

        if ($my->isSiteAdmin() || $myGuest->isAdmin()) {
            $theme->set('user', $user);

            $contents = $theme->output('site/events/dialog.guest.promote');

            return $this->ajax->resolve($contents);
        }

        return $this->ajax->resolve($theme->output('site/events/dialog.guest.error'));
    }

    public function confirmDemoteGuest()
    {
        // Only logged in users are allowed here.
        FD::requireLogin();

        // Get the guest object.
        $id = $this->input->getInt('id', 0);
        $guest = FD::table('EventGuest');
        $guest->load($id);

        // Get the user object.
        $user = FD::user($guest->uid);

        // Get the current user
        $my = FD::user();

        // Get the current user as a guest object in the same event
        $myGuest = FD::table('EventGuest');
        $myGuest->load(array('uid' => $my->id, 'type' => SOCIAL_TYPE_USER, 'cluster_id' => $guest->cluster_id));

        $theme = FD::themes();

        if (($my->isSiteAdmin() || $myGuest->isOwner()) && $guest->isStrictlyAdmin()) {
            $theme->set('user', $user);

            $contents = $theme->output('site/events/dialog.guest.demote');

            return $this->ajax->resolve($contents);
        }

        return $this->ajax->resolve($theme->output('site/events/dialog.guest.error'));
    }

    public function confirmApproveGuest()
    {
        // Only logged in users are allowed here.
        FD::requireLogin();

        // Get the guest object.
        $id = $this->input->getInt('id', 0);
        $guest = FD::table('EventGuest');
        $guest->load($id);

        // Get the user object.
        $user = FD::user($guest->uid);

        // Get the current user
        $my = FD::user();

        // Get the current user as a guest object in the same event
        $myGuest = FD::table('EventGuest');
        $myGuest->load(array('uid' => $my->id, 'type' => SOCIAL_TYPE_USER, 'cluster_id' => $guest->cluster_id));

        $theme = FD::themes();

        if (($my->isSiteAdmin() || $myGuest->isAdmin()) && $guest->isPending()) {
            $theme->set('user', $user);

            $contents = $theme->output('site/events/dialog.guest.approve');

            return $this->ajax->resolve($contents);
        }

        return $this->ajax->resolve($theme->output('site/events/dialog.guest.error'));
    }

    public function confirmRejectGuest()
    {
        // Only logged in users are allowed here.
        FD::requireLogin();

        // Get the guest object.
        $id = $this->input->getInt('id', 0);
        $guest = FD::table('EventGuest');
        $guest->load($id);

        // Get the user object.
        $user = FD::user($guest->uid);

        // Get the current user
        $my = FD::user();

        // Get the current user as a guest object in the same event
        $myGuest = FD::table('EventGuest');
        $myGuest->load(array('uid' => $my->id, 'type' => SOCIAL_TYPE_USER, 'cluster_id' => $guest->cluster_id));

        $theme = FD::themes();

        if (($my->isSiteAdmin() || $myGuest->isAdmin()) && $guest->isPending()) {
            $theme->set('user', $user);

            $contents = $theme->output('site/events/dialog.guest.reject');

            return $this->ajax->resolve($contents);
        }

        return $this->ajax->resolve($theme->output('site/events/dialog.guest.error'));
    }

    public function confirmRemoveGuest()
    {
        // Only logged in users are allowed here.
        FD::requireLogin();

        // Get the guest object.
        $id = $this->input->getInt('id', 0);
        $guest = FD::table('EventGuest');
        $guest->load($id);

        // Get the user object.
        $user = FD::user($guest->uid);

        // Get the current user
        $my = FD::user();

        // Get the current user as a guest object in the same event
        $myGuest = FD::table('EventGuest');
        $myGuest->load(array('uid' => $my->id, 'type' => SOCIAL_TYPE_USER, 'cluster_id' => $guest->cluster_id));

        $theme = FD::themes();

        if (!$guest->isOwner() && ($my->isSiteAdmin() || $myGuest->isOwner() || ($myGuest->isAdmin() && !$guest->isAdmin()))) {
            $theme->set('user', $user);
            $theme->set('guest', $guest);

            $contents = $theme->output('site/events/dialog.guest.remove');

            return $this->ajax->resolve($contents);
        }

        return $this->ajax->resolve($theme->output('site/events/dialog.guest.error'));
    }

    public function removeGuest()
    {
        //Remove Event guest user.
        return $this->ajax->resolve();
    }

    public function guestResponse($state = null)
    {
        if ($this->hasErrors()) {
            return $this->ajax->reject($this->getMessage());
        }

        return $this->ajax->resolve($state);
    }

    public function getFilter($event = null, $filter = null)
    {
        if ($this->hasErrors()) {
            return $this->ajax->reject($this->getMessage());
        }

        $theme = FD::themes();

        $theme->set('controller', 'events');
        $theme->set('filter', $filter);
        $theme->set('uid', $event->id);

        $contents = $theme->output('site/stream/form.edit');

        return $this->ajax->resolve($contents);
    }

    /**
     * Responsible to output the application contents.
     *
     * @since   1.0
     * @access  public
     * @param   SocialAppTable  The application ORM.
     */
    public function getAppContents($app)
    {
        // If there's an error throw it back to the caller.
        if ($this->hasErrors()) {
            return $this->ajax->reject($this->getMessage());
        }

        // Get the current logged in user.
        $eventId = $this->input->get('eventId', 0, 'int');
        $event   = FD::event($eventId);

        // Load the library.
        $lib      = FD::getInstance('Apps');
        $contents = $lib->renderView(SOCIAL_APPS_VIEW_TYPE_EMBED, 'events', $app, array('eventId' => $event->id));

        // Return the contents
        return $this->ajax->resolve($contents);
    }

    public function initInfo($steps = null)
    {
        if ($this->hasErrors()) {
            return $this->ajax->reject($this->getMessage());
        }

        return $this->ajax->resolve($steps);
    }

    public function getInfo($fields = null)
    {
        if ($this->hasErrors()) {
            return $this->ajax->reject($this->getMessage());
        }

        $theme = FD::themes();

        $theme->set('fields', $fields);

        $contents = $theme->output('site/events/item.info');

        return $this->ajax->resolve($contents);
    }

    /**
     * Allows caller to retrieve stream contents via ajax
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function getStream($stream = null)
    {
        if ($this->hasErrors()) {
            return $this->ajax->reject($this->getMessage());
        }
        
        // Get the event id from request
        $id = $this->input->get('id', '0', 'int');
        
        // Load up the event    
        $event = FD::event($id);

         // RSS
        if ($this->config->get('stream.rss.enabled')) {
            $this->addRss(FRoute::events(array('id' => $event->getAlias(), 'layout' => 'item'), false));
        }

        $theme = ES::themes();
        $theme->set('rssLink', $this->rssLink);
        $theme->set('stream', $stream);

        $contents = $theme->output('site/events/item.feeds');

        return $this->ajax->resolve($contents);
    }

    /**
     * Responsible to show the invite friends dialog.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function inviteFriendsDialog()
    {
        FD::requireLogin();

        $id = $this->input->get('id', '0', 'int');

        $event = FD::event($id);

        if (empty($event) || empty($event->id)) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'));
        }

        if (!$event->isPublished()) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'));
        }

        $guest = $event->getGuest($this->my->id);

        if (!$this->my->isSiteAdmin() && $event->isInviteOnly() && !$guest->isParticipant()) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'));
        }

        $model = FD::model('Events');
        $friends = $model->getFriendsInEvent($event->id, array('userId' => $this->my->id));

        $exclusion = array();

        foreach ($friends as $friend) {
            $exclusion[] = $friend->id;
        }

        $theme = FD::themes();
        $theme->set('exclusion', $exclusion);
        $theme->set('event', $event);
        $contents = $theme->output('site/events/dialog.inviteFriends');

        return $this->ajax->resolve($contents);
    }

    /**
     * Callback after inviting friends.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function inviteFriends()
    {
        if ($this->hasErrors()) {
            return $this->ajax->reject($this->getMessage());
        }

        return $this->ajax->resolve(JText::_('COM_EASYSOCIAL_EVENTS_SUCCESSFULLY_INVITED_FRIENDS'));
    }

    /**
     * Responsible to show the not going dialog.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function notGoingDialog()
    {
        FD::requireLogin();

        $id = $this->input->getInt('id', 0);

        $event = FD::event($id);

        if (empty($event) || empty($event->id)) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'));
        }

        if (!$event->isPublished()) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'));
        }

        $guest = $event->getGuest($this->my->id);

        if (!$this->my->isSiteAdmin() && !$guest->isParticipant()) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'));
        }

        $theme = FD::themes();
        $theme->set('event', $event);
        $contents = $theme->output('site/events/dialog.user.notgoing');

        return $this->ajax->resolve($contents);
    }

    /**
     * Responsible to show various actions confirmation dialog for item.
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function itemActionDialog()
    {
        FD::requireLogin();

        $id = $this->input->getInt('id', 0);
        $action = $this->input->getString('action');
        $from = $this->input->getString('from');

        $event = FD::event($id);

        if (empty($event) || empty($event->id)) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'));
        }

        $guest = $event->getGuest($this->my->id);

        if (!$this->my->isSiteAdmin() && !$guest->isAdmin() && !$guest->isOwner()) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'));
        }

        $theme = FD::themes();
        $theme->set('event', $event);
        $theme->set('action', $action);
        $theme->set('from', $from);

        //button label
        $btnLabel = JText::_('COM_EASYSOCIAL_YES_BUTTON');
        $btnClass = 'btn-es-primary';

        // override the button label and main button class here.
        switch($action) {
            case 'feature':
                $btnLabel = JText::_('COM_EASYSOCIAL_EVENTS_FEATURE_THIS_EVENT');
                break;
            default:
                $btnLabel = JText::_('COM_EASYSOCIAL_YES_BUTTON');
                break;
        }

        $theme->set('buttonlabel', $btnLabel);
        $theme->set('buttonclass', $btnClass);

        // COM_EASYSOCIAL_EVENTS_DIALOG_FEATURE_EVENT_TITLE
        // COM_EASYSOCIAL_EVENTS_DIALOG_FEATURE_EVENT_CONTENT
        // COM_EASYSOCIAL_EVENTS_DIALOG_DELETE_EVENT_TITLE
        // COM_EASYSOCIAL_EVENTS_DIALOG_DELETE_EVENT_CONTENT
        // COM_EASYSOCIAL_EVENTS_DIALOG_UNFEATURE_EVENT_TITLE
        // COM_EASYSOCIAL_EVENTS_DIALOG_UNFEATURE_EVENT_CONTENT
        // COM_EASYSOCIAL_EVENTS_DIALOG_UNPUBLISH_EVENT_TITLE
        // COM_EASYSOCIAL_EVENTS_DIALOG_UNPUBLISH_EVENT_CONTENT

        $contents = $theme->output('site/events/dialog.item.action');

        return $this->ajax->resolve($contents);
    }

    /**
     * Updates the guest state for a particular event
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function refreshGuestState()
    {
        // Ensure that the user is logged in
        FD::requireLogin();

        $id = $this->input->getInt('id', 0);

        $hideText = (bool) $this->input->getInt('hidetext', 1);

        $event = FD::event($id);

        $guest = $event->getGuest($this->my->id);

        $theme = FD::themes();

        $theme->set('event', $event);
        $theme->set('guest', $guest);
        $theme->set('hideText', $hideText);

        $contents = $theme->output('site/events/guestState.content');

        return $this->ajax->resolve($contents);
    }

    /**
     * Responsible to show the withdraw dialog.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function withdrawDialog()
    {
        FD::requireLogin();

        $id = $this->input->getInt('id', 0);

        $event = FD::event($id);

        if (empty($event) || empty($event->id)) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'));
        }

        if (!$event->isPublished()) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'));
        }

        $theme = FD::themes();
        $theme->set('event', $event);
        $contents = $theme->output('site/events/dialog.user.withdraw');

        return $this->ajax->resolve($contents);
    }

    /**
     * Responsible to show the request dialog.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function requestDialog()
    {
        FD::requireLogin();

        $id = $this->input->getInt('id', 0);

        $event = FD::event($id);

        if (empty($event) || empty($event->id)) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'));
        }

        if (!$event->isPublished()) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'));
        }

        $theme = FD::themes();
        $theme->set('event', $event);
        $contents = $theme->output('site/events/dialog.user.request');

        return $this->ajax->resolve($contents);
    }

    public function unpublishEventDialog()
    {
        FD::requireLogin();

        $id = $this->input->getInt('id', 0);

        $event = FD::event($id);

        if (empty($event) || empty($event->id)) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'));
        }

        $guest = $event->getGuest($this->my->id);

        if (!$guest->isOwner() && !$this->my->isSiteAdmin()) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'));
        }

        $theme = FD::themes();
        $theme->set('event', $event);
        $contents = $theme->output('site/events/dialog.event.unpublish');

        return $this->ajax->resolve($contents);
    }

    public function deleteEventDialog()
    {
        FD::requireLogin();

        $id = $this->input->getInt('id', 0);

        $event = FD::event($id);

        if (empty($event) || empty($event->id)) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'));
        }

        $guest = $event->getGuest($this->my->id);

        if (!$guest->isOwner() && !$this->my->isSiteAdmin() && (!$event->isGroupEvent() || ($event->isGroupEvent() && !$event->getGroup()->isOwner()))) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'));
        }

        $theme = FD::themes();
        $theme->set('event', $event);

        // Recurring support
        if ($event->isRecurringEvent() || $event->hasRecurringEvents()) {
            $contents = $theme->output('site/events/dialog.event.deleteRecurring');
        } else {
            $contents = $theme->output('site/events/dialog.event.delete');
        }

        return $this->ajax->resolve($contents);
    }

    public function deleteFilter($eventId)
    {
        $ajax = FD::ajax();

        FD::requireLogin();
        FD::info()->set($this->getMessage());

        $event = FD::event($eventId);
        $url = FRoute::events(array('layout' => 'item', 'id' => $event->getAlias()), false);

        return $ajax->redirect($url);
    }

    public function update($event)
    {
        if ($this->hasErrors() || empty($event)) {
            return $this->ajax->reject($this->getMessage());
        }

        return $this->ajax->resolve();
    }

    public function edit($errors)
    {
        if ($this->hasErrors() || !empty($errors)) {
            return $this->ajax->reject($this->getMessage(), $errors);
        }

        return $this->ajax->resolve();
    }

    public function createRecurring()
    {
        return $this->ajax->resolve();
    }

    public function deleteRecurringDialog()
    {
        FD::requireLogin();

        // Might be calling this from backend
        FD::language()->loadSite();

        $id = $this->input->getInt('id', 0);

        $event = FD::event($id);

        if (empty($event) || empty($event->id)) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'));
        }

        $guest = $event->getGuest($this->my->id);

        if (!$guest->isOwner() && !$this->my->isSiteAdmin()) {
            return $this->ajax->reject(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'));
        }

        $theme = FD::themes();
        $theme->set('event', $event);
        $contents = $theme->output('site/events/dialog.recurringevent.delete');

        return $this->ajax->resolve($contents);
    }

    public function deleteRecurring()
    {
        if ($this->hasErrors()) {
            return $this->ajax->reject($this->getMessage());
        }

        return $this->ajax->resolve();
    }

    /**
     * Updates the event button state
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function refreshButtonState()
    {
        // Ensure that the user is logged in
        FD::requireLogin();

        $id = $this->input->getInt('id', 0);
        $isPopbox = $this->input->getInt('isPopbox', 0, 'int');

        $hideText = (bool) $this->input->getInt('hidetext', 1);

        $event = FD::event($id);

        $contents = $event->showRsvpButton($isPopbox);

        return $this->ajax->resolve($contents);
    }
}
