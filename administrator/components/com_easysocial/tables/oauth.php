<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( 'JPATH_BASE' ) or die( 'Unauthorized Access' );

// Load parent's table
FD::import( 'admin:/tables/table' );

class SocialTableOauth extends SocialTable
{
	/**
	 * The unique id for this record.
	 * @var int
	 */
	public $id		= null;

	/**
	 * The unique oauth id for this record.
	 * @var int
	 */
	public $oauth_id 	= null;

	/**
	 * The unique item id for this record.
	 * @var int
	 */
	public $uid 	= null;

	/**
	 * The unique item type for this record.
	 * @var string
	 */
	public $type 	= null;

	/**
	 * The client type. E.g: (facebook,twitter,linkedin)
	 * @var string
	 */
	public $client 	= null;

	/**
	 * The valid user token
	 * @var string
	 */
	public $token	= null;

	/**
	 * The valid secret token
	 * @var string
	 */
	public $secret	= null;

	/**
	 * The date the request has been granted
	 * @var datetime
	 */
	public $created = null;

	/**
	 * The date the token expires
	 * @var datetime
	 */
	public $expires	= null;

	/**
	 * Determines if we should sync the user's stream over.
	 * @var string
	 */
	public $pull	= null;

	/**
	 * Determines if we should push the story to facebook when the user posts a new story
	 * @var string
	 */
	public $push	= null;

	/**
	 * The raw params in json format for this record.
	 * @var string
	 */
	public $params	= null;

	/**
	 * The datetime this item was last pulled.
	 * @var datetime
	 */
	public $last_pulled = null;

	/**
	 * The datetime this item was last pushed.
	 * @var datetime
	 */
	public $last_pushed = null;

	/**
	 * The JSON string of permissions
	 * @var string
	 */
	public $permissions = null;

	public function __construct( $db )
	{
		parent::__construct('#__social_oauth', 'id', $db);
	}

	/*
	 * Loads a specific record given the client type and client's id.
	 *
	 * @param   string  $client     The client type. E.g: 'facebook' , 'twitter'
	 * @param   string  $clientId   The client's unique id.
	 *
	 * @return  boolean True on success, false otherwise.
	 */
	public function loadByClient( $client , $clientId )
	{
	    $db 	= FD::db();
	    $query  = 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) . ' '
	            . 'WHERE ' . $db->nameQuote( 'client' ) . '=' . $db->Quote( $client ) . ' '
	            . 'AND ' . $db->nameQuote( 'client_uid' ) . '=' . $db->Quote( $clientId ) . ' '
				. 'LIMIT 1';
		$db->setQuery( $query );
		$result	= $db->loadObject();

		return parent::bind( $result );
	}

	/**
	 * Tries to find the oauth record given a username
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadByUsername( $username )
	{
		$model 	= FD::model( 'OAuth' );
		$row 	= $model->getRow( array( 'username' => $username ) );

		if( !$row )
		{
			return false;
		}

		$state 	= parent::bind( $row );

		return $state;
	}

	/*
	 * Generates a similar login credentials for login purposes.
	 *
	 */
	public function getLoginCredentials()
	{
		$db 	= FD::db();
		$query  = 'SELECT a.' . $db->nameQuote( 'username' ) . ', a.' . $db->nameQuote( 'password' ) . ' '
		        . 'FROM ' . $db->nameQuote( '#__users' ) . ' AS a '
		        . 'INNER JOIN ' . $db->nameQuote( '#__social_nodes' ) . ' AS b '
		        . 'ON a.' . $db->nameQuote( 'id' ) . ' = b.'. $db->nameQuote( 'uid' ) . ' '
		        . 'WHERE b.' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $this->node_id ) . ' '
				. 'LIMIT 1';
		$db->setQuery( $query );
		$credentials	= $db->loadAssoc();

		return $credentials;
	}

	/**
	 * Binds the token object to this table
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialOauth
	 * @return
	 */
	public function bindToken( SocialOauth $client )
	{
		$access 	= $client->getAccess();

		$this->token	= $access->token;
		$this->secret 	= $access->secret;
	}

	/**
	 * Determines if the user has permissions
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasPermissions( $permission )
	{
		$data 	= FD::makeArray( $this->permissions );

		return in_array( $permission , $data );
	}
}
