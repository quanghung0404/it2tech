<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * @version     $Id: jsnhelpbutton.php 16648 2012-10-03 10:15:24Z giangnd $
 * @subpackage  JSN.ImageShow
 * @modifed     JoomlaShine Team <support@joomlashine.com>
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Renders a help popup window button
 *
 * @package     JSN.ImageShow
 * @since       2.5
 */
class JButtonJSNHelpButton extends JButton
{
	/**
	 * @var    string	Button type
	 */
	protected $_name = 'JSNHelpButton';

	/**
	 * Fetches the button HTML code.
	 *
	 * @param   string   $type       Unused string.
	 * @param   string   $ref        The name of the help screen (its key reference).
	 * @param   boolean  $com        Use the help file in the component directory.
	 * @param   string   $override   Use this URL instead of any other.
	 * @param   string   $component  Name of component to get Help (null for current component)
	 *
	 * @return  string
	 *
	 * @since   2.5
	 */
	public function fetchButton($type = 'JSNHelpButton', $name = '', $text = '', $url = '', $width = 640, $height = 480, $top = 0, $left = 0, $onClose = '')
	{
		//JHTML::_('behavior.modal', 'a.jsn-is-helper-modal');
		JSNHtmlAsset::loadScript('imageshow/joomlashine/help', array(
				'pathRoot' => JURI::root(),
				'language' => JSNUtilsLanguage::getTranslated(array(
						'JSN_IMAGESHOW_OK',
						'JSN_IMAGESHOW_CLOSE',
						'JSN_IMAGESHOW_SAVE',
						'JSN_IMAGESHOW_CANCEL'
						))
						));
						$text = JText::_('JTOOLBAR_HELP');
						$class = $this->fetchIconClass('help');
						$doTask = $this->_getCommand($name, $url, $width, $height, $top, $left);
						//$html = "<a href=\"#\" rel='{\"size\": {\"x\": 500, \"y\": 350}}' class=\"toolbar jsn-is-modal\">\n";
						$html = "<a class=\"jsn-is-helper-modal\" href=\"javascript: void(0);\">\n";
						$html .= "<span class=\"$class\">\n";
						$html .= "</span>\n";
						$html .= "$text\n";
						$html .= "</a>\n";

						return $html;
	}

	/**
	 * Get the button id
	 *
	 * Redefined from JButton class
	 *
	 * @return  string	Button CSS Id
	 */
	public function fetchId()
	{
		return $this->_parent->getName() . '-' . "help";
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @param   string   $name    Button name
	 * @param   string   $url     URL for popup
	 * @param   integer  $width   Unused formerly width.
	 * @param   integer  $height  Unused formerly height.
	 * @param   integer  $top     Unused formerly top attribute.
	 * @param   integer  $left    Unused formerly left attribure.
	 *
	 * @return  string   JavaScript command string
	 *
	 * @since   2.5
	 */
	protected function _getCommand($name, $url, $width, $height, $top, $left)
	{
		if (substr($url, 0, 4) !== 'http')
		{
			$url = JURI::base() . $url;
		}

		return $url;
	}

}
