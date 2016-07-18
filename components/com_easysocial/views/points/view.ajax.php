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
	/**
	 * Displays user's points history
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getHistory()
	{
		$ajax 	= FD::ajax();

		// Get the user id to lookup
		$id 	= JRequest::getInt( 'id' );

		// Load the user based on the id.
		$user 	= FD::user( $id );

		$config 		= FD::config();
		$options 		= array( 'limit' => $config->get( 'points.history.limit' ) );

		$model 			= FD::model( 'Points' );

		// Get a list of histories for the user's points achievements.
		$histories		= $model->getHistory( $user->id , $options );
		$pagination		= $model->getPagination();

		$this->set( 'paginate'	, true );
		$this->set( 'histories'	, $histories );
		$this->set( 'user'		, $user );

		$contents 		= parent::display( 'site/points/default.history.item' );

		$done			= $pagination->total <= ( $pagination->limitstart + $pagination->limit );

		return $ajax->resolve( $contents , $pagination->pagesCurrent * $pagination->limit , $done );
	}
}
