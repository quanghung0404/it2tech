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

/**
 * General controller.
 *
 * @package     JSN_Mobilize
 * @subpackage  AdminComponent
 * @since       1.0.0
 */
class JSNMobilizeController extends JSNBaseController
{
	/**
	 * Method for display page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Get config parameters
		if (class_exists('JSNConfigHelper'))
		{
			$config = JSNConfigHelper::get();

			// Check if JSN Mobilize is configured correctly
			if ($config->get('link_mobile') == 'm.domain.tld' OR $config->get('link_tablet') == 'tablet.domain.tld')
			{
				// Get application object
				$app = JFactory::getApplication();

				// Set message
				$app->enqueueMessage(JText::_('JSN_MOBILIZE_CONFIG_LINKS'));

				// Mark required parameters
				JRequest::setVar('required', array('link_mobile', 'link_tablet'));

				// Set config view
				JRequest::setVar('view', 'config');
			}
			else
			{
				// Set edit view
				JRequest::setVar('view', JRequest::getCmd('view', 'profiles'));
			}
		}
		else
		{
			// Set edit view
			JRequest::setVar('view', JRequest::getCmd('view', 'profiles'));
		}

		// Call parent method
		parent::display($cachable, $urlparams);
	}

	/**
	 * Method for hiding a message.
	 *
	 * @return	void
	 */
	public function hideMsg()
	{
		JSNUtilsMessage::hideMessage(JFactory::getApplication()->input->getInt('msgId'));
		exit;
	}

}
