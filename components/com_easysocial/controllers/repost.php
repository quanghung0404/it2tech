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

FD::import( 'site:/controllers/controller' );

class EasySocialControllerRepost extends EasySocialController
{

	/**
	 * Retrieves a list of user that shared a particular item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getSharers()
	{
		// Check for request forgeries
		FD::checkToken();

		// User needs to be logged in
		FD::requireLogin();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the current stream property.
		$id 		= JRequest::getInt( 'id' );
		$element 	= JRequest::getString( 'element' );

		// If id is invalid, throw an error.
		if (!$id || !$element) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_ID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$model 		= FD::model( 'Repost' );
		$sharers	= $model->getRepostUsers( $id , $element , false );

		return $view->call( __FUNCTION__ , $sharers );
	}

	/**
	 * Toggle the likes on an object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param
	 * @return	string
	 */
	public function share()
	{
		// Check for request forgeries.
		FD::checkToken();

		// User needs to be logged in.
		FD::requireLogin();

		// Get the stream / album / photo id depending on the element
		$id	= JRequest::getInt('id');
		$element = JRequest::getString('element');
		$group = JRequest::getString('group', SOCIAL_APPS_GROUP_USER);
		$clusterId = JRequest::getString('clusterId', '');
		$clusterType = JRequest::getString('clusterType', '');
		$content = JRequest::getVar('content', '' );

		// Get the view.
		$view = $this->getCurrentView();

		// If id is invalid, throw an error.
		if (!$id || !$element) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_ID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get current logged in user.
		$my = FD::user();

		// Load repost library.
		$share = FD::get('Repost', $id, $element, $group, $clusterId, $clusterType);
		$state = $share->add($my->id, $content);

		// If there's an error, log this down here.
		if ($state === false) {
			// Set the view with error
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_REPOST_ERROR_REPOSTING' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ , $id , $element, $group );
		}

		// Check if there are mentions provided from the post.
		$mentions = JRequest::getVar('mentions');

		// Format the json string to array
		if ($mentions) {
			foreach($mentions as &$mention) {
				$mention = FD::json()->decode($mention);
			}
		}

		// Notify the actor that created this stream object

		// Now lets determine if we need to add the stream or not.
		$streamId = 0;

		if($state !== true) {
			// this is an new share object.
			// lets add this share into stream.
			$stream	= FD::stream();
			$streamTemplate	= $stream->getTemplate();

			// Set the actor.
			$streamTemplate->setActor($state->user_id, SOCIAL_TYPE_USER);

			// Set the context.
			$streamTemplate->setContext($state->id, SOCIAL_TYPE_SHARE);

			// set the target. photo / stream
			$streamTemplate->setTarget($id);

			// Set mentions
			$streamTemplate->setMentions($mentions);

			// Set the verb.
			$streamTemplate->setVerb('add' . '.' . $element);

			$streamTemplate->setType('full');

			$streamTemplate->setAccess('core.view');

			if ($clusterId && $clusterType) {

				$cluster = null;
				if ($clusterType == SOCIAL_TYPE_GROUP) {
					$cluster = ES::group($clusterId);
				} else {
					$cluster = ES::event($clusterId);
				}

				$streamTemplate->setCluster($clusterId, $clusterType, $cluster->type);
			}

			// Create the stream data.
			$streamItem = $stream->add($streamTemplate);
			$streamId = $streamItem->uid;
		}

		return $view->call(__FUNCTION__, $id, $element, $group, $streamId);
	}
}
