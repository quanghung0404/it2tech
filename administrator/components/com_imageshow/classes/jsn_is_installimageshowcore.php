<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_installimageshowcore.php 10185 2011-12-12 08:20:11Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.filesystem.file');
jimport('joomla.installer.helper');
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'jsn_is_installer.php');
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'jsn_is_component_installer.php');
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'jsn_is_installpackage.php');
class JSNISInstallImageshowcore extends JSNISInstallPackage{

	var $_tmpFolder;
	var $_packagePath;
	var $_packageExtracts;
	var $_listCurrentSources = array();
	var $_methods;
	var $_error = false;
	var $_msgError = '';

	function onInstall($packagePath)
	{
		$this->_packagePath = $packagePath;

		if (!JFile::exists($this->_packagePath)) {
			$this->_msgError = JText::_('INSTALLER_CORE_PACKAGE_NOT_EXISTS');
			$this->_error = true;
			return false;
		}

		$this->_unpackPackage();

		$this->_checkPacakge();

		$this->_installPackage();

		return true;
	}

	function _checkPacakge()
	{
		$installer = JSNISComponentInstaller::getInstance();

		if (!$installer->checkPackage($this->_packageExtract['dir'])) {
			$this->_error 	 = true;
			$this->_msgError = JText::_('INSTALLER_INSTALL_PATH_DOES_NOT_EXIST');
			return false;
		}
	}

	function _unpackPackage()
	{
		$this->_packageExtract = JInstallerHelper::unpack($this->_packagePath);
	}

	function _installPackage()
	{
		if ($this->_packageExtract)
		{
			$jinstaller = JInstaller::getInstance();

			if(!$jinstaller->install($this->_packageExtract['dir'])) {
				$this->_msgError = JText::_('INSTALLER_PACKAGE_UNSUCCESSFULLY_INSTALLED');
				$this->_error 	 = true;
				return false;
			}

			if (!is_file($this->_packageExtract['packagefile']))
			{
				$config = JFactory::getConfig();
				$package['packagefile'] = $config->getValue('config.tmp_path').DS.$this->_packageExtract['packagefile'];
			}

			JInstallerHelper::cleanupInstall($this->_packageExtract['packagefile'], $this->_packageExtract['extractdir']);
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

			$this->_checkPacakge();

			$this->_installPackage();

			return true;
		}

		return false;
	}
}
