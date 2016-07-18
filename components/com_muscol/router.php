<?php

defined('_JEXEC') or die('Restricted access.');

function muscolBuildRoute( &$query ) {
	$segments									=	array();
	
	$database				=	JFactory::getDBO();
	
$format = isset($query['format']) ? $query['format'] : "" ;
$layout = isset($query['layout']) ? $query['layout'] : "" ;

	if ( isset( $query['view'] ) 
	&& $format != "feed"
	&& $format != "raw" 
	&& $format != "ownpdf" 
	&& $layout != "form") {
	
		$view									=	strtolower( $query['view'] );
		//$segments[]								=	$task;

		switch ( $view ) {
			case 'artists':
				if ( isset( $query['letter'] ) && $query['letter'] ) {
					
					$segments[]					=	$query['letter'];
					unset( $query['letter'] );
				}
				break;
			
			case 'artist':
				if ( isset( $query['id'] ) && $query['id'] ) {
				
						$sql = "SELECT id,letter,artist_name
									FROM #__muscol_artists
									WHERE id = ".$query["id"] ;
									
								$database->setQuery($sql);
								$result = $database->loadObject();
					
					
					$segments[]					=	$result->letter;
					$segments[]					=	$result->id . "-". JFilterOutput::stringURLSafe( $result->artist_name );
					
					unset( $query['id'] );
				}
				break;
				
			case 'songs':
				if ( isset( $query['id'] ) && $query['id'] ) {
				
						$sql = "SELECT id,letter,artist_name
									FROM #__muscol_artists
									WHERE id = ".$query["id"] ;
									
								$database->setQuery($sql);
								$result = $database->loadObject();
					
					
					$segments[]					=	$result->letter;
					$segments[]					=	$result->id . "-". JFilterOutput::stringURLSafe( $result->artist_name );
					$segments[]					= 'songs';
					
					unset( $query['id'] );
				}
				break;
			
			case 'album':
				if ( isset( $query['id'] ) && $query['id'] ) {
				
						$sql = "SELECT al.id, al.name, ar.letter, al.artist_id, ar.artist_name
								FROM #__muscol_albums as al
								LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id
								WHERE al.id = ".$query['id'] ;
									
								$database->setQuery($sql);
								$result = $database->loadObject();
					
					
					$segments[]					=	$result->letter;
					$segments[]					=	$result->artist_id . "-". JFilterOutput::stringURLSafe( $result->artist_name );
					$segments[]					=	$result->id . "-". JFilterOutput::stringURLSafe( $result->name );
					
					unset( $query['id'] );
				}
				break;
				
			case 'song':
				if ( isset( $query['id'] ) && $query['id'] ) {
				
						$sql = "SELECT s.id, s.name, s.album_id, al.name as album_name, ar.letter, ar.artist_name, al.artist_id
								FROM #__muscol_songs as s
								LEFT JOIN #__muscol_albums as al ON al.id = s.album_id
								LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id
								WHERE s.id = ".$query['id'] ;
									
								$database->setQuery($sql);
								$result = $database->loadObject();
					
					
					$segments[]					=	$result->letter;
					$segments[]					=	$result->artist_id . "-". JFilterOutput::stringURLSafe( $result->artist_name );
					$segments[]					=	$result->album_id . "-". JFilterOutput::stringURLSafe( $result->album_name );
					$segments[]					=	$result->id . "-". JFilterOutput::stringURLSafe( $result->name );
					
					unset( $query['id'] );
				}
				break;
				
			case 'playlist':
				if ( isset( $query['id'] ) && $query['id'] ) {
				
						$sql = "SELECT pl.*
								FROM #__muscol_playlists as pl
								WHERE pl.id = ".$query['id'] ;
									
								$database->setQuery($sql);
								$result = $database->loadObject();
					
					$segments[]					=	'playlist-' . $result->id . "-". JFilterOutput::stringURLSafe( $result->title );
					
					unset( $query['id'] );
				}
				else{
					$segments[]					=	'playlist-0' . "-". JFilterOutput::stringURLSafe( 'on-the-go' );
				}
				break;
				
			default:
				break;
		}
		if($view != "search") unset($query['view']);
		//unset($query['layout']);
	
	}

	return $segments;
}

function muscolParseRoute( $segments ) {
	$vars										=	array();

	//Get the active menu item
	// $menu									=	JSite::getMenu();
	// $item									=	$menu->getActive();
	//
	// if ( ! isset( $item ) ) {
		$database				=	JFactory::getDBO();
		
	$count										=	count( $segments );
	if ( $count > 0 ) {
		//$vars['view']							=	strtolower( $segments[0] );

		switch ( $count ) {
			case 1: // artists OR playlist
				
					$letter						=	$segments[0] ;
					
					$letter = explode(':',$letter);
					if($letter[0] == 'playlist'){
					
						$letter = explode('-',$letter[1]);
						$playlist = $letter[0];
						
						$playlist = explode(":", $playlist) ;
						$playlist = $playlist[0];
						
						$vars['view']				=	'playlist';
						$vars['id']				=	$playlist;
					}else{
						
						$vars['view']				=	'artists';
						$vars['letter']				=	$segments[0];
					}
			
				break;
			case 2: // artist
					$artist						=	$segments[1] ;
					$artist = explode("-", $artist) ;
					$artist = $artist[0];
					
					$artist = explode(":", $artist) ;
					$artist = $artist[0];
					
					$vars['view']				=	'artist';
					$vars['id']				=	$artist;
				
				break;
			case 3: // album OR songs
			
					if($segments[2] == "songs"){
						$artist						=	$segments[1] ;
						$artist = explode("-", $artist) ;
						$artist = $artist[0];
						
						$artist = explode(":", $artist) ;
						$artist = $artist[0];
						
						$vars['view']				=	'songs';
						$vars['id']				=	$artist;
					}
					else{
						$album						=	$segments[2] ;
						$album = explode("-", $album) ;
						$album = $album[0];
						
						$album = explode(":", $album) ;
						$album = $album[0];
						
						$vars['view']				=	'album';
						$vars['id']				=	$album;
					}
				
				break;
			
			case 4: // song
					$song						=	$segments[3] ;
					$song = explode("-", $song) ;
					$song = $song[0];
					
					$song = explode(":", $song) ;
					$song = $song[0];
					
					$vars['view']				=	'song';
					$vars['id']				=	$song;
				
				break;

			default:
				break;
		}
	}
	return $vars;
}

?>