<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewLogin extends EasyBlogView
{
	public function display($tmpl = null)
	{
		// If user is already logged in, just redirect them
		if (!$this->my->guest) {

			$this->info->set(JText::_('COM_EASYBLOG_YOU_ARE_ALREADY_LOGIN'), 'error');

			return $this->app->redirect(EBR::_('index.php?option=com_easyblog'));
		}

		// Determines if there's any return url
		$return = $this->input->get('return', '');

		if (empty($return)) {
			$return = base64_encode('index.php?option=com_easyblog');
		}

		$this->set('return', $return);
		parent::display('login/default');
	}
}
