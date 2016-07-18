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
<div class="es-widget widget-groups">
	<div class="es-widget-head">
		<div class="pull-left widget-title">
			<?php echo JText::_('APP_USER_GROUPS_WIDGET_GROUPS_TITLE'); ?>
		</div>
		<span class="widget-label">(<?php echo $total; ?>)</span>

		<?php if ($user->id == $this->my->id && $this->my->getAccess()->allowed('groups.create')) { ?>
			<div class="pull-right fd-small">
				<a href="<?php echo FRoute::groups(array('layout' => 'create'));?>">
					<i class="icon-es-add"></i><?php echo JText::_('COM_EASYSOCIAL_NEW_GROUP');?>
				</a>
			</div>
		<?php } ?>
	</div>
	<div class="es-widget-body">
		<ul class="widget-list fd-nav fd-nav-stacked">
			<?php if ($groups) { ?>
				<?php for ($i = 0; $i < count($groups); $i++) { ?>
				<li data-profile-groups-item class="<?php echo $i >= $limit ? 'hide' : '';?>">
					<div class="media">
						<div class="media-object pull-left">
							<img src="<?php echo $groups[$i]->getAvatar();?>" class="es-avatar es-avatar-sm" />
						</div>

						<div class="media-body">
							<div><?php echo $this->html('html.group', $groups[$i]->id); ?></div>

							<div class="fd-small group-meta">
								<?php echo JText::sprintf(FD::string()->computeNoun('COM_EASYSOCIAL_GROUPS_MEMBERS', $groups[$i]->getTotalMembers()), $groups[$i]->getTotalMembers());?>
							</div>
						</div>
					</div>
				</li>
				<?php } ?>

				<?php if ($total > $limit) { ?>
				<li>
					<a href="javascript:void(0);" data-view-all-groups><?php echo JText::_('APP_USER_GROUPS_VIEW_MORE_GROUPS');?></a>
				</li>
				<?php } ?>
				
			<?php } else { ?>
				<li class="empty fd-small">
					<?php echo JText::_('APP_USER_GROUPS_WIDGET_NO_GROUPS_YET');?>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>
