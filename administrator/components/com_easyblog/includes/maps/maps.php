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

class EasyBlogMaps extends EasyBlog
{
	/**
	 * Renders the maps for a blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function html(EasyBlogPost &$post)
	{
		static $loaded = false;

		if (!$post->hasLocation()) {
			return;
		}

		$language = $this->config->get('main_locations_blog_language');

		if (!$loaded) {
			$this->doc->addScript('https://maps.googleapis.com/maps/api/js?sensor=true&language=' . $language);
		}

		// Get the map configuration
		$static = $this->config->get('main_locations_static_maps');
		$type = $this->config->get('main_locations_map_type');
		$maxZoom = $this->config->get('main_locations_max_zoom_level');
		$minZoom = $this->config->get('main_locations_min_zoom_level');
		$defaultZoom = $this->config->get('main_locations_default_zoom_level', '17');

		// Generate a unique id
		$uid = uniqid();

		$template = EB::template();
		$template->set('uid', $uid);
		$template->set('defaultZoom', $defaultZoom);
		$template->set('minZoom', $minZoom);
		$template->set('maxZoom', $maxZoom);
		$template->set('defaultZoom', $defaultZoom);
		$template->set('type', $type);
		$template->set('language', $language);
		$template->set('post', $post);

		$namespace = 'site/maps/static';

		if (!$this->config->get('main_locations_static_maps')) {
			$namespace = 'site/maps/interactive';
		}

		$output = $template->output($namespace);

		return $output;
	}
}
