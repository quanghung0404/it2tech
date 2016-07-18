<?php


/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */
jimport('joomla.application.component.controller');

class AlbumsController extends JControllerLegacy
{

	function display( $cachable = false, $urlparams = array())
	{
			parent::display($cachable,$urlparams);
		
	}
	
	
}

class ArtistsController extends JControllerLegacy
{

	function display( $cachable = false, $urlparams = array())
	{
		if(JRequest::getVar( 'layout' ) == "form" || JRequest::getVar( 'layout' ) == "simpleform"){
			$user = JFactory::getUser();
			
			$id = JRequest::getVar( 'id' ) ;
			$view = JRequest::getVar( 'view' ) ;
			
			$params =JComponentHelper::getParams( 'com_muscol' );
			$itemid = $params->get('itemid');
			if($itemid != "") $itemid = "&Itemid=" . $itemid;
			
			if(!$user->id){ // not registered
				switch($view){
					case "album":
					$msg = JText::_('NOT_AUTHORIZED_ALBUMS');
					break;
					case "artist":
					$msg = JText::_('NOT_AUTHORIZED_ARTISTS');
					break;
					case "song":
					$msg = JText::_('NOT_AUTHORIZED_SONGS');
					break;
					default:
					$msg = JText::_('NOT_AUTHORIZED_ALBUMS');
				}
		
				$link = JRoute::_('index.php?option=com_muscol&view=artists' . $itemid);
				$this->setRedirect($link, $msg);
			}
			
			else{ // registered
				
				switch($view){
					case "album":
					$can_edit = $this->check_album_permission($id);
					$msg = JText::_('NOT_AUTHORIZED_ALBUM');
					$link = JRoute::_('index.php?option=com_muscol&view=album&id='. $id . $itemid);
					break;
					
					case "song":
					$can_edit = $this->check_song_permission($id);
					$msg = JText::_('NOT_AUTHORIZED_SONG');
					$link = JRoute::_('index.php?option=com_muscol&view=song&id='. $id . $itemid);
					break;
					
					case "artist":
					$can_edit = $this->check_artist_permission($id);
					$msg = JText::_('NOT_AUTHORIZED_ARTIST');
					$link = JRoute::_('index.php?option=com_muscol&view=artist&id='. $id . $itemid);
					
					if(!$params->get('users_add_artists')){
						$can_edit = false ;
						$msg = JText::_('NOT_AUTHORIZED_ARTISTS');
						$link = JRoute::_('index.php?option=com_muscol&view=artists' . $itemid);
					}
					break;
					
					default:
					$can_edit = $this->check_album_permission($id);
					$msg = JText::_('NOT_AUTHORIZED_ALBUMS');
					$link = JRoute::_('index.php?option=com_muscol&view=album&id='. $id . $itemid);
				}
				
				if(!$can_edit){
				
					if(!$id) $link = JRoute::_('index.php?option=com_muscol&view=artists' . $itemid);
					
					$this->setRedirect($link, $msg);
				}
				else{
					
					if($view == "album"){
						$db = JFactory::getDBO();
				
						if($params->get('add_albums_own_artists')) $where_clause = ' WHERE user_id = ' . $user->id;
						else $where_clause = '';
				
						$query = 'SELECT COUNT(*) FROM #__muscol_artists '. $where_clause ;
						$db->setQuery($query);
						$has_artists = $db->loadResult();
						
						if(!$has_artists){
							if($params->get('users_add_artists')){
								$msg = JText::_('ADD_ARTIST_FIRST');
								$link = JRoute::_('index.php?option=com_muscol&view=artist&layout=form' . $itemid);
							}
							else{
								$msg = JText::_('NO_ARTISTS_AVAILABLE');
								$link = JRoute::_('index.php?option=com_muscol&view=artists' . $itemid);
							}
							$this->setRedirect($link, $msg);
						}
					}
					
					parent::display($cachable,$urlparams); // OK
				}
			}
		}
		
		else {
			
			parent::display($cachable,$urlparams);
		}
	}
	
