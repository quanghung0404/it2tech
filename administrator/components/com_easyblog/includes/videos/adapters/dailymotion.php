<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

class EasyBlogVideoDailyMotion
{
	private function getCode( $url )
	{
		preg_match( '/\/video\/([a-z0-9A-Z]*)/i' , $url , $matches );

		if( !empty( $matches ) )
		{
			return $matches[1];
		}
		
		return false;
	}
	
	public function getEmbedHTML( $url , $width , $height )
	{
		$code	= $this->getCode( $url );

		if( $code )
		{
			$html 	= '<div class="legacy-video-container"><iframe frameborder="0" width="' . $width . '" height="' . $height . '" src="http://www.dailymotion.com/embed/video/' . $code . '"></iframe></div>';
			// $html	= '<object width="' . $width . '" height="' . $height . '">'
			// 		. '		<param name="movie" value="http://www.dailymotion.com/swf/video/' . $code . '?theme=none"></param>'
			// 		. '		<param name="allowFullScreen" value="true"></param>'
			// 		. '		<param name="allowScriptAccess" value="always"></param>'
			// 		. '		<param name="wmode" value="transparent"></param>'
			// 		. '		<embed type="application/x-shockwave-flash" src="http://www.dailymotion.com/swf/video/'. $code . '?theme=none" width="' . $width . '" height="' . $height . '" wmode="transparent" allowfullscreen="true" allowscriptaccess="always"></embed>'
			// 		. '</object>';
			return $html;
		}
		return false;
	}
}