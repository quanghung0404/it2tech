<?php
/**
 * @version    $Id: jsn_is_httprequest.php 17549 2012-10-27 04:23:40Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JSNISHTTPRequest Class
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

class JSNISHTTPRequest
{
    private $_url;

    // constructor
    public function JSNISHTTPRequest($url = '')
    {
        $this->_url = $url;
    }

    // download URL to string
    public function DownloadToString()
    {
    	if ($this->_url == '') return false;
    	
    	try
    	{
    		$content = JSNUtilsHttp::get($this->_url);		
    		return $content['body'];
    	}
    	catch(Exception $e)
    	{
    		echo 'Message:' . $e->getMessage();
    		return false;
    	}    	
    }
}
