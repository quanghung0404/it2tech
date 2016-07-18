// JavaScript Document
var album_player;
function sendajax_scroll(urlpassed,target, trigger) {
	
	urlpassed = site_base_path + urlpassed ;
	
	$(target).getElement('.contentEl').empty().addClass("ajax-loading");
	
	if(target == "fastnav_artists"){
		unselect_letters();
		$("fastnav_albums").getElement('.contentEl').empty();
		$("fastnav_songs").getElement('.contentEl').empty();
	}
	else if(target == "fastnav_albums"){
		unselect_artists();
		$("fastnav_songs").getElement('.contentEl').empty();
	}
	else if(target == "fastnav_songs"){
		unselect_albums();
	}
	
	$(trigger).addClass("selected");

	var req = new Request.HTML({
		url:urlpassed,
		method: "get",
	
		
		onSuccess: function(html) {
			
			
			$(target).getElement('.contentEl').removeClass("ajax-loading"); 
			
			
			//Clear the text currently inside the results div.
			$(target).getElement('.contentEl').set('text', '');
			//Inject the new DOM elements into the results div.
			$(target).getElement('.contentEl').adopt(html);
				
			
			if(target == "fastnav_artists"){

				scrollBox2.loadContent(html);

			}
			if(target == "fastnav_albums"){

				scrollBox3.loadContent(html);

			}
			
			else if(target == "fastnav_songs"){
				
				scrollBox4.loadContent(html);

				album_player = document.getElementById('album_player'); 
			}
			
			
			
		},
		onFailure: function(){$(target).adopt('Problem loading')}
		
	}).send();

	
}

function sendajax(urlpassed,target, trigger) {
	
	urlpassed = site_base_path + urlpassed ;
	
	$(target).empty().addClass("ajax-loading");
	
	if(target == "fastnav_artists"){
		unselect_letters();
		$("fastnav_albums").empty();
		$("fastnav_songs").empty();
	}
	else if(target == "fastnav_albums"){
		unselect_artists();
		$("fastnav_songs").empty();
	}
	else if(target == "fastnav_songs"){
		unselect_albums();
	}
	
	if(trigger.substr(0,5) != "song_") $(trigger).addClass("selected");

	var req = new Request.HTML({
		url:urlpassed,
		method: "get",
	
		
		onSuccess: function(html) {
			
			
			$(target).removeClass("ajax-loading"); 
			
			
			//Clear the text currently inside the results div.
			$(target).set('text', '');
			//Inject the new DOM elements into the results div.
			$(target).adopt(html);
				
			
			if(target == "fastnav_artists"){
				
				/*scroller_artists = new MooScroller($('fastnav_artists'), $('fastnav_artists_col').getElement('.scrollKnob'), {
					scrollLinks: {
						forward: $('fastnav_artists_col').getElement('.scrollForward'),
						back: $('fastnav_artists_col').getElement('.scrollBack')
					}
				});*/
				scroller_artists.update();
			

			}
			else if(target == "fastnav_albums"){

				/*scroller_albums = new MooScroller($('fastnav_albums'), $('fastnav_albums_col').getElement('.scrollKnob'), {
					scrollLinks: {
						forward: $('fastnav_albums_col').getElement('.scrollForward'),
						back: $('fastnav_albums_col').getElement('.scrollBack')
					}
				});*/
				scroller_albums.update();

			}
			
			else if(target == "fastnav_songs"){
				
				 /*scroller_songs = new MooScroller($('fastnav_songs'), $('fastnav_songs_col').getElement('.scrollKnob'), {
					scrollLinks: {
						forward: $('fastnav_songs_col').getElement('.scrollForward'),
						back: $('fastnav_songs_col').getElement('.scrollBack')
					}
				});*/
				 scroller_songs.update();

				//album_player = document.getElementById('album_player'); 
			}
			else if(target == "now_playing"){

				 /*scroller_now_playing = new MooScroller($('now_playing'), $('scroller_now_playing').getElement('.scrollKnob'), {
					scrollLinks: {
						forward: $('scroller_now_playing').getElement('.scrollForward'),
						back: $('scroller_now_playing').getElement('.scrollBack')
					}
				});*/
				 scroller_now_playing.update();
				$(trigger).addClass("selected");

			}
			
			
			
		},
		onFailure: function(){$(target).adopt('Problem loading')}
		
	}).send();

	
}

