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

FD::import( 'admin:/controllers/controller' );

class EasySocialControllerReports extends EasySocialController
{
	/**
	 * Deletes specific reports
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function removeItem()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view.
		$view	= $this->getCurrentView();

		// Get the id from the request
		$id 	= JRequest::getInt( 'id' );

		// Load the report
		$report 	= FD::table( 'Report' );
		$report->load( $id );

		// Try to delete the report now.
		$state 	= $report->delete();

		if( !$state )
		{
			$view->setMessage( $report->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// @points: reports.delete
		// Deduct points from the author when their report is deleted.
		$points = FD::points();
		$points->assign( 'reports.delete' , 'com_easysocial' , $report->created_by );

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_REPORTS_REPORT_ITEM_HAS_BEEN_DELETED' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Deletes reports
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view.
		$view	= $this->getCurrentView();

		// Get the id from the request
		$ids 	= JRequest::getVar( 'cid' );

		// If the user is deleting with the checkbox, find similar reports
		$model 	= FD::model( 'Reports' );

		foreach( $ids as $id )
		{
			$tmpReport 	= FD::table( 'Report' );
			$tmpReport->load( $id );

			// Load all related reports
			$reports 	= $model->getReporters( $tmpReport->extension , $tmpReport->uid , $tmpReport->type );

			foreach( $reports as $report )
			{
				$report->delete();

				// @points: reports.delete
				// Deduct points from the author when their report is deleted.
				$points = FD::points();
				$points->assign( 'reports.delete' , 'com_easysocial' , $report->created_by );
			}
		}


		$view->setMessage( JText::_( 'COM_EASYSOCIAL_REPORTS_REPORT_ITEM_HAS_BEEN_DELETED' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Purge all reports on site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function purge()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get current view
		$view 	= $this->getCurrentView();

		// Get reports model
		$model 	= FD::model( 'Reports' );

		$state 	= $model->purge();

		if( !$state )
		{
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_REPORTS_PURGED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Stores a submitted report
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getReporters()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the report id.
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			$this->view->setMessage(JText::_( 'Invalid report id provided.' ) , SOCIAL_MSG_ERROR );
			return $this->view->call( __FUNCTION__ );
		}

		$report = FD::table('Report');
		$report->load($id);

		$model = FD::model('Reports');
		$reporters = $model->getReporters($report->extension, $report->uid, $report->type);

		return $this->view->call(__FUNCTION__, $reporters);
	}
}
