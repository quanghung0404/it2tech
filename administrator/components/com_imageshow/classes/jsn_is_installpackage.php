<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_installpackage.php 10073 2011-12-06 04:10:17Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.filesystem.file');
jimport('joomla.installer.helper');
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'jsn_is_installer.php');

class JSNISInstallPackage extends JObject {

	var $_tmpFolder;
	var $_packageExtracts;
	var $_methods;
	var $_error = false;
	var $_msgError = '';
	var $_config;

	function __construct()
	{
		$this->_config = JFactory::getConfig();
		$this->_tmpFolder = $this->_config->get('tmp_path');

		parent::__construct();
	}

	function install($packagePath)
	{
		$this->_packagePath = $packagePath;

		if (!JFile::exists($this->_packagePath)) {
			$this->_msgError = JText::_('INSTALLER_PACKAGE_NOT_EXISTS');
			$this->_error = true;
			return false;
		}

		$this->_unpackPackage();

		$this->_installPackage();

		return true;
	}

	function _unpackPackage()
	{
		$this->_packageExtract = JInstallerHelper::unpack($this->_packagePath);
	}

	function _installPackage()
	{
		return true;
	}
	/**
	 * check fille will be uploaded
	 * @return true/false
	 */
	function checkFileUpload()
	{
		if (!is_array($this->_fileInfo))
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('INSTALLER_NO_FILE_SELECTED'));
			return false;
		}

		if ($this->_fileInfo['error'] || $this->_fileInfo['size'] < 1)
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('WARNINSTALLUPLOADERROR'));
			return false;
		}

		return true;
	}

	/**
	 * upload fille
	 * @return file path or false on failure
	 */
	function uploadFile()
	{
		if ($this->checkFileUpload())
		{
			$tmpDEST 	= $this->_config->get('tmp_path').DS.$this->_fileInfo['name'];
			$tmpSRC		= $this->_fileInfo['tmp_name'];

			jimport('joomla.filesystem.file');

			if (JFile::upload($tmpSRC, $tmpDEST)) {
				return $this->_packagePath = $tmpDEST;
			} else {
				return false;
			}
		}

		return false;
	}

	/**
	 *
	 * install manual jns plugins
	 * @param $fileinfo get from Jrequest type = files
	 * @return true/false
	 */
	function installManual($fileInfo = null)
	{
		if (!is_array($fileInfo)) return false;

		$this->_fileInfo = $fileInfo;

		if ($this->uploadFile())
		{
			$this->_unpackPackage();

			$this->_installPackage();

			return true;
		}

		return false;
	}
}
