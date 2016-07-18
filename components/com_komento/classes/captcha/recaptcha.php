<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

/*
 * This is a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          http://recaptcha.net/plugins/php/
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin/create
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * Copyright (c) 2007 reCAPTCHA -- http://recaptcha.net
 * AUTHORS:
 *   Mike Crawford
 *   Ben Maurer
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class KomentoRecaptcha extends JObject
{
	const RECAPTCHA_API_SERVER = "http://www.google.com/recaptcha/api";
	const RECAPTCHA_API_SECURE_SERVER = "https://www.google.com/recaptcha/api";
	const RECAPTCHA_VERIFY_SERVER = "www.google.com";

	public function verify( $data, $params = array() )
	{
		if (   !array_key_exists('recaptcha_challenge_field', $data)
			|| !array_key_exists('recaptcha_response_field', $data)
			|| empty($data['recaptcha_challenge_field'])
			|| empty($data['recaptcha_response_field']) )
		{
			$this->setError( JText::_('COM_KOMENTO_RECAPTCHA_INVALID_RESPONSE') );
			return false;
		}

		$config		= Komento::getConfig();
		$privatekey	= $config->get( 'antispam_recaptcha_private_key' );

		$remoteip	= JRequest::getVar('REMOTE_ADDR', '', 'SERVER');

		// if ($remoteip)
		// {
		// 	$this->setError( 'For security reasons, you must pass the remote ip to reCAPTCHA' );
		// 	return false;
		// }

		if (empty($privatekey))
		{
			$this->setError( 'To use reCAPTCHA you must get an API key from <a href="https://www.google.com/recaptcha/admin/create">https://www.google.com/recaptcha/admin/create</a>' );
			return false;
		}

		$params		= array(
						'privatekey' => $privatekey,
						'remoteip' => $remoteip,
						'challenge' => $data['recaptcha_challenge_field'],
						'response' => $data['recaptcha_response_field']
					) + $params;

		$response	= $this->_recaptcha_http_post( self::RECAPTCHA_VERIFY_SERVER, "/recaptcha/api/verify", $params );

		$answers = explode ("\n", $response [1]);

		if (trim($answers[0]) != 'true')
		{
			$this->setError( $answers [1] );
			return false;
		}

		return true;
	}

	public function getHTML()
	{
		$template 	= Komento::getTheme();
		$config		= Komento::getConfig();

		$publicKey	= $config->get( 'antispam_recaptcha_public_key' );
		$language	= $config->get( 'antispam_recaptcha_lang', 'en' );
		$theme		= $config->get( 'antispam_recaptcha_theme', 'clean' );
		$server		= $config->get( 'antispam_recaptcha_ssl' ) ? self::RECAPTCHA_API_SECURE_SERVER : self::RECAPTCHA_API_SERVER;

		$template->set( 'server', $server );
		$template->set( 'publicKey', $publicKey );
		$template->set( 'language', $language );
		$template->set( 'theme', $theme );

		// Use AJAX method to prevent operation aborted problems with IE
		return $template->fetch( 'comment/recaptcha.php' );
	}

	/**
	 * Encodes the given data into a query string format
	 * @param $data - array of string elements to be encoded
	 * @return string - encoded request
	 */
	private function _recaptcha_qsencode( $data )
	{
		$req = "";
		foreach ($data as $key => $value)
		{
			$req .= $key . '=' . urlencode( stripslashes($value) ) . '&';
		}

		// Cut the last '&'
		$req = rtrim($req, '&');
		return $req;
	}

	/**
	 * Submits an HTTP POST to a reCAPTCHA server
	 * @param string $host
	 * @param string $path
	 * @param array $data
	 * @param int port
	 * @return array response
	 */
	private function _recaptcha_http_post($host, $path, $data, $port = 80)
	{
		$req = $this->_recaptcha_qsencode ($data);

		$http_request  = "POST $path HTTP/1.0\r\n";
		$http_request .= "Host: $host\r\n";
		$http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
		$http_request .= "Content-Length: " . strlen($req) . "\r\n";
		$http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
		$http_request .= "\r\n";
		$http_request .= $req;

		$response = '';
		if( false == ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) ) ) {
				die ('Could not open socket');
		}

		fwrite($fs, $http_request);

		while ( !feof($fs) )
		{
			$response .= fgets($fs, 1160); // One TCP-IP packet
		}

		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);

		return $response;
	}

	public static function getReloadSyntax()
	{
		return 'Recaptcha.reload();';
	}
}
