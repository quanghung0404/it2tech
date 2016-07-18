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

FD::import( 'admin:/tables/table' );
FD::import( 'admin:/includes/indexer/indexer' );

/**
 * Object relation mapping for users.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialTableUsers extends SocialTable
	implements ISocialIndexerTable
{
	/**
	 * The unique user id.
	 * @var	int
	 */
	public $user_id 	= null;

	/**
	 * The user's alias.
	 * @var	int
	 */
	public $alias 		= null;

	/**
	 * The state of the user. Since we can't just rely on the `block` column in `#__users` because
	 * there could be a couple of states, including pending.
	 * @var	int
	 */
	public $state 		= null;

	/**
	 * Any parameter that needs to be stored for this user. This should not be search-able.
	 * Used mainly for retrieve / store purposes only.
	 * @var	string
	 */
	public $params		= null;

	/**
	 * Number of connections a user has.
	 * @var	int
	 */
	public $connections	= null;

	/**
	 * The type of user.
	 * @var	int
	 */
	public $type	= 'joomla';

	/**
	 * The user's permalink
	 * @var string
	 */
	public $permalink	= '';

	/**
	 * The user's authentication code.
	 * @var string
	 */
	public $auth	= '';

	/**
	 * Determines the number of fields completed for this user in this profile.
	 * @var integer
	 */
	public $completed_fields = 0;

	/**
	 * Determines if user already sent the email for inactive account.
	 * @var integer
	 */
	public $reminder_sent = 0;

	/**
	 * Determines if user required to force reset password.
	 * @var integer
	 */
	public $require_reset = 0;

	/**
	 * Constructor method for this class.
	 *
	 * @access	public
	 * @param	JDatabase	$db		The database object.
	 * @return	null
	 */
	public function __construct( $db )
	{
		parent::__construct( '#__social_users' , 'user_id' , $db );
	}

	/**
	 * Loads a record by a given user id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadByUser( $id )
	{
		$db 		= FD::db();
		$query 		= array();

		$query[]	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl );
		$query[]	= 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $id );

		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );
		$data 		= $db->loadObject();

		if( !$data )
		{
			return false;
		}

		return parent::bind( $data );
	}

	/**
	 * Override parent's behavior of store because there's no auto increment on the primary key.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 */
	public function store( $updateNulls = false )
	{
		// Update values.
		$db 			= FD::db();

		$obj 			= new stdClass();

		$properties 	= get_object_vars( $this );

		// we need to clear up some extra keys that might get added from
		// some third party system plugins.
		unset($properties['privacy']);

		foreach( $properties as $key => $value )
		{
			if( stripos( $key , '_' ) !== 0 )
			{
				$obj->$key 		= $value;
			}
		}

		// Ensure that there's a record.
		$exists 		= $this->exists( $this->user_id );

		if( $exists )
		{
			$state	= $db->updateObject( $this->_tbl , $obj , 'user_id' );

			if( !$state )
			{
				$this->setError( $db->getError() );
			}

			return $state;
		}

		$state	= $db->insertObject( $this->_tbl , $obj );

		if( !$state )
		{
			$this->setError( $db->getError() );

			return $state;
		}

		return $state;
	}

	/**
	 * Determines if a particular record exists.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The unique user id.
	 * @return	bool		True if exists, false otherwise.
	 */
	public function exists( $id )
	{
		$db 		= FD::db();
		$query		= array();
		$query[]	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( $this->_tbl );
		$query[]	= 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $id );

		$query		= implode( ' ' , $query );

		$db->setQuery( $query );

		// If the record does not exist yet, create it.
		$exists		= $db->loadResult() ? true : false;

		return $exists;
	}

	/**
	 * Initializes the user's record in this table. Since new user's are not created automatically, we need
	 * to map their default values here.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id.
	 * @return	bool	True if created, false if user already exists.
	 */
	public function init( $id )
	{
		$db 	= FD::db();

		$exists = $this->exists( $id );
		if( !$exists )
		{
			// @TODO: Store any custom default values here.
			$obj 			= new stdClass();
			$obj->user_id 	= $id;

			// If user is created on the site but doesn't have a record, we should treat it as published.
			$obj->state  	= SOCIAL_STATE_PUBLISHED;

			$db->insertObject( '#__social_users' , $obj );

			return true;
		}

		return false;
	}

	public function syncIndex()
	{
		// do nothing. this function is to satisfy the implementation of indexer interface. the actual indexing located at /uncludes/user.php
	}


	public function deleteIndex()
	{
		$indexer = FD::get( 'Indexer' );
		$indexer->delete( $this->user_id, SOCIAL_INDEXER_TYPE_USERS);
	}



}
