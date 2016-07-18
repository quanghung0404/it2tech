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

// Import main controller
ES::import('site:/controllers/controller');

jimport('joomla.filesystem.file');

class EasySocialControllerVideos extends EasySocialController
{
	/**
	 * Retrieves a list of videos from the site
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function getVideos()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get a list of filters
		$filter = $this->input->get('filter', '', 'word');
		$category = $this->input->get('categoryId', '', 'int');
		$sort = $this->input->get('sort', '', 'word');

		$uid = $this->input->get('uid', 0, 'int');
		$type = $this->input->get('type', '', 'word');

		// Prepare the options
		$options = array();

		// Set the filter
		$options['filter'] = $filter;
		$options['category'] = $category;

		if ($sort) {
			$options['sort'] = $sort;
		}

		// Determines if we should retrieve featured videos
		$options['featured'] = false;

		if ($filter == 'featured') {
			$options['featured'] = true;
		}
		
		// Determines if this is to retrieve videos from groups or events
 		if ($uid && $type && $type != SOCIAL_TYPE_USER) {
 			$options['uid'] = $uid;
 			$options['type'] = $type;
 		}

 		if ($type == SOCIAL_TYPE_USER) {
 			$options['userid'] = $uid;
 			$options['filter'] = SOCIAL_TYPE_USER;
 		}

 		if ($filter == 'pending') {
 			$options['userid'] = $this->my->id;
 		}

		$model = ES::model('Videos');

		// Get the total numbers of videos to show on the page.
		$options['limit'] = FD::themes()->getConfig()->get('videos_limit', 10);

		// Get a list of videos from the site
		$videos = $model->getVideos($options);
		$pagination = $model->getPagination();

		$pagination->setVar('view' , 'videos');

		if ($filter && !$category) {
			$pagination->setVar('filter' , $filter);
		}

		if ($category) {
			$videoCategory = ES::table('VideoCategory');
			$videoCategory->load($category);

			$pagination->setVar('uid', $uid);
			$pagination->setVar('type', $type);
			$pagination->setVar('categoryId' , $videoCategory->getAlias());
		}

		if ($sort) {
			$pagination->setVar('sort' , $sort);
		}

		// If the current filter is not a featured filter, we should also pick up the featured videos
		$featuredVideos = array();

		if ($filter != 'featured') {
			$options['featured'] = true;
			$options['limit'] = false;
			$featuredVideos = $model->getVideos($options);
		}


		return $this->view->call(__FUNCTION__, $videos, $featuredVideos, $pagination, $filter);
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

		$id = $this->input->get('id', 0, 'int');
		$table = ES::table('Video');
		$table->load($id);

		$video = ES::video($table);

		// Ensure that the user is really allowed to delete the video
		if (!$video->canDelete()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_TO_DELETE'));
		}

		// Try to delete the video now
		$state = $video->delete();

		if (!$state) {
			return JError::raiseError(500, $video->getError());
		}

		// Set the success message
		$this->view->setMessage(JText::_('COM_EASYSOCIAL_VIDEOS_DELETE_SUCCESS'), SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__, $video);
	}

	/**
	 * Unfeatures a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unfeature()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get the video
		$id = $this->input->get('id', 0, 'int');
		$table = ES::table('Video');
		$table->load($id);

		$video = ES::video($table->uid, $table->type, $table);

		// Get the callback url
		$callback = $this->input->get('return', '', 'default');

		// Ensure that the video can be featured
		if (!$video->canUnfeature()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_TO_UNFEATURE'));
		}

		// Feature the video
		$video->removeFeatured();

		$this->view->setMessage(JText::_('COM_EASYSOCIAL_VIDEOS_UNFEATURED_SUCCESS'), SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__, $video, $callback);
	}


	/**
	 * Features a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function feature()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get the video
		$id = $this->input->get('id', 0, 'int');
		$table = ES::table('Video');
		$table->load($id);

		$video = ES::video($table->uid, $table->type, $table);

		// Get the callback url
		$callback = $this->input->get('return', '', 'default');

		// Ensure that the video can be featured
		if (!$video->canFeature()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_TO_FEATURE'));
		}

		// Feature the video
		$video->setFeatured();

		$this->view->setMessage(JText::_('COM_EASYSOCIAL_VIDEOS_FEATURED_SUCCESS'), SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__, $video, $callback);
	}

	/**
	 * Processes a video creation
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

		// Get the file data
		$file = $this->input->files->get('video');

		if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
			$file = null;
		}

		// Get the posted data
		$post = $this->input->post->getArray();

		// This video could be edited
		$id = $this->input->get('id', 0, 'int');
		$uid = $this->input->get('uid', $this->my->id, 'int');
		$type = $this->input->post->get('type', SOCIAL_TYPE_USER, 'word');

		$table = ES::table('Video');
		$table->load($id);

		$video = ES::video($uid, $type, $table);

		// Determines if this is a new video
		$isNew = $video->isNew();

		// If this is a new video, we should check against their permissions to create
		if (!$video->allowCreation() && $video->isNew()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_ADDING_VIDEOS'));
		}

		// Ensure that the user can really edit this video
		if (!$isNew && !$video->canEdit()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_EDITING'));
		}

		$options = array();

		// Video upload will create stream once it is published.
		// We will only create a stream here when it is an external link.
		if ($post['source'] != SOCIAL_VIDEO_UPLOAD) {
			$options = array('createStream' => true);
		}

		// If the source is from external link, we need to format the url properly.
		if ($post['source'] == 'link') {
			$post['link'] = $video->format($post['link']);
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
			return $this->view->call(__FUNCTION__, $video);
		}

		// Once a video is created successfully, remove any data associated from the session
		$session->set('videos.form', null, SOCIAL_SESSION_NAMESPACE);

		return $this->view->call(__FUNCTION__, $video, $isNew, $file);
	}

	/**
	 * Creates a new video from the story
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function uploadStory()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get the file data
		$file = $this->input->files->get('file');

		// Since user can't set the title of the video when uploading via the story form, we need to generate a title for it
		// based on the name of the video file.
		$data = array();

		// Format the title since the title is the file name
		$data['title'] = ucfirst(JFile::stripExt($file['name']));
		$data['source'] = 'upload';

		// Get a default category to house the video
		$model = ES::model('Videos');
		$category = $model->getDefaultCategory();

		$data['category_id'] = $category->id;

		// Get the uid and type
		$uid = $this->input->get('uid', 0, 'int');
		$type = $this->input->get('type', SOCIAL_TYPE_USER, 'word');

		$video = ES::video($uid, $type);
		$state = $video->save($data, $file);

		if (!$state) {
			$this->view->setMessage($video->getError(), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__, $video);
		}

		// Determines if the video should be processed immediately or it should be set under pending mode
		if ($this->config->get('video.autoencode')) {
			// After creating the video, process it
			$video->process();
		} else {
			// Just take a snapshot of the video
			$video->snapshot();
		}

		return $this->view->call(__FUNCTION__, $video);
	}

	/**
	 * Allows caller to remove a tag from the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeTag()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get the tag id
		$id = $this->input->get('id', 0, 'int');

		// Get the tag
		$tag = ES::table('Tag');
		$tag->load($id);

		// Check for permissions to delete this tag
		$table = ES::table('Video');
		$table->load($tag->target_id);

		$video = ES::video($table->uid, $table->type, $table);

		if (!$video->canRemoveTag()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_TO_REMOVE_TAGS'));
		}

		// Delete the tag
		$tag->delete();

		return $this->view->call(__FUNCTION__);
	}

	/**
	 * Allows caller to quickly tag people in this video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function tag()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get the user id's.
		$ids = $this->input->get('ids', array(), 'array');

		// Get the video
		$id = $this->input->get('id', 0, 'int');
		$table = ES::table('Video');
		$table->load($id);

		$video = ES::video($table->uid, $table->type, $table);

		// Insert the user tags
		$tags = $video->insertTags($ids);

		return $this->view->call(__FUNCTION__, $video, $tags);
	}

	/**
	 * Checks the status
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function status()
	{
		$id = $this->input->get('id', 0, 'int');
		$file = $this->input->get('file', '', 'raw');
		$uid = $this->input->get('uid', $this->my->id, 'int');
		$type = $this->input->get('type', SOCIAL_TYPE_USER, 'word');

		// Load the video
		$video = ES::video($uid, $type, $id);

		// Get the status of the video
		$status = $video->status();

		// If the video is processed successfully, publish the video now.
		if ($status === true) {
			$createStream = $this->input->get('createStream', true, 'bool');

			$video->publish(array('createStream' => $createStream));
		}

		return $this->view->call(__FUNCTION__, $video, $status);
	}

	/**
	 * Initiates a process request to convert video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function process()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get the video that we are trying to convert here.
		$id = $this->input->get('id', 0, 'int');
		$table = ES::table('Video');
		$table->load($id);

		$video = ES::video($table);

		// @TODO: Check if the user is really allowed to process this video
		if (!$video->canProcess()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_TO_PROCESS_VIDEO'));
		}

		// Only allow processing if the video is in pending state
		if (!$video->isPendingProcess()) {
			return JError::raiseError(500, JText::_('Not pending state'));
		}

		// Run the video process
		$video->process();


		return $this->view->call(__FUNCTION__, $video);
	}
}
