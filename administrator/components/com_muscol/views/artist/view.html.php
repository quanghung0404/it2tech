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

class ArtistsViewArtist extends JViewLegacy
{

	function display($tpl = null)
	{
		//cridem el CSS
		$document	= JFactory::getDocument();
		$document->addStyleSheet('components/com_muscol/assets/albums.css');
		
		$artist		= $this->get('Data');
		$related		= $this->get('ArtistsData');
		$tags			= $this->get('TagsData');
		$genres			= $this->get('GenresData');
		
		$isNew		= ($artist->id < 1);

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'Artist' ).': <small><small>[ ' . $text.' ]</small></small>','artist' );
		JToolBarHelper::save();
		
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::apply();
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}

		$this->assignRef('artist',		$artist);
		$this->assignRef('related',		$related);
		$this->assignRef('tags',		$tags);
		$this->assignRef('genres',		$genres);
		
		JHtml::_('formbehavior.chosen', '.chzn-select');

		//tags system
		//http://welldonethings.com/tags/manager/v3
		$document->addScript('components/com_muscol/assets/tagmanager/bootstrap-tagmanager.js');
		$document->addStyleSheet('components/com_muscol/assets/tagmanager/bootstrap-tagmanager.css');

		$js= array();
		$prefill= array();

		foreach($tags as $tag){
			$js[] = '"'.$tag->tag_name.'"';
			if (in_array($tag->id,$artist->tags)) $prefill[] = '"'.$tag->tag_name.'"';
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
		if( $id == $this->artist->genre_id ) $selected = "selected";
            
		return "<option value='".$id."' $selected >".$indent.$name."</option>";	
	}
	
}