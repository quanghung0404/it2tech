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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Import parent view
FD::import('site:/views/views');

class EasySocialViewVideos extends EasySocialSiteView
{
	/**
	 * Default videos display page
	 *
	 * @since	1,4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		// Default page title
		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_VIDEOS_FILTER_ALL');

		$model = ES::model('Videos');

		// Get filter state
		$filter = $this->input->get('filter', '', 'word');
		$activeCategory = $this->input->get('categoryId', '', 'int');

		$uid = $this->input->get('uid', 0, 'int');
		$type = $this->input->get('type', '', 'word');
		$sort = $this->input->get('sort', '', 'word');

		// If this is filtered by category, we shouldn't set active on the filter.
		if ($activeCategory) {
			$filter = 'category';
		}

		// Get a list of video categories on the site
		$categories = $model->getCategories();

		// Prepare the options
		$options = array();

		// Set the filter
		$options['filter'] = $filter;
		$options['category'] = $activeCategory;
		$options['featured'] = false;

		if ($sort) {
			$options['sort'] = $sort;
		}

		// If user is viewing my specific filters, we need to update the title accordingly.
		if ($filter && $filter != 'category') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_VIDEOS_FILTER_' . strtoupper($filter);
			$this->page->title($title);
		}

		// Construct the video creation link
		$createLinkOptions = array('layout' => 'form');

		if ($activeCategory) {
			$createLinkOptions['categoryId'] = $activeCategory;
 		}

 		$cluster = null;

 		// Only for clusters
 		if ($uid && $type && $type != SOCIAL_TYPE_USER) {
 			$cluster = ES::cluster($type, $uid);

 			$createLinkOptions['uid'] = $cluster->getAlias();
 			$createLinkOptions['type'] = $type;

 			$options['uid'] = $uid;
 			$options['type'] = $type;
 		}

 		if ($type == SOCIAL_TYPE_USER) {

 			// If user is viewing their own videos, we should use filter = mine
 			$options['filter'] = SOCIAL_TYPE_USER;

 			if ($uid == $this->my->id) {
 				$options['filter'] = 'mine';
 			} else {
 				$options['userid'] = $uid;
 			}
 			
 			
 		}

 		// this checking used in normal videos to include the featured videos when 'featured' filter clicked.
 		if ($filter == 'featured') {
			$options['featured'] = true;
		}

		$options['limit'] = FD::themes()->getConfig()->get('videos_limit', 10);

		// For pending filters, we only want to retrieve videos uploaded by the current user
		if ($filter == 'pending') {
			$options['userid'] = $this->my->id;
		}

		// Get a list of videos from the site
		$videos = $model->getVideos($options);
		$pagination = $model->getPagination();

		// Get featured videos
		$featuredVideos = array();

		if ($filter != 'featured' || $activeCategory) {
			$options['featured'] = true;
			$options['limit'] = false;
			$featuredVideos = $model->getVideos($options);
		}

		// Get the total number of videos on the site
		$total = $model->getTotalVideos($options);

		// Get the total nuber of videos the current user has
		$totalUserVideos = $model->getTotalUserVideos($this->my->id);

		// Get the total number of featured videos on the site.
		$totalFeatured = $model->getTotalFeaturedVideos($options);

		// Get the total number of pending videos on the site.
		$totalPending = $model->getTotalPendingVideos($this->my->id);

 		$createLink = FRoute::videos($createLinkOptions);

 		// Determines if the current viewer is allowed to create new video
 		$adapter = ES::video($uid, $type);

 		// Determines if the user can access this videos section. 
 		// Instead of showing user 404 page, just show the restricted area.
 		if (!$adapter->canAccessVideos()) {
 			return $this->restricted($uid, $type);
 		}

 		$allowCreation = $adapter->allowCreation();

 		// Determines if the "My Videos" link should appear
 		$showMyVideos = true;

 		if ($uid && $type) {
 			$showMyVideos = false;
 		}

 		// If the current type is user, we shouldn't display the creation if they are viewing another person's list of videos
 		if ($type == SOCIAL_TYPE_USER && $uid != $this->my->id) {
 			$allowCreation = false;
 		}

 		// Default video title
 		if (!$filter && $uid && $type) {
 			$this->page->title($adapter->getListingPageTitle());
 		}

 		// Featured videos title
 		if ($filter == 'featured') {
 			$this->page->title($adapter->getFeaturedPageTitle());
 		}

 		$allVideosPageTitle = JText::_('COM_EASYSOCIAL_PAGE_TITLE_VIDEOS_FILTER_ALL');
 		$featuredVideosPageTitle = JText::_('COM_EASYSOCIAL_PAGE_TITLE_VIDEOS_FILTER_FEATURED');

		// If this is filter by category, we need to set the category title as the page title
		if ($filter == 'category' && $activeCategory) {
			$categoryObject = ES::table('VideoCategory');
			$categoryObject->load($activeCategory);

			$this->page->title($categoryObject->title);

			if ($uid && $type) {
				$this->page->title($adapter->getCategoryPageTitle($categoryObject));
			}
		}

		// If there is a uid and type present, we need to update the title of the page
 		if ($uid && $type) {
 			$allVideosPageTitle = $adapter->getListingPageTitle();
 			$featuredVideosPageTitle = $adapter->getFeaturedPageTitle();
 		}

		foreach ($categories as &$category) {

			$category->pageTitle = $category->title;

			if ($uid && $type) {
				$category->pageTitle = $adapter->getCategoryPageTitle($category);
			}
		}

		// Generate correct return urls for operations performed here
		$returnUrl = ESR::videos();

		if ($uid && $type) {
			$returnUrl = $adapter->getAllVideosLink($filter);
		}

		$returnUrl = base64_encode($returnUrl);

		$this->set('returnUrl', $returnUrl);
 		$this->set('featuredVideosPageTitle', $featuredVideosPageTitle);
 		$this->set('allVideosPageTitle', $allVideosPageTitle);
 		$this->set('filter', $filter);
 		$this->set('showMyVideos', $showMyVideos);
 		$this->set('uid', $uid);
 		$this->set('type', $type);
 		$this->set('adapter', $adapter);
 		$this->set('allowCreation', $allowCreation);
 		$this->set('cluster', $cluster);
 		$this->set('featuredVideos', $featuredVideos);
 		$this->set('createLink', $createLink);
		$this->set('currentCategory', $activeCategory);
		$this->set('filter', $filter);
		$this->set('totalFeatured', $totalFeatured);
		$this->set('totalPending', $totalPending);
		$this->set('totalUserVideos', $totalUserVideos);
		$this->set('total', $total);
		$this->set('videos', $videos);
		$this->set('categories', $categories);
		$this->set('sort', $sort);
		$this->set('pagination', $pagination);

		echo parent::display('site/videos/default');
	}

	/**
	 * Displays a restricted page
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id
	 */
	public function restricted($uid = null, $type = SOCIAL_TYPE_USER)
	{
		if ($type == SOCIAL_TYPE_USER) {
			$node = FD::user($uid);
		}

		if ($type == SOCIAL_TYPE_GROUP) {
			$node = FD::group($uid);
		}

		$this->set('showProfileHeader', true);
		$this->set('uid', $uid);
		$this->set('type', $type);
		$this->set('node', $node);

		echo parent::display( 'site/videos/restricted' );
	}

