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

class EasyBlogComment extends EasyBlog
{
	public $pagination = null;

	/**
	 * Retrieves the adapter
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAdapter($engine)
	{
		$path = dirname(__FILE__) . '/adapters';

		$file = $path . '/' . strtolower($engine) . '.php';

		include_once($file);

		$className = 'EasyBlogComment' . ucfirst($engine);

		$obj = new $className();

		return $obj;
	}

	/**
	 * Format a list of stdclass objects into comment objects
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function format($items)
	{
		if (!$items) {
			return $items;
		}

		$my = JFactory::getUser();
		$model = EB::model('Comment');

		$comments = array();

		foreach ($items as $item) {

			$comment = EB::table('Comment');
			$comment->bind($item);

			// Load the author
			$comment->getAuthor();

			// Set the raw comments for editing
			$comment->raw = $comment->comment;

			// Set the comment depth
			if (isset($item->depth)) {
				$comment->depth = $item->depth;
			}

			// Format the comment
			$comment->comment = nl2br($comment->comment);

			$comment->comment 	= EB::comment()->parseBBCode($comment->comment);

			$comment->likesAuthor 	= '';
			$comment->likesCount 	= 0;
			$comment->isLike		= false;

			if ($this->config->get('comment_likes')) {

				$data = EB::getLikesAuthors($comment->id, 'comment', $my->id);

				$comment->likesAuthor   = $data->string;
				$comment->likesCount 	= $data->count;
				$comment->isLike 		= $model->isLikeComment($comment->id, $my->id);
			}

			// Determine if the current user liked the item or not
			$comments[] = $comment;
		}

		return $comments;
	}

	/**
	 * Determines if the comment system is a built in comment
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isBuiltin()
	{
		if ($this->config->get('intensedebate')) {
			return false;
		}

		if ($this->config->get('comment_disqus')) {
			return false;
		}

		if ($this->config->get('comment_facebook')) {
			return false;
		}

		if ($this->config->get('comment_jomcomment')) {
			return false;
		}

		if ($this->config->get('comment_compojoom')) {
			return false;
		}

		if ($this->config->get('comment_jcomments')) {
			return false;
		}

		if ($this->config->get('comment_rscomments')) {
			return false;
		}

		if ($this->config->get('comment_komento')) {
			return false;
		}

		if ($this->config->get('comment_easysocial')) {
			return false;
		}

		if ($this->config->get('main_comment')) {
			return true;
		}
		
		return true;
	}

	/**
	 * Retrieves the comment count for the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCommentCount($post)
	{
		static $counter = array();

		if (isset($counter[$post->id])) {
			return $counter[$post->id];
		}

		$adapter = false;

		// If configured to display multiple comments, we can't display the counter
		if ($this->config->get('main_comment_multiple')) {
			$counter[$post->id] = false;
			return false;
		}

		// @Livefyre comments
		if ($this->config->get('comment_livefyre')) {
			return false;
		}

		// @Intense debate comments
		if ($this->config->get('intensedebate')) {
			$counter[$post->id] = false;
			return false;
		}

		// @RSComments
		if ($this->config->get('comment_rscomments')) {
			return false;
		}

		// @FB Comments
		if ($this->config->get('comment_facebook')) {
			return false;
		}

		// easyblog builtin comment
		if ($this->config->get('main_comment')) {
			$adapter = $this->getAdapter('easyblog');
		}

		// @Komento
		if ($this->config->get('comment_komento')) {
			$adapter = $this->getAdapter('komento');
		}

		// @EasySocial
		if ($this->config->get('comment_easysocial')) {
			$adapter = $this->getAdapter('easysocial');
		}

		// @Compojoom Comments
		if ($this->config->get('comment_compojoom')) {
			$adapter = $this->getAdapter('cjcomment');
		}

		// @Disqus comments
		if ($this->config->get('comment_disqus')) {
			$adapter = $this->getAdapter('disqus');
		}

		// @JComment comments
		if ($this->config->get('comment_jcomments')) {
			$adapter = $this->getAdapter('jcomments');
		}

		if ($adapter) {

			$counter[$post->id] = $adapter->getCount($post);
			return $counter[$post->id];
		}

		// Let's allow the plugin to also trigger the comment count.
		$params = EB::registry();
		$result = EB::triggerEvent('easyblog.commentCount', $post, $params, 0);

		// Get the count
		$count = trim(implode(' ', $result));

		if (!empty($count)) {
			$counter[$post->id] = $count;

		} else {
			$counter[$post->id] = 0;
		}

		return $counter[$post->id];
	}

	public static function getBlogCommentLite(  $blogId, $limistFrontEnd = 0, $sort = 'asc')
	{
		return EasyBlogComment::getBlogComment($blogId, $limistFrontEnd, $sort, true);
	}

	/**
	 * Retrieves a list of comments
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBlogComment($postId, $limitFrontEnd = 0, $sort = 'asc', $lite = false)
	{
		$model = EB::model('Blog');

		$comments = $model->getBlogComment($postId, $limitFrontEnd, $sort, $lite);
		$pagination = $model->getPagination();

		return $comments;
	}

	/**
	 * Retrieves the comment block
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCommentHTML(EasyBlogPost &$blog, $comments = array() , $pagination = '' )
	{
		// Determines if multiple comment sources should be allowed
		$multiple = $this->config->get('main_comment_multiple');

		// Define default comment systems
		$types = array();

		// Facebook comments
		if ($this->config->get('comment_facebook')) {
			$types['FACEBOOK']	= $this->getAdapter('facebook')->html($blog);

			// If the system is configured to only display a single comment source
			if (!$multiple) {
				return $types['FACEBOOK'];
			}
		}

		// EasySocial comments
		if ($this->config->get('comment_easysocial') && EB::easysocial()->exists()) {

			// Initialize EasySocial's library
			EB::easysocial()->init();

			$easysocial = EB::easysocial()->getCommentHTML($blog);

			// Check whether easysocial plugin is enabled or not.
			if ($easysocial) {
				$types['EASYSOCIAL'] = $easysocial;
			}

			if (!$multiple) {
				return $types['EASYSOCIAL'];
			}
		}

		// Compojoom comments
		if ($this->config->get('comment_compojoom')) {

			$types['COMPOJOOM'] = $this->getAdapter('CjComment')->html($blog);

			if (!$multiple) {
				return $types['COMPOJOOM'];
			}
		}

		// Intensedebate
		if ($this->config->get('comment_intensedebate')) {

			$types['INTENSEDEBATE'] = $this->getAdapter('IntenseDebate')->html($blog);

			if (!$multiple) {
				return $types['INTENSEDEBATE'];
			}
		}

		// Disqus comments
		if ($this->config->get('comment_disqus')) {

			$types['DISQUS'] = $this->getAdapter('Disqus')->html($blog);

			if (!$multiple) {
				return $types['DISQUS'];
			}
		}

		// HyperComments comments
		if ($this->config->get('comment_hypercomments')) {

			$types['HYPERCOMMENTS'] = $this->getAdapter('HyperComments')->html($blog);

			if (!$multiple) {
				return $types['HYPERCOMMENTS'];
			}
		}

		// Livefyre
		if ($this->config->get('comment_livefyre')) {
			$types['LIVEFYRE']	= $this->getAdapter('Livefyre')->html($blog);

			if (!$multiple) {
				return $types['LIVEFYRE'];
			}
		}

		// JComments
		if ($this->config->get('comment_jcomments')) {
			$types['JCOMMENTS']	= $this->getAdapter('jcomments')->html($blog);

			if (!$multiple) {
				return $types['JCOMMENTS'];
			}
		}

		// RSComments
		if ($this->config->get('comment_rscomments')) {
			$types['RSCOMMENTS'] = $this->getAdapter('rscomments')->html($blog);

			if (!$multiple) {
				return $types['RSCOMMENTS'];
			}
		}

		// EasyDiscuss
		if ($this->config->get('comment_easydiscuss')) {

			$easydiscuss = $this->getAdapter('easyDiscuss')->html($blog);

			// Check whether easydiscuss plugin is enabled or not.
			if ($easydiscuss) {
				$types['EASYDISCUSS'] = $easydiscuss;
			}

			if (!$multiple) {
				return $types['EASYDISCUSS'];
			}
		}

		// Komento integrations
		if ($this->config->get('comment_komento') && $this->getAdapter('komento')->exists()) {
			$types['KOMENTO'] = $this->getAdapter('komento')->html($blog);

			if (!$multiple) {
				return $types['KOMENTO'];
			}
		}

		// Built in comments
		if ($this->config->get('main_comment', 1) && !$multiple) {
			$types['EASYBLOGCOMMENTS'] = $this->getAdapter('easyblog')->html($blog, $comments, $pagination);
			return $types['EASYBLOGCOMMENTS'];
		}

		// If multiple comments are enabled, we should check if the user wants to have EasyBlog.
		if ($multiple && $this->config->get('main_comment') && $this->config->get('comment_easyblog')) {
			$types['EASYBLOGCOMMENTS'] = $this->getAdapter('easyblog')->html($blog, $comments, $pagination);
		}

		// If there's 1 system only, there's no point loading the tabs.
		if (count($types) == 1) {
			return $types[key($types)];
		}

		// Reverse the comment systems array so that easyblog comments are always the first item.
		$types = array_reverse($types);

		$template = EB::template();
		$template->set('types', $types);

		$output = $template->output('site/comments/multiple');

		return $output;
	}

	public static function parseBBCode($text)
	{
		//$text	= htmlspecialchars($text , ENT_NOQUOTES );
		$text	= trim($text);
		//$text   = nl2br( $text );

		//$text = preg_replace_callback('/\[code\](.*?)\[\/code\]/ms', "escape", $text);
		$text = preg_replace_callback('/\[code( type="(.*?)")?\](.*?)\[\/code\]/ms', 'escape' , $text );

		// BBCode to find...
		$in = array( 	 '/\[b\](.*?)\[\/b\]/ms',
						 '/\[i\](.*?)\[\/i\]/ms',
						 '/\[u\](.*?)\[\/u\]/ms',
						 '/\[img\](.*?)\[\/img\]/ms',
						 '/\[email\](.*?)\[\/email\]/ms',
						 '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms',
						 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
						 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
						 '/\[quote](.*?)\[\/quote\]/ms',
						 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
						 '/\[list\](.*?)\[\/list\]/ms',
						 '/\[\*\]\s?(.*?)\n/ms'
		);
		// And replace them by...
		$out = array(	 '<strong>\1</strong>',
						 '<em>\1</em>',
						 '<u>\1</u>',
						 '<img src="\1" alt="\1" />',
						 '<a href="mailto:\1">\1</a>',
						 '<a href="\1">\2</a>',
						 '<span style="font-size:\1%">\2</span>',
						 '<span style="color:\1">\2</span>',
						 '<blockquote>\1</blockquote>',
						 '<ol start="\1">\2</ol>',
						 '<ul>\1</ul>',
						 '<li>\1</li>'
		);

		$tmp    = preg_replace( $in , '' , $text );

		$config = EasyBlogHelper::getConfig();

		if( $config->get( 'comment_autohyperlink' ) )
		{
			$text	= EasyBlogComment::replaceURL( $tmp, $text );
		}

		$text	= preg_replace($in, $out, $text);

		// Smileys to find...
		$in = array( 	 ':D',
						 ':)',
						 ':o',
						 ':p',
						 ':(',
						 ';)'
		);

		// And replace them by...
		$out = array(
						'<img alt=":D" src="'.EBLOG_EMOTICONS_DIR.'emoticon-happy.png" />',
						'<img alt=":)" src="'.EBLOG_EMOTICONS_DIR.'emoticon-smile.png" />',

						 '<img alt=":o" src="'.EBLOG_EMOTICONS_DIR.'emoticon-surprised.png" />',
						 '<img alt=":p" src="'.EBLOG_EMOTICONS_DIR.'emoticon-tongue.png" />',
						 '<img alt=":(" src="'.EBLOG_EMOTICONS_DIR.'emoticon-unhappy.png" />',
						 '<img alt=";)" src="'.EBLOG_EMOTICONS_DIR.'emoticon-wink.png" />'
		);
		$text = str_replace($in, $out, $text);

		// paragraphs
		$text = str_replace("\r", "", $text);
		$text = "<p>".preg_replace("/(\n){2,}/", "</p><p>", $text)."</p>";


		$text = preg_replace_callback('/<pre>(.*?)<\/pre>/ms', "removeBr", $text);
		$text = preg_replace('/<p><pre>(.*?)<\/pre><\/p>/ms', "<pre>\\1</pre>", $text);

		$text = preg_replace_callback('/<ul>(.*?)<\/ul>/ms', "removeBr", $text);
		$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/ms', "<ul>\\1</ul>", $text);

		return $text;
	}

	public static function replaceURL( $tmp , $text )
	{
		$pattern = '@(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';

		preg_match_all( $pattern , $tmp , $matches );

		if( isset( $matches[ 0 ] ) && is_array( $matches[ 0 ] ) )
		{
			// to avoid infinite loop, unique the matches
			$uniques = array_unique($matches[ 0 ]);

			foreach( $uniques as $match )
			{
				$match	= str_ireplace( array( '<br' , '<br />' ) , '' , $match );
				$text	= str_ireplace( $match , '<a href="' . $match . '">' . $match . '</a>' , $text );
			}
		}

		$text	= str_ireplace( '&quot;' , '"', $text );
		return $text;
	}
}

// clean some tags to remain strict
// not very elegant, but it works. No time to do better ;)
if (!function_exists('removeBr')) {
	function removeBr($s) {
		return str_replace("<br />", "", $s[0]);
	}
}

// BBCode [code]
if (!function_exists('escape')) {
	function escape($s) {
		global $text;
		$text = strip_tags($text);
		$code = $s[1];
		$code = htmlspecialchars($code);
		$code = str_replace("[", "&#91;", $code);
		$code = str_replace("]", "&#93;", $code);
		return '<pre><code>'.$code.'</code></pre>';
	}
}
