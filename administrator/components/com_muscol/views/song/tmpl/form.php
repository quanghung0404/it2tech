<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
  <div class="span6">
    <fieldset class="adminform">
      <legend><?php echo JText::_( 'DETAILS' ); ?></legend>
      <div class="control-group">
        <label class="control-label"  for="name"> <?php echo JText::_( 'SONG_NAME' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="name" id="name" size="100" maxlength="250" value="<?php echo $this->song->name;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="artist"> <?php echo JText::_( 'ARTIST' ); ?></label>
        <div class="controls">
          <?php //print_r ($this->song); ?>
          <select name="artist_id" id="artist_id" class="chzn-select">
            <?php
      for ($i=0, $n=count( $this->artists );$i < $n; $i++)  {
      $row =$this->artists[$i];
      $selected = ""; 
      if($row->id == $this->song->artist_id) $selected = "selected";
      else if(!$this->song->artist_id && $this->artist_from_album == $row->id ) $selected = "selected";
      
      ?>
            <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->artist_name;?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="name2"> <?php echo JText::_( 'DISC_NUMBER' ); ?></label>
        <div class="controls">
          <input class="inputbox input-mini" type="text" name="disc_num" id="disc_num" size="2" maxlength="4" value="<?php echo $this->song->disc_num;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="subtitle"> <?php echo JText::_( 'SONG_NUMBER' ); ?></label>
        <div class="controls">
          <input class="inputbox input-mini" type="text" name="num" id="num" size="2" maxlength="4" value="<?php echo $this->song->num;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="subtitle"> <?php echo JText::_( 'LENGTH' ); ?></label>
        <div class="controls">
          <input class="inputbox input-mini" type="text" name="hours" id="hours" size="2" maxlength="2" value="<?php echo $this->song->hours;?>" />
          :
          <input class="inputbox input-mini" type="text" name="minuts" id="minuts" size="2" maxlength="2" value="<?php echo $this->song->minuts;?>" />
          :
          <input class="inputbox input-mini" type="text" name="seconds" id="seconds" size="2" maxlength="2" value="<?php echo $this->song->seconds;?>" />
        </div>
      </div>
      
      <div class="control-group">
        <label class="control-label"  for="genre"> <?php echo JText::_( 'GENRE' ); ?></label>
        <div class="controls">
          <select name="genre_id" id="genre_id" class="chzn-select">
            <?php echo $this->show_genre_tree($this->genres,0); ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="file"> <?php echo JText::_( 'FILE' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="filename" id="filename" size="32" maxlength="250" value="<?php echo $this->song->filename;?>" /> 
          
          <div class="input-append">
          <input class="inputbox input-medium" id="song_file_display" type="text" readonly="readonly">
          <button class="btn btn-primary" onclick="jQuery('#song_file').click();" type="button"><?php echo JText::_( 'SELECT_TO_UPLOAD' ); ?></button>
        </div>

        <input class="hidden" style="display:none" type="file" name="song_file" id="song_file" onchange="jQuery('#song_file_display').val(this.value)" />

        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="tags"> <?php echo JText::_( 'TAGS' ); ?></label>
        <div class="controls">
          <input class="inputbox tm-input tm-input-info" type="text" name="tags" id="tags" value="" placeholder="<?php echo JText::_( 'TAGS_PLACEHOLDER' ); ?>" />
  
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="name"> <?php echo JText::_( 'YOUTUBE_VIDEO_URL' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="video" id="video" size="100" maxlength="250" value="<?php echo $this->song->video;?>" />
          <br /><span class="help-inline"><?php echo JText::_( 'YOUTUBE_EXPLANATION' ); ?></span>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="album_file"> <?php echo JText::_( 'BUY_LINK' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="buy_link" id="buy_link" size="80" maxlength="250" value="<?php echo $this->song->buy_link;?>" />
          <span class="help-inline"><?php echo JText::_( 'BUY_LINK_SONG_EXPLANATION' ); ?></span>
          </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="file"> <?php echo JText::_( 'SONGWRITERS' ); ?></label>
        <div class="controls">
          <textarea name="songwriters" id="songwriters" rows="4" cols="40"><?php echo $this->song->songwriters;?></textarea>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="file"> <?php echo JText::_( 'CHORDS' ); ?></label>
        <div class="controls">
          <textarea name="chords" id="chords" rows="20" cols="100"><?php echo $this->song->chords;?></textarea>
        </div>
      </div>
    </fieldset>
  </div>
  <div class="span6">
    <fieldset class="adminform">
      <legend><?php echo JText::_( 'TEXTS' ); ?></legend>
      <div class="control-group">
        <label class="control-label"  for="review"> <?php echo JText::_( 'REVIEW' ); ?></label>
        <div class="controls">
          <?php
				$editor = JFactory::getEditor();
				echo $editor->display('review', $this->song->review, '100%', '200', '60', '20', true);
			?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="lyrics"> <?php echo JText::_( 'LYRICS' ); ?></label>
        <div class="controls">
          <?php
				echo $editor->display('lyrics', $this->song->lyrics, '100%', '400', '60', '20', true);
			?>
        </div>
      </div>
    </fieldset>
  </div>
  <input type="hidden" name="album_id" value="<?php echo $this->song->album_id;?>" />
  <input type="hidden" name="from" value="<?php echo JRequest::getVar('from'); ?>" />
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="id" value="<?php echo $this->song->id; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="song" />
</form>
