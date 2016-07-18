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

class EasySocialViewUsers extends EasySocialSiteView
{
	/*
	 * used to display list of users in avatar form.
	 */
	public function popbox()
	{
		// Load front end's language file
		FD::language()->loadSite();

		$ids	= JRequest::getVar( 'ids', '' );

		$ajax 	= FD::ajax();

		if( !$ids )
		{
			// Throw some errors.
			return $ajax->reject( $this->getMessage() );
		}

		$ids = explode( '|', $ids );

		$users 	= FD::user( $ids );

		$theme 		= FD::get( 'Themes' );
		$theme->set( 'users', $users );
		$html 		= $theme->output( 'site/users/popbox.users' );

		return $ajax->resolve( $html );
	}

	/**
	 * Post processing after filtering users
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of SocialUser objects
	 */
	public function getUsersByFilter($users, $filter, $pagination = null, $displayOptions = null)
	{
		$ajax  = FD::ajax();

		$theme = FD::themes();

		$id = $this->input->get('id', 0, 'int');

		$theme->set('users', $users);
		$theme->set('isSort', true);
		$theme->set('filter', 'search');
		$theme->set('searchFilter', $filter);
		$theme->set('id', $id);
		$theme->set('displayOptions', $displayOptions);

		$contents 	= $theme->output('site/users/default.list');

		if($pagination )
		{
			$contents .= '<div class="es-pagination-footer" data-users-pagination>' . $pagination->getListFooter( 'site' ) . '</div>';
		}

		return $ajax->resolve($contents);
	}

	/**
	 * Post processing after filtering users
	 *
	 * @since	1.3
	 * @access	public
	 * @param	Array	An array of SocialUser objects
	 */
	public function getUsersByProfileFilter($users, $profile, $filter, $pagination = null)
	{
		$ajax  = FD::ajax();

		$theme = FD::themes();

		$id = $this->input->get('id', 0, 'int');

		$theme->set('users', $users);
		$theme->set('isSort', true);
		$theme->set('filter', 'profiletype');
		$theme->set('activeProfile', $profile);
		$theme->set('id', $id);

		$contents 	= $theme->output('site/users/default.list');

		if($pagination )
		{
			$contents .= '<div class="es-pagination-footer" data-users-pagination>' . $pagination->getListFooter( 'site' ) . '</div>';
		}

		return $ajax->resolve($contents);
	}

	/**
	 * Post processing after filtering users
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of SocialUser objects
	 */
	public function getUsersByProfile($users, $profile, $pagination = null)
	{
		$ajax  = FD::ajax();

		$theme = FD::themes();

		$id = $this->input->get('id', 0, 'int');

		$theme->set('users', $users);
		$theme->set('isSort', true);
		$theme->set('filter', 'profiletype');
		$theme->set('activeProfile', $profile);
		$theme->set('id', $id);

		$contents 	= $theme->output('site/users/default.list');

		if($pagination )
		{
			$contents .= '<div class="es-pagination-footer" data-users-pagination>' . $pagination->getListFooter( 'site' ) . '</div>';
		}

		return $ajax->resolve($contents);
	}

	/**
	 * Post processing after filtering users
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of SocialUser objects
	 */
	public function getUsers( $users , $isSort = false, $pagination = null )
	{
		$ajax 		= FD::ajax();

		$filter 	= JRequest::getWord( 'filter' );
		$sort 		= JRequest::getWord( 'sort', $this->config->get('users.listings.sorting') );

		$theme 		= FD::themes();
		$theme->set('users', $users);
		$theme->set('isSort', $isSort);
		$theme->set('filter', $filter);
		$theme->set('sort', $sort);
		$theme->set('profile', false);

		$contents 	= $theme->output( 'site/users/default.list' );

		if($pagination )
		{
			$contents .= '<div class="es-pagination-footer" data-users-pagination>' . $pagination->getListFooter( 'site' ) . '</div>';
		}

		return $ajax->resolve( $contents );
	}
}
