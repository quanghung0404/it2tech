<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */
jimport( 'joomla.application.component.view');

class ArtistsViewArtist extends JViewLegacy
{
	
	public $_path = array(
		'template' => array(),
		'helper' => array()
	);
	
	public $_layout = 'default';

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		
		$uri	= JFactory::getURI();
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		if($params->get('registerartistviews')) $this->get('RegisterHit');
		
		if(JRequest::getVar("layout") == "fastnav"){
			
			$this->_layout = "fastnav";
			
			$albums		= $this->get( 'AlbumsFastnav');
			$this->assignRef('albums',		$albums);
			
		}
		else{
		
			$pathway	= $mainframe->getPathway();
			$document	= JFactory::getDocument();
			
			$artist		= $this->get( 'Data');
			
			if($artist->id){
				$albums		= $this->get( 'AlbumsData');
				$related		= $this->get( 'Related');
				$also_related		= $this->get( 'AlsoRelated');
				$hits		= $this->get( 'Hits');
				
				if($params->get('showartistcomments') ){ // show the comments 
					switch($params->get('commentsystem')){ 
						
						default:
							$comments		= $this->get( 'Comments');
							$this->assignRef('comments',		$comments);
						break;
					}
				}
			
	
				$this->assignRef('albums',		$albums);
				
				$this->assignRef('also_related',		$also_related);
				
				$currency = $params->get('currency') ;
				$this->assignRef('currency',	$currency);
				$this->assignRef('hits',		$hits);
				
				// Process the prepare content plugins
				$intro = new stdClass();
				
				$intro->text = $artist->review;
				
				if($params->get('processcontentplugins')){
				
					$dispatcher	= JDispatcher::getInstance();
					$plug_params = new JRegistry('');
					
					JPluginHelper::importPlugin('content');
					$results = $dispatcher->trigger('onContentPrepare', array ('com_muscol.artist', &$intro, &$plug_params, 0));
					
					$artist->review = $intro->text ;
				}
			
			}
			
			$this->assignRef('params',		$params);
			
			if(JRequest::getVar('layout') == "form"){
				$related		= $this->get('ArtistsData');
				$tags			= $this->get('TagsData');
				$genres			= $this->get('GenresData');
				
				$artist->related = explode(",",$artist->related);
				
				$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/form.css');
				
				$this->assignRef('genres',		$genres);
				$this->assignRef('tags',		$tags);

				//tags system
				//http://welldonethings.com/tags/manager/v3
				
				$document->addScript('administrator/components/com_muscol/assets/tagmanager/bootstrap-tagmanager.js');
				$document->addStyleSheet('administrator/components/com_muscol/assets/tagmanager/bootstrap-tagmanager.css');

				$js= array();
				$prefill= array();

				foreach($tags as $tag){
					$js[] = '"'.$tag->tag_name.'"';
					if (in_array($tag->id,$artist->tags_original)) $prefill[] = '"'.$tag->tag_name.'"';
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

			$this->assignRef('artist',		$artist);
			$this->assignRef('related',		$related);
			
			//meta keywords
			if($artist->metakeywords){
				$document->setMetaData( 'keywords', $artist->metakeywords ) ;
			}
			elseif($params->get('keywords') != ""){
				$document->setMetaData( 'keywords', $document->getMetaData( 'keywords' ) . ", " . $params->get('keywords') );
			}
			
			// meta description
			if($artist->metadescription){
				$document->setMetaData( 'description', $artist->metadescription ) ;
			}
			elseif($params->get('description') != ""){
				$document->setMetaData( 'description', $params->get('description') );
			}
			
			//other metadata
			
			$document->setMetaData( 'title', $artist->artist_name ) ;
			$document->addHeadLink( $uri->base() . MusColHelper::getArtistThumbnailSrc( $artist->picture, '', '110' ) , 'image_src');
			
			if($artist->id){
				//creem els breadcrumbs
				
				$letters = MusColAlphabets::get_combined_array();
				
				$pathway->addItem($letters[$artist->letter], 'index.php?option=com_muscol&view=artists&letter='.$artist->letter);
				$pathway->addItem($artist->artist_name, 'index.php?option=com_muscol&view=artist&id='.$artist->id);
				
				//creem el titol
				
				$document->setTitle( $artist->artist_name );
			
			}
			else{
				$document->setTitle( JText::_('NEW_ARTIST') );
			}
			
			//cridem els CSS
			$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/letter.css');
			$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/artist_detailed.css');
			$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/album.css');
			$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/comments.css');
			
			//mirem el layout
			$this->_layout		= $this->get( 'Layout');
		
		}

		parent::display($tpl);
	}
	
	function change_layout(){
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		$artist		= $this->get( 'Data');	
		$base_link = 'index.php?option=com_muscol&view=artist&id=' . $artist->id . $itemid;
		
		$link= JRoute::_( $base_link );
		
		switch($this->_layout){
			
			case "grid":
				$icon_list = "show_list.gif";
				$icon_grid = "show_grid_over.gif";
				$icon_coverflow = "show_coverflow.gif";
				$link_list = ' href="'.JRoute::_( $base_link . '&layout=default' ).'"';
				$link_coverflow = ' href="'.JRoute::_( $base_link . '&layout=detailed' ).'"';
				$link_grid = ' ';
				break;
			case "detailed":
				$icon_list = "show_list.gif";
				$icon_grid = "show_grid.gif";
				$icon_coverflow = "show_coverflow_over.gif";
				$link_grid = ' href="'.JRoute::_( $base_link . '&layout=grid' ).'"';
				$link_coverflow = ' ';
				$link_list = ' href="'.JRoute::_( $base_link . '&layout=default' ).'"';
				break;
			default:
				$icon_list = "show_list_over.gif";
				$icon_grid = "show_grid.gif";
				$icon_coverflow = "show_coverflow.gif";
				$link_grid = ' href="'.JRoute::_( $base_link . '&layout=grid' ).'"';
				$link_coverflow = ' href="'.JRoute::_( $base_link . '&layout=detailed' ).'"';
				$link_list = ' ';
				break;
		}
		
		$path = 'components/com_muscol/assets/images/';
		
		return '<a class="view_mode" '.$link_list.'><img src="'.$path.$icon_list.'" title="'.JText::_('LIST_VIEW').'" alt="'.JText::_('LIST_VIEW').'"/></a><a class="view_mode" '.$link_grid.'><img src="'.$path.$icon_grid.'" title="'.JText::_('GRID_VIEW').'" alt="'.JText::_('GRID_VIEW').'"/></a><a class="view_mode" '.$link_coverflow.'><img src="'.$path.$icon_coverflow.'" title="'.JText::_('DETAILED_VIEW').'" alt="'.JText::_('DETAILED_VIEW').'"/></a>';	
	}
	
	function show_genre_tree($genres,$level){
		
		$return = "";
		
		for($i = 0; $i < count($genres); $i++){
			$return .= $this->render_option($genres[$i]->id,$genres[$i]->genre_name,$level);
			$level ++;
			if(!empty($genres[$i]->sons)){
				$return .= 	$this->show_genre_tree($genres[$i]->sons,$level);
			}
			$level --;
		}
		//echo $return;
		return $return;
		
	}
	
	function render_option($id, $name, $level){
		$indent = "";
		
		for($i = 0; $i < $level; $i++){
			$indent .= "&nbsp;&nbsp;";	
		}
		
		$selected = ""; 
		if( $id == $this->artist->genre_id ) $selected = "selected";
            
		return "<option value='".$id."' $selected >".$indent.$name."</option>";	
	}
	
}
?>
