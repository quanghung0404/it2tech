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

// We need the router
require_once( JPATH_ROOT . '/components/com_content/helpers/route.php' );

/**
 * Profile view for article app
 *
 * @since	1.0
 * @access	public
 */
class ArticleViewProfile extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 */
	public function display( $userId = null , $docType = null )
	{
		// Get the user params
		$params		= $this->getUserParams( $userId );

		// Get the app params
		$appParams	= $this->app->getParams();

		// Get the blog model
		$total 		= (int) $params->get( 'total' , $appParams->get( 'total' , 5 ) );

		// Get list of blog posts created by the user on the site.
		$model 		= $this->getModel( 'Article' );
		$articles 	= $model->getItems( $userId , $total );
		$user 		= FD::user( $userId );

		$this->format( $articles , $appParams );

		$this->set( 'user'		, $user );
		$this->set( 'articles'	, $articles );

		echo parent::display( 'profile/default' );
	}

	private function format( &$articles , $params )
	{
		if( !$articles )
		{
			return;
		}

		foreach( $articles as $article )
		{
			$category	= JTable::getInstance( 'Category' );
			$category->load( $article->catid );

			$article->category 				= $category;
			$article->permalink	 			= ContentHelperRoute::getArticleRoute( $article->id . ':' . $article->alias , $article->catid );
			$article->permalink	 			= JRoute::_($article->permalink);

			$article->category->permalink	= ContentHelperRoute::getCategoryRoute( $category->id . ':' . $category->alias );
			$article->category->permalink	= JRoute::_($article->category->permalink);

			$article->content 				= empty( $article->introtext ) ? $article->fulltext : $article->introtext;

			$titleLength 	= $params->get( 'title_length' );
			$contentLength	= $params->get( 'content_length' );

			if( $titleLength )
			{
				$article->title 	= JString::substr( $article->title , 0 , $titleLength );
			}

			// Try to get image of the article
			$image = $this->processContentImage($article->content);

			if ($image) {
				$article->image = $image;
			}

			if ($contentLength) {
				$article->content 	= JString::substr( strip_tags( $article->content ) , 0 , $contentLength ) . ' ...' ;
			} else {
				$base = JURI::base(true).'/';
				$protocols	= '[a-zA-Z0-9]+:'; //To check for all unknown protocals (a protocol must contain at least one alpahnumeric fillowed by :
				$regex		= '#(src|href|poster)="(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
				$article->content = preg_replace($regex, "$1=\"$base\$2\"", $article->content);
			}
		}
	}

	private function processContentImage( $content )
	{
		// @rule: Match images from content
		$pattern	= '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
		preg_match( $pattern , $content , $matches );

		$image		= '';
		if( $matches )
		{
			$image		= isset( $matches[1] ) ? $matches[1] : '';

			if( JString::stristr( $matches[1], 'https://' ) === false && JString::stristr( $matches[1], 'http://' ) === false && !empty( $image ) )
			{
				$image	= rtrim(JURI::root(), '/') . '/' . ltrim( $image, '/');
			}
		}
		return $image;
	}
}
