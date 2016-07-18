<?php
/**
 * @package         Modals
 * @version         6.2.9PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;


class PlgSystemModalsHelperImage
{
	var $helpers = array();
	var $data_files = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemModalsHelpers::getInstance();
	}

	public function getImageObject($folder, $file, &$params, $count = 0, $ignore_thumbnails = 0)
	{
		$file         = utf8_encode($file);
		$reverse_file = $this->isThumbnail($folder, $file, $params->thumbsuffix);

		if ($reverse_file && $ignore_thumbnails)
		{
			// Return false if this is a gallery, as we want to ignore thumbnails
			return false;
		}

		$image     = $reverse_file ?: $file;
		$thumbnail = $reverse_file ? $file : $this->getThumbnailFile($folder, $file, $params->thumbsuffix);

		// check if this image should be set as first in the list
		if (isset($params->first) && $params->first && $params->first == $file)
		{
			$params->firstid = $count;
		}

		return (object) array(
			'folder'    => $folder,
			'image'     => $image,
			'thumbnail' => $thumbnail,
		);
	}

	public function setImageDataFromDataFile($folder, &$data)
	{
		$file_data = $this->getImageDataFromDataFile($folder);

		if (empty($file_data) || !is_array($file_data))
		{
			return;
		}

		$data = $file_data + $data;
	}

	private function getImageDataFromDataFile($folder)
	{
		$folder = $this->helpers->get('file')->trimFolder($folder);

		if (isset($this->data_files[$folder]))
		{
			return $this->data_files[$folder];
		}

		jimport('joomla.filesystem.file');
		if (!JFile::exists(JPATH_SITE . '/' . $folder . '/' . 'data.txt'))
		{
			return;
		}

		$data = file_get_contents(JPATH_SITE . '/' . $folder . '/' . 'data.txt');

		$data = str_replace("\r", '', $data);
		$data = explode("\n", $data);

		$array = array();
		foreach ($data as $data_line)
		{
			if (empty($data_line)
				|| $data_line['0'] == '#'
				|| strpos($data_line, '=') === false
			)
			{
				continue;
			}
			list($key, $val) = explode('=', $data_line, 2);
			$array[$key] = $val;
		}

		$this->data_files[$folder] = $array;

		return $array;
	}

	public function setImageDataAtribute($type, &$image_data, $image, $count = 0)
	{
		if (isset($image_data[$type . '_' . $count]))
		{
			$image_data[$type] = $image_data[$type . '_' . $count];

			return;
		}

		$image_name = str_replace('.' . $this->helpers->get('file')->getFiletype($image), '', $image);

		if (isset($image_data[$type . '_' . $image_name]))
		{
			$image_data[$type] = $image_data[$type . '_' . $image_name];
		}
	}

	private function isThumbnail($folder, $file, $thumbsuffix)
	{

		// this image is a thumbnail
		if (!preg_match('#' . $thumbsuffix . '(\.[^.]+)$#', $file, $match))
		{
			return false;
		}

		$folder = $this->helpers->get('file')->trimFolder($folder);

		// check if there is a non-thumbnail image
		$test = str_replace($match['0'], $match['1'], $file);
		if (JFile::exists(JPATH_SITE . '/' . $folder . '/' . utf8_decode($test)))
		{
			return $test;
		}

		// there is no non-thumbnail image
		return false;
	}

	private function getThumbnailFile($folder, $file, $thumbsuffix)
	{
		$folder = $this->helpers->get('file')->trimFolder($folder);

		// check if there is a thumbnail image
		// image = image_x.jpg => thumbnail = image_x_t.jpg
		// image = image_1234.jpg => thumbnail = image_1234_t.jpg
		$thumbnail = preg_replace('#\.[^.]+$#', $thumbsuffix . '\0', $file);
		if (JFile::exists(JPATH_SITE . '/' . $folder . '/' . utf8_decode($thumbnail)))
		{
			// if there is a thumbnail image, then set it in the var
			return $thumbnail;
		}

		// remove ending letter/digits and test for thumbnail on that:
		// image = image_x.jpg => thumbnail = image_t.jpg
		// image = image_1234.jpg => thumbnail = image_t.jpg
		$thumbnail = preg_replace('#_(?:[a-z]|[0-9]+)(\.[^.]+)$#', $thumbsuffix . '\1', $file);
		if (JFile::exists(JPATH_SITE . '/' . $folder . '/' . utf8_decode($thumbnail)))
		{
			// if there is a thumbnail image, then set it in the var
			return $thumbnail;
		}

		return $file;
	}
}
