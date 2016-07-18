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
defined('_JEXEC') or die('Unauthorized Access');

class MtreeViewProfile extends SocialAppsView
{
	public function display($userId = null, $docType = null)
	{
		// Get the current user
		$user = FD::user($userId);

		// Get the app params
		$params = $this->app->getParams();

		// Load up the model
		$model = $this->getModel('Listing');

		// Get listings
		$items = $model->getListings($user->id, $params->get('listing_limit', 5));

		// Get reviews
		$reviews = $model->getReviews($user->id, $params->get('reviews_limit', 5));

		// Get favorites
		$favorites = $model->getFavorites($user->id, $params->get('favorites_limit', 5));

		$Itemid = $model->getItemid();
		
		$this->set('params', $params);		
		$this->set('Itemid', $Itemid);
		$this->set('favorites', $favorites);
		$this->set('reviews', $reviews);
		$this->set('items', $items);

		echo parent::display('profile/default');
	}
}
