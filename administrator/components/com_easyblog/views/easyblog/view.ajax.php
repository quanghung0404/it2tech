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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewEasyblog extends EasyBlogAdminView
{
	/**
	 * Confirmation to purge cache on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function confirmPurgeCache()
	{
		$theme = EB::template();
		$contents = $theme->output('admin/easyblog/dialog.purge');


		return $this->ajax->resolve($contents);
	}

	/**
	 * Main method to display the dashboard view.
	 *
	 * @since	4.0
	 * @access	public
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function versionChecks()
	{	
		$localVersion = EB::getLocalVersion();

		// Get the online version from the server
		$onlineVersion = EB::getLatestVersion();

		$theme = EB::template();
		$theme->set('localVersion', $localVersion);
		$theme->set('onlineVersion', $onlineVersion);

		$contents = '';
		$state = version_compare($localVersion, $onlineVersion);

		$outdated = $state === -1;

		$file = $outdated ? 'version.outdated' : 'version.latest';

		// Version up to date
		$contents 	= $theme->output('admin/structure/' . $file);

		return $this->ajax->resolve($contents, $outdated, $localVersion, $onlineVersion);
	}
}
