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

class EasySocialControllerLikes extends EasySocialController
{

	/**
	 * display the remainder's name.
	 *
	 * @since	1.0
	 * @access	public
	 * @param
	 * @return	string
	 */

	public function showOthers()
	{
		// Check for request forgeries.
		FD::checkToken();

		// User needs to be logged in.
		FD::requireLogin();

		$uid 	= JRequest::getInt( 'uid' );
		$type 	= JRequest::getVar( 'type' );
		$group 	= JRequest::getVar( 'group', SOCIAL_APPS_GROUP_USER );
		$verb 	= JRequest::getVar( 'verb', '' );

		// Get the list of excluded ids
		$excludeIds = JRequest::getVar( 'exclude' );

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the likes model
		$model 	= FD::model( 'Likes' );

		// Form the exclusion id's into an array
		$excludeIds	= explode( ',', $excludeIds );

		$key	= $type . '.' . $group;

		// If verb is set, use the verb
		if( !empty($verb) )
		{
			$key 	.= '.' . $verb;
		}

		$userIds = $model->getLikerIds( $uid, $key, $excludeIds );

		$users	= array();

		if( $userIds && count( $userIds ) > 0 )
		{
			$users = FD::user( $userIds );
		}

		return $view->call( __FUNCTION__ , $users );
	}

	/**
	 * Toggle the likes on an object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param
	 * @return	string
	 */
	public function toggle()
	{
		// Check for request forgeries.
		ES::checkToken();

		// User needs to be logged in.
		ES::requireLogin();

		// Get the stream id.
		$id = $this->input->get('id', 0, 'int');

		// Get the type
		$type = $this->input->get('type', '', 'string');

		// Get the group
		$group = $this->input->get('group', SOCIAL_APPS_GROUP_USER, 'string');

		// Get the verb
		$itemVerb = $this->input->get('verb', '', 'string');

		// Get the stream id
		$streamId = $this->input->get('streamid', 0, 'int');

		// If id is invalid, throw an error.
		if (!$id || !$type) {
			$this->view->setMessage( JText::_( 'COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_ID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Load likes library.
		$model = ES::model('Likes');

		// Build the key for likes
		$key = $type . '.' . $group;

		if ($itemVerb) {
			$key = $key . '.' . $itemVerb;
		}

		// Determine if user has liked this item previously.
		$hadLiked = $model->hasLiked($id, $key, $this->my->id);

		// If user had already liked this item, we need to unlike it.
		if ($hadLiked) {

			// Should we use the stream id to relate the likes
			$useStreamId = ($type == 'albums' || $type == 'video') ? '' : $streamId;

			$state = $model->unlike($id, $key, $this->my->id, $useStreamId);

			// we need to revert the last action of this stream.
			if ($state && $useStreamId) {
				$stream = ES::stream();
				$stream->revertLastAction($streamId, $this->my->id, SOCIAL_STREAM_LAST_ACTION_LIKE);

				// Assign unlike points to the stream author.
				$streamActor = $stream->getStreamActor($streamId);

				// Check if user trying to unlike his own post, do not deduct the points.
				if ($streamActor->id != $this->my->id) {
					ES::points()->assign('post.unlike', 'com_easysocial', $streamActor->id);
				}
			}

		} else {

			$useStreamId = ($type == 'albums' || $type == 'video') ? '' : $streamId;

			$state = $model->like($id, $key, $this->my->id, $useStreamId);

			// Now we need to update the associated stream id from the liked object
			if ($streamId) {
				
				$doUpdate = true;
				
				if ($type == 'photos') {
					$sModel = ES::model('Stream');
					$totalItem = $sModel->getStreamItemsCount($streamId);

					if ($totalItem > 1) {
						$doUpdate = false;
					}
				}

				if ($doUpdate) {
					$stream = ES::stream();
					$stream->updateModified($streamId, $this->my->id, SOCIAL_STREAM_LAST_ACTION_LIKE);

					// Assign like points to the stream author.
					$streamActor = $stream->getStreamActor($streamId);

					// Check if user trying to like his own post, do not add points.
					if ($streamActor->id != $this->my->id) {
						ES::points()->assign('post.like', 'com_easysocial', $streamActor->id);
					}
				}
			}
		}

		// The current action
		$verb = $hadLiked ? 'unlike' : 'like';

		// If there's an error, log this down here.
		if (!$state) {
			$this->view->setMessage($model->getError(), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__, $verb, $id, $type, $group, $itemVerb);
		}

		return $this->view->call(__FUNCTION__, $verb, $id, $type, $group, $itemVerb);
	}
}
