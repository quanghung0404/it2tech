<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<?php
	if($this->params->get('showletternavigation')){
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

<form action="index.php" method="get">
    <div class="searchalbums">
    <?php echo MusColHelper::searchalbums_form_content($this->genre_list, $this->params->get('itemid')); ?>
    </div>
</form>

<?php } ?>

<?php if($this->params->get('showsongsearch')){ ?>

<form action="index.php" method="get">
<div class="searchsongs">
    <?php echo MusColHelper::searchsongs_form_content($this->genre_list, $this->params->get('itemid')); ?>
    </div>
</form>

<?php }

echo $this->introtext2;

} ?>

<?php

if($this->params->get('showartistshome') || $this->letter != ""){

	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$this->artist =$this->items[$i];
		echo $this->loadTemplate('artist');
	} 
	?>
	
	<?php if($this->params->get('usepaginationartists')){ ?>
	<div align="center">
	<form action="<?php echo $this->action; ?>" method="post" name="adminForm">
	<?php echo JText::_('Display') . " ". $this->pagination->getLimitBox() . "<br />" .  $this->pagination->getPagesLinks() ; ?>
	</form>
	</div>
	<?php } ?>

<?php } ?>

<div align="center"><?php echo MusColHelper::showMusColFooter(); ?></div>
