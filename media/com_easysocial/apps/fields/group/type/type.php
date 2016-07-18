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

// Include the fields library
FD::import( 'fields:/user/textarea/textarea' );

class SocialFieldsGroupType extends SocialFieldItem
{
	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 */
	public function onSample()
	{
		return $this->display();
	}

	/**
	 * Displays the field input for user when they register their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array	The post data
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 */
	public function onRegister( &$post )
	{
		$config = FD::config();

		$value = isset($post['group_type']) ? $post['group_type'] : $this->params->get('default');

        $this->set('value', $value);

		return $this->display();
	}

	/**
	 * Displays the field input for user when they register their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array	The post data
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 */
	public function onEdit( &$post , &$group )
	{
		$value = 1;

		if (isset($group) && $group->isOpen()) {
			$value = 1;
		}
		if (isset($group) && $group->isClosed()) {
			$value = 2;
		}
		if (isset($group) && $group->isInviteOnly()) {
			$value = 3;
		}

		$this->set('value', $value);
		return $this->display();
	}

	/**
	 * Executes before the group is created
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onEditBeforeSave( &$data , &$group )
	{
		$type 	= isset( $data[ 'group_type' ] ) ? $data[ 'group_type' ] : 1;

		$changed = $group->type != $type;

		if ($changed) {
			$data['group_type_changed'] = true;
		}

		// Set the title on the group
		$group->type = $type;
	}

	public function onEditAfterSave(&$data, &$group)
	{
		if (empty($data['group_type_changed'])) {
			return true;
		}

		// Need to manually change:
		// 1. Group events type
		// 2. Stream item privacy, including group events - cluster_access

		$db = FD::db();
		$sql = $db->sql();

		// First get all the group events first
		$sql->select('#__social_clusters', 'a');
		$sql->column('a.id');
		$sql->leftjoin('#__social_events_meta', 'b');
		$sql->on('b.cluster_id', 'a.id');
		$sql->where('b.group_id', $group->id);

		$db->setQuery($sql);
		$clusterids = $db->loadColumn();

		if (!empty($clusterids)) 
		{
			$sql->clear();
			$sql->update('#__social_clusters');
			$sql->set('type', $group->type);
			$sql->where('id', $clusterids, 'IN');

			$db->setQuery($sql);
			$db->query();

			// Merge in the group id
			$clusterids[] = $group->id;

			$sql->clear();
			$sql->update('#__social_stream');
			$sql->set('cluster_access', $group->type);
			$sql->where('cluster_id', $clusterids, 'IN');

			$db->setQuery($sql);
			$db->query();
		}

		unset($data['group_type_changed']);
	}

	/**
	 * Executes before the group is created
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onRegisterBeforeSave( &$data , &$group )
	{
		$type 	= isset( $data[ 'group_type' ] ) ? $data[ 'group_type' ] : 1;

		// Set the title on the group
		$group->type 	= $type;
	}
}
