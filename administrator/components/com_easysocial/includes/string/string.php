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

jimport( 'joomla.filesystem.file' );

class SocialString
{
	private $adapter	= null;

	public function __construct()
	{
		return $this;
	}

	/**
	 * Always create a new copy.
	 *
	 * @since 	1.0
	 * @access	public
	 * @param	null
	 */
	public static function factory()
	{
		return new self();
	}

	public function __call( $method , $arguments )
	{
	    if( method_exists( $this->adapter , $method ) )
	    {
			return call_user_func_array( array( $this->adapter , $method ) , $arguments );
		}

		return false;
	}

	/**
	 * Computes a noun given the string and count
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function computeNoun( $string , $count )
	{
		$config 		= FD::config();
		$zeroAsPlural	= $config->get( 'string.pluralzero' , true );

		// Always use plural
		$text 		= $string . '_PLURAL';

		if( $count == 1 || $count == -1 || ( $count == 0 && !$zeroAsPlural ) )
		{
			$text 	= $string 	. '_SINGULAR';
		}

		return $text;
	}

	/**
	 * Convert a list of names into a string valid for notifications
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function namesToNotifications( $users )
	{
		// Ensure that users is an array
		$users 	= FD::makeArray( $users );

		// Ensure that they are all SocialUser objects
		$users 	= FD::user( $users );

		// Get the total number of users
		$total	= count( $users );

		// Init the name variable
		$name = '';


		// If there's only 1 user, we don't need to do anything.
		if ($total == 1) {
			$name 	= '{b}' . $users[0]->getName() . '{/b}';

			return $name;
		}

		// user1 and user2
		if ($total == 2) {
			$name 	= JText::sprintf('COM_EASYSOCIAL_STRING_NOTIFICATIONS_NAMES_AND' , $users[0]->getName() , $users[ 1 ]->getName() );
		}

		// user1, user2 and user3
		if ($total == 3) {
			$name 	= JText::sprintf('COM_EASYSOCIAL_STRING_NOTIFICATIONS_NAMES_AND_USER' , $users[0]->getName() , $users[ 1 ]->getName(), $users[2]->getName());
		}

		// user1, user2, user3 and user4
		if ($total == 4) {
			$name 	= JText::sprintf( 'COM_EASYSOCIAL_STRING_NOTIFICATIONS_NAMES_AND_USERS' , $users[0]->getName() , $users[ 1 ]->getName() , $users[ 2 ]->getName() , $users[ 3 ]->getName() );
		}

		// user1, user2, user3 and 2 others
		if ($total >= 5) {
			$name 	= JText::sprintf( 'COM_EASYSOCIAL_STRING_NOTIFICATIONS_NAMES_USER_AND_OTHERS' , $users[0]->getName() , $users[ 1 ]->getName() , $users[ 2 ]->getName(), $total - 3);
		}

		return $name;
	}

	/**
	 * Determines the type of parameters parsed to this method and automatically
	 * returns a stream-ish like content.
	 *
	 * E.g: user1 and user2,
	 * 		user1 , user2 and user3
	 * 		user1 , user2 , user3 and 2 others
	 *
	 * @param	Array	$people
	 * @param	boolean	$linkUsers
	 * @param	int $showLimit
	 */
	public function namesToStream( $users , $linkUsers = true , $limit = 3 , $uppercase = true , $boldNames = false, $showPopbox = false )
	{
		// Ensure that users is an array
		$users 	= FD::makeArray( $users );

		// Ensure that they are all SocialUser objects
		$users 	= FD::user( $users );

		$theme 	= FD::themes();

		$theme->set( 'users'		, $users );
		$theme->set( 'boldNames'	, $boldNames );
		$theme->set( 'linkUsers' 	, $linkUsers );
		$theme->set( 'total' 		, count( $users ) );
		$theme->set( 'limit'		, $limit );
		$theme->set( 'uppercase'	, $uppercase );
		$theme->set( 'showPopbox'	, $showPopbox );

		$message 	=  $theme->output( 'site/utilities/users' );

		return $message;
	}

	/**
	 * Replaces email text with html codes
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function replaceEmails( $text )
	{
		if( strpos( $text, 'data:image') !== false )
		{
			return $text;
		}

		// lets first replace the base64 image string.
		$pattern 	= '/(\S+@\S+\.\S+)/';
    	$replace	= '<a href="mailto:$1">$1</a>';

	    return preg_replace( $pattern , $replace, $text);
	}

	/**
	 * Replaces hyperlink text with html anchors
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The string to look into
	 * @return
	 */
	public static function replaceHyperlinks($text, $options=array('target'=>'_blank'), $tag = 'anchor')
	{
		// This seems to replace anything that has /index.php/something
		// $pattern		= '@(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';

		$pattern = '@(?i)\b((?:https?://|www\d{0,3}[.])(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*t\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';

		preg_match_all( $pattern , $text , $matches );

		$attributes = '';
		foreach ($options as $key => $val) {
			$attributes .= " $key=\"$val\"";
		}

		if( isset( $matches[ 0 ] ) && is_array( $matches[ 0 ] ) )
		{
			// to avoid infinite loop, unique the matches
			$uniques = array_unique($matches[ 0 ]);

			foreach( $uniques as $match )
			{
				$matchProtocol 	= $match;

				if( stristr( $matchProtocol , 'http://' ) === false && stristr( $matchProtocol , 'https://' ) === false && stristr( $matchProtocol , 'ftp://' ) === false )
				{
					$matchProtocol	= 'http://' . $matchProtocol;
				}

				// Skip any gist urls
				if (stristr($match, 'https://gist.github.com') !== false) {
					continue;
				}

				if ($tag == 'anchor') {
					$text   = str_ireplace( $match , '<a href="' . $matchProtocol . '"' . $attributes . '>' . $match . '</a>' , $text );
				}

				if ($tag == 'bbcode') {
					$text 	= str_ireplace( $match , '[url]' . $match . '[/url]' , $text );
				}

			}
		}

		// $text	= str_ireplace( '&quot;' , '"', $text );
		return $text;
	}