function sendajax_11(url,target, trigger) {
	
	url = site_base_path + url ;
	
	$(target).empty().addClass("ajax-loading");
	
	if(target == "fastnav_artists"){
		unselect_letters();
		$("fastnav_albums").empty();
		$("fastnav_songs").empty();
	}
	else if(target == "fastnav_albums"){
		unselect_artists();
		$("fastnav_songs").empty();
	}
	else if(target == "fastnav_songs"){
		unselect_albums();
	}
	
	$(trigger).addClass("selected");

	new Request(url, {
		method: "get",
		update: $(target),
					
		onComplete: function() {
			// when complete, we remove the spinner
			$(target).removeClass("ajax-loading"); 
			
			if(target == "fastnav_songs"){

				album_player = document.getElementById('album_player'); 
			}
			//completed("fastnav_artists");
		},

	}).send();
}

function setForm(){
	$('form_search_albums').addEvent('submit', function(e) {
		/**
		 * Prevent the submit event
		 */
		new Event(e).stop();
	 
		sendForm(this);
	
	
	});	
	
	$('fastnav_search_submit').addEvent('click', function(e) {
		sendForm($('form_search_albums'));
	
	});	

}

function sendForm(form){
	unselect_letters();
	$("fastnav_artists").empty();
	$("fastnav_songs").empty();
	
	var log = $('fastnav_albums').empty().addClass('ajax-loading');
	var boto = $('fastnav_search_submit').addClass('loading');
	
	form.set('send', {onComplete: function(response) { 
		log.removeClass('ajax-loading');
		boto.removeClass('loading');
		log.set('html', response);
		scroller_albums = new MooScroller($('fastnav_albums'), $('fastnav_albums_col').getElement('.scrollKnob'), {
								scrollLinks: {
									forward: $('fastnav_albums_col').getElement('.scrollForward'),
									back: $('fastnav_albums_col').getElement('.scrollBack')
								}
							});
	}});
	//Send the form.
	form.send();
}

function setForm_scroller(){
$('form_search_albums').addEvent('submit', function(e) {
	/**
	 * Prevent the submit event
	 */
	new Event(e).stop();
 
 	unselect_letters();
	$("fastnav_artists").getElement('.contentEl').empty();
	$("fastnav_songs").getElement('.contentEl').empty();
	
	var log = $('fastnav_albums').getElement('.contentEl').empty().addClass('ajax-loading');
 	
	this.set('send', {onComplete: function(response) { 
		log.removeClass('ajax-loading');
		log.set('html', response);
	}});
	//Send the form.
	this.send();


});	
}

function setForm_old(){
	$('form_search_albums').addEvent('submit', function(e) {
		/**
		 * Prevent the submit event
		 */
		new Event(e).stop();
	 
		unselect_letters();
		$("fastnav_artists").empty();
		$("fastnav_songs").empty();
		
		var log = $('fastnav_albums').empty().addClass('ajax-loading');
	 
		this.send({
			update: log,
			onComplete: function() {
				log.removeClass('ajax-loading');
			}
		});
	});	
}


function unselect_letters(){
	var letters = $$("#fastnav_letters .selected");

	letters.each(function(letter, i) {
		letter.removeClass("selected");
	});
}

function unselect_artists(){
	var artists = $$("#fastnav_artists .selected");

	artists.each(function(artist, i) {
		artist.removeClass("selected");
	});
}

function unselect_albums(){
	var albums = $$("#fastnav_albums .selected");

	albums.each(function(album, i) {
		album.removeClass("selected");
	});
}

function unselect_songs(){
	var songs = $$("#now_playing div.selected");

	songs.each(function(song, i) {
		song.removeClass("selected");
	});
}

function load_song_fastnav(filename){
	fastnav_player.sendEvent('STOP'); 	
	fastnav_player.sendEvent('LOAD',  	'http://localhost/jms/songs/' + filename );
	fastnav_player.sendEvent('PLAY'); 	
}

var item_to_play = 0;
var fastnav_current_playlist = "";
var now_playing_link = ""

function load_playlist_fastnav(playlist, item_num, now_playing, song_id){
	//fastnav_player.sendEvent('STOP'); 	
	if(fastnav_current_playlist != playlist){
		fastnav_player.sendEvent('LOAD',  playlist );
		fastnav_current_playlist = playlist ;
		item_to_play = item_num ;	
		setTimeout("play_item()",1000); // we wait one second for the playlist to load before play
	}
	else{
		item_to_play = item_num ;
		play_item();
	}
	
	if(now_playing_link != now_playing){
		sendajax(now_playing,'now_playing', song_id);
		now_playing_link = now_playing ;
	}
	else{//it was already loaded
		unselect_songs();
		$(song_id).addClass("selected");
		
	}
}

function play_item(){
	fastnav_player.sendEvent('ITEM',  item_to_play );
}

//ar player = null;
var currentPlaylist = null;
var currentLength = 0;
var currentItem = -1; 
var previousItem = -1; 
var currentMute = false; 
var currentVolume = 80; 
var currentPosition = 0; 
var currentState = 'NONE';
var currentLoaded = 0;
var currentRemain = 0;

