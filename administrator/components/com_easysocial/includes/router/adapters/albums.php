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
 * Component's router for albums view.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialRouterAlbums extends SocialRouterAdapter
{
	/**
	 * Constructs the album's urls
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
		if ($menu && $menu->query[ 'view' ] != 'albums') {
			$segments[]	= $this->translate($query['view']);
		}

		if (!$menu) {
			$segments[]	= $this->translate($query['view']);
		}
		unset($query['view']);

		// $layout = isset($query['layout']) ? $query['layout'] : null;

		// if (!is_null($layout)) {
		// 	$segments[]	= $this->translate('albums_layout_' . $layout);
		// 	unset($query['layout']);
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
			$segments[]	= $this->translate( 'albums_layout_' . $layout );
		}

		unset( $query[ 'layout' ] );

		$id = isset($query['id']) ? $query['id'] : null;

		if (!is_null($id)) {
			$segments[]	= $id;
			unset($query['id']);
		}

		// New url structure uses uid=x&type=y
		$uid 		= isset( $query[ 'uid' ] ) ? $query[ 'uid' ] : null;
		$type 		= isset( $query[ 'type' ] ) ? $query[ 'type' ] : null;

		if( !is_null( $uid ) && !is_null( $type ) )
		{
			$segments[]		= $type;
			$segments[]		= $uid;

			unset( $query[ 'uid' ] );
			unset( $query[ 'type' ] );
		}

		// Determines if userid is present in query string
		$userId 	= isset( $query[ 'userid' ] ) ? $query[ 'userid' ] : null;

		if( !is_null( $userId ) )
		{
			$segments[]	= SOCIAL_TYPE_USER;
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

		// User is viewing their own albums list
		// URL: http://site.com/menu/albums
		if( $total == 1 && ( $segments[ 0 ] == $this->translate( 'albums' ) || $segments[ 0 ] == 'albums' ) )
		{
			$vars[ 'view' ]	= 'albums';

			return $vars;
		}

		// User is viewing their own album
		// URL: http://site.com/menu/albums/item/ID-album-alias
		if( $total == 3 && ( $segments[ 1 ] == $this->translate( 'albums_layout_item' ) || $segments[ 1 ] == 'item' ) )
		{
			$vars[ 'view' ]		= 'albums';
			$vars[ 'layout' ]	= 'item';
			$vars[ 'id' ]		= $this->getIdFromPermalink( $segments[ 2 ] );

			return $vars;
		}

		// Creating a new album
		if( $total == 4 && ($segments[ 1 ] == $this->translate( 'albums_layout_form' ) || $segments[ 1 ] == 'form' ) )
		{
			$vars[ 'view' ]		= 'albums';
			$vars[ 'layout' ]	= 'form';
			$vars[ 'type' ]		= $segments[ 2 ];
			$vars[ 'uid' ]		= $this->getIdFromPermalink( $segments[ 3 ] , SOCIAL_TYPE_USER );

			return $vars;
		}

		// User is trying to create a new album
		// URL: http://site.com/menu/albums/form
		if( $total == 2 && ( $segments[ 1 ] == $this->translate( 'albums_layout_form' ) || $segments[ 1 ] == 'form' ) )
		{
			$vars[ 'view' ]		= 'albums';
			$vars[ 'layout' ]	= 'form';

			return $vars;
		}

		// Editing an album
		// URL: http://site.com/menu/albums/form/ID-ALIAS/TYPE/ID-TYPEALIAS
		if( $total == 5 && ( $segments[ 1 ] == $this->translate( 'albums_layout_form' ) || $segments[ 1 ] == 'form' ) )
		{
			$vars[ 'view' ]		= 'albums';
			$vars[ 'layout' ]	= 'form';
			$vars[ 'id' ]		= $this->getIdFromPermalink( $segments[ 2 ] );
			$vars[ 'type' ]		= $segments[ 3 ];
			$vars[ 'uid' ]		= $this->getIdFromPermalink( $segments[ 4 ] , $segments[ 3 ] );

			return $vars;
		}

		// User is viewing another person's albums list
		// URL: http://site.com/menus/albums/TYPE/ID-alias/
		if( $total == 3 && ( $segments[ 0 ] == $this->translate( 'albums' ) || $segments[ 0 ] == 'albums' ) )
		{
			$vars[ 'view' ]		= 'albums';
			$vars[ 'type' ]		= $segments[ 1 ];

			// Get the id from the permalink
			$vars[ 'uid' ]		= $this->getIdFromPermalink( $segments[ 2 ] , $vars[ 'type' ] );

			return $vars;
		}

		// User is viewing another object's album
		if ($total == 5 && ($segments[1] == $this->translate('albums_layout_item') || $segments[1] == 'item')) {

			$vars['view']	= 'albums';
			$vars['layout']	= 'item';
			$vars['id']		= $this->getIdFromPermalink($segments[2]);
			$vars['type']	= $segments[3];

			if ($vars['type'] == 'user') {
				$vars['uid'] = $this->getUserId($segments[4]);
			} else {
				$vars['uid'] = $this->getIdFromPermalink($segments[4]);
			}

			return $vars;
		}



		return $vars;
	}
}
