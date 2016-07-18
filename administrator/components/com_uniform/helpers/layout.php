<?php

/**
 * @version     $Id: layout.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Helpers
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
defined('_JEXEC') or die('Restricted access');

/**
 * JSNUniform layout helper
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.6
 */
class JSNUniformLayoutHelper
{

	/**
	 * Get all installed layouts
	 * 
	 * @param   string  $installPath  Path install
	 * 
	 * @return array
	 */
	public static function getInstalled($installPath)
	{
		$directories = scandir($installPath);
		$directories = array_slice($directories, 2);

		$layouts = array();
		foreach ($directories as $directory)
		{
			$layoutInformation = self::getInformation($installPath . DS . $directory);
			if ($layoutInformation === false)
			{
				continue;
			}

			$layouts[] = $layoutInformation;
		}

		return $layouts;

	}

	/**
	 * Retrieve layout information from manifest file
	 * 
	 * @param   string  $identify  Identify
	 * 
	 * @return array
	 */
	public static function getInformation($identify)
	{
		$xmlFile = $path . DS . 'uniform.xml';
		if (!is_file($xmlFile))
		{
			return false;
		}

		$xml = JFactory::getXML($xmlFile);
		if (!isset($xml->name) || empty($xml->name))
		{
			return false;
		}

		$layout = new stdClass;
		$layout->idenfity = basename($path);
		$layout->name = (string) $xml->name;
		$layout->description = (string) $xml->description;

		return $layout;

	}

}
