<?php defined('_JEXEC') or die('Restricted access'); 

JHtmlBehavior::framework();
JHTML::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', '.chzn-select');

$document = JFactory::getDocument();

$document->addScript('components/com_muscol/assets/validate.js');

$document->addScriptDeclaration("
function newartist(){
	$('artist_id').style.display = 'none';
	$('newartist').style.display = 'inline';
	$('newartist').focus();
	$('explanationartist').innerHTML = '<a href=\"javascript:pickartist()\">". JText::_( 'PICK_EXISTING_ARTIST' )."</a> ". JText::_( 'OR' )." ". JText::_( 'ADD_NEW_ARTIST' ).".' ;
}
function pickartist(){
	$('newartist').style.display = 'none';
	$('artist_id').style.display = 'inline';
	$('artist_id').focus();
	$('explanationartist').innerHTML = '". JText::_( 'PICK_EXISTING_ARTIST' )." ". JText::_( 'OR' )." <a href=\"javascript:newartist()\">". JText::_( 'ADD_NEW_ARTIST' )."</a>.' ;
}
");


?>

<div class="page-header">
  <h1><?php echo $this->album->id ? $this->album->name ." <small>[".JText::_('EDIT')."]</small>" : JText::_('NEW_ALBUM'); ?></h1>
</div>
<div class="editalbum">
  <form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-validate form-horizontal">
    <fieldset >
      <legend><?php echo JText::_( 'BASIC_DETAILS' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="name"> <?php echo JText::_( 'ALBUM_NAME' ); ?></label>
        <div class="controls">
          <input class="inputbox required" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo htmlspecialchars($this->album->name);?>" />
          <?php if(JText::_( 'ALBUM_NAME_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_NAME_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="subtitle"> <?php echo JText::_( 'ALBUM_SUBTITLE' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="subtitle" id="subtitle" size="50" maxlength="250" value="<?php echo htmlspecialchars($this->album->subtitle);?>" />
          <?php if(JText::_( 'ALBUM_SUBTITLE_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_SUBTITLE_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="artist_id"> <?php echo JText::_( 'ARTIST' ); ?></label>
        <div class="controls">
          <select name="artist_id" id="artist_id" >
            <?php
			for ($i=0, $n=count( $this->artists );$i < $n; $i++)	{
			$row =$this->artists[$i];
			$selected = ""; 
			if($row->id == $this->album->artist_id) $selected = "selected";?>
            <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->artist_name;?></option>
            <?php } ?>
          </select>
          <input class="inputbox" type="text" name="newartist" id="newartist" size="50" maxlength="250" value="" style="display:none;" />
          <span class="help-inline" id="explanationartist"><?php echo JText::_( 'PICK_EXISTING_ARTIST' ); ?> <?php echo JText::_( 'OR' ); ?> <a href="javascript:newartist()"><?php echo JText::_( 'ADD_NEW_ARTIST' ); ?></a>.</span> </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="subartist"> <?php echo JText::_( 'SUBARTIST' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="subartist" id="subartist" size="50" maxlength="250" value="<?php echo htmlspecialchars($this->album->subartist);?>" />
          <?php if(JText::_( 'ALBUM_SUBARTIST_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_SUBARTIST_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="format_id"> <?php echo JText::_( 'FORMAT' ); ?></label>
        <div class="controls">
          <select name="format_id" id="format_id"  class="chzn-select">
            <?php
			for ($i=0, $n=count( $this->formats );$i < $n; $i++)	{
			$row =$this->formats[$i];
			$selected = ""; 
			if($row->id == $this->album->format_id) $selected = "selected";?>
            <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->format_name;?></option>
            <?php } ?>
          </select>
          <?php if(JText::_( 'ALBUM_FORMAT_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_FORMAT_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="year"> <?php echo JText::_( 'YEAR' ); ?> / <?php echo JText::_( 'MONTH' ); ?></label>
        <div class="controls">
          <input class="inputbox input-mini" type="text" name="year" id="year" size="5" maxlength="4" value="<?php echo $this->album->year;?>" />
          <select name="month" id="month" class="chzn-select">
            <option value="0"></option>
            <?php 
			for ($i=1; $i <= 12; $i++)	{
			$selected = ""; 
			if($i == $this->album->month) $selected = "selected";?>
            <option <?php echo $selected;?> value="<?php echo $i;?>"><?php echo MusColHelper::month_name($i); ?></option>
            <?php } ?>
          </select>
          <?php if(JText::_( 'ALBUM_YEAR_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_YEAR_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="genre_id"> <?php echo JText::_( 'GENRE' ); ?></label>
        <div class="controls">
          <select name="genre_id" id="genre_id" class="chzn-select">
            <?php echo MusColHelper::show_genre_tree($this->genres,0, $this->album->genre_id); ?>
          </select>
          <?php if(JText::_( 'ALBUM_GENRE_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_GENRE_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="tags"> <?php echo JText::_( 'TAGS' ); ?> </label>
        <div class="controls">
          <input class="inputbox input-medium tm-input tm-input-info" type="text" name="tags" id="tags" value="" placeholder="<?php echo JText::_( 'TAGS_PLACEHOLDER' ); ?>" />
  
        </div>
        <?php if(JText::_( 'ALBUM_TAGS_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_TAGS_EXPLANATION' ); ?></span>
          <?php } ?>
      </div>
      <div class="control-group">
        <label class="control-label" for="image"> <?php echo JText::_( 'ALBUM_PICTURE' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="image" id="image" size="30" maxlength="250" value="<?php echo $this->album->image;?>" />
          <div class="input-append">
            <input class="inputbox input-medium" id="image_file_display" type="text" readonly="readonly">
            <button class="btn btn-primary" onclick="jQuery('#image_file').click();" type="button"><?php echo JText::_( 'UPLOAD' ); ?></button>
          </div>
          <input class="hidden" style="display:none" type="file" name="image_file" id="image_file" onchange="jQuery('#image_file_display').val(this.value)" />

          <?php if(JText::_( 'ALBUM_PICTURE_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_PICTURE_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <?php
			if($this->album->image != "") {?>
      <div class="control-group">
        <div class="controls"><?php echo JHTML::image("images/albums/".$this->album->image, JText::_('PICTURE'), array("class"=>"artistpictureform thumbnail")); ?></div>
      </div>
      <?php } ?>
    </fieldset>
    <fieldset >
      <legend><?php echo JText::_( 'SONGS' ); ?></legend>
      <input class="btn" type="button" onclick="javascript:new_song();" value="<?php echo JText::_('ADD_NEW_SONG'); ?>"/>
      <input class="btn" type="button" onclick="javascript:delete_selected_songs();" value="<?php echo JText::_('DELETE_SELECTED_SONGS'); ?>"/>
      <br/>
      <br/>
      <table class="table table-striped" id="songs_table" cellpadding="0" cellspacing="0">
        <thead>
          <tr>
            <th width="20"> <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
            </th>
            <th width="40" class="hidden-phone"> <?php echo JText::_( 'SONG_DISC' ); ?> </th>
            <th width="40" class="hidden-phone"> <?php echo JText::_( 'SONG_NUM' ); ?> </th>
            <!--th width="50"> <?php echo JText::_( 'SONG_POSITION' ); ?> </th-->
            <th> <?php echo JText::_( 'SONG_NAME' ); ?> </th>
            <th > <?php echo JText::_( 'SONG_FILE' ); ?> </th>
            <th width="125" class="hidden"> <?php echo JText::_( 'SONG_LENGTH' ); ?> </th>
            <th width="20"> <?php echo JText::_( 'SONG_EDIT' ); ?> </th>
          </tr>
        </thead>
        <?php
	$k = 0;
	for ($i=0, $n=count( $this->songs ); $i < $n; $i++)	{
		$song =$this->songs[$i];
		$checked 	= JHTML::_('grid.id',   $i, $song->id );
		$link_edit = JRoute::_( 'index.php?option=com_muscol&view=song&layout=form&id=' . $song->id .'&from=album');
		$tick = JHTML::image("administrator/images/tick.png",JText::_('Yes'));
		$tick_file = JHTML::image("administrator/images/tick.png",JText::_('Yes'),array("title" => $song->filename));
		$cross = JHTML::image("administrator/images/publish_x.png",JText::_('No'));
		?>
        <tr class="<?php echo "row$k"; ?>">
          <td><?php echo $checked; ?></td>
          <td class="hidden-phone"><input class="inputbox disc_num input-mini" type="text" name="disc_num_<?php echo $song->id;?>" id="disc_num_<?php echo $song->id;?>" size="3" maxlength="3" value="<?php echo $song->disc_num;?>" /></td>
          <td class="hidden-phone"><input class="inputbox  song_num input-mini" type="text" name="num_<?php echo $song->id;?>" id="num_<?php echo $song->id;?>" size="3" maxlength="3" value="<?php echo $song->num;?>" /></td>
          <td><input class="song_inputbox song_name " type="text" name="song_<?php echo $song->id;?>" id="song_<?php echo $song->id;?>" size="20" maxlength="255" value="<?php echo htmlspecialchars($song->name);?>" /></td>
          <td>
            <input class="inputbox  input-mini filename" type="hidden" name="filename_<?php echo $song->id;?>" id="filename_<?php echo $song->id;?>" value="<?php echo $song->filename;?>" />
            
            <div class="input-append">
              <input class="inputbox input-medium" id="song_file_<?php echo $song->id;?>_display" type="text" readonly="readonly" value="<?php echo $song->filename;?>">
              <button class="btn btn-primary" onclick="jQuery('#song_file_<?php echo $song->id;?>').click();" type="button"><?php echo JText::_( 'UPLOAD' ); ?></button>
            </div>
            <input class="hidden" style="display:none" type="file" name="song_file_<?php echo $song->id;?>" id="song_file_<?php echo $song->id;?>" onchange="jQuery('#song_file_<?php echo $song->id;?>_display').val(this.value)" />

          </td>
          <td class="hidden"><input class=" hours inputbox input-mini" type="text" name="hours_<?php echo $song->id;?>" id="hours_<?php echo $song->id;?>" size="2" maxlength="2" value="<?php echo $song->hours;?>" />
            :
            <input class=" minuts inputbox input-mini" type="text" name="minuts_<?php echo $song->id;?>" id="minuts_<?php echo $song->id;?>" size="2" maxlength="2" value="<?php echo $song->minuts;?>" />
            :
            <input class=" seconds inputbox input-mini" type="text" name="seconds_<?php echo $song->id;?>" id="seconds_<?php echo $song->id;?>" size="2" maxlength="2" value="<?php echo $song->seconds;?>" /></td>
          <td><input type="hidden" name="artist_id_<?php echo $song->id;?>" id="artist_id_<?php echo $song->id;?>" value="<?php echo $song->artist_id;?>" />
            <a href="<?php echo $link_edit; ?>"><i class="icon-pencil" title="<?php echo JText::_( 'EDIT_THIS_SONG' ); ?>"></i></a></td>
        </tr>
        <?php
		$k = 1 - $k;
	}
	?>
      </table>
      <div class="alert">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php echo JText::_('ADVICE_SONGS'); ?><br />
        <?php echo JText::_('MAX_FILE_SIZE') . ini_get('upload_max_filesize'); ?><br />
        <?php echo JText::_('MAX_TOTAL_SIZE') . ini_get('post_max_size'); ?> </div>
    </fieldset>
    <fieldset >
      <legend><?php echo JText::_( 'EXTENDED_DETAILS' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="ndisc"> <?php echo JText::_( 'NUMBER_OF_DISCS' ); ?></label>
        <div class="controls">
          <input class="inputbox input-mini" type="text" name="ndisc" id="ndisc" size="3" maxlength="3" value="<?php echo $this->album->ndisc;?>" />
          <?php if(JText::_( 'ALBUM_NUMBER_OF_DISCS_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_NUMBER_OF_DISCS_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="hours"> <?php echo JText::_( 'LENGTH' ); ?></label>
        <div class="controls">
          <input class="inputbox input-mini" type="text" name="hours" id="hours" size="2" maxlength="2" value="<?php echo $this->album->hours;?>" />
          :
          <input class="inputbox input-mini" type="text" name="minuts" id="minuts" size="2" maxlength="2" value="<?php echo $this->album->minuts;?>" />
          :
          <input class="inputbox input-mini" type="text" name="seconds" id="seconds" size="2" maxlength="2" value="<?php echo $this->album->seconds;?>" />
          <?php if(JText::_( 'ALBUM_LENGTH_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_LENGTH_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="price"> <?php echo JText::_( 'PRICE' ); ?></label>
        <div class="controls">
          <input class="inputbox input-mini" type="text" name="price" id="price" size="7" maxlength="8" value="<?php echo $this->album->price;?>" />
          <?php if(JText::_( 'ALBUM_PRICE_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_PRICE_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="types"> <?php echo JText::_( 'TYPES' ); ?></label>
        <div class="controls">
          <select multiple="multiple" name="types[]" id="types" size="4" class="chzn-select">
            <?php
			for ($i=0, $n=count( $this->types );$i < $n; $i++)	{
			$row =$this->types[$i];
			$selected = ""; 
			if( in_array($row->id,$this->album->types_original) ) $selected = "selected";?>
            <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->type_name;?></option>
            <?php } ?>
          </select>
          <?php if(JText::_( 'ALBUM_TYPES_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_TYPES_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      
      <div class="control-group">
        <label class="control-label" for="points"> <?php echo JText::_( 'RATING' ); ?></label>
        <div class="controls">
          <select name="points" id="points"  class="chzn-select">
            <option value="0"></option>
            <?php for($i = 1; $i < 6; $i++){ 
			if($i == $this->album->points) $selected = "selected"; else $selected = ""; ?>
            <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
            <?php } ?>
          </select>
          <?php if(JText::_( 'ALBUM_RATING_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_RATING_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="buy_link"> <?php echo JText::_( 'BUY_LINK' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="buy_link" id="buy_link" size="50" maxlength="250" value="<?php echo $this->album->buy_link;?>" />
          <?php if(JText::_( 'BUY_LINK_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'BUY_LINK_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
    </fieldset>
    <fieldset >
      <legend><?php echo JText::_( 'EDITION_DETAILS' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="edition_year"> <?php echo JText::_( 'EDITION_DATE' ); ?></label>
        <div class="controls">
          <input class="inputbox input-mini" type="text" name="edition_year" id="edition_year" size="5" maxlength="4" value="<?php echo $this->album->edition_year;?>" />
          <select name="edition_month" id="edition_month"  class="chzn-select">
            <option value="0"></option>
            <?php 
			for ($i=1; $i <= 12; $i++)	{
			$selected = ""; 
			if($i == $this->album->edition_month) $selected = "selected";?>
            <option <?php echo $selected;?> value="<?php echo $i;?>"><?php echo MusColHelper::month_name($i);?></option>
            <?php } ?>
          </select>
          <?php if(JText::_( 'ALBUM_EDITION_DATE_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_EDITION_DATE_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="edition_country"> <?php echo JText::_( 'COUNTRY' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="edition_country" id="edition_country" size="50" maxlength="250" value="<?php echo $this->album->edition_country;?>" />
          <?php if(JText::_( 'ALBUM_COUNTRY_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_COUNTRY_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="label"> <?php echo JText::_( 'LABEL' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="label" id="label" size="50" maxlength="250" value="<?php echo $this->album->label;?>" />
          <?php if(JText::_( 'ALBUM_LABEL_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_LABEL_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="catalog_number"> <?php echo JText::_( 'CATALOG_NUMBER' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="catalog_number" id="catalog_number" size="50" maxlength="250" value="<?php echo $this->album->catalog_number;?>" />
          <?php if(JText::_( 'ALBUM_CATALOG_NUMBER_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_CATALOG_NUMBER_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="edition_details"> <?php echo JText::_( 'EDITION_DETAILS' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="edition_details" id="edition_details" size="50" maxlength="250" value="<?php echo $this->album->edition_details;?>" />
          <?php if(JText::_( 'ALBUM_EDITION_DETAILS_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_EDITION_DETAILS_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
    </fieldset>
    <fieldset >
      <legend><?php echo JText::_( 'EXTRA_IMAGES' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="name2"> <?php echo JText::_( 'IMAGE_FOR_ALBUM_NAME' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="name2" id="name2" size="30" maxlength="250" value="<?php echo $this->album->name2;?>" />
          
          <div class="input-append">
            <input class="inputbox input-medium" id="name_image_file_display" type="text" readonly="readonly">
            <button class="btn btn-primary" onclick="jQuery('#name_image_file').click();" type="button"><?php echo JText::_( 'UPLOAD' ); ?></button>
          </div>
          <input class="hidden" style="display:none" type="file" name="name_image_file" id="name_image_file" onchange="jQuery('#name_image_file_display').val(this.value)" />


          <?php if(JText::_( 'ALBUM_IMAGE_FOR_ALBUM_NAME_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_IMAGE_FOR_ALBUM_NAME_EXPLANATION' ); ?></span>
          <?php } ?>
          <?php
		if($this->album->name2 != "") {?>
          <br />
          <?php echo JHTML::image('images/album_extra/album_name/' . $this->album->name2, $this->album->name2, array("style" => "max-height:30px;", "class" => "thumbnail")); ?>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="artist2"> <?php echo JText::_( 'IMAGE_FOR_ARTIST_NAME' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="artist2" id="artist2" size="30" maxlength="250" value="<?php echo $this->album->artist2;?>" />
         
          <div class="input-append">
            <input class="inputbox input-medium" id="artist_image_file_display" type="text" readonly="readonly">
            <button class="btn btn-primary" onclick="jQuery('#artist_image_file').click();" type="button"><?php echo JText::_( 'UPLOAD' ); ?></button>
          </div>
          <input class="hidden" style="display:none" type="file" name="artist_image_file" id="artist_image_file" onchange="jQuery('#artist_image_file_display').val(this.value)" />


          <?php if(JText::_( 'ALBUM_IMAGE_FOR_ARTIST_NAME_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_IMAGE_FOR_ARTIST_NAME_EXPLANATION' ); ?></span>
          <?php } ?>
          <?php
		if($this->album->artist2 != "") {?>
          <br />
          <?php echo JHTML::image('images/album_extra/artist_name/' . $this->album->artist2, $this->album->artist2, array("style" => "max-height:30px;", "class" => "thumbnail")); ?>
          <?php } ?>
        </div>
      </div>
    </fieldset>
    <fieldset >
      <legend><?php echo JText::_( 'ALBUM_REVIEW' ); ?></legend>
      <?php
            $editor = JFactory::getEditor();
            echo $editor->display('review', $this->album->review, '100%', '250', '60', '20', true);
        ?>
      <?php if(JText::_( 'ALBUM_REVIEW_EXPLANATION' )){ ?>
      <span class="help-inline"><?php echo JText::_( 'ALBUM_REVIEW_EXPLANATION' ); ?></span>
      <?php } ?>
    </fieldset>
    <fieldset >
      <legend><?php echo JText::_( 'PART_OF_A_SET' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="part_of_set"> <?php echo JText::_( 'IS_PART_OF_A_SET' ); ?></label>
        <div class="controls">
          <select name="part_of_set" id="part_of_set"  class="chzn-select">
            <option value="0"><?php echo JText::_( 'NOT_PART_OF_A_SET' ); ?></option>
            <?php
			for ($i=0, $n=count( $this->albums );$i < $n; $i++)	{
			$row =$this->albums[$i];
			$selected = ""; 
			if($row->id == $this->album->part_of_set) $selected = "selected";?>
            <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->artist_name ." - ". $row->name ." (".$row->format_name . ")";?></option>
            <?php } ?>
          </select>
          <?php if(JText::_( 'ALBUM_IS_PART_OF_A_SET_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_IS_PART_OF_A_SET_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="show_separately"> <?php echo JText::_( 'SHOW_ONLY_IN_THIS_SET' ); ?></label>
        <div class="controls">
          <input type="checkbox" name="show_separately" id="show_separately" <?php if($this->album->show_separately == 'N') echo " checked "; ?>>
          <?php if(JText::_( 'ALBUM_SHOW_ONLY_IN_THIS_SET_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_SHOW_ONLY_IN_THIS_SET_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
    </fieldset>
    <fieldset >
      <legend><?php echo JText::_( 'METADATA' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="keywords"> <?php echo JText::_( 'KEYWORDS' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="keywords" id="keywords" size="50" maxlength="250" value="<?php echo $this->album->keywords;?>" />
        </div>
      </div>
      <?php if(JText::_( 'ALBUM_KEYWORDS_EXPLANATION' )){ ?>
      <span class="help-inline"><?php echo JText::_( 'ALBUM_KEYWORDS_EXPLANATION' ); ?></span>
      <?php } ?>
      <div class="control-group">
        <label class="control-label" for="metakeywords"> <?php echo JText::_( 'META_KEYWORDS' ); ?></label>
        <div class="controls">
          <textarea name="metakeywords" id="metakeywords" cols="40" rows="4"><?php echo $this->album->metakeywords; ?></textarea>
          <?php if(JText::_( 'ALBUM_META_KEYWORDS_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_META_KEYWORDS_EXPLANATION' ); ?>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="metadescription"> <?php echo JText::_( 'META_DESCRIPTION' ); ?></label>
        <div class="controls">
          <textarea name="metadescription" id="metadescription" cols="40" rows="4"><?php echo $this->album->metadescription; ?></textarea>
          <?php if(JText::_( 'ALBUM_META_DESCRIPTION_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'ALBUM_META_DESCRIPTION_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
    </fieldset>
    <div class=" form-actions">
      <button type="submit"  class="btn btn-primary" ><i class="icon-ok"></i> <?php echo JText::_('SAVE_ALBUM'); ?></button>
      <a href="<?php echo JRoute::_('index.php?option=com_muscol&task=cancel&type=album&id='.$this->album->id); ?>" class="btn "><i class="icon-cancel"></i> <?php echo JText::_('Cancel'); ?></a> <span class="showsaving" style="display:none;"><?php echo JText::_('SAVING_ALBUM'); ?></span> </div>
    <input type="hidden" name="option" value="com_muscol" />
    <input type="hidden" name="id" value="<?php echo $this->album->id; ?>" />
    <input type="hidden" name="task" value="save_album" />
  </form>
</div>
