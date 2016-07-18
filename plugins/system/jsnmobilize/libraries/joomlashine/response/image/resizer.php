<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
define('_JEXEC',	'1');
defined('_JEXEC') or die('Restricted access');

// Shorten directory separator constant
define('DS', DIRECTORY_SEPARATOR);

// Define base directory
define(
	'JPATH_BASE',
	str_replace(
		'/',
		DIRECTORY_SEPARATOR,
		str_replace(
			'plugins/system/jsnmobilize/libraries/joomlashine/response/image/resizer.php',
			'',
			str_replace('\\', '/', __FILE__)
		)
	)
);

// Initialize Joomla framework
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

// Instantiate the application
$app = JFactory::getApplication('site');

// Initialize JSN Framework
require_once JPATH_ROOT . DS . 'plugins' . DS . 'system' . DS . 'jsnframework' . DS . 'jsnframework.php';

$dispatcher		= JDispatcher::getInstance();
$jsnframework	= new PlgSystemJSNFramework($dispatcher);
$jsnframework->onAfterInitialise();

// Initialize JSN Mobilize
require_once JPATH_ROOT . DS . 'plugins' . DS . 'system' . DS . 'jsnmobilize' . DS . 'jsnmobilize.php';

$jsnmobilize	= new PlgSystemJSNMobilize($dispatcher);
$jsnmobilize->onAfterInitialise();

// Initialize variables
if ( ! isset($_REQUEST['src']) OR ! isset($_REQUEST['width']) OR ! isset($_REQUEST['dest']))
{
	jexit(JText::_('JSN_MOBILIZE_INVALID_REQUEST'));
}

$src	= $_REQUEST['src'];
$width	= $_REQUEST['width'];
$dest	= $_REQUEST['dest'];

// Load necessary Joomla libraries
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

// Load image manipulation library
require_once JPATH_ROOT . DS . 'plugins' . DS . 'system' . DS . 'jsnmobilize' . DS . 'libraries' . DS . '3rd-party' . DS . 'ace-media-image' . DS . 'image.php';

// Resize the original image to requested width
$image = new Ace_Media_Image($_SERVER['DOCUMENT_ROOT'] . DS . str_replace('/', DS, $src));
$image->resize((int) $width, null, 'W');

if (JFolder::create(JPATH_ROOT . DS . $dest . DS . $width) AND $image->save(JPATH_ROOT . DS . $dest . DS . $width . DS . basename($src), 90))
{
	if (isset($_REQUEST['return']) AND $_REQUEST['return'] == 'uri')
	{
		// Get link to optimized image
		$link = str_replace(
			'/plugins/system/jsnmobilize/libraries/joomlashine/response/image/',
			'',
			JURI::root()
		) . str_replace(
			str_replace(array('/', '\\'), array('/', '/'), JPATH_ROOT),
			'',
			str_replace(array('/', '\\'), array('/', '/'), JPATH_ROOT . DS . $dest . DS . $width . DS . basename($src))
		);

		jexit($link);
	}
	else
	{
		// Send the requisite header information
		header('Content-Type: ' . $image->getMimeType());
		header('Content-Length: ' . filesize(JPATH_ROOT . DS . $dest . DS . $width . DS . basename($src)));

		// Read image content then send back to the client broswer
		if ($image = JFile::read(JPATH_ROOT . DS . $dest . DS . $width . DS . basename($src)))
		{
			jexit($image);
		}
		else
		{
			jexit(JText::_('JSN_MOBILIZE_READ_IMAGE_FAIL'));
		}
	}
}
else
{
	jexit(JText::_('JSN_MOBILIZE_RESIZE_IMAGE_FAIL'));
}
