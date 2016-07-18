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

class EasySocialControllerStream extends EasySocialController
{
	/**
	 * Deletes a stream filter
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteFilter()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		$view 	= FD::view( 'Stream' , false );
		$my 	= FD::user();

		$id 	= JRequest::getInt( 'id', 0 );

		if(! $id )
		{
			FD::getInstance( 'Info' )->set( JText::_( 'Invalid filter id - ' . $id ) , 'error' );
			$view->setError( JText::_( 'Invalid filter id.' ) );
			return $view->call( __FUNCTION__ );
		}


		$filter = FD::table( 'StreamFilter' );

		// make sure the user is the filter owner before we delete.
		$filter->load( array( 'id' => $id, 'uid' => $my->id, 'utype' => 'user') );

		if(! $filter->id )
		{
			FD::getInstance( 'Info' )->set( JText::_( 'Filter not found - ' . $id ) , 'error' );
			$view->setError( JText::_( 'Filter not found. Action aborted.' ) );
			return $view->call( __FUNCTION__ );
		}

		$filter->deleteItem();
		$filter->delete();

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_DELETED' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Allows caller to publish a stream item on the site
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function publish()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the stream id
		$id = $this->input->get('id', 0, 'int');

		// Load up the stream
		$stream = FD::table('Stream');
		$stream->load($id);

		// Ensure that the user is allowed
		if ($stream->cluster_type && $stream->cluster_id) {

			// @TODO: Check for access
			$stream->publish();
		}

		return $this->view->call(__FUNCTION__, $stream);
	}

	// this method is called from the dialog to quickly add new filter based on the viewing hashtag.
	public function addFilter()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		$my 	= FD::user();

		$view 	= FD::view( 'Stream' , false );

		$title   	= JRequest::getVar( 'title' );
		$tag   		= JRequest::getVar( 'tag' );

		$filter = FD::table( 'StreamFilter' );

		$filter->title = $title;
		$filter->uid   = $my->id;
		$filter->utype = 'user';

		$filter->store();

		// add hashtag into filter
		$filterItem = FD::table( 'StreamFilterItem' );

		$filterItem->filter_id 	= $filter->id;
		$filterItem->type 		= 'hashtag';
		$filterItem->content 	= $tag;

		$filterItem->store();

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_SAVED' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__, $filter );
	}

	/**
	 * Allows caller to bookmark a stream item
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addSticky()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only allowed login users to bookmark items
		FD::requireLogin();

		// Get the stream object
		$id     = $this->input->get('id', 0, 'int');
		$stream = FD::table('Stream');
		$stream->load($id);

		//TODO: Validation on the item so that prevent unauthorized attempts.



		// Get the current view
		$view = $this->getCurrentView();

		$sticky = FD::table('StreamSticky');

		// Check if this item has already been bookmarked
		$state = $sticky->load(array('stream_id' => $stream->id));

		// Stream item has already been bookmarked before
		if ($state) {

			$view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_ITEM_PINNED_BEFORE'), SOCIAL_MSG_ERROR);
			return $view->call(__FUNCTION__, false);
		}

		$sticky->stream_id = $stream->id;

		// Try to save the sticky
		$sticky->store();

		return $view->call(__FUNCTION__, $sticky);
	}

	/**
	 * Allows caller to remove a bookmark for the stream
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeSticky()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only allowed login users to bookmark items
		FD::requireLogin();

		// Get the stream object
		$id = $this->input->get('id', 0, 'int');
		$stream = FD::table('Stream');
		$stream->load($id);

		//TODO: Validation on the item so that prevent unauthorized attempts.

		// Get the current view
		$view = $this->getCurrentView();

		$sticky = FD::table('StreamSticky');

		// Check if this item has already been bookmarked
		$state = $sticky->load(array('stream_id' => $stream->id));

		// Stream item has already been bookmarked before
		if (!$state) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_STICKY_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);
			return $view->call(__FUNCTION__, $sticky);
		}

		// Delete the sticky
		$sticky->delete();

		return $view->call(__FUNCTION__, $sticky);
	}

	/**
	 * Allows caller to bookmark a stream item
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bookmark()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only allowed login users to bookmark items
		FD::requireLogin();

		// Get the stream object
		$id     = $this->input->get('id', 0, 'int');
		$stream = FD::table('Stream');
		$stream->load($id);

		// Get the current view
		$view = $this->getCurrentView();

		$bookmark = FD::table('Bookmark');

		// Check if this item has already been bookmarked
		$state = $bookmark->load(array('user_id' => $this->my->id, 'uid' => $stream->id, 'type' => SOCIAL_TYPE_STREAM));

		// Stream item has already been bookmarked before
		if ($state) {

			$view->setMessage(JText::_('COM_EASYSOCIAL_BOOKMARKS_STREAM_ITEM_BOOKMARKED_BEFORE'), SOCIAL_MSG_ERROR);
			return $view->call(__FUNCTION__);
		}

		$bookmark->uid = $stream->id;
		$bookmark->type = SOCIAL_TYPE_STREAM;
		$bookmark->user_id = $this->my->id;

		// Try to save the bookmark
		$bookmark->store();

		return $view->call(__FUNCTION__, $bookmark);
	}

	/**
	 * Allows caller to remove a bookmark for the stream
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeBookmark()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only allowed login users to bookmark items
		FD::requireLogin();

		// Get the stream object
		$id = $this->input->get('id', 0, 'int');
		$stream = FD::table('Stream');
		$stream->load($id);

		// Get the current view
		$view = $this->getCurrentView();

		$bookmark = FD::table('Bookmark');

		// Check if this item has already been bookmarked
		$state = $bookmark->load(array('user_id' => $this->my->id, 'uid' => $stream->id, 'type' => SOCIAL_TYPE_STREAM));

		// Stream item has already been bookmarked before
		if (!$state) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_BOOKMARKS_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);
			return $view->call(__FUNCTION__);
		}

		// Delete the bookmark
		$bookmark->delete();

		return $view->call(__FUNCTION__, $bookmark);
	}
	/**
	 * Stores the filter.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveFilter()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();


		$my 	= FD::user();

		$id 	= JRequest::getInt( 'id' , 0 );

		$post   = JRequest::get( 'POST' );


		// Get the current view.
		$view 	= $this->getCurrentView();

		// Load the filter table
		$filter = FD::table( 'StreamFilter' );

		if(! trim( $post['title'] ) )
		{
			$view->setError( JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_WARNING_TITLE_EMPTY' ) );
			return $view->call( __FUNCTION__, $filter );
		}

		if(!trim( $post['hashtag'] ) )
		{
			$view->setError( JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_WARNING_HASHTAG_EMPTY' ) );
			return $view->call( __FUNCTION__, $filter );
		}

		if( $id )
		{
			$filter->load( $id );
		}

		$filter->title = $post[ 'title' ];
		$filter->uid   = $my->id;
		$filter->utype = 'user';
		$filter->user_id = $my->id;
		$filter->store();

		// now we save the filter type and content.
		if ($post['hashtag']) {
			$hashtag = trim( $post[ 'hashtag' ] );
			$hashtag = str_replace( '#', '', $hashtag);
			$hashtag = str_replace( ' ', '', $hashtag);


			$filterItem = FD::table( 'StreamFilterItem' );
			$filterItem->load( array( 'filter_id' => $filter->id, 'type' => 'hashtag') );

			$filterItem->filter_id 	= $filter->id;
			$filterItem->type 		= 'hashtag';
			$filterItem->content 	= $hashtag;

			$filterItem->store();
		} else {
			$filter->deleteItem('hashtag');
		}

		$view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_FILTER_SAVED'), SOCIAL_MSG_SUCCESS);

		return $view->call(__FUNCTION__, $filter);
	}

	public function getFilter()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		$my 	= FD::user();
		$view 	= FD::view( 'Stream' , false );

		$id 	= JRequest::getInt( 'id', 0 );

		$filter = FD::table( 'StreamFilter' );
		$filter->load( $id );

		return $view->call( __FUNCTION__, $filter );
	}


	public function getCurrentDate()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		// Get the current view.
		$view 	= FD::view( 'Stream' , false );

		$date = FD::date()->toMySQL();

		return $view->call( __FUNCTION__, $date );
	}

	public function getUpdates()
	{

		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		// Get the current view.
		$view 	= FD::view( 'Stream' , false );

		// Get the type of the stream to load.
		$type 		= JRequest::getWord( 'type' , 'me' );
		$uid 		= JRequest::getVar( 'id', '');
		$source 	= JRequest::getWord( 'source' , '' );

		// next start date
		$currentdate 	= JRequest::getVar( 'currentdate' , '' );

		$streamType = ( $type == 'following' ) ? 'follow' : SOCIAL_TYPE_USER;

		$userId = '';
		$listId = '';

		if( $source == 'dashboard' )
		{
			if( $type == 'me' && !empty( $uid ) )
			{
				$listId = $uid;
			}
		}
		else if( $source == 'profile' )
		{
			$userId = $uid;
		}

		// // Get the stream
		$stream		= FD::stream();

		//cluster types
		$clusters = array( SOCIAL_TYPE_GROUP );

		if( in_array( $type , $clusters ) )
		{
			$clusterId 	= JRequest::getVar( 'id', '' );

			// this is a cluster type loadmore
			$options = array(
							'clusterId' 	=> $clusterId,
							'clusterType' 	=> $type,
							'limitStart' => $currentdate,
							'direction' => 'later'
							 );
			$stream->get( $options );
		}
		else
		{
			$options 	= array(
									'userId' 	=> $userId,
									'listId' 	=> $listId,
									'context' 	=> SOCIAL_STREAM_CONTEXT_TYPE_ALL,
									'type' 		=> $streamType,
									'limitStart' => $currentdate,
									'direction' => 'later',
									'limit' => 0
								);

			if ($type == 'everyone') {
				$options['guest'] = true;
			}

			$stream->get( $options );
		}

		return $view->call( __FUNCTION__, $stream);
	}

	public function checkUpdates()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Get the current view.
		$view 	= FD::view( 'Stream' , false );

		// Get the type of the stream to load.
		$type 		= JRequest::getVar( 'type', 'me' );
		$source 	= JRequest::getVar( 'source' );
		$uid 		= JRequest::getVar( 'id', '');
		$exclude 	= JRequest::getVar( 'exclude', '' );

		if ($type == 'module') {
			// lets overwrite the data so that we can get the updates.
			$type = 'everyone';
			$source = 'dashboard';
		}

		// next start date
		$currentdate 	= JRequest::getVar( 'currentdate' , '' );

		$data  = null;
		$model = FD::model( 'Stream' );

		//cluster types
		$clusters = array( SOCIAL_TYPE_GROUP );
		if( in_array( $type , $clusters ) )
		{
			$data  = $model->getClusterUpdateCount( $source, $currentdate, $type, $uid, $exclude );
		}
		else
		{
			$data  = $model->getUpdateCount( $source, $currentdate, $type, $uid, $exclude );
		}

		return $view->call( __FUNCTION__, $data, $source, $type, $uid, $currentdate );
	}


	/**
	 * retrieve more stream items. ( used in pagination )
	 *
	 * @since 	1.0
	 * @access 	public
	 * return   StreamItem object
	 *
	 */
	public function loadmoreGuest()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		// Get the current view.
		$view 	= FD::view( 'Stream' , false );


