<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerInstallCopy extends EasyBlogSetupController
{
	/**
	 * Responsible to copy the necessary files over.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function execute()
	{
		// Get which type of data we should be copying
		$type = $this->input->get('type', '');

		// Get the temporary path from the server.
		$tmpPath = $this->input->get('path', '', 'default');

		// Get the path to the zip file
		$archivePath = $tmpPath . '/' . $type . '.zip';

		// Where the extracted items should reside
		$path = $tmpPath . '/' . $type;

		// For development mode, we want to skip all this
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('COM_EASYBLOG_INSTALLATION_DEVELOPER_MODE', true));
		}

		// Extract the admin folder
		$state = JArchive::extract($archivePath, $path);

		if (!$state) {
			$this->setInfo(JText::sprintf('COM_EASYBLOG_INSTALLATION_COPY_ERROR_UNABLE_EXTRACT', $type), false);
			return $this->output();
		}

		// Look for files in this path
		$files = JFolder::files( $path , '.' , false , true );

		// Look for folders in this path
		$folders = JFolder::folders( $path , '.' , false , true );

		// Construct the target path first.
		if ($type == 'admin') {
			$target = JPATH_ADMINISTRATOR . '/components/com_easyblog';
		}

		if ($type == 'site') {
			$target = JPATH_ROOT . '/components/com_easyblog';
		}

		// There could be instances where the user did not upload the launcher and just used the update feature.
		if ($type == 'languages') {

			// Copy the admin language file
			$adminFile = $path . '/admin/en-GB.com_easyblog.ini';
			JFile::copy($adminFile, JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_easyblog.ini');

			// Copy the admin system language file
			$adminFileSys = $path . '/admin/en-GB.com_easyblog.sys.ini';
			JFile::copy($adminFileSys, JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_easyblog.sys.ini');

			// Copy the site language file
			$siteFile = $path . '/site/en-GB.com_easyblog.ini';
			JFile::copy($siteFile, JPATH_ROOT . '/language/en-GB/en-GB.com_easyblog.ini');


			$this->setInfo('COM_EASYBLOG_INSTALLATION_LANGUAGES_UPDATED', true);
			return $this->output();
		}

		if ($type == 'media') {
			$target = JPATH_ROOT . '/media/com_easyblog';
		}

		if ($type == 'foundry') {

			// Should we be overwriting the foundry folder.
			$overwrite = false;

			// Check the current version of Foundry installed and determine if we should overwrite foundry.
			$foundryVersion = '5.0';
			$currentFoundryVersion = JPATH_ROOT . '/media/foundry/' . $foundryVersion . '/version';
			$exists = JFile::exists($currentFoundryVersion);

			// If it doesn't exists, foundry is new here
			if (!$exists) {
				$target = $this->makeFoundryFolders($foundryVersion);

				// Unable to create foundry folders on this level
				if ($target === false) {
					return $this->output($this->getResultObj(JText::_('There was an error creating the necessary foundry folders. Please try again'), false));
				}

				// Scan for files in the folder
				$totalFiles = 0;

				foreach ($files as $file) {
					$name = basename( $file );
					$targetFile	= $target . '/' . $name;

					JFile::copy($file, $targetFile);

					$totalFiles +=1;
				}

				// Scan for folders in this folder
				$totalFolders = 0;

				foreach ($folders as $folder) {

					$name = basename($folder);
					$targetFolder = $target . '/' . $name;

					// Try to copy the folder over
					JFolder::copy($folder, $targetFolder, '', true);

					$totalFolders 	+= 1;
				}

				$result = $this->getResultObj(JText::sprintf('COM_EASYBLOG_INSTALLATION_COPY_FILES_SUCCESS', $totalFiles, $totalFolders), true);
				return $this->output($result);
			}


			// Updating existing files

			// If foundry exists, do a version compare and see if we should overwrite.
			$target = JPATH_ROOT . '/media/foundry/' . $foundryVersion;

			// Get the current foundry version
			$currentFoundryVersion = JFile::read($currentFoundryVersion);

			// Get the incoming version
			$incomingFoundryVersion = JFile::read($path . '/version');

			// Determines if the installed version is later
			$requiresUpdating = version_compare($currentFoundryVersion, $incomingFoundryVersion);

			if ($requiresUpdating <= 0) {

				JFolder::copy($path, $target, '', true);

				$result = $this->getResultObj(JText::sprintf('COM_EASYBLOG_INSTALLATION_COPY_OVERWRITE_FOUNDRY_FILES_SUCCESS', $incomingFoundryVersion), true);
				return $this->output($result);
			}

			// Otherwise, there's nothing to do here.
			$result = $this->getResultObj(JText::sprintf('COM_EASYBLOG_INSTALLATION_FOUNDRY_NO_CHANGES', $incomingFoundryVersion), true);
			return $this->output();
		}

		// Ensure that the target folder exists
		if (!JFolder::exists($target)) {
			JFolder::create($target);
		}

		// Scan for files in the folder
		$totalFiles = 0;
		$totalFolders = 0;

		foreach ($files as $file) {
			$name = basename($file);

			$targetFile = $target . '/' . $name;

			// Copy the file
			JFile::copy($file, $targetFile);

			$totalFiles++;
		}


		// Scan for folders in this folder
		foreach ($folders as $folder) {
			$name = basename($folder);
			$targetFolder = $target . '/' . $name;

			// Copy the folder across
			JFolder::copy($folder, $targetFolder, '', true);

			$totalFolders++;
		}


		$result = $this->getResultObj(JText::sprintf('COM_EASYBLOG_INSTALLATION_COPY_FILES_SUCCESS', $totalFiles, $totalFolders), true);

		return $this->output($result);
	}

	/**
	 * Create foundry folders given the current version
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function makeFoundryFolders($version)
	{
		$version = explode('.', $version);
		$majorVersion = $version[0] . '.' . $version[1];

		$path = JPATH_ROOT . '/media/foundry/' . $majorVersion;
		$state = true;

		if (JFolder::exists($path)) {
			return $path;
		}

		// Try to create the folder
		$state = JFolder::create($path);

		if ($state) {
			return $path;
		}
		
		return $state;
	}
}
