<?php

/*
 * Library for the KeyCDN API
 *
 * @author Tobias Moser
 * @version 0.1
 *
 */

class CloudFlare
{
	public $email;
	public $token;
	public $zone;
	public $CloudFlare_api = 'https://www.cloudflare.com/api_json.html';

	public function __construct($email, $token)
	{
		$this->email = $email;
		$this->token = $token;
	}

	public function purge($zone)
	{
		$params = array(
			'a'     => 'fpurge_ts',
			'tkn'   => $this->token,
			'email' => $this->email,
			'z'     => $zone,
			'v'     => 1,
		);

		// start with curl and prepare accordingly
		$ch = curl_init();

		// send query-str within url or in post-fields
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

		// url
		curl_setopt($ch, CURLOPT_URL, $this->CloudFlare_api);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// retrieve headers
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, 1);

		// set curl timeout
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);

		// make the request
		$result = curl_exec($ch);
		$headers = curl_getinfo($ch);
		$curl_error = curl_error($ch);

		curl_close($ch);

		// get json_output out of result (remove headers)
		$json_output = substr($result, $headers['header_size']);

		// error catching
		if (!empty($curl_error) || empty($json_output))
		{
			return 'CloudFlare-Error: ' . $curl_error . ', Output: ' . $json_output;
		}

		return $json_output;
	}
}
