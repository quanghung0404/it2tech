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
<div class="panel">
	<div class="panel-body dash-summary">
		<section class="dash-version is-loading" data-version-status>
			<div class="row-table">
				<div class="col-cell cell-icon cell-tight">
					<i class="fa fa-thumbs-down"></i>
					<i class="fa fa-thumbs-up"></i>
					<b class="o-loader"></b>
				</div>

				<div class="col-cell">
					<h4 class="heading-outdated text-danger"><?php echo JText::_('COM_EASYSOCIAL_VERSION_OUTDATED_VERSION_INFO');?></h4>
					<h4 class="heading-updated"><?php echo JText::_('COM_EASYSOCIAL_VERSION_HEADER_UP_TO_DATE');?></h4>
					<h4 class="heading-loading"><?php echo JText::_('COM_EASYSOCIAL_CHECKING_VERSIONS');?></h4>
					<div class="version-installed hide" data-version-installed>
						<?php echo JText::_('COM_EASYSOCIAL_VERSION_INSTALLED_VERSION');?>: <span data-current-version></span>
						<span class="version-latest text-success">&nbsp; <?php echo JText::_('COM_EASYSOCIAL_VERSION_LATEST_VERSION');?>: <span data-latest-version></span></span>
						</div>
						</div>

				<div class="col-cell cell-btn cell-tight">
					<a href="<?php echo JRoute::_('index.php?option=com_easysocial&launchInstaller=1');?>" class="btn btn-default"><?php echo JText::_('COM_EASYSOCIAL_GET_UPDATES_BUTTON');?></a>
				</div>
			</div>
		</section>

		<section class="dash-stat">
			<div class="text-center clearfix">
				<div class="dash-stat-item">
					<a href="<?php echo JRoute::_('index.php?option=com_easysocial&view=users');?>">
						<i class="fa fa-user"></i>
						<b><?php echo $totalUsers; ?></b>
						<div><?php echo JText::_('COM_EAYSOCIAL_USERS');?></div>
					</a>
				</div>

				<div class="dash-stat-item">
					<a href="<?php echo JRoute::_('index.php?option=com_easysocial&view=groups');?>">
						<i class="fa fa-users"></i>
						<b><?php echo $totalGroups;?></b>
						<div><?php echo JText::_('COM_EASYSOCIAL_GROUPS');?></div>
					</a>
				</div>

				<div class="dash-stat-item">
					<a href="<?php echo JRoute::_('index.php?option=com_easysocial&view=events');?>">
						<i class="fa fa-calendar"></i>
						<b><?php echo $totalEvents;?></b>
						<div><?php echo JText::_('COM_EASYSOCIAL_WIDGETS_STATS_TOTAL_EVENTS');?></div>
					</a>
				</div>

				<div class="dash-stat-item">
					<i class="fa fa-smile-o"></i>
					<b><?php echo $totalOnline;?></b>
					<div><?php echo JText::_('COM_EASYSOCIAL_ONLINE');?></div>
				</div>

				<div class="dash-stat-item">
					<a href="<?php echo JRoute::_('index.php?option=com_easysocial&view=albums');?>">
						<i class="fa fa-photo"></i>
						<b><?php echo $totalAlbums;?></b>
						<div><?php echo JText::_('COM_EASYSOCIAL_WIDGETS_STATS_TOTAL_ALBUMS');?></div>
					</a>
				</div>

				<div class="dash-stat-item">
					<a href="<?php echo JRoute::_('index.php?option=com_easysocial&view=videos');?>">
						<i class="fa fa-film"></i>
						<b><?php echo $totalVideos;?></b>
						<div><?php echo JText::_('COM_EASYSOCIAL_WIDGETS_STATS_TOTAL_VIDEOS');?></div>
					</a>
				</div>

				<div class="dash-stat-item">
					<a href="<?php echo JRoute::_('index.php?option=com_easysocial&view=reports');?>">
						<i class="fa fa-warning"></i>
						<b><?php echo $totalReports;?></b>
						<div><?php echo JText::_('COM_EASYSOCIAL_WIDGETS_STATS_TOTAL_REPORTS');?></div>
					</a>
				</div>
			</div>
		</section>

		<section class="dash-social">
			<strong>Stay Updated</strong>
			<div>
				<i class="fa fa-facebook-square"></i>
				<span>
					<a href="https://facebook.com/StackIdeas" target="_blank" class="text-inherit">Like us on Facebook</a>
				</span>
				</div>
			<div>
				<i class="fa fa-twitter-square"></i>
				<span>
					<a href="https://twitter.com/StackIdeas" target="_blank" class="text-inherit">Follow us on Twitter</a>
				</span>
			</div>
			<div>
				<i class="fa fa-book"></i>
				<span>
					<a href="http://stackideas.com/docs/easysocial/" target="_blank" class="text-inherit">View Product Documentation</a>
				</span>
		</div>
		</section>
	</div>
</div>
