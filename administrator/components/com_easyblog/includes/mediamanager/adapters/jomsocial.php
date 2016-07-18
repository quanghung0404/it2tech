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

class EasyBlogMediaManagerJomSocialSource extends EasyBlog
{
	private $relative = null;
	private $path = null;
	private $fileName = null;
	private $baseURI = null;
	private $exists = null;

	/**
	 * Determines if JomSocial Exists
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exists()
	{
		if (!EB::jomsocial()->exists()) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves a list of albums or photos in an album
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getItems($uri)
	{
		$exists = $this->exists();

		// Namespaces are divided by : and /
		$parts = explode(':', $uri);
		$viewAll = count($parts) == 1;

		// Let's build the photos URL now.
		$items = EBMM::filegroup();

		if ($viewAll) {

			// Use own method to retrieve albums to work around JomSocial's silly bugs.
			$albums = $this->getAlbums($this->my->id);

			if ($albums) {
				foreach ($albums as $row) {
					$items['folder'][] = $this->decorateFolder($row);
				}
			}

			// $folder = EBMM::folder($uri, $items);
			// $folder->icon = EBMM::$icons['place/jomsocial'];
			// $folder->root = true;
			// $folder->scantime = 0;

			// Folder
			$folder = new stdClass();
			$folder->place = 'jomsocial';
			$folder->title = JText::_('COM_EASYBLOG_MM_PLACE_JOMSOCIAL');
			$folder->url = 'jomsocial';
			$folder->uri = 'jomsocial';
			$folder->key = 'jomsocial';
			$folder->type = 'folder';
			$folder->icon = EasyBlogMediaManager::$icons['place/jomsocial'];
			$folder->root = true;
			$folder->scantime = 0;
			$folder->contents = $items;
			$folder->total = count($items['folder']);

		} else {

			// Get the album id it is trying to view
			$albumId = (int) $parts[1];

			$album = JTable::getInstance('Album', 'CTable');
			$album->load($albumId);

			// Render the photos model
			$model = CFactory::getModel('Photos');
			$photos = $model->getAllPhotos($album->id);

			if ($photos) {
				foreach ($photos as $photo) {
					$items['image'][] = $this->decorateImage($photo, $album);
				}
			}

			// $folder = EBMM::folder($uri, $items);

			// $folder->title = $album->name;
			// $folder->icon = EBMM::$icons['place/jomsocial'];
			// $folder->root = true;
			// $folder->scantime = 0;

			// Folder
			$folder = new stdClass();
			$folder->place = 'jomsocial';
			$folder->title = $album->get('title');
			$folder->url = 'jomsocial';
			$folder->uri = 'jomsocial';
			$folder->key = 'jomsocial';
			$folder->type = 'folder';
			$folder->icon = EBMM::$icons['place/jomsocial'];
			$folder->root = true;
			$folder->scantime = 0;
			$folder->contents = $items;
			$folder->total = count($items);

		}

		return $folder;
	}

	/**
	 * Decorates the album item as a folder for JomSocial
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function decorateFolder(CTableAlbum $album)
	{
		$obj = new stdClass();
		$obj->place = 'jomsocial';
		$obj->title = $album->name;
		$obj->url = rtrim(JURI::root() , '/') . '/' . str_ireplace(JPATH_ROOT, '', $album->path);
		$obj->key = EBMM::getKey('jomsocial:' . $album->id);
		$obj->type = 'folder';
		$obj->icon = EBMM::getIcon('image');
		$obj->modified = $album->created;
		$obj->size = 0;

		$obj->thumbnail = $album->getCoverThumbURI();
		$obj->preview = $album->getCoverThumbURI();

		return $obj;
	}


	/**
	 * Decorates the image object
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function decorateImage(CTablePhoto $photo, CTableAlbum $album)
	{
		$obj = new stdClass();
		$obj->place = 'jomsocial';
		$obj->title = JText::_($photo->caption);
		$obj->url = $photo->getOriginalURI();
		$obj->key = EBMM::getKey('jomsocial:' . $photo->albumid . '/' . $photo->id);
		$obj->type = 'image';
		$obj->icon = EBMM::getIcon('image');
		$obj->modified = $photo->created;
		$obj->size = 0;

		// Thumbnails are larger than preview
		$obj->thumbnail = $photo->getThumbURI();

		// Preview is used in the listing
		$obj->preview = $photo->getThumbURI();

		$obj->variations = $this->getVariations($photo);

		return $obj;
	}

	/**
	 * Returns information about a photo in an album
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

		if ($this->isAlbum($uri)) {
			return $this->getItems($uri);
		}

		// Get the photo id
		$id = (int) EBMM::getFilename($uri);
		$photo = JTable::getInstance('Photo', 'CTable');
		$photo->load($id);

		// Get the filesize
		$size = @filesize(JPATH_ROOT . '/' . $photo->original);

		$album = JTable::getInstance('Album', 'CTable');
		$album->load($photo->albumid);

		$item = EBMM::file($uri, 'image');

		$item->place = 'jomsocial';
		$item->title = $photo->caption;
		$item->url = $photo->getOriginalURI();
		$item->path = 'jomsocial';
		$item->size = $size;
		$item->modified = $photo->created;
		$item->thumbnail = $photo->getThumbURI();
		$item->preview = $photo->getThumbURI();
		$item->size = 0;
		$item->variations = $this->getVariations($photo, $album);

		return $item;
	}

	/**
	 * Retrieves a list of albums created on the site in JomSocial
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getAlbums($userId)
    {
    	$db = JFactory::getDBO();

    	$query = array();
    	$query[] = 'SELECT * FROM ' . $db->quoteName('#__community_photos_albums');
    	$query[] = 'WHERE ' . $db->quoteName('creator') . '=' . $db->Quote($userId);
    	$query[] = 'AND ' . $db->quoteName('groupid') . '=' . $db->Quote(0);

    	$query = implode(' ', $query);

    	$db->setQuery($query);
    	$result = $db->loadObjectList();

    	// Prepopulate with the data
        $data = array();

    	if (!$result) {
    		return $data;
    	}

    	foreach ($result as $row) {
    		$album = JTable::getInstance('Album', 'CTable');
    		$album->bind($row);

    		$album->thumbnail = $album->getCoverThumbPath();

    		$data[] = $album;
    	}

        return $data;
    }

	/**
	 * Retrieves a list of photo variations on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getVariations(CTablePhoto $photo)
	{
		// In JomSocial, there's only 2 variations, the original and the thumbnail.
		$sizes = array('original', 'thumbnail');

		$result = array();

		foreach ($sizes as $size) {

			$key = 'system/' . strtolower($size);

			// Create variation
			$variation = new stdClass();
			$variation->key = $key;
			$variation->name = $size;
			$variation->type = 'system';

			if ($size == 'original') {
				$variation->url = $photo->getOriginalURI();
			}

			if ($size == 'thumbnail') {
				$variation->url = $photo->getThumbURI();
			}

			// Get info about the image
			$info = @getimagesize(JPATH_ROOT . '/' . $photo->$size);
			list($width, $height) = $info;

			$variation->width  = $width;
			$variation->height = $height;
			$variation->size = 0;

			$result[$key] = $variation;
		}

		return $result;
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
}
