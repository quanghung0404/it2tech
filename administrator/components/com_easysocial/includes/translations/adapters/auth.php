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

class SocialTokenAuthentication
{
    /*
     * Get the access token.
     *
     * @param string $grantType    Grant type.
     * @param string $scopeUrl     Application Scope URL.
     * @param string $clientID     Application client ID.
     * @param string $clientSecret Application client ID.
     * @param string $authUrl      Oauth Url.
     *
     * @return string.
     */
    public function getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl)
    {
        $connector = ES::connector();

        // Set the arguments for the request
        $connector->addUrl($authUrl);
        $connector->addQuery('grant_type', $grantType);
        $connector->addQuery('client_id', $clientID);
        $connector->addQuery('client_secret', $clientSecret);
        $connector->addQuery('scope', $scopeUrl);

        // Set it as a post request
        $connector->setMethod('POST');

        // Try to connect and get the response
        $connector->connect();

        $output = $connector->getResult($authUrl);

        $response = json_decode($output);

        // If there's an error, skip this
        if (isset($response->error) && $response->error) {
            return false;
        }

        return $response->access_token;
    }
}