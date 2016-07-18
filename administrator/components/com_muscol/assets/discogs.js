// JavaScript Document

jQuery(document).ready(function(){

	jQuery('#discogs_form').submit(function(e){
		
		e.preventDefault();
		
		var discogs = jQuery('#return_discogs');
		discogs.html('');
		
		
		var url = 'index.php?option=com_muscol&task=search_discogs&controller=albums&q='+jQuery('#query').val();
		
		
		jQuery.ajax({
			url: url,
			success: function(responseText, textStatus, jqXHR){
				discogs.html( responseText);
			}
		});
		
		return false;
	});
	
	
	jQuery('#folder_form').submit(function(e){
		
		e.preventDefault();
		
		var folder_scan = jQuery('#return_discogs');
		
		folder_scan.html('');
		
		
		var url = 'index.php?option=com_muscol&task=scan_folder&controller=albums&q='+jQuery('#folder').val();
		
		
		jQuery.ajax({
			url: url,
			success: function(responseText, textStatus, jqXHR){
				folder_scan.html( responseText);
			}
		});
		
		
	});
	
	
});

function process_id3_multiple(folder, start, num_files){

	var url = 'index.php?option=com_muscol&controller=albums&task=process_id3_multiple&folder='+ folder + '&start=' + start + '&num_files=' + num_files + '&ajax=1';
	
	jQuery.ajax({
		url: url,
		context: document.body
	}).done(function(responseText) {
		//console.log(responseText);
		obj = JSON.parse(responseText);

		if(obj.recursive){
			paint_bar(obj);
			
			process_id3_multiple(obj.folder, obj.start, obj.num_files);
		}
		else{
			paint_bar(obj);
		}

	});

}

function paint_bar(obj){
	var thebar = jQuery('#thebar');
	var thebarcontainer = jQuery('#thebarcontainer');
	var complete_tag = jQuery('#complete_tag');
	var imported = jQuery('#imported');
	var imported_files = jQuery('#imported_files');
	var remaining = jQuery('#remaining');
	var currently_importing = jQuery('#currently_importing');
	var return_button = jQuery('#return_button');
	var importing_p = jQuery('#importing_p');

	var percent = (obj.start / obj.total ) * 100;

	thebar.css('width',percent+'%');
	thebar.html(Math.round(percent)+'%');
	imported.html(obj.start);
	imported_files.html(obj.num_files);
	remaining.html(obj.total - obj.start);
	currently_importing.html(obj.importing);

	if((obj.total - obj.start) == 0 ){
		thebar.addClass('bar-success');
		thebarcontainer.removeClass('active').removeClass('progress-striped');
		complete_tag.removeClass('hidden');
		return_button.removeClass('hidden');
		importing_p.addClass('hidden');
	}
}

function search_tracklist(){
	
	var string_to_search = jQuery('#discogs_tracklist_searchterm').val() ;

	var url = "index.php?option=com_muscol&controller=albums&task=search_discogs&search=tracklist&q=" + string_to_search ;
	
	jQuery('#return_discogs').empty().addClass('ajax-loading');

	jQuery.ajax({
		url: url,
		success: function(responseText, textStatus, jqXHR){
			
			jQuery('#return_discogs').html(responseText);
		}
	});
	
}

function get_discogs_release(release_id){

	var url = "index.php?option=com_muscol&controller=albums&task=get_discogs_release&release_id=" + release_id;

	jQuery.ajax({
		url: url,
		success: function(responseText, textStatus, jqXHR){
			
			jQuery('#discogs_release_' + release_id).html(responseText);
		}
	});

	
}

function get_discogs_release_tracklist(release_id){

	var url = "index.php?option=com_muscol&controller=albums&task=get_discogs_release&search=tracklist&release_id=" + release_id;
	
	jQuery('#discogs_release_tracklist').empty().addClass('ajax-loading');

	jQuery.ajax({
		url: url,
		success: function(responseText, textStatus, jqXHR){
			
			jQuery('#discogs_release_tracklist').html(responseText);
			jQuery('#discogs_release_tracklist').removeClass('ajax-loading');
		}
	});

	
}

function scan_folder(string_to_search){
	
	var url = "index.php?option=com_muscol&controller=albums&task=scan_folder&folder=" + string_to_search ;
	
	jQuery('#return_discogs').html('');
	
	jQuery.ajax({
		url: url,
		success: function(responseText, textStatus, jqXHR){
			
			jQuery('#return_discogs').html(responseText);
		}
	});

	
}

function new_album_folder(folder){

	var url = "index.php?option=com_muscol&controller=albums&task=new_album_folder&folder=" + folder;
	
	jQuery.ajax({
		url: url,
		success: function(responseText, textStatus, jqXHR){
			
			jQuery('#return_new_album_folder').html(responseText);
		}
	});
	
	
}