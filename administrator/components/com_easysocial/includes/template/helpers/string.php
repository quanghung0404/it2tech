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

class ThemesHelperString
{
	public static function escape( $string )
	{
		return FD::get( 'String' )->escape( $string );
	}

	/**
	 * Formats a given date string with a given date format
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The current timestamp
	 * @param	string		The language string format or the format for Date
	 * @param	bool		Determine if it should be using the appropriate offset or GMT
	 * @return
	 */
	public static function date( $timestamp , $format = '' , $withOffset = true )
	{
		// Get the current date object based on the timestamp provided.
		$date 	= FD::date( $timestamp , $withOffset );

		// If format is not provided, we should use DATE_FORMAT_LC2 by default.
		$format	= empty( $format ) ? 'DATE_FORMAT_LC2' : $format;

		// Get the proper format.
		$format	= JText::_( $format );

		$dateString 	= $date->toFormat( $format );

		return $date->toFormat( $format );
	}

	/**
	 * Pluralize the string if necessary.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function pluralize( $languageKey , $count )
	{
		return FD::string()->computeNoun( $languageKey , $count );
	}

	/**
	 * Alternative to @truncater to truncate contents with HTML codes
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function truncate($text, $length = 250, $ending = '', $exact = false)
	{
		// Load site's language file
		FD::language()->loadSite();

		if (!$ending) {
			$ending = JText::_('COM_EASYSOCIAL_ELLIPSES');
		}

		// If the plain text is shorter than the maximum length, return the whole text
		if (JString::strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
			return $text;
		}

		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
		$total_length = JString::strlen($ending);
		$open_tags = array();
		$truncate = '';

		foreach ($lines as $line_matchings) {

			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1])) {
				// if it's an "empty element" with or without xhtml-conform closing slash
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					// do nothing
				// if tag is a closing tag
				} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);
					if ($pos !== false) {
					unset($open_tags[$pos]);
					}
				// if tag is an opening tag
				} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, JString::strtolower($tag_matchings[1]));
				}
				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}
			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length =JString::strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			if ($total_length+$content_length> $length) {
				// the number of characters which are left
				$left = $length - $total_length;
				$entities_length = 0;
				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity) {
						if ($entity[1]+1-$entities_length <= $left) {
							$left--;
							$entities_length += JString::strlen($entity[0]);
						} else {
							// no more characters left
							break;
						}
					}
				}
				$truncate .= JString::substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			} else {
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}
			// if the maximum length is reached, get off the loop
			if($total_length>= $length) {
				break;
			}
		}

		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = JString::strrpos($truncate, ' ');
			if (isset($spacepos)) {
				// ...and cut the text in this position
				$truncate = JString::substr($truncate, 0, $spacepos);
			}
		}

		// add the defined ending to the text
		$truncate .= $ending;

		// close all unclosed html-tags
		foreach ($open_tags as $tag) {
			$truncate .= '</' . $tag . '>';
		}

		$uid = uniqid();

		$theme = FD::themes();
		$theme->set('truncated', $truncate);
		$theme->set('uid', $uid);
		$theme->set('text', $text);

		$output = $theme->output('admin/html/string.truncate');

		return $output;
	}

	/**
	 * Truncates a string at a centrain length and add a more link
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function truncater($text, $maxLength)
	{
		// Load site's language file
		FD::language()->loadSite();

		$theme = FD::themes();

		$length	= JString::strlen($text);
		$uid 	= uniqid();

		$theme->set( 'uid'	, $uid );
		$theme->set( 'length', $length );
		$theme->set( 'text' , $text );
		$theme->set( 'max'	, $maxLength );

		$output 	= $theme->output( 'admin/html/string.truncater' );

		return $output;
	}
}
