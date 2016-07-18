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

class EasyBlogFormatterStandard
{
	protected $items 	= null;
	protected $cache 	= null;

	public function __construct(&$items, $cache = true)
	{
		$this->items = $items;
		$this->cache = $cache;
		$this->my = JFactory::getUser();
		$this->config = EB::config();
		$this->app = JFactory::getApplication();
		$this->input = EB::request();
		$this->limitstart = $this->input->get('limitstart', 0, 'int');
	}

	/**
	 * Retrieves all post id's given a collection of items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostIds()
	{
		foreach ($this->items as $item) {
			$ids[] = (int) $item->id;
		}

		return $ids;
	}

	/**
	 * Retrieves a list of categories
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadPrimaryCategories()
	{
		$postIds = $this->getPostIds();

		if (!$postIds) {
			return array();
		}

		$model = EB::model('Categories');
		$result = $model->preload($postIds);

		return $result;
	}

	/**
	 * Preloads a list of authors
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadAuthors()
	{
		if (!$this->items) {
			return array();
		}

		// Get list of created_by
		$authorIds = array();

		foreach ($this->items as $item) {
			$authorIds[] = $item->created_by;
		}

		// Ensure that all id's are unique
		array_unique($authorIds);


		$model = EB::model('Blogger');
		$result = $model->preload($authorIds);

		return $result;
	}


	/**
	 * Retrieves a list of custom fields
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadCustomFields()
	{
		$postIds = $this->getPostIds();

		if (!$postIds) {
			return array();
		}

		$model = EB::model('Featured');
		$result = $model->preload($postIds);
	}

	/**
	 * Retrieves a list of featured items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadTags()
	{
		$postIds = $this->getPostIds();

		if (!$postIds) {
			return array();
		}

		$model = EB::model('PostTag');
		$result = $model->preload($postIds);

		return $result;
	}

	/**
	 * Retrieves a list of featured items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadFeaturedItems()
	{
		$postIds = $this->getPostIds();

		if (!$postIds) {
			return array();
		}

		$model = EB::model('Featured');
		$result = $model->preload($postIds);

		return $result;
	}

	/**
	 * Determines if the blog object requires password
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function password(EasyBlogPost &$blog)
	{
		if (!$this->config->get('main_password_protect') || empty($blog->password)) {
			return;
		}

		// If it proceeds here, check if user already entered the password to view the blog post
		$verified = EB::verifyBlogPassword($blog->blogpassword, $blog->id);

		if ($verified) {
			return;
		}

		// If it proceeds, we need to update the blog title to something different
		$blog->title = JText::sprintf('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_TITLE', $blog->title);

		return;
	}

	/**
	 * Formats the microblog posts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function formatMicroblog(EasyBlogPost &$blog)
	{
		$adapter = EB::quickpost()->getAdapter($blog->posttype);

		if ($adapter === false) {
			return;
		}

		$adapter->format($blog);
	}

	/**
	 * Determines if a content requires a read more link.
	 *
	 * @since 	4.0
	 * @access	public
	 * @param 	EasyBlogTableBlog
	 */
	public function hasReadmore(EasyBlogPost &$blog)
	{
		// By default, display the read more link if not configured to respect read more.
		if (!$this->config->get('layout_respect_readmore')) {
			return true;
		}

		// Get the maximum character before read more kicks in.
		$max = $this->config->get('layout_maxlengthasintrotext', 150);

		// When introtext is not empty and content is empty
		if (empty($blog->content) && !empty($blog->intro)) {

			$length	= JString::strlen(strip_tags($blog->intro));

			if ($length > $max && $this->config->get('layout_blogasintrotext')) {
				return true;
			}

			return false;
		}

		// As long as the content is not empty, show the read more
		if (!empty($blog->content)) {
			return true;
		}

		// If it falls anywhere else, always display the read more.
		return true;
	}

	/**
	 * Adds rel="nofollow" to all the links within the content
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string	The content target
	 * @return	string	The content with applied rel="nofollow"
	 */
	public function addNoFollow($content)
	{
		if (!$this->config->get('main_anchor_nofollow')) {
			return $content;
		}

		$content = EB::string()->addNoFollow($content);
		return $content;
	}

