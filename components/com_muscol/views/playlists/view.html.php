<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */
jimport( 'joomla.application.component.view');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'helpers.php');
	
class ArtistsViewPlaylists extends JViewLegacy
{

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		
		$pathway	= $mainframe->getPathway();
		$document	= JFactory::getDocument();
		$uri 		= JFactory::getURI();
		
		$playlists		= $this->get( 'Data');
		$on_the_go		= $this->get( 'Playlist_from_session');
		
		$playlists_others		= $this->get( 'DataOthers');
		$pagination = $this->get('Pagination');

		
		$params =JComponentHelper::getParams( 'com_muscol' );
	/*
		$plugins = JPluginHelper::getPlugin('muscolplayers');
		$plugin = $plugins[0];
		// we use only one player, the first in the plugin item list
		$plugin_ok = JPluginHelper::importPlugin('muscolplayers',$plugin->name); 
		
		$k = 0;
		
		$all_songs = array();
		for($i = 0, $n = count($songs); $i < $n; $i++){
			if($songs[$i]->filename != "" && $plugin_ok){
				$songs[$i]->position_in_playlist = $k ;
				$songs[$i]->player = plgMuscolplayers::renderPlayer($songs[$i]);
				$all_songs[] = $songs[$i];
				$k++;
			}
		}

		if(!empty($all_songs)){
			// first parameter: array of songs. second parameter: true for multiple-songs-player
			$player = plgMuscolplayers::renderPlayer($all_songs,true, array(), JRoute::_('index.php?option=com_muscol&view=playlist&id='.$playlist->id.'&format=feed&type=xspf') );	
		}
		else $player = "";
		//print_r($songs);
		*/
		$this->assignRef('playlists',		$playlists);
		$this->assignRef('on_the_go',		$on_the_go);
		
		$this->assignRef('playlists_others',		$playlists_others);
		$this->assignRef('pagination',		$pagination);
		
		$this->assign('action', 	$uri->toString());
		
		$this->assignRef('params',		$params);
		
		if($params->get('keywords') != ""){
			$document->setMetaData( 'keywords', $document->getMetaData( 'keywords' ) . ", " . $params->get('keywords') );
		}
		if($params->get('description') != ""){
			$document->setMetaData( 'description', $params->get('description') );
		}
		
		//breadcrumbs
		$pathway->addItem(JText::_('Playlists'), 'index.php?option=com_muscol&view=playlists');
		
		//creem el titol
		
		$document->setTitle( JText::_('Playlists') );
		
		//cridem els CSS
		$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/letter.css');
		$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/playlist.css');
		
		$document->addScript( $uri->base() . 'components/com_muscol/assets/set_current_playlist.js');

		parent::display($tpl);
	}

}
?>
