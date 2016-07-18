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

class ThemesHelperHTML
{
	/**
	 * Displays the mini header of an object
	 *
	 * @since 	1.2
	 * @access	public
	 */
	public static function miniheader($obj)
	{
		$theme = FD::themes();

		if ($obj instanceof SocialUser) {
			$theme->set('user', $obj);
			$output = $theme->output('site/profile/mini.header');
		}

		if ($obj instanceof SocialGroup) {
			$theme->set('group', $obj);
			$output = $theme->output('site/groups/mini.header');
		}

		if ($obj instanceof SocialEvent) {

			$theme->set('event', $obj);
			$output = $theme->output('site/events/mini.header');
		}

		return $output;
	}

	/**
	 * Renders the online state of a user
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function online($online = false, $size = 'small')
	{
		$theme = ES::themes();

		$theme->set('online', $online);
		$theme->set('size', $size);

		$output = $theme->output('site/utilities/user.online.state');

		return $output;
	}

	/**
	 * Displays the video's title in html format
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function video(SocialVideo $video)
	{
		$theme = ES::themes();

		$theme->set('video', $video);

		$output = $theme->output('admin/html/html.video');

		return $output;
	}

	/**
	 * Displays the author's name in html format
	 *
	 * @since	1.1
	 * @access	public
	 * @param	int		The user's id.
	 * @param	bool	True if to display a popbox
	 * @return
	 */
	public static function user($id, $popbox = false, $popboxPosition = 'top-left', $avatar = false)
	{
		if (is_object($id)) {
			$user = $id;
		} else {
			$user = FD::user($id);
		}

		if ($user->block) {
			return $user->getName();
		}

		$theme 	= FD::themes();

		$theme->set('popbox', $popbox);
		$theme->set('avatar', $avatar);
		$theme->set('position', $popboxPosition);
		$theme->set('user', $user);

		$output = $theme->output('admin/html/html.user');

		return $output;
	}

	/**
	 * Renders the event link
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function event($obj, $popbox = false, $popboxPosition = 'top-left')
	{
		if (!is_object($obj)) {
			$event = FD::event($obj);
		}

		if ($obj instanceof SocialEvent) {
			$event = $obj;
		}

		$theme = FD::themes();

		$theme->set('popbox', $popbox);
		$theme->set('position', $popboxPosition);
		$theme->set('event', $event);

		$output = $theme->output('admin/html/html.event');

		return $output;
	}

	/**
	 * Displays the groups's name in html format
	 *
	 * @since	1.1
	 * @access	public
	 * @param	int		The user's id.
	 * @param	bool	True if to display a popbox
	 * @return
	 */
	public static function group( $id , $popbox = false , $popboxPosition = 'top-left' )
	{
		$group 	= FD::group( $id );

		$theme 	= FD::themes();

		$theme->set( 'popbox'	, $popbox );
		$theme->set( 'position'	, $popboxPosition );
		$theme->set( 'group' 	, $group );

		$output 	= $theme->output( 'admin/html/html.group' );

		return $output;
	}

	/**
	 * Renders a map popbox
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function map(SocialLocation $location, $displayIcon = false)
	{
		$theme = ES::themes();

		$latitude = $location->getLatitude();
		$longitude = $location->getLongitude();
		$address = $location->getAddress();

		$theme->set('displayIcon', $displayIcon);
		$theme->set('latitude', $latitude);
		$theme->set('longitude', $longitude);
		$theme->set('address', $address);

		$output = $theme->output('admin/html/html.map');

		return $output;
	}
}
