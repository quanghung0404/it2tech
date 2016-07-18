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

// Include main controller here.
require_once(__DIR__ . '/controller.php');

class EasySocialControllerVideos extends EasySocialController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('apply', 'save');

		$this->registerTask('makeFeatured', 'toggleDefault');
		$this->registerTask('removeFeatured', 'toggleDefault');
		$this->registerTask('unpublish', 'unpublish');
	}

	/**
	 * Toggles a video state
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function toggleDefault()
	{
		// Check for token 
		ES::checkToken();

		// Get the list of videos
		$cid = $this->input->get('cid', array(), 'array');

		// Get the task
		$task = $this->getTask();

		$message = 'COM_EASYSOCIAL_VIDEOS_SELECTED_VIDEOS_FEATURED';

		foreach ($cid as $id) {
			$id = (int) $id;

			$table = ES::table('Video');
			$table->load($id);

			$video = ES::video($table);

			// If it's set to toggle default, we need to know the video's featured state
			if ($task == 'toggleDefault') {

				if ($video->isFeatured()) {
					$message = 'COM_EASYSOCIAL_VIDEOS_SELECTED_VIDEOS_UNFEATURED';
					$video->removeFeatured();
				} else {
					$video->setFeatured();
				}
			}

			if ($task == 'makeFeatured') {
				$video->setFeatured();
			}

			if ($task == 'removeFeatured') {
				$message = 'COM_EASYSOCIAL_VIDEOS_SELECTED_VIDEOS_UNFEATURED';
				$video->removeFeatured();
			}
		}

		$this->view->setMessage($message);

		return $this->view->call(__FUNCTION__);
	}

	/**
	 * Publishes a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function publish()
	{
		// Check for request forgeries
		ES::checkToken();

		$ids = $this->input->get('cid', array(), 'array');

		foreach ($ids as $id) {
			$id = (int) $id;

			$table = ES::table('Video');
			$table->load($id);

			$video = ES::video($table);
			$video->publish(array('createStream' => false));
		}

		$this->view->setMessage(JText::_('Selected videos has been published successfully.'), SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__);
	}

	/**
	 * Unpublishes a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function unpublish()
	{
		// Check for request forgeries
		ES::checkToken();

		$ids = $this->input->get('cid', array(), 'array');

		foreach ($ids as $id) {
			$id = (int) $id;

			$table = ES::table('Video');
			$table->load($id);

			$video = ES::video($table);
			$video->unpublish();
		}

		$this->view->setMessage(JText::_('Selected videos has been unpublished successfully.'), SOCIAL_MSG_SUCCESS);
		return $this->view->call(__FUNCTION__);
	}

	/**
	 * Saves a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function save()
	{
		// Check for request forgeries
		ES::checkToken();

		$id = $this->input->get('id', 0, 'int');
		
		// Get the file data
		$file = $this->input->files->get('video');

		// Get the posted data
		$post = $this->input->post->getArray();

		$table = ES::table('Video');
		$table->load($id);

		$video = ES::video($table);

		$options = array();

		// Video upload will create stream once it is published.
		// We will only create a stream here when it is an external link.
		if ($post['source'] != SOCIAL_VIDEO_UPLOAD) {
			$options = array('createStream' => true);
		}

		// Save the video
		$state = $video->save($post, $file, $options);

		// Load up the session
		$session = JFactory::getSession();

		if (!$state) {

			// Store the data in the session so that we can repopulate the values again
			$data = json_encode($video->export());

			$session->set('videos.form', $data, SOCIAL_SESSION_NAMESPACE);

			$this->view->setMessage($video->getError(), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__, $video, $this->getTask());
		}

		// Once a video is created successfully, remove any data associated from the session
		$session->set('videos.form', null, SOCIAL_SESSION_NAMESPACE);
		$message = 'COM_EASYSOCIAL_VIDEOS_CREATED_SUCCESS';

		if ($id) {
			$message = 'COM_EASYSOCIAL_VIDEOS_UPDATED_SUCCESS';
		}

		$this->view->setMessage($message, SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__, $video, $this->getTask());
	}

	/**
	 * Deletes a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function delete()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get the list of ids
		$ids = $this->input->get('cid', array(), 'array');

		foreach ($ids as $id) {
			$id = (int) $id;

			$table = ES::table('Video');
			$table->load($id);

			$video = ES::video($table);
			$video->delete();
		}
		
		$this->view->setMessage('COM_EASYSOCIAL_SELECTED_VIDEOS_DELETED', SOCIAL_MSG_SUCCESS);
		return $this->view->call(__FUNCTION__);
	}
}
