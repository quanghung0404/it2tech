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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<form method="post" action="<?php echo JRoute::_('index.php');?>" data-profile-privacy-form class="form-horizontal">
<div class="es-container" data-edit-privacy>
	<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
		<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_SIDEBAR_TOGGLE');?>
	</a>
	<div class="es-sidebar" data-sidebar>

		<?php echo $this->render('module', 'es-profile-editprivacy-sidebar-top'); ?>

		<div class="es-widget es-widget-borderless">

			<div class="es-widget-head"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_PRIVACY');?></div>

			<div class="es-widget-body">
				<ul class="fd-nav fd-nav-stacked privacy-groups">
				<?php $i = 0; ?>
				<?php  foreach ($privacy as $group => $items) {  ?>
					<li class="privacy-groups-item <?php echo $i == 0 ? 'active' : '';?>"
						data-profile-privacy-item
						data-group="<?php echo $group; ?>"
					>
						<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_PRIVACY_GROUP_' . strtoupper($group)); ?></a>
					</li>
					<?php $i++; ?>
				<?php } ?>
				</ul>
			</div>
		</div>

		<div class="es-widget es-widget-borderless">
			<div class="es-widget-head"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_PRIVACY_BLOCKED_USERS');?></div>

			<div class="es-widget-body">
				<ul class="fd-nav fd-nav-stacked privacy-groups">
					<li class="privacy-groups-item" data-profile-privacy-item data-group="blocked">
						<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_PRIVACY_MANAGE_BLOCKED_USERS'); ?></a>
					</li>
				</ul>
			</div>
		</div>
		<?php echo $this->render('module', 'es-profile-editprivacy-sidebar-bottom'); ?>
	</div>


	<div class="es-content">
		<?php echo $this->render('module', 'es-profile-editprivacy-before-contents'); ?>

		<div class="form-privacy form-horizontal pl-15 pr-15">
			<?php if ($privacy) { ?>
				<?php foreach ($privacy as $group => $items) { ?>
				<div class="privacy-contents privacy-content-<?php echo $group; ?>" data-privacy-content data-group="<?php echo $group; ?>">

					<div class="h4 es-title-font"><?php echo JText::_('COM_EASYSOCIAL_PRIVACY_GROUP_' . strtoupper($group)); ?></div>
					<hr />

					<p class="fd-small mb-20">
						<?php echo JText::_('COM_EASYSOCIAL_PRIVACY_GROUP_' . strtoupper($group) . '_DESC'); ?>
					</p>

					<?php foreach ($items as $item) { ?>
					<div class="form-group" data-privacy-item>

						<i class="fa fa-question-circle pull-right" <?php echo $this->html('bootstrap.popover', $item->label , $item->tips , 'bottom'); ?>></i>

						<label class="col-sm-3 control-label"><?php echo $item->label; ?></label>

						<div class="col-sm-8">
							<select autocomplete="off" class="form-control input-sm privacySelection" name="privacy[<?php echo $item->groupKey;?>][<?php echo $item->rule;?>]" data-privacy-select>
								<?php foreach ($item->options as $option => $value) { ?>
									<option value="<?php echo $option;?>"<?php echo $value ? ' selected="selected"' : '';?>>
										<?php echo JText::_('COM_EASYSOCIAL_PRIVACY_OPTION_' . strtoupper($option));?>
									</option>
								<?php } ?>
							</select>

							<a href="javascript:void(0);" style="<?php echo !$item->hasCustom ? 'display:none;' : '';?>" data-privacy-custom-edit-button>
								<i class="icon-es-settings"></i>
							</a>

							<div class="dropdown-menu dropdown-arrow-topleft privacy-custom-menu" style="display:none;" data-privacy-custom-form>
								<div class="fd-small mb-10 row">
									<div class="col-md-12">
										<?php echo JText::_('COM_EASYSOCIAL_PRIVACY_CUSTOM_DIALOG_NAME'); ?>
										<a href="javascript:void(0);" class="pull-right" data-privacy-custom-hide-button>
											<i class="fa fa-remove " title="<?php echo JText::_('COM_EASYSOCIAL_PRIVACY_CUSTOM_DIALOG_HIDE' , true );?>"></i>
										</a>
									</div>
								</div>
								<div class="textboxlist" data-textfield>
									<?php if ($item->hasCustom) { ?>
										<?php foreach ($item->customUsers as $friend) { ?>
										<div class="textboxlist-item" data-id="<?php echo $friend->id; ?>" data-title="<?php echo $friend->getName(); ?>" data-textboxlist-item>
											<span class="textboxlist-itemContent" data-textboxlist-itemContent><?php echo $friend->getName(); ?><input type="hidden" name="items" value="<?php echo $friend->id; ?>" /></span>
											<a class="textboxlist-itemRemoveButton" href="javascript: void(0);" data-textboxlist-itemRemoveButton></a>
										</div>
										<?php } ?>
									<?php } ?>
									<input type="text" class="textboxlist-textField" data-textboxlist-textField placeholder="<?php echo JText::_('COM_EASYSOCIAL_PRIVACY_CUSTOM_DIALOG_ENTER_NAME'); ?>" autocomplete="off" />
								</div>
							</div>

							<input type="hidden" name="privacyID[<?php echo $item->groupKey;?>][<?php echo $item->rule; ?>]" value="<?php echo $item->id . '_' . $item->mapid;?>" />
							<input type="hidden" name="privacyOld[<?php echo $item->groupKey;?>][<?php echo $item->rule; ?>]" value="<?php echo $item->selected; ?>" />
							<input type="hidden" data-hidden-custom name="privacyCustom[<?php echo $item->groupKey;?>][<?php echo $item->rule; ?>]" value="<?php echo implode(',', $item->customIds); ?>" />
						</div>
					</div>
					<?php } ?>
				</div>
				<?php } ?>
			<?php } ?>

			<div class="privacy-contents privacy-content-blocked" data-privacy-content data-group="blocked">
				<div class="h4 es-title-font"><?php echo JText::_('COM_EASYSOCIAL_MANAGE_BLOCKED_USERS'); ?></div>
				<hr />

				<?php if ($blocked) { ?>
					<div class="es-block-users">
						<ul class="es-item-grid es-item-grid_1col" data-users-listing>
							<?php foreach ($blocked as $block) { ?>
							<li class="es-item" data-blocked-user-<?php echo $block->user->id;?> >
								<div class="es-avatar-wrap pull-left">
									<a href="<?php echo $block->user->getPermalink();?>" class="es-avatar pull-left">
										<img src="<?php echo $block->user->getAvatar(SOCIAL_AVATAR_MEDIUM);?>" alt="<?php echo $this->html('string.escape', $block->user->getName() );?>" />
									</a>

									<?php echo $this->loadTemplate('site/utilities/user.online.state', array('online' => $block->user->isOnline() , 'size' => 'small' ) ); ?>
								</div>

								<div class="es-item-body">
									<div class="es-item-detail">
										<div class="es-item-title">
											<a href="<?php echo $block->user->getPermalink();?>"><?php echo $block->user->getName();?></a>
										</div>

										<div class="es-block-reason">
											<?php echo $block->reason;?>
										</div>

										<div class="user-actions">
											<?php echo FD::blocks()->getForm($block->user->id); ?>
										</div>
									</div>
								</div>
							</li>
							<?php } ?>
						</ul>
					</div>
				<?php } else { ?>
					<div class="is-empty">
						<div class="empty center">
							<i class="fa fa-users"></i>
							<?php echo JText::_('COM_EASYSOCIAL_PRIVACY_BLOCKED_NO_USERS_CURRENTLY'); ?>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>

		<div class="ml-20 fd-small" data-form-actions>
			<div class="checkbox">
				<label class="fd-small">
					<input type="checkbox" value="1" name="privacyReset" /> <?php echo JText::_('COM_EASYSOCIAL_PRIVACY_RESET_DESCRIPTION'); ?>
				</label>
			</div>
		</div>

		<div class="form-actions" data-form-actions>
			<div class="pull-right">
				<button class="btn btn-sm btn-es-primary" data-profile-notifications-save><?php echo JText::_('COM_EASYSOCIAL_SAVE_BUTTON');?></button>
			</div>
		</div>
		<?php echo $this->render('module', 'es-profile-editprivacy-after-contents'); ?>
	</div>
</div>
<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="controller" value="profile" />
<input type="hidden" name="task" value="savePrivacy" />
<input type="hidden" name="<?php echo FD::token();?>" value="1" />
</form>
