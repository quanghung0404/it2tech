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


class plgAuthenticationEasySocial extends JPlugin
{
	public $name 	= 'easysocial';

	public function __construct( &$subject , $config )
	{
		$config[ 'name' ]	= 'EasySocial';

		parent::__construct( $subject, $config );
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array   $credentials  Array holding the user credentials
	 * @param   array   $options      Array of extra options
	 * @param   object  $response     Authentication response object
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 */
	public function onUserAuthenticate( &$credentials, $options, &$response )
	{
		return $this->onAuthenticate( $credentials, $options, $response);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array   $credentials  Array holding the user credentials
	 * @param   array   $options      Array of extra options
	 * @param   object  $response     Authentication response object
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 */
	public function onAuthenticate( &$credentials, $options, &$response )
	{
		$file 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $file ) )
		{
			return;
		}

		// Include main library
		require_once( $file );

		// Check if Foundry exists
		if( !FD::exists() )
		{
			FD::language()->loadSite();
			echo JText::_( 'COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING' );
			return;
		}

		// Load oauth table
		$oauthTable = FD::table( 'OAuth' );

		$config 	= FD::config();

		// Check if email login is allowed.
		$emailAllowed	= $config->get( 'general.site.loginemail' );
		$isEmail 		= JMailHelper::isEmailAddress( $credentials[ 'username' ] );

		// Try to find a valid username if user tries to login with their email.
		if( $emailAllowed && $isEmail )
		{
			// Search for the email
			$model 		= FD::model( 'Users' );
			$username	= $model->getUsernameByEmail( $credentials[ 'username' ] );

			// If there's a username, replace the credentials with the username.
			if( $username )
			{
				$response->type = 'Joomla';
				$credentials[ 'username' ]	= $username;

				// Avoid using JFactory::getApplication()->login() to prevent inception because login triggers authentication plugin.

				// Get the user id based on the username
				$uid = $model->getUserid( 'username', $username );

				if( empty( $uid ) )
				{
					$response->status = JAuthentication::STATUS_FAILURE;
					$response->error_message = JText::_('JGLOBAL_AUTH_NO_USER');
				}
				else
				{
					// Verify the password
					$match = $model->verifyUserPassword( $uid, $credentials['password'] );

					if( $match === true )
					{
						// Bring this in line with the rest of the system
						$user 				= JUser::getInstance($uid);
						$response->email 	= $user->email;
						$response->fullname = $user->name;

						$app 	= JFactory::getApplication();

						if( $app->isAdmin() )
						{
							$response->language = $user->getParam('admin_language');
						}
						else
						{
							$response->language = $user->getParam('language');
						}

						$response->status = JAuthentication::STATUS_SUCCESS;
						$response->error_message = '';
					}
					else
					{
						// Invalid password
						$response->status = JAuthentication::STATUS_FAILURE;
						$response->error_message = JText::_('JGLOBAL_AUTH_INVALID_PASS');
					}
				}
			}
		}

		// Lets try to load the user.
		$state 		= $oauthTable->loadByUsername( $credentials[ 'username' ] );

		if( $state )
		{
			// Now we really need to ensure that they are logged in with their respective oauth client.
			$client 	= FD::oauth( $oauthTable->client );

			// Check the current user's token and the stored token.
			$oauthUserId = $client->getUserId();

			// We cannot match the access token because everytime the user click on the Facebook login button, the tokens are re-generated.
			if( $oauthUserId == $oauthTable->oauth_id )
			{
				$user 	= FD::user( $oauthTable->uid );

				// User login successfull. We need to update the access token with the new token.
				$oauthTable->bindToken( $client );
				$oauthTable->store();

				$response->fullname 	= $user->getName();
				$response->username 	= $user->username;
				$response->password 	= $credentials[ 'password' ];
				$response->status 		= JAuthentication::STATUS_SUCCESS;
				$response->error_message = '';

				return true;
			}
		}

		return false;
	}
}
