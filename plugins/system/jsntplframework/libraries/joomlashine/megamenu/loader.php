<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2015 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file.
defined('_JEXEC') || die('Restricted access');

class JSNTplMMLoader
{
	protected static $paths = array();
	
	/**
	 * Register base path to search for class declaration files.
	 *
	 * @param   string  $path    Base path.
	 * @param   string  $prefix  Class prefix.
	 *
	 * @return  void
	 */
	public static function register($path, $prefix = 'JSN')
	{
		self::$paths[$path] = $prefix;
		
	}
	
	/**
	 * Auload InnoThemes's classes.
	 *
	 * @param   string  $className  Name of class.
	 *
	 * @return  void
	 */
	public static function load($className)
	{
		
		// Only autoload class name prefixed with JSN
		if (strpos($className, 'JSNTpl') === 0)
		{
			
			foreach (array_reverse(self::$paths) AS $base => $prefix)
			{
				if (strpos($className, $prefix) === 0)
				{
					
					// Split the class name into parts separated by camelCase
					$path = preg_split('/(?<=[a-z0-9])(?=[A-Z])/x', str_replace($prefix, '', $className));
					
					// Convert class name to file path
					$path = implode('/', array_map('strtolower', $path));
					
					// Check if class declaration file exists
					$file = $base . '/' . $path . '.php';
					
					while ( ! ($exists = @is_file($file)) AND strpos($file, '/') !== false)
					{
						$file = preg_replace('#/([^/]+)$#', '-\\1', $file);
					}
	
					if ( ! $exists AND strpos($path, '/') === false)
					{
						
						// If class name has single word, e.g. JSN_Version, duplicate it for alternative file path, e.g. version/version.php
						$exists = @is_file($file = $base . '/' . $path . '/' . $path . '.php');
					}
					
					if ($exists)
					{
						return include_once $file;
					}
				}
			}
	
			return false;
		}
	}

	/**
	 * Search a file in registered paths.
	 *
	 * @param   string  $file  Relative file path to search for.
	 *
	 * @return  string
	 */
	public static function get_path($file)
	{
		foreach (array_reverse(self::$paths) AS $base => $prefix)
		{
			if (@is_file($base . '/' . $file))
			{
				return $base . '/' . $file;
			}
		}
	
		return null;
	}	
}

// Register class autoloader with PHP
spl_autoload_register(array('JSNTplMMLoader', 'load'));