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

// Include the fields library
FD::import('admin:/includes/fields/dependencies');

// Include helper file.
FD::import('fields:/user/joomla_username/helper');

/**
 * Field application for Joomla username
 *
 * @since   1.0
 * @author  Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserJoomla_username extends SocialFieldItem
{
    /**
     * Displays the field input for user when they register their account.
     *
     * @since   1.0
     * @access  public
     * @param   array   The post data
     * @param   SocialTableRegistration
     * @return  string  The html output.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function onRegister(&$post, &$registration)
    {
        $config = FD::config();

        // If settings is set to use email as username, then we hide username field
        if ($config->get('registrations.emailasusername')) {
            return false;
        }

        // Try to check to see if user has already set the username.
        $username = isset($post['username']) ? $post['username'] : '';

        // Check for errors
        $error = $registration->getErrors($this->inputName);

        // Set errors.
        $this->set('error', $error);

        // Set the username property for the theme.
        $this->set('username', $this->escape($username));

        $this->set('userid', null);

        return $this->display();
    }

    /**
     * Determines whether there's any errors in the submission in the registration form.
     *
     * @since   1.0
     * @access  public
     * @param   array   The posted data.
     * @return  bool    Determines if the system should proceed or throw errors.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function onRegisterValidate(&$post)
    {
        $username = !empty($post['username']) ? $post['username'] : '';

        return $this->validateUsername($username);
    }

    public function onRegisterBeforeSave(&$post)
    {
        if (!empty($post['username']) && empty($post['first_name']) && empty($post['name'])) {
            // Assign directly to name because Joomla is reading name instead
            // We also check for first_name because first_name is unique to EasySocial and this is to ensure that the field is either empty or not loaded

            $post['name'] = $post['username'];
        }
    }

    /**
     * Processes after a user registers on the site
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function onRegisterAfterSave(&$data, $user)
    {
        $config = FD::config();

        if ($config->get('users.aliasName') != 'username') {
            return;
        }

        $this->saveAlias($data, $user);
    }

    /**
     * Displays the field input for user when they edit their account.
     *
     * @since   1.0
     * @access  public
     * @param   array   The posted data.
     * @param   SocialTableRegistration
     * @param   array   The errors
     * @return  string  The html output.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function onEdit(&$post, &$user, $errors)
    {
        $config = FD::config();

        // If settings is set to use email as username, then we hide username field
        if ($config->get('registrations.emailasusername')) {
            return false;
        }

        // Set current username.
        $this->set('username', $this->escape($user->username));

        $error = $this->getError($errors);

        $this->set('error', $error);

        $this->set('userid', $user->id);

        return $this->display();
    }

    public function onEditValidate(&$post, &$user)
    {
        if (!$this->params->get('allow_edit_change', false)) {
            return true;
        }

        $username = !empty($post['username']) ? $post['username'] : '';

        return $this->validateUsername($username, $user->username);
    }

    public function onEditBeforeSave(&$post, $user)
    {
        if (!$this->params->get('allow_edit_change', false)) {
            return true;
        }

        $post['usernameChanged'] = $this->usernameChanged($post, $user);
    }

    public function onEditAfterSave(&$data, $user)
    {
        if (!$this->params->get('allow_edit_change', false)) {
            return true;
        }

        $config = FD::config();

        if ($config->get('users.aliasName') != 'username') {
            return;
        }

        // Only proceed when the name has been changed.
        if (isset($data['usernameChanged']) && !$data['usernameChanged']) {
            return;
        }

        $this->saveAlias($data, $user);
    }

    public function onAdminEdit(&$post, &$user, $errors)
    {
        $config = FD::config();

        // If settings is set to use email as username, then we hide username field
        if ($config->get('registrations.emailasusername')) {
            return false;
        }

        $username = !empty($user->username) ? $user->username : '';

        $this->set('username', $username);

        $error = $this->getError($errors);

        $this->set('error', $error);

        $this->set('userid', $user->id);

        return $this->display();
    }

    /**
     * Triggers before a user is saved
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function onAdminEditBeforeSave(&$post, $user)
    {
        // Detect if the name is changed.
        $post['usernameChanged'] = $this->usernameChanged($post, $user);
    }

    /**
     * Triggers after a user is saved by the admin
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function onAdminEditAfterSave(&$data, $user)
    {
        $config = FD::config();

        if ($config->get('users.aliasName') != 'username') {
            return;
        }

        // Only proceed when the name has been changed.
        if (isset($data['usernameChanged']) && !$data['usernameChanged']) {
            return;
        }

        $this->saveAlias($data, $user);
    }

    /**
     * Displays the sample html codes when the field is added into the profile.
     *
     * @since   1.0
     * @access  public
     * @param   array
     * @param   SocialTableRegistration
     * @return  string  The html output.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function onSample()
    {
        $config = FD::config();

        // If settings is set to use email as username, then we hide username field
        if ($config->get('registrations.emailasusername')) {
            return $this->display('hidden');
        }

        return $this->display();
    }

    public function onDisplay($user)
    {
        if (FD::config()->get('registrations.emailasusername')) {
            return;
        }

        $this->set('username', $this->escape($user->username));

        return $this->display();
    }

    /**
     * Determines if the name has changed
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function usernameChanged($post, $user)
    {
        // Detect if the name has changed
        $username = isset($post['username']) ? $post['username'] : '';

        if ($username != $user->username) {
            return true;
        }

        return false;
    }

    /**
     * Responsible to save the alias of the user.
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function saveAlias(&$data, &$user)
    {
        // Get the username
        $username = isset($data['username']) ? $data['username'] : '';

        // Filter the username so that it becomes a valid alias
        $alias = JFilterOutput::stringURLSafe($username);

        // Check if the alias exists.
        $model = FD::model('Users');

        // Keep the original state of the alias
        $tmp = $alias;

        while ($model->aliasExists($alias, $user->id)) {
            // Generate a new alias for the user.
            $alias  = $tmp . '-' . rand(1, 150);
        }

        $user->alias = $alias;

        $user->save();
    }

    public function validateUsername($username, $current = '')
    {
        $config = FD::config();

        // If settings is set to use email as username, then we bypass this check
        if ($config->get('registrations.emailasusername')) {
            return true;
        }

        // Test the username length
        if (JString::strlen($username) < $this->params->get('min')) {
            return $this->setError(JText::sprintf('PLG_FIELDS_JOOMLA_USERNAME_MIN_CHARACTERS', $this->params->get('min')));
        }

        // Test if the username is allowed
        if (!SocialFieldsUserJoomlaUsernameHelper::allowed($username, $this->params, $current)) {
            $this->setError(JText::_('PLG_FIELDS_JOOMLA_USERNAME_NOT_ALLOWED'));

            return false;
        }

        // Test if the username provided is valid.
        if (!SocialFieldsUserJoomlaUsernameHelper::isValid($username, $this->params)) {
            $this->setError(JText::_('PLG_FIELDS_JOOMLA_USERNAME_PLEASE_ENTER_VALID_USERNAME'));

            return false;
        }

        // Test if the username is available.
        if (SocialFieldsUserJoomlaUsernameHelper::exists($username, $current)) {
            $this->setError(JText::_('PLG_FIELDS_JOOMLA_USERNAME_NOT_AVAILABLE'));

            return false;
        }

        return true;
    }

    public function onOAuthGetMetaFields(&$fields)
    {
        $fields[] = FD::config()->get('oauth.facebook.username', 'email');
    }

    public function onOAuthGetUserMeta(&$details, &$client)
    {
        $details['username'] = $details[FD::config()->get('oauth.facebook.username', 'email')];
    }

    public function onRegisterOAuthBeforeSave(&$post, &$client)
    {
        if (empty($post['username'])) {
            $post['username'] = $post[Foundry::config()->get('oauth.facebook.username', 'email')];
        }
    }

    public function onRegisterOAuthAfterSave(&$data, &$client, &$user)
    {
        $config = FD::config();

        if ($config->get('users.aliasName') != 'username') {
            return;
        }

        $this->saveAlias($data, $user);
    }
}
