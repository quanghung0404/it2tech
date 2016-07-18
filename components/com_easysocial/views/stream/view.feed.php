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
	public function display()
	{
		// Get the configuration objects
		$config 	= FD::config();
		$jConfig 	= FD::config( 'joomla' );

		// Get the stream library
		$stream 	= FD::stream();
		$stream->get();

		// Get the result in an array form
		$result 		= $stream->toArray();

		// Set the document properties
		$doc 		= JFactory::getDocument();
		$doc->link	= FRoute::dashboard();

		FD::page()->title( JText::_( 'COM_EASYSOCIAL_STREAM_FEED_TITLE' ) );
		$doc->setDescription( JText::sprintf( 'COM_EASYSOCIAL_STREAM_FEED_DESC' , $jConfig->getValue( 'sitename' ) ) );

		if( $result )
		{
			$useEmail 	= $jConfig->getValue( 'feed_email' );
			foreach( $result as $row )
			{
				$item				= new JFeedItem();
				$item->title 		= $row->title;
				$item->link 		= FRoute::stream( array( 'id' => $row->uid ) );
				$item->description 	= $row->content;
				$item->date			= $row->created->toMySQL();
				$item->author 		= $row->actor->getName();

				if( $useEmail != 'none' )
				{
					$item->authorEmail 	= $jConfig->getValue( 'mailfrom' );

					if( $useEmail == 'author' )
					{
						$item->authorEmail 	= $row->actor->email;
					}
				}

				$doc->addItem( $item );
			}
		}
	}
}
