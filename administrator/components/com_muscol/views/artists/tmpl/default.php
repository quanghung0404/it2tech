<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<div id="filter-bar" class="btn-toolbar"> <?php echo JText::_( 'Filter' ); ?>:
    <div class="filter-search btn-group pull-left">
      <input type="text" name="keywords" id="keywords" value="<?php echo $this->keywords;?>" placeholder="<?php echo JText::_('Type something...'); ?>" class="inputbox" onchange="document.adminForm.submit();" />
    </div>
    <div class="filter-search btn-group pull-left">
      <?php echo $this->lists['letter']; ?>
    </div>
    <div class="btn-group pull-left">
      <button class="btn" onclick="this.form.submit();"><i class="icon-search"></i></button>
    </div>
  </div>
  
    <table class="adminlist table table-striped">
      <thead>
        <tr>
          <th class="hidden-phone" width="5"> <?php echo JHTML::_( 'grid.sort', 'ID', 'ar.id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone" width="20"> <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
          </th>
          <th> <?php echo JHTML::_( 'grid.sort', 'Artist', 'ar.class_name', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'Added by', 'u.name', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'Added on', 'ar.added', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th> <?php echo JHTML::_( 'grid.sort', 'Letter', 'ar.letter', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
        </tr>
      </thead>
      <?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row =$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_muscol&controller=artist&task=edit&cid[]='. $row->id );
		?>
      <tr class="<?php echo "row$k"; ?>">
        <td class="hidden-phone"><?php echo $row->id; ?></td>
        <td class="hidden-phone"><?php echo $checked; ?></td>
        <td><a href="<?php echo $link; ?>"><?php echo $row->artist_name; ?></a></td>
        <td class="hidden-phone"><?php echo $row->username; ?></td>
        <td class="hidden-phone"><?php echo JHTML::_('date', $row->added, JText::_('DATE_FORMAT_LC3')); ?></td>
        <td><?php echo $this->letters[$row->letter]; ?></td>
      </tr>
      <?php
		$k = 1 - $k;
	}
	?>
      <tfoot>
        <tr>
          <td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
      </tfoot>
    </table>
  
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="controller" value="artist" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
