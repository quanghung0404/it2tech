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

class EasyBlogThemesHelperFilter
{
	/**
	 * Renders the user's group tree
	 *
	 * @since	4.0
	 * @access	public
	 * @param
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function published($name = 'state', $selected = 'all' )
	{
		$theme = EB::template();

		$theme->set('name', $name);
		$theme->set('selected', $selected);

		$contents = $theme->output('admin/html/filter.published');

		return $contents;
	}

	/**
	 * Displays a search box in the filter
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function search( $value = '' , $name = 'search' )
	{
		$theme = EB::template();

		$theme->set('value', $value);
		$theme->set('name', $name);

		$contents = $theme->output('admin/html/filter.search');

		return $contents;
	}

	public static function lists($items = array(), $name = 'listitem', $selected = 'all', $initial = '', $initialValue = 'all')
	{
		$theme = EB::template();

		$theme->set('initialValue', $initialValue);
		$theme->set('initial', $initial);
		$theme->set('name', $name);
		$theme->set('items', $items);
		$theme->set('selected', $selected);

		$contents = $theme->output('admin/html/filter.lists');

		return $contents;
	}
}
