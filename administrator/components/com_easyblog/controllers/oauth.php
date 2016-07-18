<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'oauth.php' );

class EasyBlogControllerOauth extends EasyBlogController
{
	/**
	 * Constructor
	 *
	 * @since 0.1
	 */
	function __construct()
	{
		// Include the tables in path
		JTable::addIncludePath( EBLOG_TABLES );

		parent::__construct();
	}

	function request()
	{
		$mainframe 	= JFactory::getApplication();

		if(! EasyBlogHelper::isLoggedIn())
		{
			$mainframe->enqueueMessage( JText::_('COM_EASYBLOG_YOU_MUST_LOGIN_FIRST') , 'error' );
			$this->setRedirect( EBR::_('index.php?option=com_easyblog' , false ) );
			return;
		}

		$redirect	= JRequest::getVar( 'redirect' , '' );
		$type		= JRequest::getCmd( 'type' );

		if( !empty( $redirect ) )
		{
			$redirect 	= '&redirect=' . $redirect;
		}

		$userId     = JRequest::getVar( 'id' );

		// Flickr integration does not require user id.
		if( empty( $userId ) )
		{
			$mainframe->enqueueMessage( JText::_('Error, User not found.') , 'error' );
			$redirect	= JRoute::_( 'index.php?option=com_easyblog&view=users', false );
			$this->setRedirect( $redirect );
			return;
		}

		$call 		= JRequest::getWord( 'call' );
		$callUri 	= !empty( $call ) ? '&call=' . $call . '&id=' . $userId : '&id=' . $userId;

		
		$config		= EasyBlogHelper::getConfig();
		$key		= $config->get( 'integrations_' . $type . '_api_key' );
		$secret		= $config->get( 'integrations_' . $type . '_secret_key' );
		$callback	= rtrim( JURI::root() , '/' ) . '/administrator/index.php?option=com_easyblog&c=oauth&task=grant&type=' . $type . $redirect . $callUri;

		$consumer	= EasyBlogOauthHelper::getConsumer( $type , $key , $secret , $callback );
		$request	= $consumer->getRequestToken();


		if( empty( $request->token ) || empty( $request->secret ) )
		{
			$mainframe->enqueueMessage( JText::_( 'COM_EASYBLOG_OAUTH_KEY_INVALID') , 'error' );
			$redirect	= JRoute::_( 'index.php?option=com_easyblog&view=users', false );
			$this->setRedirect( $redirect);
			return;
		}

		$oauth				= EB::table('Oauth');
		$oauth->user_id		= $userId;
		$oauth->type		= $type;
		$oauth->created		= EB::date()->toMySQL();

		// Bind the request tokens
		$param 	= EB::registry();
		$param->set( 'token' , $request->token );
		$param->set( 'secret' , $request->secret );

		$oauth->request_token	= $param->toString();

		$oauth->store();

		$this->setRedirect( $consumer->getAuthorizationURL( $request->token , false , 'popup') );
	}

