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

class EasySocialModelSubscriptions extends EasySocialModel
{
	private $data			= null;
	protected $pagination		= null;

	protected $limitstart 	= null;
	protected $limit 		= null;

	function __construct()
	{
		parent::__construct( 'subscriptions' );
	}

	private function _getSubcribers( $uuid, $uType, $userId = null )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_subscriptions' );
		$sql->column( 'created_by' );
		$sql->where( 'type', $uType );
		$sql->where( 'uid', $uuid );

		if( ! is_null( $userId ) )
		{
			$sql->where( 'user_id', $userId );
		}

		$sql->order( 'id', 'desc' );

		$db->setQuery( $sql );
		$list   = $db->loadColumn();

		return $list;
	}

	private function _getSubscribersCount( $uuid, $uType )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_subscriptions' );
		$sql->where( 'type', $uType );
		$sql->where( 'uid', $uuid );

		$db->setQuery( $sql->getTotalSql() );
		$cnt   = $db->loadResult();
		return $cnt;
	}


	/**
	 * check if user has already subscribed to an item.
	 *
	 * @param	string $contentType
	 * @param	int $contentId
	 * @param	string $email
	 * @param	int $userId
	 *
	 * @return subscription id
	 *
	 */
	public function isFollowing( $uid , $type , $userId )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_subscriptions' );
		$sql->column( 'id' );
		$sql->where( 'uid' , $uid );
		$sql->where( 'type' , $type );
		$sql->where( 'user_id' , $userId );

		$db->setQuery( $sql );

		$isFollower = (bool) $db->loadResult();

		return $isFollower;
	}
}
