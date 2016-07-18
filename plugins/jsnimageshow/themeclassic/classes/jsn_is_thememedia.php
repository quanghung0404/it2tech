<?php
/**
 * @version    $Id: jsn_is_thememedia.php 16394 2012-09-25 08:31:07Z giangnd $
 * @package    JSN.ImageShow
 * @subpackage JSN.ThemeClassic
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die( 'Restricted access' );
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
JSNISFactory::importFile('classes.jsn_is_mediamanager');
class JSNISThemeMedia extends JSNISMediaManager
{
	var $_showcaseThemeName = 'themeclassic';
	var $_showcaseThemeType = 'jsnimageshow';

	public static function getInstance()
	{
		static $instanceThemeMedia;
		if ($instanceThemeMedia == null)
		{
			$instanceThemeMedia = new JSNISThemeMedia();
		}
		return $instanceThemeMedia;
	}

	function setMediaBasePath()
	{
		$act = JRequest::getCmd('act','custom');

		if ($act == 'custom')
		{
			$this->setPath(JPATH_ROOT.DS.'images', JURI::root().'images');
		}

		if ($act == 'background')
		{
			$this->setPath(JPATH_COMPONENT_SITE.DS.'assets'.DS.'images'.DS.'bg-images', JURI::root().'components/com_imageshow/assets/images/bg-images');
		}

		if ($act == 'pattern')
		{
			$this->setPath(JPATH_PLUGINS.DS.$this->_showcaseThemeType.DS.$this->_showcaseThemeName.DS.'assets'.DS.'images'.DS.'bg-patterns', JURI::root().'plugins/'.$this->_showcaseThemeType.'/'.$this->_showcaseThemeName.'/assets/images/bg-patterns');
		}

		if ($act == 'watermark')
		{
			$this->setPath(JPATH_ROOT.DS.'images', JURI::root().'images');
		}
	}
}