		// next start date
		$startlimit 	= JRequest::getVar( 'startlimit' , 0 );


		// Get the stream
		$stream 	= FD::stream();
		$stream->getPublicStream( SOCIAL_STREAM_GUEST_LIMIT, $startlimit );

		return $view->call( __FUNCTION__ , $stream );

	}


	/**
	 * retrieve more stream items. ( used in pagination )
	 *
	 * @since 	1.0
	 * @access 	public
	 * return   StreamItem object
	 *
	 */
	public function loadmore()
	{
		// Check for request forgeries.
		ES::checkToken();

		// Determines if this is a cluster view
		$isCluster = $this->input->get('iscluster', false, 'word');

		// Get the type of the stream to load.
		$type = $this->input->get('type', 'me', 'word');

		// In order to access the dashboard apps, user must be logged in.
		if (!$isCluster) {
			ES::requireLogin();
		}

		// Get the current view.
		$view 	= FD::view( 'Stream' , false );

		$startlimit 	= JRequest::getVar( 'startlimit' , 0 );

		// Get the context
		$context 	= JRequest::getVar('context');

		// Get the stream
		$stream	= FD::stream();

		$my = FD::user();

		if( !$type )
		{
			$view->setMessage( JText::_( 'Invalid feed type provided.' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $stream );
		}

		// Get feeds from user's friend list.
		if( $type == 'list' )
		{
			// The id of the friend list.
			$id 	= JRequest::getInt( 'id', 0 );

			// @TODO: We need to filter stream items from friends in specific friend list.
			if( !empty( $id ) )
			{
				$listsModel 	= FD::model( 'Lists' );
				$memberIds		= $listsModel->getMembers( $id, true);

				if( $memberIds )
				{
					$stream->get(
									array(
										'listId' 	=> $id,
										'context' 	=> SOCIAL_STREAM_CONTEXT_TYPE_ALL,
										'type' 		=> SOCIAL_TYPE_USER,
										'startlimit' => $startlimit
										)
								);
				}
			}
		}

		// custom filter.
		if( $type == 'custom' )
		{
			$id 	= JRequest::getInt( 'id', 0 );

			$sfilter = FD::table( 'StreamFilter' );
			$sfilter->load( $id );

			if( $sfilter->id )
			{
				$hashtags = $sfilter->getHashTag();
				$tags = explode( ',', $hashtags );

				if( $tags )
				{
					$stream->get( array(
										'context' 	=> SOCIAL_STREAM_CONTEXT_TYPE_ALL,
										'tag'	=> $tags,
										'startlimit' => $startlimit
									)
								);
				}
			}
		}

		if( $type == 'hashtag' )
		{
			// at this point, the tag passed in is the one without the id.
			$tags 	= JRequest::getVar( 'tag', '' );

			if( $tags )
			{
				$stream->get( array(
									'context' 	=> SOCIAL_STREAM_CONTEXT_TYPE_ALL,
									'tag'	=> $tags,
									'startlimit' => $startlimit
								)
							);
			}

		}

		if( $type == 'following' )
		{
			$stream->get(
							array(
								'context' 	=> SOCIAL_STREAM_CONTEXT_TYPE_ALL,
								'type' 		=> 'follow',
								'startlimit' => $startlimit
								)
						);
		}

		if( $type == 'bookmarks' )
		{
			$stream->get(
							array(
								'guest' 	=> true,
								'type' 		=> 'bookmarks',
								'startlimit' => $startlimit
								)
						);
		}

		if ($type == 'sticky') {
			$stream->get(
							array(
								'userId' 	=> $my->id,
								'type' 		=> 'sticky',
								'startlimit' => $startlimit
								)
						);
		}

		if( $type == 'appFilter' )
		{
			$stream->get(
							array(
								'context' 	=> $context,
								'startlimit' => $startlimit
								)
						);
		}

		// Get feeds from everyone
		if( $type == 'everyone' )
		{
			// $stream->getPublicStream( SOCIAL_STREAM_GUEST_LIMIT, 0 );
			$stream->get( array(
								'guest' 	=> true,
								'ignoreUser' => true,
								'startlimit' => $startlimit
							)
						);
		}

		// Get feeds from the user profile.
		if( $type == 'profile' )
		{
			$uid = JRequest::getVar( 'id', '');
			$stream->get(
							array(
								'profileId' => $uid,
								'startlimit' => $startlimit
								)
						);

		}

		// Get feeds from the current user and friends only.
		if( $type == 'me' )
		{
			$uid = JRequest::getVar( 'id', '');

			$streamOptions = array(
								'userId' 	=> $uid,
								'context' 	=> SOCIAL_STREAM_CONTEXT_TYPE_ALL,
								'type' 		=> SOCIAL_TYPE_USER,
								'startlimit' => $startlimit
								);

			$page = JRequest::getVar( 'view', '');

			if ($page == 'profile') {
				$streamOptions['nosticky'] = true;
			}

			$stream->get($streamOptions);
		}

		//event category
		if( $type == 'eventcategory' )
		{
			$uid = JRequest::getVar( 'id', '');
			$stream->get(
							array(
								'clusterCategory' 	=> $uid,
								'clusterType' => SOCIAL_TYPE_EVENT,
								'startlimit' => $startlimit
								)
						);
		}

		//event category
		if( $type == 'groupcategory' )
		{
			$uid = JRequest::getVar( 'id', '');
			$stream->get(
							array(
								'clusterCategory' 	=> $uid,
								'clusterType' => SOCIAL_TYPE_GROUP,
								'startlimit' => $startlimit
								)
						);
		}

		//cluster types
		$clusters = array( SOCIAL_TYPE_GROUP, SOCIAL_TYPE_EVENT );

		if( in_array( $type , $clusters ) )
		{
			$clusterId 	= JRequest::getVar( 'id', '' );
			$tags 		= JRequest::getVar( 'tag', '' );
			$filterId 	= JRequest::getInt( 'filterId' );


			// this is a cluster type loadmore
			$options = array(
							'clusterId' 	=> $clusterId,
							'clusterType' 	=> $type,
							'tag'			=> $tags,
							'nosticky' => true,
							'startlimit' => $startlimit
							 );

			if ($context) {
				$options[ 'context' ] = $context;
			}

			if( $filterId )
			{
				$sfilter = FD::table( 'StreamFilter' );
				$sfilter->load( $filterId );

				$hashtags 	= $sfilter->getHashTag();
				$tags 		= explode( ',', $hashtags );

				if( $tags )
				{
					$options[ 'tag' ] = $tags;
				}
			}

			$stream->get( $options );
		}

		return $view->call( __FUNCTION__ , $stream );

	}

	/**
	 * Hides a feeds from a context ( app ).
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function hideapp()
	{
		// Check for request forgeries!
		FD::checkToken();

		// Ensure that the user is logged in before allowing such actions.
		FD::requireLogin();

		// Get the stream's context.
		$context 	= JRequest::getVar( 'context' );

		// Get the view.
		$view 	= FD::view( 'Stream' , false );

		// If id is invalid, throw an error.
		if (!$context) {
			$view->setError( JText::_( 'COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_APP' ) );
			return $view->call( __FUNCTION__ );
		}

		// Get the current logged in user.
		$my 	= FD::user();

		// The user needs to be at least logged in to perform this action.
		if (!$my->id) {
			$view->setError( JText::_( 'COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_APP' ) );
			return $view->call( __FUNCTION__ );
		}

		// Get the model
		$model 	= FD::model( 'Stream' );
		$state	= $model->hideapp( $context , $my->id );

		// If there's an error, log this down.
		if (!$state) {
			$view->setError( $model->getError() );

			return $view->call( __FUNCTION__ );
		}

		return $view->call( __FUNCTION__ );
	}



	/**
	 * Hide feeds from an actor.
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function hideactor()
	{
		// Check for request forgeries!
		FD::checkToken();

		// Ensure that the user is logged in before allowing such actions.
		FD::requireLogin();

		// Get the stream's context.
		$actorId 	= JRequest::getVar( 'actor' );

		// Get the view.
		$view 	= FD::view( 'Stream' , false );

		// If id is invalid, throw an error.
		if (!$actorId) {
			$view->setError( JText::_( 'COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_ACTOR' ) );
			return $view->call( __FUNCTION__ );
		}

		// Get the current logged in user.
		$my = ES::user();

		// The user needs to be at least logged in to perform this action.
		if (!$my->id) {
			$view->setError( JText::_( 'COM_EASYSOCIAL_STREAM_HIDE_ACTOR_ERROR_USER_NOT_LOGIN' ) );
			return $view->call( __FUNCTION__ );
		}

		// Get the model
		$model 	= FD::model( 'Stream' );
		$state	= $model->hideactor( $actorId , $my->id );

		// If there's an error, log this down.
		if (!$state) {
			$view->setError( $model->getError() );

			return $view->call( __FUNCTION__ );
		}

		return $view->call( __FUNCTION__ );
	}

	/**
	 * UnHide stream items from actor.
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function unhideactor()
	{
		// Check for request forgeries!
		FD::checkToken();

		FD::requireLogin();

		$actorId		= JRequest::getVar( 'actor' );
		$my             = FD::user();

		$view 	= FD::view( 'Stream' , false );


		// Get the view.
		$view 	= FD::view( 'Stream' , false );

		if (empty($actorId)) {
			$view->setErrors( JText::_( 'COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_ACTOR' ) );

			return $view->call( __FUNCTION__ );
		}


		$model 	= FD::model( 'Stream' );
		$state 	= $model->unhideactor( $actorId, $my->id);

		if(! $state )
		{
			$view->setErrors( JText::_( 'COM_EASYSOCIAL_STREAM_FAILED_UNHIDE' ) );
			return $view->call( __FUNCTION__ );
		}

		return $view->call( __FUNCTION__ );
	}


	/**
	 * Hides a stream item.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unhideapp()
	{
		// Check for request forgeries!
		FD::checkToken();

		FD::requireLogin();

		$context		= JRequest::getVar( 'context' );
		$my             = FD::user();

		$view 	= FD::view( 'Stream' , false );


		// Get the view.
		$view 	= FD::view( 'Stream' , false );

		if (empty($context)) {
			$view->setErrors( JText::_( 'COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_APP' ) );

			return $view->call( __FUNCTION__ );
		}


		$model 	= FD::model( 'Stream' );
		$state 	= $model->unhideapp( $context, $my->id);

		if(! $state )
		{
			$view->setErrors( JText::_( 'COM_EASYSOCIAL_STREAM_FAILED_UNHIDE' ) );
			return $view->call( __FUNCTION__ );
		}

		return $view->call( __FUNCTION__ );
	}


	/**
	 * Delete a stream item.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries!
		FD::checkToken();

		// Ensure that the user is logged in before allowing such actions.
		FD::requireLogin();

		// Get the stream's uid.
		$id = $this->input->get('id', 0, 'int');

		// Get the view.
		$view = $this->getCurrentView();

		// Get logged in user
		$my = FD::user();

		$access = $my->getAccess();

		// Load the stream item.
		$item = FD::table('Stream');
		$item->load($id);

		// If id is invalid, throw an error.
		if (!$id || !$item->id) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_ID' ), SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// If the user is not a super admin, we need to check their privileges
		if (!$my->isSiteAdmin()) {

			// Check if the stream item is for groups
			if ($item->cluster_id) {

				if ($item->cluster_type == 'group') {
					$cluster = FD::group($item->cluster_id);
				}

				if ($item->cluster_type == 'event') {
					$cluster = FD::event($item->cluster_id);
				}

				if (!$cluster->isAdmin() && !$access->allowed('stream.delete', false)) {
					$view->setMessage( JText::_( 'COM_EASYSOCIAL_STREAM_NOT_ALLOWED_TO_DELETE' ), SOCIAL_MSG_ERROR );
					return $view->call( __FUNCTION__ );
				}

			} else {

				if (!$access->allowed('stream.delete', false)) {
					$view->setMessage( JText::_( 'COM_EASYSOCIAL_STREAM_NOT_ALLOWED_TO_DELETE' ), SOCIAL_MSG_ERROR );
					return $view->call( __FUNCTION__ );
				}
			}
		}

		$state = $item->delete();

		// If there's an error, log this down.
		if( !$state )
		{
			$view->setMessage( $model->getError() );

			return $view->call( __FUNCTION__ );
		}

		return $view->call( __FUNCTION__ );


	}


	/**
	 * Hides a stream item.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function hide()
	{
		// Check for request forgeries!
		FD::checkToken();

		// Ensure that the user is logged in before allowing such actions.
		FD::requireLogin();

		// Get the stream's uid.
		$id 	= JRequest::getInt( 'id' );

		// Get the view.
		$view 	= $this->getCurrentView();

		// Get logged in user
		$my 	= FD::user();

		// Load the stream item.
		$item 	= FD::table( 'Stream' );
		$item->load( $id );

		// If id is invalid, throw an error.
		if( !$id || !$item->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_ID' ) );
			return $view->call( __FUNCTION__ );
		}

		// Check if the user is allowed to hide this item
		if( !$item->hideable() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_STREAM_NOT_ALLOWED_TO_HIDE' ) );
			return $view->call( __FUNCTION__ );
		}

		// Get the model
		$model 	= FD::model( 'Stream' );
		$state	= $model->hide( $id , $my->id );

		// If there's an error, log this down.
		if( !$state )
		{
			$view->setMessage( $model->getError() );

			return $view->call( __FUNCTION__ );
		}

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Unhide a stream item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unhide()
	{
		// Check for request forgeries
		FD::checkToken();

		// User needs to be logged in
		FD::requireLogin();

		$id				= JRequest::getVar( 'id' );
		$my             = FD::user();

		// Get the view.
		$view 		= $this->getCurrentView();

		// Load the stream item.
		$item 	= FD::table( 'Stream' );
		$item->load( $id );

		// Check for valid id
		if( !$id || !$item->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_ID' ) );
			return $view->call( __FUNCTION__ );
		}

		// Check if the user is allowed to hide this item
		if( !$item->hideable() )
		{
			$view->setError( JText::_( 'COM_EASYSOCIAL_STREAM_NOT_ALLOWED_TO_HIDE' ) );
			return $view->call( __FUNCTION__ );
		}

		$model 	= FD::model( 'Stream' );
		$state 	= $model->unhide($id, $my->id);

		if(! $state )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_STREAM_FAILED_UNHIDE' ) );

			return $view->call( __FUNCTION__ );
		}

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Allows caller to translate a wall of text
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function translate()
	{
		// Check for request forgeries here.
		ES::checkToken();

		// Get the contents to translate
		$contents = $this->input->get('contents', '', 'raw');

		// The target language should always be the language the user is using
		$lang = JFactory::getLanguage();
		$defaultLanguage = $lang->getTag();
		$defaultLanguage = explode('-', $defaultLanguage);
		$defaultLanguage = $defaultLanguage[0];

		$params = new JRegistry($this->my->params);
		$targetLanguage = $params->get('language', $defaultLanguage);

		$translations = ES::translations();

		$output = $translations->translate($contents, $targetLanguage);

		return $this->view->call(__FUNCTION__, $output);
	}
}
