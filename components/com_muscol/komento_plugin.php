<?php


// No direct access
defined('_JEXEC') or die('Restricted access');

// Always load abstract class by uncommenting the following line
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_komento' . DS . 'komento_plugins' . DS .'abstract.php' );
require_once(JPATH_SITE.DS.'components'.DS.'com_muscol'.DS.'helpers'.DS.'helpers.php');

// Load all required files by component
// require_once( your component's files );

class KomentoCommuscol extends KomentoExtension
{
	public $component = 'com_muscol';
	public $_item;

	// map the keys here
	public $_map = array(
		// not needed with custom getContentId()
		'id'			=> 'id',

		// not needed with custom getContentTitle()
		'title'			=> 'name',

		// not needed with custom getContentHits()
		'hits'			=> 'hits',

		// not needed with custom getAuthorId()
		'created_by'	=> 'user_id',

		// not needed with custom getCategoryId()
		'catid'			=> 'artist_id',

		// not needed with custom getContentPermalink()
		'permalink'		=> 'permalink_field'
		);

	// load all main properties here based on article id
	public function load( $cid )
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_muscol');
		
		static $instances = array();

		if( !isset( $instances[$cid] ) )
		{
			// populate $this->_item with:
			// id_field
			// title_field
			// hits_field
			// created_by_field
			// catid_field
			// permalink_field
			
			$db		= JFactory::getDBO();

			$result = new stdClass();
			
			if($cid > 40000000) {//song
				
				$id = $cid - 40000000;
				$query	= 'SELECT `id`, `name`, `hits`, `user_id`, `artist_id` FROM `#__muscol_songs` WHERE `id` = ' .  $id ;
				// return false if there are no objects to load
				$db->setQuery( $query );
				if( !$result = $db->loadObject() )
				{
					return false;
				}
				//print_r($this->_item);die;
				// generate link for this article
				$result->permalink_field = 'index.php?option=com_muscol&view=song&id=' . $id;

			}
			elseif($cid > 30000000) {//playlist
				$id = $cid - 30000000;
				$query	= 'SELECT `id`, `title` as name, `user_id` FROM `#__muscol_playlists` WHERE `id` = ' . $id;
				// return false if there are no objects to load
				$db->setQuery( $query );
				if( !$result= $db->loadObject() )
				{
					return false;
				}
				// generate link for this article
				$result->permalink_field = 'index.php?option=com_muscol&view=playlist&id=' . $id;

			}
			elseif($cid > 20000000) {//artist
				$id = $cid - 20000000;
				$query	= 'SELECT `id`, `artist_name` as name, `hits`, `user_id`, `id` as artist_id FROM `#__muscol_artists` WHERE `id` = ' . $id;
				$db->setQuery( $query );
				// return false if there are no objects to load
				if( !$result = $db->loadObject() )
				{
					return false;
				}
				// generate link for this article
				$result->permalink_field = 'index.php?option=com_muscol&view=artist&id=' . $id;

			}
			elseif($cid > 10000000) {//album
				$id = $cid - 10000000;
				$query	= 'SELECT `id`, `name`, `hits`, `user_id`, `artist_id` FROM `#__muscol_albums` WHERE `id` = ' . $id;
				$db->setQuery( $query );
				// return false if there are no objects to load
				
				if( !$result = $db->loadObject() )
				{
					return false;
				}
				// generate link for this article
				$result->permalink_field = 'index.php?option=com_muscol&view=album&id=' . $id;

			}
			
			$result->cid = $cid;
			
			// call the prepareLink function and leave the rest to us
			// unless you have custom SEF methods, then use "getContentPermalink" function to overwrite
			$result->permalink_field = $this->prepareLink( $result->permalink_field );

			$instances[$cid] = $result;
			
		}
		
		$this->_item = $instances[$cid];
		
		return $this;
	}

	public function getComponentName(){
		return "Music Collection";
	}

	public function getComponentIcon(){
		return "components/com_muscol/assets/images/mc_icon.png";
	}
	
	public function getContentId(){
		//echo "test";die;
	}
	
	public function getContentTitle(){
		return $this->_item->name;
	}
	
	

	public function getContentIds( $categories = '' )
	{
		$db		= JFactory::getDbo();
		$query = '';

		if( empty( $categories ) )
		{
			$query = 'SELECT `id` FROM `#__muscol_albums` ORDER BY `id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `id` FROM `#__muscol_albums` WHERE `artist_id` IN (' . $categories . ') ORDER BY `id`';
		}

		$db->setQuery( $query );
		
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$db		= JFactory::getDbo();
		$query	= 'SELECT `id`, `artist_name` as title FROM `#__muscol_artists`';
		$db->setQuery( $query );
		$categories = $db->loadObjectList();

		
		return $categories;
	}

	// to determine if is listing view
	public function isListingView()
	{
		$views = array('artists');
		//$views = array();
		return in_array(JRequest::getCmd('view'), $views);
	}

	// to determine if is entry view
	public function isEntryView()
	{
		$views = array('artist', 'album', 'song', 'playlist', 'songs');
		//$views = array();
		return in_array(JRequest::getCmd('view'), $views);
	}

	public function onExecute( &$article, $html, $view, $options = array() )
    {
        // $html is the html content generated by Komento
        return $html;
    }
}
