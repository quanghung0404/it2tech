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

FD::import( 'site:/views/views' );

class EasySocialViewRepost extends EasySocialSiteView
{
	/**
	 * Returns an ajax chain.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The verb that we have performed.
	 */
	public function share( $uid = null , $element = null, $group = SOCIAL_APPS_GROUP_USER, $streamId = 0 )
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		// Set the message
		$share 	= FD::get( 'Repost', $uid, $element, $group );
		$cnt 	= $share->getCount();

		$cntPluralize 	= FD::language()->pluralize( $cnt, true)->getString();
		$text 			= JText::sprintf( 'COM_EASYSOCIAL_REPOST' . $cntPluralize, $cnt );

		//$text = $share->getHTML();

		$isHidden	= ( $cnt > 0 ) ? false : true;

		$streamHTML = '';

		if( $streamId )
		{
			$stream 	= FD::stream();
			$stream->getItem( $streamId );

			$streamHTML = $stream->html();
		}

		return $ajax->resolve( $text, $isHidden, $cnt, $streamHTML );
	}

	/**
	 * Display a list of sharers
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSharers( $sharers = array() )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$theme 	= FD::themes();

		$theme->set( 'users' , $sharers );
		$contents 	= $theme->output( 'site/repost/sharer.item' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the reposting form.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function form()
	{
		FD::requireLogin();

		$ajax = FD::ajax();

		$element = JRequest::getVar('element','');
		$group = JRequest::getVar('group','');
		$uid = JRequest::getInt('id');

		$clusterId = JRequest::getInt('clusterId', 0);
		$clusterType = JRequest::getVar('clusterType', '');

		// $preview = 'halo this is a preview.';
		$share = FD::get('Repost', $uid, $element, $group);

		if ($clusterId && $clusterType) {
			$share->setCluster($clusterId, $clusterType);
		}

		$theme = FD::themes();

		//check if the current user already shared this item or not. if yes, display a message and abort the sharing process.
		if( $share->isShared( FD::user()->id ) )
		{
			$message = JText::_( 'COM_EASYSOCIAL_REPOST_MESSAGE_ALREADY_REPOST' );
			$theme->set( 'message', $message );
			$html = $theme->output( 'site/repost/dialog.message' );
			return $ajax->resolve( $html );
		}

		// Get dialog
		$preview 	= $share->preview();
		$theme->set( 'preview', $preview );
		$html = $theme->output( 'site/repost/dialog.form' );

		return $ajax->resolve( $html );
	}

}
