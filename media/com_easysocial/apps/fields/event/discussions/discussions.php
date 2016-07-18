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

FD::import('fields:/user/boolean/boolean');

class SocialFieldsEventDiscussions extends SocialFieldsUserBoolean
{
    /**
     * Displays the field for creation.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   array                   $post       The posted data.
     * @param   SocialTableStepSession  $session    The session table.
     * @return  string                              The html codes for this field.
     */
    public function onRegister(&$post, &$session)
    {
        $value = isset($post[$this->inputName]) ? $post[$this->inputName] : $this->params->get('default');

        // Set the value.
        $this->set('value', $this->escape($value));

        return $this->display();
    }

    /**
     * Displays the field for edit.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     * @param   array           $errors     The errors array.
     * @return  string                      The html codes for this field.
     */
    public function onEdit(&$post, &$cluster , $errors)
    {
        $params = $cluster->getParams();

        // Get the real value for this item
        $value = isset($post[$this->inputName]) ? $post[$this->inputName] : $params->get('discussions', $this->params->get('default'));

        // Get the error.
        $error = $this->getError($errors);

        // Set the value.
        $this->set('value', $this->escape($value));
        $this->set('error', $error);

        return $this->display();
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
        $value = isset($post[$this->inputName]) ? $post[$this->inputName] : $this->params->get('default');
        $value = (bool) $value;

        $registry = $cluster->getParams();
        $registry->set('discussions' , $value);

        $cluster->params = $registry->toString();

        unset($post[$this->inputName]);
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
        $value = isset($post[$this->inputName]) ? $post[$this->inputName] : $params->get('discussions', $this->params->get('default'));
        $value = (bool) $value;

        $registry = $cluster->getParams();
        $registry->set('discussions' , $value);

        $cluster->params = $registry->toString();

        unset($post[$this->inputName]);
    }
}
