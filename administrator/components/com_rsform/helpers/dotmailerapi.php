<?php
/**
* @package RSform!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RSFPDotmailer {
	
	protected $username;
	protected $password;
	protected $errors = array();
	
	public function __construct($username, $password) {
		$this->username = $username;
		$this->password = $password;
	}
	
	public function getLists() {
		$url = 'https://api.dotmailer.com/v2/address-books';
		$lists = $this->connect($url);
		
		return $lists;
	}
	
	public function getFields() {
		$url = 'https://api.dotmailer.com/v2/data-fields';
		$fields = $this->connect($url);
		
		return $fields;
	}
	
	public function addContact($id, $contact) {
		$url = 'https://api.dotmailer.com/v2/address-books/'.$id.'/contacts';
		return $this->connect($url,$contact);
	}
	
	public function removeContact($id, $email) {
		$url = 'https://api.dotmailer.com/v2/contacts/'.$email;
		$user = $this->connect($url);
		
		if ($user->id) {
			$url = 'https://api.dotmailer.com/v2/contacts/'.$user->id;
			return $this->connect($url, null, 'DELETE');
		}

		return false;
	}
	
	public function unsubscribeContact($id, $contact) {
		$url = 'https://api.dotmailer.com/v2/address-books/'.$id.'/contacts/unsubscribe';
		$request = $this->connect($url,$contact);
		return $request;
	}
	
	protected function connect($url, $data = null, $method = 'GET') {
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username.':'.$this->password);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		
		if ($method != 'GET') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		}
		
		if ($data) {
			$json = json_encode($data);
			
			curl_setopt($ch, CURLOPT_POST, count($data));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($json))
			);
		}
		
		$response = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		if (curl_errno($ch)) {
			throw new Exception(curl_error($ch));
		}
		
		curl_close($ch);
		
		if ($method == 'DELETE' && $httpcode == 204) {
			return true;
		}
		
		if (!$response) {
			throw new Exception(JText::_('RSFP_DOTMAILER_INVALID_RESPONSE'));
		}
		
		$result = json_decode($response);
		
		if ($result === null) {
			throw new Exception(JText::_('RSFP_DOTMAILER_PARSING_ERROR'));
		}
		
		if (is_object($result) && isset($result->message)) {
			throw new Exception($result->message);
		}
		
		return $result;
	}
}