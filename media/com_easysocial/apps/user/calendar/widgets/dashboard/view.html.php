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
 * Dashboard widget for calendar
 *
 * @since	1.0
 * @access	public
 */
class CalendarWidgetsDashboard extends SocialAppsWidgets
{
	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		// Get the application params
		$params 	= $this->getParams();

		if (!$params->get('widgets_upcoming', true)) {
			return;
		}

		echo $this->getUpcomingSchedules();
	}

	/**
	 * Displays online users widget
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUpcomingSchedules()
	{
		$my 			= FD::user();
		$params 		= $this->getParams();

		// Get a list of schedules
		$model			= $this->getModel( 'Calendar' );
		$days 			= $params->get( 'widgets_days' , 14 );
		$total 			= $params->get( 'widgets_total' , 5 );
		$result 		= $model->getUpcomingSchedules( $my->id , $days , $total );
		$appointments	= array();

		if( $result )
		{
			foreach ($result as $row) {
				$calendar = FD::table('Calendar');
				$calendar->bind( $row );

				$appointments[]	= $calendar;
			}
		}

		$theme 		= FD::themes();
		$theme->set( 'appointments'	, $appointments );
		$theme->set( 'app'			, $this->app );

		return $theme->output( 'themes:/apps/user/calendar/widgets/dashboard/schedules' );
	}
}
