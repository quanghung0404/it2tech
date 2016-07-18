<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="panel-table">
		<table class="app-table table table-striped table-eb" data-table-grid>
			<thead>
				<th width="5">
					<input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
				</th>

				<th style="text-align: left;">
					<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_TEAM');?>
				</th>

				<th width="10%" class="center">
					<?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_REQUEST_DATE' ); ?>
				</th>

				<th width="1%" class="center">
					<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_ID'); ?>
				</th>
			</thead>
			<tbody>
				<?php if ($requests) { ?>
					<?php $i = 0; ?>
					<?php foreach ($requests as $request){ ?>
					<tr>
						<td class="center">
							<?php echo $this->html('grid.id', $i, $request->id); ?>
						</td>

						<td>
							<?php echo JText::sprintf('COM_EASYBLOG_TEAMBLOGS_REQUESTED_TO_JOIN_THE_TEAM', $request->user->getName(), $request->title); ?>
						</td>

						<td class="center">
							<?php echo EB::date()->dateWithOffSet($request->created)->format($this->config->get('layout_dateformat', JText::_('DATE_FORMAT_LC2')) ); ?>
						</td>

						<td class="center">
							<?php echo $request->id;?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="6" class="empty">
							<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_NO_REQUEST');?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="task" value="" />
</form>
