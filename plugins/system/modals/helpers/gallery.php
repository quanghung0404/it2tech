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


class PlgSystemModalsHelperGallery
{
	var $helpers = array();

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemModalsHelpers::getInstance();
		$this->params  = $this->helpers->getParams();
	}

	public function buildGallery($attributes, $data, $content)
	{
		$html = array();

		$folder = $this->helpers->get('file')->trimFolder($data['gallery']);

		jimport('joomla.filesystem.folder');
		if (!JFolder::exists(JPATH_SITE . '/' . $folder))
		{
			return '<a href="#">';
		}

		$this->helpers->get('image')->setImageDataFromDataFile($folder, $data);

		unset($data['gallery']);
		unset($data['inline']);

		$data['group'] = uniqid('gallery_') . rand(1000, 9999);

		$params          = new stdClass;
		$params->showall = isset($data['showall']) ? $data['showall'] : $this->params->gallery_showall;
		unset($data['showall']);

		$params->style = '';
		if ($params->showall || $content == '')
		{
			$w             = (int) (!empty($data['thumbwidth']) ? $data['thumbwidth'] : $this->params->gallery_thumb_width);
			$h             = (int) (!empty($data['thumbheight']) ? $data['thumbheight'] : $this->params->gallery_thumb_height);
			$style         = ($w ? 'width:' . $w . 'px;' : '')
				. ($h ? 'height:' . $h . 'px;' : '');
			$params->style = $style ? ' style="' . $style . '"' : '';
		}
		unset($data['thumbwidth']);
		unset($data['thumbheight']);

		$params->thumbsuffix = isset($data['thumbsuffix']) ? $data['thumbsuffix'] : $this->params->gallery_thumb_suffix;
		unset($data['thumbsuffix']);

		$params->separator = isset($data['separator']) ? $data['separator'] : str_replace('{none}', '', $this->params->gallery_separator);
		unset($data['separator']);

		$params->filter = isset($data['filter']) ? $data['filter'] : $this->params->gallery_filter;
		unset($data['filter']);

		$params->first = isset($data['first']) ? $data['first'] : 0;
		unset($data['first']);

		$params->auto_titles = isset($data['auto_titles']) ? $data['auto_titles'] : $this->params->auto_titles;
		unset($data['auto_titles']);

		$params->title_case = isset($data['title_case']) ? $data['title_case'] : $this->params->title_case;
		unset($data['title_case']);

		$params->firstid = 0;

		$images = $this->getGalleryImageList($folder, $params);

		foreach ($images as $count => $image)
		{
			$html[] = $this->getGalleryImageLink($folder, $image, $attributes, $data, $content, $params, $count);

			// Add hidden class to other images if not show all
			if (!$count && !$params->showall)
			{
				$attributes->class .= ' modal_link_hidden';
				$attributes->id = '';
			}
		}

		return implode('</a>' . $params->separator, $html);
	}

	private function getGalleryImageList($folder, &$params)
	{
		$folder = $this->helpers->get('file')->trimFolder($folder);
		$filter = $params->filter;

		if (preg_match('#(.*?\()([^\)]*)(\).*?)#', $params->filter, $match))
		{
			$filter = $match['1'] . $match['2'] . '|' . strtoupper($match['2']) . $match['3'];
		}

		$files = JFolder::files(JPATH_SITE . '/' . $folder, $filter);

		$count  = 0;
		$images = array();
		foreach ($files as $file)
		{
			if (!$image = $this->helpers->get('image')->getImageObject($folder, $file, $params, $count, true))
			{
				continue;
			}
			$images[$count] = $image;
			$count++;
		}

		return $images;
	}

	private function getGalleryImageLink($folder, &$image, &$attributes, &$data, $content, &$params, &$count)
	{
		$attributes->href = JUri::root(true) . '/' . $image->folder . '/' . $image->image;
		$image_data       = $data;

		$this->helpers->get('image')->setImageDataAtribute('title', $image_data, $image->image, $count + 1);
		$this->helpers->get('image')->setImageDataAtribute('description', $image_data, $image->image, $count + 1);

		if (!isset($image_data['title']) && $params->auto_titles)
		{
			// set the auto title
			$image_data['title'] = $this->helpers->get('file')->getTitle($image->image, $params->title_case);
		}

		$link = $this->helpers->get('link')->buildLink($attributes, $image_data);

		if ($params->showall || ($count == $params->firstid && $content == ''))
		{
			// show the thumbnail if showall is set or if the first image should be shown
			$folder = $this->helpers->get('file')->trimFolder($folder);
			$link .= '<img src="' . JRoute::_(JUri::base(true) . '/' . $folder . '/' . $image->thumbnail) . '"' . $params->style . ' />';

			return $link;
		}

		if ($count != $params->firstid)
		{
			return $link;
		}

		return $link . $content;
	}
}
