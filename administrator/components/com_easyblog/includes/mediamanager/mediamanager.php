<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EBMM extends EasyBlog
{
	/**
	 * The available extension to type mapping
	 * @var Array
	 */
	public static $types = array(
		'jpg'	=> 'image',
		'png'	=> 'image',
		'gif'	=> 'image',
		'bmp'	=> 'image',
		'jpeg'	=> 'image',
		'mp4'	=> 'video',
		'swf'	=> 'video',
		'flv'	=> 'video',
		'mov'	=> 'video',
		'f4v'	=> 'video',
		'3gp'	=> 'video',
		'm4v'	=> 'video',
		'webm'	=> 'video',
		'ogv'	=> 'video',
		'mp3'	=> 'audio',
		'm4a'	=> 'audio',
		'aac'	=> 'audio',
		'ogg'	=> 'audio'
	);

	/**
	 * Maps the given place with the specific icons
	 * @var Array
	 */
	public static $icons = array(

		// Places
		'place/post' => 'fa fa-file',
		'place/user' => 'fa fa-image',
		'place/shared' => 'fa fa-globe',
		'place/flickr' => 'fa fa-flickr',
		'place/dropbox' => 'fa fa-dropbox',
		'place/album' => 'fa fa-camera',
		'place/jomsocial' => 'fa fa-camera',
		'place/easysocial' => 'fa fa-camera',
		'place/users' => 'fa fa-users',
		'place/posts' => 'fa fa-files-o',

		// Types
		'folder' => 'fa fa-folder-o',
		'file'   => 'fa fa-file-o',
		'image'  => 'fa fa-file-image-o',
		'audio'  => 'fa fa-file-audio-o',
		'video'  => 'fa fa-file-video-o',

		// Extensions
		'txt'  => 'fa fa-file-text-o',
		'rtf'  => 'fa fa-file-text-o',

		'htm'  => 'fa fa-file-code-o',
		'html' => 'fa fa-file-code-o',
		'php'  => 'fa fa-file-code-o',
		'css'  => 'fa fa-file-code-o',
		'js'   => 'fa fa-file-code-o',
		'json' => 'fa fa-file-code-o',
		'xml'  => 'fa fa-file-code-o',

		'zip'  => 'fa fa-file-archive-o',
		'rar'  => 'fa fa-file-archive-o',
		'7z'   => 'fa fa-file-archive-o',
		'gz'   => 'fa fa-file-archive-o',
		'tar'  => 'fa fa-file-archive-o',

		'doc'  => 'fa fa-file-word-o',
		'docx' => 'fa fa-file-word-o',
		'odt'  => 'fa fa-file-word-o',

		'xls'  => 'fa fa-file-excel-o',
		'xlsx' => 'fa fa-file-excel-o',
		'ods'  => 'fa fa-file-excel-o',

		'ppt'  => 'fa fa-file-powerpoint-o',
		'pptx' => 'fa fa-file-powerpoint-o',
		'odp'  => 'fa fa-file-powerpoint-o',

		'pdf'  => 'fa fa-file-pdf-o',
		'psd'  => 'fa fa-file-image-o',
		'tif'  => 'fa fa-file-image-o',
		'tiff' => 'fa fa-file-image-o'
	);

	/**
	 * Default ACL states for media manager
	 * @var Array
	 */
	public static $acl = array(
		'canCreateFolder'    => false,
		'canUploadItem'      => false,
		'canRenameItem'      => false,
		'canRemoveItem'      => false,
		'canMoveItem'		 => false,
		'canCreateVariation' => false,
		'canDeleteVariation' => false
	);

	public static $byte = 1048576;

	/**
	 * Stores the adapter object
	 * @var mixed
	 */
	private $source = null;

	public function __construct($source = EBLOG_MEDIA_SOURCE_LOCAL)
	{
		$this->source = self::getSource($source);
	}

	/**
	 * Maps unknown function calls back to the adapter
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	public function __call($method, $arguments)
	{
		return call_user_func_array(array($this->source, $method), $arguments);
	}

	/**
	 * Generates a skeleton filegroup array
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	array
	 */
	public static function filegroup()
	{
		return array(
			'folder' => array(),
			'image'  => array(),
			'audio'  => array(),
			'video'  => array(),
			'file'   => array()
		);
	}

	/**
	 * Generates a skeleton folder object
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function folder($uri, $contents = array())
	{
		$folder = new stdClass();
		$folder->place = $uri;
		$folder->title = EasyBlogMediaManager::getPlaceName($uri);
		$folder->url = $uri;
		$folder->uri = $uri;
		$folder->key = self::getKey($uri);
		$folder->type = 'folder';
		$folder->icon = '';
		$folder->root = true;
		$folder->scantime = 0;
		$folder->contents = $contents;

		return $folder;
	}

	/**
	 * Generates a skeleton file object
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function file($uri, $type = '')
	{
		$item = new stdClass();

		$item->place = '';
		$item->title = '';
		$item->url = '';
		$item->uri = $uri;
		$item->path = '';
		$item->type = $type;
		$item->size = 0;
		$item->modified = '';
		$item->key = self::getKey($uri);
		$item->thumbnail = '';
		$item->preview = '';
		$item->variations = array();

		return $item;
	}

	/**
	 * Retrieves the adapter object given the source type
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	public static function getSource($source)
	{
		static $sources = array();

		if (isset($sources[$source])) {
			return $sources[$source];
		}

		// Load adapter
		$path = __DIR__ . '/adapters/' . strtolower($source) . '.php';

		require_once($path);

		$class = 'EasyBlogMediaManager' . ucfirst($source) . 'Source';

		$instance = new $class();

		$sources[$source] = $instance;

		return $instance;
	}

	/**
	 * Retrieves the adapter source type given the place id
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getSourceType($placeId)
	{
		if (self::isPostPlace($placeId) || self::isUserPlace($placeId) || $placeId=='shared') {
			return EBLOG_MEDIA_SOURCE_LOCAL;
		}

		// Determines if this is an album place
		if (self::isAlbumPlace($placeId)) {
			$parts = explode(':', $placeId);

			if (count($parts) > 1) {
				$placeId = $parts[0];
			}
		}

		return $placeId;
	}

	/**
	 * Retrieves information about a single place
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getPlace($uri)
	{
		$placeId = self::getPlaceId($uri);

		return (object) array(
			'id' => $placeId,
			'title' => self::getPlaceName($placeId),
			'icon' => self::getPlaceIcon($placeId),
			'acl' => self::getPlaceAcl($placeId),
			'uri' => $placeId,
			'key' => self::getKey($placeId)
		);
	}

	/**
	 * Retrieve a list of places on the site.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getPlaces($user = null, EasyBlogPost $post = null)
	{
		$config = EB::config();
		$acl = EB::acl();

		// Get the current logged in user
		$my = JFactory::getUser($user);

		$places = array();

		// Get the current post's folder
		$places[] = self::getPlace('post');

		// All articles created by the author or admin
		$places[] = self::getPlace('posts');

		// My Media
		$places[] = self::getPlace('user:' . $my->id);

		// Shared folders
		if ($config->get('main_media_manager_place_shared_media') && $acl->get('media_places_shared')) {
			$places[] = self::getPlace('shared');
		}

		// Flickr Integrations
		if ($config->get('layout_media_flickr') && $config->get('integrations_flickr_api_key') != '' && $config->get('integrations_flickr_secret_key') && $acl->get('media_places_flickr')) {
			$places[] = self::getPlace('flickr');
		}

		// EasySocial
		if ($config->get('integrations_easysocial_album') && $acl->get('media_places_album') && EB::easysocial()->exists()) {
			$places[] = self::getPlace('easysocial');
		}

		// JomSocial
		if ($config->get('integrations_jomsocial_album') && $acl->get('media_places_album') && EB::jomsocial()->exists()) {
			$places[] = self::getPlace('jomsocial');
		}

		// If the user is allowed
		if (EB::isSiteAdmin()) {

			// All Users
			$places[] = self::getPlace('users');
		}

		return $places;
	}

	/**
	 * Retrieves the place title
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getPlaceName($placeId)
	{
		$placeName = $placeId;

		if (self::isUserPlace($placeId)) {

			$my = JFactory::getUser();

			// Title should be dependent if the user is viewing their own media
			$id = explode(':', $placeId);
			$user = JFactory::getUser($id[1]);

			if ($my->id != $user->id) {
				return $user->name;
			}

			$placeName = 'user';
		}

		// If this is an article place
		if (self::isPostPlace($placeId)) {

			// Get the post id
			$id = explode(':', $placeId);
			$post = EB::post($id[1]);

			if (!$post->title) {
				return JText::sprintf('COM_EASYBLOG_MM_PLACE_POST_UNTITLED', $id[1]);
			}

			return $post->title;
		}

		// If this is an album place
		if (self::isAlbumPlace($placeId)) {

			$parts = explode('/', $placeId);
			$placeName = $placeId;

			if ($parts > 1) {
				$placeName = $parts[0];
			}
		}

		return JText::_('COM_EASYBLOG_MM_PLACE_' . strtoupper($placeName));
	}

	/**
	 * Gets the icon to be used for a place
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getPlaceIcon($placeId)
	{
		$placeName = strtolower($placeId);

		if (self::isUserPlace($placeId)) {
			$placeName = 'user';
		}

		if (self::isPostPlace($placeId)) {
			$placeName = 'post';
		}

		if (self::isAlbumPlace($placeId)) {
			$placeName = 'album';
		}

		return self::$icons["place/$placeName"];
	}

	/**
	 * Retrieves the list of allowed extensions
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getAllowedExtensions()
	{
		$config = EB::config();

		$allowed = explode(',', $config->get('main_media_extensions'));

		return $allowed;
	}

	/**
	 * Determines if the user has access to a specific place
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function hasAccess($placeId)
	{
		$acl = (object) self::getPlaceAcl($placeId);

		if (!$acl->canUploadItem) {
			return EB::exception('COM_EASYBLOG_MM_NOT_ALLOWED_TO_UPLOAD_FILE', EASYBLOG_MSG_ERROR);
		}

		return true;
	}

	/**
	 * Gets the maximum allowed upload size
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getAllowedFilesize()
	{
		$config = EB::config();
		$maximum = (float) $config->get('main_upload_image_size', 0);

		// If it's 0, no restrictions done
		if ($maximum == 0) {
			return false;
		}

		// Compute the allowed size
		$maximum = $maximum * self::$byte;

		return $maximum;
	}

	/**
	 * Gets the ACL for the specific place
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getPlaceAcl($placeId)
	{
		$my = JFactory::getUser();
		$aclLib = EB::acl();

		$allowedUpload = EB::isSiteAdmin() || $aclLib->get('upload_image');

		// TODO: I'm not sure if specific user, e.g. user 128 viewing user 64,
		// needs to be processed here. But I really like to get rid of user
		// folders altogether.
		if (self::isUserPlace($placeId)) {

			$acl = array_merge(self::$acl, array(
				'canCreateFolder'    => $allowedUpload,
				'canUploadItem'      => $allowedUpload,
				'canRenameItem'      => true,
				'canMoveItem'		 => true,
				'canRemoveItem'      => true,
				'canCreateVariation' => true,
				'canDeleteVariation' => true
			));
		}

		// Article place
		if (self::isPostPlace($placeId)) {

			$id = explode(':', $placeId);
			$post = EB::table('Blog');
			$post->load($id[1]);

			$allowed = $my->id == $post->created_by || EB::isSiteAdmin() || $aclLib->get('moderate_entry');

			// Get the article
			$acl = array_merge(self::$acl, array(
				'canCreateFolder' => $allowedUpload,
				'canUploadItem' => $allowedUpload,
				'canRenameItem' => $allowedUpload,
				'canMoveItem' => $allowedUpload,
				'canRemoveItem' => $allowedUpload,
				'canCreateVariation' => $allowed,
				'canDeleteVariation' => $allowed
			));
		}

		// Shared
		if (self::isSharedPlace($placeId)) {

			$allowed = EB::isSiteAdmin() || $aclLib->get('media_places_shared');

			$acl = array_merge(self::$acl, array(
				'canCreateFolder'    => $allowedUpload,
				'canUploadItem'      => $allowedUpload,
				'canRenameItem'      => $allowedUpload,
				'canMoveItem'		 => $allowedUpload,
				'canRemoveItem'      => $allowedUpload,
				'canCreateVariation' => $allowed,
				'canDeleteVariation' => $allowed
			));
		}

		// If there's no acl defined, we should use the default acl
		if (!isset($acl)) {
			$acl = self::$acl;
		}

		return (object) $acl;
	}

	/**
	 * Retrieves the type of file given the extension type.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getType($extension)
	{
		$type = isset(self::$types[$extension]) ? self::$types[$extension] : 'file';

		return $type;
	}

	/**
	 * Retrieves the icon to be used given the extension type.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getIcon($extension)
	{
		$key = isset(self::$icons[$extension]) ? $extension : self::getType($extension);

		return self::$icons[$key];
	}

	/**
	 * Retrieves the place from uri
	 *
	 * Example:
	 * user:605/foo/bar
	 *
	 * Returns:
	 * user:605
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public static function getPlaceId($uri)
	{
		$first = strpos($uri, '/');

		if ($first == false) {
			return $uri;
		}

		return substr($uri, 0, $first);
	}

	/**
	 * An alias to getFileName
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public static function getTitle($uri)
	{
		$placeId = self::getPlaceId($uri);

		// If they are identical, return place name
		if ($placeId == $uri) {
			return self::getPlaceName($placeId);
		}

		// Return filename
		return self::getFilename($uri);
	}

	/**
	 * Returns the file name based on the given uri
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getFilename($uri)
	{
		$last = strrpos($uri, '/');
		return substr($uri, $last + 1);
	}

	/**
	 * Retrieves the extension of a file given the file name.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public static function getExtension($filename)
	{
		$extension = JFile::getExt($filename);

		return strtolower($extension);
	}

	/**
	 * Returns path from uri
	 * user:605/foo/bar.jpg => /var/www/site.com/images/easyblog_images/605/foo/bar.jpg
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getPath($uri, $root = EASYBLOG_JOOMLA)
	{
		// TODO: Strip off . & .. for security reasons or add other types of security measures.

		// Get place
		$placeId = self::getPlaceId($uri);

		// This speed up resolving on path of places
		static $places = array();

		$config = EB::config();

		// If this place hasn't been resolved before
		if (!isset($places[$placeId])) {

			// Shared
			if ($placeId=='shared') {
				$path = $config->get('main_shared_path');
				$places['shared'] = self::cleanPath($path);
			}

			// Articles place
			if ($placeId == 'posts') {
				$path = $config->get('main_articles_path');
				$places['posts'] = self::cleanPath($path);
			}

			if ($placeId == 'users') {
				$path = $config->get('main_image_path');
				$places['users'] = self::cleanPath($path);
			}

			// Article place
			if (self::isPostPlace($placeId)) {

				if (!isset($places['post'])) {
					$path = $config->get('main_articles_path');
					$places['post'] = self::cleanPath($path);
				}

				// Get the article id
				$parts = explode(':', $placeId);
				$articleId = $parts[1];

				// Build path
				$places[$placeId] = $places['post'] . '/' . $articleId;
			}

			// User
			if (self::isUserPlace($placeId)) {

				// Do this once to speed things up
				if (!isset($places['user'])) {
					$path = $config->get('main_image_path');
					$places['user'] = self::cleanPath($path);
				}

				// Get user id
				$parts = explode(':', $placeId);
				$userId = $parts[1];

				// Disallow user other than admin to open folders other his own
				// $my = JFactory::getUser();
				// if ($my->id != $userId && !EB::isSiteAdmin()) {
				//     $userId = $my->id;
				// }

				// Build path
				$places[$placeId] = $places['user'] . '/' . $userId;
			}
		}

		$isRootFolder = $placeId == $uri;

		$path = $root . '/' . $places[$placeId];

		if (!$isRootFolder) {
			$path .= '/' . substr($uri, strpos($uri, '/') + 1);
		}


		return $path;
	}

	/**
	 * Converts a URI to a URL
	 *
	 * Example:
	 * user:605/foo/bar.jpg => http://site.com/images/easyblog_images/605/foo/bar.jpg
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getUrl($uri)
	{
		static $root;

		if (!isset($root)) {
			$root = preg_replace("(^https?://)", "//", EASYBLOG_JOOMLA_URI);
		}

		return self::getPath($uri, $root);
	}

	/**
	 * Converts a URI format to KEY format
	 *
	 * Example:
	 * article:3/bar.jpg => _12313asdasd123123
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getKey($uri)
	{

		// If key given, just return key.
		return substr($uri, 0, 1)=='_' ? $uri :
			// Else convert key to uri by
			// adding signature underscore,
			// replacing url unsafe characters,
			// and encoding to base64.
			 '_' . strtr(base64_encode($uri), '+=/', '.-~');
	}

	/**
	 * Converts a KEY to URI format
	 *
	 * Example:
	 * _12313123asdasd123123 => article:3/bar.jpg
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getUri($key)
	{
		// If uri is given, just return uri.
		return substr($key, 0, 1)!=='_' ? $key :
			// Else convert uri to key by
			// removing signature underscore,
			// reversing unsafe characters replacement,
			// and decoding from base64.
			base64_decode(strtr(substr($key, 1), '.-~', '+=/'));
	}

	public static function getHash($key)
	{
		// Returns a one-way unique identifier that is alphanumeric
		// so it can used in strict places like the id of an element.
		return md5(self::getKey($key));
	}

	public static function cleanPath($path)
	{
		return trim(str_ireplace(array('/', '\\'), '/', $path), '/');
	}

	/**
	 * Renders the media manager html output on the site.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function render()
	{
		// Get a list of places
		$places = self::getPlaces();

		$session = JFactory::getSession();

		$config = EB::config();
		$useIndex = $config->get('ajax_use_index');

		$uploadUrl = JURI::base();

		if ($useIndex) {
			$uploadUrl .= 'index.php';
		}
		$uploadUrl .= '?option=com_easyblog&task=media.upload&tmpl=component&lang=en&&sessionid=' . $session->getId() . '&' . EB::getToken() . '=1';

		$template = EB::template();
		$template->set('places', $places);
		$template->set('uploadUrl', $uploadUrl);

		$html = $template->output('site/mediamanager/default');

		return $html;
	}

	/**
	 * Renders a list of articles for media manager
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function renderPosts()
	{
		$posts = array();

		$model = EB::model('Dashboard');
		$userId = EB::user()->id;

		// If the user is an admin, list down all blog posts created on the site
		if (EB::isSiteAdmin()) {
			$userId = null;
		}

		$posts = $model->getEntries($userId, array('state' => EASYBLOG_POST_PUBLISHED));

		$template = EB::template();
		$template->set('posts', $posts);

		$html = $template->output('site/mediamanager/posts');

		return $html;
	}


	/**
	 * Renders the Flickr login
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function renderFlickr()
	{
		$my = JFactory::getUser();
		$template = EB::template();

		// Test if the user is already associated with dropbox
		$oauth = EB::table('OAuth');

		// Test if the user is associated with flickr
		$state = $oauth->loadByUser($my->id, EBLOG_OAUTH_FLICKR);

		if (!$state || empty($oauth->access_token)) {
			$redirect = base64_encode(rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&view=media&layout=flickrLogin&tmpl=component&callback=updateFlickrContent');
			$login = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&task=oauth.request&client=' . EBLOG_OAUTH_FLICKR . '&tmpl=component&redirect=' . $redirect;

			$template->set('login', $login);
			$html = $template->output('site/mediamanager/flickr/login');
		} else {
			$html = EBMM::renderFolder('flickr');
		}

		return $html;
	}

	/**
	 * Renders a list of users in media manager
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function renderUsers()
	{
		// Get a list of authors from the site
		$model = EB::model('Blogger');
		$app = JFactory::getApplication();
		$page = $app->input->get('page', 0, 'int');

		// Default to limit 20 items per page.
		$limit = 20;
		$limitstart = $page * $limit;

		$result = $model->getSiteAuthors($limit, $limitstart);
		$pagination = $model->getPagination();

		// Map them with the profile table
		$authors = array();

		if ($result) {

			//preload users
			$ids = array();
			foreach ($result as $row) {
				$ids[] = $row->id;
			}
			EB::user($ids);

			foreach ($result as $row) {
				$author = EB::user($row->id);
				$authors[] = $author;
			}
		}

		if (!isset($pagination->pagesCurrent)) {
			$currentPage = 'pages.current';
			$totalPage = 'pages.total';

			$pagination->pagesCurrent = $pagination->$currentPage;
			$pagination->pagesTotal = $pagination->$totalPage;
		}

		$template = EB::template();
		$template->set('authors', $authors);
		$template->set('pagination', $pagination);

		$html = $template->output('site/mediamanager/users');

		return $html;
	}

	/**
	 * Normalizes a path
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function normalizeFileName($name)
	{
		// Fix file names containing "/" in the file title
		if (strpos($name, '/') !== false) {
			$name = substr($name, strrpos($name, '/') + 1);
		}

		// Fix file names containing "\" in the file title
		if (strpos($name, '\\') !== false) {
			$name = substr($name, strrpos($name, '\\') + 1);
		}

		// Ensure that the file name is safe
		$name = JFile::makesafe($name);

		$name = trim($name);

		// Remove the extension
		$name = substr($name, 0, -4) . '.' . JFile::getExt($name);

		// Ensure that the file name contains an extension
		if (strpos($name, '.') === false) {
			$name = EB::date()->format('Ymd-Hms') . '.' . $name;
		}

		// Do not allow spaces in the name
		$name = str_ireplace(' ', '-', $name);

		return $name;
	}

	/**
	 * Renders a folder contents in Media manager
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function renderFolder($uri)
	{
		$my = JFactory::getUser();
		$user = EB::user();

		// Get the place data
		$place = self::getPlace($uri);

		// Determines if this is a local type or other remote sources
		$type = self::getSourceType($place->id);

		$currentEditor = $user->getEditor();

		$mm = new self($type);
		$folder = $mm->getItems($uri);

		$config = EB::config();
		$useIndex = $config->get('ajax_use_index');

		// Set the upload url
		$session = JFactory::getSession();
		$uploadUrl = JURI::base();

		if ($useIndex) {
			$uploadUrl .= 'index.php';
		}

		$uploadUrl .= '?option=com_easyblog&task=media.upload&tmpl=component&lang=en&key=' . $folder->key . '&sessionid=' . $session->getId() . '&' . EB::getToken() . '=1';

		// Set the id's for dropzones and browse button
		$browseButtonId = 'eb-mm-folder-browse-' . $folder->key;
		$dropElementId  = 'eb-mm-folder-drop-'   . $folder->key;

		$template = EB::template();
		$template->set('currentEditor', $currentEditor);
		$template->set('uploadUrl', $uploadUrl);
		$template->set('browseButtonId', $browseButtonId);
		$template->set('dropElementId', $dropElementId);
		$template->set('place', $place);
		$template->set('folder', $folder);

		$html = $template->output('site/mediamanager/folder');

		return $html;
	}

	/**
	 * Retrieves a file information
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getFile($uri)
	{
		static $items = array();

		$file = $uri;

		if (!is_object($uri)) {

			// Get the place based on the uri
			$place = self::getPlace($uri);
			$type = self::getSourceType($place->id);

			$mediaManager = new self($type);

			// Get the file information
			$file = $mediaManager->getItem($uri);
		}

		return $file;
	}

	/**
	 * Renders the html codes for file items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function renderFile($uri)
	{
		$file = self::getFile($uri);

		$template = EB::template();
		$template->set('file', $file);

		$html = $template->output('site/mediamanager/file');
		return $html;
	}

	/**
	 * Renders information about a file
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function renderInfo($uri)
	{
		$file = self::getFile($uri);
		$place = self::getPlace($file->uri);

		$template = EB::template();
		$template->set('place', $place);
		$template->set('file' , $file);

		$html = $template->output('site/mediamanager/info');

		return $html;
	}

	/**
	 * Renders a folder tree
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function renderTree($uri)
	{
		// Get the place data
		$place = self::getPlace($uri);

		// Determines if this is a local type or other remote sources
		$type = self::getSourceType($place->id);

		// Render media manager
		$mediaManager = new self($type);
		$folder = $mediaManager->getItems($uri);

		$template = EB::template();
		$template->set('place', $place);
		$template->set('folder', $folder);

		$html = $template->output('site/mediamanager/tree');

		return $html;
	}

	/**
	 * Renders a list of known variations for a set of images
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function renderVariations($uri)
	{
		$file = self::getFile($uri);
		$place = self::getPlace($file->uri);

		// Return empty html for non-image file
		if ($file->type !== 'image') {
			return '';
		}

		$template = EB::template();
		$template->set('place', $place);
		$template->set('file' , $file);

		$html = $template->output('site/mediamanager/variations');
		return $html;
	}

	/**
	 * Retrieves the media item
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getMedia($uri)
	{
		$file = self::getFile($uri);

		$media = new stdClass();
		$media->uri = $uri;
		$media->meta = EBMM::getFile($uri);
		$media->file = EBMM::renderFile($uri);
		$media->info = '';
		$media->variations = EBMM::renderVariations($uri);

		if ($file->type == 'folder') {
			$media->folder = EBMM::renderFolder($uri);
		} else {
			$media->info = EBMM::renderInfo($uri);
		}

		return $media;
	}

	// TODO: Move this to a proper Math library
	public static function formatSize($size)
	{
		$units = array(' B', ' KB', ' MB', ' GB', ' TB');
		for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
		return round($size, 2).$units[$i];
	}

	/**
	 * Determines if the given place id is a shared folder place
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isSharedPlace($placeId)
	{
		if ($placeId == 'shared') {
			return true;
		}

		// Match for shared place
		if (preg_match('/shared/i', $placeId)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the given place id is an album place
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */
	public static function isAlbumPlace($placeId)
	{
		if ($placeId == 'easysocial' || $placeId == 'jomsocial') {
			return true;
		}

		if (preg_match('/easysocial/i', $placeId) || preg_match('/jomsocial/i', $placeId)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the given place id is an album place
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */
	public static function isMoveablePlace($placeId)
	{
		if ($placeId == 'easysocial' || $placeId == 'jomsocial' || $placeId == 'flickr') {
			return false;
		}

		return true;
	}

	public static function isExternalPlace($placeId)
	{
		return !self::isMoveablePlace($placeId);
	}

	/**
	 * Determines if this is a post place
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isPostPlace($placeId)
	{
		return preg_match('/^post\:/i', $placeId);
	}

	/**
	 * Determines if this place is a user's media
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isUserPlace($placeId)
	{
		return preg_match('/^user\:/i', $placeId);
	}
}

class EasyBlogMediaManager extends EBMM {}
