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
 * Dashboard view for Feeds app.
 *
 * @since	1.0
 * @access	public
 */
class FeedsViewProfile extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 */
	public function display( $userId = null , $docType = null )
	{
		// Get the blog model
		$model 	= $this->getModel( 'Feeds' );

		// Get the params
		$params = $this->getUserParams( $userId );

		// Get the app params
		$appParams = $this->app->getParams();

		$limit 	= $params->get( 'total' , $appParams->get( 'total' , 5 ) );

		// Get list of blog posts created by the user on the site.
		$result = $model->getItems( $userId , $limit );
		$feeds 	= array();

		// Properly format feed items.
		if( $result )
		{
			foreach( $result as $row )
			{
				$feed 	= $this->getTable( 'Feed' );
				$feed->bind( $row );

				// Initialize the parser.
				$parser	= $feed->getParser();

				$feed->total 	= $parser->get_item_quantity();
				$feed->items 	= $parser->get_items( 0 , $limit );

				$feeds[]	= $feed;
			}
		}

		$this->set( 'totalDisplayed' , $limit );
		$this->set( 'params'	, $params );
		$this->set( 'feeds'		, $feeds );

		echo parent::display( 'profile/default' );
	}
}
