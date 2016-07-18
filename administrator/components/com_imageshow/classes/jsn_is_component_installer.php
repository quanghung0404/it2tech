<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_component_installer.php 8411 2011-09-22 04:45:10Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.installer.installer');
class JSNISComponentInstaller extends JInstaller
{
	function __construct()
	{
		parent::__construct();
	}

	public static function getInstance()
	{
		static $instance;

		if (!isset($instance))
		{
			$instance = new JSNISComponentInstaller();
		}
		return $instance;
	}

	public function checkPackage($path = null)
	{
		if ($path && JFolder::exists($path))
		{
			$this->setPath('source', $path);
		}
		else
		{
			$this->abort(JText::_('INSTALLER_INSTALL_PATH_DOES_NOT_EXIST'));
			return false;
		}

		if (!$this->findManifest())
		{
			return false;
		}

		$root 	= $this->manifest;
		$type 	= (string) $root->attributes()->type;
		$name 	= (string) $this->manifest->name;
		$name   = trim(strtolower($name));

		if ($type != 'component' || $name != 'imageshow')
		{
			$this->abort(JText::_('INSTALLER_VALIDATE_INSTALL_CORE_PACKAGE'));
			return false;
		}
		return true;
	}
}