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

FD::import('admin:/views/views');

class EasySocialViewEvents extends EasySocialAdminView
{
    /**
     * Displays the listings of events at the back end
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function display($tpl = null)
    {
        $this->setHeading('COM_EASYSOCIAL_EVENTS_TITLE');
        $this->setDescription('COM_EASYSOCIAL_EVENTS_DESCRIPTION');

        JToolbarHelper::addNew('create', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_NEW'), false);
        JToolbarHelper::divider();
        JToolbarHelper::custom('switchOwner', 'vcard', '', JText::_('COM_EASYSOCIAL_CHANGE_OWNER'));
        JToolbarHelper::custom('switchCategory', 'folder', '', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SWITCH_CATEGORY'));
        JToolbarHelper::divider();
        JToolbarHelper::publishList('publish');
        JToolbarHelper::unpublishList('unpublish');
        JToolbarHelper::divider();
        JToolbarHelper::custom('makeFeatured', 'featured', '', JText::_('COM_EASYSOCIAL_MAKE_FEATURED'));
        JToolbarHelper::custom('removeFeatured', 'star', '', JText::_('COM_EASYSOCIAL_REMOVE_FEATURED'));
        JToolbarHelper::divider();
        JToolbarHelper::deleteList('', 'delete', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_DELETE'));

        $model = FD::model('Events', array('initState' => true));

        $events = $model->getItems();

        $pagination = $model->getPagination();

        $this->set('events', $events);
        $this->set('pagination', $pagination);

        $search = $model->getState('search');
        $ordering = $model->getState('ordering');
        $direction = $model->getState('direction');
        $state = $model->getState('state');
        $type = $model->getState('type');
        $limit = $model->getState('limit');
        $tmpl = $this->input->getVar('tmpl');

        $this->set('search', $search);
        $this->set('ordering', $ordering);
        $this->set('direction', $direction);
        $this->set('state', $state);
        $this->set('type', $type);
        $this->set('limit', $limit);
        $this->set('tmpl', $tmpl);

        echo parent::display('admin/events/default');
    }

    /**
     * Display function for creating an event.
     *
     * @since  1.3
     * @access public
     */
    public function form($errors = array())
    {
        JToolbarHelper::apply('apply', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE'), false, false);
        JToolbarHelper::save('save', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_CLOSE'));
        JToolbarHelper::save2new('savenew', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_NEW'));
        JToolbarHelper::divider();
        JToolbarHelper::cancel('cancel', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_CANCEL'));

        FD::language()->loadSite();

        $id = $this->input->get('id', 0, 'int');

        $event = FD::event($id);

        $category = FD::table('EventCategory');

        $isNew = empty($event->id);

        $this->setHeading('COM_EASYSOCIAL_EVENTS_CREATE_EVENT_TITLE');
        $this->setDescription('COM_EASYSOCIAL_EVENTS_CREATE_EVENT_DESCRIPTION');

        // Set the structure heading here.
        if (!$isNew) {
            $this->setHeading($event->title);
            $this->setDescription('COM_EASYSOCIAL_EVENTS_EDIT_EVENT_DESCRIPTION');

            $category->load($event->category_id);
        } else {
            // By default the published state should be published.
            $event->state = SOCIAL_STATE_PUBLISHED;

            $categoryId = JRequest::getInt('category_id');
            $category->load($categoryId);
        }

        $stepsModel = FD::model('steps');
        $steps = $stepsModel->getSteps($category->id, SOCIAL_TYPE_CLUSTERS);

        $fieldsLib = FD::fields();
        $fieldsModel = FD::model('Fields');

        $post = JRequest::get('post');
        $args = array(&$post, &$event, &$errors);

        foreach ($steps as &$step) {

            $options = array('step_id' => $step->id);

            if (!$isNew) {
                $options['data'] = true;
                $options['dataId'] = $event->id;
                $options['dataType'] = SOCIAL_TYPE_EVENT;
            }

            $step->fields = $fieldsModel->getCustomFields($options);

            if (!empty($step->fields)) {
                $fieldsLib->trigger('onAdminEdit', SOCIAL_FIELDS_GROUP_EVENT, $step->fields, $args);
            }
        }

        $this->set('event', $event);
        $this->set('steps', $steps);
        $this->set('category', $category);

        $guestModel = FD::model('EventGuests', array('initState' => true));
        $guests = $guestModel->getItems(array('eventid' => $event->id));

        $this->set('guests', $guests);
        $this->set('ordering', $guestModel->getState('ordering'));
        $this->set('direction', $guestModel->getState('direction'));
        $this->set('limit', $guestModel->getState('limit'));
        $this->set('pagination', $guestModel->getPagination());


        $activeTab = JRequest::getWord('activeTab', 'event');
        $this->set('activeTab', $activeTab);

        $this->set('isNew', $isNew);

        return parent::display('admin/events/form');
    }

    /**
     * Post action after storing an event to redirect to the appropriate page according to the task.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  string       $task   The task action.
     * @param  SocialEvent  $event  The event object.
     */
    public function store($task, $event)
    {
        // Recurring support
        // If applies to all, we need to show a "progress update" page to update all childs through ajax.
        $applyAll = $event->hasRecurringEvents() && $this->input->getInt('applyRecurring');

        // Check if need to create recurring event
        $createRecurring = !empty($event->recurringData);

        if (!$applyAll && !$createRecurring) {
            FD::info()->set($this->getMessage());

            if ($task === 'apply') {
                $activeTab = JRequest::getWord('activeTab', 'event');
                return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'form', 'id' => $event->id, 'activeTab' => $activeTab)));
            }

            if ($task === 'savenew') {
                return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'form', 'category_id' => $event->category_id)));
            }