	/**
	 * Determines the type of parameters parsed to this method and automatically
	 * returns a stream-ish like string.
	 *
	 * E.g: name1 , name2 and name3
	 *
	 * @param	Array of object containing name and link property
	 * @return 	string
	 */
	public function beautifyNamestoStream( $data )
	{
		$datatring 	= '';
		$j              = 0;
		$cntData       = count( $data );
		foreach( $data as $item )
		{

			if( empty( $datatring ) )
			{
				$text			= '<a href="' . $item->link . '">' . $item->name . '</a>';
			    $datatring	= $text;
			}
			else
			{
			    if( ($j + 1) == $cntData)
			    {
					$text   		= '<a href="' . $item->link . '">' . $item->name . '</a>';
			        $datatring  	= $datatring . ' and ' . $text;
			    }
			    else
			    {
			        $datatring  = $datatring . ', ' . $text;
			    }
			}

			$j++;
		}

		return $datatring;
	}

	/**
	 * Convert special characters to HTML entities
	 *
	 * @param	string
	 * @return  string
	 */
	public function escape( $var )
	{
		return htmlspecialchars( $var, ENT_COMPAT, 'UTF-8' );
	}

	/**
	 * This is useful if the inital tag processing is using the simple mode so that we can revert back to the original tags
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function processSimpleTags( $message )
	{
		$pattern 	= '/\[tag\](.*)\[\/tag\]';

		preg_match_all( $pattern . '/uiU', $message , $matches , PREG_SET_ORDER);

		if( $matches )
		{
			foreach( $matches as $match )
			{
				$jsonString 	= html_entity_decode( $match[ 1 ] );
				$obj 			= FD::json()->decode( $jsonString );

				if( !isset( $obj->type ) )
				{
					continue;
				}

				if( $obj->type == 'entity' )
				{
					$replace 	= '<a href="' . $obj->link . '" data-popbox="module://easysocial/profile/popbox" data-popbox-position="top-left" data-user-id="' . $obj->id . '" class="mentions-user">' . $obj->title . '</a>';
				}

				if( $obj->type == 'hashtag' )
				{
					$replace 	= '<a href="' . $obj->link . '" class="mentions-hashtag">#' . $obj->title . '</a>';
				}

				$message 	= str_ireplace( $match[ 0 ] , $replace , $message );
			}
		}

		return $message;
	}

	/**
	 * Processes a text and replace the mentions / hashtags hyperlinks.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function processTags( $tags , $message , $simpleTags = false )
	{
		// We need to merge the mentions and hashtags since we are based on the offset.
		foreach ($tags as $tag) {
			
			if ($tag->type == 'entity' || $tag->type == 'user') {
				
				if (isset( $tag->user ) && $tag->user instanceof SocialUser) {
					$user = $tag->user;
				} else {
					$user = FD::user($tag->item_id);
				}

				if ($simpleTags) {
					$data = new stdClass();
					$data->type = $tag->type;
					$data->link = $user->getPermalink();
					$data->title = $user->getName();
					$data->id = $user->id;

					$replace = '[tag]' . FD::json()->encode( $data ) . '[/tag]';
				} else {
					$replace 	= '<a href="' . $user->getPermalink() . '" data-popbox="module://easysocial/profile/popbox" data-popbox-position="top-left" data-user-id="' . $user->id . '" class="mentions-user">' . $user->getName() . '</a>';
				}

			}

			if ($tag->type == 'hashtag') {
				$alias = JFilterOutput::stringURLSafe($tag->title);

				$url = FRoute::dashboard(array('layout' => 'hashtag' , 'tag' => $alias));

				if ($simpleTags) {
					$data = new stdClass();
					$data->type = $tag->type;
					$data->link = $url;
					$data->title = $tag->title;
					$data->id = $tag->id;

					$replace = '[tag]' . FD::json()->encode($data) . '[/tag]';
				} else {
					$replace = '<a href="' . $url . '" class="mentions-hashtag">#' . $tag->title . '</a>';
				}
			}

			$message	= JString::substr_replace( $message , $replace , $tag->offset , $tag->length );
		}

		return $message;
	}

	/**
	 * Replaces gist links into valid gist objects
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function replaceGist($content)
	{
		$pattern 	= '/https:\/\/gist\.github\.com\/(.*)(?= )/is';
		//
		$content	= preg_replace($pattern, '<script src="$0.js"></script>', $content);

		return $content;
	}

	/**
	 * Convert bbcode data into valid html codes
	 *
	 * @param	string
	 * @return  string (in html)
	 */
	public function parseBBCode($string, $options = array())
	{
		// Configurable option to determine if the bbcode should perform the following
		$options = array_merge(array('censor' => false, 'emoticons' => true), $options);

		$bbcode = ES::bbcode();

		$string = $bbcode->parse($string, $options);

		return $string;
	}
}
