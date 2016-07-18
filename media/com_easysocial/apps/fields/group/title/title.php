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
FD::import('fields:/user/textbox/textbox');

class SocialFieldsGroupTitle extends SocialFieldsUserTextbox
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
        $title = !empty($data[$this->inputName]) ? $data[$this->inputName] : '';

        // Set the title on the group
        $cluster->title = $title;

        unset($data[$this->inputName]);
    }

    /**
     * Executes before the group is save.
     *
     * @since   1.2
     * @access  public
     * @param   array           $data       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onEditBeforeSave(&$data, &$cluster)
    {
        $title = !empty($data[$this->inputName]) ? $data[$this->inputName] : '';

        // Set the title on the group
        $cluster->title = $title;

        unset($data[$this->inputName]);
    }

    /**
     * Executes before the group is save.
     *
     * @since   1.2
     * @access  public
     * @param   array           $data       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onAdminEditBeforeSave(&$data, &$cluster)
    {
        $title = !empty($data[$this->inputName]) ? $data[$this->inputName] : '';

        // Set the title on the group
        $cluster->title = $title;

        unset($data[$this->inputName]);
    }

    /**
     * Displays the group title textbox.
     *
     * @since   1.2
     * @access  public
     * @param   array           $data       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     * @param   array           $errors     The errors array.
     */
    public function onEdit(&$post, &$cluster, $errors)
    {
        // The value will always be the group title
        $value = !empty($post[$this->inputName]) ? $post[$this->inputName] : $cluster->getName();

        // Get the error.
        $error = $this->getError($errors);

        // Set the value.
        $this->set('value', $this->escape($value));
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
    public function onAdminEdit(&$post, &$cluster, $errors)
    {
        // The value will always be the group title
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
     * @since   1.2
     * @access  public
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onDisplay($cluster)
    {
        $value = $cluster->getName();

        $field = $this->field;

        $advGroups = array(SOCIAL_FIELDS_GROUP_GROUP, SOCIAL_FIELDS_GROUP_USER);

        if (in_array($field->type, $advGroups) && $field->searchable) {

            $title = $value;

            // let break the text based on space
            if (strpos($title, " ") !== false) {
                $segments = explode(" ", $title);
                $title = $segments[0];
            }

            $params = array( 'layout' => 'advanced' );
            if ($field->type != SOCIAL_FIELDS_GROUP_USER) {
                $params['type'] = $field->type;
                $params['uid'] = $field->uid;
            }
            $params['criterias[]'] = $field->unique_key . '|' . $field->element;
            $params['operators[]'] = 'contain';
            $params['conditions[]'] = $title;

            $advsearchLink = FRoute::search($params);
            $this->set( 'advancedsearchlink'    , $advsearchLink );
        }

        // Push variables into theme.
        $this->set('value', $this->escape($value));

        return $this->display();
    }
}
