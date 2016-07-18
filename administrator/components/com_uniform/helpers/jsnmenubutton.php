<?php

/**
 * @version     $Id: jsnmenubutton.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Helpers
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
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
		$subMenuItemLists = JSNUniformHelper::getForms(5);
		// Build options
//	$options[] = array(
//	    'title'    => JText::_('JSN_UNIFORM_SUBMENU_LAUNCHPAD'),
//	    'link'     => 'index.php?option=com_uniform',
//	    'class'    => 'parent primary',
//	    'icon'     => 'icon-off'
//	);
		$options[] = array(
		'title' => JText::_('JSN_UNIFORM_SUBMENU_FORMS'),
		'link' => 'index.php?option=com_uniform&view=forms',
		'class' => 'parent primary',
		'sub_menu_link' => 'index.php?option=com_uniform&view=form&task=form.edit&form_id={$form_id}',
		'sub_menu_field_title' => 'form_title',
		'sub_menu_link_add_title' => 'Create new forms',
		'sub_menu_link_add' => 'index.php?option=com_uniform&view=form&layout=edit',
		'data_sub_menu' => $subMenuItemLists,
		'icon' => 'jsn-icon-finder',
		);
		$options[] = array(
		'title' => JText::_('JSN_UNIFORM_SUBMENU_SUBMISSION'),
		'link' => 'index.php?option=com_uniform&view=submissions',
		'class' => 'parent primary',
		'icon' => 'jsn-icon-file'
		);
		$options[] = array(
		'class' => 'separator'
		);

		$options[] = array(
		'title' => JText::_('JSN_UNIFORM_SUBMENU_CONFIGURATION'),
		'link' => 'index.php?option=com_uniform&view=configuration'
		);
		$options[] = array(
		'title' => JText::_('JSN_UNIFORM_SUBMENU_ABOUT'),
		'link' => 'index.php?option=com_uniform&view=about'
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
