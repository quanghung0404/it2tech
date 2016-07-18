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

/**
 * This field configure if the state "maybe" is allowed for guest to select.
 */
class SocialFieldsEventConfigAllowMaybe extends SocialFieldItem
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
        // Get any previously submitted data
        $value = isset($post['allowmaybe']) ? (bool) $post['allowmaybe'] : $this->params->get('default', true);

        // Detect if there's any errors
        $error = $session->getErrors($this->inputName);

        $this->set('error', $error);
        $this->set('value', $value);

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
    public function onEdit(&$post, &$cluster, $errors)
    {
        $value = isset($post['allowmaybe']) ? (bool) $post['allowmaybe'] : $cluster->getParams()->get('allowmaybe', $this->params->get('default', true));
        $error = $this->getError($errors);

        $this->set('error', $error);
        $this->set('value', $value);

        return $this->display();
    }

    /**
     * Displays the sample html codes when the field is added into the profile.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @return  string  The html output.
     */
    public function onSample()
    {
        $value = $this->params->get('default');

        $this->set('value', $value);

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
        return $this->beforeSave($post, $cluster);
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
        return $this->beforeSave($post, $cluster);
    }

    public function beforeSave(&$post, &$cluster)
    {
        // Get the posted value
        $value = isset($post['allowmaybe']) ? (bool) $post['allowmaybe'] : $this->params->get('default', true);

        $registry = $cluster->getParams();
        $registry->set('allowmaybe', $value);

        $cluster->params = $registry->toString();

        unset($post['allowmaybe']);

        return true;
    }
}
