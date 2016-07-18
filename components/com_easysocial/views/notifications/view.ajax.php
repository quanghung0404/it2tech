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

class EasySocialViewNotifications extends EasySocialSiteView
{
	/**
	 * Counter checks for new friend notifications
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		Total number of new friend requests.
	 */
	public function friendsCounter( $total = 0 )
	{
		$ajax 	= FD::ajax();

		return $ajax->resolve( $total );
	}

	/**
	 * Counter checks for new system notifications
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		Total number of new friend requests.
	 */
	public function getSystemCounter( $total = 0 )
	{
		$ajax 	= FD::ajax();

		return $ajax->resolve( $total );
	}

	/**
	 * Counter checks for new system notifications
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		Total number of new friend requests.
	 */
	public function getConversationCounter( $total = 0 )
	{
		$ajax 	= FD::ajax();

		return $ajax->resolve( $total );
	}

	/**
	 * Returns a list of new notifications
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The list of conversation items
	 */
	public function getConversationItems( $conversations )
	{
		$ajax 	= FD::ajax();

		$theme 	= FD::themes();

		$theme->set( 'conversations' , $conversations );

		$layout = JRequest::getWord( "layout" , "toolbar" );

		if( $layout == 'toolbar' )
		{
			$output = $theme->output( 'site/toolbar/default.conversations.item' );
		}
		else
		{
			$output = $theme->output( 'site/notifications/popbox.conversations' );
		}

		return $ajax->resolve( $output );
	}

	/**
	 * Returns a list of new notifications
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		Total number of new friend requests.
	 */
	public function getSystemItems( $items )
	{
		$ajax 	= FD::ajax();

		$result	= array();

		$theme	= FD::themes();

		$theme->set( 'notifications' , $items );

		$layout = JRequest::getWord( "layout" , "toolbar" );

		if( $layout == 'toolbar' )
		{
			$content = $theme->output( 'site/toolbar/default.notifications.item' );
		}
		else
		{
			$content = $theme->output( 'site/notifications/popbox.notifications' );
		}

		return $ajax->resolve( $content );
	}

	/**
	 * Checks for new broadcasts
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBroadcasts($broadcasts)
	{
		$ajax = FD::ajax();

		if (!$broadcasts) {
			return $ajax->resolve($broadcasts);
		}

		foreach ($broadcasts as &$broadcast) {

			// Get the author object
			$author = FD::user($broadcast->created_by);

			// Retrieve the author's avatar
			$broadcast->authorAvatar = $author->getAvatar();

			$broadcast->title = $broadcast->getTitle();
		}

		return $ajax->resolve($broadcasts);
	}

	/**
	 * Counter checks for new friend notifications
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		Total number of new friend requests.
	 */
	public function friendsRequests( $items )
	{
		$ajax 	= FD::ajax();

		$result	= array();

		if( $items )
		{
			// Format return result.
			foreach( $items as &$item )
			{
				// Get the actor that added the current user.
				$item->user 	= FD::user( $item->actor_id );
			}
		}

		$theme 	= FD::themes();
		$theme->set( 'connections' , $items );

		$layout = JRequest::getWord( "layout" , "toolbar" );

		if( $layout == 'toolbar' )
		{
			$content = $theme->output( 'site/toolbar/default.friends.item' );
		}
		else
		{
			$content = $theme->output( 'site/notifications/popbox.friends' );
		}


		return $ajax->resolve( $content );
	}


	/**
	 * Post processing after a state has been set
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function setState()
	{
		FD::requireLogin();

		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve();
	}

	/**
	 * Post processing after a state has been set
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function setAllState()
	{
		FD::requireLogin();

		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve();
	}



	/**
	 * Counter checks for new friend notifications
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		Total number of new friend requests.
	 */
	public function clearAllConfirm()
	{
		FD::requireLogin();

		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$theme = FD::themes();
		$content 	= $theme->output( 'site/notifications/dialog.clearall' );

		return $ajax->resolve( $content );
	}

	/**
	 * Counter checks for new friend notifications
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		Total number of new friend requests.
	 */
	public function clearConfirm()
	{
		FD::requireLogin();

		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$theme = FD::themes();
		$content 	= $theme->output( 'site/notifications/dialog.clear' );

		return $ajax->resolve( $content );
	}


	public function loadmore( $items, $nextlimit )
	{
		FD::requireLogin();

		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$content = '';
		if( count( $items ) > 0 )
		{
			$theme = FD::themes();

			$theme->set( 'items', $items );
			$content 	= $theme->output( 'site/notifications/default.item' );
		}

		return $ajax->resolve( $content, $nextlimit );

	}

}
