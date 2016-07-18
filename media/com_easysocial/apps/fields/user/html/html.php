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

FD::import('admin:/includes/fields/dependencies');

class SocialFieldsUserHtml extends SocialFieldItem
{
	public function onRegister()
	{
		return $this->render();
	}

	public function onEdit()
	{
		return $this->render();
	}

	public function onSample()
	{
		return $this->render();
	}

	public function onDisplay()
	{
		return $this->render();
	}

	public function render()
	{
		$content = $this->params->get('html');

		$this->set('content', $content);

		return $this->display();
	}


}
