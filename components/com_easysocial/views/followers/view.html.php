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

/**
 * Follower's view.
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.0
 */
class EasySocialViewFollowers extends EasySocialSiteView
{
	/**
	 * Determines if this feature is enabled
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isEnabled()
	{
		$config 	= FD::config();

		if( $config->get( 'followers.enabled' ) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Default method to display a list of friends a user has.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function display( $tpl = null )
	{
		if( !$this->isEnabled() )
		{
			return $this->redirect( FRoute::dashboard( array() , false ) );
		}

		// Check if there's an id.
		$id 	= JRequest::getInt( 'userid' , null );

		// Get the user.
		$user 		= FD::user( $id );
		$my			= FD::user();
		$privacy 	= FD::privacy( $my->id );

		// Let's test if the current viewer is allowed to view this profile.
		if( $my->id != $user->id )
		{
			if(! $privacy->validate( 'followers.view' , $user->id ) )
			{
				return $this->restricted( $user );
			}
		}

		if( $user->isViewer() )
		{
			// Only registered users allowed to view their own followers
			FD::requireLogin();
		}

		// If user is not found, we need to redirect back to the dashboard page
		if( !$user->id )
		{
			return $this->redirect( FRoute::dashboard( array() , false ) );
		}

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get current active filter.
		$active 	= JRequest::getWord( 'filter' , 'followers' );


		// Get the list of followers for this current user.
		$model		= FD::model( 'Followers' );
		$title 		= JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FOLLOWERS' );

		$limit 		= FD::themes()->getConfig()->get( 'followersLimit' , 20 );
		$options[ 'limit' ]	= $limit;

		if( $active == 'followers' )
		{
			$users		= $model->getFollowers( $user->id , $options );
		}

		if( $active == 'following' )
		{
			$users		= $model->getFollowing( $user->id , $options );
			$title 		= JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FOLLOWING' );
		}

		if( $active == 'suggest' )
		{
			$users		= $model->getSuggestions( $user->id , $options );
			$title 		= JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_PEOPLE_TO_FOLLOW' );
		}

		// Get the pagination
		$pagination 	= $model->getPagination();

		$filterFollowers 	= FRoute::followers( array() , false );
		$filterFollowing 	= FRoute::followers( array( 'filter' => 'following' ) , false );
		$filterSuggest 	= FRoute::followers( array( 'filter' => 'suggest' ) , false );

		if( !$user->isViewer() )
		{
			$title 	= $user->getName() . ' - ' . $title;

			$filterFollowers	= FRoute::followers( array( 'userid' => $user->getAlias() ) , false );
			$filterSuggest 	= FRoute::followers( array( 'userid' => $user->getAlias() , 'filter' => 'suggest' ) , false );
		}

		FD::page()->title( $title );

		// Set the breadcrumb
		FD::page()->breadcrumb( $title );

		// Get total followers and following
		$totalFollowers = $model->getTotalFollowers($user->id);
		$totalFollowing = $model->getTotalFollowing($user->id);
		$totalSuggest = $model->getTotalSuggestions($user->id);

		// var_dump($totalSuggest);

		$this->set( 'pagination' , $pagination );
		$this->set( 'user' , $user );
		$this->set( 'active' , $active );

		$this->set( 'filterFollowers'	, $filterFollowers );
		$this->Set( 'filterFollowing'	, $filterFollowing );
		$this->Set( 'filterSuggest'	, $filterSuggest );

		$this->set( 'totalFollowers'	, $totalFollowers );
		$this->set( 'totalFollowing'	, $totalFollowing );
		$this->set( 'totalSuggest'	, $totalSuggest );

		$this->set( 'currentUser'		, $user );
		$this->set( 'users'		, $users );
		$this->set( 'privacy'	, $privacy );

		// Load theme files.
		return parent::display( 'site/followers/default' );
	}

	/**
	 * Displays a restricted page
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id
	 */
	public function restricted( $user )
	{
		$this->set( 'showProfileHeader', true);
		$this->set( 'user'   , $user );

		echo parent::display( 'site/followers/restricted' );
	}
}
