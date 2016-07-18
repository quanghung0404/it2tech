<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewEntry extends EasyBlogView
{
	public function display( $tmpl = null )
	{
		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher = JDispatcher::getInstance();
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config = EasyBlogHelper::getConfig();

		//for trigger
		$params		= $mainframe->getParams('com_easyblog');

		$joomlaVersion = EasyBlogHelper::getJoomlaVersion();

	    $blogId = $this->input->get('id', 0, 'int');
	    if (empty($blogId)) {
			return JError::raiseError( 404, JText::_('COM_EASYBLOG_BLOG_NOT_FOUND') );
		}

	    $my 	= JFactory::getUser();

	    $blog	= EB::table('Blog');
	    $blog->load($blogId);

	    $post = EB::post($blogId);

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

		// Format the post
		$post = EB::formatter('entry', $post);
		$tags = $post->getTags();

		$theme 	= EB::template();
		$theme->set('post', $post);
		$theme->set('tags', $tags);

		$blogHtml	= $theme->output( 'site/blogs/entry/pdf' );


		$pageTitle	= EasyBlogHelper::getPageTitle($config->get('main_title'));
	    $document->setTitle( $post->title . $pageTitle );
		$document->setName($post->getPermalink());

		// Fix phoca pdf plugin.
		if( method_exists( $document , 'setArticleText' ) )
		{
			$document->setArticleText( $blogHtml );
		}

		echo $blogHtml;
		return;
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
}
