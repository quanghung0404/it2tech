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
 * Profile view for Notes app.
 *
 * @since	1.0
 * @access	public
 */
class BirthdayWidgetsDashboard extends SocialAppsWidgets
{
	/**
	 * Displays the dashboard widget
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom()
	{
		// Get the app params
		$params 	= $this->app->getParams();
		$key 		= $params->get( 'dashboard_show_uniquekey' , 'BIRTHDAY' );
		$displayYear = $params->get('dashboard_show_birthday' , 1 );

		// Get current logged in user
		$my 		= FD::user();
		$birthdays 	= $this->getUpcomingBirthdays( $key , $my->id );

		$ids 		= array();
		$dateToday 	= FD::date()->toFormat( 'md' );

		$today 		= array();
		$otherDays 	= array();

		// Hide app when there's no upcoming birthdays
		if( !$birthdays )
		{
			return;
		}

		$my = FD::user();
		$privacy = FD::privacy( $my->id );

		if( $birthdays )
		{
			foreach( $birthdays as $birthday )
			{
				$ids[]	= $birthday->uid;
			}

			// Preload list of users
			FD::user( $ids );

			foreach( $birthdays as $birthday )
			{
				$obj = new stdClass();
				$obj->user 		= FD::user( $birthday->uid );
				$obj->birthday 	= $birthday->displayday;

				//Checking to display year here
				if ($displayYear) {

					$dateFormat = JText::_('COM_EASYSOCIAL_DATE_DMY');

					//check birtday the year privacy
					if (! $privacy->validate( 'field.birthday', $birthday->field_id, 'year', $birthday->uid )) {

						$dateFormat = JText::_('COM_EASYSOCIAL_DATE_DM');
					}

				} else {
					$dateFormat = JText::_('COM_EASYSOCIAL_DATE_DM');
				}

				$obj->display	= FD::date($obj->birthday)->format($dateFormat);

				if ($birthday->day == $dateToday) {
					$today[]		= $obj;
				} else {
					$otherDays[]	= $obj;
				}
			}
		}

		$this->set( 'ids'		, $ids );
		$this->set( 'birthdays'	, $birthdays );
		$this->set( 'today'		, $today );
		$this->set( 'otherDays' , $otherDays );

		echo parent::display( 'widgets/upcoming.birthday' );
	}

	/**
	 * Get list of upcoming birhtdays
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUpcomingBirthdays( $key , $userId )
	{
		$db		= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.`uid`, DATE_FORMAT( a.`raw`, ' . $db->Quote( '%m%d' ) . ') as day, a.`field_id`,';
		$query .= ' DATE_FORMAT( a.`raw`, ' . $db->Quote( '%M %d %Y' ) . ') as displayday';
		$query .= ' from `#__social_fields_data` as a';
		$query .= ' INNER JOIN `#__social_fields` as b on a.`field_id` = b.`id`';
		$query .= ' INNER JOIN (';
		$query .= ' 	select `actor_id` as `user_id` from `#__social_friends` where `target_id` = ' . $db->Quote( $userId ) . ' and `state` = ' . $db->Quote( '1' );
		$query .= ' 	 union ';
		$query .= ' 	select `target_id` as `user_id` from `#__social_friends` where `actor_id` = '. $db->Quote( $userId ) . ' and `state` = ' . $db->Quote( '1' );
		$query .= ' 	 union ';
		$query .= ' 	select `uid` as `user_id` from `#__social_subscriptions` where `user_id` = '. $db->Quote( $userId ) . ' and `type` = ' . $db->Quote( 'user.user' );

		$query .= ' 	) as x on a.`uid` = x.`user_id`';
		$query .= ' INNER JOIN `#__social_users` as c on a.`uid` = c.`user_id`';

		// exclude esad users
		$query .= ' INNER JOIN `#__social_profiles_maps` as upm on c.`user_id` = upm.`user_id`';
		$query .= ' INNER JOIN `#__social_profiles` as up on upm.`profile_id` = up.`id` and up.`community_access` = 1';

		// @TODO: Here, it needs to fetch the field id based on the key.
		$query .= ' where b.`unique_key` = ' . $db->Quote( $key );
		$query .= ' and a.`uid` != ' . $db->Quote( $userId );
		$query .= ' and a.`raw` != ' . $db->Quote( '' );
		$query .= ' and c.`state` != ' . $db->Quote('0');

		/*
		Split to 2 sections


		If md >= curdate, then we get the interval year = curdate - rawyear
		If md < curdate, then we get the interval year = curdate - rawyear + 1

		We + 1 for md < curdate because if today is dec 30, but birthday jan 1, then without + 1 on the year, jan 1 won't be between 7 days between dec 30

		EXAMPLE:

		Birthday: 1990-01-02

		diffyear = curdate() - rawyear = 2013 - 1990 = 23

		date_add(1990-01-02, INTERVAL 23 YEAR) = 2013-01-02

		Even though 01-02 is the upcoming birthday for 12-30, but because we are getting range of date by:

		between curdate() and date_add(curdate(), interval 7 day)

		The part of date_add(curdate(), interval 7 day) is ranging from 2013-12-30 to 2014-01-06

		2013-01-02 will then fail to match
		*/

		$query	.= ' AND(';
		$query	.= '      (';
		$query	.= '        DATE_FORMAT( a.`raw`, ' . $db->Quote( '%m%d' ) . ') >= DATE_FORMAT( CURDATE(), ' . $db->Quote( '%m%d' ) . ')';
		$query	.= '        AND';
		$query 	.= '        DATE_ADD( a.`raw`, INTERVAL YEAR(CURDATE()) - YEAR( a.`raw`) YEAR )';
		$query 	.= '        BETWEEN CURDATE() AND DATE_ADD( CURDATE() , INTERVAL 7 DAY )';
		$query	.= '      )';
		$query	.= '      OR';
		$query	.= '      (';
		$query	.= '        a.`raw` < CURDATE()';
		$query	.= '        AND';
		$query 	.= '        DATE_ADD( a.`raw`, INTERVAL YEAR(CURDATE()) - YEAR( a.`raw`) + 1 YEAR )';
		$query 	.= '        BETWEEN CURDATE() AND DATE_ADD( CURDATE() , INTERVAL 7 DAY )';
		$query 	.= '      )';
		$query	.= ' )';
		$query 	.= ' order by DATE_FORMAT( a.`raw`, ' . $db->Quote( '%m%d' ) . ') asc';

		$sql->raw($query);
		$db->setQuery($sql);

		$result = $db->loadObjectList();

		return $result;
	}


}
