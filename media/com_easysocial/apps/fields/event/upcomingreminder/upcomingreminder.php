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

class SocialFieldsEventUpcomingreminder extends SocialFieldItem
{
    public function onRegister(&$post, &$session)
    {
        $reminderDuration = isset($post['event_reminder']) ? $post['event_reminder'] : $this->params->get('default', 0);

        $this->set('value', $reminderDuration);

        return $this->display();
    }

    public function onAdminEdit(&$post, &$cluster, $errors)
    {
        // The value will always be the event title
        $value = isset($post['event_reminder']) ? $post['event_reminder'] : $cluster->getReminder();

        // Get the error.
        $error = $this->getError($errors);

        // Set the value.
        $this->set('value', $this->escape($value));
        $this->set('error', $error);

        return $this->display();
    }

    public function onEdit(&$post, &$cluster, $errors)
    {
        // The value will always be the event title
        $value = isset($post['event_reminder']) ? $post['event_reminder'] : $cluster->getReminder();

        // Get the error.
        $error = $this->getError($errors);

        // Set the value.
        $this->set('value', $this->escape($value));
        $this->set('error', $error);

        return $this->display();
    }

    public function onSample()
    {
        $this->set('value', '0');

        return $this->display();
    }

    /**
     * Executes before the event is created.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onRegisterBeforeSave(&$post, &$cluster)
    {
        // Get the posted value
        $value = isset($post['event_reminder']) ? $post['event_reminder'] : $this->params->get('default');
        $value = (int) $value;

        $cluster->meta->reminder = $value;

        unset($post['event_reminder']);
    }

    /**
     * Executes before the event is saved.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onEditBeforeSave(&$post, &$cluster)
    {
        // Get the posted value
        $value = isset($post['event_reminder']) ? $post['event_reminder'] : $cluster->isAllDay();
        $value = (int) $value;

        $cluster->meta->reminder = $value;

        unset($post['event_reminder']);
    }

    /**
     * Override the parent's onDisplay to not show this field.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onDisplay($cluster)
    {
        return;
    }
}
