<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
 
    <table class="adminlist table table-striped">
      <thead>
        <tr>
          <th class="hidden-phone" width="5"> <?php echo JText::_( 'ID' ); ?> </th>
          <th class="hidden-phone" width="20"> <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
          </th>
          <th width="20"> <?php echo JText::_( 'Icon' ); ?> </th>
          <th> <?php echo JText::_( 'Tag name' ); ?> </th>
        </tr>
      </thead>
      <?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row =$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_muscol&controller=tag&task=edit&cid[]='. $row->id );
		?>
      <tr class="<?php echo "row$k"; ?>">
        <td class="hidden-phone"><?php echo $row->id; ?></td>
        <td class="hidden-phone"><?php echo $checked; ?></td>
        <td ><img src="../images/tags/<?php echo $row->icon; ?>" /></td>
        <td ><a href="<?php echo $link; ?>"><?php echo $row->tag_name; ?></a></td>
      </tr>
      <?php
		$k = 1 - $k;
	}
	?>
    </table>
  
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="controller" value="tag" />
</form>
