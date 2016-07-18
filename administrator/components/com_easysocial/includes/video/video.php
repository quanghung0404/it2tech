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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class SocialVideo extends EasySocial
{
	public $table = null;

	// Determines the current type request
	public $uid = null;
	public $type = null;

	// Allowed video types
	private $allowed = array('video/mp4', 'video/ogg', 'video/webm', 'video/x-flv', 'video/3gpp', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv', 'video/x-m4v');

	public function __construct($uid = null, $type = null, $key = null)
	{
		parent::__construct();

		if ($uid instanceof SocialTableVideo) {
			$this->uid = $uid->uid;
			$this->type = $uid->type;
			$this->table = $uid;
		} else {

			// If uid and type isn't supplied, we assume that it is for the current user.
			if (is_null($uid)) {
				$uid = $this->my->id;
			}

			if (is_null($type) || !$type) {
				$type = SOCIAL_TYPE_USER;
			}

			$this->uid = $uid;
			$this->type = $type;
			$this->table = ES::table('Video');

			if ($key) {
				$this->load($key);
			}
		}

		$this->adapter = $this->getAdapter();
	}

	/**
	 * Loads the video table
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function load($key)
	{
		if ($key instanceof SocialTableVideo) {
			$this->table = $key;
			return true;
		}

		if (is_object($key) || is_array($key)) {
			$this->table->bind($key);
			return true;
		}

		if (is_int($key) || is_string($key)) {
			$this->table->load($key);
			return true;
		}
	}

	/**
	 * Magic method to access table's property
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function __get($property)
	{
		if (!property_exists($this, $property) && isset($this->table->$property)) {
			return $this->table->$property;
		}
	}

	/**
	 * Magic method to route calls to adapter
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function __call($method, $arguments)
	{
		return call_user_func_array(array($this->adapter, $method), $arguments);
	}

	/**
	 * Allow caller to bind data to the table
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bind($data)
	{
		return $this->table->bind($data);
	}

	/**
	 * Increment the hit
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hit()
	{
		// Get users ip address
		$ip = $this->input->server->get('REMOTE_ADDR');

		// Match only known browsers
		$agent = $this->input->server->get('HTTP_USER_AGENT', '', 'default');
		$pattern = '/(Mozilla.*(Gecko|KHTML|MSIE|Presto|Trident)|Opera).*/i';

		preg_match($pattern, $agent, $trackHits);

		if ($ip && !empty($this->table->id) && !empty($trackHits)) {

			$token = md5($ip . $this->table->id);
			$session = JFactory::getSession();
			$exists = $session->get($token, false);

			// If user was logged before, skip it
			if ($exists) {
				return true;
			}

			$session->set($token, 1);
		}

		// Increment the hit counter
		$this->table->hit();
	}

	/**
	 * Creates a new video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save($data, $file = array(), $options = array())
	{
		// Ensure that the site's language file is loaded
		ES::language()->loadSite();

		// Determines if this is a new video
		$isNew = $this->isNew();

		// Set the current user id only if this is a new video, otherwise whenever the video is edited,
		// the owner get's modified as well.
		if ($isNew) {
			$this->table->user_id = $this->my->id;
		}

		// Map the video data
		$this->table->bind($data);

		// Ensure that the duration is properly normalized
		if ($this->table->duration && is_float($this->table->duration)) {
			$this->table->duration = round($this->table->duration);
		}
		
		// default to user type.
		if (!$this->table->uid && !$this->table->type) {
			$this->table->uid = $this->my->id;
			$this->table->type = SOCIAL_TYPE_USER;
		}

		// If this is a new video, ensure that the requester is allowed to upload videos
		$allowCreation = $this->allowCreation();

		if ($isNew && !$allowCreation) {
			$this->setError('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_CREATE_VIDEO');
			return false;
		}

		// Determines if the requester has exceeded their limit
		$exceededLimit = $this->hasExceededLimit();

		// If the video is a new video, and their limits exceeded, do not allow them to create video
		if ($isNew && $exceededLimit) {
			$this->setError(JText::_("COM_EASYSOCIAL_VIDEOS_EXCEEDED_LIMIT"));
            return false;
		}

		// Set the video to be under pending processing state since this is a new video
		if (!$this->table->id) {
			$this->table->state = SOCIAL_VIDEO_PENDING;
		}

		// Video links
		if ($this->table->isLink()) {
			$this->table->path = $data['link'];

			// Grab the video data
			$crawler = ES::crawler();
			$crawler->crawl($this->table->path);

			$scrape = (object) $crawler->getData();

			// Set the video params with the scraped data
			$this->table->params = json_encode($scrape);

			// Set the video's duration
			$this->table->duration = @$scrape->oembed->duration;
		}

		// Validate the video
		if (!$isNew && $file || $isNew) {
			$valid = $this->validate($file);

			if (!$valid) {
				return false;
			}
		}

		// Save the video
		$state = $this->table->store();

		// Bind the video location
		if (isset($data['location']) && $data['location'] && isset($data['latitude']) && $data['latitude'] && isset($data['longitude']) && $data['longitude']) {

			// Create a location for this video
			$location = ES::table('Location');

			$location->uid = $this->table->id;
			$location->type = SOCIAL_TYPE_VIDEO;
			$location->user_id = $this->my->id;
			$location->address = $data['location'];
			$location->latitude = $data['latitude'];
			$location->longitude = $data['longitude'];

			$location->store();
		}

		// Bind the tags
		if (isset($data['tags'])) {
			$this->insertTags($data['tags']);
		}

		$privacyData = '';
		if (isset($data['privacy'])) {

			$privacyData = new stdClass();
			$privacyData->rule = 'videos.view';
			$privacyData->value = $data['privacy'];
			$privacyData->custom = $data['privacyCustom'];

			$this->insertPrivacy($privacyData);
		}

		// check if we should create stream or not.
		$createStream = (isset($options['createStream']) && $options['createStream'] && $isNew) ? true : false;
		if ($createStream) {
			$this->createStream('create', $privacyData);
		}

		// Process link videos
		if ($this->table->isLink()) {
			$this->processLinkVideo();

			// if this is a external videos, let index it into joomla smart search
			$this->syncIndex();
		}

		// If the video source is upload, we need to perform additional stuffs
		if ($this->isUpload()) {

			$fromStory = isset($options['story']) ? $options['story'] : false;

			// Determines if the saving process should verify the uploaded file or not.
			if (!$fromStory && $file) {

				// Ensure that this is not being edited
				$valid = $this->isVideoValid($file);

				if (!$valid) {
					$this->setError(JText::_("COM_EASYSOCIAL_VIDEOS_INVALID_VIDEO_FILE_PROVIDED"));

					return false;
				}

				// Set the original file title.
				$this->table->file_title = $file['name'];

				// Copy the file to the correct folder
				$path = $this->copyFileFromTmp($file);

				// Store the original video path
				$this->table->original = $path;
			}

			// need to store the privacy value temporary into params
			if ($privacyData) {
				$privacyData->processed = 0;
				$this->table->params = json_encode($privacyData);
			}

			// Re-save the video object to get the correct path
			$state = $this->table->store();

			if ($state && ($this->table->state == SOCIAL_VIDEO_PUBLISHED)) {
				$this->syncIndex();
			}
		}

		return $state;
	}

	/**
	 * Insert tags for this video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function insertTags($tags = array())
	{
		$tag = ES::tag($this->table->id, SOCIAL_TYPE_VIDEO);
		return $tag->insert($tags);
	}

	/**
	 * Insert privacy for this video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function insertPrivacy($privacy)
	{
		$privacyLib = FD::privacy();
		$privacyLib->add($privacy->rule, $this->table->id, SOCIAL_TYPE_VIDEOS, $privacy->value, null, $privacy->custom);
	}

	/**
	 * Determines if the user can remove a tag from the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canRemoveTag()
	{
		// Site admins should always be able to delete tags
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		// Video owners should be able to remove tags
		$author = $this->getAuthor();

		if ($author->id == $this->my->id) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current user can upload videos
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canUpload()
	{
		// @TODO: Check for user's acl here

		return true;
	}

	/**
	 * Checks if the file that is uploaded is valid
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isVideoValid($file)
	{
		$adapter = $this->getAdapter();

		// Check for upload limit
		$maxSize = $adapter->getUploadLimit();
		$maxSize = ES::math()->convertBytes($maxSize);

		if ($file['size'] > $maxSize) {
			$this->setError(JText::sprintf('COM_EASYSOCIAL_VIDEOS_EXCEEDED_ALLOWED_FILESIZE', $this->getUploadLimit()));
			return false;
		}

		// Check for validity of the video
		if (!in_array($file['type'], $this->allowed)) {
			$this->setError('COM_EASYSOCIAL_VIDEOS_INVALID_VIDEO_FILE');
			return false;
		}

		return true;
	}

	/**
	 * Checks for valid video url
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isValidUrl($url)
	{
		$pattern = "/(^|\\s)(https?:\\/\\/)?(([a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.([a-z]{2,6}))|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(:[0-9]{1,5})?(\\/.*)?/uism";

		$match = preg_match($pattern, $url);

		if (!$match) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if this is a new video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isNew()
	{
		return !$this->table->id;
	}

	/**
	 * Copies the file from the temporary folder
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function copyFileFromTmp($file)
	{
		// Get the storage path for this video
		$storagePath = $this->getStoragePath();

		// We need to rename the original file name.
		$storagePath .= '/' . md5($file['name']);

		// Copy the original video file into the storage path
		$state = JFile::copy($file['tmp_name'], $storagePath);

		return $storagePath;
	}

	/**
	 * Retrieves the cluster that is associated with the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCluster()
	{
		$cluster = null;

		if ($this->uid && $this->type && $this->type != SOCIAL_TYPE_USER) {
			$cluster = ES::cluster($this->type, $this->uid);
		}

		return $cluster;
	}

	/**
	 * Retrieves the container path
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getContainer()
	{
		$container = ltrim($this->config->get('video.storage.container'), '/');
		$path = JPATH_ROOT . '/' . $container;

		if (!JFolder::exists($path)) {
			JFolder::create($path);
		}

		if ($this->table->id) {
			$container .= '/' . $this->table->id;
			$path .= '/' . $this->table->id;

			if (!JFolder::exists($path)) {
				JFolder::create($path);
			}
		}

		return $container;
	}

	/**
	 * Ensures that the container folder exists
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getStorageUrl()
	{
		$container = $this->getContainer();
		$url = rtrim(JURI::root(), '/') . '/' . $container;

		return $url;
	}

	/**
	 * Ensures that the container folder exists
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getStoragePath()
	{
		$container = $this->getRelativeStoragePath();
		$storagePath = JPATH_ROOT . '/' . $container;

		if (!JFolder::exists($storagePath)) {
			JFolder::create($storagePath);
		}

		return $storagePath;
	}

	/**
	 * Retrieves the relative path
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRelativeStoragePath()
	{
		$container = ltrim($this->config->get('video.storage.container'), '/');

		if ($this->table->id) {
			$container .= '/' . $this->table->id;
		}

		return $container;
	}

	/**
	 * Gets the log file path
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLogFilePath()
	{
		$storagePath = $this->getStoragePath();
		$logFilePath = $storagePath . '/' . md5($this->table->id) . '.log';

		return $logFilePath;
	}

	/**
	 * Retrieves the video item
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getItem()
	{
		return $this->table;
	}

	/**
	 * Retrieves the path to the video file
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFile()
	{
		$container = '/' . str_ireplace('\\', '/', $this->getContainer());
		$file = basename($this->table->path);

		$relative = $container . '/' . $file;

		// Default url
		$url = rtrim(JURI::root(), '/') . $relative;

		if ($this->storage != SOCIAL_STORAGE_JOOMLA) {
			$storage = ES::storage($this->storage);
			$url = $storage->getPermalink($relative);
		}

		return $url;
	}

	/**
	 * Retrieves the path to the video file
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRelativeFilePath()
	{
		return $this->table->path;
	}

	/**
	 * Determines if this video belongs to cluster
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isCreatedInCluster()
	{
		if ($this->table->uid && $this->table->type && $this->table->type != SOCIAL_TYPE_USER) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the video is in pending processing mode
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isPendingProcess()
	{
		return $this->table->state == SOCIAL_VIDEO_PENDING;
	}

	/**
	 * Determines if the video item is being processed.
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isProcessing()
	{
		return $this->table->state == SOCIAL_VIDEO_PROCESSING;
	}

	/**
	 * Determines if the video is published
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isPublished()
	{
		return $this->table->state == SOCIAL_VIDEO_PUBLISHED;
	}

	/**
	 * Determines if this video is an upload source
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isUpload()
	{
		return $this->table->isUpload();
	}

	/**
	 * Determines if this video is a link source
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isLink()
	{
		return $this->table->isLink();
	}

	/**
	 * Creates a new log file
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createLogFile()
	{
		$name = md5($this->id);
        $name = md5($this->command);
        $path = SOCIAL_TMP . '/' . $name;
        $contents = '';

        JFile::write($path, $contents);

        $this->logFile = $path;

        return $this->logFile;
	}

	/**
	 * Processes a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function process()
	{
		// If the video is already in the midst of processing, we shouldn't allow them to process this again
		if ($this->isProcessing()) {
			$this->setError(JText::_("COM_EASYSOCIAL_VIDEOS_ALREADY_PROCESSING"));
			return false;
		}

		// Only process the video uploads
		if ($this->table->isUpload()) {
			$this->processUploadedVideo();
		}

		// Only process linked videos
		if ($this->table->isLink()) {
			$this->processLinkVideo();
		}
	}

	/**
	 * Process link videos
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function processLinkVideo()
	{
		$this->snapshot();
	}

	/**
	 * Takes a snapshot of the video to be used as the thumbnail
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function snapshot($dimension = '1280x720', $length = '00:00:01')
	{
		if ($this->isUpload()) {
			$ffmpeg = ES::ffmpeg();
			$ffmpeg->input($this->table->original);

			// Get the storage path to the video
			$fileName = md5($this->table->title) . '.jpg';

			// Construct the storage path
			$storage = $this->getStoragePath() . '/' . $fileName;

			// Set the thumbnail at the first second
			$ffmpeg->thumb($this->table->original, $storage, $length);

			// Returns the relative path
			$relativePath = $this->getRelativeStoragePath() . '/' . $fileName;

			$this->table->thumbnail = $relativePath;
			$this->table->store();

			return $relativePath;
		}

		if ($this->isLink()) {
			// Get the thumbnail of the video
			$params = json_decode($this->table->params);

			// Get the storage path to the video
			$storage = $this->getStoragePath();

			// Get the thumbnail url
			$thumbnail = $params->oembed->thumbnail;

			if (!$thumbnail) {
				return false;
			}
			
			// Crawl the image now.
			$connector = FD::get('Connector');
			$connector->addUrl($thumbnail);
			$connector->connect();

			// Get the result and parse them.
			$contents = $connector->getResult($thumbnail);

			// We need to sanitize the image file name
			$imageFileName = md5(basename($thumbnail));

			// Store the image file now
			$tmpStorage = $storage . '/' . $imageFileName;

			// Save the file
			JFile::write($tmpStorage, $contents);

			// Load the image now
			$image = FD::image();
			$image->load($tmpStorage);

			// Get the extension for the file
			$extension = $image->getExtension();
			$newStorage = $storage . '/' . $imageFileName . $extension;

			// Rename the file
			JFile::move($tmpStorage, $newStorage);

			// Ensure that image is valid
			if (!$image->isValid()) {
				JFile::delete($storage);
				return false;
			}

			$relativePath = $this->getRelativeStoragePath() . '/' . $imageFileName . $extension;

			// Save the thumbnail
			$this->table->thumbnail = $relativePath;
			$this->table->state = SOCIAL_VIDEO_PUBLISHED;
			$this->table->store();
		}
	}

	/**
	 * Process uploaded videos
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function processUploadedVideo()
	{
		// Mark the video as processing
		$this->table->processing();

		// Set the audio bitrate
		$bitrate = $this->config->get('video.audiobitrate');

		// We want to output the completed stuff to our own log files
		$logFile = $this->getLogFilePath();

		// Set the duration of the video
		$duration = $this->extractDuration();
		$this->table->duration = $duration->raw();

		// Generate a unique name for this file
		$fileName = $this->generateFileName();

		// Get the path to the output.
		$storagePath = $this->getStoragePath() . '/' . $fileName;

		// Set the output file
		$this->table->path = $this->getRelativeStoragePath() . '/' . $fileName;

		// Get the video size to resize to
		$size = $this->config->get('video.size');

		// Load up the ffmpeg library
		$ffmpeg = ES::ffmpeg();
		$ffmpeg->resize($this->table->original, $storagePath, $bitrate, $size, $logFile);

		// Set the thumbnail
		if (!$this->table->thumbnail) {
			$this->snapshot();
		}

		// Update the video object now.
		$this->table->store();
	}

	/**
	 * Generate a random file name
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function generateFileName()
	{
		$name = substr(md5(microtime()),rand(0,26), 8);

		return $name . '.mp4';
	}

	/**
	 * Retrieves the duration of the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDuration()
	{
		if (!$this->table->duration) {
			$duration = JText::_('N/A');

			return $duration;
		}

		$duration = new SocialVideoDuration($this->table->duration);

		// Since duration is always stored in seconds, we need to format this
		return $duration->format();
	}

	/**
	 * Retrieves the location for the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLocation()
	{
		static $location = null;

		if (is_null($location)) {
			$location = ES::location($this->table->id, SOCIAL_TYPE_VIDEO);
		}

		return $location;
	}

	/**
	 * Retrieves the likes library for this video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLikes($verb = '', $streamId = '')
	{
		if (!$verb) {
			$verb = 'create';
		}

		$likes = ES::likes();
		$likes->get($this->table->id, SOCIAL_TYPE_VIDEOS, $verb, $this->type, $streamId);

		return $likes;
	}

	/**
	 * Retrieve the likes count
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLikesCount($verb = '', $streamId = '')
	{
		$likes = $this->getLikes($verb, $streamId);

		return $likes->getCount();
	}

	/**
	 * Retrieves the comment library for this video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getComments($verb = '', $streamId = '')
	{
		if (!$verb) {
			$verb = 'create';
		}

		// Generate comments for the video
		$comments = ES::comments($this->table->id, SOCIAL_TYPE_VIDEOS, $verb, $this->type, array(), $streamId);

		return $comments;
	}

	/**
	 * Retrieves the comments count
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCommentsCount($verb = '', $streamId = '')
	{
		if (!$verb) {
			$verb = 'create';
		}

		$comments = $this->getComments($verb, $streamId);

		return $comments->getCount();
	}

	/**
	 * Retrieves the related stream id for a particular verb
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getStreamId($verb)
	{

		$model = ES::model('Videos');
		$streamId = $model->getStreamId($this->table->id, $verb);

		return $streamId;
	}

	/**
	 * Retrieves the creation date of a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCreatedDate()
	{
		$date = ES::date($this->table->created);

		return $date;
	}

	/**
	 * Retrieves the bookmarks library associated to this video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSharing()
	{
		$options = array('text' => JText::_('Share'));
		$options['url'] = $this->getExternalPermalink();
		$sharing = ES::sharing($options);

		return $sharing;
	}

	/**
	 * Retrieves the privacy library associated to this video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPrivacy()
	{
		static $privacy = null;

		if (is_null($privacy)) {
			$privacy = ES::privacy($this->id, SOCIAL_TYPE_VIDEOS);
		}

		return $privacy;
	}

	/**
	 * Retrieves the privacy library for this video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPrivacyButton()
	{
		$privacy = $this->getPrivacy();

		/* TODO: need to come back here once the stream for video created. */
		$streamId = $this->getVideoStreamId($this->table->id, 'create');

		$button = $privacy->form($this->table->id, SOCIAL_TYPE_VIDEOS, $this->table->uid, 'videos.view', false, $streamId);

		return $button;
	}

	/**
	 * Retrieves the view all videos link
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAllVideosLink($filter = '', $xhtml = false)
	{
		$options = array();

		if ($filter) {
			$options['filter'] = $filter;
		}

		if ($this->uid && $this->type) {
			$options['uid'] = $this->adapter->getAlias();
			$options['type'] = $this->type;
		}

		$url = FRoute::videos($options, $xhtml);

		return $url;
	}

	/**
	 * Determines if the photo should be associated with the stream item
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getVideoStreamId($videoId, $verb)
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select('#__social_stream_item', 'a');
		$sql->column('a.uid');
		$sql->where('a.context_type', SOCIAL_TYPE_VIDEOS);
		$sql->where('a.context_id', $videoId);

		if ($verb == 'upload') {
			$sql->where('a.verb', 'share');
			$sql->where('a.verb', 'upload', '=', 'OR');
		} else if($verb == 'add') {
			$sql->where('a.verb', 'create');
		} else {
			$sql->where('a.verb', $verb);
		}

		$db->setQuery($sql);

		$uid 	= (int) $db->loadResult();

		if (!$uid) {
			return;
		}

		return $uid;
	}

	/**
	 * Retrieves the reports library for this video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getReports()
	{
		// Generate the reports
		$options = array('title' => 'COM_EASYSOCIAL_VIDEOS_REPORTS_DIALOG_TITLE',
						'description' => 'COM_EASYSOCIAL_VIDEOS_REPORTS_DIALOG_DESC',
						'extension' => 'com_easysocial',
						'type' => SOCIAL_TYPE_VIDEO,
						'uid' => $this->table->id,
						'itemTitle' => $this->getTitle()
					);

		$reports = ES::reports($options);

		return $reports;
	}

	/**
	 * Retrieves a list of tags associated with the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTags()
	{
		if (!$this->table->id) {
			return array();
		}

		$model = ES::model('Tags');
		$tags = $model->getTags($this->table->id, SOCIAL_TYPE_VIDEO);

		return $tags;
	}

	/**
	 * Retrieves the title of the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTitle()
	{
		return JText::_($this->table->title);
	}

	/**
	 * Retrieves the description of the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDescription()
	{
		// Load site's language file.
		ES::language()->loadSite();

		if (!$this->table->description) {
			return JText::_('COM_EASYSOCIAL_VIDEOS_NO_DESCRIPTION_AVAILABLE');
		}

		return $this->table->description;
	}

	/**
	 * Retrieves the embed codes for the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getEmbedCodes()
	{
		if ($this->isLink()) {
			return $this->getLinkEmbedCodes();
		}

		return $this->getUploadEmbedCodes();
	}

	/**
	 * Generates the embed codes for linked videos
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLinkEmbedCodes()
	{
		// Get the video width and height
		$height = $this->config->get('video.size');
		$width = '1920';

		if ($height == '720') {
			$width = '1280';
		}

		if ($height == '480') {
			$width = '854';
		}

		$params = json_decode($this->table->params);

		$codes = str_ireplace('width="' . $params->oembed->width . '"', 'width="' . $width . '"', $params->oembed->html);
		$codes = str_ireplace('height="' . $params->oembed->height . '"', 'height="' . $height . '"', $codes);

		return $codes;
	}

	/**
	 * Generates the embed codes for uploaded videos
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUploadEmbedCodes()
	{
		$theme = ES::themes();

		// We need to generate a unique id for each videos that are embedded on the page
		$uid = uniqid();

		$theme->set('uid', $uid);
		$theme->set('video', $this);

		$output = $theme->output('site/videos/player');

		return $output;
	}

	/**
	 * Retrieves the entity adapter
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAdapter()
	{
		$file = __DIR__ . '/adapters/' . $this->type . '.php';

		require_once($file);

		$className = 'SocialVideoAdapter' . ucfirst($this->type);
		$obj = new $className($this->uid, $this->type, $this->table);

		return $obj;
	}

	/**
	 * Retrieves the thumbnail of a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getThumbnail()
	{
		if (!$this->table->thumbnail) {
			return $this->getDefaultThumbnail();
		}

		$container = str_ireplace('\\', '/', $this->getContainer());
		$file = basename($this->table->thumbnail);

		$relative = '/' . $container . '/' . $file;

		// Default url
		$url = rtrim(JURI::root(), '/') . $relative;

		// Storage service
		if ($this->table->storage != SOCIAL_STORAGE_JOOMLA) {
			$storage = ES::storage($this->table->storage);
			$url = $storage->getPermalink($relative);
		}

		return $url;
	}

	/**
	 * Retrieves the default thumbnail for video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultThumbnail()
	{
		$override = JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_easysocial/defaults/videos/cover.png';
		$overrideUri = rtrim(JURI::root(), '/') . '/templates/' . $this->app->getTemplate() . '/html/com_easysocial/defaults/videos/cover.png';

		if (JFile::exists($override)) {
			return $overrideUri;
		}

		$default = rtrim(JURI::root(), '/') . '/media/com_easysocial/images/defaults/videos/cover.jpg';

		return $default;
	}

	/**
	 * Retrieves the thumbnail of a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getThumbnailFileName()
	{
		return basename($this->table->thumbnail);
	}

	/**
	 * Retrieves the thumbnail of a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRelativeThumbnailPath()
	{
		$container = $this->getContainer();
		$path = $container . '/' . basename($this->table->thumbnail);

		return $path;
	}

	/**
	 * Retrieves the permalink to edit a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCreateLink($xhtml = true)
	{
		$options = array('layout' => 'form');

		if ($this->table->id) {
			$options['id'] = $this->table->id;
		}

		if ($this->uid && $this->type) {
			$cluster = ES::cluster($this->type, $this->uid);
			$options['uid'] = $cluster->getAlias();
			$options['type'] = $this->type;
		}

		$url = FRoute::videos($options, $xhtml);

		return $url;
	}

	/**
	 * Retrieves the permalink to edit a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getEditLink($xhtml = true)
	{
		$options = array('layout' => 'form');

		if ($this->table->id) {
			$options['id'] = $this->table->id;
		}

		if ($this->uid && $this->type) {
			$cluster = ES::cluster($this->type, $this->uid);
			$options['uid'] = $cluster->getAlias();
			$options['type'] = $this->type;
		}

		$url = FRoute::videos($options, $xhtml);

		return $url;
	}

	/**
	 * Retrieves the permalink of the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlias()
	{
		return $this->table->getAlias();
	}

	/**
	 * Retrieves the permalink of the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPermalink($xhtml = true)
	{
		return $this->table->getPermalink($xhtml);
	}

	/**
	 * Retrieves the external permalink of the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getExternalPermalink()
	{
		return $this->table->getExternalPermalink();
	}

	/**
	 * Retrieves the hits for the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getHits()
	{
		return $this->table->hits;
	}

	/**
	 * Retrieves the author of the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAuthor()
	{
		$author = ES::user($this->table->user_id);

		return $author;
	}

	/**
	 * Retrieves the category of the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCategory()
	{
		$category = ES::table('VideoCategory');
		$category->load($this->table->category_id);

		return $category;
	}

	/**
	 * Exports the video data in a std class object
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function export()
	{
		$data = (object) $this->table;

		return $data;
	}

	/**
	 * Extracts thumbnail from a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function extractThumbnail()
	{
		$ffmpeg = ES::ffmpeg();
		$ffmpeg->input($this->table->original);

		// Set the thumbnail at the first second
		$ffmpeg->thumb('1280x720', '00:00:01');

		// Get the storage path to the video
		$fileName = md5($this->table->title) . '.jpg';

		// Construct the storage path
		$storage = $this->getStoragePath() . '/' . $fileName;

		// Capture the first second of the video
		$ffmpeg->output($storage);
		$ffmpeg->execute();

		// Returns the relative path
		$relativePath = $this->getRelativeStoragePath() . '/' . $fileName;

		return $relativePath;
	}

	/**
	 * Extracts the duration using ffmpeg
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function extractDuration()
	{
		$ffmpeg = ES::ffmpeg();
		$ffmpeg->input($this->table->original);

		$output = $ffmpeg->execute();

		$duration = $this->matchDuration($output);

		return $duration;
	}

	public static function trimResult($var)
	{
		if (empty($var)) {
			return false;
		}

		return true;
	}

	/**
	 * Converts the given duration into seconds
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function convertToSeconds($hour, $minute, $second)
	{
		$seconds = 0;

		if ($hour != '00') {
			$seconds += intval($hour) * 60 * 60;
		}

		if ($minute != '00') {
			$seconds += intval($minute) * 60;
		}

		$seconds += intval($second);

		return $seconds;
	}

	/**
	 * Matches the output from ffmpeg and retrieves the duration
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function matchDuration($contents)
	{
		$duration = new SocialVideoDuration();

		// Regex to match the total duration of the original video
		$pattern = '/Duration: ([0-9]{2}):([0-9]{2}):([0-9]{2})\.[0-9]{2}/is';
		preg_match($pattern, $contents, $matches);

		if ($matches) {
			list($str, $hour, $minute, $second) = $matches;

			$seconds = $this->convertToSeconds($hour, $minute, $second);
			$duration->set($seconds);
		}

		return $duration;
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
		// Get the log file's path
		$logFilePath = $this->getLogFilePath();
		$contents = JFile::read($logFilePath);

		// Regex to match the total duration of the original video
		$duration = $this->matchDuration($contents);

		// Get total seconds for the duration
		$totalDurationSeconds = $duration->raw();

		// Get all the frames that is being converted currently
		$pattern = '/frame= (.*)/uim';
		preg_match($pattern, $contents, $frames);

		// Default processed duration
		$currentDurationSeconds = 0;

		if ($frames) {
			$frames = explode("\r", $frames[0]);

			// The last frame always needs to -2 because the last line is always a return carriage
			$frames = array_filter($frames, array($this, 'trimResult'));
			$totalFrames = count($frames);
			$index = count($frames) - 1;
			$lastFrame = $frames[$index];

			// Get the current time of the last frame
			if ($lastFrame) {
				$pattern = '/time=([0-9]{2}):([0-9]{2}):([0-9]{2})\.([0-9]{2})/is';
				preg_match($pattern, $lastFrame, $time);

				if (count($time) >= 5) {
					list($time, $hour, $minute, $second, $milisecond) = $time;
				} else {
					list($time, $hour, $minute, $second) = $time;
				}

				$currentDurationSeconds = $this->convertToSeconds($hour, $minute, $second);
			}
		}

		// Determines if this is already complete
		$pattern = '/muxing overhead\:/is';
		preg_match($pattern, $contents, $complete);

		if ($complete) {
			return true;
		}

		$progress = round(($currentDurationSeconds / $totalDurationSeconds) * 95);

		return $progress;
	}

	/**
	 * Publishes the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function publish($options = array())
	{

		$privacyData = '';

		if ($this->table->params) {
			$privacyData = json_decode($this->table->params);

			//lets perform some testing here before we proceed.
			if (isset($privacyData->rule)) {
				if ($privacyData->processed) {
					$privacyData = '';
				} else {
					//this mean its a new video. so we need to remove the privacy data.
					$this->table->params = '';
				}
			} else {
				$privacyData = '';
			}
		}

		$this->table->state = SOCIAL_VIDEO_PUBLISHED;

		$state = $this->table->store();

		// @TODO: Give points to the author for creating a new video

		// Generate a new stream item when the video is published.
		$createStream = isset($options['createStream']) ? $options['createStream'] : true;

		if ($createStream) {
			$this->createStream('create', $privacyData);
		}

		// trigger video smart search plugin for indexing.
		$this->syncIndex();

		return $state;
	}

	/**
	 * Sync's the user record with Joomla smart search
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function syncIndex()
	{
		// Determines if this is a new account
		$isNew = $this->isNew();

		// Trigger our own finder plugin
        JPluginHelper::importPlugin('finder');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onFinderAfterSave', array('easysocial.videos', &$this->table, $isNew));
	}

	/**
	 * Unpublishes the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unpublish()
	{
		$this->table->state = SOCIAL_VIDEO_UNPUBLISHED;

		$state = $this->table->store();

		// @TODO: Give points to the author for creating a new video

		// @TODO: Should we delete the stream as well?

		return $state;
	}

	/**
	 * Sets a video as featured
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setFeatured()
	{
		$this->table->featured = SOCIAL_VIDEO_PUBLISHED;
		$state = $this->table->store();

		// Generate a stream item for this featured
		$this->createStream('featured');

		// @points: video.featured
		ES::points()->assign('video.featured', 'com_easysocial', $this->getAuthor()->id);

		return $state;
	}

	/**
	 * Removes a featured video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeFeatured()
	{
		$this->table->featured = false;
		$state = $this->table->store();

		// Generate a stream item for this featured
		$this->removeStream('featured');

		// @points: video.unfeatured
		ES::points()->assign('video.unfeatured', 'com_easysocial', $this->getAuthor()->id);

		return $state;
	}

	/**
	 * Generates a new stream item for the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createStream($verb, $privacy = '')
	{
		// When a video is published, we should generate a stream for it.
		$stream = ES::stream();

		$template = $stream->getTemplate();
		$actor = $this->getAuthor();

		// Set the actor of the stream item
		$template->setActor($actor->id, SOCIAL_TYPE_USER);

		// Set the context
		$template->setContext($this->table->id, SOCIAL_TYPE_VIDEOS);

		// Set the verb
		$template->setVerb($verb);

		// If this is created within a cluster, it should be mapped to the respective cluster
		if ($this->table->uid && $this->table->type && $this->table->type != SOCIAL_TYPE_USER) {
			$template->setCluster($this->table->uid, $this->table->type);
		}

		// Set stream privacy
		if ($privacy) {

			$value = $privacy->value;
			if (is_string($value)) {
				$privacyLib = ES::privacy();
				$value = $privacyLib->toValue($value);
			}

			$template->setAccess('videos.view', $value, $privacy->custom);
		} else {
			$template->setAccess('videos.view');
		}

		// Generate the stream item now.
		$result = $stream->add($template);

		return $result;
	}

	/**
	 * Removes a stream item given the verb
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeStream($verb, $actorId = '')
	{
		// When a video is published, we should generate a stream for it.
		$stream = ES::stream();

		$result = $stream->delete($this->table->id, SOCIAL_TYPE_VIDEOS, $actorId, $verb);

		return $result;
	}

	/**
	 * Deletes the video from the site
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Remove the comments related to this video
		$comments = $this->getComments();
		$comments->delete();

		// Remove the likes related to this video
		$likes = ES::likes();
		$likes->delete($this->id, SOCIAL_TYPE_VIDEOS, 'create');

		// Assign points when a video is deleted
		ES::points()->assign('video.remove', 'com_easysocial', $this->table->id);

		// Remove the stream items related to this video
		$this->removeStream('create');

		// Remove the search results
        JPluginHelper::importPlugin('finder');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onFinderAfterDelete', array('easysocial.videos', $this->table));

		// Remove files related to this video
		$this->deleteStorage();

		// Remove from the database
		$state = $this->table->delete();

		return $state;
	}

	/**
	 * Delete video files from the site
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteStorage()
	{
		$path = $this->getStoragePath();

		// If the video is stored externally, it should be deleted differently.
		
		// Delete the entire folder
		$state = JFolder::delete($path);

		return $state;
	}

	/**
	 * Determines if the video contains a location
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasLocation()
	{
		$location = $this->getLocation();

		if ($location->hasAddress()) {
			return true;
		}

		return false;
	}

	/**
	 * Validates the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validate($file = array())
	{
		// Ensure that it has a category
		if (!$this->table->category_id) {
			$this->setError('COM_EASYSOCIAL_INVALID_CATEGORY_PROVIDED');
			return false;
		}

		// Ensure that the video has a title
		if (!$this->table->title) {
			$this->setError('COM_EASYSOCIAL_VIDEOS_INVALID_TITLE');
			return false;
		}

		// If this is a video link, ensure a link is provided
		if ($this->isLink() && !$this->table->path) {
			$this->setError('COM_EASYSOCIAL_VIDEOS_ENTER_VIDEO_URL');
			return false;
		}

		// Ensure that the video link is valid
		if ($this->isLink() && $this->table->path && !$this->isValidUrl($this->table->path)) {
			$this->setError('COM_EASYSOCIAL_VIDEOS_ENTER_VALID_URL');
			return false;
		}

		// If this is a new video we want to validate the file
		if ($this->isUpload() && $this->isNew() && empty($file['tmp_name'])) {
			$this->setError('COM_EASYSOCIAL_VIDEOS_UPLOAD_FILE');
			return false;
		}

		// If file is provided, we need to test if it is valid
		if ($this->isUpload() && $file) {

			$valid = $this->isVideoValid($file);

			if (!$valid) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Format the url correctly.
	 *
	 * @since   1.4
	 * @access  public
	 * @param   string
	 * @return  
	 */
    public function format($link=false)
    {
        if (preg_match("#https?://#", $link) === 0){
            $link = 'https://' . $link;
        }

        if (strpos($link, 'youtu.be') > 0) {
            $link = preg_replace('~^https?://youtu\.be/([a-z\d]+)$~i', 'https://www.youtube.com/watch?v=$1', $link);
        }

        return $link;
    }
}

class SocialVideoDuration
{
	public $seconds = null;

	public function __construct($seconds = '')
	{
		$this->seconds = $seconds;
	}

	public function set($seconds)
	{
		$this->seconds = $seconds;
	}

	public function raw()
	{
		return $this->seconds;
	}

	public function format()
	{
		$empty = new DateTime('@0');
		$current = new DateTime('@' . $this->seconds);

		$diff = $empty->diff($current)->format('%H:%I:%S');;

		return $diff;
	}
}
