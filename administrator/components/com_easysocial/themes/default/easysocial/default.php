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
<form id="adminForm" name="adminForm" method="post" action="index.php">
	<div class="dashboard-stats row" data-dashboard>

		<div class="col-lg-7">
			<div class="panel">
				<ul class="panel-head panel-tabs list-unstyled clearfix">
					<li class="tab-item active" data-form-tabs data-item="signup">
						<a href="#signup-tabs" data-bs-toggle="tab"><?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_RECENT_SIGNUPS' ); ?></a>
					</li>
					<li class="tab-item" data-form-tabs data-item="emails">
						<a href="#emails-tabs" data-bs-toggle="tab"><?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_MAIL_ACTIVITIES' ); ?></a>
					</li>
					<li class="tab-item" data-form-tabs data-item="pending">
						<a href="#pending-tabs" data-bs-toggle="tab">
							<?php echo JText::_('COM_EASYSOCIAL_DASHBOARD_PENDING_APPROVALS'); ?>

							<?php if ($totalPending > 0) { ?>
							<span class="badge pull-right ml-5"><?php echo $totalPending;?></span>
							<?php } ?>
						</a>
					</li>
				</ul>

				<div class="tab-content tab-content-side">
					<div class="tab-pane active in" id="signup-tabs">
						<?php echo $this->loadTemplate('admin/easysocial/widget.registration', array( 'signupData' => $signupData , 'axes' => $axes ) ); ?>
					</div>
					<div class="tab-pane" id="emails-tabs">
						<?php echo $this->loadTemplate('admin/easysocial/widget.emails' , array( 'mailStats' => $mailStats , 'axes' => $axes ) ); ?>
					</div>
					<div class="tab-pane" id="pending-tabs">
						<?php echo $this->loadTemplate('admin/easysocial/widget.pending.users' , array( 'pendingUsers' => $pendingUsers ) ); ?>
					</div>
				</div>
			</div>

			<?php echo $this->loadTemplate('admin/easysocial/widget.app.news'); ?>
		</div>

		<div class="col-lg-5">
			<?php echo $this->includeTemplate('admin/easysocial/widget.stats', array('totalUsers' => $totalUsers , 'totalOnline' => $totalOnline)); ?>
			<?php echo $this->includeTemplate('admin/easysocial/widget.map'); ?>
		</div>

	</div>

	<input type="hidden" name="boxchecked" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="view" value="" />
	<input type="hidden" name="controller" value="easysocial" />
	<?php echo $this->html( 'form.token' ); ?>
</form>
