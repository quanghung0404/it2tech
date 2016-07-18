<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<?php 	if($this->params->get('showletternavigation')){
		echo MusColHelper::letter_navigation($this->letter);
	}
	$itemid = $this->params->get('itemid');
	if($itemid != "") $itemid = "&Itemid=" . $itemid;
?>

<?php if($this->params->get('showpdficon')){ ?>
<div class="icons">
<?php 
$attr = array("title" => "PDF");
$link_pdf = JRoute::_('index.php?option=com_muscol&view=artists&format=ownpdf&letter=' . $this->letter);
$pdf_icon = JHTML::image('components/com_muscol/assets/images/page_white_acrobat.png',"PDF", array("title" => "PDF"));
?>
<a href="<?php echo $link_pdf; ?>" target="_blank"><?php echo $pdf_icon; ?></a>
</div>
<?php } ?>

<?php if($this->letter == "") {
	echo $this->introtext; ?>

<?php if($this->params->get('showalbumsearch')){ ?>
<div class="searchalbums">
<form action="index.php" method="get">
    
    <?php echo MusColHelper::searchalbums_form_content($this->genre_list, $this->params->get('itemid')); ?>
    
</form>
</div>
<?php } ?>

<?php if($this->params->get('showsongsearch')){ ?>
<div class="searchsongs">
<form action="index.php" method="get">
    <?php echo MusColHelper::searchsongs_form_content($this->genre_list, $this->params->get('itemid')); ?>
</form>
</div>
<?php }

echo $this->introtext2;

} ?>

<?php

if($this->params->get('showartistshome') || $this->letter != ""){
	?>

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="sectiontableheader" align="right" width="5%">#</td>
            <td class="sectiontableheader" width="75%"><?php echo JText::_('Artist'); ?></td>
            <td class="sectiontableheader"  align="right" width="10%"><?php echo JText::_('Albums'); ?></td>
            <td class="sectiontableheader" align="right" width="10%"><?php echo JText::_('Songs'); ?></td>
        </tr>      
	<?php
	$k = 1;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row =$this->items[$i];
		$link= JRoute::_( 'index.php?option=com_muscol&view=artist&id='. $row->id . $itemid);
		?>

        <tr class="sectiontableentry<?php echo $k; ?>" >
        	<td align="right"><?php echo ($i + 1); ?></td>
        	<td><a href='<?php echo $link; ?>'><?php echo $row->artist_name; ?></a></td>
			<td align="right"><?php echo $row->num_albums; ?></td>
            <td align="right"><?php echo $row->num_songs; ?></td>
		</tr>
	<?php $k = 3 - $k; } ?>
    </table>
	
	<?php if($this->params->get('usepaginationartists')){ ?>
	<div align="center">
	<form action="<?php echo $this->action; ?>" method="post" name="adminForm">
	<?php echo JText::_('Display') . " ". $this->pagination->getLimitBox() . "<br />" .  $this->pagination->getPagesLinks() ; ?>
	</form>
	</div>
	<?php } ?>

<?php } ?>

<div align="center"><?php echo MusColHelper::showMusColFooter(); ?></div>