	/**
	 * Displays the single video item
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function item()
	{
		// Get the video id
		$id = $this->input->get('id', 0, 'int');

		$table = ES::table('Video');
		$table->load($id);

		// Load up the video
		$video = ES::video($table->uid, $table->type, $table);

		// Ensure that the viewer can really view the video
		if (!$video->isViewable()) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_VIEWING'));
		}

		// Set the page title
		$this->page->title($video->getTitle());

		// Whenever a viewer visits a video, increment the hit counter
		$video->hit();

		// Retrieve the reports library
		$reports = $video->getReports();

		$streamId = $video->getStreamId('create');

		// Retrieve the comments library
		$comments = $video->getComments('create', $streamId);

		// Retrieve the likes library
		$likes = $video->getLikes('create', $streamId);

		// Retrieve the privacy library
		$privacyButton = $video->getPrivacyButton();

		// Retrieve the sharing library
		$sharing = $video->getSharing();

		// Retrieve the tags
		$tags = $video->getTags();
		$tagsList = '';

		if ($tags) {
			$tagsArray = array();

			foreach ($tags as $tag) {
				$tagsArray[] = $tag->item_id;
			}

			$tagsList = json_encode($tagsArray);
		}

		// Retrieve the cluster associated with the video
		$cluster = $video->getCluster();

		// Opengraph tags for video
		$this->opengraph->addTitle($video->getTitle());
		$this->opengraph->addDescription($video->description);
		$this->opengraph->addImage($video->getThumbnail());
		$this->opengraph->addUrl($video->getExternalPermalink());
		$this->opengraph->addVideo($video);

		// Get random videos from the same category
		$otherVideos = array();

		if ($this->config->get('video.layout.item.recent')) {
			$options = array('category_id' => $video->category_id, 'exclusion' => $video->id, 'limit' => $this->config->get('video.layout.item.total'));
			$model = ES::model('Videos');
			$otherVideos = $model->getVideos($options);
		}

		// Update the back link if there is an "uid" or "type" in the url
		$uid = $this->input->get('uid', '');
		$type = $this->input->get('type', '');
		$backLink = ESR::videos();

		if ($uid && $type) {
			$backLink = $video->getAllVideosLink();	
		}
		

		$this->set('tagsList', $tagsList);
		$this->set('otherVideos', $otherVideos);
		$this->set('backLink', $backLink);
		$this->set('tags', $tags);
		$this->set('sharing', $sharing);
		$this->set('reports', $reports);
		$this->set('comments', $comments);
		$this->set('likes', $likes);
		$this->set('privacyButton', $privacyButton);
		$this->set('video', $video);

		echo parent::display('site/videos/item');
	}

	/**
	 * Displays the edit form for a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function form()
	{
		// Only logged in users should be allowed to create videos
		ES::requireLogin();

		// Determines if a video is being edited
		$id = $this->input->get('id', 0, 'int');
		$uid = $this->input->get('uid', 0, 'int');
		$type = $this->input->get('type', '', 'word');

		// Load the video
		$video = ES::video($uid, $type, $id);

		// Retrieve any previous data
		$session = JFactory::getSession();
		$data = $session->get('videos.form', null, SOCIAL_SESSION_NAMESPACE);

		if ($data) {
			$data = json_decode($data);

			$video->bind($data);
		}

		// Ensure that the current user can create this video
		if (!$id && !$video->allowCreation()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_ADDING_VIDEOS'));
		}

		// Ensure that the current user can really edit this video
		if ($id && !$video->isEditable()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_EDITING'));
		}

		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_CREATE_VIDEO');

		if ($id && !$video->isNew()) {
			$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_EDIT_VIDEO');
		}

		$model = ES::model('Videos');

		// Pre-selection of a category
		$defaultCategory = $model->getDefaultCategory();
		$defaultCategory = $defaultCategory ? $defaultCategory->id : 0;

		$defaultCategory = $this->input->get('categoryId', $defaultCategory, 'int');

		// Get a list of video categories
		$options = array();

		if (!$this->my->isSiteAdmin()) {
			$options = array('respectAccess' => true, 'profileId' => $this->my->getProfile()->id);
		}

		$categories = $model->getCategories($options);

		$privacy = ES::privacy();

		// Retrieve video tags
		$tags = $video->getTags();
		$tagItemList = array();

		if ($tags) {
			foreach($tags as $tag) {
				$tagItemList[] = $tag->item_id;
			}
		}

		$isCluster = ($uid && $type && $type != SOCIAL_TYPE_USER) ? true : false;

		// Construct the cancel link
		$options = array();

		if ($uid && $type) {
			$options['uid'] = $uid;
			$options['type'] = $type;
		}

		$returnLink = FRoute::videos($options);

		if ($video->id) {
			$returnLink = $video->getPermalink();
		}

		// Get the maximum file size allowed
		$uploadLimit = $video->getUploadLimit(false);

		$this->set('returnLink', $returnLink);
		$this->set('uploadLimit', $uploadLimit);
		$this->set('defaultCategory', $defaultCategory);
		$this->set('tags', $tags);
		$this->set('tagItemList', $tagItemList);
		$this->set('video', $video);
		$this->set('privacy', $privacy);
		$this->set('categories', $categories);
		$this->set('isCluster', $isCluster);

		return parent::display('site/videos/form');
	}

	/**
	 * Displays the process to transcode the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function process()
	{
		$id = $this->input->get('id', 0, 'int');
		$uid = $this->input->get('uid', 0, 'int');
		$type = $this->input->get('type', '', 'word');

		$video = ES::video($uid, $type, $id);

		// Ensure that the current user really owns this video
		if (!$video->canProcess()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_PROCESS'));
		}

		$cluster = null;

		if ($uid && $type) {
			$cluster = ES::cluster($type, $uid);
		}

		$this->set('cluster', $cluster);
		$this->set('uid', $uid);
		$this->set('type', $type);
		$this->set('video', $video);

		echo parent::display('site/videos/process');
	}

	/**
	 * Post process after a video is deleted from the site
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete($video)
	{
		$this->info->set($this->getMessage());

		$redirect = $video->getAllVideosLink('', false);

		return $this->app->redirect($redirect);
	}

	/**
	 * Post process after a video is unfeatured on the site
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unfeature($video, $callback = null)
	{
		$this->info->set($this->getMessage());

		$redirect = $video->getAllVideosLink('featured', false);

		if ($callback) {
			$redirect = base64_decode($callback);
		}

		return $this->app->redirect($redirect);
	}

	/**
	 * Post process after a video is featured on the site
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function feature($video, $callback = null)
	{
		$this->info->set($this->getMessage());

		$redirect = $video->getAllVideosLink('featured', false);

		if ($callback) {
			$redirect = base64_decode($callback);
		}

		return $this->app->redirect($redirect);
	}

	/**
	 * Post process after a video is stored
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save(SocialVideo $video, $isNew, $file)
	{
		// If there's an error, redirect them back to the form
		if ($this->hasErrors()) {
			$this->info->set($this->getMessage());

			$options = array('layout' => 'form');

			if (!$video->isNew()) {
				$options['id'] = $video->id;
			}

			$url = FRoute::videos($options, false);

			return $this->app->redirect($url);
		}

		$message = 'COM_EASYSOCIAL_VIDEOS_ADDED_SUCCESS';

		if (!$isNew) {
			$message = 'COM_EASYSOCIAL_VIDEOS_UPDATED_SUCCESS';
		}

		// If this is a video link, we should just redirect to the video page.
		if ($video->isLink()) {

			$url = $video->getPermalink(false);

			$this->setMessage($message, SOCIAL_MSG_SUCCESS);
			$this->info->set($this->getMessage());

			return $this->app->redirect($url);
		}


		// Should we redirect the user to the progress page or redirect to the pending video page
		$options = array('id' => $video->getAlias());

		if ($isNew && $file || !$isNew && $file) {
			// If video will be processed by cronjob, do not redirect to the process page
			if (!$this->config->get('video.autoencode')) {
				$options = array('filter' => 'pending');

				if ($isNew) {
					$message = 'COM_EASYSOCIAL_VIDEOS_UPLOAD_SUCCESS_AWAIT_PROCESSING';
				}
			} else {
				$options['layout'] = 'process';

				if ($isNew) {
					$message = 'COM_EASYSOCIAL_VIDEOS_UPLOAD_SUCCESS_PROCESSING_VIDEO_NOW';
				}
			}
		}

		if (!$isNew && $video->isPublished()) {
			$options['layout'] = 'item';
		}

		$this->setMessage($message, SOCIAL_MSG_SUCCESS);
		$this->info->set($this->getMessage());

		if ($video->isCreatedInCluster()) {
			$options['uid'] = $video->uid;
			$options['type'] = $video->type;
		}

		$url = FRoute::videos($options, false);
		return $this->app->redirect($url);
	}

	/**
	 * Checks if this feature should be enabled or not.
	 *
	 * @since	1.4
	 * @access	private
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	protected function isFeatureEnabled()
	{
		// Do not allow user to access groups if it's not enabled
		if (!$this->config->get('video.enabled')) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PAGE_NOT_FOUND'));
		}
	}
}
