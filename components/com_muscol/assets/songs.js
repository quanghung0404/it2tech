
var new_songs = 0;
function new_song(){
	new_songs ++;
	
	var tbl = document.getElementById("songs_table");
	var newRow = tbl.insertRow(tbl.rows.length);
	var newCell = newRow.insertCell(0);
	
	newCell = newRow.insertCell(1);
	newCell.innerHTML = '<input class="inputbox disc_num input-mini" type="text" name="0_disc_num_' + new_songs + '" id="0_disc_num_' + new_songs + '" size="3" maxlength="3" />';
	
	newCell = newRow.insertCell(2);
	newCell.innerHTML = '<input class="inputbox song_num" type="text" name="0_num_' + new_songs + '" id="0_num_' + new_songs + '" size="3" maxlength="3" />';
	
	newCell = newRow.insertCell(3)
	newCell.innerHTML = '<input class="inputbox song_name" type="text" name="0_song_' + new_songs + '" id="0_song_' + new_songs + '" size="20" maxlength="250" />';
	
	newCell = newRow.insertCell(4);
	newCell.innerHTML = '<input class="inputbox filename" type="hidden" name="0_filename_' + new_songs + '" id="0_filename_' + new_songs + '"  /><div class="input-append"><input class="inputbox input-medium" id="0_song_file_' + new_songs + '_display" type="text" readonly="readonly" ><button class="btn btn-primary" onclick="jQuery(\'#0_song_file_' + new_songs + '\').click();" type="button">Select</button></div><input class="hidden" style="display:none" type="file" name="0_song_file_' + new_songs + '" id="0_song_file_' + new_songs + '" onchange="jQuery(\'#0_song_file_' + new_songs + '_display\').val(this.value)" />';
	newCell = newRow.insertCell(5);
	
	newCell = newRow.insertCell(6);
	

}

function delete_selected_songs(){
	
	document.adminForm.task.value = "remove_songs";
	document.adminForm.submit();
}
