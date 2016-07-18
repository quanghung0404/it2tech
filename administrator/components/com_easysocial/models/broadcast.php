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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

jimport('joomla.application.component.model');

FD::import( 'admin:/includes/model' );

class EasySocialModelBroadcast extends EasySocialModel
{
	public function __construct($config = array())
	{
		parent::__construct('broadcast', $config);
	}

	/**
	 * Retrieves a list of broadcasts created on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBroadcasts($userId)
	{
		$db  = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_broadcasts');
		$sql->where('target_id', $userId);
		$sql->where('target_type', SOCIAL_TYPE_USER);
		$sql->where('state', 1);
		$sql->order('created', 'DESC');

		$db->setQuery($sql);

		$result = $db->loadObjectList();

		if (!$result) {
			return false;
		}

		$broadcasts = array();

		foreach ($result as $row) {

			$broadcast = FD::table('Broadcast');
			$broadcast->bind($row);

			// When the broadcasts are alredy retrieved from the system, it should be marked as read.
			// Otherwise it would keep on spam the user's screen.
			$broadcast->markAsRead();

			$broadcasts[]	= $broadcast;
		}

		return $broadcasts;
	}

	/**
	 * Broadcast a message to a set of profiles on the site.
	 *
	 * @since	1.3
	 * @access	public
	 * @param 	int 	The profile id to target. 0 for all
	 * @param	string  The message to be broadcasted
	 * @param 	string  The title for the announcement
	 * @return
	 */
	public function broadcast($id, $content, $createdBy, $title = '', $link = '')
	{
		$db  = FD::db();
		$sql = $db->sql();

		$query  = array();

		$query[] = 'INSERT INTO ' . $db->quoteName('#__social_broadcasts');
		$query[] = '(`target_id`,`target_type`,`title`,`content`,`link`,`state`,`created`,`created_by`)';

		// Get the creation date
		$date = FD::date();

		$query[] = 'SELECT';
		$query[] = '`user_id`,' . $db->Quote(SOCIAL_TYPE_USER) . ',' . $db->Quote($title) . ',' . $db->Quote($content) . ',' . $db->Quote($link) . ',1,' . $db->Quote($date->toSql()) . ',' . $db->Quote($createdBy);
		$query[] = 'FROM ' . $db->quoteName('#__social_profiles_maps');
		$query[] = 'WHERE 1';

		if (!empty($id)) {
			$query[] = 'AND ' . $db->quoteName('profile_id') . '=' . $db->Quote($id);
		}

		// Exclude the broadcaster because it would be pretty insane if I am spamming myself
		$my    = FD::user();
		$query[] = 'AND `user_id` !=' . $db->Quote($my->id);


		$query = implode(' ', $query);

		$sql->raw($query);

		$db->setQuery($sql);

		$state = $db->Query();

		if (!$state) {
			return $state;
		}
		
		// Get the id of the new broadcasted item
		$id = $db->insertid();

		return $id;
	}

	/**
	 * Notify a broadcast a message to a set of profiles on the site.
	 *
	 * @since	1.3
	 * @access	public
	 * @param 	int 	The profile id to target. 0 for all
	 * @param	string  The message to be broadcasted
	 * @return
	 */
	public function notifyBroadcast($id, $title, $content, $link, $createdBy, $streamItem)
	{
		$db  = FD::db();
		$sql = $db->sql();

		$query  = array();

		$query[] = 'SELECT';
		$query[] = '`user_id`';
		$query[] = 'FROM ' . $db->quoteName('#__social_profiles_maps');
		$query[] = 'WHERE 1';

		if (!empty($id)) {
			$query[] = 'AND ' . $db->quoteName('profile_id') . '=' . $db->Quote($id);
		}

		// Exclude the broadcaster because it would be pretty insane if I am spamming myself
		$my = FD::user();
		$query[] = 'AND `user_id` !=' . $db->Quote($my->id);

		$query = implode(' ', $query);

		$sql->raw($query);

		$db->setQuery($sql);

		$results = $db->loadObjectList();

		$recipients = array();

		foreach ($results as $result) {
			$recipients[] = FD::user($result);
		}

		$systemOptions = array('uid' => $my->id, 
						 'actor_id' => $my->id,
						 'title' => $title, 
						 'content' => $content,
						 'type' => 'broadcast',
						 'url' => FRoute::stream(array('layout' => 'item', 'id' => $streamItem->uid, 'sef' => false)));

		$emailOptions = array(
				'title'		=> 'APP_USER_BROADCAST_EMAILS_NEW_BROADCAST_TITLE',
				'template'	=> 'apps/user/broadcast/new.broadcast',
				'permalink'	=> FRoute::stream(array('layout' => 'item', 'id' => $streamItem->uid, 'sef' => false))
			);

		$state = Foundry::notify( 'broadcast.notify' , $recipients , $emailOptions , $systemOptions );

		if ($state) {

			// Create an empty broadcast record for stream item
			$query  = array();

			// Get the creation date
			$date = FD::date();

			$query[] = 'INSERT INTO ' . $db->quoteName('#__social_broadcasts');
			$query[] = '(`target_id`,`target_type`,`title`,`content`,`link`,`state`,`created`,`created_by`) VALUES';
			$query[] = '(' . $db->Quote('') . ','. $db->Quote('') .',' . $db->Quote($title) . ',' . $db->Quote($content) . ',' . $db->Quote($link) . ',1,' . $db->Quote($date->toSql()) . ',' . $db->Quote($createdBy) . ')';
			
			$query = implode(' ', $query);

			$sql->raw($query);

			$db->setQuery($sql);

			$state = $db->Query();

			if (!$state) {
				return $state;
			}
			
			// Get the id of the new broadcasted item
			$id = $db->insertid();

			return $id;
		}

		return $state;
		
	}
}
