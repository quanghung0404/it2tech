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
<div id="fd" class="es mod-es-groups module-register<?php echo $suffix;?> es-responsive">

	<ul class="es-groups-list fd-reset-list">
		<?php foreach ($groups as $group) { ?>
		<li>
			<?php if ($params->get('display_avatar', true)) { ?>
			<div class="es-group-avatar es-avatar es-avatar-sm es-avatar-border-sm">
				<img src="<?php echo $group->getAvatar();?>" alt="<?php echo $modules->html('string.escape', $group->getName());?>" />
			</div>
			<?php } ?>

			<div class="es-group-object">
				<a href="<?php echo $group->getPermalink();?>" class="group-title"><?php echo $group->getName();?></a>
			</div>

			<div class="es-group-meta">
				<?php if( $params->get( 'display_category' , true ) ){ ?>
				<span>
					<a href="<?php echo FRoute::groups( array( 'layout' => 'category' , 'id' => $group->getCategory()->getAlias() ) );?>" alt="<?php echo $modules->html( 'string.escape' , $group->getCategory()->get( 'title' ) );?>" class="group-category">
						<i class="fa fa-database"></i> <?php echo $modules->html( 'string.escape' , $group->getCategory()->get( 'title' ) );?>
					</a>
				</span>
				<?php } ?>

				<?php if($params->get('display_member_counter', true)){ ?>
				<span class="hit-counter">
					<i class="fa fa-users"></i> <?php echo JText::sprintf(ES::string()->computeNoun('MOD_EASYSOCIAL_GROUPS_MEMBERS_COUNT', $group->getTotalMembers()), $group->getTotalMembers()); ?>
				</span>
				<?php } ?>
			</div>

			<?php if ($params->get('display_actions', true) && !$group->isMember()) { ?>
			<div class="es-group-actions">
				<a href="javascript:void(0);" class="btn btn-es-primary	btn-sm" data-es-groups-join data-id="<?php echo $group->id;?>"><?php echo JText::_('MOD_EASYSOCIAL_GROUPS_JOIN_GROUP');?></a>
			</div>
			<?php } ?>
		</li>
		<?php } ?>
	</ul>

</div>
