<?php defined('_JEXEC') or die('Restricted access'); 

$params =JComponentHelper::getParams( 'com_muscol' );

if(!$params->get('albums_view')){
?>

<div class="alert"> ALERT! You have to SAVE the <a href="<?php echo JRoute::_('index.php?option=com_config&view=component&component=com_muscol'); ?>">OPTIONS</a> before continuing to set up basic configuration. Refresh this page when you are done. </div>
<?php } ?>

<?php 

//recursive
$recursive = JRequest::getInt('recursive');

if($recursive){
  
  $start = JRequest::getInt('start') ;
  $total = JRequest::getInt('total') ;
  $num_files = JRequest::getInt('num_files') ;
  
  $js = "jQuery(document).ready(function(){
    process_id3_multiple('".JRequest::getVar('folder')."', '".$start."', '".$num_files."') ;
  });" ;
  $document = JFactory::getDocument();
  $document->addScriptDeclaration($js);

  $percent = ($start / $total ) * 100;
  
  ?>
  <h2><?php echo JText::_('IMPORTING_MULTIPLE_FOLDERS'); ?></h2>
    <h3><span id="imported"><?php echo $start; ?></span> <?php echo JText::_('FOLDERS_PROCESSED_SO_FAR'); ?> <span id="remaining"><?php echo $total - $start; ?></span> <?php echo JText::_('REMAINING'); ?>. <span id="complete_tag" class="text-success hidden"><?php echo JText::_('PROCESS_COMPLETE'); ?></span></h3>
    <h3><span id="imported_files"><?php echo $num_files; ?></span> <?php echo JText::_('FILES_PROCESSED_SO_FAR'); ?></h3>
    
    <div id="thebarcontainer" class="progress progress-striped active">
      <div id="thebar" class="bar" style="width: <?php echo $percent; ?>%;"></div>
    </div>

    <p id="importing_p"><?php echo JText::_('NOW_IMPORTING'); ?> <span id="currently_importing"></span></p>

    <div class="return-btn" align="center">
      <a class="btn btn-success hidden" id="return_button" href="<?php echo JRoute::_('index.php?option=com_muscol&view=albums'); ?>"><?php echo JText::_('RETURN_TO_MAIN_PAGE'); ?></a>
    </div>

    <?php 
}
else{

?>

<div class="hidden-phone">
<fieldset class="adminform" class="hidden-phone">
<legend><?php echo JText::_('FAST_ALBUM_ADDITION'); ?></legend>
<div class="span4 well hidden-phone">
  <?php if($params->get('oauth_token')){ ?>
  <form id="discogs_form" name="discogs_form" method="get" action="index.php" class="form-inline">
    <div class="input-append">
      <input type="text" name="q" id="query" class="inputbox" placeholder="<?php echo JText::_('TYPE_SOMETHING'); ?>" />
      <input type="submit" class="btn" value="<?php echo JText::_('SEARCH_ON_DISCOGS'); ?>"  />
    </div>
    <input type="hidden" name="option" value="com_muscol" />
    <input type="hidden" name="controller" value="albums" />
    <input type="hidden" name="task" value="search_discogs" />
    <span id="searching_discogs" class="searching_discogs" style="display:none;"><?php echo JText::_('SEARCHING'); ?></span>
  </form>
  <?php }else{ ?>

  <?php echo JText::_('REQUEST_DISCOGS_ACCESS_EXPLAIN'); ?>

  <a href="<?php echo JRoute::_('index.php?option=com_muscol&layout=discogs'); ?>" class="btn"><?php echo JText::_('REQUEST_DISCOGS_ACCESS'); ?></a>

  <?php } ?>
</div>
<div class="span4 well hidden-phone">
  <form id="folder_form" name="folder_form" method="get" action="index.php">
    <div class="input-append input-prepend"> <span class="add-on"><?php echo JPATH_SITE  . $params->get('songspath'). DS; ?></span>
      <input type="text" name="folder" id="folder" class="inputbox input-medium" />
      <input type="submit" class="btn" value="<?php echo JText::_('SCAN_FOLDER'); ?>" />
    </div>
    <input type="hidden" name="option" value="com_muscol" />
    <input type="hidden" name="controller" value="albums" />
    <input type="hidden" name="task" value="scan_folder" />
    <span id="scan_folder" class="searching_discogs" style="display:none;"><?php echo JText::_('SCANNING'); ?></span>
  </form>
  
</div>
<div class="clear"></div>
<div id="return_discogs"></div>
<div id="return_new_album_folder"></div>
</fieldset>
</div>
<div id="messages"></div>
<form action="index.php" method="post" name="adminForm" id="adminForm">
  <div id="filter-bar" class="btn-toolbar"> <?php echo JText::_( 'FILTER' ); ?>:
    <div class="filter-search btn-group pull-left">
      <input type="text" name="keywords" id="keywords" value="<?php echo $this->keywords;?>" placeholder="<?php echo JText::_('TYPE_SOMETHING'); ?>" class="inputbox" onchange="document.adminForm.submit();" />
    </div>
    <div class="filter-search btn-group pull-left">
      <?php echo $this->lists['artist_id']; ?>
    </div>
    <div class="btn-group pull-left">
      <button class="btn" onclick="this.form.submit();"><i class="icon-search"></i></button>
    </div>
  </div>
  <table class="table table-striped">
    <thead>
      <tr>
        <th width="5" class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'ID', 'al.id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th width="20" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
          </th>
          <th width="40"> </th>
          <th> <?php echo JHTML::_( 'grid.sort', JText::_('ALBUM'), 'al.name', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th> <?php echo JHTML::_( 'grid.sort', JText::_('ARTIST'), 'ar.artist_name', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', JText::_('YEAR'), 'al.year', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', JText::_('FORMAT'), 'f.format_name', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone"> <?php echo JText::_( 'TYPE' ); ?> </th>
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', JText::_('ADDED_BY'), 'u.name', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', JText::_('ADDED_ON'), 'al.added', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', JText::_('POINTS'), 'al.points', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', JText::_('PRICE'), 'al.price', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
        </tr>
    </thead>
    <?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row =$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_muscol&controller=album&task=edit&cid[]='. $row->id );
		$link_artist= JRoute::_( 'index.php?option=com_muscol&controller=artist&task=edit&cid[]='. $row->artist_id );
		?>
    <tr class="<?php echo "row$k"; ?>">
      <td class="hidden-phone"><?php echo $row->id; ?></td>
      <td class="hidden-phone"><?php echo $checked; ?></td>
      <td><a href="<?php echo $link; ?>" class="thumbnail"><?php echo MusColHelper::createThumbnail($row->image, $row->name, 40); ?></a></td>
      <td><a href="<?php echo $link; ?>"><?php echo $row->name; ?></a></td>
      <td><a href="<?php echo $link_artist; ?>"><?php echo $row->artist_name; ?></a></td>
      <td class="hidden-phone"><?php echo $row->year; ?></td>
      <td class="hidden-phone"><?php echo $row->format_name; ?></td>
      <td class="hidden-phone"><?php echo implode(" / ",$row->types); ?></td>
      <td class="hidden-phone"><?php echo $row->username; ?></td>
      <td class="hidden-phone"><?php echo JHTML::_('date', $row->added, JText::_('DATE_FORMAT_LC3')); ?></td>
      <td class="points hidden-phone" onmouseout="stars_out(<?php echo $row->id; ?>);"><div id="stars_<?php echo $row->id; ?>" title="<?php echo $row->points; ?>" style="display: none;"></div>
        <?php echo MusColHelper::show_stars_admin($row->points,false,$row->id,true); ?></td>
      <td align="right" class="hidden-phone right"><?php echo $row->price; ?> <?php echo $params->get('currency', '&euro;'); ?></td>
    </tr>
    <?php
		$k = 1 - $k;
	}
	?>
    <tfoot>
      <tr>
        <td colspan="12"><?php echo $this->pagination->getListFooter(); ?></td>
      </tr>
    </tfoot>
  </table>
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="controller" value="album" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>

<?php } ?>
