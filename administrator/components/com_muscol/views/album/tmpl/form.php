<?php defined('_JEXEC') or die('Restricted access'); 

$params =JComponentHelper::getParams( 'com_muscol' );
JHTML::_('behavior.formvalidation');
?>
<script type="text/javascript">
/* Override joomla.javascript, as form-validation not work with ToolBar */
Joomla.submitbutton = function(pressbutton){
    if (pressbutton == 'cancel') {
        submitform(pressbutton);
    }else{
        var f = document.adminForm;
        if (document.formvalidator.isValid(f)) {
            //f.check.value='<?php echo JSession::getFormToken(); ?>'; //send token
            submitform(pressbutton);    
        }
    
    }    
}
</script>
<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a data-toggle="tab" href="#details"><?php echo JText::_( 'ALBUM_DETAILS' ); ?></a></li>
    <li><a data-toggle="tab" href="#songs"><?php echo JText::_( 'SONGS' ); ?></a></li>
    <li><a data-toggle="tab" href="#set"><?php echo JText::_( 'PART_OF_A_SET' ); ?></a></li>
  </ul>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal form-validate">
  
  <?php

echo JHtml::_('bootstrap.startPane', 'myTab', array('active' => 'details'));
echo JHtml::_('bootstrap.addPanel', 'myTab', 'details');

