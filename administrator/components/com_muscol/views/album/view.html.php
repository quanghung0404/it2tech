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
 
class AlbumsViewAlbum extends JViewLegacy
{

	var $genres_array = null;
	
	function display($tpl = null)
	{
		//cridem el CSS
		$document	= JFactory::getDocument();
		$document->addStyleSheet('components/com_muscol/assets/albums.css');
		//get the album
		$album			= $this->get('Data');
		$artists		= $this->get('ArtistsData');
		$formats		= $this->get('FormatsData');
		$types			= $this->get('TypesData');
		$genres			= $this->get('GenresData');
		$tags			= $this->get('TagsData');
		$songs			= $this->get('Songs');
		$albums			= $this->get('AlbumsData');
		
		// quina tab del panel mostrar inicialment
		$tab = JRequest::getVar('tab',  0, '');
		$this->assignRef('tab',		$tab);
		
		$isNew		= ($album->id < 1);

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		$title = $isNew ? JText::_( 'Album' ) : $album->name;
		
		JToolBarHelper::title(   $title . ': <small><small>[ ' . $text.' ]</small></small>','album' );
		JToolBarHelper::save();
		
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::apply();
			JToolBarHelper::cancel( 'cancel', 'Close' );
			
		}
		
		// push data into the template
		$this->assignRef('album',		$album);
		$this->assignRef('artists',		$artists);
		$this->assignRef('formats',		$formats);
		$this->assignRef('types',		$types);
		$this->assignRef('genres',		$genres);
		$this->assignRef('tags',		$tags);
		$this->assignRef('songs',		$songs);
		$this->assignRef('albums',		$albums);

		JHtml::_('jquery.framework');
		
		//DISCOGS integration
		$document->addScript('components/com_muscol/assets/discogs.js');
		
		// JS
		$document->addScript('components/com_muscol/assets/songs.js');
		
		JHtml::_('formbehavior.chosen', '.chzn-select');

		//tags system
		//http://welldonethings.com/tags/manager/v3
		$document->addScript('components/com_muscol/assets/tagmanager/bootstrap-tagmanager.js');
		$document->addStyleSheet('components/com_muscol/assets/tagmanager/bootstrap-tagmanager.css');

		$js= array();
		$prefill= array();

		foreach($tags as $tag){
			$js[] = '"'.$tag->tag_name.'"';
			if (in_array($tag->id,$album->tags)) $prefill[] = '"'.$tag->tag_name.'"';
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
		if( $id == $this->album->genre_id ) $selected = "selected";
            
		return "<option value='".$id."' $selected >".$indent.$name."</option>";	
	}
	
	function displayMonth($month){
		$month_array = array(
			1 => "January" , 
			2 => "February" , 
			3 => "March" , 
			4 => "April" , 
			5 => "May" , 
			6 => "June", 
			7 => "July", 
			8 => "August" , 
			9 => "September", 
			10 => "October" , 
			11 => "November", 
			12 => "December"
		);
		return JText::_( $month_array[$month] );
	}
}