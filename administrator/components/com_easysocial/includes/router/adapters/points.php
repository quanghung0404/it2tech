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
 * Component's router for points view.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialRouterPoints extends SocialRouterAdapter
{
	/**
	 * Constructs the points urls
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function build( &$menu , &$query )
	{
		$segments 	= array();

		// If there is a menu but not pointing to the profile view, we need to set a view
		if( $menu && $menu->query[ 'view' ] != 'points' )
		{
			$segments[]	= $this->translate( $query[ 'view' ] );
		}

		// If there's no menu, use the view provided
		if( !$menu )
		{
			$segments[]	= $this->translate( $query[ 'view' ] );
		}
		unset( $query[ 'view' ] );

		$layout 	= isset( $query[ 'layout' ] ) ? $query[ 'layout' ] : null;

		if( !is_null( $layout ) )
		{
			$segments[]	= $this->translate( 'points_layout_' . $layout );
			unset( $query[ 'layout' ] );
		}

		$id 		= isset( $query[ 'id' ] ) ? $query[ 'id' ] : null;

		if( !is_null( $id ) )
		{
			$segments[]	= $id;
			unset( $query[ 'id' ] );
		}

		$userid 		= isset( $query[ 'userid' ] ) ? $query[ 'userid' ] : null;

		if( !is_null( $userid ) )
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

		// URL: http://site.com/menus/points
		if ($total == 1 && ($segments[0] == $this->translate('points') || $segments[0] == 'points')) {

			$vars[ 'view' ]	= 'points';
			return $vars;
		}

		// URL: http://site.com/menus/points/item/ID-point-alias
		if ($total == 3 && ($segments[0] == $this->translate('points') || $segments[0] == 'points') && $segments[1] == $this->translate('points_layout_item')) {
			$vars[ 'view' ] = 'points';
			$vars[ 'layout' ] = 'item';
			$vars[ 'id' ] = $segments[ 2 ];

			return $vars;
		}

		// URL: http://site.com/menus/points/item/ID-point-alias
		if ($total == 3 && ($segments[0] == $this->translate('points') || $segments[0] == 'points') && $segments[1] == $this->translate('points_layout_history')) {
			$vars[ 'view' ]		= 'points';
			$vars[ 'layout' ]	= 'history';
			$vars[ 'userid' ]	= $this->getUserId( $segments[ 2 ] );

			return $vars;
		}

		return $vars;
	}
}
