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
 * Processes ajax calls for the Joomla_Username field.
 *
 * @since   1.0
 * @author  Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserJoomla_Username extends SocialFieldItem
{
    /**
     * Validates the username.
     *
     * @since   1.0
     * @access  public
     * @param   null
     * @return  JSON    A jsong encoded string.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function isValid()
    {
        // Render the ajax lib.
        $ajax = FD::getInstance('Ajax');

        // Get the userid
        $userid = JRequest::getInt('userid', 0);

        // Get the event
        $event = JRequest::getString('event');

        // Set the current username
        $current = '';

        if (!empty($userid)) {
            $user = FD::user($userid);
            $current = $user->username;
        }

        // Get the provided username that the user has typed.
        $username = JRequest::getVar('username', '');

        // Username is required, check if username is empty
        if (JString::strlen($username) < $this->params->get('min')) {
            return $ajax->reject(JText::sprintf('PLG_FIELDS_JOOMLA_USERNAME_MIN_CHARACTERS', $this->params->get('min')));
        }

        // Test if username is allowed (by pass for adminedit).
        if ($event !== 'onAdminEdit' && !SocialFieldsUserJoomlaUsernameHelper::allowed($username, $this->params, $current)) {
            return $ajax->reject(JText::_('PLG_FIELDS_JOOMLA_USERNAME_NOT_ALLOWED'));
        }

        // Test if username exists.
        if (SocialFieldsUserJoomlaUsernameHelper::exists($username, $current)) {
            return $ajax->reject(JText::_('PLG_FIELDS_JOOMLA_USERNAME_NOT_AVAILABLE'));
        }

        // Test if the username is valid
        if (!SocialFieldsUserJoomlaUsernameHelper::isValid($username, $this->params)) {
            return $ajax->reject(JText::_('PLG_FIELDS_JOOMLA_USERNAME_IS_INVALID'));
        }

        $text = JText::_('PLG_FIELDS_JOOMLA_USERNAME_AVAILABLE');

        return $ajax->resolve($text);
    }

}
