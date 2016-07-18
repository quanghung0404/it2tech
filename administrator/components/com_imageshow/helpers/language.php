<?php
/**
 * @version    $Id: language.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JSNISLanguageHelper Class
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

class JSNISLanguageHelper
{

	/**
	 * Get all languages, that supported by component
	 *
	 * @param   string  $area  the area of site, here is: admin & site
	 *
	 * @return array
	 */

	public static function getSupportedLanguage ($area = 'site')
	{
		if ($area == 'site')
		{
			$path = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_imageshow' . DS . 'languages' . DS . 'site';
		}
		else
		{
			$path = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_imageshow' . DS . 'languages' . DS . 'admin';
		}

		$files = glob("{$path}/*.ini");
		$supportedLanguages = array();

		if (count($files))
		{
			foreach ($files as $file)
			{
				$name = basename($file);
				if (preg_match('/^([a-z]+)\-([A-Z]+)\./', $name, $matches))
				{
					$code = $matches[1] . '-' . $matches[2];
					if (!in_array($code, $supportedLanguages))
					{
						$supportedLanguages[] = $code;
					}
				}
			}
		}
		return $supportedLanguages;
	}
}
