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

class EasySocialControllerEvents extends EasySocialController
{
    /**
     * Retrieves a list of events from the site
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function getEvents()
    {
        // Check for request forgeries
        FD::checkToken();

        // Load up the model
        $model = FD::model('Events');

        // Ge the current filter from the request
        $filter = $this->input->get('filter', 'all', 'string');

        // There is a possibility to filter events by category
        $categoryid = $this->input->get('categoryid', 0, 'int');

        // Get the ordering
        $ordering = $this->input->get('ordering', 'start', 'word');

        // See if past should be included
        $includePast = $this->input->getInt('includePast', 0);

        $options = array('state' => SOCIAL_STATE_PUBLISHED, 'ordering' => $ordering, 'type' => $this->my->isSiteAdmin() ? 'all' : 'user', 'featured' => FD::config()->get('events.listing.includefeatured') ? 'all' : false);

        $options['limit'] = $this->input->getInt('limit', FD::themes()->getConfig()->get('events_limit', 20));
        $options['limitstart'] = $this->input->getInt('limitstart', 0);

        $featuredEvents = false;

        $activeCategory = false;

        // Support for group id
        $groupid = $this->input->getInt('group');

        if (!empty($groupid)) {
            $options['group_id'] = $groupid;
        }

        if ($filter === 'category') {
            $category = FD::table('EventCategory');
            $category->load($categoryid);

            $activeCategory = $category;

            $options['category'] = $category->id;

            $filter = 'all';
        }

        if ($filter === 'all') {
            // Need to get featured events separately here
            $featuredOptions = array(
                'featured' => true,
                'type' => array(
                    SOCIAL_EVENT_TYPE_PUBLIC,
                    SOCIAL_EVENT_TYPE_PRIVATE
               )
            );

            if ($activeCategory) {
                $featuredOptions['category'] = $category->id;
            }

            $featuredEvents = $model->getEvents($featuredOptions);

            // We do not want to include past events here
            if (!$includePast) {
                $options['ongoing'] = true;
                $options['upcoming'] = true;
            }
        }

        if ($filter === 'past') {
            $options['start-before'] = FD::date()->toSql();
            $options['ordering'] = 'created';
            $options['direction'] = 'desc';
        }

        if ($filter === 'featured') {
            $options['featured'] = true;

            // We do not want to include past events here
            if (!$includePast) {
                $options['ongoing'] = true;
                $options['upcoming'] = true;
            }
        }

        if ($filter === 'mine') {
            $options['creator_uid'] = $this->my->id;
            $options['creator_type'] = SOCIAL_TYPE_USER;
            $options['type'] = 'all';

            // We do not want to include past events here
            if (!$includePast) {
                $options['ongoing'] = true;
                $options['upcoming'] = true;
            }
        }

        if ($filter === 'participated') {
            $options['guestuid'] = $this->my->id;
            $options['type'] = 'all';

            // We do not want to include past events here
            if (!$includePast) {
                $options['ongoing'] = true;
                $options['upcoming'] = true;
            }
        }

        if ($filter === 'invited') {
            $options['gueststate'] = SOCIAL_EVENT_GUEST_INVITED;
            $options['guestuid'] = $this->my->id;
            $options['type'] = 'all';

            // We do not want to include past events here
            if (!$includePast) {
                $options['ongoing'] = true;
                $options['upcoming'] = true;
            }
        }

        if ($filter === 'going') {
            $options['gueststate'] = SOCIAL_EVENT_GUEST_GOING;
            $options['guestuid'] = $this->my->id;
            $options['type'] = 'all';

            // We do not want to include past events here
            if (!$includePast) {
                $options['ongoing'] = true;
                $options['upcoming'] = true;
            }
        }

        if ($filter === 'pending') {
            $options['gueststate'] = SOCIAL_EVENT_GUEST_PENDING;
            $options['guestuid'] = $this->my->id;
            $options['type'] = 'all';

            // We do not want to include past events here
            if (!$includePast) {
                $options['ongoing'] = true;
                $options['upcoming'] = true;
            }
        }

        if ($filter === 'maybe') {
            $options['gueststate'] = SOCIAL_EVENT_GUEST_MAYBE;
            $options['guestuid'] = $this->my->id;
            $options['type'] = 'all';

            // We do not want to include past events here
            if (!$includePast) {
                $options['ongoing'] = true;
                $options['upcoming'] = true;
            }
        }

        if ($filter === 'notgoing') {
            $options['gueststate'] = SOCIAL_EVENT_GUEST_NOTGOING;
            $options['guestuid'] = $this->my->id;
            $options['type'] = 'all';

            // We do not want to include past events here
            if (!$includePast) {
                $options['ongoing'] = true;
                $options['upcoming'] = true;
            }
        }

        if ($filter === 'tomorrow') {
            $filter = 'date';
            $this->input->set('date', FD::date()->modify('+1 day')->format('Y-m-d', true));
        }

        if ($filter === 'month') {
            $filter = 'date';
            $this->input->set('date', FD::date()->format('Y-m', true));
        }

        if ($filter === 'year') {
            $filter = 'date';
            $this->input->set('date', FD::date()->format('Y', true));
        }

        if ($filter === 'date') {
            // Depending on the input format.
            // Could be by year, year-month or year-month-day

            $now = FD::date();

            list($nowYMD, $nowHMS) = explode(' ', $now->toSql(true));

            $input = $this->input->getString('date');

            // We need segments to be populated. If no input is passed, then it is today, and we use today as YMD then
            if (empty($input)) {
                $input = $nowYMD;
            }

            $segments = explode('-', $input);

            $start = $nowYMD;
            $end = $nowYMD;

            // Depending on the amount of segments
            // 1 = filter by year
            // 2 = filter by month
            // 3 = filter by day

            $mode = count($segments);

            switch ($mode) {
                case 1:
                    $start = $segments[0] . '-01-01';
                    $end = $segments[0] . '-12-31';
                break;

                case 2:
                    $start = $segments[0] . '-' . $segments[1] . '-01';
                    // Need to get the month's maximum day
                    $monthDate = FD::date($start);
                    $maxDay = $monthDate->format('t');

                    $end = $segments[0] . '-' . $segments[1] . '-' . str_pad($maxDay, 2, '0', STR_PAD_LEFT);
                break;

                default:
                case 3:
                    $start = $segments[0] . '-' . $segments[1] . '-' . $segments[2];
                    $end = $segments[0] . '-' . $segments[1] . '-' . $segments[2];
                break;
            }

            $options['start-after'] = $start . ' 00:00:00';
            $options['start-before'] = $end . ' 23:59:59';
        }

        if ($filter === 'week1') {
            $now = FD::date();
            $week1 = FD::date($now->toUnix() + 60*60*24*7);

            $options['start-after'] = $now->toSql();
            $options['start-before'] = $week1->toSql();
        }

        if ($filter === 'week2') {
            $now = FD::date();
            $week2 = FD::date($now->toUnix() + 60*60*24*14);

            $options['start-after'] = $now->toSql();
            $options['start-before'] = $week2->toSql();
        }

        if ($filter === 'nearby') {
            $distance = $this->input->getString('distance');

            if (empty($distance)) {
                $distance = 10;
            }

            $options['location'] = true;
            $options['distance'] = $distance;
            $options['latitude'] = $this->input->getString('latitude');
            $options['longitude'] = $this->input->getString('longitude');
            $options['range'] = '<=';

            // We do not want to include past events here
            if (!$includePast) {
                $options['ongoing'] = true;
                $options['upcoming'] = true;
            }

            $session = JFactory::getSession();

            $userLocation = $session->get('events.userlocation', array(), SOCIAL_SESSION_NAMESPACE);

            $hasLocation = !empty($userLocation) && !empty($userLocation['latitude']) && !empty($userLocation['longitude']);

            if (!$hasLocation) {
                $userLocation['latitude'] = $options['latitude'];
                $userLocation['longitude'] = $options['longitude'];

                $session->set('events.userlocation', $userLocation, SOCIAL_SESSION_NAMESPACE);
            }
        }

        $events = $model->getEvents($options);

        // Load up the pagination
        $pagination = $model->getPagination();

        $pagination->setVar('Itemid', FRoute::getItemId('events'));
        $pagination->setVar('view', 'events');
        $pagination->setVar('filter', $filter);

        if ($includePast) {
            $pagination->setVar('includePast', $includePast);
        }

        if ($ordering != 'start') {
            $pagination->setVar('ordering', $ordering);
        }

        if ($activeCategory) {
            $pagination->setVar('categoryid', $activeCategory->id);
        }

        $dateInput = $this->input->getString('date');

        if (!empty($dateInput)) {
            $pagination->setVar('date', $dateInput);
        }

        $distanceInput = $this->input->getString('distance');

        if (!empty($distanceInput)) {
            $pagination->setVar('distance', $distanceInput);
        }

        return $this->view->call(__FUNCTION__, $filter, $events, $pagination, $activeCategory, $featuredEvents);
    }

    /**
     * Occurs when user tries to select an event category
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function selectCategory()
    {
        // Ensure that the user is logged in
        FD::requireLogin();

        // Ensure that the user really has access to create event
        if (!$this->my->isSiteAdmin() && !$this->my->getAccess()->get('events.create')) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NOT_ALLOWED_TO_CREATE_EVENT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Ensure that the user did not exceed his limits
        if (!$this->my->isSiteAdmin() && $this->my->getAccess()->intervalExceeded('events.limit', $this->my->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_EXCEEDED_CREATE_EVENT_LIMIT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Get the category id
        $id = $this->input->get('category_id', 0, 'int');

        // Try to load the category
        $category = FD::table('EventCategory');
        $category->load($id);

        if (!$category->id || !$id) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_CATEGORY_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Get the group id to see if this is coming from group event creation
        $groupId = $this->input->getInt('group_id');

        // Check the group access for event creation
        if (!empty($groupId)) {
            $group = FD::group($groupId);

            if (!$group->canCreateEvent()) {
                $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NOT_ALLOWED_TO_CREATE_EVENT'), SOCIAL_MSG_ERROR);

                return $this->view->call(__FUNCTION__);
            }
        }

        $session = JFactory::getSession();
        $session->set('category_id', $category->id, SOCIAL_SESSION_NAMESPACE);

        $stepSession = FD::table('StepSession');
        $stepSession->load(array('session_id' => $session->getId(), 'type' => SOCIAL_TYPE_EVENT));

        $stepSession->session_id = $session->getId();
        $stepSession->uid = $category->id;
        $stepSession->type = SOCIAL_TYPE_EVENT;

        $stepSession->set('step', 1);

        $stepSession->addStepAccess(1);

        // Support for group events
        if (!empty($groupId)) {
            $group = FD::group($groupId);

            if (!$group->canCreateEvent()) {
                $this->view->setError(JText::_('COM_EASYSOCIAL_GROUPS_EVENTS_NO_PERMISSION_TO_CREATE_EVENT'));

                return $this->view->call(__FUNCTION__);
            }

            $stepSession->setValue('group_id', $groupId);
        } else {
            // Check if there is a group id set in the session, if yes then remove it

            if (!empty($stepSession->values)) {
                $value = FD::makeObject($stepSession->values);

                unset($value->group_id);

                $stepSession->values = FD::json()->encode($value);
            }
        }

        $stepSession->store();

        return $this->view->call(__FUNCTION__);
    }

    /**
     * Whenever user clicks on the next step during event creation
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function saveStep()
    {
        // Check for request forgeries
        FD::checkToken();

        // Require user to be logged in
        FD::requireLogin();

        // Check if the user is allowed to create events
        if (!$this->my->isSiteAdmin() && !$this->my->getAccess()->get('events.create')) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NOT_ALLOWED_TO_CREATE_EVENT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Check if the user exceeds the limit
        if (!$this->my->isSiteAdmin() && $this->my->getAccess()->intervalExceeded('events.limit', $this->my->id) ) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_EXCEEDED_CREATE_EVENT_LIMIT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Get the session data
        $session = JFactory::getSession();
        $stepSession = FD::table('StepSession');
        $stepSession->load(array('session_id' => $session->getId(), 'type' => SOCIAL_TYPE_EVENT));

        if (empty($stepSession->step)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_UNABLE_TO_DETECT_CREATION_SESSION'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $category = FD::table('EventCategory');
        $category->load($stepSession->uid);
        $sequence = $category->getSequenceFromIndex($stepSession->step, SOCIAL_EVENT_VIEW_REGISTRATION);

        if (empty($sequence)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_VALID_CREATION_STEP'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Load the steps and fields
        $step = FD::table('FieldStep');
        $step->load(array('uid' => $category->id, 'type' => SOCIAL_TYPE_CLUSTERS, 'sequence' => $sequence));

        $registry = FD::registry();
        $registry->load($stepSession->values);

        // Get the fields
        $fieldsModel  = FD::model('Fields');
        $customFields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'visible' => SOCIAL_EVENT_VIEW_REGISTRATION));

        // Get from request
        $files = JRequest::get('FILES');
        $post  = JRequest::get('POST');
        $token = FD::token();
        $json  = FD::json();

        foreach ($post as $key => $value) {
            if ($key == $token) {
                continue;
            }

            if (is_array($value)) {
                $value = $json->encode($value);
            }

            $registry->set($key, $value);
        }

        $data = $registry->toArray();

        $args = array(&$data, &$stepSession);

        // Load up the fields library so we can trigger the field apps
        $fieldsLib = FD::fields();

        $callback  = array($fieldsLib->getHandler(), 'validate');

        $errors = $fieldsLib->trigger('onRegisterValidate', SOCIAL_FIELDS_GROUP_EVENT, $customFields, $args, $callback);

        $stepSession->values = $json->encode($data);

        $stepSession->store();

        if (!empty($errors)) {
            $stepSession->setErrors($errors);

            $stepSession->store();

            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_ERRORS_IN_FORM'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__, $stepSession);
        }

        $completed = $step->isFinalStep(SOCIAL_EVENT_VIEW_REGISTRATION);

        $stepSession->created = FD::date()->toSql();

        $nextStep = $step->getNextSequence(SOCIAL_EVENT_VIEW_REGISTRATION);

        if ($nextStep) {
            $nextIndex = $stepSession->step + 1;
            $stepSession->step = $nextIndex;
            $stepSession->addStepAccess($nextIndex);
        }

        $stepSession->store();

        // If there's still other steps, continue with the rest of the steps
        if (!$completed) {
            return $this->view->call(__FUNCTION__, $stepSession);
        }

        // Here we assume that the user completed all the steps
        $eventsModel = FD::model('Events');

        // Create the new event
        $event = $eventsModel->createEvent($stepSession);

        if (!$event->id) {
            $errors = $eventsModel->getError();

            $this->view->setMessage($errors, SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__, $stepSession);
        }

        // Assign points to the user for creating event
        FD::points()->assign('events.create', 'com_easysocial', $this->my->id);

        // add this action into access logs.
        FD::access()->log('events.limit', $this->my->id, $event->id, SOCIAL_TYPE_EVENT);

        // If there is recurring data, then we back up the session->values and the recurring data in the the event params first before deleting step session
        if (!empty($event->recurringData)) {
            $clusterTable = FD::table('Cluster');
            $clusterTable->load($event->id);
            $eventParams = FD::makeObject($clusterTable->params);
            $eventParams->postdata = FD::makeObject($stepSession->values);
            $eventParams->recurringData = $event->recurringData;
            $clusterTable->params = FD::json()->encode($eventParams);
            $clusterTable->store();
        }

        $stepSession->delete();

        if ($event->isPublished()) {

            // Create new stream item
            if ($this->config->get('events.stream.create')) {
                $event->createStream('create', $event->creator_uid, $event->creator_type);
            }

            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_CREATED_SUCCESSFULLY'), SOCIAL_MSG_SUCCESS);

        } else {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_CREATED_PENDING_APPROVAL'), SOCIAL_MSG_INFO);
        }

        return $this->view->call('complete', $event);
    }

    /**
     * Update an event
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function update()
    {
        // Check for request forgeries
        FD::checkToken();

        // Ensure that the user is logged in
        FD::requireLogin();

        // Get the event data
        $id = $this->input->get('id', 0, 'int');

        // Load up the event
        $event = FD::event($id);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        if (!$event->isPublished()) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $guest = $event->getGuest($this->my->id);

        if (!$this->my->isSiteAdmin() && !$guest->isOwner() && !$event->isAdmin() && (!$event->isGroupEvent() || ($event->isGroupEvent() && !$event->getGroup()->isOwner()))) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NOT_ALLOWED_TO_EDIT_EVENT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__, $event);
        }

        $post = JRequest::get('POST');

        $json = FD::json();
        $data = array();

        $disallowed = array(FD::token(), 'option', 'task', 'controller');

        foreach ($post as $key => $value) {
            if (in_array($key, $disallowed)) {
                continue;
            }

            if (is_array($value)) {
                $value = $json->encode($value);
            }

            $data[$key] = $value;
        }

        $fieldsModel = FD::model('Fields');

        $fields = FD::model('Fields')->getCustomFields(array('group' => SOCIAL_TYPE_EVENT, 'uid' => $event->getCategory()->id, 'visible' => SOCIAL_EVENT_VIEW_EDIT, 'data' => true, 'dataId' => $event->id, 'dataType' => SOCIAL_TYPE_EVENT));

        $fieldsLib = FD::fields();

        $args = array(&$data, &$event);

        $errors = $fieldsLib->trigger('onEditValidate', SOCIAL_FIELDS_GROUP_EVENT, $fields, $args, array($fieldsLib->getHandler(), 'validate'));

        if (!empty($errors)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_ERRORS_IN_FORM'), SOCIAL_MSG_ERROR);

            JRequest::setVar('view', 'events', 'POST');
            JRequest::setVar('layout', 'edit', 'POST');

            JRequest::set($data, 'POST');

            return $this->view->call('edit', $errors);
        }

        $errors = $fieldsLib->trigger('onEditBeforeSave', SOCIAL_FIELDS_GROUP_EVENT, $fields, $args, array($fieldsLib->getHandler(), 'beforeSave'));

        if (!empty($errors)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_ERRORS_IN_FORM'), SOCIAL_MSG_ERROR);

            JRequest::setVar('view', 'events', 'POST');
            JRequest::setVar('layout', 'edit', 'POST');

            JRequest::set($data, 'POST');

            return $this->view->call('edit', $errors);
        }

        $event->save();

        FD::points()->assign('events.update', 'com_easysocial', $this->my->id);

        $fieldsLib->trigger('onEditAfterSave', SOCIAL_FIELDS_GROUP_EVENT, $fields, $args);

        $event->bindCustomFields($data);

        $fieldsLib->trigger('onEditAfterSaveFields', SOCIAL_FIELDS_GROUP_EVENT, $fields, $args);

        // Only create if applyRecurring is false or event is not a child
        // applyRecurring && parent = true
        // applyRecurring && child = false
        // !applyRecurring && parent = true
        // !applyRecurring && child = true
        if (empty($data['applyRecurring']) || !$event->isRecurringEvent()) {
            $event->createStream('update', $this->my->id, SOCIAL_TYPE_USER);
        }

        $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_UPDATED_SUCCESSFULLY'), SOCIAL_MSG_SUCCESS);

        return $this->view->call(__FUNCTION__, $event);
    }

    /**
     * Allows caller to respond to an event
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function guestResponse()
    {
        // Check for request forgeries
        FD::checkToken();

        // Only allow logged in users
        FD::requireLogin();

        // Get the current event id
        $id = $this->input->get('id', 0, 'int');

        // Load the event
        $event = FD::event($id);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        if (!$event->isPublished()) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Determine the guest object
        $guest = $event->getGuest($this->my->id);

        // Get the state
        $state = $this->input->get('state', '', 'word');

        if (
            ($event->isClosed() && (
                (!$guest->isParticipant() && $state !== 'request') || ($guest->isPending() && $state !== 'withdraw')
               )
           ) || ($event->isInviteOnly() && !$guest->isParticipant())) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $guest->cluster_id = $id;

        $access = $this->my->getAccess();
        $total = $this->my->getTotalEvents();

        if (in_array($state, array('going', 'maybe', 'request')) && !$access->get('events.allow.join') && $access->exceeded('events.join', $total)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_EXCEEDED_JOIN_EVENT_LIMIT'), SOCIAL_MSG_ERROR);
            return $this->view->call(__FUNCTION__);
        }

        switch ($state) {
            case 'going':
                $guest->going();
            break;

            case 'notgoing':
                // Depending on the event settings
                // It is possible that if user is not going, then admin doesn't want the user to continue be in the group.

                // If guest is owner, admin or siteadmin, or this event allows not going guest then allow notgoing state
                // If guest is just a normal user, then we return state as 'notgoingdialog' so that the JS part can show a dialog to warn user about it.
                if ($event->getParams()->get('allownotgoingguest', true) || $guest->isOwner()) {
                    $guest->notGoing();
                } else {
                    $guest->withdraw();
                }
            break;

            case 'maybe':
                $guest->maybe();
            break;

            case 'request':
                $guest->request();
            break;

            case 'withdraw':
                $guest->withdraw();
            break;

            default:
                $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_GUEST_STATE'), SOCIAL_MSG_ERROR);
                return $this->view->call(__FUNCTION__);
            break;
        }

        return $this->view->call(__FUNCTION__, $state);
    }

    public function getFilter()
    {
        FD::checkToken();

        FD::requireLogin();

        $eventId = JRequest::getInt('eventId');

        $event = FD::event($eventId);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        if (!$event->isPublished()) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $guest = $event->getGuest($this->my->id);

        if (!$guest->isGuest()) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $filterId = JRequest::getInt('filterId');

        $filter = FD::table('StreamFilter');

        $filter->load($filterId);

        return $this->view->call(__FUNCTION__, $event, $filter);
    }

    public function saveFilter()
    {
        FD::checkToken();

        FD::requireLogin();

        $eventId = JRequest::getInt('uid');

        $event = FD::event($eventId);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        if (!$event->isPublished()) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $guest = $event->getGuest($this->my->id);

        if (!$guest->isGuest()) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $post = JRequest::get('POST');

        $filterId = JRequest::getInt('id');

        $filter = FD::table('StreamFilter');

        $filter->load($filterId);

        $filter->title = $post['title'];
        $filter->uid = $event->id;
        $filter->utype = SOCIAL_TYPE_EVENT;
        $filter->user_id = $this->my->id;
        $filter->store();

        if ($post['hashtag']) {
            $hashtag = trim($post['hashtag']);
            $hashtag = str_replace('#', '', $hashtag);
            $hashtag = str_replace(' ', '', $hashtag);


            $filterItem = FD::table('StreamFilterItem');
            $filterItem->load(array('filter_id' => $filter->id, 'type' => 'hashtag'));

            $filterItem->filter_id = $filter->id;
            $filterItem->type = 'hashtag';
            $filterItem->content = $hashtag;

            $filterItem->store();
        } else {
            $filter->deleteItem('hashtag');
        }

        $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_STREAM_FILTER_SAVED'), SOCIAL_MSG_SUCCESS);

        return $this->view->call(__FUNCTION__);
    }

    public function initInfo()
    {
        FD::checkToken();

        $eventId = JRequest::getInt('eventId');

        $event = FD::event($eventId);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        if (!$event->isPublished()) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $guest = $event->getGuest($this->my->id);

        if (!$this->my->isSiteAdmin() && !$event->isOpen() && !$guest->isGuest() && (!$event->isGroupEvent() || ($event->isGroupEvent() && !$event->getGroup()->isMember()))) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        FD::language()->loadAdmin();

        $steps = FD::model('Steps')->getSteps($event->category_id, SOCIAL_TYPE_CLUSTERS, SOCIAL_EVENT_VIEW_DISPLAY);

        $fieldsLib = FD::fields();

        $fieldsLib->init(array('privacy' => false));

        $fieldsModel = FD::model('Fields');

        $index = 1;

        foreach ($steps as $step) {
            $step->fields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $event->id, 'dataType' => SOCIAL_TYPE_EVENT, 'visible' => SOCIAL_EVENT_VIEW_DISPLAY));

            if (!empty($step->fields)) {
                $args = array($event);

                $fieldsLib->trigger('onDisplay', SOCIAL_FIELDS_GROUP_EVENT, $step->fields, $args);
            }

            $step->hide = true;

            foreach ($step->fields as $field) {
                // As long as one of the field in the step has an output, then this step shouldn't be hidden
                // If step has been marked false, then no point marking it as false again
                // We don't break from the loop here because there is other checking going on
                if (!empty($field->output) && $step->hide === true ) {
                    $step->hide = false;
                }
            }

            if ($index === 1) {
                $step->url = FRoute::events(array('layout' => 'item', 'id' => $event->getAlias(), 'type' => 'info'), false);
            } else {
                $step->url = FRoute::events(array('layout' => 'item', 'id' => $event->getAlias(), 'type' => 'info', 'infostep' => $index), false);
            }

            $step->title = $step->get('title');

            $step->active = !$step->hide && $index == 1;

            if ($step->active) {
                $theme = FD::themes();

                $theme->set('fields', $step->fields);

                $step->html = $theme->output('site/events/item.info');
            }

            $step->index = $index;

            $index++;
        }

        return $this->view->call(__FUNCTION__, $steps);
    }


    /**
     * Make a user an admin of an event.
     *
     * @since   1.3
     * @access  public
     */
    public function promoteGuest()
    {
        // Check for request forgeries
        FD::checkToken();

        // Require the user to be logged in
        FD::requireLogin();

        // Get the guest object
        $guest = FD::table('EventGuest');
        $state = $guest->load($this->input->getInt('id'));

        if (!$state || empty($guest->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_GUEST_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Get the event object
        $event = FD::event($guest->cluster_id);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $my = FD::user();
        $myGuest = $event->getGuest();

        if ($myGuest->isAdmin() || $my->isSiteAdmin()) {
            $guest->makeAdmin();
        } else {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);
        }

        return $this->view->call(__FUNCTION__);
    }

    /**
     * Revokes a user's admin rights.
     *
     * @since   1.3
     * @access  public
     */
    public function demoteGuest()
    {
        // Check for request forgeries
        FD::checkToken();

        // Require the user to be logged in
        FD::requireLogin();

        // Get the guest object
        $guest = FD::table('EventGuest');
        $state = $guest->load($this->input->getInt('id'));

        if (!$state || empty($guest->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_GUEST_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Get the event object
        $event = FD::event($guest->cluster_id);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $my = FD::user();
        $myGuest = $event->getGuest();

        if (($my->isSiteAdmin() || $myGuest->isOwner()) && $guest->isStrictlyAdmin()) {
            $guest->revokeAdmin();
        } else {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);
        }


        return $this->view->call(__FUNCTION__);
    }

    /**
     * Allows event owner to remove the event avatar
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function removeAvatar()
    {
        // Check for request forgeries
        FD::checkToken();

        // Get the current view
        $view = $this->getCurrentView();

        // Load the event
        $id = $this->input->get('id', 0, 'int');
        $event = FD::event($id);

        // Only allow event admins to remove avatar
        if (!$event->isAdmin()) {
            $view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);

            return $view->call(__FUNCTION__, $event);
        }

        // Try to remove the avatar from the event now
        $event->removeAvatar();

        $view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_AVATAR_REMOVED_SUCCESSFULLY'), SOCIAL_MSG_SUCCESS);

        return $view->call(__FUNCTION__, $event);
    }

    /**
     * Remove a guest from an event.
     *
     * @since   1.3
     * @access  public
     */
    public function removeGuest()
    {
        // Check for request forgeries
        FD::checkToken();

        // Require the user to be logged in
        FD::requireLogin();

        // Get the guest object
        $guest = FD::table('EventGuest');
        $state = $guest->load($this->input->getInt('id'));

        if (!$state || empty($guest->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_GUEST_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Get the event object
        $event = FD::event($guest->cluster_id);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $my = FD::user();
        $myGuest = $event->getGuest();

        if (!$guest->isOwner() && ($my->isSiteAdmin() || $myGuest->isOwner() || ($myGuest->isAdmin() && !$guest->isAdmin()))) {
            $guest->remove();
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_GUEST_REMOVAL_SUCCESS'), SOCIAL_MSG_SUCCESS);
            return $this->view->call(__FUNCTION__, $event);
        } else {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);
        }

        return $this->view->call(__FUNCTION__);
    }

    /**
     * Approve a guest.
     *
     * @since   1.3
     * @access  public
     */
    public function approveGuest()
    {
        // Check for request forgeries
        FD::checkToken();

        // Require the user to be logged in
        FD::requireLogin();

        // Get the guest object
        $guest = FD::table('EventGuest');
        $state = $guest->load($this->input->getInt('id'));

        if (!$state || empty($guest->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_GUEST_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Get the event object
        $event = FD::event($guest->cluster_id);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $my = FD::user();
        $myGuest = $event->getGuest();

        if (($my->isSiteAdmin() || $myGuest->isAdmin()) && $guest->isPending()) {
            $guest->approve();
        } else {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);
        }

        return $this->view->call(__FUNCTION__);
    }

    /**
     * Reject a guest.
     *
     * @since   1.3
     * @access  public
     */
    public function rejectGuest()
    {
        // Check for request forgeries
        FD::checkToken();

        // Require the user to be logged in
        FD::requireLogin();

        // Get the guest object
        $guest = FD::table('EventGuest');
        $state = $guest->load($this->input->getInt('id'));

        if (!$state || empty($guest->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_GUEST_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Get the event object
        $event = FD::event($guest->cluster_id);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $my = FD::user();
        $myGuest = $event->getGuest();

        if (($my->isSiteAdmin() || $myGuest->isAdmin()) && $guest->isPending()) {
            $guest->reject();
        } else {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);
        }

        return $this->view->call(__FUNCTION__);
    }

    public function getInfo()
    {
        FD::checkToken();

        $eventId = JRequest::getInt('eventId');

        $event = FD::event($eventId);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        if (!$event->isPublished()) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $guest = $event->getGuest($this->my->id);

        if (!$this->my->isSiteAdmin() && !$event->isOpen() && !$guest->isGuest() && (!$event->isGroupEvent() || ($event->isGroupEvent() && !$event->getGroup()->isMember()))) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        FD::language()->loadAdmin();

        $index = JRequest::getInt('index');

        $category = $event->getCategory();

        $sequence = $category->getSequenceFromIndex($index, SOCIAL_EVENT_VIEW_DISPLAY);

        $step = FD::table('FieldStep');
        $state = $step->load(array('uid' => $category->id, 'type' => SOCIAL_TYPE_CLUSTERS, 'sequence' => $sequence, 'visible_display' => 1));

        if (!$state) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT_INFO'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $fields = FD::model('Fields')->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $event->id, 'dataType' => SOCIAL_TYPE_EVENT, 'visible' => SOCIAL_EVENT_VIEW_DISPLAY));

        $fieldsLib = FD::fields();

        $fieldsLib->init(array('privacy' => false));

        if (!empty($fields)) {
            $args = array($event);

            $fieldsLib->trigger('onDisplay', SOCIAL_FIELDS_GROUP_EVENT, $fields, $args);
        }

        return $this->view->call(__FUNCTION__, $fields);
    }

    /**
     * Retrieves the events stream
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function getStream()
    {
        // Check for request forgeries
        FD::checkToken();

        // Get the event object
        $id = $this->input->get('eventId', 0, 'int');
        $event = FD::event($id);

        if (!$event && !$event->id) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        if (!$event->isPublished()) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $guest = $event->getGuest($this->my->id);

        if (!$this->my->isSiteAdmin() && !$event->isOpen() && !$guest->isGuest() && (!$event->isGroupEvent() || ($event->isGroupEvent() && !$event->getGroup()->isMember()))) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Load up the stream
        $stream = FD::stream();

        if ($guest->isGuest()) {
            $story  = FD::get('Story', $event->cluster_type);
            $story->setCluster($event->id, $event->cluster_type);
            $story->showPrivacy(false);
            $stream->story = $story;
        }

        //lets get the sticky posts 1st
        $stickies = $stream->getStickies(array('clusterId' => $event->id, 'clusterType' => $event->cluster_type, 'limit' => 0));
        if ($stickies) {
            $stream->stickies = $stickies;
        }

        $streamOptions = array('clusterId' => $event->id, 'clusterType' => $event->cluster_type, 'nosticky' => true);

        // Get the filter type
        $filterType = $this->input->get('type', '', 'cmd');

        if ($filterType === 'hashtag') {

            $filterId = JRequest::getInt('id');

            $filter = FD::table('StreamFilter');
            $filter->load($filterId);

            $hashtags = $filter->getHashTag();
            $tags = explode(',', $hashtags);

            if (!empty($tags)) {
                $streamOptions['tag'] = $tags;
            }
        }

        if ($filterType === 'apps') {
            $filterId = JRequest::getWord('id');

            $streamOptions['context'] = $filterId;
        }

        $stream->get($streamOptions);

        return $this->view->call(__FUNCTION__, $stream);
    }

    /**
     * Retrieves the dashboard contents.
     *
     * @since   1.3
     * @access  public
     */
    public function getAppContents()
    {
        // Check for request forgeries.
        FD::checkToken();

        // In order to access the dashboard apps, user must be logged in.
        FD::requireLogin();

        // Get the event id
        $eventId = $this->input->get('eventId', 0, 'int');

        // Try to load the event
        $event = FD::event($eventId);

        if (!$eventId || !$event) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);
            return $this->view->call(__FUNCTION__);
        }

        // If the user is not allowed to view the contents of the event
        if (!$event->canViewItem() && !$this->my->isSiteAdmin()) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS'), SOCIAL_MSG_ERROR);
            return $this->view->call(__FUNCTION__);
        }

        // Get the app id.
        $appId = $this->input->get('appId', 0, 'int');

        // Load application.
        $app = FD::table('App');
        $state = $app->load($appId);

        // If application id is not valid, throw an error.
        if (!$appId || !$state) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_APPS_INVALID_APP_ID_PROVIDED'), SOCIAL_MSG_ERROR);
            return $this->view->call(__FUNCTION__, $app);
        }

        return $this->view->call(__FUNCTION__, $app);
    }

    public function inviteFriends()
    {
        FD::checkToken();

        FD::requireLogin();

        $eventId = $this->input->get('id', 0, 'int');

        $event = FD::event($eventId);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        if (!$event->isPublished()) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $guest = $event->getGuest($this->my->id);

        if (!$guest->isGuest()) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $ids = $this->input->get('uid', array(), 'var');

        if (empty($ids)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_USER_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        foreach ($ids as $id) {
            $guest = $event->getGuest($id);

            if (!$guest->isGuest()) {
                $event->invite($id, $this->my->id);
            }
        }

        return $this->view->call(__FUNCTION__);
    }

    public function approveEvent()
    {
        $id = $this->input->getInt('id', 0);

        $event = FD::event($id);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Check the key
        $key = $this->input->getString('key');

        if ($key != $event->key) {
            $this->view->setMessage(JText::sprintf('COM_EASYSOCIAL_EVENTS_NO_ACCESS', $event->getName()), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $state = $event->approve();

        if (!$state) {
            $this->view->setMessage(JText::sprintf('COM_EASYSOCIAL_EVENTS_EVENT_APPROVE_FAILED', $event->getName()), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $this->view->setMessage(JText::sprintf('COM_EASYSOCIAL_EVENTS_EVENT_APPROVE_SUCCESS', $event->getName()), SOCIAL_MSG_SUCCESS);

        return $this->view->call(__FUNCTION__, $event);
    }

    public function rejectEvent()
    {
        $id = $this->input->getInt('id', 0);

        $event = FD::event($id);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Check the key
        $key = $this->input->getString('key');

        if ($key != $event->key) {
            $this->view->setMessage(JText::sprintf('COM_EASYSOCIAL_EVENTS_NO_ACCESS', $event->getName()), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $state = $event->reject();

        if (!$state) {
            $this->view->setMessage(JText::sprintf('COM_EASYSOCIAL_EVENTS_EVENT_REJECT_FAILED', $event->getName()), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $this->view->setMessage(JText::sprintf('COM_EASYSOCIAL_EVENTS_EVENT_REJECT_SUCCESS', $event->getName()), SOCIAL_MSG_SUCCESS);

        return $this->view->call(__FUNCTION__, $event);
    }

    public function itemAction()
    {
        $id = $this->input->getInt('id', 0);

        $event = FD::event($id);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $guest = $event->getGuest();

        // Support for group events
        if (!$this->my->isSiteAdmin() && !$guest->isAdmin() && !$guest->isOwner() && (!$event->isGroupEvent() || ($event->isGroupEvent() && !$event->getGroup()->isOwner()))) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $action = $this->input->getString('action');

        // For delete actions, the user has to be an admin or owner
        if ($action == 'delete' && !$this->my->isSiteAdmin() && !$guest->isOwner() && (!$event->isGroupEvent() || ($event->isGroupEvent() && !$event->getGroup()->isOwner()))) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Recurring support
        $mode = $this->input->getString('deleteMode', 'this');

        // Special handling needed for delete and if delete all
        if ($action == 'delete' && $mode == 'all' && ($event->isRecurringEvent() || $event->hasRecurringEvents())) {

            $parentId = $id;

            // Check if event is a parent event
            if ($event->isRecurringEvent()) {
                $parentId = $event->parent_id;
            }

            // Have to delete recurring events first
            FD::model('Events')->deleteRecurringEvents($parentId);

            $parent = FD::event($parentId);
            $parent->delete();
        } else {
            $event->$action();
        }

        // COM_EASYSOCIAL_EVENTS_EVENT_FEATURE_SUCCESS
        // COM_EASYSOCIAL_EVENTS_EVENT_DELETE_SUCCESS
        // COM_EASYSOCIAL_EVENTS_EVENT_UNFEATURE_SUCCESS
        // COM_EASYSOCIAL_EVENTS_EVENT_UNPUBLISH_SUCCESS
        $this->view->setMessage(JText::sprintf('COM_EASYSOCIAL_EVENTS_EVENT_' . strtoupper($action) . '_SUCCESS', $event->getName()), SOCIAL_MSG_SUCCESS);

        return $this->view->call(__FUNCTION__, $event);
    }


    /**
     * Service Hook for explorer
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function explorer()
    {
        // Check for request forgeries
        Foundry::checkToken();

        // Require the user to be logged in
        Foundry::requireLogin();

        // Get the current view
        $view       = $this->getCurrentView();

        // Get the group object
        $eventId    = $this->input->getint( 'uid' );
        $event      = FD::event( $eventId );

        // Determines if the current user is a guest of this event
        $guest = $event->getGuest($this->my->id);

        if (!$this->my->isSiteAdmin() && $event->isInviteOnly() && !$guest->isInvited() && !$guest->isGuest()) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);
            return $this->view->call(__FUNCTION__);
        }

        // Load up the explorer library
        $explorer   = Foundry::explorer( $event->id , SOCIAL_TYPE_EVENT );
        $hook       = JRequest::getCmd( 'hook' );

        $result     = $explorer->hook( $hook );

        $exception  = Foundry::exception( 'Folder retrieval successful' , SOCIAL_MSG_SUCCESS );

        return $view->call( __FUNCTION__ , $exception , $result );
    }

    public function deleteFilter()
    {
        // Check for request forgeries.
        FD::checkToken();

        // In order to access the dashboard apps, user must be logged in.
        FD::requireLogin();

        $view = $this->getCurrentView();

        $my = FD::user();

        $id = JRequest::getInt('id', 0);
        $eventId = JRequest::getInt('uid', 0);

        if (!$id) {
            FD::info()->set(JText::_('Invalid filter id - ' . $id) , 'error');
            $view->setError(JText::_('Invalid filter id.'));
            return $view->call(__FUNCTION__);
        }

        $filter = FD::table('StreamFilter');

        // make sure the user is the filter owner before we delete.
        $filter->load(array('id' => $id, 'uid' => $eventId, 'utype' => SOCIAL_TYPE_EVENT));

        if (!$filter->id) {
            FD::getInstance('Info')->set(JText::_('Filter not found - ' . $id) , 'error');
            $view->setError(JText::_('Filter not found. Action aborted.'));
            return $view->call(__FUNCTION__);
        }

        $filter->deleteItem();
        $filter->delete();

        $view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_FILTER_DELETED') , SOCIAL_MSG_SUCCESS);

        return $view->call(__FUNCTION__, $eventId);
    }

    public function createRecurring()
    {
        // Check for request forgeries.
        FD::checkToken();

        // In order to access the dashboard apps, user must be logged in.
        FD::requireLogin();

        $eventId = $this->input->getInt('eventId');

        $schedule = $this->input->getString('datetime');

        $parentEvent = FD::event($eventId);

        $duration = $parentEvent->hasEventEnd() ? $parentEvent->getEventEnd()->toUnix() - $parentEvent->getEventStart()->toUnix() : false;

        // Get the data from the event params

        $data = FD::makeArray($parentEvent->getParams()->get('postdata'));

        // Mark the data as createRecurring
        $data['createRecurring'] = true;

        // Manually change the start end time
        $data['startDatetime'] = FD::date($schedule)->toSql();

        if ($duration) {
            $data['endDatetime'] = FD::date($schedule + $duration)->toSql();
        } else {
            unset($data['endDatetime']);
        }

        $event = FD::model('Events')->createRecurringEvent($data, $parentEvent);

        // Duplicate nodes from parent
        FD::model('Events')->duplicateGuests($parentEvent->id, $event->id);

        return $this->view->call(__FUNCTION__);
    }

    public function deleteRecurring()
    {
        // Check for request forgeries.
        FD::checkToken();

        // In order to access the dashboard apps, user must be logged in.
        FD::requireLogin();

        $eventId = $this->input->getInt('eventId');

        $event = FD::event($eventId);

        if (empty($event) || empty($event->id)) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        $guest = $event->getGuest();

        $guest = $event->getGuest();

        // Support for group events
        if (!$this->my->isSiteAdmin() && !$guest->isOwner() && (!$event->isGroupEvent() || ($event->isGroupEvent() && !$event->getGroup()->isOwner()))) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'), SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        FD::model('Events')->deleteRecurringEvents($event->id);

        return $this->view->call(__FUNCTION__);
    }

}
