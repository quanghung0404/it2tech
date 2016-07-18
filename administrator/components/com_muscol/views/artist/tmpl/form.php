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

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data"  class="form-horizontal form-validate">
  <div class="span6">
    <fieldset class="adminform">
      <legend><?php echo JText::_( 'DETAILS' ); ?></legend>
      <div class="control-group">
        <label class="control-label"  for="artist_name"> <?php echo JText::_( 'ARTIST' ); ?></label>
        <div class="controls">
          <input class="inputbox required" type="text" name="artist_name" id="artist_name" size="32" maxlength="250" value="<?php echo htmlspecialchars($this->artist->artist_name);?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="letter"> <?php echo JText::_( 'LETTER' ); ?></label>
        <div class="controls">
          <select name="letter" id="letter" class="chzn-select">
            <option value=""></option>
            <?php
				
				$letters = MusColAlphabets::get_combined_array();
				if(in_array($params->get('alphabet'),array('arabicltr', 'arabicrtl'))) $letters = array_reverse($letters, true) ;
						
                foreach($letters as $key => $value){
                $selected = ""; 
                if($this->artist->letter == $key) $selected = "selected";?>
            <option <?php echo $selected;?> value="<?php echo $key;?>"><?php echo $value;?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="class_name"> <?php echo JText::_( 'CLASSIFICATION_NAME' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="class_name" id="class_name" size="32" maxlength="250" value="<?php echo $this->artist->class_name;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="picture"> <?php echo JText::_( 'PICTURE' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="picture" id="picture" size="32" maxlength="250" value="<?php echo $this->artist->picture;?>" />
          
          <div class="input-append">
          <input class="inputbox input-medium" id="artist_picture_file_display" type="text" readonly="readonly">
          <button class="btn btn-primary" onclick="jQuery('#artist_picture_file').click();" type="button"><?php echo JText::_( 'SELECT_TO_UPLOAD' ); ?></button>
        </div>

        <input class="hidden" style="display:none;" type="file" name="artist_picture_file" id="artist_picture_file" onchange="jQuery('#artist_picture_file_display').val(this.value)" />

          <?php
		if($this->artist->picture != "") {?>
          <br /><br />
          <img class="thumbnail" style="width:300px;" src="../images/artists/<?php echo $this->artist->picture;?>"/>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="tags"> <?php echo JText::_( 'TAGS' ); ?></label>
        <div class="controls">
          <input class="inputbox tm-input tm-input-info" type="text" name="tags" id="tags" value="" placeholder="<?php echo JText::_( 'TAGS_PLACEHOLDER' ); ?>" />
  
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="image"> <?php echo JText::_( 'IMAGE_FOR_ARTIST_NAME' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="image" id="image" size="32" maxlength="250" value="<?php echo $this->artist->image;?>" />
          
          

          <div class="input-append">
          <input class="inputbox input-medium" id="artist_image_file_display" type="text" readonly="readonly">
          <button class="btn btn-primary" onclick="jQuery('#artist_image_file').click();" type="button"><?php echo JText::_( 'SELECT_TO_UPLOAD' ); ?></button>
        </div>

        <input class="hidden" style="display:none;" type="file" name="artist_image_file" id="artist_image_file" onchange="jQuery('#artist_image_file_display').val(this.value)" />
        <span class="help-inline"><?php echo JText::_( 'USUALLY_LEAVE_EMPTY' ); ?></span>
          
          <?php
		if($this->artist->image != "") {?>
          <br /><img style="max-height:30px;" src="../images/artists/<?php echo $this->artist->image;?>"/>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="related"> <?php echo JText::_( 'RELATED_ARTISTS' ); ?></label>
        <div class="controls">
          <select multiple="multiple" name="related[]" id="related" size="10" class="chzn-select">
            <?php
			for ($i=0, $n=count( $this->related );$i < $n; $i++)	{
			$row =$this->related[$i];
			$selected = ""; 
			if( in_array($row->id,$this->artist->related) ) $selected = "selected";?>
            <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->artist_name;?></option>
            <?php } ?>
          </select>
         
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="keywords"> <?php echo JText::_( 'KEYWORDS' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="keywords" id="keywords" size="100" maxlength="250" value="<?php echo $this->artist->keywords;?>" />
          <span class="help-inline">
          <?php echo JText::_( 'SEPARATED_BY_BLANK_SPACES' ); ?></span></div>
      </div>
    </fieldset>
    <fieldset class="adminform">
      <legend><?php echo JText::_( 'METADATA' ); ?></legend>
      <div class="control-group">
        <label class="control-label"  for="metakeywords"> <?php echo JText::_( 'META_KEYWORDS' ); ?></label>
        <div class="controls">
          <textarea name="metakeywords" id="metakeywords" cols="40" rows="4"><?php echo $this->artist->metakeywords; ?></textarea>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="metadescription"> <?php echo JText::_( 'META_DESCRIPTION' ); ?></label>
        <div class="controls">
          <textarea name="metadescription" id="metadescription" cols="40" rows="4"><?php echo $this->artist->metadescription; ?></textarea>
        </div>
      </div>
    </fieldset>
  </div>
  <div class="span6">
    <fieldset class="adminform">
      <legend><?php echo JText::_( 'MORE_DETAILS' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="genre_id" > <?php echo JText::_( 'GENRE' ); ?></label>
        <div class="controls">
          <select name="genre_id" id="genre_id" class="chzn-select">
            <?php echo $this->show_genre_tree($this->genres,0); ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="city"> <?php echo JText::_( 'CITY' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="city" id="city" size="32" maxlength="255" value="<?php echo $this->artist->city;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="country"> <?php echo JText::_( 'COUNTRY' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="country" id="country" size="32" maxlength="255" value="<?php echo $this->artist->country;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="url"> <?php echo JText::_( 'WEBSITE' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="url" id="url" size="32" maxlength="255" value="<?php echo $this->artist->url;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="years_active"> <?php echo JText::_( 'YEARS_ACTIVE' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="years_active" id="years_active" size="32" maxlength="255" value="<?php echo $this->artist->years_active;?>" />
        </div>
      </div>
    </fieldset>
    <fieldset class="adminform">
      <legend><?php echo JText::_( 'REVIEW' ); ?></legend>
      <?php
				$editor = JFactory::getEditor();
				echo $editor->display('review', $this->artist->review, '100%', '400', '60', '20', true);
			?>
    </fieldset>
  </div>
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="id" value="<?php echo $this->artist->id; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="artist" />
</form>
