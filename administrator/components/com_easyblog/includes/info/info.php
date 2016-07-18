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

class EasyBlogInfo
{
	public function __construct()
	{
		$app 		= JFactory::getApplication();
		$this->app 	= $app;
	}

	/**
	 * Gets the namespace
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getNamespace()
	{
		// Determines if we are on admin view
		$admin 	= $this->app->isAdmin();
		$path 	= $admin ? 'admin' : 'site';

		$namespace 	= EBLOG_SESSION_NAMESPACE . '.' . $path;

		return $namespace;
	}
	/**
	 * Sets a message in the queue.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	stdClass	Stdclass object.
	 * @return
	 */
	public function set($message = '' , $class = '' )
	{
		$session 	= JFactory::getSession();
		
		// Determines if we are on admin view
		$admin 	= $this->app->isAdmin();
		$admin 	= $admin ? 'admin' : 'site';

		if (empty($message) && empty($class)) {
			return;
		}

		$obj 			= new stdClass();
		$obj->message 	= $message;
		$obj->type 		= $class;

		$data   	= serialize( $obj );

		$messages	= $session->get( 'messages' , array() , $this->getNamespace());
		$messages[]	= $data;

		$session->set( 'messages' , $messages , $this->getNamespace());
	}

	/**
	 * Generates a message in html.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function html()
	{
		$output   	= '';
		$session 	= JFactory::getSession();
		$messages	= $session->get( 'messages' , array() , $this->getNamespace());

		// clear it from the session.
		$session->clear( 'messages' , $this->getNamespace());

		// If there's nothing stored in the session, ignore this.
		if (!$messages) {
			return;
		}

		foreach ($messages as $message) {
			$data 		= unserialize( $message );

			if (!is_object($data)) {
				$obj			= new stdClass();
				$obj->message	= $data;
				$obj->type		= 'info';

				$data = $obj;
			}

			$theme 	= EB::template();

			$theme->set( 'content' 	, JText::_($data->message));
			$theme->set( 'class'	, $data->type );

			$output 	.= $theme->output( 'admin/info/default' );
		}

		return $output;
	}
}
