<?php
/**
 * @version		$Id: filebrowser.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSControllerFileBrowser extends FPSSController
{
	function display($cachable = false, $urlparams = array())
	{
		JRequest::setVar('view', 'filebrowser');
		JRequest::setVar('tmpl', 'component');
		parent::display();
	}

}
