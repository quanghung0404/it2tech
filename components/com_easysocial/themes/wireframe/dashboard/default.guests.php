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
<?php if ($this->template->get('dashboard_login_guests')) { ?>
	<?php echo $this->includeTemplate('site/dashboard/default.guests.login'); ?>
<?php } ?>

<div class="es-dashboard" data-dashboard>
	<div class="es-container">
		<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
			<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
		</a>
		<div class="es-sidebar" data-sidebar data-dashboard-sidebar>

			<?php echo $this->render('module', 'es-dashboard-sidebar-top', 'site/dashboard/sidebar.module.wrapper'); ?>

			<div class="es-widget es-widget-borderless">
				<div class="es-widget-head">
					<?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_NEWSFEEDS' );?>
				</div>

				<div class="es-widget-body">
					<ul class="fd-nav fd-nav-stacked feed-items" data-dashboard-feeds>

						<li class="<?php echo $filter == 'everyone' ? ' active' : '';?>"
							data-dashboardSidebar-menu
							data-dashboardFeeds-item
							data-type="everyone"
							data-id=""
							data-url="<?php echo FRoute::dashboard( array( 'type' => 'everyone' ) );?>"
							data-title="<?php echo $this->html( 'string.escape' , $this->my->getName() ) . ' - ' . JText::_( 'COM_EASYSOCIAL_DASHBOARD_FEED_DASHBOARD_EVERYONE' , true ); ?>"
						>
							<a href="<?php echo FRoute::dashboard( array( 'type' => 'everyone' ) ); ?>">
								<i class="fa fa-globe mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_NEWSFEEDS_EVERYONE' );?>
								<div class="label label-notification pull-right mr-20" data-stream-counter-everyone>0</div>
							</a>
						</li>

					</ul>
				</div>

			</div>

			<?php echo $this->render('module', 'es-dashboard-sidebar-after-newsfeeds', 'site/dashboard/sidebar.module.wrapper'); ?>
			<?php echo $this->render('module', 'es-dashboard-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>
		</div>

		<div class="es-content" data-dashboard-content>

			<i class="loading-indicator fd-small"></i>

			<?php echo $this->render('module', 'es-dashboard-before-contents'); ?>

			<div data-dashboard-real-content>
				<div class="es-snackbar">
					<div class="row-table">
						<div class="col-cell"><?php echo JText::_('COM_EASYSOCIAL_RECENT_UPDATES');?></div>

						<?php if ($this->config->get('stream.rss.enabled', true)) { ?>
						<div class="col-cell">
							<a href="<?php echo $rssLink;?>" class="fd-small pull-right subscribe-rss btn-subscribe-rss" target="_blank">
								<i class="fa fa-rss-square"></i>&nbsp; <?php echo JText::_('COM_EASYSOCIAL_SUBSCRIBE_VIA_RSS');?>
							</a>
						</div>
						<?php } ?>
					</div>
				</div>

				<?php if ($hashtag) { ?>
				<div class="es-streams">
					<div class="row">
						<div class="col-md-12">
							<h3 class="pull-left">
								<a href="<?php echo FRoute::dashboard( array( 'layout' => 'hashtag' , 'tag' => $hashtagAlias ) );?>">#<?php echo $hashtag; ?></a>
							</h3>
						</div>
					</div>
					<p class="fd-small">
						<?php echo JText::sprintf('COM_EASYSOCIAL_STREAM_HASHTAG_CURRENTLY_FILTERING', '<a href="' . FRoute::dashboard(array('layout' => 'hashtag', 'tag' => $hashtagAlias)) . '">#' . $hashtag . '</a>'); ?>
					</p>
				</div>
				<hr />
				<?php } ?>

				<div data-unity-real-content>
					<?php echo $stream->html(false, JText::_('COM_EASYSOCIAL_UNITY_STREAM_LOGIN_TO_VIEW')); ?>
					<?php echo $this->includeTemplate('site/dashboard/default.stream.login'); ?>
				</div>
			</div>

			<?php echo $this->render( 'module' , 'es-dashboard-after-contents' ); ?>
		</div>
	</div>
</div>
