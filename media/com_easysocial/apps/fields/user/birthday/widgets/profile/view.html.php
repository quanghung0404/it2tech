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

FD::import('fields:/user/datetime/datetime');

/**
 * Birthday widget to show age on profile page.
 *
 * @since	1.0
 * @access	public
 */
class BirthdayFieldWidgetsProfile
{
	/**
	 * Renders the age of the user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAge( $value )
	{
		$birthDate 		= new DateTime( $value );

		// Don't use diff because PHP 5.2 doesn't support
		// $age			= $birthDate->diff( new DateTime );

		$now = new DateTime();

		$years = floor(($now->format('U') - $birthDate->format('U')) / (60*60*24*365));

		return $years;
	}

	/**
	 * Displays the age in the position profileHeaderA
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function profileHeaderA( $key , $user , $field )
	{
		$my = FD::user();
		$privacyLib = FD::privacy( $my->id );
		if( !$privacyLib->validate( 'core.view' , $field->id, SOCIAL_TYPE_FIELD , $user->id ) )
		{
			return;
		}

		$params = $field->getParams();

		if( $params->get( 'show_age' ) && !$privacyLib->validate( 'field.birthday', $field->id, 'year', $user->id ) )
		{
			return;
		}

		// Get the current stored value.
		$value 	= $field->data;

		if( empty( $value ) )
		{
			return false;
		}

		if (is_array($value) && isset($value['date']) && !$value['date']) {
			// empty value. just return empty string.
			return false;
		}

		$data = new SocialFieldsUserDateTimeObject($value);

		$date = null;

		if( !empty( $data->year ) && !empty( $data->month ) && !empty( $data->day ) )
		{
			$date = $data->year . '-' . $data->month . '-' . $data->day;
		}

		if( !$date )
		{
			return;
		}

        $allowYear = true;

		$theme 	= FD::themes();

		if( $params->get( 'show_age' ) )
		{
			// Compute the age now.
			$age 	= $this->getAge( $date );
			$theme->set( 'value', $age );
		}
		else
		{
			$allowYear = $privacyLib->validate( 'field.birthday', $field->id, 'year', $user->id );
			$format = $allowYear ? 'j F Y' : 'j F';

			$birthday = FD::date( $date, false )->format( $format );
			$theme->set( 'value', $birthday );
		}

        // linkage to advanced search page.
        if ($field->type == SOCIAL_FIELDS_GROUP_USER && $allowYear && $field->searchable) {
            $date = $data->format('Y-m-d');

            $params = array( 'layout' => 'advanced' );
            $params['criterias[]'] = $field->unique_key . '|' . $field->element;
            $params['operators[]'] = 'between';
            $params['conditions[]'] = $date . ' 00:00:00' . '|' . $date . ' 23:59:59';

            $advsearchLink = FRoute::search($params);
            $theme->set( 'advancedsearchlink'    , $advsearchLink );
        }

		$theme->set( 'params'	, $params );

		echo $theme->output( 'fields/user/birthday/widgets/display' );
	}
}
