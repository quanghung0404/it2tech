<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
  <div class="span12">
    <fieldset class="adminform">
      <legend><?php echo JText::_( 'Details' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="tag_name"> <?php echo JText::_( 'Tag' ); ?>: </label>
        <div class="controls">
          <input class="inputbox" type="text" name="tag_name" id="tag_name" size="32" maxlength="250" value="<?php echo $this->tag->tag_name;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="icon"> <?php echo JText::_( 'Tag icon' ); ?>: </label>
        <div class="controls">
          <input class="inputbox" type="text" name="icon" id="icon" size="32" maxlength="250" value="<?php echo $this->tag->icon;?>" />
          <br />
          <input type="file" name="tag_image_file"/>
          <br />
          <?php
		if($this->tag->icon != "") {?>
          <img class="thumbnail" style="max-height:30px;" src="../images/tags/<?php echo $this->tag->icon;?>"/>
          <?php } ?>
        </div>
      </div>
    </fieldset>
  </div>
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="id" value="<?php echo $this->tag->id; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="tag" />
</form>
