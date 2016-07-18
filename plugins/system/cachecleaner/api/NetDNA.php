<?php

/**
 * NetDNA REST Client Library
 *
 * @copyright 2012
 * @author    Karlo Espiritu
 * @version   1.0 2012-09-21
 */
class NetDNA
{
	public $alias;

	public $key;

	public $secret;

	public $netdnarws_url = 'https://rws.netdna.com';

	private $consumer;

	public function __construct($alias, $key, $secret, $options = null)
	{
		$this->alias = $alias;
		$this->key = $key;
		$this->secret = $secret;

		if (!class_exists('OAuthConsumer'))
		{
			require_once __DIR__ . '/OAuth/OAuthConsumer.php';
		}
		$this->consumer = new OAuthConsumer($key, $secret, null);
	}

	private function execute($selected_call, $method_type, $params)
	{
		// the endpoint for your request
		$endpoint = "$this->netdnarws_url/$this->alias$selected_call";

		//parse endpoint before creating OAuth request
		$parsed = parse_url($endpoint);
		if (array_key_exists("parsed", $parsed))
		{
			parse_str($parsed['query'], $params);
		}

		//generate a request from your consumer
		if (!class_exists('OAuthRequest'))
		{
			require_once __DIR__ . '/OAuth/OAuthRequest.php';
		}
		$req_req = OAuthRequest::from_consumer_and_token($this->consumer, null, $method_type, $endpoint, $params);

		//sign your OAuth request using hmac_sha1
		if (!class_exists('OAuthSignatureMethod_HMAC_SHA1'))
		{
			require_once __DIR__ . '/OAuth/OAuthSignatureMethod_HMAC_SHA1.php';
		}
		$sig_method = new OAuthSignatureMethod_HMAC_SHA1();
		$req_req->sign_request($sig_method, $this->consumer, null);

		// create curl resource
		$ch = curl_init();

		// set url
		curl_setopt($ch, CURLOPT_URL, $req_req);

		// return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// Set SSL Verifyer off
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		// set curl timeout
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);

		// set curl custom request type if not standard
		if ($method_type != "GET" && $method_type != "POST")
		{
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method_type);
		}

		if ($method_type == "POST" || $method_type == "PUT" || $method_type == "DELETE")
		{
			if (!class_exists('OAuthUtil'))
			{
				require_once __DIR__ . '/OAuth/OAuthUtil.php';
			}
			$query_str = OAuthUtil::build_http_query($params);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:', 'Content-Length: ' . strlen($query_str)));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query_str);
		}

		// retrieve headers
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, 1);

		//set user agent
		curl_setopt($ch, CURLOPT_USERAGENT, 'PHP NetDNA API Client');

		// make call
		$result = curl_exec($ch);
		$headers = curl_getinfo($ch);
		$curl_error = curl_error($ch);

		// close curl resource to free up system resources
		curl_close($ch);

		// $json_output contains the output string
		$json_output = substr($result, $headers['header_size']);

		// catch errors
		if (!empty($curl_error) || empty($json_output))
		{
			//throw new \NetDNA\RWSException("CURL ERROR: $curl_error, Output: $json_output", $headers['http_code'], null, $headers);
			return 'CURL ERROR: ' . $curl_error . ', Output: ' . $json_output;
		}

		return $json_output;
	}

	public function get($selected_call, $params = array())
	{

		return $this->execute($selected_call, 'GET', $params);
	}

	public function post($selected_call, $params = array())
	{
		return $this->execute($selected_call, 'POST', $params);
	}

	public function put($selected_call, $params = array())
	{
		return $this->execute($selected_call, 'PUT', $params);
	}

	public function delete($selected_call, $params = array())
	{
		return $this->execute($selected_call, 'DELETE', $params);
	}
}
