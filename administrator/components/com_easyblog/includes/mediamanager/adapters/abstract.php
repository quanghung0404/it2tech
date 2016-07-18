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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class EasyBlogMediaManagerAbstractSource
{
	/**
	 * Exclusions
	 * @var string
	 */
	protected $rx_exclude;

	/**
	 * Variation types
	 * @var array
	 */
	protected $variationTypes = array(
										EBLOG_BLOG_IMAGE_PREFIX       => 'blogimage',
										EBLOG_USER_VARIATION_PREFIX   => 'user',
										EBLOG_SYSTEM_VARIATION_PREFIX => 'system'
									);

	public function __construct()
	{
		$this->rx_exclude = '/^\\.|^(index\\.html|svn|CVS|__MACOSX)$/uiU';
		$this->rx_variations = $this->rx_variations();
	}

	protected function rx_variations($filename=null)
	{
		$filename = empty($filename) ? '.*' : preg_quote($filename);

		$regex = '/^(' .

			// 3.5 thumbnails
			EBLOG_BLOG_IMAGE_PREFIX . '|' .
			EBLOG_USER_VARIATION_PREFIX . '|' .
			EBLOG_SYSTEM_VARIATION_PREFIX .
			')_([^_]*)_(' . $filename . ')' .

			// 2.0 thumbnails
			'|(' . EBLOG_MEDIA_THUMBNAIL_PREFIX . ')(' . $filename . ')/ui';

		return $regex;
	}

	/**
	 * Determines if the file is excluded
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function isExcluded($file)
	{
		if (preg_match($this->rx_exclude, $file)) {
			return true;
		}

		return false;
	}

	/**
	 * Creates a new item object for media manager
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	protected function createItem($uri)
	{
		$item = new stdClass();

		$item->place = EasyBlogMediaManager::getPlaceId($uri);
		$item->title = EasyBlogMediaManager::getTitle($uri);

		$item->url = EasyBlogMediaManager::getUrl($uri);
		$item->uri = $uri;
		$item->key = EasyBlogMediaManager::getKey($uri);

		return $item;
	}

	/**
	 * Returns the structure of the folder
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	protected function getFolderItem($uri)
	{
		$item = $this->createItem($uri);

		$item->type = 'folder';
		$item->icon = EasyBlogMediaManager::$icons['folder'];
		$item->root = strpos($uri, '/') === false;
		$item->scantime = 0;
		$item->contents = EasyBlogMediaManager::filegroup();

		return $item;
	}

	/**
	 * Retrieves information about a uri
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	protected function getFileItem($uri)
	{
		$item = $this->createItem($uri);

		$extension = EasyBlogMediaManager::getExtension($item->title);

		$item->extension = $extension;
		$item->type = EasyBlogMediaManager::getType($extension);
		$item->icon = EasyBlogMediaManager::getIcon($extension);

		return $item;
	}

	/**
	 * Retrieves a list of items from a given uri
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getItems($uri, $includeVariations=false)
	{
		// Get path and folder
		$folder = $this->getFolderItem($uri);
		$folderpath = EasyBlogMediaManager::getPath($uri);

		// Read the source directory
		$handle = @opendir($folderpath);

		if (!$handle) {

			// Perhaps the folder doesn't exist yet, we should try to create the folder.
			// $state = JFolder::create($folderpath);

			// if (!$state) {
				return $folder;
			// }

			// Read the folder
			// $handle = @opendir($folderpath);
		}

		// Scan
		// ~149ms on 700 files (first-time)
		// ~48ms on 700 files (subsequent)
		$start = microtime(true);

		// Filegroup is the array where files are stored.
		// Sort arrays are used to speed up file sorting.
		$filegroup = EasyBlogMediaManager::filegroup();
		$sort_by_modified = EasyBlogMediaManager::filegroup();
		$sort_by_title    = EasyBlogMediaManager::filegroup();

		// The strategy used here is to use a single loop that build:
		// - data that is ready-to-use
		// - sort arrays so sorting becomes cheap.
		// - variations
		$variations = array();
		$total = 0;

		while (($filename = readdir($handle)) !== false) {

			// Exclude files
			if (preg_match($this->rx_exclude, $filename)) {
				continue;
			}

			// Collect variations
			// ~8ms on 700 files
			if (preg_match($this->rx_variations, $filename, $parts)) {

				if (!$includeVariations) continue;

				// 2.0 thumbnails
				if (count($parts)==6) {
					$source = $parts[5];
					$type   = 'system';
					$name   = 'thumb';

				// 3.5 thumbnails
				} else {
					$source = $parts[3];
					$type   = $this->variationTypes[$parts[1]];
					$name   = $parts[2];
				}

				$key = $type . '/' . $name;

				$variation = new stdClass();
				$variation->name = $name;
				$variation->url  = $folder->url . '/' . $filename;
				$variation->filename = $filename;

				if (!isset($variations[$source])) {
					$variations[$source] = array();
				}

				$variations[$source][$key] = $variation;
				continue;
			}

			$item = new stdClass();
			$item->title = $filename; // getTitle is not needed
			$item->place = $folder->place;
			$item->uri = $folder->uri . '/' . $filename;
			$item->url   = $folder->url . '/' . $filename;
			$item->key   = EasyBlogMediaManager::getKey($item->uri);

			// Folder
			$filepath = $folderpath . '/' . $filename;

			if (is_dir($filepath)) {
				$item->type = 'folder';
				$item->icon = EasyBlogMediaManager::$icons['folder'];
				$item->modified = null;

			// File
			} else {
				$extension = EasyBlogMediaManager::getExtension($filename);
				$item->extension = $extension;
				$item->type      = EasyBlogMediaManager::getType($extension);
				$item->icon      = EasyBlogMediaManager::getIcon($extension);
				$item->modified  = @filemtime($filepath);
			}

			// Get item type
			$type = $item->type;

			// Add thumbnail & preview
			if ($type=='image') {
				$item->thumbnail = $folder->url . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_icon_' . $filename;
				$item->preview   = $folder->url . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_thumbnail_' . $filename;
			}

			// Add to filegroup
			$filegroup[$type][] = $item;

			// Add to sort arrays
			$sort_by_title[$type][]    = $item->title;
			$sort_by_modified[$type][] = $item->modified;

			$total++;
		}

		// Sort folders
		// Folder doesn't need to be sorted by modification time.
		array_multisort($sort_by_title['folder'], SORT_ASC, $filegroup['folder']);

		// Sort files
		// ~0.6ms on 700 files
		foreach (array('image', 'audio', 'video', 'file') as $type) {

			array_multisort($sort_by_modified[$type], SORT_DESC, $sort_by_title[$type], SORT_ASC, $filegroup[$type]);
		}

		// Add variations & thumbnail property
		// ~6ms on 700 files
		if ($includeVariations) {
			foreach($filegroup['image'] as $item) {

				$sizes = isset($variations[$item->title]) ? $variations[$item->title] : array();

				// Original variation
				$variation = new stdClass();
				$variation->name = 'original';
				$variation->type = 'system';
				$variation->url  = $item->url;

				$sizes['system/original'] = $variation;

				$item->variations = $sizes;
			}
		}

		$folder->contents = $filegroup;
		$folder->total = $total;

		$end = microtime(true);
		$scantime = ($end - $start) * 1000;

		$folder->scantime = $scantime;

		return $folder;
	}

	/**
	 * 
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getItem($uri) 
	{
		$start = microtime(true);

		$path = EasyBlogMediaManager::getPath($uri);
		$isFolder = @is_dir($path);

		$item = $isFolder ? $this->getFolderItem($uri) : $this->getFileItem($uri);

		// File stats
		$item->modified = @filemtime($path);
		$item->size = @filesize($path);

		// Variations
		if ($item->type=='image') {

			$folderurl = dirname($item->url);
			$filename  = basename($path);

			$item->thumbnail = $folderurl . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_icon_' . $filename;
			$item->preview   = $folderurl . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_thumbnail_' . $filename;
			$item->variations = $this->getVariations($uri);
		}

		if (is_dir($path)) {
			$item->type = 'folder';
			$item->icon = EasyBlogMediaManager::$icons['folder'];
			$item->modified = null;
		} else {
			$extension = EasyBlogMediaManager::getExtension($item->title);

			$item->extension = $extension;
			$item->type = EasyBlogMediaManager::getType($extension);
			$item->icon = EasyBlogMediaManager::getIcon($extension);
			$item->modified = @filemtime($path);
		}

		$end = microtime(true);
		$scantime = ($end - $start) * 1000;

		$item->scantime = $scantime;

		return $item;
	}

	public function getVariations($uri)
	{
		// Build variations
		$variations = array();

		// Url & paths
		$baseUri = dirname($uri);
		$filename = basename($uri);
		$filepath = EasyBlogMediaManager::getPath($uri);
		$fileurl = EasyBlogMediaManager::getUrl($uri);
		$folderpath = dirname($filepath);
		$folderurl = dirname($fileurl);

		// Original variation
		$variation = new stdClass();
		$variation->name = 'original';
		$variation->type = 'system';
		$variation->url  = $fileurl;

		// Get original variation width & height
		$info = @getimagesize($path);

		if ($info) {
			$variation->width  = $info[0];
			$variation->height = $info[1];
		}

		$variations['system/original'] = $variation;

		// Scan for variations
		$handle = @opendir($folderpath);

		if ($handle) {

			// Regex to match variations related to this file
			$filter = $this->rx_variations($filename);

			while (($filename = readdir($handle)) !== false) {

				if (!preg_match($filter, $filename, $parts)) {
					continue;
				}

				// 2.0 thumbnails
				if (count($parts)==6) {
					$source = $parts[5];
					$type   = 'system';
					$name   = 'thumb';

				// 3.5 thumbnails
				} else {
					$source = $parts[3];
					$type   = $this->variationTypes[$parts[1]];
					$name   = $parts[2];
				}

				// Variables
				$key  = $type . '/' . $name;
				$url  = $folderurl . '/' . $filename;
				$path = $folderpath . '/' . $filename;

				// Create variation
				$variation = new stdClass();
				$variation->name = $name;
				$variation->type = $type;
				$variation->url = $url;
				$variation->uri = $baseUri . '/' . basename($filename);

				// Get variation width & height
				$info = @getimagesize($path);

				if ($info) {
					$variation->width  = $info[0];
					$variation->height = $info[1];
				}

				$variation->size = @filesize($path);

				// Add to variations
				$variations[$key] = $variation;
			}
		}

		return $variations;
	}

	public static function uniqueFilename($folderpath, $filename)
	{
		// Break up file parts
		$dot = strrpos($filename, '.');
		$name = $dot===false ? $filename : substr($filename, 0, $dot - 1);
		$extension = $dot===false ? '' : substr($filename, $dot);
		$timestamp = EB::date()->format("%Y%m%d-%H%M%S");
		$i = 1;

		while (JFile::exists($folderpath . '/' . $filename)) {
			$filename = $name . '_' . $timestamp . '_' . $i . $extension;
			$i++;
		}

		return $filename;
	}

	public function upload($file, $folderuri=null)
	{
		// If $uri is not given, assume current user
		if (empty($folderuri)) {
			$my = EB::user();
			$folderuri = 'user:' . $my->id;
		}

		// Get folder item and path
		$folderpath = EasyBlogMediaManager::getPath($folderuri);

		// Create folder (and parent folders) if necessary
		// We used to copy index.html on it. Now we don't.
		$createFolder = JFolder::create($folderpath);

		if (!$createFolder) {
			// TODO: Language
			return EB::exception('Unable to create folder');
		}

		// Sanitize filename, prevent filename collision.
		$filename = JFile::makeSafe($file['name']);
		$filename = self::uniqueFilename($folderpath, $filename);

		// Move uploaded file to its destination
		$uploadpath = $file['tmp_name'];
		$filepath = $folderpath . '/' . $filename;

		// Try to move the uploaded file into the file path
		$state = JFile::copy($uploadpath, $filepath);
		if (!$state) {
			return EB::exception('COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_ERROR');
		}

		// @TODO: Resize the original image if necessary
		// if ($image->orientationFixed || $config->get('main_resize_original_image')) {

		// 	$maxWidth  = $config->get('main_original_image_width');
		// 	$maxHeight = $config->get('main_original_image_height');
		// 	$quality   = $config->get('main_original_image_quality');

		// 	if ($image->getWidth() > $maxWidth || $image->getHeight() > $maxHeight) {
		// 		$image->resizeWithin($maxWidth, $maxHeight);
		// 	}

		// 	$state = $image->save($path, $image->image_type, $quality);

		// 	if ($state===false) {
		// 		return EB::exception('Unable to resize original image.');
		// 	}
		// }

		// If this is an image, also create image variations.
		if (@getimagesize($filepath)!==false) {

			// Render the image sets
			EB::imageset()->initDimensions($filepath);
		}

		// Construct item
		$uri = $folderuri . '/' . $filename;
		$item = $this->getItem($uri);

		return $item;
	}

	/**
	 * Deletes a file from the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function delete($uri)
	{
		$item = $this->getItem($uri);
		$path = EasyBlogMediaManager::getPath($uri);

		// Remove folder
		if ($item->type=='folder') {

			if (!JFolder::delete($path)) {
				// TODO: Language
				return EB::exception('Unable to remove folder.');
			}
		}

		// Remove variations before removing original file,
		// so if anything goes wrong when removing variations, the
		// original file is still intact.
		if ($item->type=='image') {

			$state = $this->deleteVariations($uri);

			if ($state instanceof EasyBlogException) {
				return $state;
			}
		}

		if (!JFile::delete($path)) {
			// TODO: Language
			return EB::exception('Unable to remove file.');
		}

		return true;
	}

	/**
	 * Determines if this item is a legacy image file
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function isLegacyImageVariation()
	{

	}

	/**
	 * Renames a file
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string	Source path
	 * @param	string	Target path
	 * @return	
	 */
	public function rename($sourceUri, $targetUri)
	{
		$sourcePath = EasyBlogMediaManager::getPath($sourceUri);
		$targetPath = EasyBlogMediaManager::getPath($targetUri);

		$sourceItem = $this->getItem($sourceUri);

		// Get the new filename
		$newFileName = basename($targetPath);

		// Rename the main file
		$newUri = dirname($sourceUri) . '/' . $newFileName;
		$source = EasyBlogMediaManager::getPath($sourceUri);
		$target = EasyBlogMediaManager::getPath($newUri);

		$state = JFile::move($source, $target);

		// For images, we want to rename the variations as well.
		if ($sourceItem->type == 'image' && isset($sourceItem->variations)) {

			foreach ($sourceItem->variations as $key => $variation) {
				
				if ($key == 'system/original') {
					continue;
				}

				// Get the new uri
				$newUri = dirname($variation->uri) . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_' . $variation->name . '_' . $newFileName;
				$source = EasyBlogMediaManager::getPath($variation->uri);
				$target = EasyBlogMediaManager::getPath($newUri);

				// Try to move the file now
				$state = JFile::move($source, $target);
			}
		}

	}

	/**
	 * Rename all variations when the main file is renamed
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function renameVariations($sourceUri, $targetUri)
	{
		$fileName = basename($targetUri);

		dump($fileName);
	}

	/**
	 * Moves a file or folder from destination to target
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function move($source, $target)
	{
        $sourceUri = EasyBlogMediaManager::getUri($source);
        $targetUri = EasyBlogMediaManager::getUri($target);

        // Get the absolute paths
		$sourcePath = EasyBlogMediaManager::getPath($sourceUri);
		$targetPath = EasyBlogMediaManager::getPath($targetUri);

		// Get the new filename
		$newFileName = basename($sourcePath);

		// Get the absolute path to the target file / folder
		$newTargetPath = $targetPath . '/' . $newFileName;
		$newTargetUri = $targetUri . '/' . $newFileName;

		// Get the item details
		$item = $this->getItem($sourceUri);

		// Test if the source items really exists
		if ($item->type == 'folder' && !JFolder::exists($sourcePath)) {
			return EB::exception('Unable to locate source folder to move.');
		}

		// Test if the source items really exists
		if ($item->type != 'folder' && !JFile::exists($sourcePath)) {
			return EB::exception('Unable to locate source file to move.');
		}

		// Test for target path
		if ($item->type == 'folder' && JFolder::exists($newTargetPath)) {
			return EB::exception('A folder with the same name exists on the specified target path.');
		}


		if ($item->type != 'folder' && JFile::exists($newTargetPath)) {
			return EB::exception('A file with the same name exists on the specified target path.');
		}

		// If this is a folder, just move the folder
		if ($item->type == 'folder') {

			$state = JFolder::move($sourcePath, $newTargetPath);

			if (!$state) {
				return EB::exception('Unable to move folder.');
			}
		}

		// Move the file
		if ($item->type != 'folder') {

			JFile::move($sourcePath, $newTargetPath);

			// Move variations
			if ($item->type == 'image') {

				// For images, we want to rename the variations as well.
				if ($item->type == 'image' && isset($item->variations)) {

					foreach ($item->variations as $key => $variation) {
						
						if ($key == 'system/original') {
							continue;
						}

						// Get the new uri
						$newUri = $targetUri . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_' . $variation->name . '_' . $newFileName;
						$target = EasyBlogMediaManager::getPath($newUri);

						// Get the original file for the variation
						$source = EasyBlogMediaManager::getPath($variation->uri);

						// Check if the file exists first
						if (!JFile::exists($source)) {
							continue;
						}

						// Try to move the file now
						$state = JFile::move($source, $target);
					}
				}

			}
		}


		// Get the new item info about the moved object
		$item = $this->getItem($newTargetUri);

		return $item;
	}

	/**
	 * Structures the variation object
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getVariationItem($name, $url, $fileName)
	{
		$variation = new stdClass();

		$variation->name = $name;
		$variation->url = $url;
		$variation->filename = $fileName;

		return $variation;
	}

	public function createVariation($uri, $name, $params)
	{
		$config = EB::config();

		// Filepath, filename, folderpath, folderuri
		$filepath   = EasyBlogMediaManager::getPath($uri);
		$filename   = basename($uri);
		$folderpath = dirname($filepath);
		$folderuri  = dirname($uri);

		// Build target name, filename, path, uri, quality.
		$i = 0; do {

			$target_name = $name . (empty($i) ? '' : $i);
			$target_filename = EBLOG_USER_VARIATION_PREFIX . '_'
			                   . $target_name
			                   . '-'  . $this->serializeParam($params) . '_'
			                   . $filename;
			$target_path = $folderpath . '/' . $target_filename;
			$i++;

		} while(JFile::exists($target_path));

		$target_uri     = $foldeuri . '/' . $target_filename;
		$target_quality = isset($params->quality) ? $params->quality : $config->get('main_image_quality');

		// TODO: Reject if width/height exceeds
        // maxVariationWidth: $system->config->get( 'main_media_manager_image_panel_max_variation_image_width' );
        // maxVariationHeight: $system->config->get( 'main_media_manager_image_panel_max_variation_image_height' );

		// Resize image
		$image = EB::simpleimage();
		$image->load($filepath);
		$image->resize($params->width, $params->height, $params->x, $params->y);
		$state = $image->save($target_path, $image->image_type, $target_quality);

		if (!$state) {
			// TODO: Language
			return EB::exception('COM_EASYBLOG_FAILED_TO_CREATE_VARIATION_PERMISSIONS');
		}

		// Create variation object
		$variation = new stdClass();
		$variation->name = $target_name;
		$variation->type = 'user';
		$variation->url = $this->getUrl($target_uri);
		$variation->width = $params->width;
		$variation->height = $params->height;

		return $variation;
	}

	// public function deleteVariation($uri, $name)
	// {
	// 	// We only allow deletion of user prefixes.
	// 	$filename = EBLOG_USER_VARIATION_PREFIX . '_' . $name . '_' . basename($uri);
	// 	$filepath = EasyBlogMediaManager::getPath(dirname($uri)) . '/' . $filename;

	// 	if (!JFile::exists($filepath)) {
	// 		return EB::exception('COM_EASYBLOG_FAILED_TO_DELETE_VARIATION_AS_IT_DOESNT_EXISTS');
	// 	}

	// 	$state = JFile::delete($path);
	// 	if (!$state) {
	// 		// TODO: Language
	// 		return EB::exception('COM_EASYBLOG_FAILED_TO_DELETE_VARIATION_PERMISSIONS');
	// 	}

	// 	return true;
	// }

	/**
	 * Remove all image variations from the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function deleteVariations($uri)
	{
		// Accepts uri or item.
		if (is_string($uri)) {
			$item = $this->getItem($uri);
		} else {
			$item = $uri;
		}

		$errors = array();

		foreach ($item->variations as $key => $variation) {

			// Skip original variation
			if ($key == 'system/original') {
				continue;
			}

			// Get the variations path
			$file = EasyBlogMediaManager::getPath($variation->uri);

			if (!JFile::exists($file)) {
				$errors[] = $file;

				continue;
			}

			$state = JFile::delete($file);
			
			if (!$state) {
				$errors[] = $file;
			}
		}

		if (count($errors)) {
			// TODO: Language
			return EB::exception('Unable to remove the following image variations: ' . implode(', ', $errors) . '.');
		}

		return true;
	}

	public function serializeParam($params)
	{
		$parts = array();

		$map = array(
			'width'   => 'w',
			'height'  => 'h',
			'x'       => 'x',
			'y'       => 'y',
			'quality' => 'q'
		);

		foreach($map as $key => $value) {
			if (!empty($params->$key)) {
			 $parts[] = $value . $params->$key;
			}
		}

		$str = implode(',', $parts);
	}
}
