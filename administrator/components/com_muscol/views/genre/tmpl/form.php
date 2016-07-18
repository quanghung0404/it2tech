<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
  <div class="span12">
    <fieldset class="adminform">
      <legend><?php echo JText::_( 'Details' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="genre_name"> <?php echo JText::_( 'Genre' ); ?>: </label>
        <div class="controls">
          <input class="inputbox" type="text" name="genre_name" id="genre_name" size="32" maxlength="250" value="<?php echo $this->genre->genre_name;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="parents"> <?php echo JText::_( 'Parents' ); ?>: </label>
        <div class="controls">
          <select multiple="multiple" name="parents[]" id="parents" class="chzn-select">
            <option value="0">-- <?php echo JText::_( 'No parents'); ?> --</option>
            <?php echo $this->show_genre_tree($this->parents,0); ?>
          </select>
        </div>
      </div>
    </fieldset>
  </div>
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="id" value="<?php echo $this->genre->id; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="genre" />
</form>
