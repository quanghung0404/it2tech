<?php
/**
 * @version    $Id: imageshow.php 16609 2012-10-02 09:23:05Z haonv $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

// Set the directory separator define if necessary.
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}


include_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_imageshow' . DS . 'imagesources' . DS . 'plugin_helpers' . DS . 'sourceexternal.php');

class plgJSNImageshowSourcePicasa extends plgJSNImageshowSourceExternal
{
	var $_imageSourceName 	= 'picasa';

	function onLoadJSNImageSource($name)
	{
		if ($name != $this->_prefix.$this->_imageSourceName)
		{
			return false;
		}
		parent::onLoadJSNImageSource($name);
	}

	function _setPluginPath()
	{
		$this->_pluginPath = dirname(__FILE__);
	}

	function listSourcepicasaTables()
	{
		$tables = array('#__imageshow_external_source_picasa');
		return $tables;
	}
}