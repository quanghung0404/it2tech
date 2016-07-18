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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewEntry extends EasyBlogView
{
	/**
	 * Displays confirmation to publish a previewed post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function confirmUseRevision()
	{
		$uid = $this->input->get('uid', '', 'default');

		$post = EB::post($uid);

		if (!$uid || !$post->id) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Default return url.
		$return = base64_encode($post->getPermalink());

		// Theme uses back end language file
		EB::loadLanguages(JPATH_ADMINISTRATOR);
		
		$theme = EB::template();
		$theme->set('post', $post);
		$theme->set('return', $return);

		$output = $theme->output('site/blogs/entry/dialog.userevision');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to publish a previewed post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function confirmPublish()
	{
		$id = $this->input->get('id', 0, 'int');

		$post = EB::post($id);

		if (!$id || !$post->id) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Default return url.
		$return = base64_encode($post->getPermalink());

		// Theme uses back end language file
		EB::loadLanguages(JPATH_ADMINISTRATOR);
		
		$theme = EB::template();
		$theme->set('post', $post);
		$theme->set('return', $return);

		$output = $theme->output('site/blogs/entry/dialog.publish');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to reject a post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function confirmReject()
	{
		$id = $this->input->get('id', 0, 'int');

		$post = EB::post($id);

		if (!$id || !$post->id) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Check if the user has access to approve
		if (!$post->canModerate()) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'));
		}

		// Default return url.
		$return = base64_encode($post->getPermalink());

		// Theme uses back end language file
		EB::loadLanguages(JPATH_ADMINISTRATOR);
		
		$theme = EB::template();
		$theme->set('post', $post);
		$theme->set('return', $return);

		$output = $theme->output('site/blogs/entry/dialog.reject');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to approve a post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function confirmApprove()
	{
		$id = $this->input->get('id', 0, 'int');

		$post = EB::post($id);

		if (!$id || !$post->id) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Check if the user has access to approve
		if (!$post->canModerate()) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'));
		}

		// Default return url.
		$return = base64_encode($post->getPermalink());

		$theme = EB::template();
		$theme->set('post', $post);
		$theme->set('return', $return);

		$output = $theme->output('site/blogs/entry/dialog.approve');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to feature a post 
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function featurePost()
	{
		// Get the post id
		$id = $this->input->get('id', 0, 'int');

		// Load up the blog post
		$post = EB::post($id);

		if (!$id || !$post->id) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Check if the user has access to approve
		if (!$this->acl->get('feature_entry')) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		// Get the return url if there's any so that we can redirect them accordingly later
		$return = $this->input->get('return', '', 'default');

		$theme = EB::template();
		$theme->set('post', $post);
		$theme->set('return', $return);
		$output = $theme->output('site/blogs/entry/dialog.feature');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to unfeature a post 
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function unfeaturePost()
	{
		// Get the post id
		$id = $this->input->get('id', 0, 'int');

		// Load up the blog post
		$post = EB::post($id);

		if (!$id || !$post->id) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Check if the user has access to approve
		if (!$post->canModerate()) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'));
		}

		// Get the return url if there's any so that we can redirect them accordingly later
		$return = $this->input->get('return', '', 'default');

		if (!$return) {
			$return = base64_encode($post->getPermalink(false));
		}

		$theme = EB::template();
		$theme->set('post', $post);
		$theme->set('return', $return);
		$output = $theme->output('site/blogs/entry/dialog.unfeature');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to unarchive a post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function confirmUnarchive()
	{
		// Get the blog post id
		$id = $this->input->get('id', 0, 'int');

		// Load up the blog post
		$post = EB::post($id);

		if (!$id || !$post->id) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Check if the user has access to approve
		if (!$post->canModerate()) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'));
		}

		// Get the return url if there's any so that we can redirect them accordingly later
		$return = $this->input->get('return', '', 'default');

		$theme = EB::template();
		$theme->set('post', $post);
		$theme->set('return', $return);
		$output = $theme->output('site/blogs/entry/dialog.unarchive');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to archive a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function confirmArchive()
	{
		// Get the blog post id
		$id = $this->input->get('id', 0, 'int');

		// Load up the blog post
		$post = EB::post($id);

		if (!$id || !$post->id) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Check if the user has access to approve
		if (!$post->canModerate()) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'));
		}

		// Get the return url if there's any so that we can redirect them accordingly later
		$return = $this->input->get('return', '', 'default');

		$theme = EB::template();
		$theme->set('post', $post);
		$theme->set('return', $return);
		$output = $theme->output('site/blogs/entry/dialog.archive');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays a delete confirmation dialog
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function confirmDelete()
	{
		// Get the blog post id
		$id = $this->input->get('id', 0, 'int');

		// Load up the blog post
		$post = EB::post($id);

		if (!$id || !$post->id) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Check if the user has access to delete.
		if (!$post->canDelete()) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'));
		}

		// Get the return url if there's any so that we can redirect them accordingly later
		$return = $this->input->get('return', '', 'default');

		$theme = EB::template();
		$theme->set('post', $post);
		$theme->set('return', $return);
		$output = $theme->output('site/blogs/entry/dialog.delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays an unpublish confirmation dialog
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function confirmUnpublish()
	{
		$ajax = EB::ajax();

		// Get the blog post id
		$id = $this->input->get('id', 0, 'int');

		// Load up the blog post
		$post = EB::post($id);

		if (!$id || !$post->id) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Check if the user has access to approve
		if (!$post->canModerate() && !$post->canPublish()) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'));
		}

		// Get the return url if there's any so that we can redirect them accordingly later
		$return = $this->input->get('return', '', 'default');

		$theme = EB::template();
		$theme->set('post', $post);
		$theme->set('return', $return);
		$output = $theme->output('site/blogs/entry/dialog.unpublish');

		return $ajax->resolve($output);
	}

}

