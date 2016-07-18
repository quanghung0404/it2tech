// JavaScript Document

/**
 * @version		2.0.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

var new_songs = 0;
function new_song(){
	new_songs ++;
	/*var container = document.getElementById("new_songs");
	
	var inner = '<div><input class="text_area" type="text" name="0_disc_num_' + new_songs + '" id="0_disc_num_' + new_songs + '" size="3" maxlength="3" /> <input class="text_area" type="text" name="0_num_' + new_songs + '" id="0_num_' + new_songs + '" size="3" maxlength="3" /> <input class="text_area" type="text" name="0_song_' + new_songs + '" id="0_song_' + new_songs + '" size="100" maxlength="250" /></div>';
	
	container.innerHTML += inner;
	*/
	var tbl = document.getElementById("songs_table");
	var newRow = tbl.insertRow(tbl.rows.length);
	var newCell = newRow.insertCell(0);
	
	newCell = newRow.insertCell(1);
	
	newCell = newRow.insertCell(2);
	newCell.innerHTML = '<input class="inputbox input-mini" type="text" name="0_disc_num_' + new_songs + '" id="0_disc_num_' + new_songs + '" size="3" maxlength="3" />';
	
	newCell = newRow.insertCell(3);
	newCell.innerHTML = '<input class="inputbox input-mini" type="text" name="0_num_' + new_songs + '" id="0_num_' + new_songs + '" size="3" maxlength="3" />';
	
	newCell = newRow.insertCell(4);
	newCell.innerHTML = '<input class="inputbox input-mini" type="text" name="0_position_' + new_songs + '" id="0_position_' + new_songs + '" size="3" maxlength="6" />';
	
	newCell = newRow.insertCell(5);
	newCell.innerHTML = '<input class="text_area" type="text" name="0_song_' + new_songs + '" id="0_song_' + new_songs + '" size="100" maxlength="250" />';
	
	newCell = newRow.insertCell(6);
	//newCell.innerHTML = '<input class="text_area" type="text" name="0_filename_' + new_songs + '" id="0_filename_' + new_songs + '" size="32" maxlength="255" /><input class="hidden" type="file" name="0_song_file_' + new_songs + '" id="0_song_file_' + new_songs + '" onchange="jQuery(\'#0_song_file_' + new_songs + '_display\').val(this.value)" />';
	newCell.innerHTML ='<input class="inputbox input-medium" type="text" name="0_filename_' + new_songs + '" id="0_filename_' + new_songs + '" size="32" maxlength="255" /> <div class="input-append"><input class="inputbox input-medium" id="0_song_file_' + new_songs + '_display" type="text" readonly="readonly"><button class="btn btn-primary" onclick="jQuery(\'#0_song_file_' + new_songs + '\').click();" type="button">Upload</button></div><input class="hidden" style="display:none;" type="file" name="0_song_file_' + new_songs + '" id="0_song_file_' + new_songs + '" onchange="jQuery(\'#0_song_file_' + new_songs + '_display\').val(this.value)" />';

	newCell = newRow.insertCell(7);
	newCell.innerHTML = '<input class="inputbox input-mini" type="text" name="0_hours_' + new_songs + '" id="0_hours_' + new_songs + '" size="2" maxlength="2" /> : <input class="inputbox input-mini" type="text" name="0_minuts_' + new_songs + '" id="0_minuts_' + new_songs + '" size="2" maxlength="2" /> : <input class="inputbox input-mini" type="text" name="0_seconds_' + new_songs + '" id="0_seconds_' + new_songs + '" size="2" maxlength="2" />';
	
	newCell = newRow.insertCell(8);
	newCell = newRow.insertCell(9);
	newCell = newRow.insertCell(10);

}

function delete_selected_songs(){
	
	document.adminForm.controller.value = "song";
	document.adminForm.task.value = "remove";
	document.adminForm.submit();
}
