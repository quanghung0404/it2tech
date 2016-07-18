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

// Extend from user textbox
FD::import('fields:/user/textarea/textarea');

class SocialFieldsGroupDescription extends SocialFieldsUserTextarea
{
    /**
     * Executes before the group is created.
     *
     * @since   1.2
     * @access  public
     * @param   array           $data       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onRegisterBeforeSave(&$data, &$cluster)
    {
        $desc = !empty($data[$this->inputName]) ? $data[$this->inputName] : '';

        // Set the description on the group
        $cluster->description = $desc;

        unset($data[$this->inputName]);
    }

    /**
     * Executes before the group is saved.
     *
     * @since   1.2
     * @access  public
     * @param   array           $data       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onEditBeforeSave(&$data, &$cluster)
    {
        $desc = !empty($data[$this->inputName]) ? $data[$this->inputName] : '';

        // Set the description on the group
        $cluster->description = $desc;

        unset($data[$this->inputName]);
    }

    /**
     * Executes before the group is saved.
     *
     * @since   1.2
     * @access  public
     * @param   array           $data       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onAdminEditBeforeSave(&$data, &$cluster)
    {
        $desc = !empty($data[$this->inputName]) ? $data[$this->inputName] : '';

        // Set the description on the group
        $cluster->description = $desc;

        unset($data[$this->inputName]);
    }

    /**
     * Displays the group description textbox.
     *
     * @since   1.2
     * @access  public
     * @param   array           $data       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     * @param   array           $errors     The errors array.
     */
    public function onEdit(&$data, &$cluster, $errors)
    {
        $description = !empty($post[$this->inputName]) ? $post[$this->inputName] : $cluster->description;

        // Get the error.
        $error = $this->getError($errors);

        $this->set('value', $this->escape($description));
        $this->set('error', $error);

        return $this->display();
    }

    /**
     * Displays the group description textbox.
     *
     * @since   1.2
     * @access  public
     * @param   array           $data       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     * @param   array           $errors     The errors array.
     */
    public function onAdminEdit(&$data, &$cluster, $errors)
    {
        $description = !empty($post[$this->inputName]) ? $post[$this->inputName] : $cluster->description;

        // Get the error.
        $error = $this->getError($errors);

        $this->set('value', $this->escape($description));
        $this->set('error', $error);

        return $this->display();
    }

    /**
     * Responsible to output the html codes that is displayed to a user.
     *
     * @since   1.2
     * @access  public
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onDisplay($cluster)
    {
        // Push variables into theme.
        $this->set('value', nl2br($this->escape($cluster->description)));

        return $this->display();
    }
}
