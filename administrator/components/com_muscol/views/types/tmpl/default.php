<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
 
    <table class="adminlist table table-striped">
      <thead>
        <tr>
          <th class="hidden-phone" width="5"> <?php echo JText::_( 'ID' ); ?> </th>
          <th class="hidden-phone" width="20"> <?php 
				$counter = 0;
				for ($i=0, $n=count( $this->items ); $i < $n; $i++){ 
							if($this->items[$i]->num_albums == 0) $counter++;
				}
				?>
            <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
          </th>
          <th> <?php echo JText::_( 'Type name' ); ?> </th>
        </tr>
      </thead>
      <?php
	$k = 0;
	for ($i=0, $n=count( $this->items ),$check_i = 0; $i < $n; $i++)	{
		$row =$this->items[$i];
		if($row->num_albums) $checked 	= "";
		else {
			$checked 	= JHTML::_('grid.id',   $check_i, $row->id );
			$check_i++;
		}
		$link 		= JRoute::_( 'index.php?option=com_muscol&controller=type&task=edit&cid[]='. $row->id );
		?>
      <tr class="<?php echo "row$k"; ?>">
        <td class="hidden-phone"><?php echo $row->id; ?></td>
        <td class="hidden-phone"><?php echo $checked; ?></td>
        <td ><a href="<?php echo $link; ?>"><?php echo $row->type_name; ?></a></td>
      </tr>
      <?php
		$k = 1 - $k;
	}
	?>
    </table>
 
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="controller" value="type" />
</form>
