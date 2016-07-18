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
<div id="fd" class="es mod-es-group-menu module-menu<?php echo $suffix;?>">

	<div class="es-user">
		<div class="media">
			<?php if( $params->get( 'show_avatar' , true ) ){ ?>
			<div class="media-object pull-left">
				<div class="es-avatar pull-left">
					<img src="<?php echo $group->getAvatar( SOCIAL_AVATAR_MEDIUM );?>" alt="<?php echo $modules->html( 'string.escape' , $group->getName() );?>" />
				</div>
			</div>
			<?php } ?>

			<div class="media-body">
				<div class="user-info">
					<?php if( $params->get( 'show_name' , true ) ){ ?>
					<div class="user-name">
						<a href="<?php echo $group->getPermalink();?>" class="user-name-link"><?php echo $group->getName();?></a>
					</div>
					<?php } ?>

					<?php if( $params->get( 'show_members' , true ) ){ ?>
					<div class="user-points">
						<div>
							<?php echo JText::sprintf( '%1$s members' , $group->getTotalMembers() ); ?>
						</div>
					</div>
					<?php } ?>

					<?php if( $params->get( 'show_edit' , true ) ){ ?>
					<div class="user-edit">
						<a href="<?php echo $group->getEditPermalink();?>"><i class="fa fa-pencil"></i></a>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<?php if( $params->get( 'show_pending' , true ) && $pending ){ ?>
	<div class="es-group-menu-pending">
		<div class="es-title"><?php echo JText::_( 'MOD_EASYSOCIAL_GROUP_MENU_PENDING_MEMBERS' );?></div>

		<ul class="fd-reset-list">
			<?php foreach( $pending as $user ){ ?>
			<li>
				<div class="media">
					<div class="media-object pull-left">
						<img src="<?php echo $user->getAvatar();?>" class="es-avatar es-avatar-sm" />
					</div>
					<div class="media-body">
						<div>
							<a href="<?php echo $user->getPermalink();?>" data-popbox="module://easysocial/profile/popbox" data-popbox-position="top-left" data-user-id="<?php echo $user->id;?>"><?php echo $user->getName();?></a>
						</div>
						<div>
							<a href="javascript:void(0);" data-group-menu-approve data-group-id="<?php echo $group->id;?>" data-id="<?php echo $user->id;?>" class="btn btn-mini btn-es-primary"><?php echo JText::_( 'MOD_EASYSOCIAL_GROUP_MENU_APPROVE' ); ?></a>

							<a href="javascript:void(0);" data-group-menu-reject data-group-id="<?php echo $group->id;?>" data-id="<?php echo $user->id;?>" class="btn btn-mini btn-es-danger"><?php echo JText::_( 'MOD_EASYSOCIAL_GROUP_MENU_REJECT' ); ?></a>
						</div>
					</div>
				</div>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>

	<?php if( $params->get( 'show_apps' , true ) ){ ?>
	<div class="es-group-menu-apps">

		<div class="es-title mt-10"><?php echo JText::_( 'MOD_EASYSOCIAL_GROUP_MENU_APPLICATIONS' );?></div>

		<ul class="es-menu-list">
			<?php foreach( $apps as $application ){ ?>
			<li>
				<a href="<?php echo FRoute::groups( array( 'layout' => 'item' , 'id' => $group->getAlias() , 'appId' => $application->getAlias() ) );?>">
					<img src="<?php echo $application->getIcon();?>" width="16" class="mr-5" />
					<?php echo $application->get( 'title' ); ?>
				</a>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>

</div>
