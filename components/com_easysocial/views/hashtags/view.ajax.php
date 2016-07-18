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

FD::import( 'site:/views/views' );

class EasySocialViewHashtags extends EasySocialSiteView
{
	/**
	 * Returns a result of suggested hash tag
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function suggest( $result = array() )
	{
		$ajax 	= FD::ajax();

		// If there's nothing, just return the empty object.
		if( !$result )
		{
			return $ajax->resolve(array());
		}

		// Format result to SocialUser object.
		$tags 	= array();

		// Load through the result list.
		foreach( $result as $tag )
		{
			$obj 			= new stdClass();
			$obj->title 	= $tag->title;

			$tags[]	= $obj;
		}

		return $ajax->resolve( $tags );
	}
}
