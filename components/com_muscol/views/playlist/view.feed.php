<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */
jimport( 'joomla.application.component.view');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'xspf.php');
	
class ArtistsViewPlaylist extends JViewLegacy
{

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
	
		$document = JFactory::getDocument();
		//print_r($document);die();
		$playlist		= $this->get( 'PlaylistData');
		$songs		= $this->get( 'Data');
		
		$uri = JFactory::getURI();
		$comp_params =JComponentHelper::getParams( 'com_muscol' );
		
		$plugins = JPluginHelper::getPlugin('muscolplayers');
		//we take the first plugin in the list
		$plugin = JPluginHelper::getPlugin('muscolplayers',$plugins[0]->name);
		
		$params = new JRegistry( $plugin->params );
		
		$i = 0;
		$playlist->types = explode("," , $playlist->types );
		
		foreach ( $songs as $song )
		{
			switch($playlist->types[$i]){
				case 'v':
				if($song->video != ""){
					
					// load individual item creator class
					$item = new JFeedItem();
					$item->title 		= $song->name . " (" . JText::_('Video') . ")";
					$item->creator 		= $song->artist_name;
								
					$video_pieces = explode("?",$song->video) ;
					if(count($video_pieces) == 2 ){ // http://www.youtube.com/watch?v=6hzrDeceEKc
						$youtube_video_id = str_replace("v=", "", $video_pieces[1]);
					}
					else{ // http://www.youtube.com/v/6hzrDeceEKc OR 6hzrDeceEKc
						$youtube_video_id = str_replace("http://www.youtube.com/v/", "", $song->video);
					}
					$youtube_video_url = "http://www.youtube.com/v/" . $youtube_video_id ;
					
					$item->location 	= $youtube_video_url ;
					
					if($comp_params->get('loadimagesplayer')) $item->image 		= $youtube_video_url ;
					$item->annotation 	= $song->artist_name . " - " . $song->album_name;
					
					// loads item info into rss array
					$document->addItem( $item );
				}
				
				break;
				
				default:
				if($song->filename != ""){
					
					$song_path_complet = MusColHelper::getSongFileURL($song) ;
									
					// load individual item creator class
					$item = new JFeedItem();
					$item->title 		= $song->name;
					$item->creator 		= $song->artist_name;
					$item->location 	= $song_path_complet;
					$item->duration 	= $song->length;
					if($song->image && $comp_params->get('loadimagesplayer')) $item->image 		= $uri->base() . "images/albums/" . $song->image;
					$item->annotation 	= $song->artist_name . " - " . $song->album_name;
					
					// loads item info into rss array
					$document->addItem( $item );
				}
				break;
			}
			$i++;
				
		}
		
		$document->_styleSheets = array();
		
	}

}
?>
