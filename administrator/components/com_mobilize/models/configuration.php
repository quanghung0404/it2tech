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
 * Configuration model.
 *
 * @package     JSN_Mobilize
 * @subpackage  AdminComponent
 * @since       1.0.0
 */
class JSNMobilizeModelConfiguration extends JSNConfigModel
{

	/**
	 * Method to do additional instant update according config change
	 *
	 * @param   string  $name   Name of changed config parameter.
	 * @param   mixed   $value  Recent config parameter value.
	 *
	 * @return  void
	 */
	protected function instantUpdate($name, $value)
	{
		if ($name == 'disable_all_messages')
		{
			// Get name of messages table
			$table = '#__jsn_' . substr(JRequest::getCmd('option'), 4) . '_messages';

			// Enable/disable all messages
			$db = JFactory::getDbo();
			$db->setQuery("UPDATE `{$table}` SET published = " . (1 - $value) . " WHERE 1");
			$db->query();
		}
		else
		{
			return parent::instantUpdate($name, $value);
		}

		return true;
	}

}
