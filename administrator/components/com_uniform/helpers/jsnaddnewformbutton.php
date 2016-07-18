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
class JButtonJSNAddNewFormButton extends JButton
{

	protected $_name = 'JSNAddNewFormButton';

	/**
	 * Get the button.
	 *
	 * @return  string
	 */
	public function fetchButton()
	{
		$options[] = array(
			'title' => JText::_('JSN_UNIFORM_BLANK_FORM'),
			'link' => 'index.php?option=com_uniform&view=form&layout=edit',
			'class' => 'parent primary',
			'icon' => 'jsn-icon-finder',
		);
		$options[] = array(
			'title' => JText::_('JSN_UNIFORM_CONTACT_US_FORM'),
			'link' => 'index.php?option=com_uniform&view=form&layout=edit&form=Contact Us',
			'class' => 'parent primary',
			'icon' => 'jsn-icon-finder',
		);
		$options[] = array(
			'title' => JText::_('JSN_UNIFORM_CUSTOMER_FEEDBACK_FORM'),
			'link' => 'index.php?option=com_uniform&view=form&layout=edit&form=Customer Feedback',
			'class' => 'parent primary',
			'icon' => 'jsn-icon-finder',
		);
		$options[] = array(
			'title' => JText::_('JSN_UNIFORM_JOB_APPLICATION_FORM'),
			'link' => 'index.php?option=com_uniform&view=form&layout=edit&form=Job Application',
			'class' => 'parent primary',
			'icon' => 'jsn-icon-finder',
		);
		$options[] = array(
			'title' => JText::_('JSN_UNIFORM_EVENT_REGISTRATION'),
			'link' => 'index.php?option=com_uniform&view=form&layout=edit&form=Event Registration',
			'class' => 'parent primary',
			'icon' => 'jsn-icon-finder',
		);
		$options[] = array(
			'title' => JText::_('JSN_UNIFORM_VOTING_FORM'),
			'link' => 'index.php?option=com_uniform&view=form&layout=edit&form=Voting Form',
			'class' => 'parent primary',
			'icon' => 'jsn-icon-finder',
		);
		// Generate HTML code for sub-menu
		$html = JSNHtmlGenerate::menuToolbar($options, JText::_('JTOOLBAR_NEW'), 'icon-32-new');

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
