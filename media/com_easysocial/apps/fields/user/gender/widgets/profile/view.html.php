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
class GenderFieldWidgetsProfile
{
	/**
	 * Displays the gender in the position profileHeaderA
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function profileHeaderA( $key , $user , $field )
	{
		$value 	= $field->data;

		// If user didn't set their gender, don't need to do anything
		if( !$value )
		{
			return;
		}

		$my			= FD::user();
		$privacyLib = FD::privacy( $my->id );
		if( !$privacyLib->validate( 'core.view' , $field->id, SOCIAL_TYPE_FIELD , $user->id ) )
		{
			return;
		}

		$theme 	= FD::themes();
		$theme->set( 'value'	, $value );
		$theme->set( 'params'	, $field->getParams() );

		// linkage to advanced search page.
		if ($field->type == SOCIAL_FIELDS_GROUP_USER && $field->searchable) {
			$params = array( 'layout' => 'advanced' );
			$params['criterias[]'] = $field->unique_key . '|' . $field->element;
			$params['operators[]'] = 'equal';
			$params['conditions[]'] = $value;

			$advsearchLink = FRoute::search($params);
			$theme->set( 'advancedsearchlink'	, $advsearchLink );
		}

		echo $theme->output( 'fields/user/gender/widgets/display' );
	}
}
