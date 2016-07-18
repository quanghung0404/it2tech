<?php defined('_JEXEC') or die('Restricted access'); 

JHtmlBehavior::framework();
JHTML::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', '.chzn-select');

$document = JFactory::getDocument();

$document->addScript('components/com_muscol/assets/validate.js');

?>

<div class="page-header">
  <h1><?php echo $this->song->id ? $this->song->name ." <small>[".JText::_('EDIT')."]</small>" : JText::_('NEW_SONG'); ?></h1>
</div>
<div class="editsong">
  <form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data"  class="form-validate form-horizontal">
    <fieldset >
      <legend><?php echo JText::_( 'BASIC_DETAILS' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="name"> <?php echo JText::_( 'SONG_NAME' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo htmlspecialchars($this->song->name);?>" />
          <?php if(JText::_( 'SONG_NAME_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'SONG_NAME_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="artist"> <?php echo JText::_( 'ARTIST' ); ?></label>
        <div class="controls">
          <select name="artist_id" id="artist_id" class="chzn-select">
            <option value="0"><?php echo JText::_('SELECT_ARTIST'); ?></option>
            <?php
			for ($i=0, $n=count( $this->artists );$i < $n; $i++)	{
			$row =$this->artists[$i];
			$selected = ""; 
			if($row->id == $this->song->artist_id) $selected = "selected";
			else if(!$this->song->artist_id && $this->artist_from_album == $row->id ) $selected = "selected";
			
			?>
            <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->artist_name;?></option>
            <?php } ?>
          </select>
          <?php if(JText::_( 'SONG_ARTIST_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'SONG_ARTIST_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="song_file"> <?php echo JText::_( 'FILE' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="filename" id="filename" size="32" maxlength="250" value="<?php echo $this->song->filename;?>" />
          
          <div class="input-append">
            <input class="inputbox input-medium" id="song_file_display" type="text" readonly="readonly">
            <button class="btn btn-primary" onclick="jQuery('#song_file').click();" type="button"><?php echo JText::_( 'UPLOAD' ); ?></button>
          </div>
          <input class="hidden" style="display:none" type="file" name="song_file" id="song_file" onchange="jQuery('#song_file_display').val(this.value)" />

          <?php if(JText::_( 'SONG_FILE_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'SONG_FILE_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="genre_id"> <?php echo JText::_( 'GENRE' ); ?></label>
        <div class="controls">
          <select name="genre_id" id="genre_id" class="chzn-select">
            <?php echo MusColHelper::show_genre_tree($this->genres,0, $this->song->genre_id); ?>
          </select>
          <?php if(JText::_( 'SONG_GENRE_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'SONG_GENRE_EXPLANATION' ); ?></span>
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
    </fieldset>
    <fieldset >
      <legend><?php echo JText::_( 'EXTENDED_DETAILS' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="disc_num"> <?php echo JText::_( 'DISC_NUMBER' ); ?></label>
        <div class="controls">
          <input class="inputbox input-mini" type="text" name="disc_num" id="disc_num" size="2" maxlength="4" value="<?php echo $this->song->disc_num;?>" />
          <?php if(JText::_( 'SONG_DISC_NUMBER_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'SONG_DISC_NUMBER_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="num"> <?php echo JText::_( 'SONG_NUMBER' ); ?></label>
        <div class="controls">
          <input class="inputbox input-mini" type="text" name="num" id="num" size="2" maxlength="4" value="<?php echo $this->song->num;?>" />
          <?php if(JText::_( 'SONG_NUMBER_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'SONG_NUMBER_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="position"> <?php echo JText::_( 'TRACKLIST_POSITION' ); ?></label>
        <div class="controls">
          <input class="inputbox input-mini" type="text" name="position" id="position" size="2" maxlength="4" value="<?php echo $this->song->position;?>" />
          <?php if(JText::_( 'SONG_POSITION_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'SONG_POSITION_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="hours"> <?php echo JText::_( 'LENGTH' ); ?></label>
        <div class="controls">
          <input class="inputbox input-mini" type="text" name="hours" id="hours" size="2" maxlength="2" value="<?php echo $this->song->hours;?>" />
          :
          <input class="inputbox input-mini" type="text" name="minuts" id="minuts" size="2" maxlength="2" value="<?php echo $this->song->minuts;?>" />
          :
          <input class="inputbox input-mini" type="text" name="seconds" id="seconds" size="2" maxlength="2" value="<?php echo $this->song->seconds;?>" />
          <?php if(JText::_( 'SONG_LENGTH_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'SONG_LENGTH_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="video"> <?php echo JText::_( 'YOUTUBE_VIDEO_URL' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="video" id="video" size="50" maxlength="250" value="<?php echo $this->song->video;?>" /><br />
          <?php if(JText::_( 'YOUTUBE_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'YOUTUBE_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="buy_link"> <?php echo JText::_( 'BUY_LINK' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="buy_link" id="buy_link" size="80" maxlength="250" value="<?php echo $this->song->buy_link;?>" />
          <?php if(JText::_( 'BUY_LINK_SONG_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'BUY_LINK_SONG_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="review"> <?php echo JText::_( 'SONG_REVIEW' ); ?></label>
        <div class="controls">
          <?php
				$editor = JFactory::getEditor();
				echo $editor->display('review', $this->song->review, '100%', '200', '60', '20', true);
			?>
          <?php if(JText::_( 'SONG_REVIEW_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'SONG_REVIEW_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="songwriters"> <?php echo JText::_( 'SONGWRITERS' ); ?></label>
        <div class="controls">
          <textarea name="songwriters" id="songwriters" rows="4" cols="40"><?php echo $this->song->songwriters;?></textarea>
          <?php if(JText::_( 'SONGWRITERS_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'SONGWRITERS_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="chords"> <?php echo JText::_( 'SONGWRITERS' ); ?></label>
        <div class="controls">
          <textarea name="chords" id="chords" rows="20" cols="50"><?php echo $this->song->chords;?></textarea>
          <?php if(JText::_( 'CHORDS_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'CHORDS_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="lyrics"> <?php echo JText::_( 'LYRICS' ); ?></label>
        <div class="controls">
          <?php
				echo $editor->display('lyrics', $this->song->lyrics, '100%', '400', '60', '20', true);
			?>
          <?php if(JText::_( 'LYRICS_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'LYRICS_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
    </fieldset>
    <div class=" form-actions">
      <button type="submit"  class="btn btn-primary" ><i class="icon-ok"></i> <?php echo JText::_('SAVE_SONG'); ?></button>
      <a href="<?php echo JRoute::_('index.php?option=com_muscol&task=cancel&type=song&id='.$this->song->id); ?>" class="btn "><i class="icon-cancel"></i> <?php echo JText::_('Cancel'); ?></a> <span class="showsaving" style="display:none;"><?php echo JText::_('SAVING_SONG'); ?></span> </div>
    <input type="hidden" name="album_id" value="<?php echo $this->song->album_id;?>" />
    <input type="hidden" name="option" value="com_muscol" />
    <input type="hidden" name="id" value="<?php echo $this->song->id; ?>" />
    <input type="hidden" name="from" value="<?php echo JRequest::getVar('from'); ?>" />
    <input type="hidden" name="task" value="save_song" />
  </form>
</div>
