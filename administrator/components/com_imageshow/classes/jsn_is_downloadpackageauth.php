<?php
/**
 * @version    $Id: jsn_is_downloadpackageauth.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');
class JSNISDownloadPackageAuth
{
	var $_tmpPackageName 	= '';
	var $_downloadURL		= '';
	var $_tmpFolder			= '';
	var $_tmpPackagePath	= '';
	var $_msgError 			= '';
	var $_getFields			= '';

	public function JSNISDownloadPackageAuth()
	{
		$this->_tmpFolder 	= JPATH_ROOT . DS . 'tmp' . DS;
		$this->_downloadURL = JSN_IMAGESHOW_AUTOUPDATE_URL;
		$this->_jarchiveZip = JArchive::getAdapter('zip');
	}

	public function setOptions($options = null)
	{
		if ($options)
		{
			$this->_identifyName 	= $options->identifyName;
			$this->_edition		 	= $options->edition;
			$this->_joomlaVersion 	= $options->joomlaVersion;
			$this->_tmpPackageName	= 'jsn_tmp_package_' . $options->identifyName . '.zip';
			$this->_tmpPackagePath 	= $this->_tmpFolder . $this->_tmpPackageName;
			$this->_getFields 		.= '&identified_name=' . urlencode($this->_identifyName) . '&edition=' . urlencode($this->_edition) . '&joomla_version=' . urlencode($this->_joomlaVersion);

			if (isset($options->username) && $options->username != '')
			{
				$this->_getFields .= '&username='.urlencode($options->username);
			}

			if (isset($options->password) && $options->password != '')
			{
				$this->_getFields .= '&password=' . urlencode($options->password);
			}
		}
	}

	public function download($options)
	{
		$this->setOptions($options);
		$url = $this->_downloadURL . $this->_getFields;

		try
		{
			$content = JSNUtilsHttp::get($url);
			if ($content)
			{
				if (!$this->_jarchiveZip->checkZipData($content['body']))
				{
					$this->_msgError = (string) $content['body'];
					if (JFile::exists($this->_tmpPackagePath))
					{
						JFile::delete($this->_tmpPackagePath);
						return false;
					}
				}
				else
				{
					JFile::write($this->_tmpPackagePath, $content['body']);
					return basename($this->_tmpPackagePath);
				}

			}

			return false;
		}
		catch(Exception $e)
		{
			return false;
		}
	}
}