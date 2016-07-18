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
          <th> <?php echo JText::_( 'Format name' ); ?> </th>
          <th> <?php echo JText::_( 'Icon' ); ?> </th>
          <th class="hidden-phone"> <?php echo JText::_( 'Display group' ); ?> </th>
          <th class="hidden-phone" width="8%"> <?php echo JText::_( 'Order' ); ?> <a title="<?php echo JText::_('Save Order'); ?>" href="javascript:Joomla.checkAll(<?php echo $counter; ?>);submitbutton('saveorder');"><i class="icon-save"></i> </a> </th>
          <th class="hidden-phone"> <?php echo JText::_( 'Number of items' ); ?> </th>
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
		$link 		= JRoute::_( 'index.php?option=com_muscol&controller=format&task=edit&cid[]='. $row->id );
		?>
      <tr class="<?php echo "row$k"; ?>">
        <td class="hidden-phone"><?php echo $row->id; ?></td>
        <td class="hidden-phone"><?php echo $checked; ?></td>
        <td <?php if($row->display_group) echo "style='padding-left:50px;'" ?>><a href="<?php echo $link; ?>"><?php echo $row->format_name; ?></a></td>
        <td><img src="../images/formats/<?php echo $row->icon; ?>" /></td>
        <td class="hidden-phone"><?php echo $row->display_group_name; ?></td>
        <td class="hidden-phone"><input type="hidden" value="<?php echo $row->id; ?>" name="order_id[]"/>
          <input class="inputbox input-small" type="text" style="text-align: center;" value="<?php echo $row->order_num; ?>" size="5" name="order[]"/></td>
        <td class="hidden-phone"><?php echo $row->num_albums; ?></td>
      </tr>
      <?php
		$k = 1 - $k;
	}
	?>
    </table>
 
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="controller" value="format" />
</form>
