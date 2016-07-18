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

class EasyBlogThemesHelperSettings
{
	/**
	 * Renders a yes/no settings
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function toggle($name, $title, $desc = '', $attributes = '')
	{
		$theme 	= EB::getTemplate();

		if (empty($desc)) {
			$desc 	= $title . '_DESC';
		}
		
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('attributes', $attributes);

		$contents 	= $theme->output('admin/html/settings.toggle');

		return $contents;
	}

	/**
	 * Renders a small inputbox
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function smalltext($name, $title, $desc = '', $prefix = '')
	{
		$theme 	= EB::getTemplate();

		if (empty($desc)) {
			$desc 	= $title . '_DESC';
		}
		

		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('prefix', $prefix);

		$contents 	= $theme->output('admin/html/settings.text.small');

		return $contents;
	}

	/**
	 * Renders a small inputbox
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function text($name, $title, $desc = '', $prefix = '', $instructions = '', $class = '')
	{
		$theme = EB::getTemplate();
		
		if (empty($desc)) {
			$desc 	= $title . '_DESC';
		}
			
		$theme->set('class', $class);
		$theme->set('instructions', $instructions);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('prefix', $prefix);

		$contents 	= $theme->output('admin/html/settings.text');

		return $contents;
	}

	/**
	 * Renders a small inputbox
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function textarea($name, $title, $desc = '', $prefix = '', $instructions = '')
	{
		$theme 	= EB::getTemplate();

		if (empty($desc)) {
			$desc 	= $title . '_DESC';
		}
		
		$theme->set('instructions', $instructions);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('prefix', $prefix);

		$contents 	= $theme->output('admin/html/settings.textarea');

		return $contents;
	}

	/**
	 * Renders a small inputbox
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function categories($name, $title, $desc = '', $prefix = '', $instructions = '')
	{
		$theme 	= EB::getTemplate();

		if (empty($desc)) {
			$desc 	= $title . '_DESC';
		}
		
		$theme->set('instructions', $instructions);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('prefix', $prefix);

		$contents 	= $theme->output('admin/html/settings.categories');

		return $contents;
	}
}
