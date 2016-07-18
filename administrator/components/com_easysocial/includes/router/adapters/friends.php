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
 * Component's router for friends view.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialRouterFriends extends SocialRouterAdapter
{
	/**
	 * Constructs friends urls
	 *
	 * @since	1.0
	 * @access	public
	 * @param	JMenu 	The active menu object.
	 * @param	array 	An array of query strings
	 * @return	array 	The url structure
	 */
	public function build( &$menu , &$query )
	{
		$segments 	= array();

		// If there is no active menu for friends, we need to add the view.
		if( $menu && $menu->query[ 'view' ] != 'friends' )
		{
			$segments[]	= $this->translate( $query[ 'view' ] );
		}

		// If there's no menu, use the view provided
		if( !$menu )
		{
			$segments[]	= $this->translate( $query[ 'view' ] );
		}

		unset( $query[ 'view' ] );

		// Check if user id is supplied. If it does exist, use their alias as the first segment.
		$userId		= isset( $query[ 'userid' ] ) ? $query[ 'userid' ] : null;

		if( !is_null( $userId ) )
		{
			$segments[]	= $query[ 'userid' ];
			unset( $query[ 'userid' ] );
		}

		$layout 	= isset( $query[ 'layout' ] ) ? $query[ 'layout' ] : null;

		if( !is_null( $layout ) )
		{
			$segments[]	= $this->translate( 'friends_layout_' . $layout );
			unset( $query[ 'layout' ] );
		}

		$listId 	= isset( $query[ 'listId' ] ) ? $query[ 'listId' ] : '';

		if( $listId )
		{
			$segments[]	= $this->translate( 'friends_layout_list' );
			$segments[]	= $listId;
			unset( $query[ 'listId' ] );
		}

		$filter 	= isset( $query[ 'filter' ] ) ? $query[ 'filter' ] : '';

		if( $filter )
		{
			$segments[]	= $this->translate( 'friends_filter_' . $filter );
			unset( $query[ 'filter' ] );
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

		// If the second segment is part of the layout, use it.
		$layouts 	= $this->getAvailableLayouts( 'Friends' );

		// URL: http://site.com/menu/friends/invite
		if( $total == 2 && $segments[ 1 ] == $this->translate('friends_layout_invite')) {
			$vars[ 'view' ]		= 'friends';
			$vars[ 'layout' ]	= 'invite';

			return $vars;
		}

		// URL: http://site.com/menu/friends/listform
		if( $total == 2 && $segments[ 1 ] == $this->translate( 'friends_layout_listform' ) )
		{
			$vars[ 'view' ]		= 'friends';
			$vars[ 'layout' ]	= 'listform';

			return $vars;
		}

		// Viewing a list of my own friends
		//
		// URL: http://site.com/menu/friends
		if( $total == 1 && $segments[ 0 ] == $this->translate( 'friends' ) )
		{
			$vars[ 'view' ]	= 'friends';

			return $vars;
		}

		// We need to test for "filters" first
		//
		// URL: http://site.com/menu/friends/pending
		// URL: http://site.com/menu/friends/request
		// URL: http://site.com/menu/friends/suggest
		$filters 	= array($this->translate('friends_filter_invites'), $this->translate('friends_filter_pending'), $this->translate('friends_filter_request'), $this->translate('friends_filter_suggest'));

		if( $total == 2 && $segments[ 0 ] == $this->translate('friends') && in_array($segments[1], $filters))
		{
			$vars['view'] = 'friends';

			if ($segments[1] == $this->translate('friends_filter_pending')) {
				$vars['filter']	= 'pending';
			}

			if ($segments[1] == $this->translate('friends_filter_request')) {
				$vars['filter']	= 'request';
			}

			if ($segments[1 ] == $this->translate('friends_filter_suggest')) {
				$vars['filter']	= 'suggest';
			}

			if ($segments[1] == $this->translate('friends_filter_invites')) {
				$vars['filter']	= 'invites';
			}

			return $vars;
		}

		// URL: http://site.com/menu/friends/ID-username
		if ($total == 2) {

			$vars['view']	= 'friends';
			$vars['userid']	= $this->getUserId( $segments[ 1 ] );

			return $vars;
		}

		// URL: http://site.com/menu/friends/ID-username/mutual
		if( $total == 3 && $segments[ 0 ] == $this->translate( 'friends' ) && $segments[ 2 ] == $this->translate( 'friends_filter_mutual' ) )
		{
			$vars[ 'view' ]		= 'friends';
			$vars[ 'userid' ]	= $this->getUserId( $segments[ 1 ] );
			$vars[ 'filter' ]	= 'mutual';

			return $vars;
		}

		// If there are 3 segments and the second segment is 'list'
		// URL: http://site.com/menu/friends/list/ID
		if( $total == 3 && $segments[ 1 ] == $this->translate( 'friends_layout_list' ) )
		{
			$vars[ 'view' ]		= 'friends';
			$vars[ 'layout' ]	= 'list';
			$vars[ 'listId' ]	= $segments[ 2 ];

			return $vars;
		}

		return $vars;
	}
}