?>
  <div class="span6">
    <fieldset class="adminform">
      <legend><?php echo JText::_( 'Details' ); ?></legend>
      <div class="control-group">
        <label class="control-label"  for="name"> <?php echo JText::_( 'ALBUM_NAME' ); ?></label>
        <div class="controls">
          <input class="inputbox required" type="text" name="name" id="name" size="80" maxlength="250" value="<?php echo htmlspecialchars($this->album->name);?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="subtitle"> <?php echo JText::_( 'SUBTITLE' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="subtitle" id="subtitle" size="80" maxlength="250" value="<?php echo htmlspecialchars($this->album->subtitle);?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="artist"> <?php echo JText::_( 'ARTIST' ); ?></label>
        <div class="controls">
          <select class="chzn-select"  name="artist_id" id="artist_id">
            <?php
			for ($i=0, $n=count( $this->artists );$i < $n; $i++)	{
			$row =$this->artists[$i];
			$selected = ""; 
			if($row->id == $this->album->artist_id) $selected = "selected";?>
            <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->artist_name;?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="subartist"> <?php echo JText::_( 'SUBARTIST' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="subartist" id="subartist" size="80" maxlength="250" value="<?php echo htmlspecialchars($this->album->subartist);?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="format"> <?php echo JText::_( 'FORMAT' ); ?></label>
        <div class="controls">
          <select class="chzn-select"  name="format_id" id="format_id">
            <?php
			for ($i=0, $n=count( $this->formats );$i < $n; $i++)	{
			$row =$this->formats[$i];
			$selected = ""; 
			if($row->id == $this->album->format_id) $selected = "selected";?>
            <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->format_name;?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="ndisc"> <?php echo JText::_( 'N_DISCS' ); ?></label>
        <div class="controls">
          <input class="inputbox  input-mini" type="text" name="ndisc" id="ndisc" size="3" maxlength="3" value="<?php echo $this->album->ndisc;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="type"> <?php echo JText::_( 'YEAR' ); ?> / <?php echo JText::_( 'MONTH' ); ?></label>
        <div class="controls">
          <input class="inputbox input-mini" type="text" name="year" id="year" size="5" maxlength="4" value="<?php echo $this->album->year;?>" />
          <select class="chzn-select"  name="month" id="month">
            <option value="0"></option>
            <?php 
			for ($i=1; $i <= 12; $i++)	{
			$selected = ""; 
			if($i == $this->album->month) $selected = "selected";?>
            <option <?php echo $selected;?> value="<?php echo $i;?>"><?php echo $this->displayMonth($i);?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="genre"> <?php echo JText::_( 'GENRE' ); ?></label>
        <div class="controls">
          <select class="chzn-select"  name="genre_id" id="genre_id">
            <?php echo $this->show_genre_tree($this->genres,0); ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="tags"> <?php echo JText::_( 'TAGS' ); ?></label>
        <div class="controls">
          <input class="inputbox tm-input tm-input-info" type="text" name="tags" id="tags" value="" placeholder="<?php echo JText::_( 'TAGS_PLACEHOLDER' ); ?>" />
  
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="length"> <?php echo JText::_( 'LENGTH' ); ?></label>
        <div class="controls">
          <input class="inputbox input-mini" type="text" name="hours" id="hours" size="2" maxlength="2" value="<?php echo $this->album->hours;?>" />
          :
          <input class="inputbox input-mini" type="text" name="minuts" id="minuts" size="2" maxlength="2" value="<?php echo $this->album->minuts;?>" />
          :
          <input class="inputbox input-mini" type="text" name="seconds" id="seconds" size="2" maxlength="2" value="<?php echo $this->album->seconds;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="price"> <?php echo JText::_( 'PRICE' ); ?></label>
        <div class="controls">
          <input class="inputbox  input-mini" type="text" name="price" id="price" size="7" maxlength="8" value="<?php echo $this->album->price;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="album_file"> <?php echo JText::_( 'ALBUM_FILE' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="album_file" id="album_file" size="80" maxlength="250" value="<?php echo $this->album->album_file;?>" />
          <br />
          <span style="font-size:10px"><?php echo JText::_( 'Example' ); ?>: /images/complet_albums/albumXX.zip</span></div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="album_file"> <?php echo JText::_( 'BUY_LINK' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="buy_link" id="buy_link" size="80" maxlength="250" value="<?php echo $this->album->buy_link;?>" />
          <br />
          <span style="font-size:10px"><?php echo JText::_( 'EXTERNAL_LINK' ); ?></span></div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="keywords"> <?php echo JText::_( 'KEYWORDS' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="keywords" id="keywords" size="80" maxlength="250" value="<?php echo $this->album->keywords;?>" />
        </div>
      </div>
    </fieldset>
    <fieldset class="adminform">
      <legend><?php echo JText::_( 'EDITION_DETAILS' ); ?></legend>
      <div class="control-group">
        <label class="control-label"  for="type"> <?php echo JText::_( 'EDITION_DATE' ); ?></label>
        <div class="controls">
          <input class="inputbox  input-mini" type="text" name="edition_year" id="edition_year" size="5" maxlength="4" value="<?php echo $this->album->edition_year;?>" />
          <select class="chzn-select"  name="edition_month" id="edition_month">
            <option value="0"></option>
            <?php 
			for ($i=1; $i <= 12; $i++)	{
			$selected = ""; 
			if($i == $this->album->edition_month) $selected = "selected";?>
            <option <?php echo $selected;?> value="<?php echo $i;?>"><?php echo $this->displayMonth($i);?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="edition_country"> <?php echo JText::_( 'COUNTRY' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="edition_country" id="edition_country" size="80" maxlength="250" value="<?php echo $this->album->edition_country;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="label"> <?php echo JText::_( 'LABEL' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="label" id="label" size="80" maxlength="250" value="<?php echo $this->album->label;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="catalog_number"> <?php echo JText::_( 'CATALOG_NUMBER' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="catalog_number" id="catalog_number" size="80" maxlength="250" value="<?php echo $this->album->catalog_number;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="edition_details"> <?php echo JText::_( 'EDITION_DETAILS' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="edition_details" id="edition_details" size="80" maxlength="250" value="<?php echo $this->album->edition_details;?>" />
        </div>
      </div>
    </fieldset>
    <fieldset class="adminform">
      <legend><?php echo JText::_( 'EXTRA_INFORMATION' ); ?></legend>
      
      <div class="control-group">
        <label class="control-label"  for="type"> <?php echo JText::_( 'TYPES' ); ?></label>
        <div class="controls">
          <select class="chzn-select"  multiple="multiple" name="types[]" id="types">
            <?php
			for ($i=0, $n=count( $this->types );$i < $n; $i++)	{
			$row =$this->types[$i];
			$selected = ""; 
			if( in_array($row->id,$this->album->types) ) $selected = "selected";?>
            <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->type_name;?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      
    </fieldset>
    <fieldset class="adminform">
      <legend><?php echo JText::_( 'Metadata' ); ?></legend>
      <div class="control-group">
        <label class="control-label"  for="metakeywords"> <?php echo JText::_( 'META_KEYWORDS' ); ?></label>
        <div class="controls">
          <textarea name="metakeywords" id="metakeywords" cols="40" rows="4"><?php echo $this->album->metakeywords; ?></textarea>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="metadescription"> <?php echo JText::_( 'META_DESCRIPTION' ); ?></label>
        <div class="controls">
          <textarea name="metadescription" id="metadescription" cols="40" rows="4"><?php echo $this->album->metadescription; ?></textarea>
        </div>
      </div>
    </fieldset>
  </div>
  <div class="span6">
  <fieldset class="adminform">
    <legend><?php echo JText::_( 'PRIMARY_IMAGE' ); ?></legend>
    <div class="control-group">
      <label class="control-label"  for="image"> <?php echo JText::_( 'FRONT_SLEEVE' ); ?></label>
      <div class="controls">
        <input class="inputbox input-medium" type="text" name="image" id="image" size="50" maxlength="250" value="<?php echo $this->album->image;?>" />
        
        <div class="input-append">
          <input class="inputbox input-medium" id="image_file_display" type="text" readonly="readonly">
          <button class="btn btn-primary" onclick="jQuery('#image_file').click();" type="button"><?php echo JText::_( 'SELECT_TO_UPLOAD' ); ?></button>
        </div>

        <input class="hidden" type="file" name="image_file" id="image_file" onchange="jQuery('#image_file_display').val(this.value)" />

        <span class="help-inline">
        <?php echo JText::_('COVER_EXPLAIN'); ?>
      </span>

        <?php if($this->album->image){ ?>
        <br /><br />
        <img class="thumbnail" style="max-width:300px;" src="../images/albums/<?php echo $this->album->image;?>"/><?php } ?></div>
    </div>
  </fieldset>
  <fieldset class="adminform">
    <legend><?php echo JText::_( 'SECONDARY_IMAGES' ); ?></legend>
    <div class="control-group">
      <label class="control-label"  for="name2"> <?php echo JText::_( 'SUBS_IMAGE_ALBUM' ); ?></label>
      <div class="controls">
        <input class="inputbox input-medium" type="text" name="name2" id="name2" size="50" maxlength="250" value="<?php echo $this->album->name2;?>" />

        <div class="input-append">
          <input class="inputbox input-medium" id="name_image_file_display" type="text" readonly="readonly">
          <button class="btn btn-primary" onclick="jQuery('#name_image_file').click();" type="button"><?php echo JText::_( 'SELECT_TO_UPLOAD' ); ?></button>
        </div>

        <input class="hidden" style="display:none;" type="file" name="name_image_file" id="name_image_file" onchange="jQuery('#name_image_file_display').val(this.value)" />

        <?php
		if($this->album->name2 != "") {?>
        <img style="max-height:30px;" src="../images/album_extra/album_name/<?php echo $this->album->name2;?>"/>
        <?php } ?>
        
        <span class="help-inline"><?php echo JText::_('EMPTY_FIELD'); ?></span></div>
    </div>
    <div class="control-group">
    <label class="control-label"  for="artist2"> <?php echo JText::_( 'SUBS_IMAGE_ARTIST' ); ?></label>
    <div class="controls">
      <input class="inputbox input-medium" type="text" name="artist2" id="artist2" size="50" maxlength="250" value="<?php echo $this->album->artist2;?>" />

       <div class="input-append">
          <input class="inputbox input-medium" id="artist_image_file_display" type="text" readonly="readonly">
          <button class="btn btn-primary" onclick="jQuery('#artist_image_file').click();" type="button"><?php echo JText::_( 'SELECT_TO_UPLOAD' ); ?></button>
        </div>

        <input class="hidden" style="display:none;" type="file" name="artist_image_file" id="artist_image_file" onchange="jQuery('#artist_image_file_display').val(this.value)" />

      <?php
		if($this->album->artist2 != "") {?>
      <img style="max-height:30px;" src="../images/album_extra/artist_name/<?php echo $this->album->artist2;?>"/>
      <?php } ?>
      <span class="help-inline">
      <?php echo JText::_('EMPTY_FIELD'); ?>
    </span></div></div>
  </fieldset>
  <fieldset class="adminform">
    <legend><?php echo JText::_( 'REVIEW' ); ?></legend>
    <?php
            $editor = JFactory::getEditor();
            echo $editor->display('review', $this->album->review, '100%', '400', '60', '20', true);
        ?>
    
  </fieldset>
  </div>
  <?php
echo JHtml::_('bootstrap.endPanel');
echo JHtml::_('bootstrap.addPanel', 'myTab', 'songs');
?>
    
    <div class="alert">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?php echo JText::_('ADVICE_SONGS'); ?><br />
    <?php echo JText::_('MAX_FILESIZE_DEFINED') . ini_get('upload_max_filesize'); ?>
</div>

  <input class="btn" type="button" onclick="javascript:new_song();" value="<?php echo JText::_('ADD_NEW_SONG'); ?>"/>
  <input class="btn" type="button" onclick="javascript:delete_selected_songs();" value="<?php echo JText::_('DELETE_SELECTED_SONGS'); ?>"/>
  <br/>
  <br/>
  <table class="adminlist table table-striped" id="songs_table">
    <thead>
      <tr>
        <th class="hidden-phone" width="5"> <?php echo JText::_( 'ID' ); ?> </th>
        <th class="hidden-phone" width="20"> <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
        </th>
        <th class="hidden-phone" width="40"> <?php echo JText::_( 'DISC_NUM' ); ?> </th>
        <th class="hidden-phone" width="40"> <?php echo JText::_( 'SONG_NUM' ); ?> </th>
        <th class="hidden-phone" width="50"> <?php echo JText::_( 'POSITION' ); ?> </th>
        <th> <?php echo JText::_( 'NAME' ); ?> </th>
        <th> <?php echo JText::_( 'FILE' ); ?> </th>
        <th class="hidden-phone"> <?php echo JText::_( 'LENGTH' ); ?> </th>
        <th width="20"> <?php echo JText::_( 'EDIT' ); ?> </th>
        <th class="hidden-phone" width="20"> <?php echo JText::_( 'LYRICS' ); ?> </th>
        <th class="hidden-phone"> <?php echo JText::_( 'FILE' ); ?> </th>
      </tr>
    </thead>
    <?php
	$k = 0;
	for ($i=0, $n=count( $this->songs ); $i < $n; $i++)	{
		$song =$this->songs[$i];
		$checked 	= JHTML::_('grid.id',   $i, $song->id );
		$link_edit = JRoute::_( 'index.php?option=com_muscol&controller=song&task=edit&cid[]=' . $song->id );
		$tick = JHTML::image('administrator/components/com_muscol/assets/images/tick.png',JText::_('JYES'));
		$tick_file = JHTML::image("administrator/components/com_muscol/assets/images/tick.png",JText::_('JYES'),array("title" => $song->filename));
		$cross = JHTML::image('administrator/components/com_muscol/assets/images/publish_x.png',JText::_('JNO'));
		?>
    <tr class="<?php echo "row$k"; ?>">
      <td class="hidden-phone"><?php echo $song->id; ?></td>
      <td class="hidden-phone"><?php echo $checked; ?></td>
      <td class="hidden-phone"><input class="inputbox input-mini" type="text" name="disc_num_<?php echo $song->id;?>" id="disc_num_<?php echo $song->id;?>" size="3" maxlength="3" value="<?php echo $song->disc_num;?>" /></td>
      <td class="hidden-phone"><input class="inputbox input-mini" type="text" name="num_<?php echo $song->id;?>" id="num_<?php echo $song->id;?>" size="3" maxlength="3" value="<?php echo $song->num;?>" /></td>
      <td class="hidden-phone"><input class="inputbox input-mini" type="text" name="position_<?php echo $song->id;?>" id="position_<?php echo $song->id;?>" size="3" maxlength="6" value="<?php echo $song->position;?>" /></td>
      <td><input class="inputbox" type="text" name="song_<?php echo $song->id;?>" id="song_<?php echo $song->id;?>" size="50" maxlength="255" value="<?php echo $song->name;?>" /></td>
      <td><input class="inputbox input-medium" type="text" name="filename_<?php echo $song->id;?>" id="filename_<?php echo $song->id;?>" size="32" maxlength="255" value="<?php echo $song->filename;?>" />

        <div class="input-append">
          <input class="inputbox input-medium" id="song_file_<?php echo $song->id;?>_display" type="text" readonly="readonly">
          <button class="btn btn-primary" onclick="jQuery('#song_file_<?php echo $song->id;?>').click();" type="button"><?php echo JText::_( 'UPLOAD' ); ?></button>
        </div>

        <input class="hidden" type="file" name="song_file_<?php echo $song->id;?>" id="song_file_<?php echo $song->id;?>" onchange="jQuery('#song_file_<?php echo $song->id;?>_display').val(this.value)" />

      </td>
      <td class="hidden-phone"><input class="inputbox input-mini" type="text" name="hours_<?php echo $song->id;?>" id="hours_<?php echo $song->id;?>" size="2" maxlength="2" value="<?php echo $song->hours;?>" />
        :
        <input class="inputbox input-mini" type="text" name="minuts_<?php echo $song->id;?>" id="minuts_<?php echo $song->id;?>" size="2" maxlength="2" value="<?php echo $song->minuts;?>" />
        :
        <input class="inputbox input-mini" type="text" name="seconds_<?php echo $song->id;?>" id="seconds_<?php echo $song->id;?>" size="2" maxlength="2" value="<?php echo $song->seconds;?>" /></td>
      <td><a href="<?php echo $link_edit; ?>"><img src="components/com_muscol/assets/images/icons/page_white_edit.png" title="<?php echo JText::_( 'EDIT_THIS_SONG' ); ?>" alt="<?php echo JText::_( 'EDIT_THIS_SONG' ); ?>" /></a></td>
      <td class="hidden-phone"><?php if($song->lyrics == "") echo $cross; else echo $tick; ?></td>
      <td class="hidden-phone"><?php if($song->filename == "") echo $cross; else echo $tick_file; ?>
        <?php echo $song->filename; ?>
        <input type="hidden" name="artist_id_<?php echo $song->id;?>" id="artist_id_<?php echo $song->id;?>" value="<?php echo $song->artist_id;?>" />
        </td>
        <?php
		$k = 1 - $k;
	}
	?>
</tr>
  </table>
  <div id="discogs_release_tracklist"></div>
  <br />
  <br />
  
  <!--div class="input-append">
  	<input id="discogs_tracklist_searchterm" name="discogs_tracklist_searchterm"  class="inputbox" value="<?php echo $this->album->name; ?> <?php echo $this->album->artist_name; ?>" />
     <input class="btn" type="button" onclick="javascript:search_tracklist();" value="<?php echo JText::_('Search Tracklist on Discogs'); ?>" />
    </div-->
  
  <div id="return_discogs"></div>
  <?php
echo JHtml::_('bootstrap.endPanel');
echo JHtml::_('bootstrap.addPanel', 'myTab', 'set');
?>
  <fieldset class="adminform">
    <legend><?php echo JText::_( 'PART_OF_A_SET' ); ?></legend>
    <div class="control-group">
      <label class="control-label"  for="part_of_set"> <?php echo JText::_( 'SET' ); ?></label>
      <div class="controls">
        <select class="chzn-select"  name="part_of_set" id="part_of_set">
          <option value="0"><?php echo JText::_( 'NOT_PART_OF_A_SET' ); ?></option>
          <?php
			for ($i=0, $n=count( $this->albums );$i < $n; $i++)	{
			$row =$this->albums[$i];
			$selected = ""; 
			if($row->id == $this->album->part_of_set) $selected = "selected";?>
          <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->artist_name ." - ". $row->name ." (".$row->format_name . ")";?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="show_separately"> <?php echo JText::_( 'SHOW_ONLY_AS_PART_OF_THIS_SET' ); ?></label>
      <div class="controls">
        <input type="checkbox" name="show_separately" id="show_separately" <?php if($this->album->show_separately == 'N') echo " checked "; ?>>
      </div>
    </div>
  </fieldset>
  <?php
echo JHtml::_('bootstrap.endPanel');
echo JHtml::_('bootstrap.endPane', 'myTab');
?>
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="id" value="<?php echo $this->album->id; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="album" />
</form>
