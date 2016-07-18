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

FD::import('admin:/includes/fields/dependencies');

class SocialFieldsEventRecurring extends SocialFieldItem
{
    public function onRegister(&$post, &$session)
    {
        $value = !empty($post[$this->inputName]) ? $post[$this->inputName] : null;

        $value = $this->getRecurringValue($value);

        $original = $this->getRecurringValue();

        $this->set('weekdays', $this->getWeekdays());

        $error = $session->getErrors($this->inputName);

        $this->set('value', $value);
        $this->set('original', $this->getRecurringValue());
        $this->set('error', $error);

        $this->set('allday', false);

        $this->set('showWarningMessages', 0);

        return $this->display();
    }

    public function onAdminEdit(&$post, &$event, $errors)
    {
        // Do not display if this is a child event
        if (!empty($event->parent_id)) {
            return;
        }

        $display = $this->onEdit($post, $event, $errors);

        $this->set('showWarningMessages', empty($event->id) ? 0 : 1);

        return $display;
    }

    public function onEdit(&$post, &$event, $errors)
    {
        // Do not display if this is a child event
        if (!empty($event->parent_id)) {
            return;
        }

        $value = !empty($post[$this->inputName]) ? $post[$this->inputName] : $this->value;

        $value = $this->getRecurringValue($value);

        $original = $this->getRecurringValue($this->value);

        $error = $this->getError($errors);

        $this->set('weekdays', $this->getWeekdays());

        $this->set('value', $value);

        $this->set('original', $original);

        $this->set('error', $error);

        $this->set('allday', $event->isAllDay());

        $this->set('showWarningMessages', 1);

        $this->set('eventId', $event->id);

        return $this->display();
    }

    public function onSample()
    {
        $this->set('weekdays', $this->getWeekdays());

        $this->set('value', $this->getRecurringValue());

        $this->set('original', $this->getRecurringValue());

        $this->set('allday', false);

        $this->set('showWarningMessages', 0);

        return $this->display();
    }

    public function onRegisterValidate(&$post)
    {
        // Registration won't be child event.

        return $this->onValidate($post);
    }

    public function onEditValidate(&$post, &$event)
    {
        // If this is a child event, we don't want to validate
        if (!empty($event->parent_id)) {
            return true;
        }

        return $this->onValidate($post);
    }

    public function onAdminEditValidate(&$post, &$event)
    {
        // If this is a child event, we don't want to validate
        if (!empty($event->parent_id)) {
            return true;
        }

        return $this->onValidate($post);
    }

    private function onValidate(&$post)
    {
        $value = $this->getRecurringValue($post[$this->inputName]);

        if ($value->type !== 'none' && empty($value->end)) {
            $this->setError(JText::_('FIELD_EVENT_RECURRING_VALIDATION_END_DATE_REQUIRED'));
            return false;
        }

        if ($value->type === 'none') {
            return true;
        }

        $endDatetime = !empty($post['endDatetime']) ? $post['endDatetime'] : '';
        $endDatetimeObj = new SocialFieldsEventStartendObject;
        $endDatetimeObj->load($endDatetime);

        $recurringEnd = FD::date($value->end);

        // Check if the end recur date is more than the event end date
        if ($recurringEnd->toUnix() < $endDatetimeObj->toUnix()) {
            $this->setError(JText::_('FIELD_EVENT_RECURRING_VALIDATION_INVALID_END_DATE'));
            return false;
        }

        // If max is set to 0 then we don need to validate
        $max = FD::config()->get('events.recurringlimit', 0);
        if (empty($max) || $max == 0) {
            return true;
        }

        $startDatetime = !empty($post['startDatetime']) ? $post['startDatetime'] : '';
        $startDatetimeObj = new SocialFieldsEventStartendObject;
        $startDatetimeObj->load($startDatetime);

        $schedule = FD::model('Events')->getRecurringSchedule(array(
            'eventStart' => $startDatetimeObj->toDate(),
            'end' => $value->end,
            'type' => $value->type,
            'daily' => $value->daily
        ));

        $totalRecurring = count($schedule);

        if ($totalRecurring > $max) {
            $this->setError(JText::sprintf('FIELD_EVENT_RECURRING_VALIDATION_MAX_RECURRING_LIMIT', $totalRecurring, $max));
            return false;
        }

        return true;
    }

