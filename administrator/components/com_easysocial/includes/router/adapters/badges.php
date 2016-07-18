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
 * Component's router for badges view.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialRouterBadges extends SocialRouterAdapter
{
	/**
	 * Construct's the badges url
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function build( &$menu , &$query )
	{
		$segments 	= array();

		// If there is no active menu for friends, we need to add the view.
		if( $menu && $menu->query[ 'view' ] != 'badges' )
		{
			$segments[]	= $this->translate($query[ 'view' ] );
		}

		if( !$menu )
		{
			$segments[]	= $this->translate( $query[ 'view' ] );
		}
		unset( $query[ 'view' ] );

		// $layout 	= isset( $query[ 'layout' ] ) ? $query[ 'layout' ] : null;

		// if( !is_null( $layout ) )
		// {
		// 	$segments[]	= $this->translate( 'badges_layout_' . $layout );
		// 	unset( $query[ 'layout' ] );
		// }

		$layout 	= isset( $query[ 'layout' ] ) ? $query[ 'layout' ] : null;
		$menuLayout = isset( $menu->query[ 'layout' ] ) ? $menu->query[ 'layout' ] : null;
		$addLayout = false;

		if (is_null($menuLayout)) {
			if (!is_null($layout)) {
				$addLayout = true;
			}
		} else {
			if (!is_null( $layout) && $layout != $menuLayout) {
				$addLayout = true;
			}
		}

		if ($addLayout) {
			$segments[]	= $this->translate( 'badges_layout_' . $layout );
		}
		unset( $query[ 'layout' ] );

		$id 		= isset( $query[ 'id' ] ) ? $query[ 'id' ] : null;

		if( !is_null( $id ) )
		{
			$segments[]	= $id;
			unset( $query[ 'id' ] );
		}

		// If user id is set
		$userId 	= isset( $query[ 'userid' ] ) ? $query[ 'userid' ] : null;

		if( !is_null( $userId ) )
		{
			$segments[]	= $query[ 'userid' ];
			unset( $query[ 'userid' ] );
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

		// URL: http://site.com/menu/badges
		if( $total == 1 )
		{
			$vars[ 'view' ]		= 'badges';
			return $vars;
		}

		// URL: http://site.com/menu/badges/achievements
		if( $total == 2 && $segments[ 1 ] == $this->translate( 'badges_layout_achievements' ) )
		{
			$vars[ 'view' ]		= 'badges';
			$vars[ 'layout' ]	= 'achievements';

			return $vars;
		}

		// URL: http://site.com/menu-badges/achievements
		if( $total == 2 && $segments[ 0 ] == $this->translate('badges_layout_achievements') )
		{
			$vars[ 'view' ]		= 'badges';
			$vars[ 'layout' ]	= 'achievements';
			$vars[ 'userid' ]	= $this->getUserId($segments[1]);

			return $vars;
		}

		// URL: http://site.com/menu/badges/item/ID-badge-alias
		if( $total == 3 && $segments[ 1 ] == $this->translate( 'badges_layout_item' ) )
		{
			$vars[ 'view' ]		= 'badges';
			$vars[ 'layout' ]	= 'item';
			$vars[ 'id' ]		= $this->getIdFromPermalink( $segments[ 2 ] );

			return $vars;
		}

		// URL: http://site.com/menu/badges/achievements/ID-user-alias
		if( $total == 3 && $segments[ 1 ] == $this->translate( 'badges_layout_achievements' ) )
		{
			$vars[ 'view' ]		= 'badges';
			$vars[ 'layout' ]	= 'achievements';
			$vars[ 'userid' ]	= $this->getUserId( $segments[ 2 ] );
		}

		return $vars;
	}
}
