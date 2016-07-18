<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_factory.php 16077 2012-09-17 02:30:25Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.filesystem.file' );
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_imageshow'.DS.'imageshow.defines.php';
class JSNISFactory
{
	private static $_internalSources;
	public static $_imageSourceClass;
	public static $_imageSourceConfig = array();


	public static function getInstance($className, $config = null)
	{
		static $arrayInstance = array();

		if (!empty($config)) {
			$arrayInstance[$className] = new $className($config);
		}

		if (empty($arrayInstance[$className])) {
			$arrayInstance[$className] = new $className();
		}

		return $arrayInstance[$className];
	}

	public static function getObj($string , $specifyClass = null, $config = null, $basePath = 'admin', $ext = '.php')
	{
		$path 		= '';
		$array 		= explode('.', $string);
		$fileName 	= end($array);
		$className  = self::paserFileNameToClass($fileName);

		if (count($array) > 0)
		{
			foreach($array as $value)
			{
				if (!empty($value)) {
					$path .= DS.$value;
				}
			}

			if ($basePath == 'admin') {
				$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_imageshow'.DS.$path.$ext;
			} elseif ($basePath == 'site') {
				$path = JPATH_SITE.DS.'components'.DS.'com_imageshow'.DS.$path.$ext;
			} else if ($basePath == 'jsnplugin') {
				$path = JSN_IMAGESHOW_PATH_JSN_PLUGIN.DS.$path.$ext;
			}else {
				$path = $basePath.DS.$path.$ext;
			}

			if (file_exists($path))
			{
				require_once($path);

				if (empty($specifyClass)) {
					$class = self::getInstance($className, $config);
				} else {
					$class = self::getInstance($specifyClass , $config);
				}

				return $class;
			}
			else
			{
				echo $path.' '.JText::_('NOT EXISTS');
			}
		}
	}

	public static function paserFileNameToClass($fileName)
	{
		if (!empty($fileName) && count(explode('_', $fileName)) == 3)
		{
			$arrayNamePart = explode('_', $fileName);
			return strtoupper($arrayNamePart[0].$arrayNamePart[1]).ucfirst($arrayNamePart[2]);
		}
		return false;
	}

	public static function importFile($string , $basePath = 'admin', $ext = '.php')
	{
		$path 		= '';
		$array 		= explode('.', $string);
		$fileName 	= end($array);
		$className  = self::paserFileNameToClass($fileName);

		if (count($array) > 0)
		{
			foreach ($array as $value)
			{
				if (!empty($value))
				{
					$path .= DS.$value;
				}
			}

			if ($basePath == 'admin')
			{
				$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_imageshow'.DS.$path.$ext;
			}
			elseif ($basePath == 'site')
			{
				$path = JPATH_SITE.DS.'components'.DS.'com_imageshow'.DS.$path.$ext;
			}
			else
			{
				$path = $basePath.DS.$path.$ext;
			}

			if (JFile::exists($path))
			{
				require_once($path);
			}
			else
			{
				return JError::raiseError(500, $path.' '.JText::_('NOT EXISTS'));
			}
		}
	}

	public static function getSource($sourceIdentify, $type = 'internal', $showlistID = 0)
	{
		JTable::addIncludePath(JSN_IMAGESHOW_ADMIN_PATH.DS.'tables');
		require_once JSN_IMAGESHOW_ADMIN_PATH.DS.'imagesources'.DS.'images_sources.php';
		require_once JSN_IMAGESHOW_ADMIN_PATH.DS.'imagesources'.DS.'images_sources_default.php';

		self::$_imageSourceConfig['showlistID'] = $showlistID;

		if (empty($sourceIdentify) && !empty($type)) {
			self::$_imageSourceClass = 'JSNImagesSources'.ucfirst($type);
		}

		if (empty($sourceIdentify) && empty($type)) {
			self::$_imageSourceClass = 'JSNImagesSourcesDefault';
		}

		if ($type == 'internal' || $type == 'external') {
			self::_getPluginSource($sourceIdentify, $type);
		} else {
			self::_getFolderSource($sourceIdentify);
		}

		if (class_exists(self::$_imageSourceClass)) {
			return new self::$_imageSourceClass(self::$_imageSourceConfig);
		} else {
			return JError::raiseError(500, self::$_imageSourceClass. ' not exists');
		}
	}

	public static function _getPluginSource($sourceIdentify, $type)
	{
		require_once JSN_IMAGESHOW_ADMIN_PATH.DS.'imagesources'.DS.'images_sources_internal'.'.php';
		require_once JSN_IMAGESHOW_ADMIN_PATH.DS.'imagesources'.DS.'images_sources_external'.'.php';

		$pluginName = 'source'.$sourceIdentify;

		$objJSNSource = JSNISFactory::getObj('classes.jsn_is_source');
		$objJSNSource->callSourcePlugin(array('pluginName' => $pluginName));

		$define = JPATH_PLUGINS.DS.'jsnimageshow'.DS.'source'.$sourceIdentify.DS.'define.php';
		$defineObj = json_decode((constant('JSN_IS_'.strtoupper('source'.$sourceIdentify))));

		self::$_imageSourceClass  = 'JSN'.ucfirst($type).'Source'.ucfirst($sourceIdentify);
		self::$_imageSourceConfig = array_merge(self::$_imageSourceConfig, array('sourceIdentify' => $sourceIdentify, 'sourceDefine' => $defineObj));
	}

	public static function _getFolderSource($sourceIdentify)
	{
		$type = 'internal';

		require_once JSN_IMAGESHOW_ADMIN_PATH.DS.'imagesources'.DS.'images_sources_internal.php';

		$sourceFile 	= JPath::clean(JSN_IMAGESHOW_ADMIN_PATH.DS.'imagesources'.DS.'source_folder'.DS.$type.'_source_'.$sourceIdentify.'.php');
		$sourceDefine 	= JPath::clean(JSN_IMAGESHOW_ADMIN_PATH.DS.'imagesources'.DS.'source_folder'.DS.'define.php');

		if (JFile::exists($sourceFile) && JFile::exists($sourceDefine))
		{

			require_once $sourceFile;
			require_once $sourceDefine;

			self::$_imageSourceClass 	= 'JSN'.ucfirst($type).'Source'.ucfirst($sourceIdentify);
			self::$_imageSourceConfig 	= array_merge(self::$_imageSourceConfig, array('sourceIdentify' => $sourceIdentify, 'sourceDefine' => json_decode(constant('JSN_IS_SOURCEFOLDER'))));
		}
	}
}