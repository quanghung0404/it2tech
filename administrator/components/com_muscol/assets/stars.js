// JavaScript Document

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

function stars(star_num,album_id){
	var grey = "";//"_" + star_num;
	for(var i = 1; i<6;i++){
		if(i > star_num) grey = "_grey";
		document.getElementById("star" + i + "_" + album_id).src = "components/com_muscol/assets/images/star" + grey + ".png";
		
	}
}
function stars_out(album_id){
	var star_num = document.getElementById("stars_"+album_id).title;
	//alert(star_num);
	var grey = "";//"_" + star_num;
	for(var i = 1; i<6;i++){
		if(i > star_num) grey = "_grey";
		document.getElementById("star" + i + "_" + album_id).src = "components/com_muscol/assets/images/star" + grey + ".png";
		
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
		document.getElementById("star" + i + "_" + album_id).src = "components/com_muscol/assets/images/star" + grey + ".png";
		
	}
}
function puntua(points,album_id)
{
var xmlHttp;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    try
      {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
    catch (e)
      {
      alert("Your browser does not support AJAX!");
      return false;
      }
    }
  }
  xmlHttp.onreadystatechange=function()
    {
    if(xmlHttp.readyState==4)
      {
	  //alert(points+" "+album_id);
      document.getElementById("stars_"+album_id).title=points;
	  stars(points,album_id);
	  //alert(xmlHttp.responseText);
	  document.getElementById("messages").innerHTML = xmlHttp.responseText;
	  //document.getElementById("missatges").style.display = "block";*/
      }
    }
  //xmlHttp.open("GET","puntua.php?points="+points+"&album_id="+album_id,true);
  xmlHttp.open("GET","index.php?option=com_muscol&controller=albums&task=rate&points=" + points + "&album_id="+album_id,true);
  xmlHttp.send(null);
  }