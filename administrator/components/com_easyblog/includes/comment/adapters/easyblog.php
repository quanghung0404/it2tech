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

require_once(dirname(__FILE__) . '/base.php');

class EasyBlogCommentEasyBlog extends EasyBlogCommentBase
{
	/**
	 * Displays the comment output
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function html(EasyBlogPost &$blog)
	{
		// Load up template file
		$theme = EB::template();

		// Get comments
		$result = $this->prepareComments($blog);

		$comments = isset($result->comments) ? $result->comments : array();
		$pagination = isset($result->pagination) ? $result->pagination : false;

		// Retrieve the pagination for the blog entry comment view
		$pagination	= $pagination->getPagesLinks();

		// Get user's information
		$profile = EB::user($this->my->id);

		// Retrieve blog posts url
		$url = base64_encode($blog->getPermalink());

		// Retrieve login url
		$loginUrl = EB::getLoginLink($url);

		// check if the user has subcribed to this thread
		$subscribed	= false;

		if (!$this->my->guest) {
			$model 	= EB::model('Blog');
			$subscribed = $model->isBlogSubscribedUser($blog->id, $this->my->id, $this->my->email);
		}

		// Determines if the user can register while commenting
		$registration = $this->canRegister();
		$date = EB::date();

		// Determines if we should show the website field
		$website = false;

		if ($this->config->get('comment_show_website') || $this->config->get('comment_required_website')) {
			$website = true;
		}

		// Determines if we should show the email field
		$email = false;

		if ($this->config->get('comment_show_email') || $this->config->get('comment_require_email') || $registration) {
			$email = true;
		}

		$language = JFactory::getLanguage();
		$rtl = $language->isRTL();

		$theme->set('rtl', $rtl);
		$theme->set('email', $email);
		$theme->set('website', $website);
		$theme->set('date', $date);
		$theme->set('user', $profile);
		$theme->set('loginURL', $loginUrl);
		$theme->set('blog', $blog);
		$theme->set('comments', $comments);
		$theme->set('pagination', $pagination);
		$theme->set('registration', $registration);
		$theme->set('subscribed', $subscribed);

		$output = $theme->output('site/comments/default');

		return $output;
	}


	/**
	 * Prepares the comment output
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function prepareComments(&$blog)
	{
		$config 	= EB::config();
		$my 		= JFactory::getUser();
		$result		= new stdClass();

		// add checking if comment system disabled by site owner
		if ($this->config->get('main_comment') && $blog->allowcomment) {

			// Retrieve blog comments
			$model = EB::model('Blog');
			$result->comments = $model->getBlogComment($blog->id);
			$result->pagination = $model->getPagination();
		}

		// Set the total number of comments for this blog post
		$blog->totalComments = EB::comment()->getCommentCount($blog);

		return $result;

	}

	/**
	 * Determines if user registration is allowed
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canRegister()
	{
		$registration 	= $this->config->get('comment_registeroncomment');

		// Double check this with Joomla's registration component
		if ($registration) {
			$params			= JComponentHelper::getParams('com_users');
			$registration	= $params->get('allowUserRegistration') == '0' ? false : $registration;
		}

		return $registration;
	}

	/**
	 * Renders the comment count for Komento
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCount(EasyBlogPost $post)
	{
		static $count = array();

		if (!isset($count[$post->id])) {

			$model = EB::model('Comment');
			$count[$post->id] = $model->getCount($post->id);
		}

		return $count[$post->id];
	}
}


