<?php

/**
 * @version     $Id: uninstall.uniform.php 19028 2012-11-28 07:40:50Z thailv $
 * @package     JSNUniform
 * @subpackage  uninstall
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Function to finalize extension removal.
 *  
 * @return void
 */
function com_uninstall()
{
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	$query->select('form_id');
	$query->from('#__jsn_uniform_forms');
	$db->setQuery($query);
	if ($db->loadObjectList())
	{
		foreach ($db->loadObjectList() as $form)
		{
			$db->setQuery("DROP TABLE IF EXISTS #__jsn_uniform_submissions_{$form->form_id}");
			$db->execute();
		}
	}
}