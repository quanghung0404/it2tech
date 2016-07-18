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
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/includes/fields/dependencies');

class SocialFieldsEventDescription extends SocialFieldItem
{
    /**
     * Support for generic getFieldValue('DESCRIPTION')
     *
     * @since  1.3.9
     * @access public
     * @return SocialFieldValue    The value container
     */
    public function getValue()
    {
        $container = $this->getValueContainer();

        if ($this->field->type == SOCIAL_TYPE_EVENT && !empty($this->field->uid)) {
            $event = FD::event($this->field->uid);

            $container->value = $event->getDescription();

            $container->data = $event->description;
        }

        return $container;
    }

    /**
     * Displays the field for edit.
     *
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     * @param   array           $errors     The errors array.
     * @return  string                      The html codes for this field.
     */
    public function onEdit(&$post, &$cluster, $errors)
    {
        $desc = $this->input->get($this->inputName, $cluster->description, 'raw');

        // Get the error.
        $error = $this->getError($errors);

        // Get the editor
        $editor = $this->getEditor();

        $this->set('editor', $editor);
        $this->set('value', $desc);
        $this->set('error', $error);

        return $this->display();
    }

    /**
     * Displays the field for admin edit.
     *
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     * @param   array           $errors     The errors array.
     * @return  string                      The html codes for this field.
     */
    public function onAdminEdit(&$post, &$cluster, $errors)
    {
        $desc = $this->input->get($this->inputName, $cluster->description, 'raw');

        // Get the error.
        $error = $this->getError($errors);

        // Get the editor.
        $editor = $this->getEditor();

        $this->set('editor', $editor);
        $this->set('value', $desc);
        $this->set('error', $error);

        return $this->display();
    }

    /**
     * Responsible to output the html codes that is displayed to a user.
     *
     * @since   1.3
     * @access  public
     * @param   SocialCluster   $cluster    The cluster object.
     * @return  string                      The html codes for this field.
     */
    public function onDisplay($cluster)
    {
        // Push variables into theme.
        $value = $cluster->getDescription();

        $this->set('value', $value);

        return $this->display();
    }

    /**
     * Displays the field input for user when they register their account.
     *
     * @since   1.4
     * @access  public
     * @param   array
     * @param   SocialTableRegistration
     * @return  string  The html output.
     */
    public function onRegister(&$post, &$registration)
    {
        // Get the value from posted data if it's available.
        $value = $this->input->get($this->inputName, $this->params->get('default'), 'raw');

        // Get any errors for this field.
        $error = $registration->getErrors($this->inputName);

        // Get the editor that is configured
        $editor = $this->getEditor();

        $this->set('editor', $editor);
        $this->set('value', $value);
        $this->set('error', $error);

        return $this->display();
    }

    /**
     * Validates the event creation
     *
     * @since   1.4.9
     * @access  public
     * @param   string
     * @return  
     */
    public function onRegisterValidate(&$post)
    {
        $value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

        $valid = $this->validate($value);
        
        return $valid;
    }

    /**
     * Validates the event editing
     *
     * @since   1.4.9
     * @access  public
     * @param   string
     * @return  
     */
    public function onEditValidate(&$post)
    {
        $value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

        $valid = $this->validate($value);
        
        return $valid;
    }

    /**
     * General validation function
     *
     * @since   1.4.9
     * @access  public
     * @param   string  Value of the string to validate
     * @return  bool    State of the validation
     *
     */
    private function validate($value)
    {
        if ($this->isRequired() && empty($value)) {
            return $this->setError(JText::_('PLG_FIELDS_EVENT_DESCRIPTION_VALIDATION_INPUT_REQUIRED'));
        }

        return true;
    }

    /**
     * Executes before the event is created.
     *
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onRegisterBeforeSave(&$post, &$cluster)
    {
        $desc = !isset($post[$this->inputName]) ? $this->input->get($this->inputName, '', 'raw') : $post[$this->inputName];

        // Set the description on the event
        $cluster->description = $desc;

        unset($post[$this->inputName]);
    }

    /**
     * Executes before the event is saved.
     *
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onEditBeforeSave(&$post, &$cluster)
    {
        $desc = $this->input->get($this->inputName, '', 'raw');

        // Set the description on the event
        $cluster->description = $desc;

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
    public function onAdminEditBeforeSave(&$post, &$cluster)
    {
        $desc = $this->input->get($this->inputName, '', 'raw');

        // Set the description on the event
        $cluster->description = $desc;

        unset($post[$this->inputName]);
    }

    /**
     * Displays the sample codes for this field in the field editor
     *
     * @since   1.4
     * @access  public
     * @param   array
     * @param   SocialTableRegistration
     * @return  string  The html output.
     *
     */
    public function onSample()
    {
        $editor = $this->getEditor();

        $this->set('editor', $editor);

        return $this->display();
    }

    /**
     * Retrieves the editor object.
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function getEditor()
    {
        // Get the default editor
        $defaultEditor = $this->params->get('editor');

        $editor = JFactory::getEditor($defaultEditor);

        return $editor;
    }
}
