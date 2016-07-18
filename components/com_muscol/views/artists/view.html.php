<?php

/** 
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

jimport( 'joomla.application.component.view');

class ArtistsViewArtists extends JViewLegacy
{
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		
		$uri	= JFactory::getURI();
		
		if(JRequest::getVar("layout") == "fastnav"){
			
			$this->_layout = "fastnav";
			
			
			$items		= $this->get( 'ArtistsFastnav');
			
			$this->assignRef('items',		$items);
			
		}
		else{
		
			$pathway	= $mainframe->getPathway();
			$document	= JFactory::getDocument();
			$uri 		= JFactory::getURI();
			
			$params =JComponentHelper::getParams( 'com_muscol' );
			
			$this->_layout = $params->get('artists_view');
			if($this->_layout == "") $this->_layout = "default";
	
			$items		= $this->get( 'Data');
			$letter		= $this->get( 'Letter');

			$genre_list		= $this->get( 'GenresData');

			$pagination = $this->get('Pagination');
	
			$this->assignRef('items',		$items);
			$this->assignRef('letter',		$letter);
			$this->assignRef('params',		$params);
			$this->assignRef('genre_list',		$genre_list);
			
			$this->assignRef('pagination', $pagination);
			
			$this->assign('action', 	$uri->toString());
			
			/*
			 * Process the prepare content plugins
			 */

			$intro = new stdClass();
			$intro2 = new stdClass();
			
			$intro->text = $params->get('introtext');
			$intro->text = str_replace("\n", '<br />', $intro->text); 
			$intro2->text = $params->get('introtext2');
			$intro2->text = str_replace("\n", '<br />', $intro2->text); 
			
			if($params->get('processcontentplugins')){
			
				$dispatcher	= JDispatcher::getInstance();
				$plug_params = new JRegistry('');
				
				JPluginHelper::importPlugin('content');
				$results = $dispatcher->trigger('onContentPrepare', array ('com_muscol.artists', &$intro, &$plug_params, 0));
				$results = $dispatcher->trigger('onContentPrepare', array ('com_muscol.artists', &$intro2, &$plug_params, 0));
			}
			
			$this->assignRef('introtext',		$intro->text);
			$this->assignRef('introtext2',		$intro2->text);
			
			if($params->get('keywords') != ""){
				$document->setMetaData( 'keywords', $document->getMetaData( 'keywords' ) . ", " . $params->get('keywords') );
			}
			if($params->get('description') != ""){
				$document->setMetaData( 'description', $params->get('description') );
			}
			
			//creem els breadcrumbs
			
			$letters = MusColAlphabets::get_combined_array();
			
			if($letter) $pathway->addItem($letters[$letter], 'index.php?option=com_muscol&view=artists&letter='.$letter . '&Itemid=' . $params->get('itemid'));
			
			//cridem els CSS
			$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/letter.css');
			$document->addStyleSheet( $uri->base() . 'components/com_muscol/assets/artists.css');
		}
		
		//JHtml::_('formbehavior.chosen', '.chzn-select');
				
		parent::display($tpl);
	}
	
	
}
?>
