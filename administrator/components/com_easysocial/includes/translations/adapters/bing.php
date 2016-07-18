<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/adapter.php');

class SocialTranslationsBing extends SocialTranslationsAdapter
{  
    public $id = null;
    public $secret = null;
    public $endPoint = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/';
    public $scopeUrl = 'http://api.microsofttranslator.com';
    public $grantType = 'client_credentials';

    public function __construct()
    {
        parent::__construct();

        $this->id = $this->config->get('stream.translations.bingid');
        $this->secret = $this->config->get('stream.translations.bingsecret');
    }

    /**
     * Translates a given content to a language
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function translate($contents, $targetLanguage)
    {
        $token = $this->getToken();

        $connector = ES::connector();

        // Set the url
        $url = 'http://api.microsofttranslator.com/v2/Http.svc/Translate?to=' . $targetLanguage . '&text=' . urlencode($contents);
        $connector->addUrl($url);
        
        // Add the token to the headers
        $headers = array('Authorization: Bearer ' . $token, 'Content-Type: text/xml');
        $connector->addHeader($headers);

        $connector->connect();

        $response = $connector->getResult($url);
        //Interprets a string of XML into an object.
        $xmlObj = simplexml_load_string($response);

        $output = '';

        $tmp = (array) $xmlObj[0];
        $keys = array_keys($tmp);

        // If there is a <body> in the response, we know something went wrong, just return the original contents
        if (isset($keys[0]) && $keys[0] === 'body') {
            return $contents;
        }

        foreach ((array)$xmlObj[0] as $val) {    
            $output = $val;
        }

        return $output;
	}
}
