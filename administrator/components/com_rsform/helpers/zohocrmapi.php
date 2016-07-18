<?php
/**
* @package RSform!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RSFPZohoCrm {

	protected $token;
	public $debug;
	
	public function __construct($token) {
		$this->token = $token;
	}
	
	public function getFields() {
		$fields = array();
		$url	= 'https://crm.zoho.com/crm/private/json/Leads/getFields?authtoken='.$this->token.'&scope=crmapi';
		
		if ($result = $this->connect($url)) {
			if ($resultFields = json_decode($result)) {
				if (is_object($resultFields)) {
					if (isset($resultFields->Leads)) {
						$sections = isset($resultFields->Leads->section) && is_array($resultFields->Leads->section) ? $resultFields->Leads->section : null;
						if ($sections) {
							foreach ($sections as $section) {
								$fields[$section->name] = array();
								
								if (isset($section->FL)) {
									if (is_array($section->FL)) {
										foreach ($section->FL as $field) {
											$fields[$section->name][] = $field;
										}
									} elseif (is_object($section->FL)) {
										$fields[$section->name][] = $section->FL;
									}
								}
							}
						}
					} elseif (isset($resultFields->response)) {
						if (isset($resultFields->response->error->message)) {
							throw new Exception($resultFields->response->error->message);
						}
					}
				}
			}
		}
		
		return $fields;
	}
	
	public function addLead($data) {
		$url		= 'https://crm.zoho.com/crm/private/xml/Leads/insertRecords?authtoken='.$this->token.'&scope=crmapi';
		$response	= $this->connect($url, $data);
		
		$this->parse($response);
		
		return $response;
	}
	
	protected function connect($url, $data = null) {
		
		if (!function_exists('curl_init')) {
			throw new Exception(JText::_('RSFP_ZOHOCRM_NO_CURL'));
		}
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		
		if ($data) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		}
		
		$response = curl_exec($ch);
		
		if (curl_errno($ch)) {
			throw new Exception(curl_error($ch));
		}
		
		curl_close($ch);
		
		if (!$response) {
			throw new Exception(JText::_('RSFP_ZOHOCRM_INVALID_RESPONSE'));
		}
		
		return $response;
	}
	
	protected function parse($response) {
		$xml = simplexml_load_string($response);
		if (isset($xml->error) && isset($xml->error->message)) {
			$code = '';
			if (isset($xml->error->code)) {
				$code = 'Code '.(string) $xml->error->code.'. ';
			}
			throw new Exception($code.(string) $xml->error->message);
		}
		
		if ($this->debug && isset($xml->result) && isset($xml->result->message)) {
			JFactory::getApplication()->enqueueMessage((string) $xml->result->message);
		}
	}
}