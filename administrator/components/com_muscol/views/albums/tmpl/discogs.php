<?php defined('_JEXEC') or die('Restricted access'); 

?>
<?php
///////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Google API OAuth Authorization using the OAuthSimple library
//
// Author: Guido Schlabitz
// Email: guido.schlabitz@gmail.com
//
// This example uses the OAuthSimple library for PHP
// found here:  http://unitedHeroes.net/OAuthSimple
//
// For more information about the OAuth process for web applications
// accessing Google APIs, read this guide:
// http://code.google.com/apis/accounts/docs/OAuth_ref.html
//
//////////////////////////////////////////////////////////////////////
error_reporting(E_ALL);

require (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_muscol'.DS.'assets'.DS.'oauthsimple-master'.DS.'php'.DS.'OAuthSimple.php');

$uri = JFactory::getURI();

$oauthObject = new OAuthSimple('bZaNCebFXkBrvtmiuprD', 'BXopzkqgzQHEUysXloujDuiCodZctmsY');
$scope = 'http://api.discogs.com';

// As this is an example, I am not doing any error checking to keep 
// things simple.  Initialize the output in case we get stuck in
// the first step.
$output = 'Authorizing...';

// Fill in your API key/consumer key you received when you registered your 
// application with Google.
$signatures = array( 'consumer_key'     => 'bZaNCebFXkBrvtmiuprD',
                     'shared_secret'    => 'BXopzkqgzQHEUysXloujDuiCodZctmsY');

// In step 3, a verifier will be submitted.  If it's not there, we must be
// just starting out. Let's do step 1 then.
if (!isset($_GET['oauth_verifier'])) {
    ///////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    // Step 1: Get a Request Token
    //
    // Get a temporary request token to facilitate the user authorization 
    // in step 2. We make a request to the OAuthGetRequestToken endpoint,
    // submitting the scope of the access we need (in this case, all the 
    // user's calendars) and also tell Google where to go once the token
    // authorization on their side is finished.
    //
    $result = $oauthObject->sign(array(
        'path'      =>'http://api.discogs.com/oauth/request_token',
        'parameters'=> array(
            'scope'         => $scope,
            'oauth_callback'=> $uri->current()."?option=com_muscol&layout=discogs"),
        'signatures'=> $signatures));

    // The above object generates a simple URL that includes a signature, the 
    // needed parameters, and the web page that will handle our request.  I now
    // "load" that web page into a string variable.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $result['signed_url']);
    curl_setopt($ch, CURLOPT_USERAGENT, "Music Collection 2.4");
    $r = curl_exec($ch);
    curl_close($ch);

    // We parse the string for the request token and the matching token
    // secret. Again, I'm not handling any errors and just plough ahead 
    // assuming everything is hunky dory.
    parse_str($r, $returned_items);
    $request_token = $returned_items['oauth_token'];
    $request_token_secret = $returned_items['oauth_token_secret'];

    // We will need the request token and secret after the authorization.
    // Google will forward the request token, but not the secret.
    // Set a cookie, so the secret will be available once we return to this page.
    setcookie("oauth_token_secret", $request_token_secret, time()+3600);
    //
    //////////////////////////////////////////////////////////////////////
    
    ///////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    // Step 2: Authorize the Request Token
    //
    // Generate a URL for an authorization request, then redirect to that URL
    // so the user can authorize our access request.  The user could also deny
    // the request, so don't forget to add something to handle that case.
    $result = $oauthObject->sign(array(
        'path'      =>'http://www.discogs.com/oauth/authorize',
        'parameters'=> array(
            'oauth_token' => $request_token),
        'signatures'=> $signatures));

    // See you in a sec in step 3.
    header("Location:$result[signed_url]");
    exit;
    //////////////////////////////////////////////////////////////////////
}
else {
    ///////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    // Step 3: Exchange the Authorized Request Token for a Long-Term
    //         Access Token.
    //
    // We just returned from the user authorization process on Google's site.
    // The token returned is the same request token we got in step 1.  To 
    // sign this exchange request, we also need the request token secret that
    // we baked into a cookie earlier. 
    //

    // Fetch the cookie and amend our signature array with the request
    // token and secret.
    $signatures['oauth_secret'] = $_COOKIE['oauth_token_secret'];
    $signatures['oauth_token'] = $_GET['oauth_token'];
    
    // Build the request-URL...
    $result = $oauthObject->sign(array(
        'path'      => 'http://api.discogs.com/oauth/access_token',
        'parameters'=> array(
            'oauth_verifier' => $_GET['oauth_verifier'],
            'oauth_token'    => $_GET['oauth_token']),
        'signatures'=> $signatures));

    // ... and grab the resulting string again. 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $result['signed_url']);
    curl_setopt($ch, CURLOPT_USERAGENT, "Music Collection 2.4");
    $r = curl_exec($ch);

    // Voila, we've got a long-term access token.
    parse_str($r, $returned_items);        
    $access_token = $returned_items['oauth_token'];
    $access_token_secret = $returned_items['oauth_token_secret'];

    $params = JComponentHelper::getParams( 'com_muscol' );
    $params->set('oauth_token', $access_token);
    $params->set('access_token_secret', $access_token_secret);

    // Get a new database query instance
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    // Build the query
    $query->update('#__extensions AS a');
    $query->set('a.params = ' . $db->quote((string)$params));
    $query->where('a.element = "com_muscol"');

    // Execute the query
    $db->setQuery($query);
    $db->query();
    
    // We can use this long-term access token to request Google API data,
    // for example, a list of calendars. 
    // All Google API data requests will have to be signed just as before,
    // but we can now bypass the authorization process and use the long-term
    // access token you hopefully stored somewhere permanently.
    $signatures['oauth_token'] = $access_token;
    $signatures['oauth_secret'] = $access_token_secret;
    //////////////////////////////////////////////////////////////////////
    
    $mainframe = JFactory::getApplication();
    $link = JRoute::_('index.php?option=com_muscol');
    if($access_token && $access_token_secret){
        $msg = JText::_("DISCOGS_ACCESS_GRANTED");
        $type = null ;
    }
    else{
        $msg = JText::_("DISCOGS_ACCESS_DENIED");
        $type = "error" ;
    }
    $mainframe->redirect($link, $msg, $type);
}        
?>
