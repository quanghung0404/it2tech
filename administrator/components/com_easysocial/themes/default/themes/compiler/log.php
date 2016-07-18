<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<table class="table table-condensed es-theme-compiler-log" data-log>
	<tbody>
		<?php if ($log) { ?>
		<?php foreach($log['details'] as $key => $detail) { ?>
		<tr class="<?php echo $this->html('bootstrap.state', 'table', $detail['type']); ?>">
			<td><?php echo FD::date($detail['timestamp'])->toFormat('H:i:s'); ?></td>
			<td><?php echo $detail['message']; ?></td>
		</tr>
		<?php } ?>
		<tr class="<?php echo $this->html('bootstrap.state', 'table', $log['state']); ?>">
			<td><?php echo FD::date($log['time_end'])->toFormat('H:i:s'); ?></td>
			<?php if ($log['failed']) { ?>
			<td><?php echo JText::_('COM_EASYSOCIAL_THEMES_COMPILER_TASK_FAILED'); ?></td>
			<?php } else {  ?>
			<td><?php echo JText::_('COM_EASYSOCIAL_THEMES_COMPILER_TASK_COMPLETED'); ?></td>
			<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr class="info">
			<td colspan="2">
				<span class="pull-left"><i class="fa fa-clock-o"></i> <b><?php echo JText::_('COM_EASYSOCIAL_THEMES_COMPILER_TOTAL_TIME'); ?>:</b> <span data-time-total><?php echo round($log['time_total'], 2); ?>s</span></span>
				<span class="pull-right"><i class="fa fa-bars"></i> <b><?php echo JText::_('COM_EASYSOCIAL_THEMES_COMPILER_MEMORY_PEAK_USAGE'); ?>:</b> <span data-memory-usage><?php echo round(FD::math()->convertUnits($log['mem_peak'], 'B', 'MB'), 2); ?>mb</span></span>
			</td>
		</tr>
	</tfoot>
</table>
