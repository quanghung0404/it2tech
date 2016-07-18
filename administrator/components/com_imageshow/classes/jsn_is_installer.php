<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_installer.php 13759 2012-07-04 04:31:41Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.installer.installer');
class JSNISInstaller extends JInstaller
{
	function __construct()
	{
		parent::__construct();
	}

	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
		{
			$instance = new JSNISInstaller();
		}
		return $instance;
	}

	function setAdapter($name, &$adapter = null, $options = Array())
	{
		if (!is_object($adapter))
		{
			require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'adapters'.DS.strtolower($name).'.php');
			$class = 'JSNISInstaller'.ucfirst($name);
			if (!class_exists($class))
			{
				return false;
			}

			$adapter = new $class($this, $this->_db, $options);
		}

		$this->_adapters[$name] = &$adapter;

		return true;
	}

	function setupInstall()
	{
		if (!$this->findManifest())
		{
			return false;
		}

		$root 	= $this->manifest;
		$type 	= (string) $root->attributes()->type;
		$group 	= (string) $root->attributes()->group;

		if($type != 'plugin' || $group != 'jsnimageshow')
		{
			$this->abort(JText::_('INSTALLER_VALIDATE_INSTALL_THEME_PACKAGE'));
			return false;
		}

		// Lazy load the adapter
		if (!isset($this->_adapters[$type]) || !is_object($this->_adapters[$type]))
		{
			if (!$this->setAdapter($type))
			{
				return false;
			}
		}
		return true;
	}

	function install($path=null)
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

		if (!$this->setupInstall())
		{
			return false;
		}

		$root 		= $this->manifest;
		$type 		= (string) $root->attributes()->type;
		$group 		= (string) $root->attributes()->group;

		if (is_object($this->_adapters[$type]))
		{
			return $this->_adapters[$type]->install();
		}
		return false;
	}
}
?>