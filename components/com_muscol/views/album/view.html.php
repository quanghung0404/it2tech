<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */
jimport( 'joomla.application.component.view');

class ArtistsViewAlbum extends JViewLegacy
{
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		
		$pathway	= $mainframe->getPathway();
		$document	= JFactory::getDocument();
		$uri	= JFactory::getURI();
		
		$album		= $this->get( 'Data');
		$params = JComponentHelper::getParams( 'com_muscol' );
		
		if($params->get('registeralbumviews')) $this->get('RegisterHit');
		
		if($album->id){
			$compilation	= $this->get( 'CompilationAlbums');
			$songs		= $this->get( 'Songs');
			$is_rated		= $this->get( 'RatedByThisUser');
			$average_rating		= $this->get( 'AverageRating');
			$hits		= $this->get( 'Hits');
			$num_rating		= $this->get( 'NumRating');
			
			$prev_album		= $this->get( 'PrevAlbumData');
			$next_album		= $this->get( 'NextAlbumData');
			
			
			if($params->get('showalbumcomments') ){ // show the comments 
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
			
			$k = 0;
			
			$all_songs = array();
			
			if(JRequest::getVar('layout') == "fastnav") $extra_options = array("player_fastnav_song" => true) ;
			else $extra_options = array();
			
			if(JRequest::getVar('layout') != "form"){
				
				for($i = 0, $n = count($songs); $i < $n; $i++){
					if( ($songs[$i]->filename != "" || $songs[$i]->video != "" ) && $plugin_ok){
						
						//there is SONG
						if( $songs[$i]->filename != ""){
							$songs[$i]->position_in_playlist = $k ;
							$songs[$i]->player = plgMuscolplayers::renderPlayer($songs[$i], false, $extra_options);
							//$all_songs[] = $songs[$i];
							
							//new for song playing count statistics
							$js_songs[] = "songs_position_id[".$k."] = ".$songs[$i]->id.";";
							//new for HTML5 player
							$js_songs_url[] = "songs_position_url[".$k."] = '".MusColHelper::getSongFileURLslashes($songs[$i])."';" ;

							$k++;
							
						}
						//there is VIDEO
						elseif( $songs[$i]->filename == "" && $songs[$i]->video != ""){
							//$songs[$i]->position_in_playlist = $k ;
							$songs[$i]->player = plgMuscolplayers::renderPlayer($songs[$i], false, $extra_options);
							
						}
						
						$all_songs[] = $songs[$i];
						
						
					}
				}
				
				if(count($js_songs)) $document->addScriptDeclaration( "var songs_position_id = new Array(); ".implode(" ",$js_songs) );
				if(count($js_songs_url)) $document->addScriptDeclaration( "var songs_position_url = new Array(); ".implode(" ",$js_songs_url) );
				
				if(!empty($all_songs)){
					
					// first parameter: array of songs. second parameter: true for multiple-songs-player
					$player = plgMuscolplayers::renderPlayer($all_songs, true, array("album_id" => $album->id), JRoute::_('index.php?option=com_muscol&view=album&id='.$album->id.'&format=feed&type=xspf') );
					
				}
				else $player = "";
			
			}
			
			// Process the prepare content plugins
			$intro = new stdClass();
			
			$intro->text = $album->review;
			
			if($params->get('processcontentplugins')){
			
				$dispatcher	= JDispatcher::getInstance();
				$plug_params = new JRegistry('');
				
				JPluginHelper::importPlugin('content');
				$results = $dispatcher->trigger('onContentPrepare', array ('com_muscol.album', &$intro, &$plug_params, 0));
				
				$album->review = $intro->text ;
			}
		
		}// end if album->id
	
		$this->assignRef('album',		$album);
		$this->assignRef('compilation',		$compilation);
		
		$this->assignRef('prev_album',		$prev_album);
		$this->assignRef('next_album',		$next_album);
		$this->assignRef('is_rated',		$is_rated);
		$this->assignRef('average_rating',		$average_rating);
		$this->assignRef('num_rating',		$num_rating);
		$this->assignRef('player',		$player);
		$this->assignRef('params',		$params);
		$currency = $params->get('currency') ;
		$this->assignRef('currency',	$currency);
		$this->assignRef('hits',		$hits);
		
		if(JRequest::getVar('layout') == "form"){
			$artists		= $this->get('ArtistsData');
			$formats		= $this->get('FormatsData');
			$types			= $this->get('TypesData');
			$genres			= $this->get('GenresData');
			$tags			= $this->get('TagsData');
			$songs		= $this->get( 'Songs');
			$albums		= $this->get('AlbumsData');
			
			
			$this->assignRef('artists',		$artists);
			$this->assignRef('formats',		$formats);
			$this->assignRef('types',		$types);
			$this->assignRef('genres',		$genres);
			$this->assignRef('tags',		$tags);
			$this->assignRef('albums',		$albums);
			
			$document->addStyleSheet($uri->base() . 'components/com_muscol/assets/form.css');
			
			$document->addScript( $uri->base() . 'components/com_muscol/assets/songs.js');

			//tags system
			//http://welldonethings.com/tags/manager/v3
			
			$document->addScript('administrator/components/com_muscol/assets/tagmanager/bootstrap-tagmanager.js');
			$document->addStyleSheet('administrator/components/com_muscol/assets/tagmanager/bootstrap-tagmanager.css');

			$js= array();
			$prefill= array();

			foreach($tags as $tag){
				$js[] = '"'.$tag->tag_name.'"';
				if (in_array($tag->id,$album->tags_original)) $prefill[] = '"'.$tag->tag_name.'"';
			} 
			$js = implode(",", $js);
			$prefill = implode(",", $prefill);

			$document->addScriptDeclaration('

				jQuery(document).ready(function (){ 

				 jQuery("#tags").tagsManager({
				      prefilled: ['.$prefill.'],
				      typeahead: true,
	        		  typeaheadSource: ['.$js.'],
	        		  typeaheadAjaxPolling: false,
				    });
				 
			     });');
				
			}
		
		$this->assignRef('songs',		$songs);
		
		//meta keywords
		if($album->metakeywords){
			$document->setMetaData( 'keywords', $album->metakeywords ) ;
		}
		elseif($params->get('keywords') != ""){
			$document->setMetaData( 'keywords', $document->getMetaData( 'keywords' ) . ", " . $params->get('keywords') );
		}
		
		// meta description
		if($album->metadescription){
			$document->setMetaData( 'description', $album->metadescription ) ;
		}
		elseif($params->get('description') != ""){
			$document->setMetaData( 'description', $params->get('description') );
		}
		
		//other metadata
		
		$document->setMetaData( 'title', $album->name . " - " .  $album->artist_name ) ;
		$document->addHeadLink( $uri->base() . MusColHelper::getThumbnailSrc( $album->image, '110', '110' ) , 'image_src');
		
		//FaceBook Open Graph
		$config = JFactory::getConfig();
		
		$document->addCustomTag( '<meta property="og:title" content="'.$album->name . " - " .  $album->artist_name.'" />' ) ;
		$document->addCustomTag( '<meta property="og:type" content="music.album" />' ) ;
		$document->addCustomTag( '<meta property="og:url" content="'.urlencode($uri->current()).'" />' ) ;
		$document->addCustomTag( '<meta property="og:image" content="'.$uri->base() . "images/albums/". $album->image.'" />' ) ;
		$document->addCustomTag( '<meta property="og:site_name" content="'.$config->get( 'sitename' ).'" />' ) ;
		
		if($album->id){
			//creem els breadcrumbs
			
			$letters = MusColAlphabets::get_combined_array();
			
			$pathway->addItem($letters[$album->letter], 'index.php?option=com_muscol&view=artists&letter='.$album->letter);
			$pathway->addItem($album->artist_name, 'index.php?option=com_muscol&view=artist&id='.$album->artist_id);
			$pathway->addItem($album->name, 'index.php?option=com_muscol&view=album&id='.$album->id);
			
			//creem el titol
			
			$document->setTitle( $album->name . " - " .  $album->artist_name);
					
			//cridem els JavaScript
			$document->addScriptDeclaration('var star_icon_path = "'.JURI::root(true).'/components/com_muscol/assets/images/";');
			$document->addScript( $uri->base() . 'components/com_muscol/assets/stars.js');
		
		}
		else{
			$document->setTitle( JText::_('NEW_ALBUM') );
		}
		
		//cridem els CSS
		$document->addStyleSheet($uri->base() . 'components/com_muscol/assets/letter.css');
		$document->addStyleSheet($uri->base() . 'components/com_muscol/assets/album.css');
		$document->addStyleSheet($uri->base() . 'components/com_muscol/assets/artist_detailed.css');
		$document->addStyleSheet($uri->base() . 'components/com_muscol/assets/comments.css');
		

		parent::display($tpl);
	}

}
?>
