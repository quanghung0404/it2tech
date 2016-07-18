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

jimport( 'joomla.html.parameter' );

/**
 * Mailchimp library
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialMailchimp
{
	/**
	 * The mailer object.
	 * @var SocialMailer
	 */
	static $instance = null;

	private $key	= null;
	private $url	= 'api.mailchimp.com/1.3/';

	public function __construct( $key )
	{
		$this->key 	= $key;

		if( $this->key )
		{
			$datacenter	= explode( '-' , $this->key );

			$this->url	= 'http://' . $datacenter[1] . '.' . $this->url;
		}
	}


	/**
	 * This is a singleton object in which it can / should only be instantiated using the getInstance method.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function factory( $key )
	{
		return new self( $key );
	}

	/**
	 * Allows caller to subscribe to a newsletter
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function subscribe( $listId , $email , $firstName , $lastName = '' , $welcomeEmail = false )
	{
		$firstName 	= urlencode( $firstName );
		$lastName 	= urlencode( $lastName );

		$url	= $this->url . '?method=listSubscribe';
		$url	= $url . '&apikey=' . $this->key;
		$url	= $url . '&id=' . $listId;
		$url	= $url . '&output=json';
		$url	= $url . '&email_address=' . $email;
		$url	= $url . '&merge_vars[FNAME]=' . $firstName;
		$url	= $url . '&merge_vars[LNAME]=' . $lastName;
		$url	= $url . '&merge_vars[email_type]=html';
		$url	= $url . '&merge_vars[send_welcome]=' . $welcomeEmail;

		$ch		= curl_init( $url );

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		$result		= curl_exec($ch);
		curl_close($ch);

		return true;
	}

	/**
	 * Unsubscribe user from a list
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unsubscribe($listId, $email)
	{
		$url	= $this->url . '?method=listUnsubscribe';
		$url	= $url . '&apikey=' . $this->key;
		$url	= $url . '&id=' . $listId;
		$url	= $url . '&output=json';
		$url	= $url . '&email_address=' . $email;
		$url	= $url . '&send_notify=true';

		$ch		= curl_init( $url );

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		$result		= curl_exec($ch);
		curl_close($ch);
// dump($result);
		return true;
	}
}
