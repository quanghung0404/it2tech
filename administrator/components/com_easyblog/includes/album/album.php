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

class EasyBlogAlbum extends EasyBlog
{
	/**
	 * Search and removes the gallery tag from the content.
	 *
	 * @access	public
	 * @param	string	$content	The content to search on.
	 *
	 */
	public function strip( $content )
	{
		$pattern	= '/\[embed=album\].*?\[\/embed\]/';

		return preg_replace( $pattern , '' , $content );
	}

	/**
	 * Used in conjunction with EB::formatter()
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function stripCodes(EasyBlogPost &$post)
	{
		$post->text = $this->strip($post->text);
	}

	/**
	 * Used in conjunction with EB::formatter()
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function format(EasyBlogPost &$blog)
	{
		$blog->intro = $this->process($blog->intro);
		$blog->content = $this->process($blog->content);
	}

	/**
	 * Retrieves a list of albums from the content
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getItems($content, $userId = '')
	{
		$file = JPATH_ROOT . '/components/com_community//libraries/core.php';
		$cfg = EB::config();
		$result	= array();

		jimport( 'joomla.filesystem.file' );

		// If jomsocial doesn't even exist, just skip this.
		if (!JFile::exists($file) || !$cfg->get('integrations_jomsocial_album')) {
			return $result;
		}

		require_once( $file );

		// @task: Process new gallery tags. These tags are only used in 3.5 onwards.
		$pattern	= '/\[embed=album\](.*)\[\/embed\]/i';

		preg_match_all( $pattern , $content , $matches , PREG_SET_ORDER );

		if (!empty($matches)) {

			foreach ($matches as $match) {
				// The full text of the matched content.
				$text		= $match[ 0 ];

				// The json string
				$jsonString	= $match[ 1 ];

				// Let's parse the JSON string and get the result.
				$obj = json_decode( $jsonString );

				// @task: When there's nothing there, we just return the original content.
				if ($obj === false) {
					// @TODO: Remove the gallery tag.

					// @task: Skipe processing for this match.
					continue;
				}


				$albumId = $obj->file;

				// Let's get a list of photos from this particular album.
				$model = CFactory::getModel( 'photos' );

				// Always retrieve the list of albums first
				$photos = $model->getAllPhotos( $albumId );

				if (!$photos) {
					continue;
				}

				$images = array();

				foreach ($photos as $photo) {
					$image 	= new stdClass();

					$image->title = $photo->caption;
					$image->original = rtrim( JURI::root() , '/' ) . '/' . str_ireplace( '\\' , '/' , $photo->image );
					$image->thumbnail = rtrim( JURI::root() , '/' ) . '/' . str_ireplace( '\\' , '/' , $photo->thumbnail );

					$images[]	= $image;
				}

				$theme	= EB::template();
				$theme->set( 'uid'		, uniqid() );
				$theme->set( 'images' 	, $images );

				$output = $theme->output('site/blogs/latest/blog.album');
				$result[] = $output;
			}
		}

		return $result;
	}

	/**
	 * Processes a legacy album tag into it's html counterpart
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function process($content, $userId = '')
	{
		// Match the following tags
		$pattern = '/\[embed=album\](.*)\[\/embed\]/i';

		preg_match_all( $pattern , $content , $matches , PREG_SET_ORDER );

		if (!empty($matches)) {
			$cfg		= EasyBlogHelper::getConfig();

			foreach ($matches as $match) {
				// The full text of the matched content.
				$text		= $match[ 0 ];

				// The json string
				$jsonString	= $match[ 1 ];

				// Let's parse the JSON string and get the result.
				$obj		= json_decode($jsonString);

				// @task: When there's nothing there, we just return the original content.
				if ($obj === false) {
					// @TODO: Remove the gallery tag.
					return $content;
				}

				$albumId	= $obj->file;

				// @task: Ensure that the id is properly sanitized.
				$albumId	= str_ireplace( array( '/' , '\\' ) , '' , $albumId );
				$albumId	= (int) $albumId;


				if ($obj->place == 'easysocial') {
					if (!$this->includeEasySocial()) {
						continue;
					}

					$model 		= Foundry::model( 'Photos' );
					$photos 	= $model->getPhotos( array( 'album_id' => $albumId , 'pagination' => false ) );

					if (!$photos) {
						continue;
					}

					$images		= array();

					foreach ($photos as $photo) {
						$image 	= new stdClass();

						$image->title 		= $photo->caption;

						$image->original 	= $photo->getSource( 'original' );
						$image->thumbnail 	= $photo->getSource( 'thumbnail' );

						$images[]	= $image;
					}

				} else {
					if (!$this->includeJomSocial()) {
						continue;
					}

					// Let's get a list of photos from this particular album.
					$model		= CFactory::getModel( 'photos' );

					// Always retrieve the list of albums first
					$photos		= $model->getAllPhotos( $albumId );

					if (!$photos) {
						continue;
					}


					$images		= array();

					foreach ($photos as $photo) {
						$image 	= new stdClass();

						$image->title 		= $photo->caption;
						$image->original	= rtrim( JURI::root() , '/' ) . '/' . str_ireplace( '\\' , '/' , $photo->image );
						$image->thumbnail 	= rtrim( JURI::root() , '/' ) . '/' . str_ireplace( '\\' , '/' , $photo->thumbnail );

						$images[]	= $image;
					}

				}

				$theme	= EB::template();
				$theme->set( 'uid'		, uniqid() );
				$theme->set( 'images' 	, $images );

				$output 	= $theme->output('site/blogs/latest/blog.album');

				// Now, we'll need to alter the original contents.
				$content	= str_ireplace( $text , $output , $content );
			}
		}

		return $content;
	}

	public function includeEasySocial()
	{
		static $exists = null;

		if( is_null( $exists ) )
		{
			$file 	= JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';
			$cfg 	= EasyBlogHelper::getConfig();

			jimport( 'joomla.filesystem.file' );

			// If jomsocial doesn't even exist, just skip this.
			if( !JFile::exists( $file ) || !$cfg->get( 'integrations_easysocial_album' ) )
			{
				$exists	= false;
			}
			else
			{
				$exists = true;
				require_once( $file );
			}
		}

		return $exists;
	}

	public function includeJomSocial()
	{
		static $exists = null;

		if( is_null( $exists ) )
		{
			$file 	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
			$cfg 	= EasyBlogHelper::getConfig();

			jimport( 'joomla.filesystem.file' );

			// If jomsocial doesn't even exist, just skip this.
			if( !JFile::exists( $file ) || !$cfg->get( 'integrations_jomsocial_album' ) )
			{
				$exists 	= false;
			}
			else
			{
				require_once( $file );
				$exists 	= true;
			}
		}

		return $exists;
	}
}