	/**
	 * Truncates the content of the blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function truncate(EasyBlogTable &$blog)
	{
		// Get the maximum allowed characters in the content
		$max 		= $this->config->get('layout_maxlengthasintrotext', 150);
		$max 		= $max <= 0 ? 200 : $max;
		$truncate	= true;

		// If introtext is already present, we don't need to truncate anything
		if ($blog->intro && !$blog->content) {
			$truncate 	= false;
		}

		// If we do not need to run any truncation, just run a simple formatting
		if (!$truncate || !$this->config->get('layout_blogasintrotext')) {

			// Process videos
			EB::videos()->format($blog);

			// Process audio files
			EB::audio()->format($blog);

			// Format gallery items
			EB::gallery()->format($blog);

			// Format albums
			EB::album()->format($blog);

			// Remove known codes
			$this->stripCodes($blog);

			if ($this->config->get('main_truncate_image_position') == 'hidden') {

				// Need to remove images, and videos.
				$blog->intro 	= $this->strip_only($blog->intro, '<img>');
				$blog->content	= $this->strip_only($blog->content, '<img>');
			}

			// Determine the correct content to display
			$blog->text 	= empty($blog->intro) ? $blog->content : $blog->intro;

			return;
		}

		// // For normal blog posts, w
		// // @rule: If this is a normal blog post, we match them manually
		// if( isset($row->source) && ( !$row->source || empty( $row->source ) ) )
		// {
		// 	// @rule: Try to match all videos from the blog post first.
		// 	$row->videos		= EB::videos()->getHTMLArray( $row->intro . $row->content );

		// 	// @rule:
		// 	$row->galleries	= EasyBlogHelper::getHelper( 'Gallery' )->getHTMLArray( $row->intro . $row->content );

		// 	// @rule:
		// 	$row->audios 		= EasyBlogHelper::getHelper( 'Audio' )->getHTMLArray( $row->intro . $row->content );

		// 	// @rule:
		// 	$row->albums		= EasyBlogHelper::getHelper( 'Album' )->getHTMLArray( $row->intro . $row->content );
		// }

		// Strip out known codes
		$this->stripCodes($blog);

		// This is the combined content of the intro and the fulltext
		$content 	= $blog->intro . $blog->content;

		// Append ellipses to the content if necessary
		if ($this->config->get('main_truncate_ellipses') && isset($blog->readmore) && $blog->readmore) {
			$blog->text	.= JText::_('COM_EASYBLOG_ELLIPSES');
		}

		if (!$blog->posttype) {
			return $blog;
		}

		if( isset($row->posttype) && ( !$row->posttype || empty( $row->posttype ) ) )
		{
			// @task: Determine the position of media items that should be included in the content.
			$embedHTML			= '';
			$embedVideoHTML		= '';
			$imgHTML            = '';

			if( !empty( $row->galleries ) )
			{
				$embedHTML		.= implode( '' , $row->galleries );
			}

			if( !empty( $row->audios ) )
			{
				$embedHTML		.= implode( '' , $row->audios );
			}

			if( !empty( $row->videos ) )
			{
				$embedVideoHTML		= implode( '' , $row->videos );
			}

			if( !empty( $row->albums ) )
			{
				$embedHTML		.= implode( '' , $row->albums );
			}


			// images
			if( $config->get( 'main_truncate_image_position') == 'top' && !empty( $imgHTML ) )
			{
				$row->text	= $imgHTML . $row->text;
			}
			else if( $config->get( 'main_truncate_image_position') == 'bottom' && !empty( $imgHTML ) )
			{
				$row->text	= $row->text . $imgHTML;
			}


			// videos
			if( $config->get( 'main_truncate_video_position') == 'top' && !empty( $embedVideoHTML) )
			{
				$row->text	= $embedVideoHTML . '<br />' . $row->text;
			}
			else if( $config->get( 'main_truncate_video_position') == 'bottom' && !empty( $embedVideoHTML) )
			{
				$row->text	= $row->text . '<br />' . $embedVideoHTML;
			}


			// @task: Prepend the other media items in the start of the blog posts.
			if( $config->get( 'main_truncate_media_position') == 'top' && !empty( $embedHTML ) )
			{
				$row->text	= $embedHTML . $row->text;
			}
			else if( $config->get( 'main_truncate_media_position') == 'bottom' && !empty( $embedHTML) )
			{
				$row->text	.= $embedHTML;
			}
		}

		return $blog;
	}

	/**
	 * Reverse of strip_tags
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function strip_only($str, $tags, $stripContent = false)
	{
		$content = '';

		if(!is_array($tags))
		{
			$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));

			if(end($tags) == '')
			{
				array_pop($tags);
			}
		}

		foreach($tags as $tag)
		{
			if ($stripContent)
			{
				$content = '(.+</'.$tag.'[^>]*>|)';
			}
			$str = preg_replace('#</?'.$tag.'[^>]*>'.$content.'#is', '', $str);
		}
		return $str;
	}

	/**
	 * Remove known dirty codes from the content
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function stripCodes(EasyBlogTableBlog &$blog)
	{
		// Remove video codes
		EB::videos()->stripCodes($blog);

		// Remove audio codes
		EB::audio()->stripCodes($blog);

		// Remove gallery codes
		EB::gallery()->stripCodes($blog);

		// Remove album codes
		EB::album()->stripCodes($blog);
	}

	public function truncateByWords($content)
	{
		$tag		= false;
		$count		= 0;
		$output		= '';

		// Remove uneccessary html tags to avoid unclosed html tags
		$content		= strip_tags( $content );

		$chunks		= preg_split("/([\s]+)/", $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		foreach($chunks as $piece)
		{

			if( !$tag || stripos($piece, '>') !== false )
			{
				$tag = (bool) (strripos($piece, '>') < strripos($piece, '<'));
			}

			if( !$tag && trim($piece) == '' )
			{
				$count++;
			}

			if( $count > $maxCharacter && !$tag )
			{
				break;
			}

			$output .= $piece;
		}

		return $output;
	}

	public function truncateByChars($content)
	{
		$maxCharacter	= $config->get('layout_maxlengthasintrotext', 150);

		// Remove uneccessary html tags to avoid unclosed html tags
		$content	= strip_tags( $content );

		// Remove blank spaces since the word calculation should not include new lines or blanks.
		$content	= trim( $content );

		$content 	= JString::substr($content, 0, $maxCharacter);
	}

	public function truncateByBreak($content)
	{
		$position	= 0;
		$matches	= array();
		$tag		= '<br';

		$matches	= array();

		do
		{
			$position	= @JString::strpos( strtolower( $content ) , $tag , $position + 1 );

			if( $position !== false )
			{
				$matches[]	= $position;
			}
		} while( $position !== false );

		$maxTag		= (int) $config->get( 'main_truncate_maxtag' );

		if( count( $matches ) > $maxTag )
		{
			$row->text	= JString::substr( $content , 0 , $matches[ $maxTag - 1 ] + 6 );
			$row->readmore	= true;
		}
		else
		{
			$row->text	= $content;
			$row->readmore	= false;
		}
	}

	public function truncateByParagraph()
	{
		$position	= 0;
		$matches	= array();
		$tag		= '</p>';

		// @task: If configured to not display any media items on frontpage, we need to remove it here.
		if( $frontpage && $config->get( 'main_truncate_image_position' ) == 'hidden' )
		{
			// Need to remove images, and videos.
			$content 	= self::strip_only( $content , '<img>' );
		}

		do
		{
			$position	= @JString::strpos( strtolower( $content ) , $tag , $position + 1 );

			if( $position !== false )
			{
				$matches[]	= $position;
			}
		} while( $position !== false );

		// @TODO: Configurable
		$maxTag		= (int) $config->get( 'main_truncate_maxtag' );

		if( count( $matches ) > $maxTag )
		{
			$row->text	= JString::substr( $content , 0 , $matches[ $maxTag - 1 ] + 4 );

			$htmlTagPattern    		= array('/\<div/i', '/\<table/i');
			$htmlCloseTagPattern   	= array('/\<\/div\>/is', '/\<\/table\>/is');
			$htmlCloseTag   		= array('</div>', '</table>');

			for( $i = 0; $i < count($htmlTagPattern); $i++ )
			{

				$htmlItem   			= $htmlTagPattern[$i];
				$htmlItemClosePattern	= $htmlCloseTagPattern[$i];
				$htmlItemCloseTag		= $htmlCloseTag[$i];

				preg_match_all( $htmlItem , strtolower( $row->text ), $totalOpenItem );

				if( isset( $totalOpenItem[0] ) && !empty( $totalOpenItem[0] ) )
				{
					$totalOpenItem	= count( $totalOpenItem[0] );

					preg_match_all( $htmlItemClosePattern , strtolower( $row->text ) , $totalClosedItem );

					$totalClosedItem	= count( $totalClosedItem[0] );

					$totalItemToAdd	= $totalOpenItem - $totalClosedItem;

					if( $totalItemToAdd > 0 )
					{
						for( $y = 1; $y <= $totalItemToAdd; $y++ )
						{
							$row->text 	.= $htmlItemCloseTag;
						}
					}
				}
			}

			$row->readmore	= true;
		}
		else
		{
			$row->text		= $content;
			$row->readmore	= false;
		}
	}

	/**
	 * Truncate's blog post with the respective settings.
	 *
	 * @access	public
	 */
	public static function truncateContent( &$row , $loadVideo = false , $frontpage = false , $loadGallery = true )
	{
		$config			= EasyBlogHelper::getConfig();
		$truncate		= true;
		$maxCharacter	= $config->get('layout_maxlengthasintrotext', 150);

		// @task: Maximum characters should not be lesser than 0
		$maxCharacter	= $maxCharacter <= 0 ? 150 : $maxCharacter;

		// Check if truncation is really necessary because if introtext is already present, just use it.
		if( !empty($row->intro) && !empty($row->content) )
		{
			// We do not want the script to truncate anything since we'll just be using the intro part.
			$truncate			= false;
		}

		// @task: If truncation is not necessary or the intro text is empty, let's just use the content.
		if( !$config->get( 'layout_blogasintrotext' ) || !$truncate )
		{

			//here we process the video and get the links.
			if( $loadVideo )
			{
				$row->intro		= EB::videos()->processVideos( $row->intro );
				$row->content	= EB::videos()->processVideos( $row->content );
			}

			// @rule: Process audio files.
			$row->intro		= EasyBlogHelper::getHelper( 'Audio' )->process( $row->intro );
			$row->content		= EasyBlogHelper::getHelper( 'Audio' )->process( $row->content );

			if( ( ( $config->get( 'main_image_gallery_frontpage' ) && $frontpage ) || !$frontpage ) && $loadGallery )
			{
				$row->intro		= EasyBlogHelper::getHelper( 'Gallery' )->process( $row->intro , $row->created_by );
				$row->content	= EasyBlogHelper::getHelper( 'Gallery' )->process( $row->content , $row->created_by );

				// Process jomsocial albums
				$row->intro		= EasyBlogHelper::getHelper( 'Album' )->process( $row->intro , $row->created_by );
				$row->content	= EasyBlogHelper::getHelper( 'Album' )->process( $row->content , $row->created_by );
			}

			// @task: Strip out video tags
			$row->intro		= EB::videos()->strip( $row->intro );
			$row->content	= EB::videos()->strip( $row->content );

			// @task: Strip out audio tags
			$row->intro		= EasyBlogHelper::getHelper( 'Audio' )->strip( $row->intro );
			$row->content	= EasyBlogHelper::getHelper( 'Audio' )->strip( $row->content );

			// @task: Strip out gallery tags
			$row->intro		= EasyBlogHelper::getHelper( 'Gallery' )->strip( $row->intro );
			$row->content	= EasyBlogHelper::getHelper( 'Gallery' )->strip( $row->content );

			// @task: Strip out album tags
			$row->intro		= EasyBlogHelper::getHelper( 'Album' )->strip( $row->intro );
			$row->content	= EasyBlogHelper::getHelper( 'Album' )->strip( $row->content );

			// @rule: Once the gallery is already processed above, we will need to strip out the gallery contents since it may contain some unwanted codes
			// @2.0: <input class="easyblog-gallery"
			// @3.5: {ebgallery:'name'}
			$row->intro			= EasyBlogHelper::removeGallery( $row->intro );
			$row->content		= EasyBlogHelper::removeGallery( $row->content );

			if( $frontpage && $config->get( 'main_truncate_image_position' ) == 'hidden' )
			{
				// Need to remove images, and videos.
				$row->intro = self::strip_only( $row->intro , '<img>' );
				$row->content = self::strip_only( $row->content , '<img>' );
			}


			$row->text	= empty($row->intro) ? $row->content : $row->intro;

			return $row;
		}

		// @rule: If this is a normal blog post, we match them manually
		if( isset($row->posttype) && ( !$row->posttype || empty( $row->posttype ) ) )
		{
			// @rule: Try to match all videos from the blog post first.
			$row->videos		= EB::videos()->getHTMLArray( $row->intro . $row->content );

			// @rule:
			$row->galleries	= EasyBlogHelper::getHelper( 'Gallery' )->getHTMLArray( $row->intro . $row->content );

			// @rule:
			$row->audios 		= EasyBlogHelper::getHelper( 'Audio' )->getHTMLArray( $row->intro . $row->content );

			// @rule:
			$row->albums		= EasyBlogHelper::getHelper( 'Album' )->getHTMLArray( $row->intro . $row->content );
		}

		// @task: Here we need to strip out all items that are embedded since they are now not required because they'll be truncated.
		// @task: Strip out video tags
		$row->intro		= EB::videos()->strip( $row->intro );
		$row->content	= EB::videos()->strip( $row->content );

		// @task: Strip out audio tags
		$row->intro		= EasyBlogHelper::getHelper( 'Audio' )->strip( $row->intro );
		$row->content	= EasyBlogHelper::getHelper( 'Audio' )->strip( $row->content );

		// @task: Strip out gallery tags
		$row->intro		= EasyBlogHelper::getHelper( 'Gallery' )->strip( $row->intro );
		$row->content	= EasyBlogHelper::getHelper( 'Gallery' )->strip( $row->content );

		// @task: Strip out album tags
		$row->intro		= EasyBlogHelper::getHelper( 'Album' )->strip( $row->intro );
		$row->content	= EasyBlogHelper::getHelper( 'Album' )->strip( $row->content );

		// This is the combined content of the intro and the fulltext
		$content		= $row->intro . $row->content;

		//var_dump($row );exit;

		if( $config->get( 'main_truncate_ellipses' ) && isset( $row->readmore) && $row->readmore )
		{
			$row->text	.= JText::_( 'COM_EASYBLOG_ELLIPSES' );
		}

		if( isset($row->posttype) && ( !$row->posttype || empty( $row->posttype ) ) )
		{
			// @task: Determine the position of media items that should be included in the content.
			$embedHTML			= '';
			$embedVideoHTML		= '';
			$imgHTML            = '';

			if( !empty( $row->galleries ) )
			{
				$embedHTML		.= implode( '' , $row->galleries );
			}

			if( !empty( $row->audios ) )
			{
				$embedHTML		.= implode( '' , $row->audios );
			}

			if( !empty( $row->videos ) )
			{
				$embedVideoHTML		= implode( '' , $row->videos );
			}

			if( !empty( $row->albums ) )
			{
				$embedHTML		.= implode( '' , $row->albums );
			}


			// images
			if( $config->get( 'main_truncate_image_position') == 'top' && !empty( $imgHTML ) )
			{
				$row->text	= $imgHTML . $row->text;
			}
			else if( $config->get( 'main_truncate_image_position') == 'bottom' && !empty( $imgHTML ) )
			{
				$row->text	= $row->text . $imgHTML;
			}


			// videos
			if( $config->get( 'main_truncate_video_position') == 'top' && !empty( $embedVideoHTML) )
			{
				$row->text	= $embedVideoHTML . '<br />' . $row->text;
			}
			else if( $config->get( 'main_truncate_video_position') == 'bottom' && !empty( $embedVideoHTML) )
			{
				$row->text	= $row->text . '<br />' . $embedVideoHTML;
			}


			// @task: Prepend the other media items in the start of the blog posts.
			if( $config->get( 'main_truncate_media_position') == 'top' && !empty( $embedHTML ) )
			{
				$row->text	= $embedHTML . $row->text;
			}
			else if( $config->get( 'main_truncate_media_position') == 'bottom' && !empty( $embedHTML) )
			{
				$row->text	.= $embedHTML;
			}
		}

		return $row;
	}

