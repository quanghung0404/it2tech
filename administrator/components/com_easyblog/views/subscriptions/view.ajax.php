<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewSubscriptions extends EasyBlogAdminView
{
	/**
	 * Allows admin to create a new manual subscriber on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function form()
	{
		// Check for access
		$this->checkAccess('easyblog.manage.subscription');

		$ajax = EB::ajax();

		// Get the type
		$type = $this->input->get('type', '', 'word');

		$theme 	= EB::template();
		$theme->set('type', $type);
		$output = $theme->output('admin/subscriptions/dialog.form');

		return $ajax->resolve($output);
	}
}
