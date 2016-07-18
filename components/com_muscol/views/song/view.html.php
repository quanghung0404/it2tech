<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */
jimport( 'joomla.application.component.view');
jimport( 'joomla.plugin.helper');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'helpers.php');

class ArtistsViewSong extends JViewLegacy
{
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		
		$pathway	= $mainframe->getPathway();
		$document	= JFactory::getDocument();
		$dispatcher	= JDispatcher::getInstance();
		$params = JComponentHelper::getParams( 'com_muscol' );
		
		if($params->get('registersongviews')) $this->get('RegisterHit');
		
		$uri	= JFactory::getURI();
	
		$song		= $this->get( 'Data');
		$prev_song		= $this->get( 'PrevSongData');
		$next_song		= $this->get( 'NextSongData');
		$hits		= $this->get( 'Hits');
		
		$is_rated		= $this->get( 'RatedByThisUser');
		$average_rating		= $this->get( 'AverageRating');
		$num_rating		= $this->get( 'NumRating');
		
		if($params->get('showsongcomments') ){ // show the comments 
			switch($params->get('commentsystem')){ 
				
				default:
					$comments		= $this->get( 'Comments');
					$this->assignRef('comments',		$comments);
				break;
			}
		}
		
		if(JRequest::getVar('layout') != "form"){
			
			$plugins = JPluginHelper::getPlugin('muscolplayers');
			$plugin = $plugins[0];
			// we use only one player, the first in the plugin item list
			$plugin_ok = JPluginHelper::importPlugin('muscolplayers',$plugin->name); 
			
			if( ($song->filename != "" || $song->video != "") && $plugin_ok){
				$player = plgMuscolplayers::renderPlayer($song, false, array("force_show_player" => true));
				
				//new for song playing count statistics
				$js_songs[] = "songs_position_id[0] = ".$song->id.";";
				//new for HTML5 player
				$js_songs_url[] = "songs_position_url[0] = '".MusColHelper::getSongFileURLslashes($song)."';" ;
				if(count($js_songs)) $document->addScriptDeclaration( "var songs_position_id = new Array(); ".implode(" ",$js_songs) );
				if(count($js_songs_url)) $document->addScriptDeclaration( "var songs_position_url = new Array(); ".implode(" ",$js_songs_url) );
				
			}
			else $player = "";
			
			// Process the prepare content plugins
			$intro = new stdClass();
			
			$intro->text = $song->review;
			
			if($params->get('processcontentplugins')){
			
				$dispatcher	= JDispatcher::getInstance();
				$plug_params = new JRegistry('');
				
				JPluginHelper::importPlugin('content');
				$results = $dispatcher->trigger('onContentPrepare', array ('com_muscol.song', &$intro, &$plug_params, 0));
				
				$song->review = $intro->text ;
			}
		
		}
		else{

			$tags			= $this->get('TagsData');
			$this->assignRef('tags',		$tags);

			//tags system
			//http://welldonethings.com/tags/manager/v3
			
			$document->addScript('administrator/components/com_muscol/assets/tagmanager/bootstrap-tagmanager.js');
			$document->addStyleSheet('administrator/components/com_muscol/assets/tagmanager/bootstrap-tagmanager.css');

			$js= array();
			$prefill= array();

			foreach($tags as $tag){
				$js[] = '"'.$tag->tag_name.'"';
				if (in_array($tag->id,$song->tags_original)) $prefill[] = '"'.$tag->tag_name.'"';
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

		$this->assignRef('song',		$song);
		$this->assignRef('prev_song',		$prev_song);
		$this->assignRef('next_song',		$next_song);
		$this->assignRef('player',		$player);
		$this->assignRef('params',		$params);
		$this->assignRef('hits',		$hits);
		
		$this->assignRef('is_rated',		$is_rated);
		$this->assignRef('average_rating',		$average_rating);
		$this->assignRef('num_rating',		$num_rating);
		
		if($params->get('keywords') != ""){
			$document->setMetaData( 'keywords', $document->getMetaData( 'keywords' ) . ", " . $params->get('keywords') );
		}
		if($params->get('description') != ""){
			$document->setMetaData( 'description', $params->get('description') );
		}
		
		if(JRequest::getVar('layout') == "form"){
			$artists		= $this->get('ArtistsData');
			$genres			= $this->get('GenresData');
			
			$this->assignRef('artists',		$artists);
			$this->assignRef('genres',		$genres);
			
			$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/form.css');

		}
		
		//creem els breadcrumbs
		$letters = MusColAlphabets::get_combined_array();
		
		if($song->id){
			$pathway->addItem($letters[$song->letter], 'index.php?option=com_muscol&view=artists&letter='.$song->letter);
			$pathway->addItem($song->artist_name, 'index.php?option=com_muscol&view=artist&id='.$song->artist_id);
			$pathway->addItem($song->album_name, 'index.php?option=com_muscol&view=album&id='.$song->album_id);
			$pathway->addItem($song->name, 'index.php?option=com_muscol&view=song&id='.$song->id);
		}
		
		//creem el titol
		
		$document->setTitle( $song->name ." - ". $song->artist_name);
		
		//cridem els JavaScript
		$document->addScriptDeclaration('var star_icon_path = "'.JURI::root(true).'/components/com_muscol/assets/images/";');
		$document->addScript( $uri->base() . 'components/com_muscol/assets/stars.js');
		
		//cridem els CSS
		$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/letter.css');
		$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/song.css');
		$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/comments.css');
		
		//FaceBook Open Graph
		$config = JFactory::getConfig();
		
		$document->addCustomTag( '<meta property="og:title" content="'.$song->name . " - " .  $song->artist_name.'" />' ) ;
		$document->addCustomTag( '<meta property="og:type" content="music.song" />' ) ;
		$document->addCustomTag( '<meta property="og:url" content="'.urlencode($uri->current()).'" />' ) ;
		$document->addCustomTag( '<meta property="og:image" content="'.$uri->base() . "images/albums/". $song->image.'" />' ) ;
		$document->addCustomTag( '<meta property="og:site_name" content="'.$config->get( 'sitename' ).'" />' ) ;
		
		parent::display($tpl);
	}
}
?>
