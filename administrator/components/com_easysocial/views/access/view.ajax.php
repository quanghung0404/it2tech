<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

FD::import('admin:/views/views');

class EasySocialViewAccess extends EasySocialAdminView
{
	/**
	 * Post process after scanning for files
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function scanFiles($files = array())
	{
		$message = JText::sprintf('COM_EASYSOCIAL_DISCOVER_FOUND_FILES', count($files));

		return $this->ajax->resolve($files, $message);
	}

	public function installFile($obj = null)
	{
		$ajax 		= FD::ajax();

		if ($this->hasErrors())
		{
			return $ajax->reject($this->getMessage());
		}

		$message 	= JText::sprintf('COM_EASYSOCIAL_DISCOVER_CHECKED_OUT', $obj->file, count($obj->rules));

		return $ajax->resolve($message);
	}
}
