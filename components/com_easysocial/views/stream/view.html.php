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

// Necessary to import the custom view.
FD::import( 'site:/views/views' );

class EasySocialViewStream extends EasySocialSiteView
{
	/**
	 * Responsible to output a single stream item.
	 *
	 * @access	public
	 * @return	null
	 *
	 */
	public function item()
	{
		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Get the stream id from the request
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_STREAM_INVALID_STREAM_ID'));
		}

		// Load the stream table data first
		$streamTable = FD::table('Stream');
		$loadState = $streamTable->load($id);

		// If we are unable to find the record, just throw an error
		if (!$loadState) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_STREAM_INVALID_STREAM_ID'));
		}

		// Retrieve stream
		$streamLib = FD::stream();
		$stream = $streamLib->getItem($id, $streamTable->cluster_id, $streamTable->cluster_type);

		if ($stream === false) {
			// this could be due to permission issue if the stream belong to group / event
			if ($streamTable->cluster_id && $streamTable->cluster_type) {
				$template 	= 'site/stream/restricted.' . $streamTable->cluster_type;
				$this->set('streamTable' , $streamTable );
				parent::display($template);
				return;
			} else {
				// stream from user group.
				return JError::raiseError(404, JText::_('COM_EASYSOCIAL_STREAM_CONTENT_NOT_AVAILABLE'));
			}
		}

		// If the user is not allowed to view this stream, display the appropriate message
		if ($stream === true || count($stream) <= 0) {
			$type 		= $streamTable->cluster_type ? $streamTable->cluster_type : SOCIAL_TYPE_USER;
			$template 	= 'site/stream/restricted.' . $type;

			$this->set('streamTable' , $streamTable );

			parent::display($template);

			return;
		}

		// Get the first stream item
		$stream = $stream[0];

		// Strip off any html tags from the title
		$title = strip_tags($stream->title);
		$title = trim($title);

		// Set the page title
		FD::page()->title($title);

		// Append opengraph tags
		$image = $streamLib->getContentImage($stream);

		if (!$image) {
			// Try to get user avatar image as an alternative.
			$image = FD::user($stream->actor->id)->getAvatar();
		}

		if ($image) {
			$stream->opengraph->addImage($image);
		}



		// Get the permalink of this stream
		$permalink 	= FRoute::stream(array('id' => $stream->uid, 'layout' => 'item', 'external' => 1));

		// Append additional opengraph details
		$stream->opengraph->addUrl($permalink);
		$stream->opengraph->addType( 'article' );
		$stream->opengraph->addTitle($title);

		// render the meta tags here.
		$stream->opengraph->render();

		// Get stream actions
		$actions = '';

		// Determines if we should display actions
		if ($stream->display == SOCIAL_STREAM_DISPLAY_FULL) {
			$actions = $streamLib->getActions($stream);
		}

		// Determines if we should display the translations.
		$language = $this->my->getLanguage();
		$siteLanguage = JFactory::getLanguage();
		$showTranslations = false;

		if (($language != $siteLanguage->getTag()) || $this->config->get('stream.translations.explicit')) {
			$showTranslations = true;
		}

		$this->set('showTranslations', $showTranslations);
		$this->set('actions', $actions);
		$this->set('stream', $stream);

		parent::display('site/stream/item');

		return;
	}

	public function saveFilter( $filter )
	{
		// Unauthorized users should not be allowed to access this page.
		FD::requireLogin();

		FD::info()->set( $this->getMessage() );

		if( $filter->id )
		{
			//$this->redirect( FRoute::stream( array( 'layout' => 'form', 'id' => $filter->id ) , false ) );
			$this->redirect( FRoute::dashboard( array(), false ) );
		}
		else
		{
			$model = FD::model( 'Stream' );
			$items = $model->getFilters( FD::user()->id );

			$this->set( 'items', $items );

			$this->set( 'filter', $filter );
			echo parent::display( 'site/stream/filter.form' );
		}
	}


	public function form()
	{
		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Unauthorized users should not be allowed to access this page.
		FD::requireLogin();

		$my 	= FD::user();
		$id 	= JRequest::getInt( 'id', 0 );

		$filter = FD::table( 'StreamFilter' );
		$filter->load( $id );

		$model = FD::model( 'Stream' );
		$items = $model->getFilters( $my->id );

		$this->set( 'filter', $filter );
		$this->set( 'items', $items );


		// Set page title
		if( $filter->id )
		{
			FD::page()->title( JText::sprintf( 'COM_EASYSOCIAL_STREAM_FILTER_EDIT_FILTER', $filter->title ) );
		}
		else
		{
			FD::page()->title( JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_CREATE_NEW_FILTER' ) );
		}

		// Set the page breadcrumb
		FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_DASHBOARD' ) , FRoute::dashboard() );
		FD::page()->breadcrumb( JText::_( 'Filter' ) );


		echo parent::display( 'site/stream/filter.form' );
	}

}
