<?php
/*------------------------------------------------------------------------
# SEO Boss
# ------------------------------------------------------------------------
# author    JoomBoss
# copyright Copyright (C) 2012 Joomboss.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomboss.com
# Technical Support:  Forum - http://joomboss.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
?>
<form action="index.php" method="post" name="adminForm">
<table class="adminlist">
	<thead>
		<tr>
			<th width="20"><input type="checkbox" name="toggle" value=""
				onclick="checkAll(<?php echo
count( $this->rows ); ?>);" /></th>
			<th class="title"><?php echo JText::_( 'SEO_NAME' )?></th>
			<th width="15%"><?php echo JText::_( 'SEO_URL' )?></th>
			<th width="5%" nowrap="nowrap"><?php echo JText::_( 'SEO_ACTIVE' )?></th>
		</tr>
	</thead>
	<?php
	jimport('joomla.filter.output');
	$k = 0;
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
	{
		$row = $rows[$i];
		$checked = JHTML::_('grid.id', $i, $row->id );
		$published = JHTML::_('grid.published', $row, $i );
		$link = JFilterOutput::ampReplace( 'index.php?option=' .
        $option . '&task=edit_keyword&cid[]='. $row->id );
		
		?>
	<tr class="<?php echo "row$k"; ?>">
		<td><?php echo $checked; ?></td>
		<td>
            <a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
        </td>
		<td><?php echo $row->url; ?></td>
		<td align="center"><?php echo $published;?></td>
	</tr>
	<?php
	$k = 1 - $k;
	}
	?>
</table>
<input type="hidden" name="option" value="com_seoboss" /> <input
	type="hidden" name="task" value="" /> <input type="hidden"
	name="boxchecked" value="0" /></form>
