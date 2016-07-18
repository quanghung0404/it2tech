<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-dashboard" data-dashboard>
	<div class="es-container">
		<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
			<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_SIDEBAR_TOGGLE');?>
		</a>
		<div class="es-sidebar" data-sidebar data-dashboard-sidebar>
			<?php echo $this->render('module' , 'es-dashboard-sidebar-top' , 'site/dashboard/sidebar.module.wrapper' ); ?>

			<?php echo $this->render('widgets', SOCIAL_TYPE_USER , 'dashboard' , 'sidebarTop' ); ?>

			<?php echo $this->includeTemplate('site/dashboard/sidebar.feeds'); ?>

			<?php echo $this->render('module' , 'es-dashboard-sidebar-after-newsfeeds' , 'site/dashboard/sidebar.module.wrapper' ); ?>

			<?php if ($this->template->get('dashboard_feeds_groups', true)) { ?>
				<?php echo $this->includeTemplate('site/dashboard/sidebar.groups'); ?>
			<?php } ?>

			<?php if ($this->template->get('dashboard_feeds_events', true)) { ?>
				<?php echo $this->includeTemplate('site/dashboard/sidebar.events'); ?>
			<?php } ?>

			<?php if ($this->template->get('dashboard_show_apps', true)) { ?>
				<?php echo $this->includeTemplate('site/dashboard/sidebar.apps'); ?>
			<?php } ?>

			<?php echo $this->render('widgets' , SOCIAL_TYPE_USER , 'dashboard' , 'sidebarBottom'); ?>

			<?php echo $this->render('module', 'es-dashboard-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>
		</div>

		<div class="es-content" data-dashboard-content>

			<i class="loading-indicator fd-small"></i>

			<?php echo $this->render('module', 'es-dashboard-before-contents'); ?>

			<div data-dashboard-real-content>

				<?php if ($contents) { ?>
					<?php echo $contents; ?>
				<?php } else { ?>
					<?php echo $this->includeTemplate('site/dashboard/feeds'); ?>
				<?php } ?>
			</div>

			<?php echo $this->render('module', 'es-dashboard-after-contents'); ?>
		</div>
	</div>
</div>
