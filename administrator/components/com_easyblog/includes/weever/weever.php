<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogWeever
{
	public function getMainFeed()
	{
		$obj	= new EasyBlogWeeverMain();

		return $obj;
	}

	public function getDetailsFeed()
	{
		$obj	= new EasyBlogWeeverItemDetails();

		return $obj;
	}
}

class EasyBlogWeeverItem
{
	public $r3sVersion		= '0.8.1';
	public $tags			= array();
	public $geo				= array();
	public $url 			= null;
	public $uuid			= null;
	public $author			= null;
	public $publisher		= null;
	public $relationships	= null;
	public $name			= null;
	public $datetime		= array( 'published'	=> '' ,
									 'modified'		=> '',
									 'start'		=> '',
									 'end'			=> ''
								);
	public function toJSON( $exit = false , $callback = '' )
	{
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );
		$json	= new Services_JSON();

		$data	= !empty( $callback ) ? $callback . '(' : '';
		$data	.= $json->encode( $this );
		$data	.= !empty( $callback ) ? ')' : '';

		if( $exit )
		{
			header('Content-type: application/json');
			echo $data;
			exit;
		}

		return $data;
	}

	public function setImage( $blog )
	{
		if( $blog->getImage() )
		{
			// $this->image['mobile' ]	= $blog->getImage( 'mobile' );
			// $this->image['full']	= $blog->getImage( 'original' );

			// updated @ 17 april 2013, requested by aaron song.
			$mobile 	= $blog->getImage( 'mobile' );
			$original 	= $blog->getImage( 'original' );

			if( !empty( $mobile ) && !empty( $original ) )
			{
				$this->images[] = $mobile;
				$this->images[] = $original;
			}
			else if( !empty( $mobile ) )
			{
				$this->images[] = $mobile;
			}
			else if( !empty( $original ) )
			{
				$this->images[] = $original;
			}
			else
			{
				$this->images[] = '';
			}
		}
		else
		{
			$this->images[] = '';
		}
	}

	public function setDateTime( $obj )
	{
		if( is_object( $obj ) )
		{
			foreach( $obj as $key => $value )
			{
				if( isset( $this->datetime[ $key ] ) )
				{
					$this->datetime[ $key ]	= $value;
				}
			}
		}

		if( is_string( $obj ) )
		{
			$this->datetime[ 'published' ]	= $obj;
		}
	}

	public function setGeo( $lat , $lng )
	{
		return;
		$this->geo[ 'latitude' ]	= $lat;
		$this->geo[ 'longitude' ]	= $lng;
	}

	public function set( $key , $value = '' )
	{
		$this->$key	= $value;
	}

	public function setAuthor( $userId )
	{
		$user 	= EB::user($userId);

		$this->author	= $user->getName();
		$this->publisher	= $user->getName();
	}

	public function setTags( &$item )
	{
		if( !( $item instanceof TableBlog ) )
		{
			$blog	= EB::table('Blog');
			$blog->load( $item->id );
		}
		else
		{
			$blog 	= $item;
		}

		$tags	= $blog->getTags();

		if( $tags )
		{
			foreach( $tags as $tag )
			{
				$data			= array();
				$data[ 'name' ]	= $tag->title;
				$date[ 'link' ] = $this->getWeeverURL( 'index.php?option=com_easyblog&view=tag&layout=listings&id=' . $tag->id );

				$this->tags[]	= $data;
			}
		}
	}

	public function map( &$blog )
	{
		$ignore		= array( 'image' , 'datetime' , 'tags' , 'geo' );

		foreach( $blog as $key => $value )
		{
			if( isset( $this->$key ) && !in_array( $key , $ignore ) )
			{
				$this->$key = $value;
			}
		}

		$this->name 				= $blog->title;

		$dateTimeObject				= new stdClass();
		$dateTimeObject->published	= $blog->created;
		$dateTimeObject->modified	= $blog->created;
		$dateTimeObject->start		= '';
		$dateTimeObject->end 		= '';

		// @task: Set the author
		$this->setAuthor( $blog->created_by );

		// @task: Set the datetime of the blog post
		$this->setDateTime( $dateTimeObject );

		// @task: Set the URL
		$this->url		= $this->getWeeverURL( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id );

		// @task: Set the geolocation
		$this->setGeo( $blog->latitude , $blog->longitude );

		// @task: Set the blog image
		$this->setImage( $blog );

		// @task: Set the tags
		$this->setTags( $blog );

		return $this;
	}

	public function getWeeverURL( $url )
	{
		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );
		$sef  		= EasyBlogRouter::isSefEnabled();
		$join		= $sef ? '?' : '&';
		$url		= EasyBlogRouter::getRoutedUrl( $url, false, true ) . $join . 'format=weever';

		return $url;
	}
}

class EasyBlogWeeverMain extends EasyBlogWeeverItem
{
	public $thisPage	= null;
	public $lastPage	= null;
	public $count		= 15;
	public $type 		= 'htmlContent';
	public $sort 		= 'rdate';
	public $language	= 'en-GB';
	public $copyright	= null;
	public $license		= null;
	public $generator	= 'EasyBlog R3S Template for Weever App';
	public $images		= array(); // updated @ 17 april 2013, requested by aaron song.
	public $publisher	= null;
	public $rating		= null;
	public $url			= null;
	public $description	= null;

	public function __construct()
	{

	}

	public function addChild( &$blog )
	{
		$summary 		= new EasyBlogWeeverItemSummary();
		$this->items[]	= $summary->map( $blog );
	}
}

class EasyBlogWeeverItemDetails extends EasyBlogWeeverItem
{
	public $html 	= null;
	public $url		= null;
	public $geo 	= array();

	public function toJSON( $exit = false , $callback = '' )
	{
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );
		$json	= new Services_JSON();

		$data	= !empty( $callback ) ? $callback . '(' : '';
		$data	.= $json->encode( array( 'results' => array( $this ) ) );
		$data	.= !empty( $callback ) ? ')' : '';

		if( $exit )
		{
			header('Content-type: application/json');
			echo $data;
			exit;
		}

		return $data;
	}

	public function map( &$blog )
	{
		parent::map( $blog );

		// @task: Process blog triggers here.

		// @rule: Process videos
		$blog->intro	= EB::videos()->processVideos( $blog->intro );
		$blog->content	= EB::videos()->processVideos( $blog->content );

		$blogger 		= EB::user($blog->created_by);

		// Assign custom variables
		$blog->author	= $blogger;

		$themes			= EB::template();
		$themes->set( 'blog' 	, $blog );
		$themes->set( 'blogger'	, $blogger );

		$content		= $themes->output('site/blogs/entry/weever');

		$this->set( 'html' , $content );

		return $this;
	}
}

class EasyBlogWeeverItemSummary extends EasyBlogWeeverItem
{
	public $type			= 'htmlContent';
	public $description		= '';

	// updated @ 17 april 2013, requested by aaron song.
	public $images			= array();

}
