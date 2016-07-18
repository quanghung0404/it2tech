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

/**
 * Profile view for Adsense
 *
 * @since	1.0
 * @access	public
 */
class AdsenseWidgetsProfile extends SocialAppsWidgets
{
	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom( $user )
	{
		// Get the user params
		$params 	= $this->getUserParams( $user->id );

		// Get the app params
		$appParam	= $this->app->getParams();

		// User might not want to show this app in their profile.
		if( !$params->get( 'profile_adsense_code' , $appParam->get( 'profile_adsense_code' , '' ) ) )
		{
			return;
		}

		echo $this->getAdsense( $user , $params );
	}

	/**
	 * Display the list of photos a user has uploaded
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAdsense( $user , $params )
	{
		$appParam	= $this->app->getParams();
		$code 		= $params->get( 'profile_adsense_code' , $appParam->get( 'profile_adsense_code' , '' ) );

		$this->set( 'code'	, $code );

		return parent::display( 'widgets/profile/adsense' );
	}
}
