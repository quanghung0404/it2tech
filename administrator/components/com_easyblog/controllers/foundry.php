<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerFoundry extends EasyBlogController
{
	public function getResource()
	{
		$resources = JRequest::getVar('resource');

		if (!$resources) {

		}
		foreach( $resources as &$resource )
		{
			$resource = (object) $resource;
			$func = 'get' . ucfirst( $resource->type );
			$result = self::$func( $resource->name );

			if( $result !== false )
			{
				$resource->content = $result;
			}
		}

		header('Content-type: text/x-json; UTF-8');
		echo json_encode($resources);
		exit;
	}

	public function getView($namespace = '', $type = '', $prefix = '', $config = Array() )
	{
		// Load language support for front end and back end.
		$lang 	= JFactory::getLanguage();

		$lang->load('com_easyblog', JPATH_ROOT . '/administrator');
		$lang->load('com_easyblog', JPATH_ROOT);

		$output 	= '';
		$parts 		= explode('/', $namespace);

		// For admin
		if ($parts[0] == 'admin') {

			$template 	= EB::template(null, array('admin' => true));
			$output 	= $template->output($namespace, array(), 'ejs');
		} else if ($parts[0] == 'dashboard') {
			$template 	= EB::template(null, array('dashboard' => true));
			$output 	= $template->output('site/dashboard/' . $parts[1], array(), 'ejs');
		}  else {
			$template 	= EB::template();
			$output		= $template->output($namespace, array(), 'ejs');
		}

		return $output;
	}

	public function getLanguage( $lang )
	{
		// Load language support for front end and back end.
		EB::loadLanguages();

		return JText::_( strtoupper( $lang ) );
	}
}
