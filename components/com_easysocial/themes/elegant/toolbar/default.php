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
<div class="es-navbar" data-notifications>
    <ul class="es-nav pull-left fd-reset-list clearfix">
        <li class="dropdown_">
            <a href="#" class="es-nav-dropdown-toggle dropdown-toggle_" data-bs-toggle="dropdown">
                <i class="fa fa-th"></i>
            </a>

            <div class="es-nav-dropdown for-guide dropdown-menu" role="menu">
            	<?php if (!$this->my->guest) { ?>
					<?php if ($dashboard) { ?>
					<div data-toolbar-item>
						<a href="<?php echo FRoute::dashboard();?>">
							<i class="fa fa-home"></i>
							<b><?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_DASHBOARD' , true );?></b>
						</a>
					</div>
					<?php } ?>
				<?php } ?>

            	<?php if ($this->config->get('photos.enabled') && !$this->my->guest){ ?>
				<div>
					<a href="<?php echo FRoute::albums(array('uid' => $this->my->getAlias() , 'type' => SOCIAL_TYPE_USER));?>">
						<i class="fa fa-photo"></i>
						<b><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_PHOTOS');?></b>
					</a>
				</div>
				<?php } ?>

				<?php if ($this->config->get('video.enabled')) { ?>
				<div>
					<a href="<?php echo FRoute::videos();?>">
						<i class="fa fa-film"></i>
						<b><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_VIDEOS');?></b>
					</a>
				</div>
				<?php } ?>

				<?php if ($this->config->get('groups.enabled')){ ?>
				<div>
					<a href="<?php echo FRoute::groups();?>">
						<i class="fa fa-users"></i>
						<b><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_GROUPS');?></b>
					</a>
				</div>
				<?php } ?>

				<?php if ($this->config->get('events.enabled')){ ?>
				<div>
					<a href="<?php echo FRoute::events();?>">
						<i class="fa fa-calendar"></i>
						<b><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_EVENTS');?></b>
					</a>
				</div>
				<?php } ?>

				<?php if ($this->config->get('badges.enabled')){ ?>
				<div>
					<a href="<?php echo FRoute::badges(array('layout' => 'achievements'));?>">
						<i class="fa fa-trophy"></i>
						<b><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_ACHIEVEMENTS');?></b>
					</a>
				</div>
				<?php } ?>

				<?php if ($this->config->get('points.enabled') && !$this->my->guest){ ?>
				<div>
					<a href="<?php echo FRoute::points(array('layout' => 'history' , 'userid' => $this->my->getAlias()));?>">
						<i class="fa fa-dot-circle-o"></i>
						<b><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_POINTS_HISTORY');?></b>
					</a>
				</div>
				<?php } ?>

				<?php if ($this->config->get('conversations.enabled') && !$this->my->guest){ ?>
				<div>
					<a href="<?php echo FRoute::conversations();?>">
						<i class="fa fa-comments"></i>
						<b><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_CONVERSATIONS');?></b>
					</a>
				</div>
				<?php } ?>

				<?php if (!$this->my->guest && $this->config->get('apps.browser')) { ?>
				<div>
					<a href="<?php echo FRoute::apps();?>">
						<i class="fa fa-puzzle-piece"></i>
						<b><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_APPS');?></b>
					</a>
				</div>
				<?php } ?>
            </div>
        </li>
    </ul>
    <ul class="es-nav pull-right fd-reset-list">
    	<?php if ($search) { ?>
    	    <li class="ed-nav-toggle-search-wrap">
    	        <a href="javascript:void(0);" class="es-nav-toggle-search" data-elegant-toggle-search>
    	            <i class="fa fa-search"></i>
    	        </a>
    	    </li>
    	<?php } ?>

    	<?php if (!$this->my->guest) { ?>
			<?php if ($notifications) { ?>
				<?php echo $this->loadTemplate('site/toolbar/default.notifications', array('newNotifications' => $newNotifications, 'popboxPosition' => $popboxPosition, 'popboxCollision' => $popboxCollision)); ?>
			<?php } ?>

			<?php if ($conversations) { ?>
				<?php echo $this->loadTemplate('site/toolbar/default.conversations', array('newConversations' => $newConversations, 'popboxPosition' => $popboxPosition, 'popboxCollision' => $popboxCollision)); ?>
			<?php } ?>

			<?php if ($friends) { ?>
				<?php echo $this->loadTemplate('site/toolbar/default.friends', array('requests' => $newRequests, 'popboxPosition' => $popboxPosition, 'popboxCollision' => $popboxCollision)); ?>
			<?php } ?>
		<?php } ?>

		<?php if ($this->my->guest && ($login)) { ?>
			<li class="dropdown_">
				<?php echo $this->includeTemplate( 'site/toolbar/default.login' , array( 'facebook' => $facebook )); ?>
			</li>
		<?php } ?>

		<?php if (!$this->my->guest && $profile){ ?>
			<?php echo $this->includeTemplate('site/toolbar/default.profile'); ?>
		<?php } ?>
    </ul>

    <?php if ($search) { ?>

		<form action="<?php echo JRoute::_('index.php');?>" method="post" class="es-navbar-form">
			<div class="es-nav-search fd-navbar-search" data-nav-search>
				<i class="fa fa-search"></i>
				<input type="text" name="q" class="search-query" autocomplete="off" data-nav-search-input placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_SEARCH' , true );?>" />
			</div>

			<?php if (isset($filterTypes) && $filterTypes) { ?>
			<div class="es-nav-search-filter dropdown pull-right" data-nav-search-filter>
				<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" data-filter-button>
					<span class="fa fa-cog"></span>
				</a>

				<ul class="es-nav-dropdown es-dropdown-right fd-reset-list dropdown-menu">
					<li class="es-navbar-dropdown-head">
						<div class="es-filter-header">
							<div><?php echo JText::_('COM_EASYSOCIAL_SEARCH_FILTER_DESC');?></div>
						</div>

						<div class="es-filter-helper">
							<div class="col-cell">
								<div class="select-all">
									<a href="javascript:void(0);" data-filter-selectall><?php echo JText::_('COM_EASYSOCIAL_SEARCH_FILTER_SELECT_ALL'); ?></a>
								</div>
							</div>

							<div class="col-cell">
								<div class="deselect-all">
									<a href="javascript:void(0);" data-filter-deselectall><?php echo JText::_('COM_EASYSOCIAL_SEARCH_FILTER_DESELECT_ALL'); ?></a>
								</div>
							</div>
						</div>
					</li>
					<?php
						$count = 0;
						foreach($filterTypes as $fType) {
							$typeAlias = $fType->id . '-' . $fType->title;
					?>
					<li>
						<div class="es-checkbox">
							<input id="search-type-<?php echo $count;?>"
									type="checkbox"
									name="filtertypes[]"
									value="<?php echo $typeAlias; ?>"
									<?php echo (isset($fType->checked) && $fType->checked) ? ' checked="true"' : ''; ?>
									data-search-filtertypes />
							<label for="search-type-<?php echo $count;?>">
								<?php echo $fType->displayTitle;?>
							</label>
						</div>
					</li>
					<?php
							$count++;
						}
					?>
				</ul>
			</div>
			<?php } ?>

			<?php echo $this->html('form.itemid', FRoute::getItemId('search')); ?>
			<input type="hidden" name="controller" value="search" />
			<input type="hidden" name="task" value="query" />
			<input type="hidden" name="option" value="com_easysocial" />
			<input type="hidden" name="<?php echo FD::token();?>" value="1" />
		</form>

	<?php } ?>
</div>