function playerReady(thePlayer) {
	//player = window.document[thePlayer.id];
	
	addListeners();
}


function addListeners() {
	if (fastnav_player) { 
	
		fastnav_player.addControllerListener("ITEM", "itemListener");
		//alert("listener");
	} else {
		setTimeout("addListeners()",100);
	}
}

function itemListener(obj) { 
	if (obj.index != currentItem) {
 		previousItem = currentItem;
		currentItem = obj.index;

		//if (previousItem == -1) { getPlaylistData(); }
		
		//we select the song on the playlist
		unselect_songs();
		$("song_" + currentItem).addClass("selected");
		
		/*
		var tmp = document.getElementById("itm");
		if (tmp) { 
			tmp.innerHTML = "current item: " + currentItem +
				"<br>previous item: " + previousItem;
		}

		var tmp = document.getElementById("item");
		if (tmp) { tmp.innerHTML = "item: " + currentItem; }

		var tmp = document.getElementById("pid"); 
		if (tmp) { 
			tmp.innerHTML = "(received from the player with the id: <i><b>" + obj.id + "</b></i>)"; 
		} 
		*/

		printItemData(currentItem);
	}
}

var current_image_loaded = "";

function printItemData(theIndex) {
	var plst = null;
	plst = fastnav_player.getPlaylist();

	if (plst) {
		
				
		// we load the name on the div
		$('current_song_title').set('html', plst[theIndex].title);
		$('current_song_artist').set('html', plst[theIndex].author);
		
		var image_name = plst[theIndex].image.replace(site_base_path+'images/albums/', "");
		var width = "70" ;
		
		var image_url = site_base_path + 'components/com_muscol/helpers/image.php?file=' +site_base_filepath+site_separator+'images'+site_separator+'albums'+site_separator+ image_name +'&width=' + width ;
		
		if(current_image_loaded != image_name){
			$('song_thumbnail').empty().set('html', '<img src="' + image_url + '" />');
			current_image_loaded = image_name ;
		}
		
		/*
		var txt = '';
		txt += '<li><b>item number: </b>' + theIndex + ':</li>';
		txt += '<li><b>title: </b>' + plst[theIndex].title + '</li>';
		txt += '<li><b>author: </b>' + plst[theIndex].author + '</li>';
		txt += '<li><b>description: </b>' + plst[theIndex].description + '</li>';
		txt += '<li><b>file: </b>' + plst[theIndex].file + '</li>';
		txt += '<li><b>image: </b>' + plst[theIndex].image + '</li>';
		txt += '<li><b>link: </b><a href="' + plst[theIndex].link + '">' + plst[theIndex].link + '</a></li>';
		//txt += '<li><b>description: </b>' + plst[theIndex].description + '</li>';

		var tmp = document.getElementById("itmsDat");
		if (tmp) { tmp.innerHTML = txt; }
		*/
	} 	
}

function getPlaylistData() { 
	var plst = null;
	plst = fastnav_player.getPlaylist();

	if (plst) { 
		currentPlaylist = plst; 
	}	
	
}




var scroller_letters;
var scroller_artists;
var scroller_albums;
var scroller_songs;
var scroller_now_playing;

window.addEvent('domready', function(){
									 
	
	
	setForm();	
		
	 scroller_letters = new MooScroller($('fastnav_letters'), $('fastnav_letters_col').getElement('.scrollKnob'), {
			scrollLinks: {
				forward: $('fastnav_letters_col').getElement('.scrollForward'),
				back: $('fastnav_letters_col').getElement('.scrollBack')
			}
		});
	
	 scroller_artists = new MooScroller($('fastnav_artists'), $('fastnav_artists_col').getElement('.scrollKnob'), {
			scrollLinks: {
				forward: $('fastnav_artists_col').getElement('.scrollForward'),
				back: $('fastnav_artists_col').getElement('.scrollBack')
			}
		});
	 scroller_albums = new MooScroller($('fastnav_albums'), $('fastnav_albums_col').getElement('.scrollKnob'), {
			scrollLinks: {
				forward: $('fastnav_albums_col').getElement('.scrollForward'),
				back: $('fastnav_albums_col').getElement('.scrollBack')
			}
		});
	 scroller_songs = new MooScroller($('fastnav_songs'), $('fastnav_songs_col').getElement('.scrollKnob'), {
			scrollLinks: {
				forward: $('fastnav_songs_col').getElement('.scrollForward'),
				back: $('fastnav_songs_col').getElement('.scrollBack')
			}
		});
	 scroller_now_playing = new MooScroller($('now_playing'), $('scroller_now_playing').getElement('.scrollKnob'), {
			scrollLinks: {
				forward: $('scroller_now_playing').getElement('.scrollForward'),
				back: $('scroller_now_playing').getElement('.scrollBack')
			}
		});
	
	var toggle = new Fx.Slide('scroller_now_playing', {
		duration: 1000,
		transition: Fx.Transitions.Pow.easeOut
	});
	
	$('now_playing_toggle').addEvent('click', function(e){
		e = new Event(e);
		toggle.toggle();
		e.stop();
	});
	
	
});