	/**
	 * This will be a callback from the oauth client.
	 * @param	null
	 * @return	null
	 **/
	public function grant()
	{
		$type		= JRequest::getCmd( 'type' );
		$userId		= JRequest::getVar( 'id' );
		$mainframe	= JFactory::getApplication();
		$config		= EasyBlogHelper::getConfig();
		$key		= $config->get( 'integrations_' . $type . '_api_key' );
		$secret		= $config->get( 'integrations_' . $type . '_secret_key' );
		$my			= JFactory::getUser( $userId );

		$redirect		= JRequest::getVar( 'redirect' , '' );
		$redirectUri 	= !empty( $redirect ) ? '&redirect=' . $redirect : '';

		// @task: Let's see if caller wants us to go to any specific location or not.
		if( !empty( $redirect ) )
		{
			$redirect	= base64_decode( $redirect );
		}

		if(! EasyBlogHelper::isLoggedIn())
		{
			$mainframe->enqueueMessage( JText::_('COM_EASYBLOG_YOU_MUST_LOGIN_FIRST') , 'error' );
			$this->setRedirect( JRoute::_('index.php?option=com_easyblog&view=users' , false ) );
			return;
		}

		$oauth		= EB::table('Oauth');
		$loaded		= $oauth->loadByUser( $my->id , $type );

		$denied     = JRequest::getVar( 'denied' , '' );


		$call 		= JRequest::getWord( 'call' );
		$callUri 	= !empty( $call ) ? '&call=' . $call . '&id=' . $my->id : '&id=' . $my->id;


		if( !empty( $denied ) )
		{
		    $oauth->delete();
			$mainframe->enqueueMessage( JText::_('COM_EASYBLOG_OAUTH_DENIED_ERROR') , 'error' );
			$redirect	= JRoute::_( 'index.php?option=com_easyblog&view=users', false );
			$this->setRedirect( $redirect , false );
			return;
		}

		if( !$loaded )
		{
			$mainframe->enqueueMessage( JText::_('COM_EASYBLOG_OAUTH_UNABLE_TO_LOCATE_RECORD') , 'error' );
			$redirect	= JRoute::_( 'index.php?option=com_easyblog&view=users', false );
			$this->setRedirect( $redirect , false );
			return;
		}

		$request 	= EB::registry($oauth->request_token);
		$callback	= rtrim( JURI::root() , '/' ) . '/administrator/index.php?option=com_easyblog&c=oauth&task=grant&type=' . $type . $redirect . $callUri;


		$consumer	= EasyBlogOauthHelper::getConsumer( $type , $key , $secret , $callback );
		$verifier	= $consumer->getVerifier();

		if( empty( $verifier ) )
		{
			// Since there is a problem with the oauth authentication, we need to delete the existing record.
			$oauth->delete();

			JError::raiseError( 500 , JText::_( 'COM_EASYBLOG_INVALID_VERIFIER_CODE' ) );
		}

		$access		= $consumer->getAccess( $request->get( 'token' ) , $request->get( 'secret' ) , $verifier );

		if( !$access || empty( $access->token ) || empty( $access->secret ) )
		{
			// Since there is a problem with the oauth authentication, we need to delete the existing record.
			$oauth->delete();

			$mainframe->enqueueMessage( JText::_('COM_EASYBLOG_OAUTH_ACCESS_TOKEN_ERROR'), 'error' );
			$this->setRedirect( $redirect , false );
			return;
		}

		$param 	= EB::registry();
		$param->set( 'token' 	, $access->token );
		$param->set( 'secret'	, $access->secret );

		if( isset( $access->expires ) )
		{
			$param->set( 'expires' , $access->expires );
		}

		$oauth->access_token	= $param->toString();
		$oauth->params			= $access->params;

		$oauth->store();


		$mainframe->enqueueMessage( JText::_('Application revoked successfully.') );
		$url 	=	JRoute::_('index.php?option=com_easyblog&c=user&id=' . $my->id . '&task=edit', false);

		if( !empty( $redirect ) )
		{
			$url 	= $redirect;
		}

		// @task: Let's see if the oauth client
		if( !empty( $call ) )
		{
			$consumer->$call();
		}
		else
		{
			$this->setRedirect( $url );
		}
	}

	/**
	 * Responsible to revoke access for the specific oauth client
	 *
	 * @param	null
	 * @return	null
	 **/
	public function revoke()
	{
		$mainframe	= JFactory::getApplication();
		$id			= JRequest::getCmd( 'id' );
		$return		= JRequest::getCmd( 'return', 'user' );
		$activechild= JRequest::getCmd( 'activechild', '' );
		$my			= JFactory::getUser($id);
		$url		= JRoute::_('index.php?option=com_easyblog&view=dashboard&layout=profile' , false );
		$type		= JRequest::getWord( 'type' );
		$config		= EasyBlogHelper::getConfig();

		if( $my->id == 0 )
		{
			$mainframe->enqueueMessage( JText::_('COM_EASYBLOG_OAUTH_INVALID_USER') , 'error');
			$this->setRedirect( $return );
		}

		$oauth		= EB::table('OAuth');
		$oauth->loadByUser( $my->id , $type );

		// Revoke the access through the respective client first.
		$callback	= trim(JURI::base(), "/").JRoute::_( '/index.php?option=com_easyblog&c=oauth&task=grant&type=' . $type . '&return=' . $return . '&activechild=' . $activechild . '&id=' . $id , false , true );
		$key		= $config->get( 'integrations_' . $type . '_api_key' );
		$secret		= $config->get( 'integrations_' . $type . '_secret_key' );
		$consumer	= EasyBlogOauthHelper::getConsumer( $type , $key , $secret , $callback );
		$consumer->setAccess( $oauth->access_token );

		switch($return)
		{
			case 'settings':
				$redirect = JRoute::_('index.php?option=com_easyblog&view=settings&active=social&activechild='.$activechild , false );
				break;
			case 'user':
			default:
				$redirect = JRoute::_('index.php?option=com_easyblog&c=user&id='.$id.'&task=edit' , false );
				break;
		}

		// @task: Only show errors when the user is really authenticated with the respective provider.
		if( !$consumer->revokeApp() && !empty( $oauth->access_token) )
		{
			$mainframe->enqueueMessage( JText::_('There was an error when trying to revoke your app.') , 'error');
			$this->setRedirect( $redirect );
			return;
		}
		$oauth->delete();

		$mainframe->enqueueMessage( JText::_('Application revoked successfully.') );
		$this->setRedirect( $redirect );
	}
}
