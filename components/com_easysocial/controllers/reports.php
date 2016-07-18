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

FD::import( 'site:/controllers/controller' );

class EasySocialControllerReports extends EasySocialController
{

	/**
	 * Stores a submitted report
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get data from $_POST
		$post 		= JRequest::get( 'post' );

		// Get current view.
		$view 		= $this->getCurrentView();

		// Get the current logged in user
		$my 		= FD::user();

		// Determine if the user is a guest
		$config 	= FD::config();

		if( !$my->id && !$config->get('reports.guests', false)) {
			return;
		}
		// Determine if this user has the permissions to submit reports.
		$access 	= FD::access();

		if (!$access->allowed( 'reports.submit')) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_REPORTS_NOT_ALLOWED_TO_SUBMIT_REPORTS' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the reports model
		$model 		= FD::model('Reports');

		// Determine if this user has exceeded the number of reports that they can submit
		$total 		= $model->getCount( array( 'created_by' => $my->id ) );

		if ($access->exceeded( 'reports.limit' , $total)) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_REPORTS_LIMIT_EXCEEDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Create the report
		$report 	= FD::table( 'Report' );
		$report->bind($post);

		// Try to get the user's ip address.
		$report->ip = JRequest::getVar( 'REMOTE_ADDR' , '' , 'SERVER' );

		// Set the creator id.
		$report->created_by 	= $my->id;

		// Set the default state of the report to new
		$report->state 			= 0;

		// Try to store the report.
		$state 	= $report->store();

		// If there's an error, throw it
		if (!$state) {
			$view->setMessage( $report->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// @badge: reports.create
		// Add badge for the author when a report is created.
		$badge 	= FD::badges();
		$badge->log( 'com_easysocial' , 'reports.create' , $my->id , JText::_( 'COM_EASYSOCIAL_REPORTS_BADGE_CREATED_REPORT' ) );

		// @points: reports.create
		// Add points for the author when a report is created.
		$points = FD::points();
		$points->assign( 'reports.create' , 'com_easysocial' , $my->id );

		// Determine if we should send an email
		$config 	= FD::config();

		if ($config->get('reports.notifications.moderators')) {
			$report->notify();
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_REPORTS_STORED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}
}
