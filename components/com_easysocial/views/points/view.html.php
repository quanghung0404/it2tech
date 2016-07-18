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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Import parent view
FD::import( 'site:/views/views' );

class EasySocialViewPoints extends EasySocialSiteView
{
	private function checkFeature()
	{
		$config	= FD::config();

		// Do not allow user to access photos if it's not enabled
		if( !$config->get( 'points.enabled' ) )
		{
			$this->setMessage( JText::_( 'COM_EASYSOCIAL_POINTS_DISABLED' ) , SOCIAL_MSG_ERROR );

			FD::info()->set( $this->getMessage() );
			$this->redirect( FRoute::dashboard( array() , false ) );
			$this->close();
		}
	}

	/**
	 * Default method to display the registration page.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	function display( $tpl = null )
	{
		$this->checkFeature();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Set the page title
		FD::page()->title( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_POINTS' ) );

		// Set the page breadcrumb
		FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_POINTS' ) );

		// Get list of badges.
		$model		= FD::model( 'Points' );

		// Get number of badges to display per page.
		$limit 		= FD::themes()->getConfig()->get( 'pointslimit' );

		$options	= array( 'limit' => $limit, 'published' => '1' );

		$points		= $model->getItems( $options );
		$pagination	= $model->getPagination();

		$this->set( 'pagination', $pagination );
		$this->set( 'points' 	, $points );

		parent::display( 'site/points/default' );
	}

	/**
	 * Displays user's points history
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function history()
	{
		$this->checkFeature();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		$id 	= JRequest::getInt( 'userid' );

		if( !$id )
		{
			$id 	= null;
		}

		$user 	= FD::user( $id );

		// If the user id is not provided, we need to display some error message.
		if( !$user->id )
		{
			FD::info()->set( JText::_( 'COM_EASYSOCIAL_POINTS_INVALID_USER_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $this->redirect( FRoute::dashboard( array() , false ) );
		}

		// If the user blocked, we need to display some error message.
		if( $user->isBlock() )
		{
			FD::info()->set( JText::sprintf( 'COM_EASYSOCIAL_POINTS_USER_NOT_EXIST', $user->getName() ) , SOCIAL_MSG_ERROR );
			return $this->redirect( FRoute::dashboard( array() , false ) );
		}


		if (! JFactory::getUser()->guest && (JFactory::getUser()->id != $user->id)) {
			if(FD::user()->isBlockedBy($user->id)) {
				return JError::raiseError(404, JText::_('COM_EASYSOCIAL_POINTS_USER_NOT_EXIST'));
			}
		}

		// Language should be loaded for the back end.
		FD::language()->loadAdmin();

		// Set the page title
		FD::page()->title( JText::sprintf( 'COM_EASYSOCIAL_PAGE_TITLE_POINTS_USER_HISTORY' , $user->getName() ) );

		// Set the page breadcrumb
		FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_POINTS' ) , FRoute::points() );
		FD::page()->breadcrumb( JText::sprintf( 'COM_EASYSOCIAL_PAGE_TITLE_POINTS_USER_HISTORY' , $user->getName() ) );


		$my 		= FD::user();
		$privacy 	= $my->getPrivacy();

		// Let's test if the current viewer is allowed to view this profile.
		if ($my->id != $user->id && !$privacy->validate('profiles.view', $user->id, SOCIAL_TYPE_USER)) {
			$facebook = ES::oauth('facebook');
			$return = base64_encode($user->getPermalink());

			$this->set('return', $return);
			$this->set('facebook', $facebook);
			$this->set('user', $user);

			parent::display('site/profile/restricted');

			return;
		}



		$config 		= FD::config();
		$options 		= array( 'limit' => $config->get( 'points.history.limit' ) );

		$model 			= FD::model( 'Points' );

		// Get a list of histories for the user's points achievements.
		$histories		= $model->getHistory( $user->id , $options );

		$pagination		= $model->getPagination();

		$this->set( 'pagination', $pagination );
		$this->set( 'histories'	, $histories );
		$this->set( 'user'		, $user );

		parent::display( 'site/points/default.history' );
	}

	/**
	 * Default method to display the registration page.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	function item( $tpl = null )
	{
		$this->checkFeature();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		$id 	= JRequest::getInt( 'id' );

		$point 	= FD::table( 'Points' );
		$point->load( $id );

		if( !$id || !$point->id )
		{
			FD::info()->set( null , JText::_( 'The points id provided is not a valid id.' ) , SOCIAL_MSG_ERROR );
			return $this->redirect( FRoute::dashboard( array() , false ) );
		}

		$point->loadLanguage();

		// Load language file.
		JFactory::getLanguage()->load( 'com_easysocial' , JPATH_ROOT . '/administrator' );

		// Set the page title
		FD::page()->title( $point->get( 'title' ) );

		// Set the page breadcrumb
		FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_POINTS' ) , FRoute::points() );
		FD::page()->breadcrumb( $point->get( 'title' ) );

		// Get list of point achievers.
		$achievers 	= $point->getAchievers();

		$this->set( 'achievers' , $achievers );
		$this->set( 'point'		, $point );

		parent::display( 'site/points/default.item' );
	}
}
