<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */
jimport( 'joomla.application.component.view');
	
class ArtistsViewSearch extends JViewLegacy
{

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$uri	= JFactory::getURI();
		
		if(JRequest::getVar("layout") == "fastnav"){
			
			$this->_layout = "fastnav";
			
			$albums		= $this->get( 'AlbumsFastnav');
			$this->assignRef('albums',		$albums);
			
		}
		else{
		
			$pathway	= $mainframe->getPathway();
			$document	= JFactory::getDocument();
			
			$search = JRequest::getVar('search');
			
			$params =JComponentHelper::getParams( 'com_muscol' );
			
			$genre_list		= $this->get( 'GenresData');
			$genre_id		= $this->get( 'GenreId');
			$searchword		= $this->get( 'Searchword');
			$tag_id		= $this->get( 'TagId');

			switch($search){
				case "songs":
	
					$songs		=  $this->get( 'SongsData');
					$artist_id		= $this->get( 'ArtistId');
					$pagination = $this->get('Pagination');
					
					$params =JComponentHelper::getParams( 'com_muscol' );
					
					$plugins = JPluginHelper::getPlugin('muscolplayers');
					$plugin = $plugins[0];
					// we use only one player, the first in the plugin item list
					$plugin_ok = JPluginHelper::importPlugin('muscolplayers',$plugin->name); 
					
					$all_songs = array();
					
					$k = 0;
					
					for($i = 0, $n = count($songs); $i < $n; $i++){
						if( ($songs[$i]->filename != "" || $songs[$i]->video != "" ) && $plugin_ok){
							$songs[$i]->position_in_playlist = $k ;
							
							$songs[$i]->player = plgMuscolplayers::renderPlayer($songs[$i]);
							
							$all_songs[] = $songs[$i];
							
							//new for song playing count statistics
							$js_songs[] = "songs_position_id[".$i."] = ".$songs[$i]->id.";";
							//new for HTML5 player
							$js_songs_url[] = "songs_position_url[".$i."] = '".MusColHelper::getSongFileURLslashes($songs[$i])."';" ;
						
							$k++;
						}
					}
					
					if(count($js_songs)) $document->addScriptDeclaration( "var songs_position_id = new Array(); ".implode(" ",$js_songs) );
					if(count($js_songs_url)) $document->addScriptDeclaration( "var songs_position_url = new Array(); ".implode(" ",$js_songs_url) );
					
					if(!empty($all_songs)){
						// first parameter: array of songs. second parameter: true for multiple-songs-player
						$player = plgMuscolplayers::renderPlayer($all_songs, true, array(), JRoute::_('index.php?searchword='.$searchword.'&artist_id='.$artist_id.'&genre_id='.$genre_id.'&tag_id='.$tag_id.'&option=com_muscol&view=search&search=songs&format=feed&type=xspf'.'&limitstart='.$pagination->limitstart) );
					}
					
					else $player = "";
	
					$this->assignRef('songs',		$songs);
					$this->assignRef('pagination', $pagination);
					$this->assignRef('player',		$player);
					
			
				break;
				
				case "albums":default:
					$albums		= $this->get( 'AlbumsData');
					$pagination = $this->get('Pagination');
					
					$this->assignRef('albums',		$albums);
					$this->assignRef('format_id',		$format_id);
					$currency = $params->get('currency') ;
					$this->assignRef('currency',	$currency);
					$this->assignRef('pagination', $pagination);
				
				break;
			}
			
			$this->assignRef('searchword',		$searchword);
			$this->assignRef('params',		$params);
			$this->assignRef('genre_list',		$genre_list);
			$this->assignRef('genre_id',		$genre_id);
		
			
			if($params->get('keywords') != ""){
				$document->setMetaData( 'keywords', $document->getMetaData( 'keywords' ) . ", " . $params->get('keywords') );
			}
			if($params->get('description') != ""){
				$document->setMetaData( 'description', $params->get('description') );
			}
		
			//creem els breadcrumbs
			
			$pathway->addItem(JText::_('Search'), 'index.php?option=com_muscol&view=search');
			
			//creem el titol
			
			$document->setTitle( JText::_('Search') );
			
			//cridem els CSS
			$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/letter.css');
			$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/artist_detailed.css');
			$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/album.css');
			
			//mirem el layout
			$this->_layout		= $this->get( 'Layout');
			
			if($search == "songs") $this->_layout = "songs";
		}

		parent::display($tpl);
	}
	

}
?>
