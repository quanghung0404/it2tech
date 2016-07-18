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

// Include parent library
require_once( dirname( __FILE__ ) . '/controller.php' );

class EasyBlogControllerMaintenanceFinalize extends EasyBlogSetupController
{
	public function execute()
	{
		$this->engine();

		$version = $this->getInstalledVersion();

		// Update the version in the database to the latest now
		$config = EB::table('Configs');
		$config->load(array('name' => 'scriptversion'));

		$config->name = 'scriptversion';
		$config->params = $version;

		// Save the new config
		$config->store($config->name);

		// Remove any folders in the temporary folder.
		$this->cleanup(EB_TMP);

		// Remove helpers folder and constants.php if this is an upgrade from 3.x to 5.x
		$this->removeFrontendUnusedFolders();
		$this->removeConstantsFile();

		// Remove installation temporary file
		JFile::delete(JPATH_ROOT . '/tmp/easyblog.installation');

		$result = $this->getResultObj(JText::sprintf('COM_EASYBLOG_INSTALLATION_MAINTENANCE_UPDATED_MAINTENANCE_VERSION', $version), 1, 'success');

		return $this->output($result);
	}

	/**
	 * Perform system wide cleanups after the installation is completed.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function cleanup($path)
	{
		$folders = JFolder::folders($path, '.', false, true);
		$files = JFolder::files($path, '.', false, true);

		if ($folders) {
			foreach ($folders as $folder) {
				JFolder::delete($folder);
			}
		}

		if ($files) {
			foreach ($files as $file) {
				JFile::delete($file);
			}
		}

		// Cleanup javascript files
		$this->removeOldJavascripts();
	}

	/**
	 * Remove all old javascript files
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function removeOldJavascripts()
	{
		// Get the current installed version
		$version = $this->getInstalledVersion();

		// Ignored files
		$ignored = array('.svn', 'CVS', '.DS_Store', '__MACOSX');

		$types = array('admin', 'composer', 'dashboard', 'module', 'site');

		foreach ($types as $type) {
			$ignored[] = $type . '-' . $version . '.static.min.js';
			$ignored[] = $type . '-' . $version . '.static.js';
			$ignored[] = $type . '-' . $version . '.optimized.min.js';
			$ignored[] = $type . '-' . $version . '.optimized.js';

			$files = JFolder::files(JPATH_ROOT . '/media/com_easyblog/scripts', $type . '-', false, true, $ignored);

			if ($files) {
				foreach ($files as $file) {
					JFile::delete($file);
				}
			}
		}
	}

	public function removeConstantsFile()
	{
		// old constants.php location.
		$file = JPATH_ROOT . '/components/com_easyblog/constants.php';

		if (JFile::exists($file)) {
			JFile::delete($file);
		}
	}

	public function removeFrontendUnusedFolders()
	{
		// models
		$path = JPATH_ROOT . '/components/com_easyblog/models';
		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}

		// classes
		$path = JPATH_ROOT . '/components/com_easyblog/classes';
		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}
	}
}
