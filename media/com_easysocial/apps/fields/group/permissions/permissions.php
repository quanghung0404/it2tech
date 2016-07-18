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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Extend from user header
FD::import('fields:/user/header/header');

class SocialFieldsGroupPermissions extends SocialFieldItem
{
    /**
     * Displays the form for group owner to define permissions
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return  
     */
    public function onRegister(&$post, &$session)
    {
        // Get the posted value if there's any
        $value = !empty($post['stream_permissions']) ? $post['stream_permissions'] : '';

        // If this is a new group being created, ensure that admin is always checked by default
        if (!$value && !is_array($value)) {
            $value = array('admin');
        }

        // Ensure that it's an array
        $value = FD::makeArray($value);


        $this->set('value', $value);

        return $this->display();
    }

    /**
     * Displays the form for group owner to define permissions when group is being edited
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return  
     */
    public function onEdit(&$post, SocialGroup &$group, $errors)
    {
        $permissions = $group->getParams()->get('stream_permissions', array());

        $value = !empty($post['stream_permissions']) ? $post['stream_permissions'] : $permissions;
        $value = FD::makeArray($value);

        $this->set('value', $value);

        return $this->display();
    }

    /**
     * Displays the sample output for the back end.
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return  
     */
    public function onSample()
    {
        return $this->display();
    }

    /**
     * Processes the save for new group creation
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return  
     */
    public function onRegisterBeforeSave(&$post, &$group)
    {
        return $this->onBeforeSave($post, $group);
    }

    /**
     * Processes the save for group editing
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return  
     */
    public function onEditBeforeSave(&$post, SocialGroup &$group)
    {
        return $this->onBeforeSave($post, $group);
    }

    /**
     * Before the form is saved, we need to store these data into the group properties
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return  
     */
    public function onBeforeSave(&$post, SocialGroup &$group)
    {
        $value = !empty($post['stream_permissions']) ? $post['stream_permissions'] : array();
        $value = FD::makeArray($value);
        
        // Set it into the group params so that we can retrieve this later
        $params = $group->getParams();
        $params->set('stream_permissions', $value);

        $group->params = $params->toString();

        unset($post['stream_permissions']);
    }
}
