<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
  <div class="span12">
    <fieldset class="adminform">
      <legend><?php echo JText::_( 'Details' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="format"> <?php echo JText::_( 'Type' ); ?>: </label>
        <div class="controls">
          <input class="inputbox" type="text" name="type_name" id="type_name" size="32" maxlength="250" value="<?php echo $this->type->type_name;?>" />
        </div>
      </div>
    </fieldset>
  </div>
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="id" value="<?php echo $this->type->id; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="type" />
</form>