	public static function formatBlog( $data , $loadComments = false , $removeFeaturedImage = true, $loadVideo = true , $frontpage = false , $loadGallery = true )
	{
		// Ensures that we only proceed to format items if there's real data.
		if (!$data) {
			return $data;
		}

		$app		= JFactory::getApplication();
		$params		= $app->getParams('com_easyblog');
		$model 		= EB::model('Blog');
		$config		= EB::config();

		// Get the tags relation model
		$modelPT 	= EB::model('PostTag');

		// Initialize a default array set
		$result 	= array();

		// Some benchmark says that counting it here would be faster
		$total 	= count($data);

		for ($i = 0; $i < $total; $i++) {

			$row 	= $data[$i];
			$blog 	= EB::table('Blog');
			$blog->bind($row);

			// Since the $blog object does not contain 'team_id', we need to set this here.
			if ($raw->source_type == EASYBLOG_POST_SOURCE_TEAM) {
				$blog->team_id 		= $row->source_id;
			}

			// Since the $blog object does not contain 'category', we need to set this here.
			$blog->category 		= $row->category;
			$blog->featuredImage	= isset( $row->featuredImage ) ? $row->featuredImage : '';

			// Load the author's profile
			$author 	= EB::user($blog->created_by);

			// @Assign dynamic properties that must exist everytime formatBlog is called
			// We can't rely on ->author because CB plugins would mess things up.
			$blog->author			= $author;
			$blog->blogger			= $author;

			$blog->isFeatured		= EB::isFeatured('post', $blog->id);
			$blog->category			= (empty($blog->category)) ? JText::_('COM_EASYBLOG_UNCATEGORIZED') : JText::_($blog->category);

			// @task: Detect password protections.
			$requireVerification	= false;
			$tmpTitle				= $blog->title;

			if($config->get('main_password_protect', true) && !empty($blog->blogpassword)) {
				$blog->title				= JText::sprintf('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_TITLE', $blog->title);
				$requireVerification	= true;
			}

			// @rule: If user already authenticated with the correct password, we will hide the password
			if( $requireVerification && EB::verifyBlogPassword( $blog->blogpassword , $blog->id ) ) {
				$blog->title			= $tmpTitle;
				$blog->blogpassword		= '';
			}

			// @rule: Initialize all variables
			$blog->videos		= array();
			$blog->galleries	= array();
			$blog->albums 		= array();
			$blog->audios		= array();

			// @rule: Before anything get's processed we need to format all the microblog posts first.
			if( !empty( $blog->posttype ) )
			{
				self::formatMicroblog( $blog );
			}

			// @rule: Detect if the content requires a read more link.
			$blog->readmore 	= EasyBlogHelper::requireReadmore( $blog );

			// @rule: Remove any adsense codes from the content.
			$blog->intro		= EB::adsense()->strip($blog->intro);
			$blog->content		= EB::adsense()->strip($blog->content);

			// Truncate content
			EB::truncateContent($blog, $loadVideo, $frontpage, $loadGallery);

			// Assign tags to the custom properties.
			$blog->tags			= $modelPT->getBlogTags($blog->id);




			// Facebook Like integrations
			$facebookLike		= EB::facebook()->getLikeHTML($blog);
			$blog->facebookLike	= $facebookLike;

			$result[]	= $blog;
		}

		return $result;
	}

	/**
	 * Used in json formatters
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sanitize($text) {

		$text = htmlspecialchars_decode($text);
		$text = str_ireplace('&nbsp;', ' ', $text);

		return $text;
	}
}
