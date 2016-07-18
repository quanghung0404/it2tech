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

FD::import( 'site:/views/views' );

class EasySocialViewStream extends EasySocialSiteView
{

	/**
	 * Confirmation for deleting stream item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		$ajax 	= FD::ajax();

		$theme 		= FD::themes();
		$contents	= $theme->output( 'site/stream/dialog.delete' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the edit stream form
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function edit()
	{
		$ajax 	= FD::ajax();

		$id 	= JRequest::getInt('id');

		$stream 	= FD::table('Stream');
		$stream->load($id);

		$mentions 	= $stream->getTags(array('user', 'hashtag'));

		$story 		= FD::story();
		$story->setContent($stream->content);
		$story->setMentions($mentions);

		$contents	= $story->getMentionsForm();

		return $ajax->resolve($contents);
	}

	/**
	 * Displays the save filter form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmSaveFilter()
	{
		// Require user to be logged in
		FD::requireLogin();

		$ajax	= FD::ajax();

		$tag	= JRequest::getVar( 'tag' );

		$theme 		= FD::themes();
		$theme->set( 'tag', $tag );

		$contents	= $theme->output( 'site/stream/dialog.filter.add' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Post process after a stream item is published on the site
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function publish($stream)
	{
		return $this->ajax->resolve();
	}

	public function confirmFilterDelete()
	{
		$theme = FD::themes();
		$contents = $theme->output('site/stream/dialog.filter.delete');

		return $this->ajax->resolve($contents);
	}

	public function addFilter( $filter )
	{
		$ajax 	= FD::ajax();

		FD::requireLogin();

		$theme 		= FD::themes();

		$theme->set( 'filter'	, $filter );
		$theme->set( 'fid'	, '' );

		$content	= $theme->output( 'site/dashboard/sidebar.feeds.filter.item' );

		return $ajax->resolve( $content, JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_SAVED' ) );
	}



	public function deleteFilter()
	{
		$ajax 	= FD::ajax();

		FD::requireLogin();
		FD::info()->set( $this->getMessage() );

		$url = FRoute::dashboard( array(), false );

		return $ajax->redirect( $url );
	}


	/**
	 * Post processing after an item is already pinned
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addSticky($sticky)
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve($sticky);
	}

	/**
	 * Post processing after an item is already unpinned
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeSticky($sticky)
	{
		$ajax = FD::ajax();

		// We should display a nicer message
		$theme = FD::themes();
		$contents = $theme->output('site/stream/sticky.removed');

		return $ajax->resolve($contents);
	}

	/**
	 * Post processing after an item is already bookmarked
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bookmark($bookmark)
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve($bookmark);
	}

	/**
	 * Post processing after an item is already bookmarked
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeBookmark($bookmark)
	{
		$ajax = FD::ajax();

		// We should display a nicer message
		$theme = FD::themes();
		$contents = $theme->output('site/stream/bookmark.removed');

		return $ajax->resolve($contents);
	}

	public function delete()
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$contents = JText::_( 'COM_EASYSOCIAL_STREAM_FEED_DELETED_SUCCESSFULLY' );
		return $ajax->resolve( $contents );
	}


	public function getCurrentDate( $currentDate )
	{
		// Load ajax library.
		$ajax 	= FD::ajax();
		return $ajax->resolve( $currentDate );
	}

	public function getUpdates($stream)
	{

		// Load ajax library.
		$ajax 	= FD::ajax();

		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		$content 	= $stream->html( true );
		$nextdate 	= FD::date()->toMySQL();

		$streamIds = array();
		$ids = $stream->getUids();

		if (!empty($ids)) {
			$streamIds = $ids;
		}

		return $ajax->resolve( $content, $nextdate, $streamIds );
	}


	/**
	 * Displays the stream filter form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFilter( $filter )
	{
		$ajax 	= FD::ajax();

		$theme 		= FD::themes();
		$theme->set( 'filter', $filter );

		$contents	= $theme->output( 'site/stream/form.edit' );

		return $ajax->resolve( $contents );
	}


	public function checkUpdates( $data, $source, $type, $uid, $currentdate )
	{
		// Load ajax library.
		$ajax 	= FD::ajax();

		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		$content 	= '';
		if( count( $data ) > 0 )
		{
			//foreach( $data as $item )

			if( $type == 'list' )
			{
				$type = $type . '-' . $uid;
			}

			for( $i = 0; $i < count( $data ); $i++ )
			{
				$item =& $data[ $i ];

				if( $item['type'] == $type )
				{
					//debug
					//$item['cnt'] = 5;
					if( $item['cnt'] && $item['cnt'] > 0 )
					{
						$theme = FD::themes();

						$theme->set( 'count'  , $item['cnt'] );
						$theme->set( 'currentdate', $currentdate );
						$theme->set( 'type'	, $type );
						$theme->set( 'uid'	, $uid );

						$content = $theme->output( 'site/stream/update.notification' );
					}
				}
			}
		}

		// $content 	= $stream->html( true );
		// $startdate 	= FD::date()->toMySQL(); // always use the current date.
		// $total   	= $stream->getCount();

		// $content 	= '';
		// $total   	= 0;


		$startdate 	= FD::date()->toMySQL();


		// return $ajax->resolve( $content, $startdate );
		return $ajax->resolve( $data, $content, $startdate);

	}


	/**
	 * Responsible to return the ajax chains
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialAjax
	 */
	public function loadmoreGuest( $stream )
	{
		// Load ajax library.
		$ajax 	= FD::ajax();

		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		$content 	= $stream->html( true );
		$startlimit = $stream->getNextStartLimit();


		if( empty( $startlimit ) )
		{
			$startlimit = '';
		}

		return $ajax->resolve( $content, $startlimit );
	}


