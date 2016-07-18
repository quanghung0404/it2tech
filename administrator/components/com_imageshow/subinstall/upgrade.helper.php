<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: upgrade.helper.php 15763 2012-09-01 06:41:07Z hiennh $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
// Set the directory separator define if necessary.
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
include_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_imageshow' . DS . 'classes' . DS . 'jsn_is_httprequest.php';
include_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_imageshow' . DS . 'classes' . DS . 'jsn_is_readxmldetails.php';
include_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_imageshow' . DS . 'classes' . DS . 'jsn_is_comparefiles.php';

class JSNUpgradeHelper
{
	var $_mainfest		= null;
	var $_url 			= null;
	var $_previousName	= null;
	var $_currentName	= null;
	var $_path			= null;
	var $_manifestXML	= array();

	function JSNUpgradeHelper($manifest)
	{
		$this->setManifest($manifest);
		$this->setManifestXML();
		$this->setPreviousName();
		$this->setCurrentName();
		$this->setPath();
		$this->setURL();
	}

	function setManifest($manifest)
	{
		$this->_mainfest = $manifest;
	}

	function setManifestXML()
	{
		$objectReadxmlDetail = new JSNISReadXmlDetails();
		$this->_manifestXML  = $objectReadxmlDetail->parserXMLDetails();
	}

	function setURL()
	{
		$this->_url = 'http://media.joomlashine.com/products/extensions/jsn_imageshow/checksum/';
	}

	function getPreviousName()
	{
		return 'imageshow';
	}

	function getCurrentName()
	{
		return 'imageshow';
	}

	function setPreviousName()
	{
		$edition = $this->getPreviousEdition();
		$name    = $this->getPreviousName();
		if($edition != '')
		{
			$edition = '_'.$edition;
		}
		$fileName 			 = 'jsn_'.$name.$edition.'_'.$this->getPreviousVersion().'.checksum';
		$this->_previousName = $fileName;
	}

	function setCurrentName()
	{
		$edition = $this->getCurrentEdition();
		$name    = $this->getCurrentName();
		if ($edition != '')
		{
			$edition = '_'.$edition;
		}
		$fileName 			 = 'jsn_'.$name.$edition.'_'.$this->getCurrentVersion().'.checksum';
		$this->_currentName  = $fileName;
	}

	function setPath()
	{
		$path 	  	  = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_imageshow';
		$this->_path  = $path;
	}

	function getPreviousVersion()
	{
		$session = JFactory::getSession();
		$session->set('preversion', @$this->_manifestXML['version'], 'jsnimageshow');
		return @$this->_manifestXML['version'];
	}

	function getCurrentVersion()
	{
		$version     = $this->_mainfest->version;
		return (string) $version;
	}

	function getPreviousEdition()
	{
		return (isset($this->_manifestXML['edition']) ? str_replace(' ', '_', JString::strtolower($this->_manifestXML['edition'])):'');
	}

	function getCurrentEdition()
	{
		$edition     = $this->_mainfest->edition;
		return str_replace(' ', '_' , JString::strtolower($edition));
	}

	function checkPreviousChecksumFile()
	{
		$path = $this->_path . DS . $this->_previousName;

		if (JFile::exists($path))
		{
			return $path;
		}

		return false;
	}

	function checkCurrentChecksumFile()
	{
		$parent = JInstaller::getInstance();
		$path = $parent->getPath('source') . DS . 'admin' . DS . $this->_currentName;

		if (JFile::exists($path))
		{
			return $path;
		}

		return false;
	}

	function getPreviousFileContent()
	{
		$path = $this->checkPreviousChecksumFile();

		if ($path != false)
		{
			return file($path);
		}
		else
		{
			$url 		= $this->_url . $this->_previousName;
			$objJSNHTTP	= new JSNISHTTPRequest($url);
			$content    = $objJSNHTTP->DownloadToString();

			if (!$content)
			{
				$content = array();
				return $content;
			}

			return explode("\n", $content);
		}
	}

	function getCurrentFileContent()
	{
		$path = $this->checkCurrentChecksumFile();

		if ($path != false)
		{
			return @file($path);
		}
	}

	function deleteRedundantFile($files)
	{
		$strReplace = array('\\', '/');
		if (count($files))
		{
			foreach ($files as $key => $file)
			{
				$path = JPATH_ROOT . DS . str_replace($strReplace, DS, $key);
				if (JFile::exists($path))
				{
					if (basename($path) != 'defines.php')
					{
						JFile::delete($path);
					}
				}

				$dir = @dirname($path);
				if (is_dir($dir))
				{
					$fileList = JFolder::files($dir, '.', true);

					if(!count($fileList))
					{
						@rmdir($dir);
					}
				}
			}
			//$this->deletePreviousChecksumFile();
		}
		return true;
	}

	function deletePreviousChecksumFile()
	{
		$path = $this->checkPreviousChecksumFile();
		if ($path != false)
		{
			if (JFile::exists($path))
			{
				JFile::delete($path);
			}
			return true;
		}
		return false;
	}

	function executeUpgrade()
	{
		$previousFileContent = $this->getPreviousFileContent();
		$currentFileContent  = $this->getCurrentFileContent();
		$objCompareFiles	 = new JSNISCompareFiles();
		$result 			 = $objCompareFiles->compareFileContent($currentFileContent, $previousFileContent);
		$this->deleteRedundantFile($result);
	}
}