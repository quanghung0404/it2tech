<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */
jimport( 'joomla.application.component.view');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'helpers.php');

class ArtistsViewPlaylist extends JViewLegacy
{

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		
		$pathway	= $mainframe->getPathway();
		$document	= JFactory::getDocument();
		$uri 		= JFactory::getURI();
		
		$playlist		= $this->get( 'PlaylistData');
		$songs		= $this->get( 'Data');
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		if($params->get('registerplaylistviews')) $this->get('RegisterHit');
		
		if($params->get('showplaylistcomments') ){ // show the comments 
			switch($params->get('commentsystem')){ 
				
				default:
					$comments		= $this->get( 'Comments');
					$this->assignRef('comments',		$comments);
				break;
			}
		}
	
		$plugins = JPluginHelper::getPlugin('muscolplayers');
		$plugin = $plugins[0];
		// we use only one player, the first in the plugin item list
		$plugin_ok = JPluginHelper::importPlugin('muscolplayers',$plugin->name); 
		
		$types = array();
		if(isset($playlist->types)) $types = explode("," , $playlist->types );

		$k = 0;
		
		$all_songs = array();
		
		for($i = 0, $n = count($songs); $i < $n; $i++){
			if( ($songs[$i]->filename != "" || $songs[$i]->video != "" ) && $plugin_ok){
				$songs[$i]->position_in_playlist = $k ;
				$songs[$i]->player = plgMuscolplayers::renderPlayer($songs[$i]);
				//print_r($songs[$i]->player);
				//$songs[$i]->playtype = $types[$i] ;

				$all_songs[] = $songs[$i];
				
				//new for song playing count statistics
				$js_songs[] = "songs_position_id[".$i."] = ".$songs[$i]->id.";";
				//new for HTML5 player
				$js_songs_url[] = "songs_position_url[".$i."] = '".MusColHelper::getSongFileURLslashes($songs[$i])."';" ;
							
				$k++;
			}
		}
		//die;
		if(count($js_songs)) $document->addScriptDeclaration( "var songs_position_id = new Array(); ".implode(" ",$js_songs) );
		if(count($js_songs_url)) $document->addScriptDeclaration( "var songs_position_url = new Array(); ".implode(" ",$js_songs_url) );
		
		if(!empty($all_songs)){
			// first parameter: array of songs. second parameter: true for multiple-songs-player
			$player = plgMuscolplayers::renderPlayer($all_songs,true, array(), JRoute::_('index.php?option=com_muscol&view=playlist&id='.$playlist->id.'&format=feed&type=xspf'), $types );	
		}
		else $player = "";
		//print_r($songs);
		$this->assignRef('playlist',		$playlist);
		$this->assignRef('songs',		$songs);
		$this->assignRef('params',		$params);
		$this->assignRef('player',		$player);
		
		$this->assign('uri', 	$uri);
		
		if($params->get('keywords') != ""){
			$document->setMetaData( 'keywords', $document->getMetaData( 'keywords' ) . ", " . $params->get('keywords') );
		}
		if($params->get('description') != ""){
			$document->setMetaData( 'description', $params->get('description') );
		}
		
		//breadcrumbs
		$pathway->addItem(JText::_('Playlists'), 'index.php?option=com_muscol&view=playlists');
		$pathway->addItem($playlist->title, 'index.php?option=com_muscol&view=playlist&id='.$playlist->id);
		
		//creem el titol
		
		$document->setTitle( $playlist->title );
		
		//cridem els CSS
		$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/letter.css');
		$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/playlist.css');
		$document->addScript( $uri->base() . 'components/com_muscol/assets/set_current_playlist.js');
		$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/comments.css');
		
		if(JRequest::getVar('layout') == "form"){
			
			$document->addStyleSheet($uri->base() . 'components/com_muscol/assets/form.css');
			
		}
		
		$user = JFactory::getUser(); 
		if($playlist->id){
			if( ($user->id != $playlist->user_id) ) $this->_layout = 'default' ; // we avoid direct access to the form template. this layour only can be reached by the function edit_playlist()
		}

		parent::display($tpl);
	}

}
?>
