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

/**
 * ClickableHook
 *
 * Converts URLs and emails (not wrapped in tags) into clickable links.
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/php/decoda
 */

class ClickableHook extends DecodaHook {

	/**
	 * Matches a link or an email, and converts it to an anchor tag.
	 *
	 * @access public
	 * @param string $content
	 * @return string
	 */
	public function afterParse($content) {

		if ($this->getParser()->getFilter('Url')) {

            // $chars		= preg_quote('-_=+|\;:&?/[]%,.!@#$*(){}"\'', '/');
            // $protocols	= array('http', 'https', 'ftp', 'irc', 'file', 'telnet');
            // $pattern	= implode('', array(
						      //           '(' . implode('|', $protocols) . ')s?:\/\/', // protocol
						      //           '([\w\.\+]+:[\w\.\+]+@)?', // login
						      //           '([\w\.]{5,255}+)', // domain, tld
						      //           '(:[0-9]{0,6}+)?', // port
						      //           '([a-z0-9' . $chars . ']+)?', // query
						      //           '(#[a-z0-9' . $chars . ']+)?' // fragment
						      //       )
            // 			);

            // $content	= preg_replace_callback('/("|\'|>|<br>|<br\/>)?(' . $pattern . ')/is', array($this, '_urlCallback'), $content);
			$protocol	= '(http|ftp|irc|file|telnet)s?:\/?\/?';
			$login		= '([-a-zA-Z0-9\.\+]+:[-a-zA-Z0-9\.\+]+@)?';
			$domain		= '([-a-zA-Z0-9\.]{5,255}+)';
			$port		= '(:[0-9]{0,6}+)?';
			$query		= '([a-zA-Z0-9' . preg_quote('-_=;:&?/[].,', '/') . '\(\)]+)?';
			$pattern 	= '/(^|\n|\s)' . $protocol . $login . $domain . $query . '/is';
			$content	= preg_replace_callback($pattern, array($this, '_urlCallback'), $content);
		}

		// Based on schema: http://en.wikipedia.org/wiki/Email_address
		if ($this->getParser()->getFilter('Email')) {
			$content = preg_replace_callback(EmailFilter::EMAIL_PATTERN, array($this, '_emailCallback'), $content);
		}

		return $content;
	}

	/**
	 * Callback for email processing.
	 *
	 * @access protected
	 * @param array $matches
	 * @return string
	 */
	protected function _emailCallback($matches) {
		return $this->getParser()->getFilter('Email')->parse(array(
			'tag' => 'email',
			'attributes' => array()
		), trim($matches[0]));
	}

	/**
	 * Callback for URL processing.
	 *
	 * @access protected
	 * @param array $matches
	 * @return string
	 */
	protected function _urlCallback($matches) {
		return $this->getParser()->getFilter('Url')->parse(array(
			'tag' => 'url',
			'attributes' => array('target' => '_blank', 'rel' => 'nofollow')
		), trim($matches[0]));
	}

}
