<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
  
    <table class="adminlist table table-striped">
      <thead>
        <tr>
          <th class="hidden-phone" width="5"> <?php echo JHTML::_( 'grid.sort', 'ID', 'c.id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone" width="20"> <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
          </th>
          <th > <?php echo JHTML::_( 'grid.sort', 'User', 'u.name', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone" width="170"> <?php echo JHTML::_( 'grid.sort', 'Date', 'c.date', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th width="250"> <?php echo JText::_( 'Item' ); ?> </th>
          <th> <?php echo JText::_( 'Comment' ); ?> </th>
        </tr>
      </thead>
      <?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row =$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		//$link 		= JRoute::_( 'index.php?option=com_muscol&controller=artist&task=edit&cid[]='. $row->id );
		?>
      <tr class="<?php echo "row$k"; ?>">
        <td class="hidden-phone"><?php echo $row->id; ?></td>
        <td class="hidden-phone"><?php echo $checked; ?></td>
        <td><?php echo $row->name; ?></td>
        <td class="hidden-phone"><?php echo JHTML::_('date',$row->date, JText::_('DATE_FORMAT_LC3')); ?></td>
        <td><?php 
		switch($row->comment_type){
			case "song":
			echo $row->song_name . " [".JText::_('song')."]";
			break;
			case "album": case "":
			echo $row->album_name . " [".JText::_('album')."]";
			break;
			case "artist":
			echo $row->artist_name . " [".JText::_('artist')."]";
			break;
			case "playlist":
			echo $row->playlist_name . " [".JText::_('playlist')."]";
			break;
			
		} 
		?></td>
        <td><?php echo $row->comment; ?></td>
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
  <input type="hidden" name="controller" value="comment" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
