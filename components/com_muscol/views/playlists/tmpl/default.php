<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
$user = JFactory::getUser(); ?>

<?php 	if($this->params->get('showletternavigation')){
		echo MusColHelper::letter_navigation('');
	}
	$itemid = $this->params->get('itemid');
	if($itemid != "") $itemid = "&Itemid=" . $itemid;
?>

<div class="discos">

<h2 class="playlist_header"><?php echo JText::_('MY_PLAYLISTS'); ?></h2>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover">
    <tr>
        <td class="sectiontableheader" align="right" width="5%">#</td>
        <td class="sectiontableheader" width="50%"><?php echo JText::_('PLAYLIST_NAME'); ?></td>
        <td class="sectiontableheader" align="right" ><?php echo JText::_('N_ITEMS'); ?></td>
        <td class="sectiontableheader" align="center" ></td>
        <td class="sectiontableheader" align="center" ></td>
    </tr>
    
    <?php $link_playlist = JRoute::_('index.php?option=com_muscol&view=playlist&id=0' . $itemid);
		$link_consolidate = JRoute::_('index.php?option=com_muscol&task=consolidate_playlist' ); ?>
    <tr class="sectiontableentry1" >
        <td align="right"></td>
        <td><a href="<?php echo $link_playlist; ?>"><?php echo $this->on_the_go->title; ?></a></td>
        <td align="right"><?php if($this->on_the_go->songs != "") echo count(explode(",",$this->on_the_go->songs)); else echo 0; ?></td>
        <td>
		<a class='hasTooltip' data-original-title='<?php echo JText::_('SET_PLAYLIST_AS_CURRENT'); ?>' href="javascript:set_current_playlist(<?php echo $this->on_the_go->id; ?>);"><?php echo JHTML::image('components/com_muscol/assets/images/set_playlist.png','Set playlist as current', array("title" => JText::_("SET_PLAYLIST_AS_CURRENT"))) ; ?></a></td>
        
        <td align="right">
        <?php if($user->id && $this->params->get('allowcreateplaylists')): ?>
        <a class='hasTooltip' data-original-title='<?php echo JText::_('SAVE_THIS_PLAYLIST'); ?>' href="<?php echo $link_consolidate; ?>"><?php echo JHTML::image('components/com_muscol/assets/images/save_playlist.png','Consolidate', array("title" => JText::_("SAVE_THIS_PLAYLIST"))) ; ?></a>
        <?php endif; ?></td>
        
	</tr>
    <tr class="sectiontableentry2" ><td colspan="5"></td></tr>
    
    <?php $k = 1; 
	for ($i = 0, $n=count( $this->playlists ); $i < $n; $i++)	{ 
		$link_playlist = JRoute::_('index.php?option=com_muscol&view=playlist&id='. $this->playlists[$i]->id . $itemid);
		$edit_link  = JRoute::_('index.php?option=com_muscol&view=playlist&task=edit_playlist&id='. $this->playlists[$i]->id . $itemid);
        $delete_link = JRoute::_( 'index.php?option=com_muscol&task=remove_playlist&id='.$this->playlists[$i]->id ); ?>
	<tr class="sectiontableentry<?php echo $k; ?>" >
        <td align="right"><?php echo ($i + 1); ?></td>
        <td><a href="<?php echo $link_playlist; ?>"><?php echo $this->playlists[$i]->title; ?></a></td>
        <td align="right"><?php echo count(explode(",",$this->playlists[$i]->songs)); ?></td>
        <td>
		<a class='hasTooltip' data-original-title='<?php echo JText::_('SET_PLAYLIST_AS_CURRENT'); ?>' href="javascript:set_current_playlist(<?php echo $this->playlists[$i]->id; ?>);"><?php echo JHTML::image('components/com_muscol/assets/images/set_playlist.png','Set playlist as current', array("title" => JText::_("SET_PLAYLIST_AS_CURRENT"))) ; ?></a></td>
        
        <td align="right">
       
        <div class="btn-group pull-right">
          <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-cog"></i> <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo $edit_link; ?>"><i class="icon-pencil"></i> <?php echo JText::_("EDIT_THIS_PLAYLIST"); ?></a></li>
            <li><a href="<?php echo $delete_link; ?>"><i class="icon-trash"></i> <?php echo JText::_("DELETE_THIS_PLAYLIST"); ?></a></li>
          </ul>
        </div>
        
        </td>
        
	</tr>
    <?php $k = 3 - $k; } ?>
   
</table>

<form action="<?php echo $this->action; ?>" method="post" name="playlists_form" id="playlists_form">

<h2 class="playlist_header"><?php echo JText::_('OTHER_PLAYLISTS'); ?></h2>
<table width="100%" border="0" cellspacing="0" cellpadding="0"  class="table table-striped table-hover">
    <tr>
        <td class="sectiontableheader" align="right" width="5%">#</td>
        <td class="sectiontableheader" width="50%"><?php echo JText::_('PLAYLIST_NAME'); ?></td>
        
        <td class="sectiontableheader hidden-phone" align="right" ><?php echo JText::_('N_ITEMS'); ?></td>
        <td class="sectiontableheader" align="center" ></td>
        <td class="sectiontableheader" ><?php echo JText::_('CREATED_BY'); ?></td>
        
    </tr>
    
    <?php $k = 1; 
	for ($i = 0, $n=count( $this->playlists_others ); $i < $n; $i++)	{ 
		$link_playlist = JRoute::_('index.php?option=com_muscol&view=playlist&id='. $this->playlists_others[$i]->id . $itemid);
		?>
	<tr class="sectiontableentry<?php echo $k; ?>" >
        <td align="right"><?php echo ($i + 1); ?></td>
        <td><a href="<?php echo $link_playlist; ?>"><?php echo $this->playlists_others[$i]->title; ?></a></td>
        
        <td align="right" class=" hidden-phone"><?php echo count(explode(",",$this->playlists_others[$i]->songs)); ?></td>
        
        <td>
		<a href="javascript:set_current_playlist(<?php echo $this->playlists_others[$i]->id; ?>);"><?php echo JHTML::image('components/com_muscol/assets/images/set_playlist.png','Set playlist as current', array("title" => JText::_("SET_PLAYLIST_AS_CURRENT"))) ; ?></a></td>
        
        <td><?php echo $this->playlists_others[$i]->user_name; ?></td>

	</tr>
    <?php $k = 3 - $k; } ?>
   
</table>

<div ><?php echo $this->pagination->getListFooter(); ?></div>

<input type="hidden" name="view" value="playlists" />
<input type="hidden" name="option" value="com_muscol" />

</form>

</div>

<div align="center"><?php echo MusColHelper::showMusColFooter(); ?></div>