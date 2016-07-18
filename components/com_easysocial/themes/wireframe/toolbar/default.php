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
<div class="navbar es-toolbar wide" data-notifications>
	<div class="navbar-inner">
		<div class="es-toolbar-wrap">

			<!-- Reserve the dom and use css to hide the menu for guest. -->
			<ul class="fd-nav pull-left <?php echo (!$this->my->guest) == '' ? 'invisible' : '';?>">
				<?php if ($dashboard) { ?>
				<li class="toolbarItem toolbar-home" data-toolbar-item>
					<a data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_DASHBOARD' , true );?>"
						data-placement="top"
						data-es-provide="tooltip"
						href="<?php echo FRoute::dashboard();?>"
					>
						<i class="fa fa-home"></i>
						<span class="visible-phone"><?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_DASHBOARD' , true );?></span>
					</a>
				</li>
				<li class="divider-vertical"></li>
				<?php } ?>

				<?php if ($friends) { ?>
					<?php echo $this->loadTemplate('site/toolbar/default.friends', array('requests' => $newRequests, 'popboxPosition' => $popboxPosition, 'popboxCollision' => $popboxCollision)); ?>
				<?php } ?>

				<?php if ($conversations) { ?>
					<?php echo $this->loadTemplate('site/toolbar/default.conversations', array('newConversations' => $newConversations, 'popboxPosition' => $popboxPosition, 'popboxCollision' => $popboxCollision)); ?>
				<?php } ?>

				<?php if ($notifications) { ?>
					<?php echo $this->loadTemplate('site/toolbar/default.notifications', array('newNotifications' => $newNotifications, 'popboxPosition' => $popboxPosition, 'popboxCollision' => $popboxCollision)); ?>
				<?php } ?>

			</ul>

			<?php if ($search) { ?>

			<form action="<?php echo JRoute::_('index.php');?>" method="post">
				<div class="fd-navbar-search pull-right" data-nav-search>
					<i class="fa fa-search"></i>
					<input type="text" name="q" class="search-query" autocomplete="off" data-nav-search-input placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_SEARCH' , true );?>" />
				</div>


				<?php if (isset($filterTypes) && $filterTypes) { ?>
				<div class="es-navbar-search-filter dropdown pull-right" data-nav-search-filter>
					<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" data-filter-button>
						<span class="fa fa-cog"></span>
					</a>
					<ul class="es-navbar-dropdown es-dropdown-right fd-reset-list dropdown-menu">
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

			<ul class="fd-nav pull-right">
				<?php if ($this->my->guest && ($login)) { ?>
				<li class="dropdown_">
					<?php echo $this->includeTemplate( 'site/toolbar/default.login' , array( 'facebook' => $facebook )); ?>
				</li>
				<?php } ?>

				<?php if (!$this->my->guest && $profile){ ?>
					<?php echo $this->includeTemplate('site/toolbar/default.profile'); ?>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
