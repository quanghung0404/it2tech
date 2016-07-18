<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerFoundry extends EasyBlogController
{
	/**
	 * Responsible to retrieve resources
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getResource()
	{
		$resources 	= JRequest::getVar('resource');

		if (!$resources) {
			die('Invalid request');
		}

		foreach ($resources as &$resource) {

			$resource = (object) $resource;

			// Convert "view" into "FoundryView" because the getView method
			// will be overriding the parent's getView method and it will fail
			if ($resource->type == 'view') {
				$method = 'getFoundryView';
			} else {
				$method = 'get' . ucfirst($resource->type);
			}

			if (!method_exists($this, $method)) {
				continue;
			}

			$result = $this->$method($resource->name);

			if ($result !== false) {
				$resource->content = $result;
			}
		}

		header('Content-type: text/x-json; UTF-8');
		echo json_encode($resources);
		exit;
	}

	/**
	 * Retrieves a view file
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getFoundryView($name = '', $type = '', $prefix = '', $config = array())
	{
		$file = $name;
		$parts = explode('/', $file);

		$template = EB::template();
		$output = $template->output($file, array(), 'ejs');

		return $output;
	}

	/**
	 * Translates text
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string	The language string to translate
	 * @return	string
	 */
	public function getLanguage($constant)
	{
		// Load languages on the site
		EB::loadLanguages();

		$string	= JText::_(strtoupper($constant));

		return $string;
	}
}