	function check_album_permission($album_id){
		
		if(!$album_id) return true; // everybody can add an album if is a registered user
		
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$query = 'SELECT user_id FROM #__muscol_albums WHERE id = ' . $album_id ;
		$db->setQuery($query);
		$album_creator = $db->loadResult();
		
		if($album_creator == $user->id) return true;
		else return false;
		
	}
	
	function check_artist_permission($artist_id){
		
		if(!$artist_id) return true; // everybody can add an album if is a registered user
		
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$query = 'SELECT user_id FROM #__muscol_artists WHERE id = ' . $artist_id ;
		$db->setQuery($query);
		$artist_creator = $db->loadResult();
		
		if($artist_creator == $user->id) return true;
		else return false;
		
	}
	
	function check_song_permission($song_id){
		
		if(!$song_id) return true; // everybody can add a song if is a registered user
		
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$query = 'SELECT user_id FROM #__muscol_songs WHERE id = ' . $song_id ;
		$db->setQuery($query);
		$song_creator = $db->loadResult();
		
		if($song_creator == $user->id) return true;
		else return false;
		
	}
	
	function save_album()
	{
		$db = JFactory::getDBO();
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		$new_artist = JRequest::getString('newartist');
		if($new_artist){
			$model = $this->getModel('artist');
			
			$artist_data['id'] = 0 ;
			$artist_data['artist_name'] = $new_artist ;
			$artist_data['genre_id'] = JRequest::getVar('genre_id') ;
			
			if ($artist_id = $model->store($artist_data)) {
				$msg_art = JText::_( 'ARTIST_SAVED' );
				
				JRequest::setVar('artist_id', $artist_id);
				
			} else {
				$msg_art = JText::_( 'ERROR_SAVING_ARTIST' );
				
			}
		}
		
		$model = $this->getModel('album');
		
		if ($id = $model->store()) {
			$msg = JText::_( 'ALBUM_SAVED' );
			$saved_ok = true;
			
		} else {
			$msg = JText::_( 'ERROR_SAVING_ALBUM' );
			$saved_ok = false;
		}

		$link = JRoute::_('index.php?option=com_muscol&view=album&id='. $id . $itemid) ;
		
		$this->setRedirect($link, $msg . " " . $msg_art);
	}
	
	function save_artist()
	{
		$model = $this->getModel('artist');
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		
		if($itemid != "") $itemid = "&Itemid=" . $itemid;

		if ($id = $model->store()) {
			$msg = JText::_( 'ARTIST_SAVED' );
			$saved_ok = true;
		} else {
			$msg = JText::_( 'ERROR_SAVING_ARTIST' );
			$saved_ok = false;
		}

		$link = JRoute::_('index.php?option=com_muscol&view=artist&id='. $id . $itemid) ;
		
		$this->setRedirect($link, $msg);
	}
	
	function save_song()
	{
		$model = $this->getModel('song');

		if ($album_id = $model->store($post)) {
			$msg = JText::_( 'SONG_SAVED' );
		} else {
			$msg = JText::_( 'ERROR_SAVING_SONG' );
		}
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		$id = JRequest::getVar('id'); // song id
		
		switch(JRequest::getVar('from')){
			case "album":
			$link = JRoute::_('index.php?option=com_muscol&view=album&layout=form&id=' . $album_id . $itemid);
			break;
			
			default:
			if(!$id)  $link = JRoute::_('index.php?option=com_muscol&view=album&id='. $album_id . $itemid);
			else $link = JRoute::_('index.php?option=com_muscol&view=song&id='. $id . $itemid);
		}

		$this->setRedirect($link, $msg);
	}
	
