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

// Include main view file.
FD::import( 'site:/views/views' );

class EasySocialViewAccount extends EasySocialSiteView
{
	/**
	 * Determines if the view should be visible on lockdown mode
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isLockDown()
	{
		$config 	= FD::config();
		$layout 	= $this->getLayout();

		// Allowed layouts on lockdown mode
		$allowed 	= array( 'forgetUsername' , 'forgetPassword' , 'confirmReset' , 'confirmResetPassword' , 'resetUser' , 'completeResetPassword', 'completeReset' );

		if( $config->get( 'general.site.lockdown.registration' ) || in_array( $layout , $allowed ) )
		{
			return false;
		}

		return true;
	}

	/**
	 * There is no display method for this view, we need to redirect it back to dashboard
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.3.9
	 * @access public
	 */
	public function display($tpl = null)
	{
		return $this->redirect(FRoute::dashboard());
	}

	/**
	 * Post process after reminding username
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remindUsername()
	{
		// Enqueue the message
		FD::info()->set( $this->getMessage() );

		if( $this->hasErrors() )
		{
			return $this->redirect( FRoute::account( array( 'layout' => 'forgetUsername' ) , false ) );
		}

		$this->redirect( FRoute::login( array() , false )  );
	}

	/**
	 * Post process after reminding password
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remindPassword()
	{
		// Enqueue the message
		FD::info()->set( $this->getMessage() );

		if( $this->hasErrors() )
		{
			return $this->redirect(FRoute::account( array( 'layout' => 'forgetPassword' ) , false ) );
		}

		$url 	= FRoute::account( array( 'layout' => 'confirmReset' ) , false );

		$this->redirect( $url );
	}

	/**
	 * Post process after user resets the password
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function completeResetPassword()
	{
		// Enqueue the message
		FD::info()->set( $this->getMessage() );

		if( $this->hasErrors() )
		{
			return $this->redirect( FRoute::account( array( 'layout' => 'completeReset' ) , false ) );
		}

		// If it was successful, redirect user to the login page
		$this->redirect( FRoute::login( array() , false ) );
	}

	/**
	 * Post process after user enters the verification code
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function confirmResetPassword()
	{
		// Enqueue the message
		FD::info()->set($this->getMessage());

		if ($this->hasErrors()) {
			$redirect = FRoute::account(array('layout' => 'confirmReset'), false);
			return $this->redirect($redirect);
		}

		$redirect = FRoute::account(array('layout' => 'completeReset'), false);

		$this->redirect($redirect);
	}

	/**
	 * Displays the forget username form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function forgetUsername()
	{
		$my 	= FD::user();

		// If user is already logged in, do not allow them here.
		if( $my->id )
		{
			return $this->redirect( FRoute::dashboard( array() , false ) );
		}

		// Set the page title
		FD::page()->title( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_REMIND_USERNAME' ) );

		// Set the page breadcrumb
		FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_REMIND_USERNAME' ) );

		parent::display( 'site/profile/forget.username' );
	}

	/**
	 * Displays the forget password form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function forgetPassword()
	{
		$my 	= FD::user();

		// If user is already logged in, do not allow them here.
		if( $my->id )
		{
			return $this->redirect( FRoute::dashboard( array() , false ) );
		}

		// Set the page title
		FD::page()->title( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_REMIND_PASSWORD' ) );

		// Set the page breadcrumb
		FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_REMIND_PASSWORD' ) );


		parent::display( 'site/profile/forget.password' );
	}

	/**
	 * Displays the forget password form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmReset()
	{
		parent::display( 'site/profile/reset.password' );
	}


	/**
	 * Displays the forget password form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function completeReset()
	{

		$app		= JFactory::getApplication();
		$token		= $app->getUserState( 'com_users.reset.token' , null );
		$userId		= $app->getUserState( 'com_users.reset.user' , null );

		$enableValidation = false;

		if ($token && $userId) {
			// lets check if user has the joomla password field enabled or not.
			$user = FD::user($userId);
			$items = FD::model('Fields')->getCustomFields(array('group' => SOCIAL_TYPE_USER, 'uid' => $user->profile_id, 'data' => false , 'dataId' => $user->id , 'dataType' => SOCIAL_TYPE_USER, 'element' => 'joomla_password'));

			if ($items) {
				$passwordField = $items[0];

				$params = $passwordField->getParams();
				$this->set('params', $params);

				$enableValidation = true;
			}
		}

		$this->set('enableValidation', $enableValidation);
		parent::display('site/profile/reset.password.complete');
	}


	/**
	 * Displays the password reset form
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function requirePasswordReset()
	{
		parent::display( 'site/profile/require.reset.password' );
	}

	/**
	 * Displays the password reset form
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function completeRequireResetPassword()
	{
		// Enqueue the message
		FD::info()->set($this->getMessage());

		if ($this->hasErrors()) {
			$redirect = FRoute::account(array('layout' => 'requirePasswordReset'), false);
			return $this->redirect($redirect);
		}

		$redirect = FRoute::dashboard(array(), false);

		$this->redirect($redirect);
	}


}
