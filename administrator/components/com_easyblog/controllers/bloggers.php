<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerBloggers extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();

		// Save tasks
		$this->registerTask('apply', 'save');

		// Toggle featured status for bloggers
		$this->registerTask('feature', 'toggleFeatured');
		$this->registerTask('unfeature', 'toggleFeatured');
	}


	/**
	 * Toggle featured bloggers.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function toggleFeatured()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check for acl rules.
		$this->checkAccess('user');

		// Get a list of user id's.
		$ids = $this->input->get('cid', array(), 'array');

		// Get the default redirection url
		$redirect = 'index.php?option=com_easyblog&view=bloggers';

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_INVALID_BLOGGER_ID', 'error');
			return $this->app->redirect($redirect);
		}

		// Get the current task so we know if should feature / unfeature items
		$task = $this->getTask();

		foreach ($ids as $id) {
			$id = (int) $id;

			$author = EB::user($id);

			if ($task == 'unfeature') {
				$author->removeFeatured();
			}

			if ($task == 'feature') {
				$author->setFeatured();
			}
		}

		$message = 'COM_EASYBLOG_BLOGGER_FEATURED_SUCCESSFULLY';

		if ($task == 'unfeature') {
			$message = 'COM_EASYBLOG_BLOGGER_UNFEATURED_SUCCESSFULLY';
		}

		$this->info->set($message, 'success');

		// Redirect back to the users listings.
		return $this->app->redirect('index.php?option=com_easyblog&view=bloggers');
	}

	/**
	 * Saves an author object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check for acl rules.
		$this->checkAccess('user');

 		// Create a new JUser object
 		$id = $this->input->get('id', 0, 'int');
 		$user = JFactory::getUser($id);

		// Get the user group's id
		$gid = $user->get('gid');

		// Get the posted data
		$post = $this->input->getArray('post');

		// Retrieve the username and password of the user
		$post['username'] = $this->input->get('username', '', 'default');
		$post['password'] = $this->input->get('password', '', 'default');
		$post['password2'] = $this->input->get('password2', '', 'default');

		// Get the data from Joomla's form
		if (isset($post['jform']['params'])) {
			$post['params'] = $post['jform']['params'];
			unset($post['jform']);
		}

		// Bind the post request on the user's object
		$state = $user->bind($post);

		// Default redirection url
		$redirect = 'index.php?option=com_easyblog&view=bloggers&layout=form&id=' . $user->id;

		if (!$state) {
			$this->info->set($user->getError(), 'error');

			return $this->app->redirect($redirect);
		}

		// Get the user's id
		if ($user->id == $this->my->id && $user->block) {
			$this->info->set(JText::_('You are not allowed to block yourself.'), 'error');

			return $this->app->redirect($redirect);
		}

		if ($user->authorise('core.admin') && $user->block) {
			$this->info->set(JText::_('You are not allowed to block a super administrator.'), 'error');

			return $this->app->redirect($redirect);
		}

		if ($user->authorise('core.admin') && !$this->my->authorise('core.admin')) {
			$this->info->set(JText::_('You cannot edit a Super User account.'), 'error');

			return $this->app->redirect($redirect);
		}

		$gid = $post['gid'];

		if (!empty($gid)) {

			$user->groups = array();

			foreach ($gid as $groupid) {
				$user->groups[$groupid] = $groupid;
			}
		}

		// Are we dealing with a new user which we need to create?
		$isNew = $user->id < 1;

		// Try to save the user now
		$state = $user->save();

		if (!$state) {

			$this->info->set($user->getError(), 'error');
			return $this->app->redirect($redirect);
		}

		// Update the user's session data if the current user is being edited to ensure that
		// the current user's data is correct
		if ($user->id == $this->my->id) {
			$session = JFactory::getSession();
			$session->set('user', $user);
		}

		// If this is a new record, ensure that the id is not set
		if ($isNew) {
			unset($post['id']);
		}

		// Set the proper permalink
		if (isset($post['user_permalink'])) {
			$post['permalink']  = $post['user_permalink'];
			unset( $post['user_permalink'] );
		}

		// Only allow site admins to add html codes for the description and biography
		if (EB::isSiteAdmin()) {

			$post['description'] = $this->input->get('description', '', 'raw');
			$post['biography'] = $this->input->get('biography', '', 'raw');
		}

		// After the user record is stored, we also want to update EasyBlog's records.
		$author = EB::user($user->id);

		// Bind the posted data
		$author->bind($post);

		// Get the file data
		$file = $this->input->files->get('avatar', '');

		if (isset($file['tmp_name']) && !empty($file['tmp_name'])) {
			$author->bindAvatar($file, EB::acl());
		}

		// Save other user parameters
		$registry = EB::registry();

		// save blogger multipletheme settin
		if (isset($post['theme'])) {
			$registry->set('theme', $post['theme']);
		}		

		// Save google profile url
		if (isset($post['google_profile_url'])) {
			$registry->set('google_profile_url', $post['google_profile_url']);
		}

		if (isset($post['show_google_profile_url'])) {
			$registry->set('show_google_profile_url', $post['show_google_profile_url']);
		}

		$author->params = $registry->toString();

		// Try to save the author object now
		$author->store();

		// Save the social settings
		$twitter = EB::table('OAuth');
		$twitter->load(array('user_id' => $user->id, 'type' => EBLOG_OAUTH_TWITTER));
		$twitter->auto = $this->input->get('integrations_twitter_auto');
		$twitter->message = $this->input->get('integrations_twitter_message', '' , 'default');

		// Try to save the twitter oauth object now
		$state = $twitter->store();

		if (!$state) {
			$this->info->set($twitter->getError(),'error');

			return $this->app->redirect($redirect);
		}

		// Try to save the linked in oauth object
		$linkedin = EB::table('OAuth');
		$linkedin->load(array('user_id' => $user->id, 'type' => EBLOG_OAUTH_LINKEDIN));
		$linkedin->auto = $this->input->get('integrations_linkedin_auto', '', 'bool');
		$linkedin->message = $this->input->get('integrations_linkedin_message', '', 'default');
		$linkedin->private = $this->input->get('integrations_linkedin_private', '', 'bool');

		// Save the oauth object now
		$state = $linkedin->store();

		if (!$state) {
			$this->info->set($linkedin->getError(), 'error');

			return $this->app->redirect($redirect);
		}

		// Store the Facebook oauth object now
		$facebook = EB::table('OAuth');
		$facebook->load(array('user_id' => $user->id, 'type' => EBLOG_OAUTH_FACEBOOK));
		$facebook->auto = $this->input->get('integrations_facebook_auto', '', 'bool');
		$facebook->message = '';

		// Save the facebook object now
		$state = $facebook->store();

		if (!$state) {
			$this->info->set($facebook->getError(), 'error');

			return $this->app->redirect($redirect);
		}

		// Save google adsense codes now
		if ($this->config->get('integration_google_adsense_enable')) {

			$adsense = EB::table('Adsense');
			$adsense->load($user->id);

			$adsense->code = $post['adsense_code'];
			$adsense->display = $post['adsense_display'];
			$adsense->published = $post['adsense_published'];

			$adsense->store();
		}

		// Store Feedburner's data
		$feedburner = EB::table('Feedburner');
		$feedburner->load($user->id);

		$feedburner->url = $post['feedburner_url'];
		$feedburner->store();


		// Get the current task
		$task = $this->getTask();

		$this->info->set('COM_EASYBLOG_BLOGGER_SAVED', 'success');

		if ($task == 'apply') {
			$redirect = 'index.php?option=com_easyblog&view=bloggers&layout=form&id=' . $user->id;
			return $this->app->redirect($redirect);
		}

		return $this->app->redirect('index.php?option=com_easyblog&view=bloggers');
	}

	/**
	 * Deletes a user from the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check for acl rules.
		$this->checkAccess('user');

		// Get a list of user id's to delete
		$ids = $this->input->get('cid', array(), 'array');

		// Default redirection url
		$redirect = 'index.php?option=com_easyblog&view=bloggers';

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_INVALID_BLOGGER_ID');

			return $this->app->redirect($redirect);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$user = JFactory::getUser($id);

			if ($user->authorise('core.admin')) {
				// Throw error
				$this->info->set('COM_EASYBLOG_BLOGGER_NOT_ALLOWED_TO_DELETE_SUPER_ADMIN');
				return $this->app->redirect($redirect);
			}

			if ($user->id == $this->my->id) {
				$this->info->set('COM_EASYBLOG_BLOGGER_NOT_ALLOWED_TO_DELETE_SELF');

				return $this->app->redirect($redirect);
			}

			// Try to delete the user
			$user->delete();
		}

		$this->info->set('COM_EASYBLOG_BLOGGER_DELETED_SUCCESSFULLY', 'success');

		return $this->app->redirect($redirect);
	}

}
