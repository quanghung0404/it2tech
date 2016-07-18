<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewSubscription extends EasyBlogView
{
	/**
	 * Displays the ajax subscription form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function form()
	{
		// Check for request forgeries
		EB::checkToken();

		// Determines if registration should be allowed here
		$registration = JComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0 ? 0 : $this->config->get('main_registeronsubscribe');

		// Guests are not allowed to subscribe
		if (!$this->my->id && !$this->config->get('main_allowguestsubscribe')) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_SUBSCRIPTION_PLEASE_LOGIN'));
		}

		if (!$this->acl->get('allow_subscription') ) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_SUBSCRIBE_BLOG'));
		}

		// Get the subscription type.
		$type = $this->input->get('type', '', 'word');
		$id   = $this->input->get('id', '', 'int');

		switch($type)
		{
			case EBLOG_SUBSCRIPTION_BLOGGER:
				$title	= JText::_('COM_EASYBLOG_SUBSCRIPTION_BLOGGER_DIALOG_TITLE');
				$desc	= JText::_('COM_EASYBLOG_SUBSCRIPTION_BLOGGER_DIALOG_CONTENT' );
			break;
			case EBLOG_SUBSCRIPTION_CATEGORY:

				$category = EB::table('Category');
				$category->load($id);

				$title	= JText::sprintf('COM_EASYBLOG_SUBSCRIPTION_CONTEXT_DIALOG_TITLE', $category->getTitle());
				$desc	= JText::sprintf('COM_EASYBLOG_SUBSCRIPTION_CATEGORY_DIALOG_CONTENT', $category->getTitle());
			break;
			case EBLOG_SUBSCRIPTION_TEAMBLOG:
				$title	= JText::_('COM_EASYBLOG_SUBSCRIPTION_TEAMBLOG_DIALOG_TITLE');
				$desc	= JText::_('COM_EASYBLOG_SUBSCRIBE_TEAM_INFORMATION');
			break;
			case EBLOG_SUBSCRIPTION_ENTRY:
				$title	= JText::_('COM_EASYBLOG_SUBSCRIPTION_ENTRY_DIALOG_TITLE');
				$desc	= JText::_('COM_EASYBLOG_SUBSCRIBE_ENTRY_INFORMATION');
			break;
			default:
				$title 	= JText::_('COM_EASYBLOG_SUBSCRIPTION_SITE_DIALOG_TITLE');
				$desc   = JText::_('COM_EASYBLOG_SUBSCRIPTION_SITE_DIALOG_CONTENT');
			break;
		}

		$theme 	= EB::template();

		$theme->set('registration', $registration);
		$theme->set('type', $type);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('id', $id);
		$theme->set('userId', $this->my->id);

		$output = $theme->output('site/subscription/dialog.form');

		return $this->ajax->resolve($output);
	}

	/**
	 * Allows caller to subscribe to the blog
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function subscribe()
	{
		// Check for request forgeries
		EB::checkToken();

		// Ensure that guests are allowed to subscribe
		if (!$this->acl->get('allow_subscription') && !$this->my->id && !$this->config->get('main_allowguestsubscribe')) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_SUBSCRIBE_BLOG'));
		}

		// Validate the email address
		$email = $this->input->get('email', '', 'default');

		if (!$email) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_SUBSCRIPTION_EMAIL_EMPTY_ERROR'));
		}

		// Test if email is valid
		$valid = EB::string()->isValidEmail($email);

		if (!$valid) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_SUBSCRIPTION_EMAIL_INVALID_ERROR'));
		}

		// Determines if the user wants to register
		$registered = $this->input->get('register', '', 'bool');
		$name = $this->input->get('name', '', 'default');
		$username = $this->input->get('username', '', 'default');
		$id = $this->input->get('id', '', 'int');
		$type  = $this->input->get('type', '', 'string');
		$userId  = $this->input->get('userId', 0, 'int');

		if (!$name) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_SUBSCRIPTION_NAME_EMPTY_ERROR'));
		}

		if ($registered && !$this->my->id && !$username) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_SUBSCRIPTION_USERNAME_EMPTY_ERROR'));
		}

		// Load up the registration library
		$lib = EB::registration();

		// Try to validate the username and email
		$options = array('username' => $username, 'email' => $email);
		$validated = $lib->validate();

		if ($validated !== true) {
			return $this->ajax->reject(JText::_($validated));
		}

		// Add the user's name
		$options['name'] = $name;

		// if the user is guest
		if ($userId == 0 && $registered) {
			// Add the user to the system
			$userId = $lib->addUser($options);
		}

		// Process mailchimp subscriptions here.
		$mailchimp = EB::mailchimp()->subscribe($email, $name);

		// Process mailchimp subscriptions here.
		$sendy = EB::sendy()->subscribe($email, $name);

		// Only use our built in subscription if mailchimp and sendy didn't send anything
		if (!$mailchimp && !$sendy) {

			// Since we have already merged all these tables into one, we don't need to use separate methods
			// to insert new subscriptions
			$subscription = EB::table('Subscriptions');
			$options = array('email' => $email, 'uid' => $id, 'utype' => $type);

			if (!$userId) {
				$options['user_id'] = 0;
			} else {
				$options['user_id'] = $userId;
			}

			if ($name) {
				$options['fullname'] = $name;
			}

			// If this is a valid user, perhaps he's trying to change his email
			if ($userId) {
				$subscription->load(array('user_id' => $userId, 'utype' => $type, 'uid' => $id));
			} else {
				$subscription->load($options);
			}

			// Bind the data
			$subscription->bind($options);

			// Try to save the record now
			$state = $subscription->store();

			// We don't really need to do anythin
			// If the subscribed method returns false, we could assume that they are already subscribed previously
			if (!$state) {
				return $this->ajax->reject(JText::_('COM_EASYBLOG_SUBSCRIPTION_ALREADY_SUBSCRIBED_ERROR'));
			}
		}

		$theme = EB::template();
		$theme->set('email', $email);
		$theme->set('registered', $registered);

		$output = $theme->output('site/subscription/dialog.subscribed');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays a confirmation dialog to ask if the user really wants to unsubscribe
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmUnsubscribe()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the subscription id
		$id = $this->input->get('id', 0, 'int');

		// We also allow caller to unsubscribe by uid and type
		$uid = $this->input->get('uid');
		$type = $this->input->get('type');

		// Get the return url
		$return = $this->input->get('return', '', 'raw');

		// Try to load the subscription
		$subscription = EB::table('Subscriptions');
		$subscription->load($id);

		// Try to load by uid and type
		if (!$subscription->id || !$id) {
			$subscription->load(array('uid' => $uid, 'utype' => $type));
		}

		if (!$subscription->id || !$id) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_SUBSCRIPTION_INVALID_ID'));
		}

		$theme = EB::template();
		$theme->set('subscription', $subscription);
		$theme->set('return', $return);

		$output = $theme->output('site/subscription/dialog.confirm.unsubscribe');

		return $this->ajax->resolve($output);
	}
}

