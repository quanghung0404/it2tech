<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */
jimport( 'joomla.application.component.view');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'helpers.php');
//jomcomment integration
if(file_exists(JPATH_PLUGINS . DS . 'content' . DS . 'jom_comment_bot.php')) include_once( JPATH_PLUGINS . DS . 'content' . DS . 'jom_comment_bot.php' );

class ArtistsViewSongs extends JViewLegacy
{

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		
		$pathway	= $mainframe->getPathway();
		$document	= JFactory::getDocument();
		$uri 		= JFactory::getURI();
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		if($params->get('registerartistviews')) $this->get('RegisterHit');
		
		$artist		= $this->get( 'Data');
		$songs		= $this->get( 'SongsData');
		$related		= $this->get( 'Related');
		$also_related		= $this->get( 'AlsoRelated');
		$pagination = $this->get('Pagination');
		
		
		if($params->get('showartistcomments') ){ // show the comments 
			switch($params->get('commentsystem')){ 
				
				case 'jomcomment':
					 $this->assignRef('jomcomment',	jomcomment( 400000000 + $artist->id, "com_muscol"));  // 400000000 code for artist comments
					 break;
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
			$player = plgMuscolplayers::renderPlayer($all_songs,true, array(), JRoute::_('index.php?option=com_muscol&view=songs&id='.$artist->id.'&format=feed&type=xspf&limitstart='.JRequest::getVar('limitstart')) );	
		}
		else $player = "";
		//print_r($songs);
		$this->assignRef('artist',		$artist);
		$this->assignRef('songs',		$songs);
		$this->assignRef('related',		$related);
		$this->assignRef('also_related',		$also_related);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('player',		$player);
		$this->assignRef('params',		$params);
		
		$this->assign('action', 	$uri->toString());
		
		if($params->get('keywords') != ""){
			$document->setMetaData( 'keywords', $document->getMetaData( 'keywords' ) . ", " . $params->get('keywords') );
		}
		if($params->get('description') != ""){
			$document->setMetaData( 'description', $params->get('description') );
		}

				
		//creem els breadcrumbs
		
		$letters = MusColAlphabets::get_combined_array();
		
		$pathway->addItem($letters[$artist->letter], 'index.php?option=com_muscol&view=artists&letter='.$artist->letter);
		$pathway->addItem($artist->artist_name, 'index.php?option=com_muscol&view=artist&id='.$artist->id);
		$pathway->addItem(JText::_('Songs'), 'index.php?option=com_muscol&view=songs&id='.$artist->id);
		
		//creem el titol
		
		$document->setTitle( $artist->artist_name );
		
		//cridem els CSS
		$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/letter.css');
		$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/artist_detailed.css');
		$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/album.css');
		$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/comments.css');

		parent::display($tpl);
	}

}
?>
