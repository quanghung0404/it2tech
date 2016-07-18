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
<div class="es-profile userProfile" data-id="<?php echo $user->id;?>" data-profile>

	<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
		<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
	</a>

	<?php echo $this->render( 'widgets' , 'user' , 'profile' , 'aboveHeader' , array( $user ) ); ?>

	<?php echo $this->render( 'module' , 'es-profile-before-header' ); ?>

	<!-- Include cover section -->
	<?php echo $this->includeTemplate( 'site/profile/default.header' ); ?>

	<?php echo $this->render( 'module' , 'es-profile-after-header' ); ?>

	<div class="es-container">


		<div class="es-sidebar" data-sidebar>

			<?php if ($appFilters && $this->template->get('profile_feeds_apps')) { ?>
			<div class="es-widget">
				<div class="es-widget-head">
					<span><?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_NEWSFEEDS_APPS'); ?></span>
				</div>
				<div class="es-widget-body">
					<ul class="fd-nav fd-nav-stacked feed-items" data-profile-feeds>
						<?php $i = 1; ?>
						<?php foreach ($appFilters as $appFilter) { ?>
							<?php echo $this->includeTemplate('site/profile/default.sidebar.filter', array('filter' => $appFilter, 'hide' => $i > 3)); ?>
							<?php $i++; ?>
						<?php } ?>

						<?php if (count($appFilters) > 3) { ?>
						<li>
							<a href="javascript:void(0);" class="filter-more" data-app-filters-showall><?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_SHOW_MORE_FILTERS'); ?></a>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php } ?>

			<?php echo $this->render( 'module' , 'es-profile-sidebar-top' ); ?>

			<?php echo $this->render( 'widgets' , 'user' , 'profile' , 'sidebarTop' , array( $user ) ); ?>

			<div class="es-widget">
				<div class="es-widget-head">
					<div class="pull-left widget-title">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_APPS_HEADING' );?>
					</div>

					<?php if ($user->isViewer() && $this->template->get('profile_apps_browse')) { ?>
					<a class="pull-right fd-small" href="<?php echo FRoute::apps();?>">
						<i class="icon-es-add"></i> <?php echo JText::_( 'COM_EASYSOCIAL_BROWSE' ); ?>
					</a>
					<?php } ?>
				</div>
				<div class="es-widget-body">
					<ul class="widget-list fd-nav fd-nav-stacked" data-profile-apps>
						<li
							data-profile-apps-item
							data-layout="custom"
						>
							<a href="<?php echo FRoute::profile(array('id' => $user->getAlias(), 'layout' => 'about')); ?>" data-info <?php if (!empty($infoSteps)) { ?>data-loaded="1"<?php } ?>>
								<i class="icon-es-aircon-user mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_PROFILE_ABOUT'); ?>
							</a>
						</li>

						<?php if (!empty($infoSteps)) { ?>
							<?php foreach ($infoSteps as $step) { ?>
								<?php if (!$step->hide) { ?>
								<li
									class="<?php if ($step->active) { ?>active<?php } ?>"
									data-profile-apps-item
									data-layout="custom"
								>
									<a class="ml-20" href="<?php echo $step->url; ?>" title="<?php echo $step->title; ?>" data-info-item data-info-index="<?php echo $step->index; ?>">
										<i class="fa fa-info  mr-5"></i> <?php echo $step->title; ?>
									</a>
								</li>
								<?php } ?>
							<?php } ?>
						<?php } ?>

						<li class="<?php echo !empty($timeline) ? 'active' : '';?>"
							data-layout="embed"
							data-id="<?php echo $user->id;?>"
							data-namespace="site/controllers/profile/getStream"
							data-embed-url="<?php echo FRoute::profile(array('id' => $user->getAlias(), 'layout' => 'timeline'));?>"
							data-profile-apps-item
							>
							<a href="javascript:void(0);">
								<i class="icon-es-genius mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_TIMELINE' );?>
							</a>
						</li>
						<?php if ($apps) { ?>
							<?php foreach ($apps as $app) { ?>
								<?php $app->loadCss(); ?>
									<li class="app-item<?php echo $activeApp == $app->id ? ' active' : '';?>"
										data-app-id="<?php echo $app->id;?>"
										data-id="<?php echo $user->id;?>"
										data-layout="<?php echo $app->getViews( 'profile' )->type; ?>"
										data-namespace="site/controllers/profile/getAppContents"
										data-canvas-url="<?php echo FRoute::apps( array( 'id' => $app->getAlias() , 'layout' => 'canvas' , 'uid' => $user->getAlias(), 'type' => SOCIAL_TYPE_USER) );?>"
										data-embed-url="<?php echo FRoute::profile( array( 'id' => $user->getAlias() , 'appId' => $app->getAlias() ) );?>"
										data-title="<?php echo $app->getPageTitle(); ?>"
										data-profile-apps-item
									>
										<a href="javascript:void(0);">
											<img src="<?php echo $app->getIcon();?>" class="app-icon-small mr-5" /> <?php echo $app->get( 'title' ); ?>
										</a>
									</li>
							<?php } ?>
						<?php } ?>
					</ul>
				</div>
			</div>

			<?php echo $this->render( 'module' , 'es-profile-sidebar-after-apps' ); ?>

			<?php echo $this->render( 'widgets' , 'user' , 'profile' , 'sidebarBottom' , array( $user ) ); ?>

			<?php echo $this->render( 'module' , 'es-profile-sidebar-bottom' ); ?>
		</div>

		<div class="es-content" data-profile-contents>
			<i class="loading-indicator fd-small"></i>

			<?php echo $this->render( 'widgets' , 'user' , 'profile' , 'aboveStream' , array( $user ) ); ?>

			<?php echo $this->render( 'module' , 'es-profile-before-contents' ); ?>
			<div data-profile-real-content>
			<?php echo $contents; ?>
			</div>
			<?php echo $this->render( 'module' , 'es-profile-after-contents' ); ?>
		</div>

	</div>

</div>
