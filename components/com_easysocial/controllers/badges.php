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

// Import main controller
FD::import( 'site:/controllers/controller' );

class EasySocialControllerBadges extends EasySocialController
{
	public function loadAchievers()
	{
		FD::checkToken();

		$view = $this->getCurrentView();

		$id = JRequest::getInt( 'id' );

		$start = JRequest::getInt( 'start' );

		$theme = FD::themes();

		$options = array(
			'start' => $start,
			'limit' => $theme->getConfig()->get( 'achieverslimit', 50 )
		);

		$model = FD::model( 'badges' );

		$achievers = $model->getAchievers( $id, $options );

		$html = '';

		if( $achievers )
		{
			foreach( $achievers as $user )
			{
				$html .= $theme->loadTemplate( 'site/badges/default.item.achiever', array( 'user' => $user ) );
			}
		}

		$view->call( __FUNCTION__, $html );
	}
}
