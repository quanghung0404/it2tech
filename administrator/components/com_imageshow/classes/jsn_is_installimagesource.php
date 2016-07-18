<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_installimagesource.php 11375 2012-02-24 10:19:14Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.filesystem.file');
jimport('joomla.installer.helper');
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'jsn_is_installpackage.php');
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'jsn_is_upgradeimagesourcedb.php');
class JSNISInstallImageSource extends JSNISInstallPackage{
	var $_listCurrentSources = array();

	function __construct()
	{
		$this->_getListSources();
		parent::__construct();
	}

	function _installPackage()
	{
		$installer = JSNISInstaller::getInstance();

		if ($this->_packageExtract)
		{
			$objJSNPlugins 	= JSNISFactory::getObj('classes.jsn_is_plugins');
			$countSource   	= count($this->_listCurrentSources);
			$currentSources = array();

			if ($countSource)
			{
				for ($i = 0; $i < $countSource; $i++)
				{
					$item = $this->_listCurrentSources[$i];
					$currentSources [$item->element] = $objJSNPlugins->getXmlFile($item);
				}
			}

			if (!$installer->install($this->_packageExtract['dir']))
			{
				$this->_msgError = JText::_('INSTALLER_IMAGE_SOURCE_PACKAGE_UNSUCCESSFULLY_INSTALLED');
				$this->_error	 = true;
				return false;
			}

			$this->_upgradeSourceDB($currentSources, $installer);

			if (!is_file($this->_packageExtract['packagefile']))
			{
				$config = JFactory::getConfig();
				$package['packagefile'] = $config->getValue('config.tmp_path').DS.$this->_packageExtract['packagefile'];
			}

			JInstallerHelper::cleanupInstall($this->_packageExtract['packagefile'], $this->_packageExtract['extractdir']);
		}
	}

	function _getListSources()
	{
		$db = JFactory::getDBO();
		$query = 'SELECT * FROM #__extensions WHERE folder = \'jsnimageshow\' AND LOWER(element) LIKE \'source%\'';
		$db->setQuery($query);
		$listSources = $db->loadObjectList();
		return $this->_listCurrentSources = (count($listSources) > 0) ? $listSources : array();
	}

	function _upgradeSourceDB($currentSources, $installer)
	{
		$manifest   = $installer->getManifest();
		$name   	= trim($manifest->name);
		$name		= strtolower($name);
		$name		= str_replace(' ', '', $name);
		$folder		= $name;

		if (isset($imageSources[$folder]))
		{
			$objUpgradeImageSourceDB = new JSNISUpgradeImageSourceDB($manifest, $imageSources[$folder]);
			$objUpgradeImageSourceDB->executeUpgradeDB();
		}
	}
}
