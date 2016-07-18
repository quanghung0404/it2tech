<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

class modImageWallHelper
{
	public static function getPosts(&$params)
	{
		$config = EB::config();
		$count = (int) $params->get('count', 0);

		// Retrieve the model from the component.
		$model = EB::model('Blog');

		$categories	= trim($params->get('catid'));
		$type = !empty($categories)? 'category' : '';

		if (!empty($categories)) {
			$categories = explode(',', $categories);
		}

		$sorting = array();

		$sorting[] = $params->get('sorting', 'latest');
		$sorting[] = $params->get('ordering', 'desc');

		$rows = $model->getBlogsBy($type, $categories, $sorting, $count, EBLOG_FILTER_PUBLISHED, null, false);
		$posts = array();

		// Retreive settings from params
		$maxWidth = $params->get('imagewidth' , 300);
		$maxHeight = $params->get('imageheight', 200);

		foreach ($rows as $data) {
			$row = EB::post($data->id);
			$row->bind($data);

			$row->media  = '';
			//var_dump($row->image);
			if (!empty($row->image)) {
				$media = $row->getImage('thumbnail');
				$imgHtml = $media;
				$row->media = $imgHtml;
			} else {

				$image = self::getImage($row->intro . $row->content);

				if ($image !== false) {
					// TODO: Here's where we need to crop the image based on the cropping ratio provided in the params.
					// Currently this is just a lame hack to fix the width and height

					$pattern = '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'>]*)/i';
					preg_match($pattern, $image, $matches);

					$imageurl = '';

					if ($matches) {
						$imageurl = isset($matches[1]) ? $matches[1] : '';
					}

					if (!empty($imageurl)) {
						// $imgHtml = '<img title="'.$row->title.'" src="' . $imageurl . '" style="width: '. $maxWidth . 'px !important;height: '. $maxHeight . 'px !important;" />';
						$imgHtml = $imageurl;
						$row->media = $imgHtml;
					} else {
						$row->media	= $image;
					}

				}
			}

			if (!empty($row->media)) {
				$posts[] = $row;
			}
		}

		return $posts;
	}

	/**
	 * Retrieves the first image from the post
	 *
	 * @param	string $content This is the content of the blog.
	 * @return	mixed Image element on success, false otherwise.
	 */
	public static function getImage($content)
	{
		$pattern = '#<img[^>]*>#i';

		preg_match($pattern, $content, $matches);

		if (isset($matches[0])) {
			return $matches[0];
		}

		return false;
	}
}
