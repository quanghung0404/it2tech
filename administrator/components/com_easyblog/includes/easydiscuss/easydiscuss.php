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

class EasyBlogEasyDiscuss
{
	private $exists	= false;
	private $config = null;

	public function __construct()
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_easyblog', JPATH_ROOT);

		$this->exists = $this->exists();
		$this->config = EB::config();
	}

	/**
	 * Determines if EasyDiscuss exists
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function exists()
	{
		jimport('joomla.filesystem.file');

		$file = JPATH_ROOT . '/administrator/components/com_easydiscuss/includes/easydiscuss.php';

		if (!JFile::exists($file)) {
			return false;
		}
		
		include_once($file);

		return true;
	}

	/**
	 * Adds a point for the particular user based on it's actions.
	 *
	 * @access	public
	 * @param	string	$action 	The action string.
	 * @param	int 	$targetId	The user's id.
	 * @return	boolean 			True on success, false otherwise.
	 */
	public function addPoint( $action , $targetId )
	{
		if (!$this->exists) {
			return false;
		}

		if (!$this->config->get('integrations_easydiscuss_points')) {
			return false;
		}

		return ED::points()->assign($action, $targetId);
	}

	/**
	 * Adds a badge for the particular user based on it's actions.
	 *
	 * @access	public
	 * @param	string	$action 	The action string.
	 * @param	int 	$targetId	The user's id.
	 * @return	boolean 			True on success, false otherwise.
	 */
	public function addBadge($action, $targetId)
	{
		if (!$this->exists) {
			return false;
		}

		if (!$this->config->get('integrations_easydiscuss_badges')) {
			return false;
		}

		return ED::badges()->assign($action, $targetId);
	}

	public function addRank($targetId)
	{
		if (!$this->exists) {
			return false;
		}

		return ED::ranks()->assignRank($targetId);
	}

	/**
	 * Add's a history in EasyDiscuss which will be later reused to calculate their badges or
	 * achievements.
	 *
	 * @access	public
	 * @param	string	$action		The unique action string.
	 * @param	int 	$targetId	The target user's id.
	 * @param	string 	$title 		The title of the action / history.
	 * @return 	boolean 			True on success, false otherwise.
	 */
	public function log( $action , $targetId , $title )
	{
		if (!$this->exists) {
			return false;
		}

		if (!$this->config->get('integrations_easydiscuss_badges') && !$this->config->get('integrations_easydiscuss_points')) {
			return false;
		}

		return ED::history()->log($action, $targetId, $title);
	}

	public function readNotification( $targetId , $notificationType )
	{
		if (!$this->exists) {
			return false;
		}

		// @rule: Get current logged in user.
		$my = JFactory::getUser();

		// @rule: Clear up any notifications that are visible for the user.
		$notifications = ED::model('Notification');
		$notifications->markRead($my->id, $targetId, $notificationType);
	}

	/**
	 * Inserts a new notification for EasyDiscuss
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function insertNotification($command, $post)
	{
		if ($command = 'new.blog') {

			if (!$this->config->get('integrations_easydiscuss_notification_blog')) {
				return false;
			}

			$link = $post->getExternalBlogLink('index.php?option=com_easyblog&view=entry&id='. $post->id);
			
			// Get list of users who subscribed to this blog.
			$targets = $post->getRegisteredSubscribers('new', array($post->created_by));

			// Get the author of the post
			$author = $post->getAuthor();

			return $this->addNotification($post, JText::sprintf('COM_EASYBLOG_EASYDISCUSS_NOTIFICATIONS_NEW_BLOG', $author->getName(), $post->title), EBLOG_NOTIFICATIONS_TYPE_BLOG, $targets, $post->created_by, $link);
		}
	}

	/**
	 * Adds a notification item in EasyDiscuss
	 *
	 * @access	public
	 * @param 	TableBlog	$blog 	The blog table.
	 */
	public function addNotification(&$blog , $title , $type , $target , $author , $link )
	{
		if (!$this->exists) {
			return false;
		}

		if (!is_array($target)) {
			$target 	= array( $target );
		}

		if ($type == EBLOG_NOTIFICATIONS_TYPE_COMMENT && !$this->config->get('integrations_easydiscuss_notification_comment')) {
			return false;
		}

		if ($type == EBLOG_NOTIFICATIONS_TYPE_COMMENT && !$this->config->get('integrations_easydiscuss_notification_comment_follower')) {
			return false;
		}

		foreach ($target as $targetId) {
			$notification = ED::table('Notifications');

			$notification->bind( array(
					'title'		=> $title,
					'cid'		=> $blog->id,
					'type'		=> $type,
					'target'	=> $targetId,
					'author'	=> $author,
					'permalink'	=> $link,
					'favicon'	=> JURI::root() . 'components/com_easyblog/assets/images/discuss_notifications/' . $type . '.png',
					'component'	=> 'com_easyblog'
				) );
			$notification->store();
		}

	}
}
