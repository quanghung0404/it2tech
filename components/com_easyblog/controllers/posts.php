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

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerPosts extends EasyBlogController
{
	public function __construct($options = array())
	{
		parent::__construct($options);

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');

		$this->registerTask('archive', 'toggleArchive');
		$this->registerTask('unarchive', 'toggleArchive');
	}

	/**
	 * Duplicates a blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function copy()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the id's
		$ids = $this->input->get('ids', array(), 'array');

		// Default redirection
		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_COPY_ERROR', 'error');
			return $this->app->redirect($redirect);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$post = EB::post($id);
			$post->duplicate();
		}

		$this->info->set('COM_EASYBLOG_DASHBOARD_BLOG_COPIED_SUCCESS', 'success');
		return $this->app->redirect($redirect);
	}


	/**
	 * Auto posts blog posts into social sites
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function autopost()
	{
		// Set the default redirection url
		$return = $this->input->get('return', '', 'default');

		if ($return) {
			$return = base64_encode($return);
		} else {
			$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);
		}

		// Get the auto post type
		$type = $this->input->get('type', '', 'cmd');

		// Get the pot id
		$id = $this->input->get('id', 0, 'int');

		// Load up the post
		$post = EB::post($id);

		// Try to autopost now
		$post->autopost($type);

		$message = JText::sprintf('COM_EASYBLOG_OAUTH_POST_SUCCESS', $type);

		$this->info->set($message, 'success');

		return $this->app->redirect($return);
	}


	/**
	 * Authorizes the password for the blog post.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function authorize($task = null)
	{
		// Default return url
		$return = $this->input->get('return', '', 'default');

		if ($return) {
			$return = base64_encode($return);
		}

		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			$this->info->set('COM_EASYBLOG_INVALID_ID_PROVIDED', 'error');

			return $this->app->redirect($return);
		}

		if (!$return) {
			$post = EB::post($id);

			$return = $post->getPermalink(false);
		}

		// Get the submitted password
		$password = $this->input->get('blogpassword_' . $id);

		// Get the current session data
		$session = JFactory::getSession();
		$session->set('PROTECTEDBLOG_' . $id, $password, 'EASYBLOG');

		$this->app->redirect($return);
	}

	/**
	 * Approves a post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function approve()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the post id
		$ids = $this->input->get('ids', array(), 'array');

		// Get any return urls.
		$return = $this->getReturnURL();

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'), 'error');
			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$post = EB::post($id);

			if (!$post->canModerate()) {
				$this->info->set(JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'), 'error');
				return $this->app->redirect($return);
			}

			// Approve the post
			$post->approve();
		}

		$this->info->set('COM_EASYBLOG_POST_APPROVED_SUCCESSFULLY', 'success');

		return $this->app->redirect($return);
	}


	/**
	 * Rejects a post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function reject()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the post id
		$ids = $this->input->get('ids', array(), 'array');
		$message = $this->input->get('message', '', 'default');

		// Get any return urls.
		$return = $this->getReturnURL();

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'), 'error');
			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$post = EB::post($id);

			if (!$post->canModerate()) {
				$this->info->set(JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'), 'error');
				return $this->app->redirect($return);
			}

			// Approve the post
			$post->reject($message, $this->my->id);
		}

		$this->info->set('COM_EASYBLOG_POST_REJECTED_SUCCESSFULLY', 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Archives / unarchives a post or a list of post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function toggleArchive()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the post id
		$ids = $this->input->get('ids', 0, 'array');

		// Get any return url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Test the provided id
		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'), 'error');
			return $this->app->redirect($return);
		}

		// Determines the current operation
		$task = $this->getTask();

		foreach ($ids as $id) {
			$id = (int) $id;

			$post = EB::post($id);

			// Check for permissions
			if (!$post->canModerate()) {
				$this->info->set(JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'), 'error');
				return $this->app->redirect($return);
			}

			$post->$task();
		}

		$message = 'COM_EASYBLOG_POST_ARCHIVED_SUCCESSFULLY';

		if ($task == 'unarchive') {
			$message = 'COM_EASYBLOG_POST_UNARCHIVED_SUCCESSFULLY';
		}

		$this->info->set($message, 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Features a blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unfeature()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the list of blog id's
		$id = $this->input->get('id', '', 'int');

		// Get any return url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Check if user has access
		if (!EB::isSiteAdmin() && !$this->acl->get('feature_entry')) {

			$this->info->set('COM_EASYBLOG_NOT_ALLOWED', 'error');

			return $this->app->redirect($return);
		}

		// Load the blog object
		$post = EB::post($id);

		// Check if user has access
		if (!$id || !$post->id) {

			$this->info->set('COM_EASYBLOG_INVALID_ID_PROVIDED', 'error');

			return $this->app->redirect($return);
		}

		// Ensure that the current user can moderate the post.
		if (!$post->canModerate()) {
			$this->info->set(JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'), 'error');
			return $this->app->redirect($return);
		}

		// Unfeature the post
		$post->removeFeatured();

		$message = JText::_('COM_EASYBLOG_BLOG_POSTS_UNFEATURED_SUCCESS');

		$this->info->set($message, 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Features a blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function feature()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the list of blog id's
		$id = $this->input->get('id', '', 'int');

		// Get any return url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Check if user has access
		if (!EB::isSiteAdmin() && !$this->acl->get('feature_entry')) {

			EB::info()->set('COM_EASYBLOG_NOT_ALLOWED', 'error');

			return $this->app->redirect($return);
		}

		// Load the blog object
		$post = EB::post($id);

		// Do not allow password protected blog posts to be featured
		if ($this->config->get('main_password_protect') && !empty($blog->blogpassword)) {
			EB::info()->set('COM_EASYBLOG_PASSWORD_PROTECTED_CANNOT_BE_FEATURED', 'error');

			return $this->app->redirect($return);
		}

		// Feature the item
		$post->setFeatured();

		$message = JText::_('COM_EASYBLOG_BLOG_POSTS_FEATURED_SUCCESS');

		$this->info->set($message, 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Deletes a revision from the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteRevision()
	{
		// Check for token
		EB::checkToken();

		// Load the revision
		$id = $this->input->get('id', 0, 'int');
		$revision = EB::table('Revision');
		$revision->load($id);

		if (!$revision->canDelete()) {
			return $this->ajax->reject(EB::exception(JText::_('COM_EASYBLOG_COMPOSER_NOT_ALLOWED_TO_DELETE_REVISION')));
		}

		if (!$revision->delete()) {
			return $this->ajax->reject(EB::exception('COM_EASYBLOG_COMPOSER_REVISIONS_ERRORS_DELETING_REVISION'));
		}

		return $this->ajax->resolve();
	}



	/**
	 * Delete mulitple revisions from the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteRevisions()
	{
		// Check for token
		EB::checkToken();

		// Load the revision

		$ids = $this->input->get('ids', '', 'array');

		// Get any return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=revisions', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		foreach ($ids as $id) {

			$revision = EB::table('Revision');
			$revision->load($id);

			if (!$revision->canDelete()) {
				$this->info->set(JText::_('COM_EASYBLOG_COMPOSER_NOT_ALLOWED_TO_DELETE_REVISION'), 'error');
				return $this->app->redirect($return);
			}

			$revision->delete();
		}

		EB::info()->set(JText::_('COM_EASYBLOG_DELETE_REVISIONS_SUCCESS'), 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Deletes blog posts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Check for tokens
		EB::checkToken();

		// Get the list of blog id's
		$ids = $this->input->get('ids', '', 'array');

		// Get any return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		foreach ($ids as $id) {

			$post = EB::post($id);

			if (!$post->canDelete()) {
				$this->info->set(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_BLOG'), 'error');
				return $this->app->redirect($return);
			}

			$post->delete();
		}

		EB::info()->set(JText::_('COM_EASYBLOG_DASHBOARD_DELETE_SUCCESS'), 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Toggle publish for posts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function togglePublish()
	{
		// Check for tokens
		EB::checkToken();

		// Build the return url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Ensure that the user has access to publish items
		if ($this->my->guest) {
			return JError::raiseError(500, 'COM_EASYBLOG_NO_PERMISSION_TO_PUBLISH_OR_UNPUBLISH_BLOG');
		}

		// Get the task
		$task = $this->getTask();

		// Get id's
		$ids = $this->input->get('ids', '', 'array');

		foreach ($ids as $id) {

			$post = EB::post($id);

			if (!$this->acl->get('moderate_entry') && !$this->acl->get('publish_entry') && !EB::isSiteAdmin()) {
				$this->info->set(JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'), 'error');
				return $this->app->redirect($return);
			}

			if (method_exists($post, $task)) {
				$post->$task();
			}
		}


		$message = JText::_('COM_EASYBLOG_POSTS_PUBLISHED_SUCCESS');

		if ($task == 'unpublish') {
			$message = JText::_('COM_EASYBLOG_POSTS_UNPUBLISHED_SUCCESS');
		}

		// Set info data
		$this->info->set($message, 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Ensure that the user is allowed to save the blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function verifyAccess()
	{
		// Check for request forgeries
		EB::checkToken();

		// Ensure that the user must be logged into the site
		EB::requireLogin();

		// Ensure that the user really has permissions to create blog posts on the site
		if (!$this->acl->get('add_entry')) {
			throw EB::exception('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG');
		}

		// Ensure uid is provided
		$uid = $this->input->get('uid');
		if (empty($uid)) {
			throw EB::exception('COM_EASYBLOG_MISSING_UID');
		}
	}

	/**
	 * Given a revision id, update the post to use the revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function useRevision()
	{
		try {
			$this->verifyAccess();
		} catch (EasyBlogException $exception) {

			dump($exception->getMessage());
		}

		$return = $this->input->get('return', '', 'default');
		$uid = $this->input->get('uid', '', 'default');

		if (! $return) {
			$return = EBR::_('index.php?option=com_easyblog&view=composer&tmpl=component&uid=' . $uid, false);
		} else {
			$return = base64_decode($return);
		}

		// Load up the post
		$uid = $this->input->get('uid');
		$post = EB::post($uid);

		$post->published = EASYBLOG_POST_PUBLISHED;
		$post->useRevision();

		return $this->app->redirect($return);
	}

	/**
	 * Saves a blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save()
	{
		try {
			$this->verifyAccess();
		} catch(EasyBlogException $exception) {
			return $this->ajax->reject($exception);
		}

		// Get uid & data
		$uid = $this->input->get('uid');
		$data = $this->input->getArray('post');

		// var_dump($data);exit;

		// Contents needs to be raw
		$data['content'] = $this->input->get('content', '', 'raw');
		$data['document'] = $this->input->get('document', '', 'raw');

		// Load up the post library
		$post = EB::post($uid);
		$post->bind($data, array());

		// Default options
		$options = array();

		// since this is a form submit and we knwo the date that submited already with the offset timezone. we need to reverse it.
		$options['applyDateOffset'] = true;

		// check if this is a 'Apply' action or not.
		$isApply = $this->input->get('isapply', false, 'bool');


		// For autosave requests we do not want to run validation on it.
		$autosave = $this->input->get('autosave', false, 'bool');

		if ($autosave) {
			$options['validateData'] = false;
		}

		// Notify that post is successfully
		$message = ($isApply) ? 'COM_EASYBLOG_POST_APPLIED_SUCCESS' : 'COM_EASYBLOG_POST_SAVED_SUCCESS';
		$state = EASYBLOG_MSG_SUCCESS;

		if (!$post->isNew()) {
			$message = ($isApply) ? 'COM_EASYBLOG_POST_APPLIED_SUCCESS' : 'COM_EASYBLOG_POST_UPDATED_SUCCESS';
			$state = EASYBLOG_MSG_INFO;
		}

		// Save post
		try {
			$post->save($options);
		} catch(EasyBlogException $exception) {

			// Reject if there is an error while saving post
			return $this->ajax->reject($exception);
		}

		// If this is being submitted for approval
		if ($post->isBeingSubmittedForApproval()) {
			$message = 'COM_EASYBLOG_POST_SUBMITTED_FOR_APPROVAL';
			$state = EASYBLOG_MSG_WARNING;
		}

		// If this is a draft post.
		if ($post->isDraft()) {
			$message = 'COM_EASYBLOG_POST_SAVED_FOR_LATER_SUCCESS';
			$state = EASYBLOG_MSG_INFO;
		}

		// For autosave
		if ($autosave) {
			$date = EB::date();
			$date->setTimezone();

			$message = JText::sprintf('COM_EASYBLOG_POST_AUTOMATICALLY_SAVED_AT', $date->format(JText::_('COM_EASYBLOG_COMPOSER_AUTOSAVE_TIME_FORMAT'), true));
			$state = EASYBLOG_MSG_SUCCESS;
		}

		$exception = EB::exception($message, $state);

		// Resolve with post data
		$data = $post->toData();

		// Reduces number of slashes.
		// TODO: Should this be part of toData();
		$data->revision->content = json_decode($data->revision->content);


		// Determines if the current page load should be loading from block templates
		$postTemplate = EB::table('PostTemplate');
		$postTemplate->load($this->input->get('block_template', 0, 'int'));

		if (!$postTemplate->id || $postTemplate->id == 1) {
			$postTemplate = false;
		}


		// Generate the revision status html codes
		$theme = EB::template();
		$theme->set('post', $post);
		$theme->set('workingRevision', $post->getWorkingRevision());
		$theme->set('revisions', $post->getRevisions());
		$theme->set('postTemplate', $postTemplate);

		$revisionStatus = $theme->output('site/composer/panel/revisions/list');

		// Get the post's edit url
		$editLink = $post->getEditLink(false);

		// Get the post's preview url
		$previewLink = $post->getPreviewLink(false);

		return $this->ajax->resolve($data, $exception, $revisionStatus, $editLink, $previewLink);
	}

}
