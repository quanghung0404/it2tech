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
	public function display($tmpl = null)
	{
		// Get the blog post
		$id = $this->input->get('id', 0, 'int');

		// Load the blog post now
		$blog 	= EB::table('Blog');
		$blog->load($id);

		// If blog id is not provided correctly, throw a 404 error page
		if (!$id || !$blog->id) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'));
		}

		// If the settings requires the user to be logged in, do not allow guests here.
		if ($this->my->id <= 0 && $this->config->get('main_login_read')) {

			$url 	= EB::_('index.php?option=com_easyblog&view=entry&id=' . $id . '&layout=login', false);

			return $this->app->redirect($url);
		}

		// Check if blog is password protected.
		if ($this->config->get('main_password_protect') && !empty($blog->blogpassword) && !$blog->verifyPassword()) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'));
		}

		// If the blog post is already deleted, we shouldn't let it to be accessible at all.
		if ($blog->isTrashed()) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'));
		}

		// Check if the blog post is trashed
		if (!$blog->isPublished() && $my->id != $blog->created_by && !EB::isSiteAdmin()) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'));
		}

		// If the viewer is the owner of the blog post, display a proper message
		if ($this->my->id == $blog->created_by && !$blog->isPublished()) {
			$notice = JText::_('COM_EASYBLOG_ENTRY_BLOG_UNPUBLISHED_VISIBLE_TO_OWNER');
		}

		if (EB::isSiteAdmin() && !$blog->isPublished()) {
			$notice = JText::_('COM_EASYBLOG_ENTRY_BLOG_UNPUBLISHED_VISIBLE_TO_ADMIN');
		}

		$blog = EB::formatter('post', $blog);

		$this->set('post', $blog);
		
		return parent::display();
	}

}