	function save_comment()
	{
		$model = $this->getModel('album');

		if ($model->store_comment($post)) {
			$msg = JText::_( 'COMMENT_SAVED' );
			$saved_ok = true;
		} else {
			$msg = JText::_( 'ERROR_SAVING_COMMENT' );
			$saved_ok = false;
		}

		$album_id = JRequest::getVar('album_id'); // stands also for song_id
		$comment_type = JRequest::getVar('comment_type');
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		switch($comment_type){
			case "album":
			$link = JRoute::_('index.php?option=com_muscol&view=album&id='. $album_id  . $itemid) ;
			break;
			
			case "song":
			$link = JRoute::_('index.php?option=com_muscol&view=song&id='. $album_id  . $itemid) ;
			break;
			
			case "playlist":
			$link = JRoute::_('index.php?option=com_muscol&view=playlist&id='. $album_id  . $itemid) ;
			break;
			
			case "artist":
			$link = JRoute::_('index.php?option=com_muscol&view=artist&id='. $album_id  . $itemid) ;
			break;
		}
		
		if($saved_ok){

			//new plugin access
			$dispatcher	= JDispatcher::getInstance();
			$plugin_ok = JPluginHelper::importPlugin('muscol');
			$results = $dispatcher->trigger('onSaveComment', array ($album_id, $comment_type));
			
		}

		

		$this->setRedirect($link);
	}
	
	function rate(){
		
		$model = $this->getModel('rating');

		if ($model->store_rating()) {
			$msg = JText::_( 'RATING_SAVED' );
			$saved_ok = true;
		} else {
			$msg = JText::_( 'ERROR_SAVING_RATING' );
			$saved_ok = false;
		}

		$album_id = JRequest::getInt('album_id');
		
		$link = MusColHelper::routeAlbum($album_id);
		
		if($saved_ok){

			//new plugin access
			$dispatcher	= JDispatcher::getInstance();
			$plugin_ok = JPluginHelper::importPlugin('muscol');
			$results = $dispatcher->trigger('onSaveRatingAlbum', array ($album_id));
			
		}

		$this->setRedirect($link);
	}
	
	function rate_song(){
		
		$model = $this->getModel('rating');

		if ($model->store_rating()) {
			$msg = JText::_( 'RATING_SAVED' );
			$saved_ok = true;
		} else {
			$msg = JText::_( 'ERROR_SAVING_RATING' );
			$saved_ok = false;
		}

		$album_id = JRequest::getInt('album_id');
		
		$link = MusColHelper::routeSong($album_id);
			
		if($saved_ok){

			//new plugin access
			$dispatcher	= JDispatcher::getInstance();
			$plugin_ok = JPluginHelper::importPlugin('muscol');
			$results = $dispatcher->trigger('onSaveRatingSong', array ($album_id));
			
		}

		$this->setRedirect($link);
	}
	
	//AJAX function to add a song to a playlist
	function add_song_to_playlist(){ 
 
		 $mainframe = JFactory::getApplication();
		 $db = JFactory::getDBO();

		 $id = JRequest::getInt( 'id');
		 $uri = JFactory::getURI();

		 if($this->check_playlist_permission( $id )){
			 $song_id = JRequest::getInt( 'song_id');
		
			 $type = JRequest::getString( 'type');
			 
			 $model = $this->getModel('playlist');
			 $return = $model->add_item($id,$song_id,$type);

			 $query = " SELECT s.*, ar.artist_name, al.image FROM #__muscol_songs AS s 
			 LEFT JOIN #__muscol_artists AS ar ON ar.id = s.artist_id 
			 LEFT JOIN #__muscol_albums AS al ON al.id = s.album_id WHERE s.id = ".$song_id;
			 $db->setQuery($query);
			 $song = $db->loadObject();

			 switch($type){
				case 'v':

					$video_pieces = explode("?",$song->video) ;
					if(count($video_pieces) == 2 ){ // http://www.youtube.com/watch?v=6hzrDeceEKc
						$youtube_video_id = str_replace("v=", "", $video_pieces[1]);
					}
					else{ // http://www.youtube.com/v/6hzrDeceEKc OR 6hzrDeceEKc
						$youtube_video_id = str_replace("http://www.youtube.com/v/", "", $song->video);
					}
					$thefile = "http://www.youtube.com/v/" . $youtube_video_id ;

				break;
				default:
					$thefile = MusColHelper::getSongFileURL($song);
				break;
			}

			 $object = new stdClass();
			 $object->title = $song->name ;
			 $object->description = $song->artist_name ;
			 $object->file = $thefile ;
			 $object->image = $uri->base() . "images/albums/" . $song->image ;
	
			 echo json_encode($object); 
		 }
		 else echo 0;
		 
		 $mainframe->close();
 
  	}
	
