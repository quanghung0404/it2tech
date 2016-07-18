<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogPingomatic extends EasyBlog
{
	/**
	 * Sends a ping to pingomatic servers
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function ping(EasyBlogPost &$post, $debug = false)
	{
		// If this is disabled, don't do anything
		if (!$this->config->get('main_pingomatic')) {
			return false;
		}

		// Get the title of the blog post
		$title = EB::string()->escape($post->title);

		// Get the permalink to the post
		$link = $post->getExternalPermalink();
		$link = urlencode($link);

		// Construct the xml content to send to pingomatic
	    $content ='<?xml version="1.0"?>'.
	        '<methodCall>'.
	        ' <methodName>weblogUpdates.ping</methodName>'.
	        '  <params>'.
	        '   <param>'.
	        '    <value>'.$title.'</value>'.
	        '   </param>'.
	        '   <param>'.
	        '    <value>'.$link.'</value>'.
	        '   </param>'.
	        '  </params>'.
	        '</methodCall>';

	    $headers="POST / HTTP/1.0\r\n".
	    "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1) Gecko/20090624 Firefox/3.5 (.NET CLR 3.5.30729)\r\n".
	    "Host: rpc.pingomatic.com\r\n".
	    "Content-Type: text/xml\r\n".
	    "Content-length: ".strlen($content);

	    $request=$headers."\r\n\r\n".$content;
	    $response = "";
	    $fs=fsockopen('rpc.pingomatic.com',80, $errno, $errstr);
	    if ($fs) {
	        fwrite ($fs, $request);
	        while (!feof($fs)) $response .= fgets($fs);
	        if ($debug) echo "<xmp>".$response."</xmp>";
	        fclose ($fs);
	        preg_match_all("/<(name|value|boolean|string)>(.*)<\/(name|value|boolean|string)>/U",$response,$ar, PREG_PATTERN_ORDER);
	        for($i=0;$i<count($ar[2]);$i++) $ar[2][$i]= strip_tags($ar[2][$i]);
	        return array('status'=> ( $ar[2][1]==1 ? 'ko' : 'ok' ), 'msg'=>$ar[2][3] );
	    } else {
	        if ($debug) echo "<xmp>".$errstr." (".$errno.")</xmp>";
	        return array('status'=>'ko', 'msg'=>$errstr." (".$errno.")");
	    }
	}
}
