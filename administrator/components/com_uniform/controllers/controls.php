<?php

/**
 * @version     $Id: controls.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Controller
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Controls controllers of Jcontroller
 * 
 * @package     Controllers
 * @subpackage  Controls
 * @since       1.6
 */
class JSNUniformControllerControls extends JController
{

	/**
	 * [load description]
	 * 
	 * @return void
	 */
	public function load()
	{
		header('content-type: text/javascript');

		$folders = array_slice(scandir(JSN_UNIFORM_PAGEDESIGN_ELEMENTS_PATH), 2);
		$output = '';

		foreach ($folders as $folder)
		{
			if (!is_file(JSN_UNIFORM_PAGEDESIGN_ELEMENTS_PATH . $folder . "/options.js"))
			{
				continue;
			}

			$template = file_get_contents(JSN_UNIFORM_PAGEDESIGN_ELEMENTS_PATH . $folder . "/template.html");
			$options = file_get_contents(JSN_UNIFORM_PAGEDESIGN_ELEMENTS_PATH . $folder . "/options.js");

			$output .= "JSNPageDesign.defineControl('{$folder}', {$options}, '{$template}');";
			$output .= "\r\n";
		}

		$output = str_replace(array("\r\n", "\n", "\t"), '', $output);
		echo $output;

		JFactory::getApplication()->close(0);
	}

}