	function remove_song_playlist(){
		 $mainframe = JFactory::getApplication();
		 if($this->check_playlist_permission( JRequest::getVar('id') )){
			 $model = $this->getModel('playlist');
			if(!$model->remove_songs()) {
				$msg = JText::_( 'ERROR_SONGS_DELETED_PLAYLIST' );
				$msg_type = "error";
			} else {
				$msg = JText::_( 'SONGS_DELETED_PLAYLIST' );
				$msg_type = "message";
			}
		 }
		else{
			 $msg = JText::_('NOT_ALLOWED_EDIT_PLAYLIST');
			 $msg_type = "notice";
		 }
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;

		$this->setRedirect( 'index.php?option=com_muscol&view=playlist&id=' . JRequest::getVar('id') . $itemid, $msg, $msg_type );
	}
	
	function save_playlist_order(){
		 $mainframe = JFactory::getApplication();
		 if($this->check_playlist_permission( JRequest::getVar('id') )){
			 $model = $this->getModel('playlist');
			if(!$model->save_playlist_order()) {
				$msg = JText::_( 'ERROR_PLAYLIST_ORDER_SAVED' );
				$msg_type = "error";
			} else {
				$msg = JText::_( 'PLAYLIST_ORDER_SAVED' );
				$msg_type = "message";
			}
		 }
		 else{
			 $msg = JText::_('NOT_ALLOWED_EDIT_PLAYLIST');
			$msg_type = "notice";
		 }
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;

		$this->setRedirect( 'index.php?option=com_muscol&view=playlist&id=' . JRequest::getVar('id') . $itemid, $msg, $msg_type );
	}
	
	// this functions is called by the JW player module to show the popup player
	// this function is always called using AJAX, never directly
	function popup_jwplayer(){ 
 
		 $mainframe = JFactory::getApplication();
		 
		 if(file_exists(JPATH_SITE.DS.'modules'.DS.'mod_muscol_jwplayer'.DS.'helper.php'))
		 require_once (JPATH_SITE.DS.'modules'.DS.'mod_muscol_jwplayer'.DS.'helper.php');
		
		 // we get the params for the popup player FROM THE COMPONENT, not the module
		 //$params =JComponentHelper::getParams( 'com_muscol' );
		 $params = new JRegistry('');
		 
		 $params->set('width',JRequest::getVar( 'width') );
		 $params->set('height',JRequest::getVar( 'height') );
		 $params->set('playlist',JRequest::getVar( 'playlist') );
		 $params->set('playlistsize',JRequest::getVar( 'playlistsize') );
		 
		 $params->set('load_mootools',JRequest::getVar( 'load_mootools') );

		 $params->set('default_playlist',JRequest::getVar( 'default_playlist') );
		 
		 $module_jwplayer	= modMusColJWPlayerHelper::getPopupPlayer($params);
		 require(JModuleHelper::getLayoutPath('mod_muscol_jwplayer','popup'));
	  
		 $mainframe->close();
 
    }
	