function completed(){
	var box = $(target);
	var fx = new Fx.Style(box, "background-color", {
		duration: 800,
		transition: Fx.Transitions.Quad.easeOut
	}).start("#cccccc", "#eeeeee");
}

/* test cols resizable */
/*
window.addEvent('domready', function() {
			//	define column elemnts
			var col_wrap	=	$('col_wrapper');	//	define the column wrapper so as to be able to get the total width via mootools
			var col_left	=	$('col_1');
			var col_center	=	$('col_2');
			var col_right	=	$('col_3');
						
			//	define padding (seperator line widths) for column borders as defined in css
			var pad			= 	1; 
			
			//	define snap if required - set to 0 for no snap
			var w_snap		=	5;
			
			var w_total		=	col_wrap.getWidth()-(pad*2);	// total width of wrapper
			var w_min		=	120;							//	minimum width for columns
			var w_min_c		=	w_min-(2*pad);
			
			//	define message output elements (not essential to script)
			var col_1_msg	=	$("col_1_msg");
			var col_2_msg	=	$("col_2_msg");
			var col_3_msg	=	$("col_3_msg");
			
			//show column start widths in col headers (just for show)
			col_1_msg.innerHTML	= col_left.getWidth()+"px";
			col_2_msg.innerHTML	= col_center.getWidth()+"px";
			col_3_msg.innerHTML	= col_right.getWidth()+"px";
			

			
			//	left column - affects center column position and width
			col_left.makeResizable({
				handle: col_left.getChildren('.resize'),
				grid: w_snap,
				modifiers: {x: 'width', y: false},
				limit: {x: [w_min,null]},
				
				
				onStart:function(el){
					//	get available width - total width minus right column - minimum col with
					w_avail=(w_total-col_right.getWidth())-w_min;
				},
				onDrag: function(el) {
					if(el.getWidth()>=w_avail){
						//	max width reached - stop drag (force max widths)
						el.setStyle("width",w_avail);
					}
					
					//	set center col left position
					col_center.setStyle("left",col_left.getWidth());
					
					//	define and set center col width (total minus left minus right)
					w_center=w_total-col_left.getWidth()-col_right.getWidth();
					col_center.setStyle("width",w_center.toInt()-(pad*2));
					
					//	messages
					col_1_msg.innerHTML=" "+col_left.getWidth()+"px";
					col_2_msg.innerHTML=" "+col_center.getWidth()+"px";
					col_3_msg.innerHTML=" "+col_right.getWidth()+"px";
				}, 
				onComplete: function() {
					//could add final width to form field here
				}
			});
			
			// mootools can't resize to the left so we have to resize the center column rather than the right-hand column
			col_center.makeResizable({
				handle: col_center.getChildren('.resize'),
				grid: w_snap,
				modifiers: {x: 'width', y: false},
				limit: {x: [w_min_c,null]},
				
				
				
				onStart:function(el){
					//	get start width so as to be able to adjust center column width
					w_start=el.getWidth();						
					
					//	get available width - total width minus left column - minimum col with
					w_avail=w_total-col_left.getWidth()-w_min-(pad*2);
				},
				onDrag: function(el) {
					if(el.getWidth()>=w_avail){
						//	max width reached - stop drag (force max widths)
						el.setStyle("width",w_avail);
					}else if(el.getWidth()==w_min_c){
						//	ensure that right col has complete available width
						el.setStyle("width",w_min_c);
					}
					
					
					// define new left position
					l_new = col_left.getWidth()+col_center.getWidth();	//	force left space for right col
					col_right.setStyle("left",l_new.toInt());
					
					
					//	define and set right column width -  will always be result of left and center columns
					w_new = w_total-col_left.getWidth()-col_center.getWidth();
					col_right.setStyle("width",w_new.toInt());
					
					//	show messages
					col_1_msg.innerHTML=" "+col_left.getWidth()+"px";
					col_2_msg.innerHTML=" "+col_center.getWidth()+"px";
					col_3_msg.innerHTML=" "+col_right.getWidth()+"px";
				}, 
				onComplete: function() {
					//could add final width to form field here
				}
				
			});
		});
*/