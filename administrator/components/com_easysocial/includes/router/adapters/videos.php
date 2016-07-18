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

class SocialRouterVideos extends SocialRouterAdapter
{
	/**
	 * Constructs the points urls
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function build(&$menu, &$query)
	{
		$segments = array();

		// If there is a menu but not pointing to the profile view, we need to set a view
		if ($menu && $menu->query['view'] != 'videos') {
			$segments[]	= $this->translate($query['view']);
		}

		// If there's no menu, use the view provided
		if (!$menu) {
			$segments[]	= $this->translate($query['view']);
		}

		// Get available variables
		$layout = isset($query['layout']) ? $query['layout'] : '';

		// Linkage to clusters
		if (isset($query['uid']) && isset($query['type'])) {
			$segments[] = $query['type'];
			$segments[] = $query['uid'];

			unset($query['uid']);
			unset($query['type']);
		}

		// Video id
		if (isset($query['id'])) {
			$segments[] = $query['id'];

			unset($query['id']);
		}

		// Layout
		if (isset($query['layout'])) {
			$segments[] = $this->translate('videos_layout_' . $layout);
			unset($query['layout']);
		}

		// Filtering by category
		if (isset($query['categoryId'])) {
			$segments[] = $query['categoryId'];

			unset($query['categoryId']);
		}

		// Filtering on videos listing
		if (isset($query['filter'])) {
			$segments[] = $this->translate('videos_filter_' . $query['filter']);

			unset($query['filter']);
		}
		unset($query['view']);

		return $segments;
	}

	/**
	 * Translates the SEF url to the appropriate url
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	An array of url segments
	 * @return	array 	The query string data
	 */
	public function parse(&$segments)
	{
		$vars = array();
		$total = count($segments);

		// By default this view is going to be videos
		$vars['view'] = 'videos';

		$filters = array($this->translate('videos_filter_featured'), $this->translate('videos_filter_mine'), $this->translate('videos_filter_pending'));
		$layouts = array($this->translate('videos_layout_form'), $this->translate('videos_layout_item'));

		// videos/id/process
		if (count($segments) == 3 && $segments[2] == $this->translate('videos_layout_process')) {
			$vars['id'] = $this->getIdFromPermalink($segments[1]);
			$vars['layout'] = 'process';

			return $vars;
		}

		// videos/[type]/[uid]/id/process
		if (count($segments) == 5 && $segments[4] == $this->translate('videos_layout_process')) {
			$vars['type'] = $segments[1];
			$vars['uid'] = $segments[2];
			$vars['layout'] = 'process';
			$vars['id'] = $this->getIdFromPermalink($segments[3]);

			return $vars;
		}

		// videos/id/item
		if (count($segments) == 3 && $segments[2] == $this->translate('videos_layout_item')) {
			$vars['layout']	= 'item';
			$vars['id'] = $this->getIdFromPermalink($segments[1]);

			return $vars;
		}

		// videos/mine
		if (count($segments) == 2 && $segments[1] == $this->translate('videos_filter_mine')) {
			$vars['filter'] = 'mine';
			return $vars;
		}

		// videos/pending
		if (count($segments) == 2 && $segments[1] == $this->translate('videos_filter_pending')) {
			$vars['filter'] = 'pending';
			return $vars;
		}

		// videos/form
		if (count($segments) == 2 && $segments[1] == $this->translate('videos_layout_form')) {
			$vars['layout'] = 'form';

			return $vars;
		}

		// videos/id-category
		if (count($segments) == 2 && !in_array($segments[1], $filters) && !in_array($segments[1], $layouts)) {
			
			$vars['categoryId'] = $segments[1];

			return $vars;
		}

		// videos/[type]/[uid]/[id]/form
		if (count($segments) == 5 && $segments[4] == $this->translate('videos_layout_form')) {
			$vars['type'] = $segments[1];
			$vars['uid'] = $segments[2];
			$vars['id'] = $segments[3];
			$vars['layout'] = 'form';
			return $vars;			
		}

		// videos/[type]/[uid]/[id]/[item]
		if (count($segments) == 5 && $segments[4] == $this->translate('videos_layout_item')) {
			$vars['type'] = $segments[1];
			$vars['uid'] = $segments[2];
			$vars['id'] = $segments[3];
			$vars['layout'] = 'item';
			return $vars;			
		}

		// videos/[type]/[uid]/form
		if (count($segments) == 4 && $segments[3] == $this->translate('videos_layout_form')) {
			$vars['type'] = $segments[1];
			$vars['uid'] = $segments[2];
			$vars['layout'] = 'form';
			return $vars;
		}

		// videos/[type]/[uid]/[filter]
		if (count($segments) == 4 && in_array($segments[3], $filters)) {
			$vars['type'] = $segments[1];
			$vars['uid'] = $segments[2];
			$vars['filter'] = $segments[3];
		}

		// videos/[type]/[uid]/[categoryId]
		if (count($segments) == 4 && !in_array($segments[3], $filters)) {
			$vars['type'] = $segments[1];
			$vars['uid'] = $segments[2];
			$vars['categoryId'] = $segments[3];
		}

		// videos/[type]/[uid]
		if (count($segments) == 3) {
			$vars['type'] = $segments[1];

			if ($vars['type'] == 'user') {
				$vars['uid'] = $this->getUserId($segments[2]);
			} else {
				$vars['uid'] = $this->getIdFromPermalink($segments[2]);
			}
		}

		return $vars;
	}
}