	function set_current_playlist(){
		$mainframe = JFactory::getApplication();
		
		$value = JRequest::getVar( 'id') ;
		 
		$session =JSession::getInstance('','');
		$session->set('current_playlist',$value);
		
		$uri = JFactory::getURI();
		
		// we return the new playlit URL
		//echo JRoute::_('index.php?option=com_muscol&view=playlist&id='.$value.'&format=feed&type=xspf') ;
		echo $uri->base() .'index.php?option=com_muscol&view=playlist&id='.$value.'&format=feed&type=xspf' ;
	  
		$mainframe->close();
	}
	
	function save_popup_status_on_session(){
		$mainframe = JFactory::getApplication();
		
		$value = JRequest::getVar( 'popup_active') ;
		if(!$value) $value = "";
		 
		$session =JSession::getInstance('','');
		$session->set('popup_active',$value);
	  
		$mainframe->close();
	}
	
	function consolidate_playlist(){
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		if($params->get('allowcreateplaylists')){
			if($user->id){ // only can save playlists registered users
				$model = $this->getModel('playlist');
				if(!$model->consolidate_playlist()) {
					$msg = JText::_( 'ERROR_SAVING_PLAYLIST' );
					$msg_type = "error";
				} else {
					$msg = JText::_( 'PLAYLIST_SAVED' );
					$msg_type = "message";
					$saved_ok = true;
				}
			}
			else{
				$msg = JText::_( 'ONLY_REGISTERED_SAVE_PLAYLISTS' );
				$msg_type = "error";
			}
		}
		else{
			$msg = JText::_( 'ADMIN_DISABLED_PLAYLIST_SAVING' );
			$msg_type = "message";
		}
		
		
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		
		if($saved_ok){
			
			/**** JOMSOCIAL INTEGRATION *******/
			
			if(file_exists( JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php') && $params->get('notify_jomsocial')){
				
				$query = ' SELECT title as name,user_id, id FROM #__muscol_playlists ORDER BY id DESC ' ;
				$db = JFactory::getDBO();
				$db->setQuery($query);
				$item = $db->loadObject() ;
				
				$link = JRoute::_('index.php?option=com_muscol&view=playlist&id='.$item->id .  $itemid );
				
				CFactory::load( 'libraries' , 'activities' );
				$user = JFactory::getUser();
				
				$act = new stdClass();
				$act->actor 	= $user->id;
				$act->target 	= 0;
				$act->title		= "{actor} ".JText::_('created a')." <a href='".$link."'>" . JText::_('new playlist')  ."</a>" ;
				$act->app		= 'myalbums';
				$act->cid		= $user->id;
				CActivityStream::add($act);
			
			}
				
			/*************/	
		}
		
		$link = JRoute::_('index.php?option=com_muscol&view=playlists' .  $itemid );

		$this->setRedirect( $link, $msg, $msg_type );
	}
	
	function save_playlist(){
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		
		if($user->id && $this->check_playlist_permission( JRequest::getVar('id') ) ){ // only can save playlists registered users
			$model = $this->getModel('playlist');
			if(!$model->save_playlist()) {
				$msg = JText::_( 'ERROR_SAVING_PLAYLIST' );
				$msg_type = "error";
			} else {
				$msg = JText::_( 'PLAYLIST_SAVED' );
				$msg_type = "message";
			}
		}
		else{
			$msg = JText::_( 'You are not allowed to edit this playlist' );
			$msg_type = "notice";
		}
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;

		$this->setRedirect( 'index.php?option=com_muscol&view=playlist&id=' . JRequest::getVar('id') .  $itemid, $msg, $msg_type );
	}
	
	function remove_playlist(){
		
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		
		if($user->id && $this->check_playlist_permission( JRequest::getVar('id') ) ){ // only can delete playlists registered users
			$model = $this->getModel('playlist');
			if(!$model->delete_playlist()) {
				$msg = JText::_( 'ERROR_ERASING_PLAYLIST' );
				$msg_type = "error";
			} else {
				$msg = JText::_( 'PLAYLIST_ERASED' );
				$msg_type = "message";
			}
		}
		else{
			$msg = JText::_( 'NOT_ALLOWED_EDIT_PLAYLIST' );
			$msg_type = "notice";
		}
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;

		$this->setRedirect( 'index.php?option=com_muscol&view=playlists' .  $itemid, $msg, $msg_type );
		
	}
	
	function edit_playlist()
	{
		$playlist_id = JRequest::getVar('id') ;
			
			if($this->check_playlist_permission($playlist_id)) JRequest::setVar( 'layout', 'form' );
			else {
				$params =JComponentHelper::getParams( 'com_muscol' );
				$itemid = $params->get('itemid');
				if($itemid != "") $itemid = "&Itemid=" . $itemid;
				$msg = JText::_('NOT_ALLOWED_EDIT_PLAYLIST');
				$msg_type = "notice";
				$this->setRedirect( 'index.php?option=com_muscol&view=playlist&id=' . $playlist_id .  $itemid, $msg, $msg_type );
			}
		
		parent::display();
	}
	
	function check_playlist_permission($playlist_id){
		if(!$playlist_id) return true; // everybody can edit his own on-the-go playlist
		
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$query = 'SELECT user_id FROM #__muscol_playlists WHERE id = ' . $playlist_id ;
		$db->setQuery($query);
		$playlist_creator = $db->loadResult();
		
		if($playlist_creator == $user->id) return true;
		else return false;
		
	}
	
	function remove_songs()
	{
		//this function is called only from the album form template
		$album_id = JRequest::getVar('id');
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		$model = $this->getModel('song');
		
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_DELETING_SONGS' );
		} else {
			$msg = JText::_( 'SONGS_DELETED' );
		}

		$this->setRedirect( 'index.php?option=com_muscol&view=album&layout=form&id=' . $album_id . $itemid, $msg );
	}
	
