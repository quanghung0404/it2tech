<?php defined('_JEXEC') or die('Restricted access'); 
$params =JComponentHelper::getParams( 'com_muscol' );

JHtmlBehavior::framework();
JHTML::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', '.chzn-select');

$document = JFactory::getDocument();

$document->addScript('components/com_muscol/assets/validate.js');

?>

<div class="page-header">
  <h1><?php echo $this->artist->id ? $this->artist->artist_name ." <small>[".JText::_('EDIT')."]</small>" : JText::_('NEW_ARTIST'); ?></h1>
</div>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-validate form-horizontal">
  <fieldset >
    <legend><?php echo JText::_( 'BASIC_DETAILS' ); ?></legend>
    <div class="control-group">
      <label class="control-label" for="artist_name"> <?php echo JText::_( 'ARTIST_NAME' ); ?> </label>
      <div class="controls">
        <input class="text_area required" type="text" name="artist_name" id="artist_name" size="32" maxlength="250" value="<?php echo htmlspecialchars($this->artist->artist_name); ?>" />
        <?php if(JText::_( 'ARTIST_NAME_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'ARTIST_NAME_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="picture"> <?php echo JText::_( 'PICTURE' ); ?> </label>
      <div class="controls">
        <input class="inputbox " type="text" name="picture" id="picture" size="32" maxlength="250" value="<?php echo $this->artist->picture;?>" />

        <div class="input-append">
          <input class="inputbox input-medium" id="artist_picture_file_display" type="text" readonly="readonly">
          <button class="btn btn-primary" onclick="jQuery('#artist_picture_file').click();" type="button"><?php echo JText::_( 'UPLOAD' ); ?></button>

        </div>
        <input class="hidden" style="display:none" type="file" name="artist_picture_file" id="artist_picture_file" onchange="jQuery('#artist_picture_file_display').val(this.value)" />

        <?php if(JText::_( 'ARTIST_PICTURE_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'ARTIST_PICTURE_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
    <?php
			if($this->artist->picture != "") {?>
    <div class="control-group">
      <div class="controls"><?php echo JHTML::image("images/artists/".$this->artist->picture, JText::_('PICTURE'), array("class"=>"artistpictureform thumbnail")); ?></div>
    </div>
    <?php } ?>
    <div class="control-group">
      <label class="control-label" for="genre_id"> <?php echo JText::_( 'GENRE' ); ?> </label>
      <div class="controls">
        <select name="genre_id" id="genre_id" class="chzn-select">
          <?php echo $this->show_genre_tree($this->genres,0); ?>
        </select>
        <?php if(JText::_( 'ARTIST_GENRE_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'ARTIST_GENRE_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
    <div class="control-group">
        <label class="control-label"  for="tags"> <?php echo JText::_( 'TAGS' ); ?> </label>
        <div class="controls">
          <input class="inputbox tm-input tm-input-info" type="text" name="tags" id="tags" value="" placeholder="<?php echo JText::_( 'TAGS_PLACEHOLDER' ); ?>" />
  
        </div>
      </div>
    <div class="control-group">
      <label class="control-label" for="city"> <?php echo JText::_( 'CITY' ); ?> </label>
      <div class="controls">
        <input class="inputbox" type="text" name="city" id="city" size="32" maxlength="255" value="<?php echo $this->artist->city;?>" />
        <?php if(JText::_( 'ARTIST_CITY_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'ARTIST_CITY_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="country"> <?php echo JText::_( 'COUNTRY' ); ?> </label>
      <div class="controls">
        <input class="inputbox" type="text" name="country" id="country" size="32" maxlength="255" value="<?php echo $this->artist->country;?>" />
        <?php if(JText::_( 'ARTIST_COUNTRY_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'ARTIST_COUNTRY_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="url"> <?php echo JText::_( 'WEBSITE' ); ?> </label>
      <div class="controls">
        <input class="inputbox" type="text" name="url" id="url" size="32" maxlength="255" value="<?php echo $this->artist->url;?>" />
        <?php if(JText::_( 'ARTIST_WEBSITE_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'ARTIST_WEBSITE_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="years_active"> <?php echo JText::_( 'YEARS_ACTIVE' ); ?> </label>
      <div class="controls">
        <input class="inputbox" type="text" name="years_active" id="years_active" size="32" maxlength="255" value="<?php echo $this->artist->years_active;?>" />
        <?php if(JText::_( 'ARTIST_YEARS_ACTIVE_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'ARTIST_YEARS_ACTIVE_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="review"> <?php echo JText::_( 'ARTIST_REVIEW' ); ?> </label>
      <div class="controls">
        <?php
				$editor = JFactory::getEditor();
				echo $editor->display('review', $this->artist->review, '100%', '250', '60', '20', false);
			?>
        <?php if(JText::_( 'ARTIST_REVIEW_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'ARTIST_REVIEW_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
  </fieldset>
  <fieldset >
    <legend><?php echo JText::_( 'EXTENDED_DETAILS' ); ?></legend>
    <div class="control-group">
      <label class="control-label" for="letter"> <?php echo JText::_( 'LETTER' ); ?> </label>
      <div class="controls">
        <select name="letter" id="letter"  class="chzn-select">
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
        <?php if(JText::_( 'LETTER_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'LETTER_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="class_name"> <?php echo JText::_( 'CLASSIFICATION_NAME' ); ?> </label>
      <div class="controls">
        <input class="inputbox" type="text" name="class_name" id="class_name" size="32" maxlength="250" value="<?php echo $this->artist->class_name;?>" />
        <?php if(JText::_( 'CLASSIFICATION_NAME_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'CLASSIFICATION_NAME_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="related"> <?php echo JText::_( 'RELATED_ARTISTS' ); ?> </label>
      <div class="controls">
        <select multiple="multiple" name="related[]" id="related" size="6"  class="chzn-select">
          <?php
			for ($i=0, $n=count( $this->related );$i < $n; $i++)	{
			$row =$this->related[$i];
			$selected = ""; 
			if( in_array($row->id,$this->artist->related) ) $selected = "selected";?>
          <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->artist_name;?></option>
          <?php } ?>
        </select>
        <?php if(JText::_( 'RELATED_ARTISTS_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'RELATED_ARTISTS_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="image"> <?php echo JText::_( 'IMAGE_FOR_ARTIST_NAME' ); ?> </label>
      <div class="controls">
        <input class="inputbox" type="text" name="image" id="image" size="32" maxlength="250" value="<?php echo $this->artist->image;?>" />
        
         <div class="input-append">
          <input class="inputbox input-medium" id="artist_image_file_display" type="text" readonly="readonly">
          <button class="btn btn-primary" onclick="jQuery('#artist_image_file').click();" type="button"><?php echo JText::_( 'UPLOAD' ); ?></button>
        </div>
        <input class="hidden" style="display:none" type="file" name="artist_image_file" id="artist_image_file" onchange="jQuery('#artist_image_file_display').val(this.value)" />

        <?php if(JText::_( 'IMAGE_FOR_ARTIST_NAME_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'IMAGE_FOR_ARTIST_NAME_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
    <?php
			if($this->artist->image != "") {?>
    <div class="control-group">
      <div class="controls"><?php echo JHTML::image("images/artists/".$this->artist->image, JText::_('Logo'), array("class"=>"artistpictureform thumbnail")); ?></div>
    </div>
    <?php } ?>
  </fieldset>
  <fieldset >
    <legend><?php echo JText::_( 'METADATA' ); ?></legend>
    <div class="control-group">
      <label class="control-label" for="keywords"> <?php echo JText::_( 'KEYWORDS' ); ?> </label>
      <div class="controls">
        <input class="inputbox" type="text" name="keywords" id="keywords" size="50" maxlength="250" value="<?php echo $this->artist->keywords;?>" />
        <?php if(JText::_( 'KEYWORDS_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'KEYWORDS_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="metakeywords"> <?php echo JText::_( 'META_KEYWORDS' ); ?> </label>
      <div class="controls">
        <textarea name="metakeywords" id="metakeywords" cols="40" rows="4"><?php echo $this->artist->metakeywords; ?></textarea>
        <?php if(JText::_( 'META_KEYWORDS_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'META_KEYWORDS_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="metadescription"> <?php echo JText::_( 'META_DESCRIPTION' ); ?> </label>
      <div class="controls">
        <textarea name="metadescription" id="metadescription" cols="40" rows="4"><?php echo $this->artist->metadescription; ?></textarea>
        <?php if(JText::_( 'META_DESCRIPTION_EXPLANATION' )){ ?>
        <span class="help-inline"><?php echo JText::_( 'META_DESCRIPTION_EXPLANATION' ); ?></span>
        <?php } ?>
      </div>
    </div>
  </fieldset>
  <div class=" form-actions">
    <button type="submit"  class="btn btn-primary" ><i class="icon-ok"></i> <?php echo JText::_('SAVE_ARTIST'); ?></button>
    <a href="<?php echo JRoute::_('index.php?option=com_muscol&task=cancel&type=artist&id='.$this->artist->id); ?>" class="btn "><i class="icon-cancel"></i> <?php echo JText::_('Cancel'); ?></a> <span class="showsaving" style="display:none;"><?php echo JText::_('SAVING_ARTIST'); ?></span> </div>
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="id" value="<?php echo $this->artist->id; ?>" />
  <input type="hidden" name="task" value="save_artist" />
  <input type="hidden" name="layout" value="<?php echo $this->params->get('albums_view'); ?>" />
</form>
