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
	 * Main display for the blog entry view
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		// Get the blog post id from the request
		$id = $this->input->get('id', 0, 'int');

		// Load the blog post now
		$post = EB::post($id);

		// If blog id is not provided correctly, throw a 404 error page
		if (!$id || !$post->id) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'));
		}

		// If the settings requires the user to be logged in, do not allow guests here.
		if ($this->my->guest && $this->config->get('main_login_read')) {
			return EB::requireLogin();
		}

		// Check if blog is password protected.
		$protected = $this->isProtected($post);

		if ($protected !== false) {
			return;
		}

		// If the blog post is already deleted, we shouldn't let it to be accessible at all.
		if ($post->isTrashed()) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'));
		}

		// Check if the blog post is trashed
		if (!$post->isPublished() && $this->my->id != $post->created_by && !EB::isSiteAdmin()) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'));
		}

		// Check for team's privacy
		$allowed = $this->checkTeamPrivacy($post);

		if ($allowed === false) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_TEAMBLOG_MEMBERS_ONLY'));
		}

		// Check if the blog post is accessible.
		$accessible = $post->isAccessible();

		if (!$accessible->allowed) {
			echo $accessible->error;

			return;
		}

		// Increment the hit counter for the blog post.
		$post->hit();

		// Format the post
		$post = EB::formatter('entry', $post);

		$theme 	= EB::template();
		$theme->set('post', $post);

		$output = $theme->output('site/blogs/entry/default.print');
		echo $output;
	}

	/**
	 * Determines if the user is allowed to view this post if this post is associated with a team.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function checkTeamPrivacy(EasyBlogPost &$blog)
	{
		$id = $blog->getTeamAssociation();

		// This post is not associated with any team, so we do not need to check anything on the privacy
		if (!$id) {
			return true;
		}

		$team = EB::table('TeamBlog');
		$team->load($id);

		// If the team access is restricted to members only
		if ($team->access == EBLOG_TEAMBLOG_ACCESS_MEMBER && !$team->isMember($this->my->id) && !EB::isSiteAdmin()) {
			return false;
		}

		// If the team access is restricted to registered users, ensure that they are logged in.
		if ($team->access == EBLOG_TEAMBLOG_ACCESS_REGISTERED && $this->my->guest) {
			echo EB::showLogin();

			return false;
		}

		return true;
	}

	/**
	 * Determines if the current post is protected
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isProtected(EasyBlogPost &$blog)
	{
		// Password protection disabled
		if (!$this->config->get('main_password_protect')) {
			return false;
		}

		// Site admin should not be restricted
		if (EB::isSiteAdmin()) {
			return false;
		}

		// Blog does not contain any password protection
		if (empty($blog->blogpassword)) {
			return false;
		}

		// User already entered password
		if ($blog->verifyPassword()) {
			return false;
		}
		// Set the return url to the current url
		$return = base64_encode(JRequest::getURI());
		$category = $blog->getPrimaryCategory();
		$blog->category = $category;
		$blog->categories = $blog->getCategories();

		// Get the blogger object
		$blogger = EB::user($blog->created_by);

		// Set the author object into the table.
		$blog->author 	= $blogger;

		$this->set('blogger', $blog->author);
		$this->set('return', $return);
		$this->set('blog', $blog);
		$this->set('category', $category);

		parent::display('blogs/entry/default.protected');

		return;
	}
}
