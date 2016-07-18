<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerThemes extends EasyBlogController
{
	public function getAjaxTemplate()
	{
		$files	= JRequest::getVar( 'names' , '' );

		if (empty($files)) {
			return false;
		}

		// Ensure the integrity of each items submitted to be an array.
		if (!is_array($files)) {
			$files	= array( $files );
		}

		$result		= array();

		$template 	= EB::template();

		foreach ($files as $file) {

			$dashboard = explode( '/' , $file );

			if ($dashboard[0]=="dashboard") {
				$out		= $template->output('site/dashboard/'.$dashboard[1] . '.ejs' );

			} elseif ($dashboard[0]=="media") {
				$out		= $template->output( 'site/media/' . $dashboard[1] . '.ejs' );

			} else {
				$out		= $template->output( 'site/' . $file . '.ejs' );

			}

			$obj			= new stdClass();
			$obj->name		= $file;
			$obj->content	= $out;

			$result[]		= $obj;
		}

		header('Content-type: text/x-json; UTF-8');
		$json	 		= new Services_JSON();
		echo $json->encode( $result );
		exit;
	}
}
