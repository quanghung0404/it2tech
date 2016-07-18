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

FD::import('fields:/user/textbox/textbox');

class SocialFieldsEventTitle extends SocialFieldsUserTextbox
{
    /**
     * Support for generic getFieldValue('TITLE')
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3.9
     * @access public
     * @return SocialFieldValue    The value container
     */
    public function getValue()
    {
        $container = $this->getValueContainer();

        if ($this->field->type == SOCIAL_TYPE_EVENT && !empty($this->field->uid)) {
            $event = FD::event($this->field->uid);

            $container->value = $event->getName();

            $container->data = $event->title;
        }

        return $container;
    }

    /**
     * Displays the event title textbox.
     *
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     * @param   array           $errors     The errors array.
     */
    public function onEdit(&$post, &$cluster, $errors)
    {
        // The value will always be the event title
        $value = !empty($post[$this->inputName]) ? $post[$this->inputName] : $cluster->getName();

        // Get the error.
        $error = $this->getError($errors);

        // Set the value.
        $this->set('value', $this->escape($value));
        $this->set('error', $error);

        return $this->display();
    }

    /**
     * Displays the event description textbox.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     * @param   array           $errors     The errors array.
     */
    public function onAdminEdit(&$post, &$cluster, $errors)
    {
        // The value will always be the event title
        $value = !empty($post[$this->inputName]) ? $post[$this->inputName] : $cluster->getName();

        // Get the error.
        $error = $this->getError($errors);

        // Set the value.
        $this->set('value', $this->escape($value));
        $this->set('error', $error);

        return $this->display();
    }

    /**
     * Responsible to output the html codes that is displayed to a user.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onDisplay($cluster)
    {
        $value = $cluster->getName();

        // Push variables into theme.
        $this->set('value', $this->escape($value));

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
        $title = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

        // Set the title on the event
        $cluster->title = $title;

        unset($post[$this->inputName]);
    }

    /**
     * Executes before the event is save.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onEditBeforeSave(&$post, &$cluster)
    {
        $title = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

        // Set the title on the event
        $cluster->title = $title;

        unset($post[$this->inputName]);
    }

    /**
     * Executes before the event is save.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onAdminEditBeforeSave(&$post, &$cluster)
    {
        $title = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

        // Set the title on the event
        $cluster->title = $title;

        unset($post[$this->inputName]);
    }
}
