<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialRouterProfile extends SocialRouterAdapter
{
	/**
	 * Constructs the profile urls
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function build(&$menu, &$query)
	{
		$segments = array();

		// If there is a menu but not pointing to the profile view, we need to set a view
		if ($menu && $menu->query['view'] != 'profile') {
			$segments[]	= $this->translate($query['view']);
		}

		// If there's no menu, use the view provided
		if (!$menu) {
			$segments[] = $this->translate($query['view']);
		}
		unset($query['view']);

		// Check if the user
		$id 		= isset( $query[ 'id' ] ) ? $query[ 'id' ] : null;

		// If user id is provided, use their given alias.
		if( !is_null( $id ) )
		{
			$config 	= FD::config();

			$segments[]	= $query[ 'id' ];

			unset( $query[ 'id' ] );
		}

		$layout = isset( $query[ 'layout' ] ) ? $query[ 'layout' ] : null;

		if( !is_null( $layout ) )
		{
			if ($layout !== 'about' && $layout !== 'timeline') {
				$segments[] = $this->translate('profile_layout_' . $query['layout']);
			}

			$defaultLayout = FD::config()->get('users.profile.display', 'timeline');

			// Special handling for timeline and about

			// Depending settings, if default is set to timeline and layout is timeline, we don't need to add this into the segments

			if ($layout === 'timeline' && $layout !== $defaultLayout) {
				$segments[] = $this->translate('profile_layout_' . $query['layout']);
			}

			if ($layout === 'about' && ($layout !== $defaultLayout || isset($query['step']))) {
				// If layout is about and there is a step provided, then about has to be added regardless of settings
				$segments[] = $this->translate('profile_layout_' . $query['layout']);

				if (isset($query['step'])) {
					$segments[] = $query['step'];
					unset($query['step']);
				}
			}

			unset($query['layout']);
		}

		// Determines if the viewer is trying to view an app from a user.
		$appId 		= isset( $query[ 'appId' ] ) ? $query[ 'appId' ] : null;

		if( !is_null( $appId ) )
		{
			$segments[]	= $appId;
			unset( $query[ 'appId' ] );
		}

		return $segments;
	}

	/**
	 * Translates the SEF url to the appropriate url
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	An array of url segments
	 * @return	array 	The query string data
	 */
	public function parse( &$segments )
	{
		$vars 		= array();
		$total 		= count( $segments );
		$layouts 	= $this->getAvailableLayouts( 'Profile' );

		// URL: http://site.com/menu/profile
		if( $total == 1 )
		{
			$vars[ 'view' ]		= 'profile';

			return $vars;
		}

		// URL: http://site.com/menu/profile/confirmReset
		if( $total == 2 && $segments[ 1 ] == $this->translate( 'profile_layout_confirmreset' ) )
		{
			$vars[ 'view' ]		= 'profile';
			$vars[ 'layout' ]	= 'confirmReset';

			return $vars;
		}

		// URL: http://site.com/menu/profile/confirmReset
		if( $total == 2 && $segments[ 1 ] == $this->translate( 'profile_layout_completereset' ) )
		{
			$vars[ 'view' ]		= 'profile';
			$vars[ 'layout' ]	= 'completeReset';

			return $vars;
		}

		// This rule has to be before the "id" because passing an "id" would also mean viewing the person's profile.
		//
		// URL: http://site.com/menu/profile/edit
		if( $total == 2 && $segments[ 1 ] == $this->translate( 'profile_layout_edit' ) )
		{
			$vars[ 'view' ]		= 'profile';
			$vars[ 'layout' ]	= 'edit';

			return $vars;
		}

		// This rule has to be before the "id" because passing an "id" would also mean viewing the person's profile.
		//
		// URL: http://site.com/menu/profile/editprivacy
		if( $total == 2 && $segments[ 1 ] == $this->translate( 'profile_layout_editprivacy' ) )
		{
			$vars[ 'view' ]		= 'profile';
			$vars[ 'layout' ]	= 'editPrivacy';

			return $vars;
		}

		// This rule has to be before the "id" because passing an "id" would also mean viewing the person's profile.
		//
		// URL: http://site.com/menu/profile/editnotifications
		if( $total == 2 && $segments[ 1 ] == $this->translate( 'profile_layout_editnotifications' ) )
		{
			$vars[ 'view' ]		= 'profile';
			$vars[ 'layout' ]	= 'editNotifications';

			return $vars;
		}

		// URL: http://site.com/menu/profile/username/timeline
		// URL: http://site.com/menu/profile/ID-username/timeline
		if( $total == 3 && $segments[ 2 ] == $this->translate( 'profile_layout_timeline' ) )
		{
			$vars[ 'view' ] 	= 'profile';
			$vars[ 'id' ]		= $this->getUserId( $segments[ 1 ] );
			$vars[ 'layout' ]	= 'timeline';

			return $vars;
		}

		// URL: http://site.com/menu/profile/username/about
		// URL: http://site.com/menu/profile/ID-username/about
		if( $total == 3 && $segments[ 2 ] == $this->translate( 'profile_layout_about' ) )
		{
			$vars[ 'view' ] 	= 'profile';
			$vars[ 'id' ]		= $this->getUserId( $segments[ 1 ] );
			$vars[ 'layout' ]	= 'about';

			return $vars;
		}

		// URL: http://site.com/menu/profile/username/about/[step]
		// URL: http://site.com/menu/profile/ID-username/about/[step]
		if( $total == 4 && $segments[ 2 ] == $this->translate( 'profile_layout_about' ) )
		{
			$vars[ 'view' ] 	= 'profile';
			$vars[ 'id' ]		= $this->getUserId( $segments[ 1 ] );
			$vars[ 'layout' ]	= 'about';
			$vars[ 'step' ]		= $segments[3];

			return $vars;
		}

		// URL: http://site.com/menu/profile/forgetpassword
		if( $total == 2 && $segments[ 1 ] == $this->translate( 'profile_layout_forgetpassword' ) )
		{
			$vars[ 'view' ] 	= 'profile';
			$vars[ 'layout' ]	= 'forgetPassword';

			return $vars;
		}

		// URL: http://site.com/menu/profile/forgetusername
		if( $total == 2 && $segments[ 1 ] == $this->translate( 'profile_layout_forgetusername' ) )
		{
			$vars[ 'view' ] 	= 'profile';
			$vars[ 'layout' ]	= 'forgetUsername';

			return $vars;
		}

		// This rule has to be before the "id" because passing an "id" would also mean viewing the person's profile.
		//
		// URL: http://site.com/menu/profile/editPrivacy
		if( $total == 2 && ( $segments[ 1 ] == $this->translate( 'profile_layout_editprivacy' ) || str_ireplace( ':' , '-' , $segments[ 1 ] ) == $this->translate( 'profile_layout_editprivacy' ) ) )
		{
			$vars[ 'view' ] 	= 'profile';
			$vars[ 'layout' ]	= 'editPrivacy';

			return $vars;
		}

		// This rule has to be before the "id" because passing an "id" would also mean viewing the person's profile.
		//
		// URL: http://site.com/menu/profile/editNotifications
		if( $total == 2 && ( $segments[ 1 ] == $this->translate( 'profile_layout_editnotifications' ) || str_ireplace( ':' , '-' , $segments[ 1 ] ) == $this->translate( 'profile_layout_editnotifications' ) ) )
		{
			$vars[ 'view' ] 	= 'profile';
			$vars[ 'layout' ]	= 'editNotifications';

			return $vars;
		}

		// URL: http://site.com/menu/profile/username OR http://site.com/menu/profile/ID-name
		if( $total == 2 )
		{
			$vars[ 'view' ]	= 'profile';

			$vars[ 'id' ]	= $this->getUserId( $segments[ 1 ] );

			return $vars;
		}

		// Viewing an app in a profile
		//
		// URL: http://site.com/menu/profile/username/ID-app
		if( $total == 3 )
		{
			$vars[ 'view' ]		= 'profile';
			$vars[ 'id' ]		= $this->getUserId( $segments[ 1 ] );
			$vars[ 'appId' ]	= $this->getIdFromPermalink( $segments[ 2 ] );

			return $vars;
		}

		return $vars;
	}

}
