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
<ul class="list-unstyled es-reports" data-reporters>
	<?php if ($reporters) { ?>
		<?php foreach( $reporters as $report ){ ?>
		<li class="es-report" data-reporters-item data-id="<?php echo $report->id;?>">

				<div class="es-report-msg">
				<?php if (!$report->message) { ?>
					<i><?php echo JText::_('COM_EASYSOCIAL_REPORTS_USER_DID_NOT_PROVIDE_ANY_MESSAGE'); ?></i>
				<?php } else { ?>
					<?php echo $report->get( 'message' ); ?>
				<?php } ?>
			</div>

			<div class="pull-left es-report-reporter">
				<a href="<?php echo $report->getUser()->getPermalink(true, true);?>" class="es-avatar es-avatar-xs pull-left" target="_blank">
					<img src="<?php echo $report->getUser()->getAvatar();?>" alt="<?php echo $this->html( 'string.escape' , $report->getUser()->getName() ); ?>" />
				</a>
				<span class="es-report-username ml-10">
					<a href="<?php echo $report->getUser()->getPermalink(true, true);?>" target="_blank"><?php echo $report->getUser()->getName();?></a>
				</span>

				<span class="es-report-ip">
					<?php echo $report->ip;?>
				</span>

			</div>

			<div class=" pull-right es-report-action">
				<a class="btn btn-es-danger btn-sm btn-remove" data-remove-item>
					<i class="fa fa-remove"></i>
					<?php echo JText::_('COM_EASYSOCIAL_REMOVE_REPORT_BUTTON'); ?>
				</a>
			</div>
		</li>
		<?php } ?>
	<?php } ?>
</ul>
