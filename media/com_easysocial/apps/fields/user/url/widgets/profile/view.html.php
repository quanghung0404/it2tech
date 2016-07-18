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
class UrlFieldWidgetsProfile
{
	public function profileHeaderD( $key , $user , $field )
	{
		// Get the data
		$data 	= $field->data;

		if( !$data )
		{
			return;
		}

		$my = FD::user();
		$privacyLib = FD::privacy( $my->id );
		if( !$privacyLib->validate( 'core.view' , $field->id, SOCIAL_TYPE_FIELD , $user->id ) )
		{
			return;
		}

		// If there's no http:// or https:// , automatically append http://
		if( stristr( $data , 'http://' ) === false && stristr( $data , 'https://' ) === false )
		{
			$data 	= 'http://' . $data;
		}

		$theme 	= FD::themes();
		$theme->set( 'value'	, $data );
		$theme->set( 'params'	, $field->getParams() );

		echo $theme->output( 'fields/user/url/widgets/display' );
	}
}