	function erase_album()
	{
		$model = $this->getModel('album');
		
		$id = JRequest::getVar('id');
		
		$confirm = JRequest::getVar('confirm');
		
		if($confirm){ // delete for real
		
			$params =JComponentHelper::getParams( 'com_muscol' );
			$itemid = $params->get('itemid');
			if($itemid != "") $itemid = "&Itemid=" . $itemid;
			
			$can_edit = $this->check_album_permission($id);
			$msg = JText::_('You are not authorized to delete this album');
			$link = JRoute::_('index.php?option=com_muscol&view=album&id='. $id . $itemid);
			
			if($can_edit){
						
				if(!$model->delete()) {
					$msg = JText::_( 'ERROR_DELETING_ALBUMS' );
				} else {
					$msg = JText::_( 'ALBUMS_DELETED' );
				}
				
				$link = JRoute::_('index.php?option=com_muscol&view=artists' . $itemid);
			
			}
			
			$this->setRedirect( $link , $msg );
		
		}
		else{ // ask for confirmation
		
			$can_edit = $this->check_album_permission($id);
			$msg = JText::_('NOT_AUTHORIZED_DELETE_ALBUM');
			$link = JRoute::_('index.php?option=com_muscol&view=album&id='. $id . $itemid);
			
			if($can_edit){
				
				echo '<div class="muscol_confirmation">'.JText::_('SURE_DELETE_ALBUM').'</div><br /><div class="muscol_confirmation_buttons"><a href="'.JRoute::_('index.php?option=com_muscol&task=erase_album&id=' . $id .'&confirm=1').'">'.JText::_('Delete').'</a> | <a href="'.JRoute::_('index.php?option=com_muscol&view=album&id='. $id . $itemid).'">'.JText::_('Cancel').'</a></div>' ;
			
			}
			else{
				$this->setRedirect( $link , $msg );
			}
			
		}
	}
	
