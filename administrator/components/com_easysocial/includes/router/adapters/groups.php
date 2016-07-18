<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialRouterGroups extends SocialRouterAdapter
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
		$segments 	= array();

		// If there is a menu but not pointing to the profile view, we need to set a view
		if($menu && $menu->query['view'] != 'groups') {
			$segments[]	= $this->translate($query['view']);
		}

		// If there's no menu, use the view provided
		if (!$menu) {
			$segments[]	= $this->translate($query['view']);
		}

		unset($query['view']);

		// Translate category urls
		$category 	= isset($query['categoryid']) ? $query['categoryid'] : null;

		if (!is_null($category)) {
			$segments[]	= $query['categoryid'];
			unset($query['categoryid']);
		}

		// Translate layout
		$layout = isset($query['layout']) ? $query['layout'] : null;

		if (!is_null($layout)) {
			$segments[]	= $this->translate('groups_layout_' . $layout);
			unset($query['layout']);
		}

		// Translate step
		$step = isset($query['step']) ? $query['step'] : null;

		if (!is_null($step)) {
			$segments[]	= $step;
			unset($query['step']);
		}

		// Translate id
		$id = isset($query['id']) ? $query['id'] : null;

		if (!is_null($id)) {
			$segments[]	= $id;
			unset($query['id']);
		}

		// Translate app id
		$appId = isset($query['appId']) ? $query['appId'] : null;

		if (!is_null($appId)) {
			$segments[]	= $appId;
			unset($query['appId']);
		}

		// If there is no type defined but there is a "app" defined and default display is NOT timeline, then we have to punch in timeline manually
		if (isset($query['app']) && !isset($query['type']) && FD::config()->get('events.item.display', 'timeline') !== 'timeline') {
		    $segments[] = $this->translate('groups_type_timeline');
		}

		// If there is no type defined but there is a "filterId" defined and default display is NOT timeline, then we have to punch in timeline manually
		if (isset($query['filterId']) && !isset($query['type']) && FD::config()->get('events.item.display', 'timeline') !== 'timeline') {
		    $segments[] = $this->translate('groups_type_timeline');
		}

		// Special handling for timeline and about
		if(isset($query['type'])) {
			$defaultDisplay = FD::config()->get('events.item.display', 'timeline');

			// If type is info and there is a step provided, then info has to be added regardless of settings
			if ($query['type'] === 'info' && ($defaultDisplay !== $query['type'] || isset($query['infostep']))) {
				$segments[] = $this->translate('groups_type_info');

				if (isset($query['infostep'])) {
					$segments[] = $query['infostep'];
					unset($query['infostep']);
				}
			}

			// Depending settings, if default is set to timeline and type is timeline, we don't need to add this into the segments
			if ($query['type'] === 'timeline' && $defaultDisplay !== $query['type']) {
				$segments[] = $this->translate('groups_type_timeline');
			}

			if ($query['type'] === 'filterForm') {
				$segments[] = $this->translate('groups_type_filterform');

				if (isset($query['filterId'])) {
					$segments[] = $query['filterId'];
					unset($query['filterId']);
				}
			}

			unset($query['type']);
		}

		// Translate filter urls
		$filter = isset($query['filter']) ? $query['filter'] : null;
		$menuFilter = isset($menu->query['filter']) ? $menu->query['filter'] : null;
		$addFilter = false;

		if (is_null($menuFilter) && !is_null($filter)) {
			$addFilter = true;
		}
	
		if (!is_null($filter) && $filter != $menuFilter) {
			$addFilter = true;
		}

		if ($addFilter) {
			$segments[]	= $this->translate('groups_filter_' . $query['filter']);
		}

		unset($query['filter']);

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

		// Default groups view
		$vars['view'] = 'groups';

		// URL: http://site.com/menus/points
		if ($total == 1) {
			return $vars;
		}

		// http://site.com/menu/groups/create
		if ($total == 2 && $segments[1] == $this->translate('groups_layout_create')) {
			$vars['layout']	= 'create';
		}

		// http://site.com/menu/groups/filter
		// http://site.com/menu/groups/ID-GROUPCATEGORY
		if ($total == 2) {
			$filters = array(
				$this->translate('groups_filter_all'),
				$this->translate('groups_filter_recent'),
				$this->translate('groups_filter_featured'),
				$this->translate('groups_filter_mine'),
				$this->translate('groups_filter_invited')
			);

			if (in_array($segments[1], $filters)) {
				$vars['filter'] = $this->getFilter($segments[1]);
			} else {
				$catId = (int) $this->getIdFromPermalink($segments[1]);
				if ($catId) {
					$vars['categoryid']	= $catId;
				} else {
					$vars['filter'] = $segments[1];
				}
			}
		}

		// http://site.com/menu/groups/category/ID-category
		if ($total == 3 && $segments[1] == $this->translate('groups_layout_category')) {
			$vars['layout']	= 'category';
			$vars['id'] = $this->getIdFromPermalink($segments[2]);
			return $vars;
		}

		// http://site.com/menu/groups/info/ID-category
		if ($total == 3 && $segments[1] == $this->translate('groups_layout_info')) {
			$vars['layout']	= 'info';
			$vars['id']		= $this->getIdFromPermalink($segments[2]);
			return $vars;
		}

		// http://site.com/menu/groups/item/ID-alias
		if ($total == 3 && $segments[1] == $this->translate('groups_layout_item')) {
			$vars['layout']	= 'item';
			$vars['id'] = $this->getIdFromPermalink($segments[2]);

			return $vars;
		}

		// http://site.com/menu/groups/edit/ID-alias
		if ($total == 3 && $segments[1] == $this->translate('groups_layout_edit')) {
			$vars['layout']	= 'edit';
			$vars['id'] = $this->getIdFromPermalink($segments[2]);

			return $vars;
		}

		// http://site.com/menu/groups/create
		if ($total == 3 && $segments[1] == $this->translate('groups_layout_steps')) {
			$vars['layout']	= 'steps';
			$vars['step'] = $segments[2];
		}

		// http://site.com/menu/groups/filter/ID-category
		if ($total == 3) {
			$vars['categoryid']	= $segments[2];
			$vars['filter'] = $this->getFilter($segments[1]);
		}

		$typeException = array($this->translate('groups_type_info'), $this->translate('groups_type_timeline'), $this->translate('groups_type_filterform'));

		// Specifically check for both info and timeline. If 4th segment is not info nor timeline, then we assume it is app
		if ($total === 4 && $segments[1] === $this->translate('groups_layout_item') && !in_array($segments[3], $typeException)) {
			$vars['layout'] = 'item';
			$vars['id'] = $this->getIdFromPermalink($segments[2]);
			$appId = $this->getIdFromPermalink($segments[3]);

			// $vars['type'] = $appId;
			$vars[(int) $appId ? 'appId' : 'app'] = $appId;
		}

		if (($total === 4 || $total === 5) && $segments[1] === $this->translate('groups_layout_item') && in_array($segments[3], $typeException)) {

			$vars['layout'] = 'item';
			$vars['id'] = $this->getIdFromPermalink($segments[2]);

			if ($segments[3] === $this->translate('groups_type_info')) {
				$vars['type'] = 'info';

				if (!empty($segments[4])) {
					$vars['step'] = $segments[4];
				}
			}

			if ($segments[3] === $this->translate('groups_type_timeline')) {
				$vars['type'] = 'timeline';
			}

			if ($segments[3] === $this->translate('groups_type_filterform')) {
				$vars['type'] = 'filterForm';

				if (!empty($segments[4])) {
					$vars['filterId'] = $segments[4];
				}
			}
		}

		// Group apps for members
		if ($total == 5 && $segments[1] == $this->translate('groups_layout_item')) {
			$vars['layout'] = 'item';
			$vars['id'] = $this->getIdFromPermalink($segments[2]);
			$vars['filter'] = $segments[4];
			$vars['appId'] = $segments[3];
		}

		return $vars;
	}

	/**
	 * Retrieves the correct url that the current request should use.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUrl($query, $url)
	{
		static $cache	= array();

		// Get a list of menus for the current view.
		$itemMenus	= FRoute::getMenus($this->name, 'item');

		// For single group item
		// index.php?option=com_easysocial&view=groups&layout=item&id=xxxx
		$items 	= array('item', 'info', 'edit');

		if (isset($query['layout']) && in_array($query['layout'], $items) && isset($query['id']) && !empty($itemMenus)) {

			foreach($itemMenus as $menu) {
				$id 		= (int) $menu->segments->id;
				$queryId	= (int) $query['id'];

				if ($queryId == $id) {

					// The query cannot contain appId
					if ($query['layout'] == 'item' && !isset($query['appId'])) {
						$url 	= 'index.php?Itemid=' . $menu->id;
						return $url;
					}


					$url 	.= '&Itemid=' . $menu->id;
					return $url;
				}
			}
		}

		// For group categories
		$menus 	= FRoute::getMenus($this->name, 'category');
		$items 	= array('category');

		if (isset($query['layout']) && in_array($query['layout'], $items) && isset($query['id']) && !empty($itemMenus)) {

			foreach ($menus as $menu) {
				$id 		= (int) $menu->segments->id;
				$queryId	= (int) $query['id'];

				if ($queryId == $id) {
					if ($query['layout'] == 'category') {
						$url 	= 'index.php?Itemid=' . $menu->id;

						return $url;
					}

					$url 	.= '&Itemid=' . $menu->id;

					return $url;
				}

			}
		}

		return false;
	}

	/**
	 * Retrieve the filter
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The translated string
	 * @return	string	The actual filter title
	 */
	private function getFilter($translated)
	{
		if($translated == $this->translate('groups_filter_featured'))
		{
			return 'featured';
		}

		if($translated == $this->translate('groups_filter_recent'))
		{
			return 'recent';
		}

		if($translated == $this->translate('groups_filter_mine'))
		{
			return 'mine';
		}

		if($translated == $this->translate('groups_filter_invited'))
		{
			return 'invited';
		}

		// Default to return all
		return 'all';
	}
}
