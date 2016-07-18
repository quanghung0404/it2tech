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

class SocialInfo
{
	static $instance	= null;

	/**
	 * Object initialisation for the class to fetch the info object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param   null
	 * @return  SocialInfo   The person object.
	 */
	public static function getInstance()
	{
		if( is_null( self::$instance ) )
		{
			self::$instance	= new self();
		}

		return self::$instance;
	}

	public function debug()
	{
		return __FILE__;
	}

	/**
	 * Sets a message in the queue.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object/false	$obj		The standard object class of the message to set, or false to use subsequent parameter.
	 * @param	string			$message	The message to set.
	 * @param	string			$class		The type of the message.
	 * @param	string			$namespace	The namespace of the message.
	 * @return
	 */
	public function set($obj, $message = '', $class = '', $namespace = null)
	{
		$session = JFactory::getSession();

		if ($obj === false && empty($message) && empty($class)) {
			return;
		}

		if (!$obj) {
			$obj = new stdClass();
			$obj->message = JText::_($message);
			$obj->type = $class;
		}

		$data = serialize($obj);

		$messages	= $session->get( 'messages' , array() , SOCIAL_SESSION_NAMESPACE );

		// Namespacing purposes to prevent duplication
		// Without namespacing (backwards/legacy), messages will just get queued indefinitely
		// With namespacing, only 1 instance of the same message should exist
		if (empty($namespace)) {
			$messages[]	= $data;
		} else {
			$messages[$namespace] = $data;
		}

		$session->set( 'messages' , $messages , SOCIAL_SESSION_NAMESPACE );
	}

	/**
	 * Generates a message in html.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function toHTML()
	{
		$output   	= '';
		$session 	= JFactory::getSession();
		$messages	= $session->get( 'messages' , array() , SOCIAL_SESSION_NAMESPACE );

		// clear it from the session.
		$session->clear( 'messages' , SOCIAL_SESSION_NAMESPACE );

		// If there's nothing stored in the session, ignore this.
		if( !$messages )
		{
			return;
		}

		foreach( $messages as $message )
		{
			$data 		= unserialize( $message );

			if(! is_object( $data ) )
			{
				$obj = new stdClass();
				$obj->message = $data;
				$obj->type = 'info';

				$data = $obj;
			}

			$theme 		= FD::themes();

			$theme->set( 'content' 	, $data->message );
			$theme->set( 'class'	, $data->type );

			$output 	.= $theme->output( 'site/info/default' );
		}

		return $output;
	}
}