	function erase_artist()
	{
		$model = $this->getModel('artist');
		
		$id = JRequest::getVar('id');
		
		$confirm = JRequest::getVar('confirm');
		
		if($confirm){ // delete for real
		
			$params =JComponentHelper::getParams( 'com_muscol' );
			$itemid = $params->get('itemid');
			if($itemid != "") $itemid = "&Itemid=" . $itemid;
			
			$can_edit = $this->check_artist_permission($id);
			$msg = JText::_('NOT_AUTHORIZED_DELETE_ARTIST');
			$link = JRoute::_('index.php?option=com_muscol&view=artist&id='. $id . $itemid);
			
			if($can_edit){
						
				if(!$model->delete()) {
					$msg = JText::_( 'ERROR_DELETING_ARTISTS' );
				} else {
					$msg = JText::_( 'ARTISTS_DELETED' );
				}
				
				$link = JRoute::_('index.php?option=com_muscol&view=artists' . $itemid);
			
			}
			
			$this->setRedirect( $link , $msg );
			
		}
		else{ // ask for confirmation
		
			$can_edit = $this->check_artist_permission($id);
			$msg = JText::_('NOT_AUTHORIZED_DELETE_ARTIST');
			$link = JRoute::_('index.php?option=com_muscol&view=artist&id='. $id . $itemid);
			
			if($can_edit){
						
				echo '<div class="muscol_confirmation">'.JText::_('SURE_DELETE_ARTIST').'</div><br /><div class="muscol_confirmation_buttons"><a href="'.JRoute::_('index.php?option=com_muscol&task=erase_artist&id=' . $id .'&confirm=1').'">'.JText::_('Delete').'</a> | <a href="'.JRoute::_('index.php?option=com_muscol&view=artist&id='. $id . $itemid).'">'.JText::_('Cancel').'</a></div>' ;
			
			}
			else{
				$this->setRedirect( $link , $msg );
			}
			
		}
	}
	
	function add_song_play_count(){
		
		$mainframe = JFactory::getApplication();
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		$id = JRequest::getInt('id');
		
		$model = $this->getModel('song');
		
		if($params->get('registersongplays'))  $model->getRegisterHit($id, 4); //4 is for "play song count"
		
		$mainframe->close() ;
	}
	
	function add_song_play_count_module(){
		
		$mainframe = JFactory::getApplication();
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		$pos = JRequest::getInt('pos');
		$db = JFactory::getDBO();
		
		
		$session =JSession::getInstance('','');
		$playlist_id = $session->get('current_playlist') ; // the playlist is an array
		
		if($playlist_id){
		$query = 'SELECT pl.songs FROM #__muscol_playlists AS pl WHERE pl.id = ' . $playlist_id ;
					;
			$db->setQuery($query);
			$songs_in_playlist = $db->loadResult();
			$playlist = explode(",", $songs_in_playlist) ;
		}
		else{
			$session =JSession::getInstance('','');
			$playlist = $session->get('muscol_playlist') ; // the playlist is an array	
			
		}
		
		$id = $playlist[$pos];
		
		$model = $this->getModel('song');
		
		if($params->get('registersongplays'))  $model->getRegisterHit($id, 4); //4 is for "play song count"
		
		$mainframe->close() ;
	}
	
	function thumbnail(){
		$mainframe = JFactory::getApplication();
		include(JPATH_SITE.DS.'components'.DS.'com_muscol'.DS.'helpers'.DS.'image_new.php');	
		$mainframe->close() ;
	}

	
	function search_albums(){
		parent::display();
	}
	
	function cancel()
	{
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		$msg = JText::_( 'OPERATION_CANCELLED' );
		
		$id = JRequest::getInt('id') ;
		$type = JRequest::getVar('type') ;

		if($id) $link = JRoute::_('index.php?option=com_muscol&view='.$type.'&id='.$id . $itemid, false) ;
		else $link = JRoute::_('index.php?option=com_muscol&view=artists&layout=detailed' . $itemid, false) ;
		
		$this->setRedirect($link, $msg);
	}
	
}
?>
