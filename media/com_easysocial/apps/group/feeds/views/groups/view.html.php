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
 * Dashboard view for Notes app.
 *
 * @since	1.3
 * @access	public
 */
class FeedsViewGroups extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 */
	public function display($groupId = null, $docType = null )
	{
		$group 	= FD::group($groupId);

		// Render the rss model
		$model = FD::model('RSS');
		$result = $model->getItems($group->id, SOCIAL_TYPE_GROUP);

		// If there are tasks, we need to bind them with the table.
		$feeds 	= array();

		if ($result) {

			foreach ($result as $row) {

				// Bind the result back to the note object.
				$rss = FD::table('Rss');
				$rss->bind($row);

				// Initialize the parser.
				$parser = JFactory::getFeedParser($rss->url);

				$rss->parser = false;
				
				if ($parser) {
					$rss->parser = $parser;
					$rss->total = @$parser->get_item_quantity();
					$rss->items = @$parser->get_items();
				}

				$feeds[] = $rss;
			}
		}

		// Get the app params
		$params = $this->app->getParams();
		$limit 	= $params->get('total', 5);

		$this->set('totalDisplayed', $limit);
		$this->set('appId', $this->app->id);
		$this->set('group', $group);
		$this->set('feeds', $feeds);

		echo parent::display('views/default');
	}
}
