<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Import parent view
ES::import('site:/views/views');

class EasySocialViewVideos extends EasySocialSiteView
{
	/**
	 * Processes videos on the site
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function process(SocialVideo $video)
	{
		return $this->ajax->resolve();
	}

	/**
	 * Returns the status of the processing
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function status($video, $progress)
	{
		// Once the progress is complete, we need to send the url to the video
		$permalink = $video->getPermalink(false);

		if ($progress === true) {
			return $this->ajax->resolve($permalink, 'done', $video->export(), $video->getThumbnail());
		}

		return $this->ajax->resolve($permalink, $progress);
	}

	/**
	 * Displays confirmation to feature videos
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmFeature()
	{
		// Get the video id
		$id = $this->input->get('id', 0, 'int');

		// Determines if the user wants to specify a custom callback url
		$callback = $this->input->get('callbackUrl', '', 'default');

		// Ensure that the user is really allowed to feature this video
		$videoTable = ES::table('Video');
		$videoTable->load($id);

		$video = ES::video($videoTable->uid, $videoTable->type, $videoTable);

		if (!$video->canFeature()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_TO_FEATURE'));
		}

		$theme = ES::themes();
		$theme->set('id', $id);
		$theme->set('callback', $callback);

		$output = $theme->output('site/videos/dialog.feature');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to unfeature videos
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmUnfeature()
	{
		// Get the video id
		$id = $this->input->get('id', 0, 'int');

		// Determines if the user wants to specify a custom callback url
		$callback = $this->input->get('callbackUrl', '', 'default');

		// Ensure that the user is really allowed to delete this video
		$videoTable = ES::table('Video');
		$videoTable->load($id);

		$video = ES::video($videoTable->uid, $videoTable->type, $videoTable);

		if (!$video->canUnfeature()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_TO_UNFEATURE'));
		}

		$theme = ES::themes();
		$theme->set('id', $id);
		$theme->set('callback', $callback);

		$output = $theme->output('site/videos/dialog.unfeature');

		return $this->ajax->resolve($output);
	}

	/**
	 * Post processing after a tag is deleted
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeTag()
	{
		return $this->ajax->resolve();
	}

	/**
	 * Displays confirmation to delete videos
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		// Get the video id
		$id = $this->input->get('id', 0, 'int');

		$videoTable = ES::table('Video');
		$videoTable->load($id);

		// Ensure that the user is really allowed to delete this video
		$video = ES::video($videoTable);

		if (!$video->canDelete()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_TO_DELETE'));
		}

		$theme = ES::themes();
		$theme->set('id', $id);

		$output = $theme->output('site/videos/dialog.delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Post processing after video is tagged with people
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function tag(SocialVideo $video, $tags = array())
	{
		$theme = ES::themes();
		$theme->set('tags', $tags);

		$output = $theme->output('site/videos/tags');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays encoding message
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function showEncodingMessage()
	{
		$theme = ES::themes();

		$output = $theme->output('site/videos/dialog.encoding');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays a dialog for users to tag
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function tagPeople()
	{
		$theme = ES::themes();

		// Get the video id
		$id = $this->input->get('id', 0, 'int');
		$exclusion = $this->input->get('exclusion', array(), 'array');

		$video = ES::video($id);

		// Get a list of users that are already tagged with this video
		$tags = $video->getTags();

		$theme->set('exclusion', $exclusion);

		$output = $theme->output('site/videos/dialog.tag');

		return $this->ajax->resolve($output);
	}

	/**
	 * Returns a list of videos on the site
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getVideos($videos = array(), $featuredVideos = array(), $pagination = null, $filter = null)
	{
		$output = '';

		// If there is a list of featured videos, we need to output them as well
		if ($featuredVideos) {
			$theme = ES::themes();
			$theme->set('featuredVideos', $featuredVideos);
			$output = $theme->output('site/videos/default.featured.items');
		}

		$theme = ES::themes();
		$theme->set('videos', $videos);
		$theme->set('filter', $filter);
		$theme->set('isFeatured', false);

		if ($filter == 'featured') {
			$theme->set('isFeatured', true);
		}

		if ($pagination) {
			$theme->set('pagination', $pagination);
		}

		$output .= $theme->output('site/videos/default.items');

		return $this->ajax->resolve($output);
	}
}
