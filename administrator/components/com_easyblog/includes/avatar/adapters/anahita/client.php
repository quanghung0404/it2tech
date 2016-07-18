<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogAvatarAnahita
{
	public function exists()
	{
		if (!class_exists('KFactory')) {
			return false;
		}

		return true;
	}

	public function getAvatar($profile)
	{
		$person	= KFactory::get('lib.anahita.se.person.helper')->getPerson($profile->id);

		$avatar = $person->getAvatar()->getURL(AnSeAvatar::SIZE_MEDIUM);

		return $avatar;
	}
}