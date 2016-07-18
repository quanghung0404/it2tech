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
    <fieldset class="muscolfieldset">
      <legend><?php echo JText::_( 'BASIC_DETAILS' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="song_file"> <?php echo JText::_( 'FILE' ); ?></label>
        <div class="controls">
          <input class="inputbox" type="text" name="filename" id="filename" size="32" maxlength="250" value="<?php echo $this->song->filename;?>" />
          <br />
          <input type="file" name="song_file" id="song_file"/>
          <?php if(JText::_( 'SONG_FILE_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'SONG_FILE_EXPLANATION' ); ?></span>
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
