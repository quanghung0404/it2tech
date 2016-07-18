<?php
/**
 * @version    $Id: jsn_is_downloadcore.php 16077 2012-09-17 02:30:25Z giangnd $
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

jimport('joomla.filesystem.file');
include_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_imageshow' . DS . 'classes' . DS . 'jsn_is_downloadpackageauth.php';

class JSNISDownloadCore extends JSNISDownloadPackageAuth
{
	public function setOptions($options = null)
	{
		if (isset($options->upgrade))
		{
			$this->_getFields .= '&upgrade=' .urlencode($options->upgrade);
		}
		parent::setOptions($options);
	}
}