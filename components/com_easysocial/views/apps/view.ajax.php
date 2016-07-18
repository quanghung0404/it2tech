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

FD::import( 'site:/views/views' );

class EasySocialViewApps extends EasySocialSiteView
{
	/**
	 * Returns the apps html
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getApps( $apps )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$contents 	= '';

		if( $apps )
		{
			foreach( $apps as $app )
			{
				$theme 	= FD::themes();
				$theme->set( 'app' , $app );
				$contents 	.= $theme->output( 'site/apps/default.item' );
			}
		}
		else
		{
			$theme 	= FD::themes();
			$contents 	= $theme->output( 'site/apps/default.empty' );
		}

		return $ajax->resolve( $contents );
	}

	/**
	* Obtains the application output when a user clicks on a bookmark item.
	*
	* @since	1.0
	* @access	public
	* @param
	*
	* @return
	*/
	public function renderBookmark()
	{
		$id 	= JRequest::getInt( 'id' );
		$app 	= FD::table( 'Application' );
		$app->load( $id );

		$lib 	= FD::getInstance( 'Apps' );
		$output = $lib->render( 'bookmark' , $app->element , $app->group );

		// Return the JSON output.
		$ajax 	= FD::getInstance( 'Ajax' );
		$ajax->success( $output );
	}

	/**
	 * Retrieves the terms and conditions for the app
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTnc()
	{
		// User's need to be logged in
		FD::requireLogin();

		// Load back end language files
		FD::language()->loadAdmin();

		$ajax 	= FD::ajax();
		$theme	= FD::themes();

		$output = $theme->output( 'site/apps/dialog.install' );

		return $ajax->resolve( $output );
	}

	/**
	 * Post processing after an app is installed.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installApp()
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$theme = FD::themes();

		$html = $theme->output( 'site/apps/dialog.installed' );

		$ajax->resolve( $html );
	}

	/**
	 * Uninstall confirmation
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function confirmUninstall()
	{
		$ajax = FD::ajax();

		$theme = FD::themes();

		$html = $theme->output( 'site/apps/dialog.uninstall' );

		$ajax->resolve( $html );
	}

	/**
	 * Post processing after an app is uninstalled.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function uninstallApp()
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$theme = FD::themes();

		$html = $theme->output( 'site/apps/dialog.uninstalled' );

		$ajax->resolve( $html );
	}

	/**
	 * Post process after settings is saved
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveSettings()
	{
		// User must be logged in.
		FD::requireLogin();

		// Get the ajax library
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve();
	}

	/**
	 * Display confirmation that the settings is saved successfully.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveSuccess()
	{
		// User must be logged in.
		FD::requireLogin();

		// Get the ajax library
		$ajax 	= FD::ajax();

		// Get the themes library
		$theme 	= FD::themes();

		$output = $theme->output( 'site/apps/dialog.saved' );

		return $ajax->resolve( $output );
	}

	/**
	 * Display app settings
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function settings()
	{
		// User must be logged in.
		FD::requireLogin();

		// Get the ajax library
		$ajax 	= FD::ajax();

		// Get the themes library
		$theme 	= FD::themes();

		// Get the app id from request.
		$id 	= JRequest::getInt( 'id' );

		// Try to load the app
		$app 	= FD::table( 'App' );
		$app->load( $id );

		if( !$id || !$app->id )
		{
			return $ajax->reject( FD::info()->set( JText::_( 'COM_EASYSOCIAL_APPS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR ) );
		}

		// Ensure that the user can really access this app settings.
		if( !$app->isInstalled() )
		{
			return $ajax->reject( FD::info()->set( JText::_( 'COM_EASYSOCIAL_APPS_SETTINGS_NOT_INSTALLED' ) , SOCIAL_MSG_ERROR ) );
		}

		// $app->loadLanguage();

		// Get current registry object for the current user.
		$params	= $app->getUserParams();

		// Render user settings from the app.
		$form 	= $app->renderForm( 'user' , $params );

		$theme->set( 'id'	, $app->id );
		$theme->set( 'form' , $form );

		$output = $theme->output( 'site/apps/dialog.settings' );

		return $ajax->resolve( $output );
	}
}
