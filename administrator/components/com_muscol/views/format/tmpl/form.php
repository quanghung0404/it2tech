<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
  <div class="span12">
    <fieldset class="adminform">
      <legend><?php echo JText::_( 'Details' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="format_name"> <?php echo JText::_( 'Format' ); ?>: </label>
        <div class="controls">
          <input class="inputbox" type="text" name="format_name" id="format_name" size="32" maxlength="250" value="<?php echo $this->format->format_name;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="display_group"> <?php echo JText::_( 'Display group' ); ?>: </label>
        <div class="controls">
          <select name="display_group" id="display_group" class="chzn-select">
            <option value="0">--<?php echo JText::_('None'); ?>--</option>
            <?php
			for ($i=0, $n=count( $this->formats );$i < $n; $i++)	{
			$row =$this->formats[$i];
			$selected = ""; 
			if($row->id == $this->format->display_group) $selected = "selected";?>
            <option <?php echo $selected;?> value="<?php echo $row->id;?>"><?php echo $row->format_name;?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="icon"> <?php echo JText::_( 'Format icon' ); ?>: </label>
        <div class="controls">
          <input class="inputbox" type="text" name="icon" id="icon" size="32" maxlength="250" value="<?php echo $this->format->icon;?>" /><br />
          <input type="file" name="format_image_file"/>
          <?php
		if($this->format->icon != "") {?>
          <br /><img class="thumbnail" style="max-height:30px;" src="../images/formats/<?php echo $this->format->icon;?>"/>
          <?php } ?>
        </div>
      </div>
    </fieldset>
  </div>
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="id" value="<?php echo $this->format->id; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="format" />
</form>
