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
<div id="fd" class="es mod-es-users module-users<?php echo $suffix;?>">
	<div class="mod-bd">
		<div class="es-widget">
			<?php if( $users ){ ?>
			<ul class="widget-list-grid">
				<?php foreach( $users as $user ){ ?>
				<li>
					<a href="<?php echo $user->getPermalink();?>" class="es-avatar">
						<img src="<?php echo $user->getAvatar();?>" data-popbox-toggle="hover" <?php echo $params->get( 'popover' , true ) ? ' data-popbox="module://easysocial/profile/popbox" data-popbox-position="' . $params->get( 'popover_position' , 'top-left' ) . '" data-user-id="' . $user->id . '"' : '';?> />
					</a>
				</li>
				<?php } ?>
			</ul>

			<?php } else { ?>
			<div class="empty fd-small">
				<?php echo JText::_( 'MOD_EASYSOCIAL_USERS_NO_USERS_CURRENTLY' ); ?>
			</div>
			<?php } ?>

			<?php if( $params->get( 'showall_link' , true ) ){ ?>
			<div class="mod-small fd-small">
				<a href="<?php echo FRoute::users();?>"><?php echo JText::_( 'MOD_EASYSOCIAL_USERS_VIEW_ALL_USERS' ); ?></a>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
