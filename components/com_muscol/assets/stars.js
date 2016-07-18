function stars(star_num,album_id){
	var grey = "";//"_" + star_num;
	for(var i = 1; i<6;i++){
		if(i > star_num) grey = "_grey";
		document.getElementById("star" + i + "_" + album_id).src = star_icon_path + "star" + grey + ".png";
		
	}
}
function stars_out(album_id){
	var star_num = document.getElementById("stars_"+album_id).title;
	//alert(star_num);
	var grey = "";//"_" + star_num;
	for(var i = 1; i<6;i++){
		if(i > star_num) grey = "_grey";
		document.getElementById("star" + i + "_" + album_id).src = star_icon_path + "star" + grey + ".png";
		
	}
}
function canvia_estrelles(points,album_id){
	document.getElementById("points_form").value=points;
	  
	  stars(points,album_id);
}
function canvia_estrelles_out(album_id){
	var star_num = document.getElementById("points_form").value;
	
	var grey = "";//"_" + star_num;
	for(var i = 1; i<6;i++){
		if(i > star_num) grey = "_grey";
		document.getElementById("star" + i + "_" + album_id).src = star_icon_path + "star" + grey + ".png";
		
	}
}
function puntua(points,album_id){
  
  var url = "index.php?option=com_muscol&task=rate&points=" + points + "&album_id="+album_id ;
	  
	  jQuery.ajax({
		url: url,
		success: function(response, textStatus, jqXHR){
			document.getElementById("rating").style.display = "none";

	  		document.getElementById("messages").innerHTML = response;
		}
	});
	
  }