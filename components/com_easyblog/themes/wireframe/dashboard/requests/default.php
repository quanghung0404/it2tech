<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form method="post" action="<?php echo JRoute::_('index.php');?>" data-eb-dashboard-requests>
	<div class="eb-head">
		<h2 class="eb-head-title reset-heading pull-left">
			<i class="fa fa-files-o"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_TEAM_REQUESTS');?>
		</h2>
	</div>

	<?php if ($requests) { ?>
	<div class="eb-box eb-table">
		<div class="eb-table-body">
			<?php foreach ($requests as $request) { ?>
				<div class="row-table align-middle">
					<div class="col-cell cell-avatar cell-clear-right" style="width: 68px;">
						<img src="<?php echo $request->user->getAvatar();?>" width="48" height="48">
					</div>

					<div class="col-cell cell-bio">
						<b><?php echo $request->user->getName();?></b>
						<div>
							<?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_REQUESTED_TO_JOIN_TEAM', '<span><a href="' . $request->team->getPermalink() . '" target="_blank">' . $request->team->title . '</span></a>'); ?>
						</div>
						<div class="text-muted"><?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_REQUESTED_TO_JOIN_ON', $request->date->format(JText::_('DATE_FORMAT_LC1')));?></div>
					</div>

					<div class="col-cell cell-action text-right">
						<a class="btn btn-danger" href="javascript:void(0);" data-reject-request data-id="<?php echo $request->id;?>"><?php echo JText::_('COM_EASYBLOG_REJECT_REQUEST_BUTTON');?></a>
						<a class="btn btn-primary" href="javascript:void(0);" data-approve-request data-id="<?php echo $request->id;?>"><?php echo JText::_('COM_EASYBLOG_APPROVE_REQUEST_BUTTON');?></a>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>

	<div class="eb-box empty text-center<?php echo !$requests ? ' is-empty' : '';?>">
		<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_REQUESTS_EMPTY'); ?></b>
	</div>

	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="requests" />
	<?php echo $this->html('form.action'); ?>
</form>
