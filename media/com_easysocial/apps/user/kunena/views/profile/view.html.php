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

require_once(dirname(dirname(__DIR__)) . '/helper.php');

class KunenaViewProfile extends SocialAppsView
{
	public function display($userId = null, $docType = null)
	{
		if (!KunenaHelper::exists()) {
			return;
		}

		// Load language file from Kunena
		KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');

		// Load Kunena's language file
		JFactory::getLanguage()->load('com_kunena.libraries', JPATH_ADMINISTRATOR);

		// Get the current user
		$user = FD::user($userId);

		// Get the user params
		$params = $this->getUserParams($user->id);

		// Get the app params
		$appParams = $this->app->getParams();

		// Get the total items to display
		$total = (int) $params->get('total_post_display', $appParams->get('total_post_display', 5));

		// Get the posts created by the user.
		$model = $this->getModel('Posts');
		$posts = $model->getPosts($user->id, $total);

		// Get the replies
		$replies = $model->getReplies($user->id);

		// Get stats
		$stats = $model->getStats($user->id);

		// Get total replies
		$totalReplies = $model->getTotalReplies($user->id);

		// Get Kunena's template
		$kTemplate = KunenaFactory::getTemplate();

		$kUser = KunenaUserHelper::get($userId);

		$this->set('totalReplies', $totalReplies);
		$this->set( 'stats'		, $stats );
		$this->set( 'thanks'	, $kUser->thankyou );
		$this->set( 'totalPosts', $kUser->posts );
		$this->set( 'kTemplate'	, $kTemplate );
		$this->set( 'user'		, $user );
		$this->set( 'params'	, $params );
		$this->set( 'posts' 	, $posts );
		$this->set( 'replies'	, $replies );


		echo parent::display( 'profile/default' );
	}
}
