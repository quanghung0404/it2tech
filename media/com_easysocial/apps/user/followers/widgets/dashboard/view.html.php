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

class FollowersWidgetsDashboard extends SocialAppsWidgets
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
		// Get application params
		$appParams = $this->getParams();

		$user = FD::user();

		// Get the user params
		$params = $this->getUserParams($user->id);

		if ($appParams->get('widget_suggestions', true)) {
			echo $this->getSuggestions($params);
		}
	}

	/**
	 * Display a list of followers for the user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSuggestions( &$params )
	{
		$user = FD::user();

		$appParams = $this->app->getParams();

		if( !$params->get( 'widget_suggestions' , $appParams->get( 'widget_suggestions' , true ) ) ) {
			return;
		}

		$limit = $params->get('limit', $appParams->get('widget_suggestions_total', 5));

		$model = FD::model( 'Followers' );
		$users = $model->getSuggestions( $user->id, array('max' => $limit) );
		$total = $model->getTotalSuggestions( $user->id );

		$theme 		= FD::themes();
		$theme->set('total', $total);
		$theme->set('users', $users);
		$theme->set('limit', $limit);

		return $theme->output('themes:/apps/user/followers/widgets/dashboard/suggestions');
	}

}
