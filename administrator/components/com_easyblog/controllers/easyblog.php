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

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerEasyBlog extends EasyBlogController
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Purges the cache from the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function purgeCache()
	{
		$compiler = EB::compiler();

		// Purge resources
		$compiler->purgeResources();

		$this->info->set(JText::_('COM_EASYBLOG_CACHE_PURGED_FROM_SITE'), 'success');
		return $this->app->redirect('index.php?option=com_easyblog');
	}
}
