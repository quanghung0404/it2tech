<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */
jimport( 'joomla.application.component.view');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'xspf.php');

class ArtistsViewAlbum extends JViewLegacy
{
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
	
		$document = JFactory::getDocument();
		//print_r($document);die();
		$album		= $this->get( 'Data');
		$songs		= $this->get( 'Songs');
		
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
				$item->creator 		= $song->artist_name ? $song->artist_name : $album->artist_name ;
				$item->location 	= $song_path_complet;
				if($song->length) $item->duration 		= $song->length;
				if($album->image && $comp_params->get('loadimagesplayer') ) $item->image 		= $uri->base() . "images/albums/" . $album->image;
				$item->annotation 		= $song->artist_name ? $song->artist_name : $album->artist_name . " - " . $album->name;
				
				// loads item info into rss array
				$document->addItem( $item );
			}
		
		}
		
		$document->_styleSheets = array();
		
	}
}
?>
