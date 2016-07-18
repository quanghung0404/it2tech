<?php
// namespace administrator\components\com_jmap\framework\seostats\services;
/**
 *
 * @package JMAP::SEOSTATS::administrator::components::com_jmap
 * @subpackage seostats
 * @subpackage services
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Configuration constants for the SEOSTATS package
 *
 * @package JMAP::SEOSTATS::administrator::components::com_jmap
 * @subpackage seostats
 * @subpackage services
 * @since 3.3
 */
class JMapSeostatsServices {
	public static $PROVIDER = '["alexa","google"]';
	
	// Alexa public report URLs.
	public static $ALEXA_SITEINFO_URL = 'http://www.alexa.com/siteinfo/%s';
	public static $ALEXA_GRAPH_URL = 'https://traffic.alexa.com/graph?&o=f&c=1&y=%s&b=ffffff&n=666666&w=%s&h=%s&r=%sm&u=%s';
	
	// Google Websearch API Endpoint.
	public static $GOOGLE_APISEARCH_URL = 'http://ajax.googleapis.com/ajax/services/search/web?v=1.0&rsz=%s&q=%s';
	
	// Google Pagespeed Insights API Endpoint.
	public static $GOOGLE_PAGESPEED_URL = 'https://www.googleapis.com/pagespeedonline/v1/runPagespeed?url=%s&key=%s';
	
	// Google +1 Fastbutton URL.
	public static $GOOGLE_PLUSONE_URL = 'https://plusone.google.com/u/0/_/+1/fastbutton?count=true&url=%s';
	
	// The default top level domain ending to use to query Google.
	const GOOGLE_TLD = 'com';
	
	// The HTTP header value for the 'Accept-Language' attribute.
	//
	// Note: Google search results, doesn't matter which tld you request, vary depending on
	// the value sent for the HTTP header attribute 'Accept-Language'! Eg: I am from Germany.
	// Even if I use the "ncr" (no country redirect) request parameter, all search results
	// that I get in response to a query on google.com will be localized to German, because
	// my browser sends an Accept-Language header value of 'de-de,de;q=0.8,en-us;q=0.5,en;q=0.3'.
	// On the other side, if I change my browser settings to send a value of 'en-us;q=0.8,en;q=0.3',
	// all my searches on google.de (the german Google page) will be localized English.
	// Thus, if you want to get the same results that you see when you search Google from
	// your browser, you must not only set the @const GOOGLE_TLD to your country specifiy TLD,
	// but also set the value below to be the same used by your browser!
	const HTTP_HEADER_ACCEPT_LANGUAGE = 'en-US;q=0.8,en;q=0.3';
	
	// For curl instances: Whether to allow Google to store cookies, or not.
	const ALLOW_GOOGLE_COOKIES = 0;
}