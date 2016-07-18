<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_installshowcasetheme.php 11579 2012-03-07 04:21:07Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.filesystem.file');
jimport('joomla.installer.helper');
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'jsn_is_installpackage.php');
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'jsn_is_upgradethemedb.php');
class JSNISInstallShowcaseTheme extends JSNISInstallPackage {
	var $_listCurrentThemes = array();

	function __construct()
	{
		$this->_getListThemes();
		parent::__construct();
	}

	function _installPackage()
	{
		$installer 	  = JSNISInstaller::getInstance();
		$objJSNPlugin = JSNISFactory::getObj('classes.jsn_is_plugins');

		if ($this->_packageExtract)
		{
			$objJSNPlugins 	= JSNISFactory::getObj('classes.jsn_is_plugins');
			$countTheme   	= count($this->_listCurrentThemes);
			$currentThemes  	= array();

			if ($countTheme)
			{
				for ($i = 0; $i < $countTheme; $i++)
				{
					$item = $this->_listCurrentThemes[$i];
					$currentThemes [$item->element] = $objJSNPlugins->getXmlFile($item);
				}
			}

			if (!$installer->install($this->_packageExtract['dir']))
			{
				$this->_msgError = JText::_('SHOWCASE_INSTALL_THEME_PACKAGE_UNSUCCESSFULLY_INSTALLED');
				$this->_error	 = true;
				return false;
			}

			$this->_upgradeThemeDB($currentThemes, $installer);

			if (!is_file($this->_packageExtract['packagefile']))
			{
				$config = JFactory::getConfig();
				$package['packagefile'] = $config->getValue('config.tmp_path').DS.$this->_packageExtract['packagefile'];
			}

			JInstallerHelper::cleanupInstall($this->_packageExtract['packagefile'], $this->_packageExtract['extractdir']);
		}
	}

	function _getListThemes()
	{

		$db = JFactory::getDBO();
		$query = 'SELECT * FROM #__extensions WHERE folder = \'jsnimageshow\' AND LOWER(element) LIKE \'theme%\'';
		$db->setQuery($query);
		$listThemes = $db->loadObjectList();

		return $this->_listCurrentThemes = (count($listThemes) > 0) ? $listThemes : array();
	}

	function _upgradeThemeDB($currentThemes, $installer)
	{
		$manifest   = $installer->getManifest();
		$name   	= trim($manifest->name);
		$name		= strtolower($name);
		$name		= str_replace(' ', '', $name);
		$folder		= $name;

		if (isset($currentThemes[$folder]))
		{
			$objUpgradeThemeDB = new JSNISUpgradeThemeDB($manifest, $currentThemes[$folder]);
			$objUpgradeThemeDB->executeUpgradeDB();
		}
	}
}
