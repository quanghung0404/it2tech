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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewRSD extends EasyBlogView
{
	public function display($tpl = null)
	{
		if (!$this->config->get('main_remotepublishing_xmlrpc')) {
			return;
		}

		$theme = EB::template();

		// Get the site details
		$title = EB::jconfig()->get('sitename');
		$link = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&amp;tmpl=component';
		$xmlrpc = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&amp;view=xmlrpc&amp;tmpl=component';

		$theme->set('title', $title);
		$theme->set('link', $link);
		$theme->set('xmlrpc', $xmlrpc);

		$output = $theme->output('site/rsd/template');
		
		header('Content-Type: application/xml; charset=utf-8', true);
		echo $output;
		exit;
	}
}
