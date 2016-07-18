<?php

/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  AdminComponent
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla utility
jimport('joomla.utilities.utility');

/**
 * Button base class.
 *
 * @package     JSN_Mobilize
 * @subpackage  AdminComponent
 * @since       1.0.0
 */
class JButtonJSNMenuButton extends JButton
{

	protected $_name = 'JSNMenuButton';

	/**
	 * Get the button.
	 *
	 * @return  string
	 */
	public function fetchButton()
	{
		// Build options
		$options[] = array(
		'title' => JText::_('JSN_MOBILIZE_SUB_MENU_MOBILIZATION_TEXT'),
		'link' => 'index.php?option=com_mobilize&view=edit',
		'class' => 'parent primary',
		'icon' => 'jsn-icon24 icon-finder',
		);
		$options[] = array(
		'class' => 'separator'
		);
		$options[] = array(
		'title' => JText::_('JSN_MOBILIZE_SUB_MENU_CONFIGURARTION_TEXT'),
		'link' => 'index.php?option=com_mobilize&view=config'
		);
		$options[] = array(
		'title' => JText::_('JSN_MOBILIZE_SUB_MENU_ABOUT_TEXT'),
		'link' => 'index.php?option=com_mobilize&view=about'
		);

		// Generate HTML code for sub-menu
		$html = JSNHtmlGenerate::menuToolbar($options);

		return $html;
	}

	/**
	 * Fetch Id attribute.
	 *
	 * @return  string
	 */
	public function fetchId()
	{
		return "jsn-is-menu-button";
	}

}
