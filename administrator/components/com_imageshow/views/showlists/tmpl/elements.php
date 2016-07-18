<?php
/**
 * @version    $Id: elements.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<form
	action="index.php?option=com_imageshow&controller=showlist&task=elements&tmpl=component"
	method="post" name="adminForm" id="adminForm">
	<div id="jsn-image-source-profile-details" class="jsn-bootstrap">
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th width="10" class="center">#</th>
					<th width="75%"><?php echo JText::_("TITLE"); ?>
					</th>
					<th width="20" nowrap="nowrap" class="center"><?php echo JText::_('HITS'); ?>
					</th>
					<th width="20" nowrap="nowrap" class="center"><?php echo JText::_('ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count($this->items); $i < $n; $i++)
			{
				$row = $this->items[$i];
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td class="center"><?php echo $i + 1; ?>
					</td>

					<td><?php echo $this->escape($row->showlist_title); ?>
					</td>
					<td class="center"><?php echo $row->hits;?></td>
					<td class="center"><?php echo $row->showlist_id;?></td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="option" value="com_imageshow" /> <input
		type="hidden" name="task" value="elements" /> <input type="hidden"
		name="controller" value="showlist" />
	<?php echo JHTML::_('form.token'); ?>
</form>
