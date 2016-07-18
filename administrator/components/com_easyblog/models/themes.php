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

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelThemes extends EasyBlogAdminModel
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Retrieves a list of installed themes on the site
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getThemes()
	{
		$path = EBLOG_THEMES;

		$result	= JFolder::folders( $path , '.', false , true , $exclude = array('.svn', 'CVS' , '.' , '.DS_Store' ) );
		$themes	= array();

		// Cleanup output
		foreach ($result as $item) {
			$name = basename($item);

			if ($name != 'dashboard') {
				$obj = EB::getThemeObject($name);

				if ($obj) {
					$obj->default = false;

					if ($this->config->get('layout_theme') == $obj->element) {
						$obj->default = true;
					}

					$themes[]	= $obj;
				}
			}
		}

		return $themes;
	}

	/**
	 * Installs a new theme
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function install($file)
	{
		$source = $file['tmp_name'];
		$fileName = md5( $file[ 'name' ] . EB::date()->toMySQL());
		$fileExtension = '_themes_install.zip';
		$destination = JPATH_ROOT . '/tmp/' . $fileName . $fileExtension;

		// Upload the zip archive
		// argument in JFile::upload($src, $dest, $use_streams, $allow_unsafe, $safeFileOptions)
		// allow_unsafe = true. We need this to let files containing PHP code to upload. See JInputFiles::get.
		$state = JFile::upload($source, $destination, false, true);

		if (!$state) {
			$this->setError( JText::_( 'COM_EASYBLOG_THEMES_INSTALLER_ERROR_COPY_FROM_PHP' ) );

			return false;
		}

		// Extract the zip
		$extracted = dirname($destination) . '/' . $fileName . '_themes_install';
		$state = JArchive::extract($destination, $extracted);

		// Once it is extracted, delete the zip file
		JFile::delete($destination);

		// Get the configuration file.
		$manifest = $extracted . '/config/template.json';
		$manifest = JFile::read($manifest);

		// Get the theme object
		$theme = json_decode($manifest);

		// Move it to the appropriate folder
		$themeDestination 	= EBLOG_THEMES . '/' . strtolower($theme->element);
		$exists	= JFolder::exists($themeDestination);

		// If folder exists, overwrite it. For now, just throw an error.
		if ($exists) {
			// Delete teh etracted folder
			JFolder::delete($extracted);

			$this->setError( JText::sprintf('COM_EASYBLOG_THEMES_INSTALLER_ERROR_SAME_THEME_FOLDER_EXISTS', $theme->element));
			return false;
		}

		// Move extracted folder
		$state	= JFolder::move($extracted, $themeDestination);

		if (!$state) {
			// Delete the etracted folder
			JFolder::delete($extracted);

			$this->setError(JText::_('COM_EASYBLOG_THEMES_INSTALLER_ERROR_MOVING_FOLDER_TO_THEMES_FOLDER'));
			return false;
		}

		return true;
	}
}
