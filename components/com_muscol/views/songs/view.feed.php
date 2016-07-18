<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */
jimport( 'joomla.application.component.view');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'xspf.php');
	
class ArtistsViewSongs extends JViewLegacy
{

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
	
		$document = JFactory::getDocument();
		//print_r($document);die();
		$artist		= $this->get( 'Data');
		$songs		= $this->get( 'SongsData');
		
		$uri = JFactory::getURI();
		$comp_params =JComponentHelper::getParams( 'com_muscol' );
		
		$plugins = JPluginHelper::getPlugin('muscolplayers');
		//we take the first plugin in the list
		$plugin = JPluginHelper::getPlugin('muscolplayers',$plugins[0]->name);
		
		$params = new JRegistry( $plugin->params );
		
		
		foreach ( $songs as $song )
		{
			if($song->filename != ""){
				
				$song_path_complet = MusColHelper::getSongFileURL($song) ;
								
				// load individual item creator class
				$item = new JFeedItem();
				$item->title 		= $song->name;
				$item->creator 		= $artist->artist_name;
				$item->location 	= $song_path_complet;
				if($song->length) $item->duration 	= $song->length;
				if($song->image && $comp_params->get('loadimagesplayer') ) $item->image 		= $uri->base() . "images/albums/" . $song->image;
				$item->annotation 	= $artist->artist_name . " - " . $song->album_name;
				
				// loads item info into rss array
				$document->addItem( $item );
			}
		}
		
		$document->_styleSheets = array();
		
	}

}
?>
