<?php
/**
 * @version    $Id: update.php 16577 2012-10-01 10:27:30Z haonv $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class ImageShowModelupdate extends JSNUpdateModel
{
	public function __construct()
	{
		parent::__construct();
	}

	/*protected function downloadPackage()
	{
		// Get Joomla config
		$config = JFactory::getConfig();

		// Initialize variable
		$input	= JFactory::getApplication()->input;
		$jVer	= new JVersion;

		// Get the product info
		$info		= JSNUtilsXml::loadManifestCache();
		$edition	= JSNUtilsText::getConstant('EDITION');
		$identified	= ($identified	= JSNUtilsText::getConstant('IDENTIFIED_NAME')) ? $identified : strtolower($info->name);

		// Build query string
		$query[] = 'joomla_version=' . $jVer->RELEASE;
		$query[] = 'username=' . $input->getUsername('customer_username');
		$query[] = 'password=' . $input->getString('customer_password');
		$query[] = 'identified_name=' . ($input->getCmd('id') ? $input->getCmd('id') : $identified);
		$query[] = 'edition=' . strtolower(str_replace(' ', '+', $input->getVar('edition') ? $input->getVar('edition') : $edition));

		// Build final URL for downloading update
		$url = JSN_EXT_DOWNLOAD_UPDATE_URL . '&' . implode('&', $query);

		// Generate file name for update package
		$name[] = 'jsn';
		$name[] = $input->getCmd('id') ? $input->getCmd('id') : $identified;

		if ($edition)
		{
			$name[]	= strtolower(str_replace(' ', '_', $input->getVar('edition') ? $input->getVar('edition') : $edition));
		}

		$name[] = 'j' . $jVer->RELEASE;
		$name[] = 'install.zip';
		$name   = implode('_', $name);

		// Set maximum execution time
		ini_set('max_execution_time', 300);

		// Try to download the update package
		try
		{
			$path = $config->get('tmp_path') . DS . $name;

			if ( ! JSNUtilsHttp::get($url, $path, true))
			{
				throw new Exception(JText::_('JSN_EXTFW_UPDATE_DOWNLOAD_PACKAGE_FAIL'));
			}
		}
		catch (Exception $e)
		{
			throw new Exception(JText::_('JSN_EXTFW_UPDATE_DOWNLOAD_PACKAGE_FAIL'));
		}

		// Validate downloaded update package
		if (filesize($path) < 10)
		{
			// Get LightCart error code
			$errorCode = JFile::read($path);

			throw new Exception(JText::_('JSN_EXTFW_LIGHTCART_ERROR_' . $errorCode));
		}

		return $path;
	}*/
}