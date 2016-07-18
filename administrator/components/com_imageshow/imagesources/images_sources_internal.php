<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: images_sources_internal.php 14818 2012-08-07 11:27:26Z haonv $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
class JSNImagesSourcesInternal extends JSNImagesSourcesDefault
{
	protected $_syncmode = false;
	public $_syncAlbum = array();

	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->checkComponent();

		$objJSNImages = JSNISFactory::getObj('classes.jsn_is_images');
		$albumSync 	  = $objJSNImages->getSyncAlbumsByShowlistID($this->_showlistTable->showlist_id);

		if (count($albumSync) > 0) {
			$this->_syncmode 	= true;
			$this->_syncAlbum 	= $albumSync;
		}
	}

	private function checkComponent()
	{
		$objJSNSource = JSNISFactory::getObj('classes.jsn_is_source');

		if (isset($this->_source['sourceDefine']->component) && $this->_source['sourceDefine']->component != '')
		{
			$objJSNUtils = JSNISFactory::getObj('classes.jsn_is_utils');
			$comInstalled = $objJSNUtils->checkComInstalled($this->_source['sourceDefine']->component);

			if (!$comInstalled)
			{
				$objJSNSource->updateShowlistBySource(array('image_source_name' => $this->_showlistTable->image_source_name,
															'image_source_type' => $this->_showlistTable->image_source_type
				));
			}
		}
	}

	public function saveImages($config = array())
	{
		parent::saveImages($config);
	}

	/*
	 * save infor source to showlist
	 */
	public function onSelectSource($config = array())
	{
		if ($config['showlist_id'] && $config['image_source_type'] &&  $config['source_identify'])
		{
			if ($this->_showlistTable->load($config['showlist_id']))
			{
				$this->_showlistTable->image_source_type = $config['image_source_type'];
				$this->_showlistTable->image_source_name = $config['source_identify'];
				$this->_showlistTable->image_source_profile_id = 0;

				if ($this->_showlistTable->store()) {
					return array('sourcesaved' => true);
				}
			}
		}

		return array('sourcesaved' => true);
	}

	public function getImages($config = array())
	{
		parent::getImages($config);

		if ($this->_syncmode == true) { // get sync images if sync feature is enabled
			$this->getSyncImages($config);
		}
		return $this->_data['images'];
	}

	/**
	 * @param showlist_id
	 * @param limitEdition true/fasle
	 */
	public function getSyncImages($config = array()) {}

	public function getProfileTitle() {
		return (isset($this->_source['sourceDefine'])) ? $this->_source['sourceDefine']->description : '';
	}

	//	public function addOriginalInfo(){}

	//	public function loadImages($config = array()){}

	public function getImages2JSON($config = array())
	{
		parent::getImages2JSON($config);

		if ($this->_syncmode == true) { // get sync images if sync feature is enabled
			$this->getSyncImages($config);
		}

		return $this->_data['images'];
	}

	public function getImageSrc($config = array('image_big' => '', 'URL' => '')) {
		return $config['URL'].$config['image_big'];
	}
	/**
	 *
	 * Check Image exists in showlist
	 *
	 * @param String $ImageID
	 */
	public function checkImageSelected( $ImageID )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("count(image_id)");
		$query->from("#__imageshow_images");
		$query->where("image_extid=".$db->quote($ImageID));
		$query->where("showlist_id=".$this->_source['showlistID']);
		$db->setQuery($query);
		return (int) $db->loadResult() > 0 ? true : false;
	}

	/**
	 * Load images store
	 */
	/* public function loadImagesStored()
	 {

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("*");
		$query->from("#__imageshow_images");
		$query->where("showlist_id=".$this->_source['showlistID']);
		$query->where("image_extid IS NOT NULL ");
		$query->order("ordering ASC");
		$db->setQuery($query);
		//echo $query;

		$results = $db->loadAssocList();

		$images = Array();
		foreach ($results as $value) {
		$images[] = $value;
		}

		return $images;
		}*/

	/**
	 * Check image exists?
	 */
	/*function chechImageExists($image_exitid,$showlistId)
	 {
		$db = JFactory::getDbo();
		$query = "SELECT * FROM #__imageshow_images WHERE image_extid=".$db->quote($image_exitid)." AND showlist_id=".$db->quote($showlistId);
		$db->setQuery($query);
		$db->query();
		return $db->getNumRows();

		}*/
	/**
	 *
	 * Save sync
	 */
	public function savesync()
	{
		$syncCate    = JRequest::getVar('syncCate', '');
		$showListID  = JRequest::getVar('showListID', '');
		$sourceType  = JRequest::getVar('sourceType', '');
		$sourceName  = JRequest::getVar('sourceName', '');
		$imageSource = JSNFactory::getSource( $sourceName, $sourceType, $showListID );
		$imageSource->saveSync($syncCate);
		$this->_syncmode = true;
		jexit();
	}
	/**
	 * Save list images on showlist
	 */
	/*function saveImagesShowlist($data = array())
	 {

		$imagesTable 					= JTable::getInstance('images', 'Table');
		$imagesTable->showlist_id   	= $data->showlist_id;
		$imagesTable->image_extid  		= $data->image_extid;
		$imagesTable->album_extid   	= $data->album_extid;
		$imagesTable->ordering      	= $data->ordering;
		$imagesTable->image_small 		= $data->image_small;
		$imagesTable->image_medium 		= $data->image_medium;
		$imagesTable->image_big 		= $data->image_big;
		$imagesTable->image_link 		= $data->image_link;
		$imagesTable->image_description = $data->image_description;
		$imagesTable->image_title		= $data->image_title;
		$imagesTable->sync				= $data->sync;
		if(JSNImagesSourcesInternal::chechImageExists($imagesTable->image_extid,$imagesTable->showlist_id) > 0){

		}else{
		return $imagesTable->store(array('replcaceSpace' => false));
		}

		}*/

	/*
	 *
	 * Check sync if exists
	 * @param String $syncName
	 */
	public function checkSync($syncName)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("count(image_id)" );
		$query->from("#__imageshow_images");
		$query->where("showlist_id=".$this->_source['showlistID']);
		$query->where("album_extid = ".$db->quote( $syncName ));
		$db->setQuery($query);
		//echo  $query;
		return $db->loadResult() > 0 ? true : false ;
	}

	/**
	 *
	 * Reset showlist
	 */
	public function resetShowListImages()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete();
		$query->from("#__imageshow_images");
		$query->where("showlist_id=".$this->_source['showlistID']);
		$db->setQuery($query);
		$db->query();
	}

	/**
	 *
	 * Remove sync
	 * @param String $syncName
	 */
	public function removeSync( $syncName )
	{

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete();
		$query->from("#__imageshow_images");
		$query->where("showlist_id=".$this->_source['showlistID']);
		$query->where("album_extid = ".$db->quote( $syncName ));
		$db->setQuery($query);
		$db->query();
	}

	/**
	 *
	 * Get showlist mode
	 */
	public function getShowlistMode()
	{
		return $this->_syncmode? 'sync' : false;
	}
	/**
	 * Convert xml data to tree menu
	 */
	function convertXmlToTreeMenu($xmlarray,$catSelected)
	{

		$selected = (JSNImagesSourcesInternal::checkCatisSelected($xmlarray['@attributes']['data']))?' catselected':'';
		$catchoosed = (trim($catSelected) == trim($xmlarray['@attributes']['data']))?' catchoosed':'';

		$categories = '<li class="'.$selected.$catchoosed.'" id="'.$xmlarray['@attributes']['data'].'">'.$xmlarray['@attributes']['label'];
		$categories.= '<ul>';
		if(isset($xmlarray['node'])){
			if(empty($xmlarray['node'][1])){

				$selected = (JSNImagesSourcesInternal::checkCatisSelected($xmlarray['node']['@attributes']['data']))?' catselected':'';
				$catchoosed = (trim($catSelected) == trim($xmlarray['node']['@attributes']['data']) && $catSelected!='0')?' catchoosed':'';
				$categories.= '<li class="'.$selected.$catchoosed.'" id="'.$xmlarray['node']['@attributes']['data'].'">'.$xmlarray['node']['@attributes']['label'];
				if(isset($xmlarray['node']['node'])){
					$categories.=JSNImagesSourcesInternal::_drawChildTreeMenu($xmlarray['node']['node'],$catSelected);
				}
				else
				{
					$categories.= '</li>';
				}	
			}else{

				foreach($xmlarray['node'] as $node){

					$selected1 = (JSNImagesSourcesInternal::checkCatisSelected($node['@attributes']['data']))?' catselected':'';
					$catchoosed = (trim($catSelected) == trim($node['@attributes']['data']) && $catSelected!='0')?' catchoosed':'';
					$categories.= '<li class="secondchild'.$selected1.$catchoosed.'" id="'.$node['@attributes']['data'].'">'.$node['@attributes']['label'];
					if(isset($node['node'])){
						$categories.=JSNImagesSourcesInternal::_drawChildTreeMenu($node['node'],$catSelected);
					}
				}
			}

			$categories.='</li>';
		}
		$categories.= '</ul>';
		$categories.='</li>';
		return $categories;
	}

	function _drawChildTreeMenu($nodearray,$catSelected)
	{
		$menu = '';

		if(!isset($nodearray[0])){
			$menu.= '<ul>';
			$selected1 = (JSNImagesSourcesInternal::checkCatisSelected($nodearray['@attributes']['data']))?' catselected':'';
			$catchoosed = (trim($catSelected) == trim($nodearray['@attributes']['data']) && $catSelected!='0')?' catchoosed':'';
			$menu.= '<li class="'.$selected1.$catchoosed.'" id="'.$nodearray['@attributes']['data'].'">'.$nodearray['@attributes']['label'];
			if(isset($nodearray['node'])){
				$menu.= JSNImagesSourcesInternal::_drawChildTreeMenu($nodearray['node'],$catSelected);
			}
			$menu.='</li>';
			$menu.= '</ul>';
		}else{
			if(empty($nodearray[1])){
				$selected = (JSNImagesSourcesInternal::checkCatisSelected($nodearray['@attributes']['data']))?' catselected':'';
				$menu.= '<ul>';
				$catchoosed = (trim($catSelected) == trim($nodearray['@attributes']['data']) && $catSelected!='0')?' catchoosed':'';
				$menu.= '<li class="'.$selected.$catchoosed.'" id="'.$nodearray['@attributes']['data'].'">'.$nodearray['@attributes']['label'];
				$menu.= '</li></ul>';
			}else{
				foreach($nodearray as $node){
					$menu.= '<ul>';
					$selected = (JSNImagesSourcesInternal::checkCatisSelected($node['@attributes']['data']))?' catselected':'';
					$catchoosed = (trim($catSelected) == trim($node['@attributes']['data']) && $catSelected!='0')?' catchoosed':'';
					$menu.= '<li class="'.$selected.$catchoosed.'" id="'.$node['@attributes']['data'].'">'.$node['@attributes']['label'];
					if(isset($node['node'])){
						$menu.= JSNImagesSourcesInternal::_drawChildTreeMenu($node['node'],$catSelected);
					}
					$menu.= '</li></ul>';
				}
			}

		}
		return $menu;
	}

	function checkCatisSelected($catId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("count(image_id)" );
		$query->from("#__imageshow_images");
		$query->where("album_extid = ".$db->quote( $catId ));
		$query->where("showlist_id=".$this->_source['showlistID']);
		$db->setQuery($query);
		return $db->loadResult() > 0 ? true : false ;
	}

}