            return $this->redirect(FRoute::url(array('view' => 'events')));
        }

        $this->setHeading('COM_EASYSOCIAL_EVENTS_APPLYING_RECURRING_EVENT_CHANGES');
        $this->setDescription('COM_EASYSOCIAL_EVENTS_APPLYING_RECURRING_EVENT_CHANGES_DESCRIPTION');

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

        $string = $json->encode($data);

        $this->set('data', $string);

        $this->set('event', $event);

        $updateids = array();

        if ($applyAll) {
            $children = $event->getRecurringEvents();

            foreach ($children as $child) {
                $updateids[] = $child->id;
            }
        }

        $this->set('updateids', $json->encode($updateids));

        $schedule = array();

        if ($createRecurring) {
            // Get the recurring schedule
            $schedule = FD::model('Events')->getRecurringSchedule(array(
                'eventStart' => $event->getEventStart(),
                'end' => $event->recurringData->end,
                'type' => $event->recurringData->type,
                'daily' => $event->recurringData->daily
            ));
        }

        $this->set('schedule', $json->encode($schedule));

        $this->set('task', $task);

        return parent::display('admin/events/store');
    }

    /**
     * Post action of delete to redirect to event listing.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function delete()
    {
        FD::info()->set($this->getMessage());

        return $this->redirect(FRoute::url(array('view' => 'events')));
    }

    /**
     * Display function for pending event listing.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function pending($tpl = null)
    {
        // Check access
        if (!$this->authorise('easysocial.access.events')) {
            $this->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR') , 'error');
        }

        $this->setHeading('COM_EASYSOCIAL_PENDING_EVENTS_TITLE');
        $this->setDescription('COM_EASYSOCIAL_PENDING_EVENTS_DESCRIPTION');

        JToolbarHelper::custom('approve', 'publish', 'social-publish-hover', JText::_('COM_EASYSOCIAL_APPROVE_BUTTON'), true);
        JToolbarHelper::custom('reject', 'unpublish', 'social-unpublish-hover', JText::_('COM_EASYSOCIAL_REJECT_BUTTON'), true);

        $model = FD::model('Events', array('initState' => true));

        $model->setState('state', SOCIAL_CLUSTER_PENDING);

        $events = $model->getItems();

        // Recurring support
        // Check if event is recurring event to add in the flag
        foreach ($events as $event) {
            $event->isRecurring = $event->getParams()->exists('recurringData');
        }

        $pagination = $model->getPagination();

        $this->set('events', $events);
        $this->set('pagination', $pagination);

        $search = $model->getState('search');
        $ordering = $model->getState('ordering');
        $direction = $model->getState('direction');
        $state = $model->getState('state');
        $type = $model->getState('type');
        $limit = $model->getState('limit');

        $this->set('search', $search);
        $this->set('ordering', $ordering);
        $this->set('direction', $direction);
        $this->set('state', $state);
        $this->set('type', $type);
        $this->set('limit', $limit);

        echo parent::display('admin/events/pending');
    }

    /**
     * Display function for event categories listing page.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     */
    public function categories($tpl = null)
    {
        // Check access
        if (!$this->authorise('easysocial.access.events')) {
            $this->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR') , 'error');
        }

        $this->setHeading('COM_EASYSOCIAL_EVENT_CATEGORIES_TITLE');
        $this->setDescription('COM_EASYSOCIAL_EVENT_CATEGORIES_DESCRIPTION');

        JToolbarHelper::addNew('categoryForm', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_NEW'), false);
        JToolbarHelper::divider();
        JToolbarHelper::publishList('publishCategory');
        JToolbarHelper::unpublishList('unpublishCategory');
        JToolbarHelper::divider();
        JToolbarHelper::deleteList('', 'deleteCategory', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_DELETE'));

        $model = FD::model('EventCategories', array('initState' => true));

        $categories = $model->getItems();

        $pagination = $model->getPagination();

        $this->set('categories', $categories);
        $this->set('pagination', $pagination);

        $search = $model->getState('search');
        $ordering = $model->getState('ordering');
        $direction = $model->getState('direction');
        $state = $model->getState('state');
        $limit = $model->getState('limit');

        $this->set('search', $search);
        $this->set('ordering', $ordering);
        $this->set('direction', $direction);
        $this->set('state', $state);
        $this->set('limit', $limit);

        $this->set('simple', $this->input->getString('tmpl') == 'component');

        echo parent::display('admin/events/categories');
    }

    /**
     * Display function for event category form.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     */
    public function categoryForm($tpl = null)
    {
        // Check access
        if (!$this->authorise('easysocial.access.events')) {
            $this->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR') , 'error');
        }

        $id = JRequest::getInt('id');

        $category = FD::table('EventCategory');
        $category->load($id);

        // Set the structure heading here.
        if ($category->id) {
            $this->setHeading($category->get('title'));
            $this->setDescription('COM_EASYSOCIAL_EVENT_CATEGORY_EDIT_DESCRIPTION');
        }
        else {
            $this->setHeading('COM_EASYSOCIAL_EVENT_CATEGORY_CREATE_TITLE');
            $this->setDescription('COM_EASYSOCIAL_EVENT_CATEGORY_CREATE_DESCRIPTION');

            // By default the published state should be published.
            $category->state = SOCIAL_STATE_PUBLISHED;
        }

        JToolbarHelper::apply('applyCategory', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE'), false, false);
        JToolbarHelper::save('saveCategory', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_CLOSE'));
        JToolbarHelper::save2new('saveCategoryNew', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_NEW'));
        JToolbarHelper::divider();
        JToolbarHelper::cancel('cancel', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_CANCEL'));

        $activeTab = JRequest::getWord('activeTab', 'settings');
        $createAccess = '';

        // Set properties for the template.
        $this->set('activeTab', $activeTab);
        $this->set('category', $category);

        if ($category->id) {
            FD::language()->loadSite();

            $options = array('type' => SOCIAL_APPS_TYPE_FIELDS, 'group' => SOCIAL_TYPE_EVENT, 'state' => SOCIAL_STATE_PUBLISHED);

            // Get the available custom fields for groups
            $appsModel = FD::model('Apps');
            $defaultFields = $appsModel->getApps($options);

            // Get the steps for this id
            $stepsModel = FD::model('Steps');
            $steps = $stepsModel->getSteps($category->id, SOCIAL_TYPE_CLUSTERS);

            // Get the fields for this id
            $fieldsModel = FD::model('Fields');
            $fields = $fieldsModel->getCustomFields(array('uid' => $category->id, 'state' => 'all', 'group' => SOCIAL_TYPE_EVENT));

            // Empty array to pass to the trigger.
            $data = array();

            // Get the fields sample output
            $lib = FD::fields();
            $lib->trigger('onSample', SOCIAL_TYPE_EVENT, $fields, $data, array($lib->getHandler(), 'getOutput'));

            // Create a temporary storage
            $tmpFields = array();

            // Group the fields to each workflow properly
            if ($steps) {
                foreach ($steps as $step) {
                    $step->fields = array();

                    if (!empty($fields)) {
                        foreach ($fields as $field) {
                            if ($field->step_id == $step->id) {
                                $step->fields[] = $field;
                            }

                            $tmpFields[ $field->app_id ]    = $field;
                        }
                    }
                }
            }

            // We need to know the amount of core apps and used core apps
            // 1.3 Update, we split out unique apps as well
            $coreAppsCount = 0;
            $usedCoreAppsCount = 0;
            $uniqueAppsCount = 0;
            $usedUniqueAppsCount = 0;

            // hide the apps if it is a core app and it is used in the field
            if ($defaultFields) {
                foreach ($defaultFields as $app) {
                    $app->hidden = false;

                    // If app is core, increase the coreAppsCount counter
                    if ($app->core) {
                        $coreAppsCount++;
                    }

                    // If app is NOT core and unique, increase the coreAppsCount counter
                    // This is because core apps are definitely unique, so we do not want to include core apps here
                    if (!$app->core && $app->unique) {
                        $uniqueAppsCount++;
                    }

                    // Test if this app has already been assigned to the $tmpFields
                    if (isset($tmpFields[$app->id]) && $app->core) {
                        $usedCoreAppsCount++;

                        $app->hidden = true;
                    }

                    // Test if this app is NOT core and unique and has already been assigned
                    // This is because core apps are definitely unique, so we do not want to include core apps here
                    if (isset($tmpFields[$app->id]) && !$app->core && $app->unique) {
                        $usedUniqueAppsCount++;

                        $app->hidden = true;
                    }
                }
            }

            unset($tmpFields);

            // Get the creation access
            $createAccess = $category->getAccess('create');

            // We need to know if there are any core apps remain
            $coreAppsRemain = $usedCoreAppsCount < $coreAppsCount;

            // We need to know if there are any unique apps remain
            $uniqueAppsRemain = $usedUniqueAppsCount < $uniqueAppsCount;

            // Set the profiles allowed to create groups
            $this->set('createAccess', $createAccess);

            // Set the flag of coreAppsRemain
            $this->set('coreAppsRemain', $coreAppsRemain);

            // Set the flag of uniqueAppsRemain
            $this->set('uniqueAppsRemain', $uniqueAppsRemain);

            // Set the default apps to the template.
            $this->set('defaultFields', $defaultFields);

            // Set the steps for the template.
            $this->set('steps', $steps);

            // Set the fields to the template
            $this->set('fields', $fields);

            // Set the field group type to the template
            $this->set('fieldGroup', SOCIAL_FIELDS_GROUP_EVENT);

            // Render the access form.
            $accessModel = FD::model('Access');
            $accessForm = $accessModel->getForm($category->id, SOCIAL_TYPE_EVENT, 'access');
            $this->set('accessForm' , $accessForm);
        }

        // Set the profiles allowed to create groups
        $this->set('createAccess', $createAccess);

        echo parent::display('admin/events/form.category');
    }

    /**
     * Post process for the task applyCategory, saveCategoryNew and saveCategory to redirect to the corresponding page.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   SocialTableEventCategory    $category The event category table object.
     */
    public function saveCategory($category)
    {
        FD::info()->set($this->getMessage());

        $activeTab = $this->input->getString('activeTab', 'settings');

        if ($this->hasErrors()) {
            return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'categoryForm', 'activeTab' => $activeTab)));
        }

        $task = JRequest::getVar('task');

        if ($task === 'applyCategory') {
            return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'categoryForm', 'id' => $category->id, 'activeTab' => $activeTab)));
        }

        if ($task === 'saveCategoryNew') {
            return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'categoryForm', 'activeTab' => $activeTab)));
        }

        return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'categories', 'activeTab' => $activeTab)));
    }

    /**
     * Post action for deleteCategory to redirect to event category listing.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function deleteCategory()
    {
        FD::info()->set($this->getMessage());

        return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'categories')));
    }

    /**
     * Post action after publishing or unpublishing events to redirect to event listing.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function togglePublish()
    {
        FD::info()->set($this->getMessage());

        return $this->redirect(FRoute::url(array('view' => 'events')));
    }

    /**
     * Post action after publishing or unpublishing event category to redirect to event listing.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function togglePublishCategory()
    {
        FD::info()->set($this->getMessage());

        return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'categories')));
    }

    /**
     * Post action after approving an event to redirect back to the pending listing.
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function approve()
    {
        $ids = $this->input->getVar('cid');
        $ids = FD::makeArray($ids);

        $schedules = array();
        $postdatas = array();
        $eventids = array();

        foreach ($ids as $id) {
            $event = FD::event($id);

            $params = $event->getParams();

            if ($params->exists('recurringData')) {

                $schedule = FD::model('Events')->getRecurringSchedule(array(
                    'eventStart' => $event->getEventStart(),
                    'end' => $params->get('recurringData')->end,
                    'type' => $params->get('recurringData')->type,
                    'daily' => $params->get('recurringData')->daily
                ));

                if (!empty($schedule)) {
                    $eventids[] = $event->id;
                    $schedules[$event->id] = $schedule;
                    $postdatas[$event->id] = FD::makeObject($params->get('postdata'));
                }
            }
        }

        if (empty($schedules)) {
            FD::info()->set($this->getMessage());

            return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'pending')));
        }

        $this->setHeading(JText::_('COM_EASYSOCIAL_EVENTS_APPLYING_RECURRING_EVENT_CHANGES'));
        $this->setDescription(JText::_('COM_EASYSOCIAL_EVENTS_APPLYING_RECURRING_EVENT_CHANGES_DESCRIPTION'));

        $json = FD::json();

        $this->set('schedules', $json->encode($schedules));
        $this->set('postdatas', $json->encode($postdatas));
        $this->set('eventids', $json->encode($eventids));

        echo parent::display('admin/events/approve.recurring');
    }

    public function approveRecurringSuccess()
    {
        $eventids = $this->input->getString('ids');
        $eventids = FD::makeArray($eventids);

        foreach ($eventids as $id) {
            $clusterTable = FD::table('Cluster');
            $clusterTable->load($id);
            $eventParams = FD::makeObject($clusterTable->params);
            unset($eventParams->postdata);
            $clusterTable->params = FD::json()->encode($eventParams);
            $clusterTable->store();
        }

        FD::info()->set(false, JText::_('COM_EASYSOCIAL_EVENT_APPROVE_SUCCESS'), SOCIAL_MSG_SUCCESS);

        return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'pending')));
    }

    /**
     * Post action after rejecting an event to redirect back to the pending listing.
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function reject()
    {
        FD::info()->set($this->getMessage());

        return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'pending')));
    }

    /**
     * Post action after inviting guests to an event to redirect back to the event form.
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function inviteGuests()
    {
        FD::info()->set($this->getMessage());

        $id = JRequest::getInt('id');

        return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'form', 'id' => $id, 'activeTab' => 'guests')));
    }

    /**
     * Post action after approving guests to redirect back to the event form.
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function approveGuests()
    {
        FD::info()->set($this->getMessage());

        $id = JRequest::getInt('id');

        return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'form', 'id' => $id, 'activeTab' => 'guests')));
    }

    /**
     * Post action after rejecting guests to redirect back to the event form.
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function removeGuests()
    {
        FD::info()->set($this->getMessage());

        $id = JRequest::getInt('id');

        return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'form', 'id' => $id, 'activeTab' => 'guests')));
    }

    /**
     * Post action after switching an event's owner.
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function switchOwner()
    {
        FD::info()->set($this->getMessage());

        return $this->redirect(FRoute::url(array('view' => 'events')));
    }

    /**
     * Post action after promoting guests to redirect back to the event form.
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function promoteGuests()
    {
        FD::info()->set($this->getMessage());

        $id = JRequest::getInt('id');

        return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'form', 'id' => $id, 'activeTab' => 'guests')));
    }

    /**
     * Post action after removing guests admin role to redirect back to the event form.
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     */
    public function demoteGuests()
    {
        FD::info()->set($this->getMessage());

        $id = JRequest::getInt('id');

        return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'form', 'id' => $id, 'activeTab' => 'guests')));
    }

    /**
     * Post process after a group is marked as featured
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function toggleDefault()
    {
        FD::info()->set($this->getMessage());

        $this->redirect('index.php?option=com_easysocial&view=events');
    }

    public function move($layout = null)
    {
        FD::info()->set($this->getMessage());

        $this->redirect('index.php?option=com_easysocial&view=events&layout=' . $layout);
    }

    public function switchCategory()
    {
        FD::info()->set($this->getMessage());

        $this->redirect('index.php?option=com_easysocial&view=events');
    }

    public function updateRecurringSuccess()
    {
        FD::info()->set(false, JText::_('COM_EASYSOCIAL_EVENTS_FORM_UPDATE_SUCCESS'), SOCIAL_MSG_SUCCESS);

        $task = $this->input->getString('task');

        $eventId = $this->input->getInt('id');

        $event = FD::event($eventId);

        // Remove the post data from params
        $clusterTable = FD::table('Cluster');
        $clusterTable->load($event->id);
        $eventParams = FD::makeObject($clusterTable->params);
        unset($eventParams->postdata);
        $clusterTable->params = FD::json()->encode($eventParams);
        $clusterTable->store();

        if ($task === 'apply') {
            return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'form', 'id' => $event->id, 'activeTab' => 'event')));
        }

        if ($task === 'savenew') {
            return $this->redirect(FRoute::url(array('view' => 'events', 'layout' => 'form', 'category_id' => $event->category_id)));
        }

        return $this->redirect(FRoute::url(array('view' => 'events')));
    }
}
