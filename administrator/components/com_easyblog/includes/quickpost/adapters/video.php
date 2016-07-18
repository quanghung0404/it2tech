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

require_once(__DIR__ . '/abstract.php');

class EasyBlogQuickPostVideo extends EasyBlogQuickPostAbstract
{
	/**
	 * Processes the content
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bind(&$blog)
	{
		$title = $this->input->get('title', '', 'default');
		$video = $this->input->get('content', '', 'default');

		// Since title is optional, we generate a random name for the video title
		if (!$title) {
			$title = JText::sprintf('COM_EASYBLOG_MICROBLOG_VIDEO_TITLE_GENERIC', EB::date()->format(JText::_('DATE_FORMAT_LC2')));
		}

		// Get the default settings
		$width = $this->config->get('max_video_width');
		$height = $this->config->get('max_video_height');

		// Now we need to embed the image URL into the blog content.
		$content = '[embed=videolink]{"video":"' . $video . '","width":"' . $width . '","height":"' .$height . '"}[/embed]';

		$blog->title = $title;
		$blog->content = $content;
		$blog->posttype = EBLOG_MICROBLOG_VIDEO;
	}

	/**
	 * Validates the quick post submission
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validate()
	{
		$content = $this->input->get('content', '', 'raw');

		if (!$content) {
			return EB::exception('COM_EASYBLOG_MICROBLOG_ERROR_EMPTY_VIDEO', 'error');
		}

		return true;
	}

	/**
	 * Since quotes are stored in the title, we don't really need to do anything here
	 */
	public function afterSave( &$blog )
	{
		return true;
	}

	public function getSuccessMessage()
	{
		return JText::_( 'COM_EASYBLOG_MICROBLOG_VIDEO_POSTED_SUCCESSFULLY' );
	}

	/**
	 * Formats a quick post content
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function format(EasyBlogPost &$blog)
	{
		// Find and replace all images in intro.
		$obj = self::getAndRemoveVideo($blog->intro);

		if ($obj) {
			$blog->intro = $obj->content;
			$blog->videos = $obj->videos;
		}

		// Lets strip out the images from the text / content.
		$obj = self::getAndRemoveVideo($blog->content);

		if ($obj) {
			$blog->content = $obj->content;
			$blog->videos = array_merge($obj->videos, $blog->videos);
		}

		return $blog;
	}

	/**
	 * Retrieves the video links from the content and remove the links from the content
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function getAndRemoveVideo($content)
	{
		$lib = EB::videos();

		// Retrieve all videos from the content
		$videos = $lib->getVideoObjects($content, true);

		// Strip out all video codes from the content
		$content = $lib->strip($content);

		$obj = new stdClass();
		$obj->content = $content;
		$obj->videos = $videos;

		return $obj;
	}
}
