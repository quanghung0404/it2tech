<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php 	if($this->params->get('showletternavigation')){
		echo MusColHelper::letter_navigation('');
	}
?>


  <form action="<?php echo JRoute::_('index.php'); ?>" method="get">
  <div class="searchsongs">
    <?php echo MusColHelper::searchsongs_form_content($this->genre_list, $this->params->get('itemid')); ?>
    </div>
  </form>
 
<form action="<?php echo JRoute::_('index.php'); ?>" method="get" name="adminForm" id="adminForm">
  <div class="searchalbums"> <?php echo MusColHelper::searchalbums_form_content($this->genre_list, $this->params->get('itemid')); ?> </div>
  <?php if($this->params->get('showorderbyalbumsearch')){ ?>
  <div class="orderby_search"> <?php echo JText::_('SORT_BY') . ' ' . MusColHelper::orderby_dropdown(false, 'orderby', $this->params->get('submitchange_albumsearch', true)); ?> </div>
  <?php } ?>
  <?php
if($this->_layout == "grid") echo "<ul class='albums_grid'>" ;

foreach ($this->albums as $this->detail_album)	{
	echo $this->loadTemplate('album');          
}
if($this->_layout == "grid") echo "</ul>" ;
?>
  <div class="muscol_pagination" align="center"> <?php echo $this->pagination->getListFooter(); ?> </div>
</form>
<div align="center"><?php echo MusColHelper::showMusColFooter(); ?></div>
