<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
 
class SongsViewSong extends JViewLegacy
{

	var $genres_array = null;
	
	function display($tpl = null)
	{
		//get the album
		$song			= $this->get('Data');
		$artists		= $this->get('ArtistsData');
		$artist_from_album	= $this->get('ArtistFromAlbum');
		$genres			= $this->get('GenresData');
		$tags			= $this->get('TagsData');
		
		//cridem el CSS
		$document	= JFactory::getDocument();
		$document->addStyleSheet('components/com_muscol/assets/albums.css');
		
		$isNew		= ($song->id < 1);

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'Song' ).': <small><small>[ ' . $text.' ]</small></small>','type' );
		JToolBarHelper::apply();
		JToolBarHelper::save();
		
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		
		// push data into the template
		$this->assignRef('song',		$song);
		$this->assignRef('artists',		$artists);
		$this->assignRef('artist_from_album',		$artist_from_album);
		$this->assignRef('genres',		$genres);
		$this->assignRef('tags',		$tags);

		JHtml::_('jquery.framework');
		
		JHtml::_('formbehavior.chosen', '.chzn-select');

		//tags system
		//http://welldonethings.com/tags/manager/v3
		$document->addScript('components/com_muscol/assets/tagmanager/bootstrap-tagmanager.js');
		$document->addStyleSheet('components/com_muscol/assets/tagmanager/bootstrap-tagmanager.css');

		$js= array();
		$prefill= array();

		foreach($tags as $tag){
			$js[] = '"'.$tag->tag_name.'"';
			if (in_array($tag->id,$song->tags)) $prefill[] = '"'.$tag->tag_name.'"';
		} 
		$js = implode(",", $js);
		$prefill = implode(",", $prefill);

		$document->addScriptDeclaration('

			jQuery(document).ready(function (){ 

			 jQuery("#tags").tagsManager({
			      prefilled: ['.$prefill.'],
			      typeahead: true,
        		  //typeaheadAjaxSource: "'.JRoute::_('index.php?option=com_muscol&controller=tags&task=typeahead', false).'",
        		  //typeaheadAjaxPolling: true,
        		  typeaheadSource: ['.$js.'],
        		  typeaheadAjaxPolling: false,
			    });
			 
		     });');

		parent::display($tpl);
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
		if( $id == $this->song->genre_id ) $selected = "selected";
            
		return "<option value='".$id."' $selected >".$indent.$name."</option>";	
	}
}