	/**
	 * Responsible to return the ajax chains
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialAjax
	 */
	public function loadmore($stream)
	{
		// Get any errors from controller
		$error = $this->getError();

		if ($error) {
			return $this->ajax->reject($error);
		}

		// Get the content from the stream
		$content = $stream->html(true);

		$startlimit = $stream->getNextStartLimit();

		if (empty($startlimit)) {
			$startlimit = '';
		}

		return $this->ajax->resolve($content, $startlimit);
	}

	/**
	 * Responsible to return the ajax chains
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialAjax
	 */
	public function hide()
	{
		// Load ajax library.
		$ajax 	= FD::ajax();

		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		$theme = FD::themes();
		$contents = $theme->output('site/stream/hidden');

		return $ajax->resolve($contents);
	}

	/**
	 * Post processing after actor stream is hidden
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialAjax
	 */
	public function hideactor()
	{
		// Load ajax library.
		$ajax 	= FD::ajax();

		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		$actorId 	= JRequest::getVar( 'actor' );

		$actor 		= FD::user( $actorId );

		$theme 		= FD::themes();
		$theme->set( 'actor' , $actor );
		$contents	= $theme->output( 'site/stream/hidden.actor' );

		return $ajax->resolve( $contents );
	}


	public function unhideactor()
	{
		// Load ajax library.
		$ajax 	= FD::ajax();

		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		return $ajax->resolve();
	}


	/**
	 * Post processing after app is hidden
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialAjax
	 */
	public function hideapp()
	{
		// Load ajax library.
		$ajax 	= FD::ajax();

		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		$context 	= JRequest::getVar( 'context' );

		$theme 		= FD::themes();
		$theme->set( 'context' , $context );
		$contents	= $theme->output( 'site/stream/hidden.app' );

		return $ajax->resolve( $contents );
	}

	public function unhide()
	{
		// Load ajax library.
		$ajax 	= FD::ajax();

		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		return $ajax->resolve();
	}

	public function unhideapp()
	{
		// Load ajax library.
		$ajax 	= FD::ajax();

		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		return $ajax->resolve();
	}

	/**
	 * Post process after translating stream contents
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function translate($output)
	{
		return $this->ajax->resolve($output);
	}
}
