<?php

/** 
 * @version		2.0.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */
class MusColBookmarks{
	
	static function show_bookmarks(){
		
		$uri = JFactory::getURI();
		$document	= JFactory::getDocument();
		
		$url = urlencode($uri->toString());
		$title = $document->getTitle();
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		switch($params->get('bookmarksystem')){
			case 'addthis':
			
			$return = MusColBookmarks::bookmark_addthis() ;
			
			break;
			
			default:

			$bookmarks[0] = new stdClass();
			$bookmarks[1] = new stdClass();
			$bookmarks[2] = new stdClass();
			$bookmarks[3] = new stdClass();
			$bookmarks[4] = new stdClass();
			$bookmarks[5] = new stdClass();
			$bookmarks[6] = new stdClass();
			$bookmarks[7] = new stdClass();
			$bookmarks[8] = new stdClass();
			$bookmarks[9] = new stdClass();
			$bookmarks[10] = new stdClass();
			$bookmarks[11] = new stdClass();
			
			// FACEBOOK
			$bookmarks[0]->name = "Facebook" ;
			$bookmarks[0]->icon = "facebook.png" ;
			$bookmarks[0]->linkurl = "http://www.facebook.com/sharer.php?u=" . $url ."&t=" . $title;
			
			// DEL.ICIO.US
			$bookmarks[1]->name = "del.icio.us" ;
			$bookmarks[1]->icon = "delicious.png" ;
			$bookmarks[1]->linkurl = "http://del.icio.us/post?url=" . $url ."&title=" . $title;
			
			// STUMBLEUPON
			$bookmarks[2]->name = "StumbleUpon" ;
			$bookmarks[2]->icon = "stumble.png" ;
			$bookmarks[2]->linkurl = "http://www.stumbleupon.com/submit?url=" . $url ."&title=" . $title;
			
			// DIGG
			$bookmarks[3]->name = "Digg" ;
			$bookmarks[3]->icon = "digg.png" ;
			$bookmarks[3]->linkurl = "http://digg.com/submit?phase=2&url=" . $url ."&title=" . $title;
			
			// TECHNORATI
			$bookmarks[4]->name = "Technorati" ;
			$bookmarks[4]->icon = "technorati.png" ;
			$bookmarks[4]->linkurl = "http://technorati.com/faves?add=" . $url ;
			
			// NEWSVINE
			$bookmarks[5]->name = "NewsVine" ;
			$bookmarks[5]->icon = "newsvine.png" ;
			$bookmarks[5]->linkurl = "http://www.newsvine.com/_tools/seed&save?u=" . $url ."&h=" . $title;
			
			// REDDIT
			$bookmarks[6]->name = "Reddit" ;
			$bookmarks[6]->icon = "reddit.png" ;
			$bookmarks[6]->linkurl = "http://reddit.com/submit?url=" . $url ."&title=" . $title;
			
			// GOOGLE
			$bookmarks[7]->name = "Google" ;
			$bookmarks[7]->icon = "google.png" ;
			$bookmarks[7]->linkurl = "http://www.google.com/bookmarks/mark?op=edit&bkmk=" . $url ."&title=" . $title;
			
			// LINKEDIN
			$bookmarks[8]->name = "LinkedIn" ;
			$bookmarks[8]->icon = "linkedin.png" ;
			$bookmarks[8]->linkurl = "http://www.linkedin.com/shareArticle?mini=true&url=" . $url ."&title=" . $title;
			
			// MYSPACE
			$bookmarks[9]->name = "MySpace" ;
			$bookmarks[9]->icon = "myspace.png" ;
			$bookmarks[9]->linkurl = "http://www.myspace.com/Modules/PostTo/Pages/?l=3&u=" . $url ."&c=" . $title;
			
			// MIXX
			$bookmarks[10]->name = "Mixx" ;
			$bookmarks[10]->icon = "mixx.png" ;
			$bookmarks[10]->linkurl = "http://www.mixx.com/submit?page_url=" . $url ;
			
			// FURL
			$bookmarks[11]->name = "Furl" ;
			$bookmarks[11]->icon = "furl.png" ;
			$bookmarks[11]->linkurl = "http://furl.net/storelt.jsp?url=" . $url ."&t=" . $title;;
			
			
			
			$return = array();
			foreach($bookmarks as $bookmark){
				$return[] = "<a href='".$bookmark->linkurl."' target='_blank' title='".$bookmark->name."'>".JHTML::image('components/com_muscol/assets/images/bookmarks/' . $bookmark->icon , $bookmark->name )."</a>" ;
			}
			
			$return = implode(" ", $return);
		break;
		}
		
		switch(JRequest::getVar('view')){
			case "album":
				$width_fb = "412";
				$height_fb = "120";
				break;
			case "artist": case 'songs': default:
				$width_fb = "220";
				$height_fb = "120";
			break;
		}
		
		if($params->get('showtwitter'))
		
		$return .= '<br /><br /><a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" >Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>' ;
		
		if($params->get('showfacebook'))
		
		$return .= '<br /><br /><iframe src="http://www.facebook.com/plugins/like.php?href='.$url.'&amp;layout=standard&amp;show_faces=true&amp;width='.$width_fb.'&amp;action=like&amp;font&amp;colorscheme=light&amp;height='.$height_fb.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.$width_fb.'px; height:'.$height_fb.'px;" allowTransparency="true"></iframe>' ;

		return $return ;

	}
	
	static function bookmark_addthis(){
		return '<!-- AddThis Button BEGIN -->
<a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=250&amp;username=xa-4b795fb56f4d5326"><img src="http://s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125" height="16" alt="Bookmark and Share" style="border:0"/></a><script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4b795fb56f4d5326"></script>
<!-- AddThis Button END -->' ;

	}
	
}