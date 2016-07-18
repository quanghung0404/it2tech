<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
$user = JFactory::getUser(); 

JHtmlBehavior::framework();
JHTML::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', '.chzn-select');

$document = JFactory::getDocument();

$document->addScript('components/com_muscol/assets/validate.js');

	if($this->params->get('showletternavigation')){
		echo MusColHelper::letter_navigation('');
	}
	$itemid = $this->params->get('itemid');
	if($itemid != "") $itemid = "&Itemid=" . $itemid;
?>

<div class="page-header">
  <h1><?php echo $this->playlist->id ? $this->playlist->title ." <small>[".JText::_('EDIT')."]</small>" : JText::_('NEW_PLAYLIST'); ?></h1>
</div>
<div class="editplaylist">
  <form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-validate form-horizontal">
    <fieldset >
      <legend><?php echo JText::_( 'BASIC_DETAILS' ); ?></legend>
      <div class="control-group">
        <label class="control-label" for="title"> <?php echo JText::_( 'PLAYLIST_TITLE' ); ?></label>
        <div class="controls">
          <input type="text" class="inputbox required" name="title" id="title" size="50" maxlength="255" value="<?php echo htmlspecialchars($this->playlist->title); ?>" />
          <?php if(JText::_( 'PLAYLIST_NAME_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'PLAYLIST_NAME_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="description"> <?php echo JText::_( 'PLAYLIST_DESCRIPTION' ); ?></label>
        <div class="controls">
          <?php $editor = JFactory::getEditor();
          echo $editor->display('description', $this->playlist->description, '100%', '400', '60', '20', false);
     ?>
          <?php if(JText::_( 'PLAYLIST_DESCRIPTION_EXPLANATION' )){ ?>
          <span class="help-inline"><?php echo JText::_( 'PLAYLIST_DESCRIPTION_EXPLANATION' ); ?></span>
          <?php } ?>
        </div>
      </div>
    </fieldset>
    <div class=" form-actions">
      <button type="submit"  class="btn btn-primary" ><i class="icon-ok"></i> <?php echo JText::_('SAVE_PLAYLIST_DETAILS'); ?></button>
      <a href="<?php echo JRoute::_('index.php?option=com_muscol&task=cancel&type=playlist&id='.$this->playlist->id); ?>" class="btn "><i class="icon-cancel"></i> <?php echo JText::_('Cancel'); ?></a> <span class="showsaving" style="display:none;"><?php echo JText::_('SAVING_PLAYLIST'); ?></span> </div>
    <fieldset >
      <legend><?php echo JText::_( 'ITEMS_IN_PLAYLIST' ); ?></legend>
      <?php if( ( $user->id && $this->params->get('displayplayer') ) || $this->params->get('displayplayer') == 2 ){ ?>
      <div class="player" align="center" style="padding:10px;"><?php echo $this->player; ?></div>
      <?php } ?>
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped">
        <thead>
          <tr>
            <th class="sectiontableheader" align="right" width="5%">#</th>
            <th class="sectiontableheader" width="5%"></th>
            <th class="sectiontableheader" width="25%"><?php echo JText::_('Song'); ?></th>
            <th class="sectiontableheader hidden-phone"  width="25%"><?php echo JText::_('Album'); ?></th>
            <th class="sectiontableheader" align="right" width="15%"></th>
            <th class="sectiontableheader" align="center"><a href="javascript:document.adminForm.task.value='save_playlist_order';document.adminForm.submit();"><?php echo JHTML::image('components/com_muscol/assets/images/save_order.png','Save order', array("title" => JText::_("SAVE_ORDER"))) ; ?></a></th>
            <th class="sectiontableheader"  ></th>
            <th class="sectiontableheader" align="right" width="5%"> </th>
          </tr>
        </thead>
        <?php $k = 1; 
	
	$this->playlist->types = explode(",", $this->playlist->types );
	
	for ($i = 0, $n=count( $this->songs ); $i < $n; $i++)	{ 
		$link_song = JRoute::_('index.php?option=com_muscol&view=song&id='. $this->songs[$i]->id . $itemid);
		$link_album = JRoute::_('index.php?option=com_muscol&view=album&id='. $this->songs[$i]->album_id . $itemid);
		$file_link = JRoute::_( 'index.php?option=com_muscol&view=file&format=raw&id='. $this->songs[$i]->id ); 
        $delete_link = JRoute::_( 'index.php?option=com_muscol&task=remove_song_playlist&id='.$this->playlist->id.'&song_positions[]='. $i ); ?>
        <tr class="sectiontableentry<?php echo $k; ?>" >
          <td align="right"><?php echo ($i + 1); ?></td>
          <td align="right"><?php if($this->playlist->types[$i] == "v") echo JHTML::image('components/com_muscol/assets/images/video.png','Video', array("title" => JText::_("Video"))) ; else echo JHTML::image('components/com_muscol/assets/images/audio.png','Audio', array("title" => JText::_("Audio"))) ; ?></td>
          <td><a href="<?php echo $link_song; ?>"><?php echo $this->songs[$i]->name; ?></a></td>
          <td class=" hidden-phone"><a href="<?php echo $link_album; ?>"><?php echo $this->songs[$i]->album_name; ?></a></td>
          <td><?php if( ($user->id && $this->params->get('displayplayer')) || $this->params->get('displayplayer') == 2 ){ echo $this->songs[$i]->player; } ?></td>
          <td><input type="text" value="<?php echo ($i + 1); ?>" name="playlist_order[]" size="2" maxlength="4" class="inputbox input-mini"/></td>
          <td><a href="<?php echo $delete_link; ?>"><?php echo JHTML::image('components/com_muscol/assets/images/delete.png','Delete', array("title" => JText::_("DELETE_ITEM_PLAYLIST"))) ; ?></a></td>
          <td><?php if($this->songs[$i]->filename != "" && $this->params->get('allowsongdownload')){ 
				if( $user->id  || $this->params->get('allowsongdownload') == 2 ){ ?>
            <a href="<?php echo $file_link; ?>" title="<?php echo JText::_('DOWNLOAD_THIS_SONG'); ?>"><?php echo JHTML::image('components/com_muscol/assets/images/music.png','File'); ?></a>
            <?php } 
			else  echo JHTML::image('components/com_muscol/assets/images/music.png','File',array("title" => JText::_("FILE_REGISTERED_USERS"))); 
             } ?></td>
        </tr>
        <?php $k = 3 - $k; } ?>
      </table>
    </fieldset>
    <input type="hidden" name="id" value="<?php echo $this->playlist->id; ?>" />
    <input type="hidden" name="task" value="save_playlist" />
    <input type="hidden" name="option" value="com_muscol" />
  </form>
</div>
