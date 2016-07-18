<?php
/**
 * @package		Easyblog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Easyblog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

class EasyBlogControllerAutoposting extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask( 'applyForm' , 'saveForm' );
	}

	public function saveForm()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'autoposting' );

		$post	= JRequest::get( 'POST' );
		$type	= JRequest::getVar( 'type' );

		// We do not want these variables to end
		unset( $post[ 'task' ] );
		unset( $post[ 'controller' ] );
		unset( $post[ 'layout' ] );
		unset( $post[ 'option' ] );
		unset( $post[ 'c' ] );
		unset( $post[ 'type' ] );

		$token 	= EasyBlogHelper::getToken();

		if( isset( $post[ $token ] ) )
		{
			unset( $post[ $token ] );
		}

		if( isset( $post[ 'integrations_linkedin_company' ] ) )
		{
			$post[ 'integrations_linkedin_company' ] 	= implode( ',' , $post[ 'integrations_linkedin_company' ] );
		}

		$model	= EB::model( 'Settings' );
		$model->save( $post );

		$redirect 	= 'index.php?option=com_easyblog&view=autoposting&layout=form&type=' . $type;

		if( $this->getTask() == 'saveForm' )
		{
			$redirect 	= 'index.php?option=com_easyblog&view=autoposting';
		}

		$app 		= JFactory::getApplication();

		$message	= JText::sprintf( 'COM_EASYBLOG_OAUTH_SETTINGS_SAVED_SUCCESS' , ucfirst( $type ) );
		$app->redirect( $redirect , $message );
	}

	function save()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'autoposting' );

		$mainframe	= JFactory::getApplication();
		$current	= JRequest::getInt( 'step' );
		$layout		= JRequest::getString( 'layout' );
		$result		= $this->store();
		$step		= $current + 1;

		// @rule: Since all signon buttons are on step 2,
		// we need to check if the user is associated before allowing them to go to step 3
		if( !EasyBlogHelper::getHelper( 'OAuth' )->isAssociated( $layout ) && $step == '3' )
		{
			$mainframe->redirect( 'index.php?option=com_easyblog&view=autoposting&layout=' . $layout . '&step=2' , JText::_( 'COM_EASYBLOG_AUTOPOSTING_PLEASE_SIGN_ON_FIRST') , 'error' );
			$mainframe->close();
		}

		if( $result['type'] == 'completed' )
		{
			$mainframe->redirect( 'index.php?option=com_easyblog&view=autoposting' , $result['message'] );
			$mainframe->close();
		}

		$mainframe->redirect( 'index.php?option=com_easyblog&view=autoposting&layout=' . $layout . '&step=' . $step );
	}

	private function store()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'autoposting' );

		$step 	= JRequest::getInt( 'step' );
		$layout	= JRequest::getWord( 'layout' );

		// Retrieve the settings model.
		$model	= EB::model( 'Settings' );

		$message	= '';
		$status 	= '';
		$data 		= array();

		switch( $step )
		{
			case '1':
				// On the first step, we only store the following settings
				//
				// - Application ID
				// - Application Secret
				$appId 			= JRequest::getVar( 'integrations_' . $layout . '_secret_key' );
				$appSecret		= JRequest::getVar( 'integrations_' . $layout . '_api_key' );

				$data[ 'integrations_' . $layout . '_secret_key' ]	= $appId;
				$data[ 'integrations_' . $layout . '_api_key' ] = $appSecret;

				// This option definitely needs to be enabled, otherwise there's no point for the user accessing.
				// the setup.
				$data[ 'integrations_' . $layout ]		= 1;

				$model->save( $data );

				$message 		= JText::_( 'COM_EASYBLOG_AUTOPOST_SETTINGS_SAVED' );
				$status 		= 'success';
			break;
			case '2':
				// On the second step, we only store the following settings
				//
				// - Post as Facebook page (Optional only for Facebook)
				// --- Facebook page (Optional only for Facebook)
				// - Auto posting when new blog post is created
				// - Auto posting when existing blog post is updated

				if( $layout == 'facebook' )
				{
					// See if user wants this option.
					$page		= JRequest::getVar( 'integrations_facebook_impersonate_page' );

					// Retrieve the page id's.
					$id 	= JRequest::getVar( 'integrations_facebook_page_id' );

					if( $page && $id )
					{
						// Set the impersonate as page option
						$data[ 'integrations_facebook_impersonate_page' ]	= 1;
						$data['integrations_facebook_page_id']				= $id;
					}
				}

				$data[ 'integrations_' . $layout . '_centralized' ]					= 1;
				$data[ 'integrations_' . $layout . '_centralized_auto_post' ]		= JRequest::getVar( 'integrations_' . $layout . '_centralized_auto_post' );
				$data[ 'integrations_' . $layout . '_centralized_send_updates' ]	= JRequest::getVar( 'integrations_' . $layout . '_centralized_send_updates' );

				$model->save( $data );

				$message 		= JText::_( 'COM_EASYBLOG_AUTOPOST_SETTINGS_SAVED' );
				$status 		= 'success';
			break;
			case '3':
				// On the third step, we only store the following settings
				//
				// - Maximum length of the content (Facebook only)
				// - Content source (Facebook only)
				// - Allow site users to setup their own autopostings.

				if( $layout == 'facebook' )
				{
					$data[ 'integrations_' . $layout . '_source' ]					= JRequest::getVar( 'integrations_' . $layout . '_centralized_auto_post' );
					$data[ 'integrations_' . $layout . '_blogs_length' ]	= JRequest::getVar( 'integrations_' . $layout . '_blogs_length' );
				}

				// Test for bit.ly because only Twitter has this option :(
				if( $layout == 'twitter' )
				{
					// Test if shortening service is required.
					$shortenURLs	= JRequest::getVar( 'main_twitter_shorten_url' );

					if( $shortenURLs )
					{
						$data[ 'main_twitter_shorten_url' ]			= 1;
						$data[ 'main_twitter_urlshortener_login' ]	= JRequest::getVar( 'main_twitter_urlshortener_login' );
						$data[ 'main_twitter_urlshortener_apikey' ]	= JRequest::getVar( 'main_twitter_urlshortener_apikey' );
					}
				}

				// Test for default message type as only Twitter and LinkedIn has this.
				if( $layout == 'twitter' || $layout == 'linkedin' )
				{
					$data[ 'main_' . $layout . '_message' ]		= JRequest::getVar( 'main_' . $layout . '_message' );
				}

				// All social provider has this option.
				$data[ 'integrations_' . $layout . '_centralized_and_own' ]		= JRequest::getVar( 'integrations_' . $layout . '_centralized_and_own' );

				$model->save( $data );

				$message 		= JText::_( 'COM_EASYBLOG_AUTOPOST_SETTINGS_SAVED' );
				$status 		= 'completed';
			break;

		}

		return array( 'message' => $message , 'type' => $status );
		exit;
	}

	public function request()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'autoposting' );

		$config		= EasyBlogHelper::getConfig();
		$type		= JRequest::getCmd( 'type' );
		$step		= JRequest::getInt( 'step' );

		$key	= $config->get( 'integrations_' . $type . '_api_key' );
		$secret	= $config->get( 'integrations_' . $type . '_secret_key' );

		$my 		= JFactory::getUser();

		// Detect where the request is from.
		$from 		= JRequest::getWord( 'return' );

		// Caller might want us to do some javascript callbacks.
		$call 		= JRequest::getWord( 'call' );
		$call		= !empty( $call ) ? '&call=' . $call : '';

		$return 	= JRequest::getWord( 'return' );
		$return 	= !empty( $return ) ? '&return=' . $return : '';

		$callback	= rtrim( JURI::root() , '/' ) . '/administrator/index.php?option=com_easyblog&c=autoposting&task=grant&type=' . $type . $return . $call;

		$consumer	= EasyBlogHelper::getHelper( 'OAuth' )->getConsumer( $type , $key , $secret , $callback );
		$request	= $consumer->getRequestToken();
		$redirect	= JRoute::_( 'index.php?option=com_easyblog&view=autoposting&layout=' . $type . '&step=' . $step , false );

		if( empty( $request->token ) || empty( $request->secret ) )
		{
			$this->setRedirect( $redirect , JText::sprintf( 'COM_EASYBLOG_AUTOPOST_ERRORS_INVALID_REQUEST_TOKENS' , $type ) );
			return;
		}

		$oauth				= EB::table('Oauth');

		// We do not need to set the user_id because system autopostings doesn't need to be associated with any users
		$oauth->type		= $type;
		$oauth->created		= EB::date()->toMySQL();

		// Bind the request tokens
		$param	= EB::registry();
		$param->set( 'token' 	, $request->token );
		$param->set( 'secret'	, $request->secret );

		$oauth->system 			= 1;
		$oauth->request_token	= $param->toString();
		$oauth->store();

		$this->setRedirect( $consumer->getAuthorizationURL( $request->token , false , 'popup' ) );
	}


	public function revoke()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'autoposting' );

		$mainframe	= JFactory::getApplication();
		$type		= JRequest::getCmd( 'type' );
		$config		= EasyBlogHelper::getConfig();
		$from		= JRequest::getWord( 'return' );

		$oauth		= EB::table('Oauth');
		$oauth->loadSystemByType( $type );

		// Revoke the access through the respective client first.
		$callback	= rtrim( JURI::root() , '/' ) . '/administrator/index.php?option=com_easyblog&c=autoposting&task=grant&type=' . $type . '&return=' . $from;


		$key 	= $config->get( 'integrations_' . $type . '_api_key' );
		$secret	= $config->get( 'integrations_' . $type . '_secret_key' );

		$consumer	= EasyBlogHelper::getHelper( 'OAuth' )->getConsumer( $type , $key , $secret , $callback );
		$consumer->setAccess( $oauth->access_token );

		$redirect	= JRoute::_( 'index.php?option=com_easyblog&view=autoposting&layout=' . $type . '&step=2', false );

		if( $from == 'form' )
		{
			$redirect 	= JRoute::_( 'index.php?option=com_easyblog&view=autoposting&layout=form&type=' . $type , false );
		}

		if( !$consumer->revokeApp() )
		{
			$this->setRedirect( $redirect , JText::_( 'There was an error when trying to revoke your app.') );
			return;
		}
		$oauth->delete();

		$this->setRedirect( $redirect, JText::_('Application revoked successfully.') );
	}


	public function grant()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'autoposting' );

		$type		= JRequest::getCmd( 'type' );
		$mainframe	= JFactory::getApplication();
		$config		= EasyBlogHelper::getConfig();
		$key		= $config->get( 'integrations_' . $type . '_api_key' );
		$secret		= $config->get( 'integrations_' . $type . '_secret_key' );

		$my			= JFactory::getUser();
		$from		= JRequest::getWord( 'return' );
		$oauth		= EB::table('Oauth');
		$loaded		= $oauth->loadSystemByType( $type );
		$denied		= JRequest::getVar( 'denied' , '' );
		$redirect	= JRoute::_( 'index.php?option=com_easyblog&view=autoposting&layout=' . $type . '&step=2' , false );

		if( $from == 'form' )
		{
			$redirect 	= JRoute::_( 'index.php?option=com_easyblog&view=autoposting&layout=form&type=' . $type , false );
		}

		$call 		= JRequest::getWord( 'call' );
		$callUri 	= !empty( $call ) ? '&call=' . $call : '';

		if( !empty( $denied ) )
		{
			$oauth->delete();

			$this->setRedirect( $redirect , JText::sprintf( 'Denied by %1s' , $type ) , 'error');
			return;
		}

		if( !$loaded )
		{
			$oauth->delete();

			JError::raiseError( 500 , JText::_( 'COM_EASYBLOG_AUTOPOST_ERRORS_REQUEST_TOKENS_NOT_LOADED' ) );
		}

		$request 	= EB::registry($oauth->request_token);

		$return 	= JRequest::getWord( 'return' );
		$return  	= !empty( $return ) ? '&return=' . $return : '';

		$callback	= rtrim( JURI::root() , '/' ) . '/administrator/index.php?option=com_easyblog&c=autoposting&task=grant&type=' . $type . $return . $callUri;

		$consumer	= EasyBlogHelper::getHelper( 'OAuth' )->getConsumer( $type , $key , $secret , $callback );
		$verifier	= $consumer->getVerifier();

		if( empty( $verifier ) )
		{
			// Since there is a problem with the oauth authentication, we need to delete the existing record.
			$oauth->delete();

			JError::raiseError( 500 , JText::_( 'COM_EASYBLOG_AUTOPOST_ERRORS_INVALID_VERIFIER' ) );
		}

		$access		= $consumer->getAccess( $request->get( 'token' ) , $request->get( 'secret' ) , $verifier );

		if( !$access || empty( $access->token ) || empty( $access->secret ) )
		{
			// Since there is a problem with the oauth authentication, we need to delete the existing record.
			$oauth->delete();

			$this->setRedirect( $redirect , JText::sprintf( 'COM_EASYBLOG_AUTOPOST_ERRORS_INVALID_ACCESS_TOKENS' , $type ) , 'error' );
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

		// @task: Let's see if the oauth client
		if( !empty( $call ) )
		{
			$consumer->$call();
		}
		else
		{
			$this->setRedirect( $redirect , JText::_( 'COM_EASYBLOG_AUTOPOST_ACCOUNT_ASSOCIATED_SUCCESSFULLY') );
		}

		return;
	}
}
