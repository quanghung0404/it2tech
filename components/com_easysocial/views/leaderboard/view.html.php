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

// Import main views
FD::import( 'site:/views/views' );

class EasySocialViewLeaderBoard extends EasySocialSiteView
{
	/**
	 * Default display method for leader board
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	function display( $tpl = null )
	{
		// Check for user profile completeness
		FD::checkCompleteProfile();

		$config 	= FD::config();
		$limit 		= $config->get( 'layout.leaderboard.limit' );

		$model 		= FD::model( 'Leaderboard' );

		// Should we exclude admin here
		$excludeAdmin	= !$config->get( 'leaderboard.listings.admin' );

		$options	= array( 'ordering' => 'points' , 'limit' => $limit , 'excludeAdmin' => $excludeAdmin );

		$users 		= $model->getLadder( $options , false );

		// Set the page title
		FD::page()->title( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_LEADERBOARD' ) );

		$this->set( 'users'		, $users );

		echo parent::display( 'site/leaderboard/default' );
	}
}