    public function onRegisterAfterSave(&$post, &$event)
    {
        // If this is a child event, we don't want the recur data.
        if (!empty($event->parent_id)) {
            unset($post[$this->inputName]);
            return true;
        }

        $value = FD::makeObject($post[$this->inputName]);

        // If type is none, then we do not need end value to be stored
        if ($value->type === 'none') {
            $value->end = '';
        }

        // If type is not daily, then we don't want daily values
        if ($value->type !== 'daily') {
            $value->daily = array();
        }

        $post[$this->inputName] = $value;

        // In register, there is no "deletion" to worry about

        // In register, there is no "changes" to detect

        if ($value->type !== 'none') {
            // Store the recurring data on the event object so that the controller can do subsequent processing in recurring event creation
            $event->recurringData = $value;
        }

        // If creation is needed, then we leave it up to controller and view to do it instead through ajax

        return true;
    }

    public function onEditAfterSave(&$post, &$event)
    {
        // If this is a child event, we don't want the recur data.
        if (!empty($event->parent_id)) {
            unset($post[$this->inputName]);
            return true;
        }

        $original = $this->getRecurringValue(!empty($this->value) ? $this->value : null);

        $value = FD::makeObject($post[$this->inputName]);

        // If type is none, then we do not need end value to be stored
        if ($value->type === 'none') {
            $value->end = '';
        }

        // If type is not daily, then we don't want daily values
        if ($value->type !== 'daily') {
            $value->daily = array();
        }

        $post[$this->inputName] = $value;

        if ($value->type === 'none') {
            // Delete all recurring events.
            FD::model('Events')->deleteRecurringEvents($event->id);
        }

        // We need to reorder the daily values because it is possible that the array start with other day of the week, depending on settings
        sort($value->daily);

        // Detect changes to see if need to recreate events
        $changed = false;

        // If there is no child events and the type is not none, then it is considered as change
        if (!$event->hasRecurringEvents() && $value->type != 'none') {
            $changed = true;
        }

        if ($original->type != $value->type || $original->end != $value->end || count($original->daily) != count($value->daily)) {
            $changed = true;
        }

        foreach ($original->daily as $i => $d) {
            if (!in_array($d, $value->daily)) {
                $changed = true;
                break;
            }
        }

        foreach ($value->daily as $i => $d) {
            if (!in_array($d, $original->daily)) {
                $changed = true;
                break;
            }
        }

        if ($changed) {
            // Store the recurring data on the event object so that the controller can do subsequent processing in recurring event creation
            $event->recurringData = $value;
        }

        // If creation is needed, then we leave it up to controller and view to do it instead through ajax

        return true;
    }

    public function getWeekdays()
    {
        // obeying date format 'w'
        $weekdays = array(
            array('key' => 0, 'value' => JText::_('SUNDAY')),
            array('key' => 1, 'value' => JText::_('MONDAY')),
            array('key' => 2, 'value' => JText::_('TUESDAY')),
            array('key' => 3, 'value' => JText::_('WEDNESDAY')),
            array('key' => 4, 'value' => JText::_('THURSDAY')),
            array('key' => 5, 'value' => JText::_('FRIDAY')),
            array('key' => 6, 'value' => JText::_('SATURDAY'))
        );

        // Configurable option
        $startOfWeek = FD::config()->get('events.startofweek', 0);

        if ($startOfWeek > 0) {
            $spliced = array_splice($weekdays, $startOfWeek);
            $weekdays = array_merge($spliced, $weekdays);
        }

        return $weekdays;
    }

    public function getRecurringValue($data = null)
    {
        $value = new SocialFieldsEventRecurringObject($data);

        return $value;
    }
}

class SocialFieldsEventRecurringObject
{
    public $type;
    public $end;

    public $daily = array();

    public function __construct($data = null)
    {
        !empty($data) && $this->load($data);
    }

    public function load($data)
    {
        if (empty($data)) {
            return false;
        }

        $data = FD::makeObject($data);

        if (!$data) {
            return false;
        }

        $json = FD::json();

        foreach ($data as $key => $val) {
            if ($key == 'daily') {
                $val = FD::makeArray($val);
            }

            $this->$key = $val;
        }

        return true;
    }
}
