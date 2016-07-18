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

/**
 * Displays the canvas view for news app
 *
 * @since	1.2
 * @access	public
 */
class NewsViewForm extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 */
	public function display($uid = null, $docType = null)
	{
		$group = FD::group($uid);
		$editor = JFactory::getEditor();

		// Only allow group admin to create or edit news
		if (!$group->isAdmin() && !$this->my->isSiteAdmin()) {
			FD::info()->set(false, JText::_('COM_EASYSOCIAL_GROUPS_ONLY_MEMBER_ARE_ALLOWED'), SOCIAL_MSG_ERROR);
			return $this->redirect($group->getPermalink(false));
		}

		$id = JRequest::getInt('newsId');
		$news = FD::table('GroupNews');
		$news->load($id);

		FD::page()->title(JText::_('APP_GROUP_NEWS_FORM_UPDATE_PAGE_TITLE'));

		// Determine if this is a new record or not
		if (!$id) {
			$news->comments = true;
			FD::page()->title(JText::_('APP_GROUP_NEWS_FORM_CREATE_PAGE_TITLE'));
		}

		// Get app params
		$params = $this->app->getParams();

		$this->set('params', $params);
		$this->set('news', $news);
		$this->set('editor', $editor);
		$this->set('group', $group);

		echo parent::display('canvas/form');
	}
}
