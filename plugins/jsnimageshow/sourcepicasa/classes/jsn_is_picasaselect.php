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

class JSNISPicasaSelect extends JHtmlSelect
{
	function getSelectBoxThumbnailSize($default = 144)
	{
		$thumbSize = $this->getThumbnailSizeOptions();
		return JHTML::_('select.genericList', $thumbSize, 'picasa_thumbnail_size', 'class="jsn-master jsn-input-xxlarge-fluid"', 'value', 'text', $default);
	}

	function getSelectBoxImageSize($default = 1024)
	{
		$imageSize = $this->getImageSizeOptions();
		return JHTML::_('select.genericList', $imageSize, 'picasa_image_size', 'class="jsn-master jsn-input-xxlarge-fluid"', 'value', 'text', $default);
	}

	function getImageSizeOptions()
	{
		$imageSize = array(
		array('value' => 94, 'text' => JText::_('94')),
		array('value' => 110, 'text' => JText::_('110')),
		array('value' => 128, 'text' => JText::_('128')),
		array('value' => 200, 'text' => JText::_('200')),
		array('value' => 220, 'text' => JText::_('220')),
		array('value' => 288, 'text' => JText::_('288')),
		array('value' => 320, 'text' => JText::_('320')),
		array('value' => 400, 'text' => JText::_('400')),
		array('value' => 512, 'text' => JText::_('512')),
		array('value' => 576, 'text' => JText::_('576')),
		array('value' => 640, 'text' => JText::_('640')),
		array('value' => 720, 'text' => JText::_('720')),
		array('value' => 800, 'text' => JText::_('800')),
		array('value' => 912, 'text' => JText::_('912')),
		array('value' => 1024, 'text' => JText::_('1024')),
		array('value' => 1152, 'text' => JText::_('1152')),
		array('value' => 1280, 'text' => JText::_('1280')),
		array('value' => 1440, 'text' => JText::_('1440')),
		array('value' => 1600, 'text' => JText::_('1600')),
		);

		return $imageSize;
	}

	function getThumbnailSizeOptions()
	{
		$thumbSize = array(
		array('value' => 32, 'text' => JText::_('32')),
		array('value' => 64, 'text' => JText::_('64')),
		array('value' => 72, 'text' => JText::_('72')),
		array('value' => 104, 'text' => JText::_('104')),
		array('value' => 144, 'text' => JText::_('144')),
		array('value' => 150, 'text' => JText::_('150')),
		array('value' => 160, 'text' => JText::_('160'))
		);

		return $thumbSize;
	}
}