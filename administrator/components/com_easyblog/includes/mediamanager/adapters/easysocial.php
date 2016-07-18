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

jimport('joomla.filesystem.file');

class EasyBlogMediaManagerEasySocialSource extends EasyBlog
{
	/**
	 * Determines if EasySocial integrations are available.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exists()
	{
		$exists = EB::easysocial()->exists();

		if ($exists) {
			return true;
		}

		return false;
	}

	/**
	 * Returns an array of folders / albums in a given folder since jomsocial only stores user images here.
	 *
	 * @access	public
	 * @param	string	$path	The path that contains the items.
	 * @param	int 	$depth	The depth level to search for child items.
	 */
	public function getItems($uri)
	{
		if (!$this->exists()) {
			return false;
		}

		// load easysocial language files.
		Foundry::language()->load('com_easysocial', JPATH_ROOT);


		// Determines if we are trying to view a single album or all albums
		$parts = explode(':', $uri);
		$viewAll = count($parts) == 1;

		// Let's build the photos URL now.
		$items = EBMM::filegroup();

		// Viewing of all albums
		if ($viewAll) {

			$model = FD::model('Albums');
			$result = $model->getAlbums($this->my->id, SOCIAL_TYPE_USER);

			if ($result) {
				foreach ($result as $row) {
					$items['folder'][] = $this->decorateFolder($row);
				}
			}

			// Folder
			$folder = new stdClass();
			$folder->place = 'easysocial';
			$folder->title = JText::_('COM_EASYBLOG_MM_PLACE_EASYSOCIAL');
			$folder->url = 'easysocial';
			$folder->uri = 'easysocial';
			$folder->key = 'easysocial';
			$folder->type = 'folder';
			$folder->icon = EasyBlogMediaManager::$icons['place/easysocial'];
			$folder->root = true;
			$folder->scantime = 0;
			$folder->contents = $items;
			$folder->total = count($items['folder']);
		} else {

			// Get the album id it is trying to view
			$albumId = (int) $parts[1];
			$album = FD::table('Album');
			$album->load($albumId);

			// Render the photos model
			$model = FD::model('Photos');
			$options = array('album_id' => $albumId, 'pagination' => false);

			// Get the photos
			$photos = $model->getPhotos($options);

			if ($photos) {
				foreach ($photos as $photo) {
					$items['image'][] = $this->decorateImage($photo, $album);
				}
			}

			// Folder
			$folder = new stdClass();
			$folder->place = 'easysocial';
			$folder->title = JText::_($album->get('title'));
			$folder->url = 'easysocial';
			$folder->uri = 'easysocial';
			$folder->key = 'easysocial';
			$folder->type = 'folder';
			$folder->icon = EBMM::$icons['place/easysocial'];
			$folder->root = true;
			$folder->scantime = 0;
			$folder->contents = $items;
			$folder->total = count($items);
		}

		return $folder;
	}

	/**
	 * Retrieves information about a particular photo
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getItem($uri)
	{
		if (!$this->exists()) {
			return false;
		}

		// If this is an album request, we should list down all photos
		if ($this->isAlbum($uri)) {
			return $this->getItems($uri);
		}

		// Get the photo id
		$id = (int) EBMM::getFilename($uri);

		// Load the photo item
		$photo = FD::table('Photo');
		$photo->load($id);

		// Get the album object
		$album = $photo->getAlbum();

		$item = new stdClass();

		$item->place = 'easysocial';
		$item->title = $photo->get('title');
		$item->url = $photo->getSource('original');
		$item->uri = $uri;
		$item->path = 'easysocial';
		$item->type = 'image';
		$item->icon = '';
		$item->size = 0;
		$item->modified = $photo->created;
		$item->key = EBMM::getKey($uri);
		$item->thumbnail = $photo->getSource('thumbnail');
		$item->preview = $photo->getSource('thumbnail');
		$item->variations = $this->getVariations($photo, $album);

		return $item;
	}

	/**
	 * Decorates the properties of an album
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function decorateFolder(&$item)
	{
		$obj = new stdClass();
		$obj->place = 'easysocial';
		$obj->title = JText::_($item->title);
		$obj->url = rtrim(JURI::root() , '/') . str_ireplace(JPATH_ROOT, '', $item->getStoragePath());
		$obj->key = EBMM::getKey('easysocial:' . $item->id);
		$obj->type = 'folder';
		$obj->icon = EBMM::getIcon('image');
		$obj->modified = $item->created;
		$obj->size = 0;

		$obj->thumbnail = $item->getCoverUrl();
		$obj->preview = $item->getCoverUrl();

		return $obj;
	}

	/**
	 *
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function decorateImage(SocialTablePhoto $photo, SocialTableAlbum $album)
	{
		$obj = new stdClass();
		$obj->place = 'easysocial';
		$obj->title = $photo->get('title');
		$obj->url = rtrim(JURI::root() , '/') . str_ireplace(JPATH_ROOT, '', $photo->getStoragePath($album));
		$obj->key = EBMM::getKey('easysocial:' . $photo->album_id . '/' . $photo->id);
		$obj->type = 'image';
		$obj->icon = EBMM::getIcon('image');
		$obj->modified = $photo->created;
		$obj->size = 0;

		// Thumbnails are larger than preview
		$obj->thumbnail = $photo->getSource();

		// Preview is used in the listing
		$obj->preview = $photo->getSource();

		$obj->variations = $this->getVariations($photo);

		return $obj;
	}

	/**
	 * Determines if a given uri is an album or a photo item
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isAlbum($uri)
	{
		$parts = explode('/', $uri);

		if (count($parts) > 1) {
			return false;
		}

		return true;
	}

	/**
	 * Creates a new item object
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createItem($uri)
	{

		return $item;
	}

	/**
	 * Retrieves variation for an image
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getVariations(SocialTablePhoto $photo)
	{
		$result = array();

		require_once(JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/photos/photos.php');

		foreach (SocialPhotos::$sizes as $title => $size) {

			$key = 'system/' . strtolower($title);

			// Create variation
			$variation = new stdClass();
			$variation->key = $key;
			$variation->name = $title;
			$variation->type = 'system';
			$variation->url = $photo->getSource($title);
			$variation->width = $size['width'];
			$variation->height = $size['height'];
			$variation->size = 0;

			$result[$key] = $variation;
		}

		return $result;
	}
}
