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

// Import main controller
FD::import('site:/controllers/controller');

class EasySocialControllerAccount extends EasySocialController
{
	/**
	 * Processes username reminder
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remindUsername()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the current logged in user.
		$my 	= FD::user();

		if( $my->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_YOU_ARE_ALREADY_LOGGED_IN' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the email address
		$email 	= JRequest::getVar( 'es-email' );

		$model 	= FD::model( 'Users' );
		$state	= $model->remindUsername( $email );

		if( !$state )
		{
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		$view->setMessage( JText::sprintf( 'COM_EASYSOCIAL_PROFILE_USERNAME_SENT' , $email ) );
		return $view->call( __FUNCTION__ );
	}


	/**
	 * Processes username reminder
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remindPassword()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the current logged in user.
		$my 	= FD::user();

		if ($my->id) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_YOU_ARE_ALREADY_LOGGED_IN' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the email address
		$email 	= JRequest::getVar( 'es-email' );

		$model 	= FD::model( 'Users' );
		$state	= $model->remindPassword($email);

		if( !$state )
		{
			$view->setMessage($model->getError(), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		$view->setMessage( JText::sprintf( 'COM_EASYSOCIAL_PROFILE_USERNAME_SENT' , $email ) );
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Password reset confirmation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmResetPassword()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the current logged in user.
		$my 	= FD::user();

		if( $my->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_YOU_ARE_ALREADY_LOGGED_IN' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$username 	= JRequest::getVar( 'es-username' );
		$code 		= JRequest::getVar( 'es-code' );

		$model 	= FD::model( 'Users' );
		$state	= $model->verifyResetPassword( $username , $code );

		if( !$state )
		{
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Completes password reset
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function completeResetPassword()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the current logged in user.
		$my 	= FD::user();

		if( $my->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_YOU_ARE_ALREADY_LOGGED_IN' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$password 		= JRequest::getVar( 'es-password' );
		$password2 		= JRequest::getVar( 'es-password2' );

		// Check if the password matches
		if( $password != $password2 )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_PASSWORDS_NOT_MATCHING' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$model 	= FD::model( 'Users' );
		$state	= $model->resetPassword( $password , $password2 );

		if( !$state )
		{
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_SUCCESSFUL' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Completes require password reset
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function completeRequireResetPassword()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the current logged in user.
		$my 	= FD::user();

		$password 		= JRequest::getVar( 'es-password' );
		$password2 		= JRequest::getVar( 'es-password2' );

		// Check if the password matches
		if( !$password || !$password2 || $password != $password2 )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_PASSWORDS_NOT_MATCHING' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$model 	= FD::model( 'Users' );
		$state	= $model->resetRequirePassword( $password , $password2 );

		if( !$state )
		{
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_REQUIRE_PASSWORD_UPDATE_SUCCESSFUL' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}



	/**
	 * Replicate's Joomla login behavior
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function login()
	{
		JSession::checkToken('post') or jexit(JText::_('JInvalid_Token'));

		$app = JFactory::getApplication();

		// Populate the data array:
		$data = array();
		$data['return']		= base64_decode($app->input->post->get('return', '', 'BASE64'));
		$data['username']	= JRequest::getVar('username', '', 'method', 'username');
		$data['password']	= JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
		$data['secretkey']	= JRequest::getString('secretkey', '');

		// Get the user's state because there could be instances where Joomla is redirecting users
		$tmp 	= $app->getUserState('users.login.form.data');

		if (isset($tmp['return']) && !empty($tmp['return'])) {
			$data['return']	= $tmp['return'];
		}

		// Set the return URL if empty.
		if (empty($data['return']))
		{
			$data['return'] = 'index.php?option=com_easysocial&view=login';
		}

		// Set the return URL in the user state to allow modification by plugins
		$app->setUserState('users.login.form.return', $data['return']);

		// Get the log in options.
		$options = array();
		$options['remember'] = JRequest::getBool('remember', false);
		$options['return'] = $data['return'];

		// Silent! Kill you!
		$options['silent']	= true;

		// Get the log in credentials.
		$credentials = array();
		$credentials['username']  = $data['username'];
		$credentials['password']  = $data['password'];
		$credentials['secretkey'] = $data['secretkey'];


		// Perform the log in.
		if (true === $app->login($credentials, $options))
		{
			$userModel = FD::model('Users');

			// we need to check if user required to reset password or not.
			$jUser = JFactory::getUser();

			// this is for Joomla's JUser->requireReset enabled
			if (isset($jUser->requireReset) && $jUser->requireReset) {

				//@TODO:: if juser->requireReset, we need to reset this flag and enable the require_reset flag from our social_user
				// to avoid infnity loop caused by redirections
				$userModel->updateJoomlaUserPasswordResetFlag($jUser->id, '0', '1');

				$jUser->setProperties(array('requireReset' => '0'));

				// Redirect to the profile edit page
				// $app->enqueueMessage(JText::_('JGLOBAL_PASSWORD_RESET_REQUIRED'), 'notice');
				// $app->redirect(JRoute::_('index.php?option=com_users&view=profile&layout=edit'));
			}

			// let get user data again.
			$user = FD::user();

			// @TODO:: here we will redirect user to our password reset page. awesome possum.
			if ($user->require_reset) {

				$url 	= FRoute::account( array( 'layout' => 'requirePasswordReset' ) , false );
				return $app->redirect( $url );
			}

			// let update the reminder_sent flag to 0.
			$userModel->updateReminderSentFlag($user->id, '0');

			// Set the remember state
			if ($options['remember'] == true)
			{
				$app->setUserState('rememberLogin', true);
			}

			// Success
			$app->setUserState('users.login.form.data', array());

			// Redirect link should use the return data instead of relying it on getUserState('users.login.form.return')
			// Because EasySocial has its own settings of login redirection, hence this should respect the return link passed
			// We cannot fallback because the return link needs to be set in the options before calling login, and as such, the fallback has been set before calling $app->login, and no fallback is needed here.
			$app->redirect(JRoute::_($data['return'], false));
		}
		else
		{
			// Login failed !
			$data['remember'] = (int) $options['remember'];
			$app->setUserState('users.login.form.data', $data);

			$returnFailed 	= base64_decode($app->input->post->get('returnFailed', '', 'BASE64'));

			if( empty( $returnFailed ) )
			{
				$returnFailed 	= FRoute::login( array() , false );
			}

			FD::info()->set( null , JText::_( 'JGLOBAL_AUTH_INVALID_PASS' ) , SOCIAL_MSG_ERROR );
			$app->redirect( $returnFailed );
		}
	}

	/**
	 * Allows caller to log the user out from the site
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function logout()
	{
		JSession::checkToken('request') or jexit(JText::_('JInvalid_Token'));

		// Perform the logout
		$error = $this->app->logout();

		// Check if the log out succeeded.
		if (!($error instanceof Exception)) {

			// Get the return url from the request and validate that it is internal.
			$return = JRequest::getVar('return', '', 'method', 'base64');
			$return = base64_decode($return);

			if (!JUri::isInternal($return)) {
				$return = '';
			}

			// Redirect the user.
			$this->app->redirect(JRoute::_($return, false));
			$this->app->close();
		}

		$this->app->redirect(FRoute::login(array(), false));
	}


	/**
	 * Determines if the view should be visible on lockdown mode
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isLockDown($task)
	{
		$allowed = array('login', 'confirmResetPassword', 'completeResetPassword', 'remindPassword', 'remindUsername');

		if (in_array($task, $allowed)) {
			return false;
		}
		return true;
	}
}
