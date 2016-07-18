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

class EasySocialViewComments extends EasySocialSiteView
{
	/**
	 * Post process after comment is saved.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableComments	The comment table object.
	 */
	public function save($comment = null)
	{
		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}

		$output = $comment->renderHTML();
		
		return $this->ajax->resolve($output);
	}

	public function update( $comment = null )
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve( $comment->getComment() );
	}

	public function load( $comments = null )
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$htmls = array();

		foreach( $comments as $comment )
		{
			if( !$comment instanceof SocialTableComments )
			{
				continue;
			}

			$htmls[] = $comment->renderHTML();
		}

		return $ajax->resolve( $htmls );
	}

	public function like( $likes = null )
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$hasLiked = $likes->hasLiked();
		$likeCount = $likes->getCount();
		$likesText = $likes->toString( null, true );

		return $ajax->resolve( $hasLiked, $likeCount, $likesText );
	}

	public function likedUsers( $html = null )
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve( $html );
	}

	public function likesText( $string = null )
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve( $string );
	}

	/**
	 * Confirmation to delete a comment attachment item
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function confirmDeleteCommentAttachment()
	{
		$theme = ES::themes();
		$contents = $theme->output('site/comments/dialog.delete.attachment');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Post processing after a comment attachment is deleted
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function deleteAttachment()
	{
		return $this->ajax->resolve();
	}

	public function delete()
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve();
	}

	public function getRawComment( $comment = null )
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve( $comment );
	}

	public function getUpdates( $data = null )
	{
		FD::ajax()->resolve( $data );
	}

	public function confirmDelete()
	{
		$theme = FD::themes();

		$dialog = $theme->output( 'site/comments/dialog.delete' );

		FD::ajax()->resolve( $dialog );
	}

	public function getReplies( $replies = array() )
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$htmls = array();

		foreach( $replies as $reply )
		{
			if( !$reply instanceof SocialTableComments )
			{
				continue;
			}

			$htmls[] = $reply->renderHTML();
		}

		return $ajax->resolve( $htmls );
	}

	public function getEditComment($contents)
	{
		return FD::ajax()->resolve($contents);
	}
}
