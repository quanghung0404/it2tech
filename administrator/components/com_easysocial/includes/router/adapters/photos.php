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
 * Component's router for photos view.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialRouterPhotos extends SocialRouterAdapter
{
	/**
	 * Constructs the photo's urls
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
		if( $menu && $menu->query[ 'view' ] != 'photos' )
		{
			$segments[]	= $this->translate( $query[ 'view' ] );
		}

		if( !$menu )
		{
			$segments[]	= $this->translate( $query[ 'view' ] );
		}
		unset( $query[ 'view' ] );

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
			$segments[]	= $this->translate( 'photos_layout_' . $layout );
		}

		unset( $query[ 'layout' ] );

		$id 		= isset( $query[ 'id' ] ) ? $query[ 'id' ] : null;

		if( !is_null( $id ) )
		{
			$segments[]	= $id;
			unset( $query[ 'id' ] );
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

		// Determines if we should encode the userid
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
		$hasLayout  = false;


		// When user is viewing their own photo
		// URL: http://site.com/menu/photos/item/ID-photo-alias
		if( $total == 3 && $segments[ 1 ] == $this->translate( 'photos_layout_item' ) )
		{
			$hasLayout = true;

			$vars[ 'view' ]		= 'photos';
			$vars[ 'layout' ]	= 'item';
			$vars[ 'id' ]		= $this->getIdFromPermalink( $segments[ 2 ] );

			return $vars;
		}

		// When user tries to download their own photo
		// URL: http://site.com/menu/photos/item/ID-photo-alias
		if( $total == 3 && $segments[ 1 ] == $this->translate( 'photos_layout_download' ) )
		{
			$hasLayout = true;

			$vars[ 'view' ]		= 'photos';
			$vars[ 'layout' ]	= 'download';
			$vars[ 'id' ]		= $this->getIdFromPermalink( $segments[ 2 ] );

			return $vars;
		}

		// When user tries to edit their own photo
		// URL: http://site.com/menu/photos/form/ID-photo-alias
		if( $total == 3 && $segments[ 1 ] == $this->translate( 'photos_layout_form' ) )
		{
			$hasLayout = true;

			$vars[ 'view' ]		= 'photos';
			$vars[ 'layout' ]	= 'form';
			$vars[ 'id' ]		= $this->getIdFromPermalink( $segments[ 2 ] );

			return $vars;
		}

		// When viewer tries to view another person's photo
		// URL: http://site.com/menu/photos/item/ID-photo-alias/ID-user-alias
		if( $total == 5 && $segments[ 1 ] == $this->translate( 'photos_layout_item' ) )
		{
			$vars[ 'view' ]		= 'photos';
			$vars[ 'layout' ]	= 'item';
			$vars[ 'id' ]		= $segments[ 2 ];
			$vars[ 'type' ]		= $segments[ 3 ];

			if( $vars[ 'type' ] == 'user' )
			{
				$vars[ 'uid' ]	= $this->getUserId( $segments[ 4 ] );
			}
			else
			{
				$vars[ 'uid' ]	= $this->getIdFromPermalink( $segments[ 4 ] );
			}

			return $vars;
		}


		// When viewer tries to view another person's photo
		// URL: http://site.com/menu/photos/item/ID-photo-alias/ID-user-alias
		if( $total == 4 && $segments[ 1 ] == $this->translate( 'photos_layout_item' ) )
		{
			$hasLayout = true;

			$vars[ 'view' ]		= 'photos';
			$vars[ 'layout' ]	= 'item';
			$vars[ 'id' ]		= $segments[ 2 ];
			$vars[ 'userid' ]	= $this->getUserId( $segments[ 3 ] );

			return $vars;
		}

		// When viewer is editing a photo
		// URL: http://site.com/menu/photos/form/ID-photo-alias/ID-user-alias
		if( $total == 4 && $segments[ 1 ] == $this->translate( 'photos_layout_form' ) )
		{
			$vars[ 'view' ]		= 'photos';
			$vars[ 'layout' ]	= 'form';
			$vars[ 'id' ]		= $segments[ 2 ];
			$vars[ 'userid' ]	= $this->getUserId( $segments[ 3 ] );

			return $vars;
		}

		// URL: http://site.com/menu/photos/ID-photo-alias/ID-user-alias
		if( $total == 3 && !$hasLayout )
		{

			$vars[ 'view' ]		= 'photos';
			$vars[ 'layout' ]	= 'item';
			$vars[ 'id' ]		= $this->getIdFromPermalink( $segments[ 1 ] );
			$vars[ 'userid' ]	= $this->getUserId( $segments[ 2 ] );

			return $vars;
		}

		// dump($segments);

		return $vars;
	}
}
