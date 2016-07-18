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
<?php if( $notifications ){ ?>
	<?php foreach( $notifications as $notification ){ ?>
	<li class="type-<?php echo $notification->type;?> is-unread">

		<div class="media notice-message">
			<a href="<?php echo FRoute::notifications( array( 'id' => $notification->id , 'layout' => 'route' ) );?>">
				<div class="media-object pull-left">
					<div class="es-avatar">
						<img src="<?php echo $notification->user->getAvatar();?>" title="<?php echo $this->html( 'string.escape' , $notification->user->getName() );?>" />
					</div>
				</div>
				<div class="media-body">

					<?php if( $notification->image ){ ?>
					<span class="pull-right object-image">
						<span style="background-image: url('<?php echo $notification->image;?>');"></span>
					</span>
					<?php } ?>

					<div class="object-info">
						<div class="object-title">
							<?php echo $notification->title; ?>
						</div>

						<?php if( $notification->content ){ ?>
						<div class="object-content">
							<?php echo $this->html( 'string.escape' , $notification->content ); ?>
						</div>
						<?php } ?>

						<div class="object-timestamp mt-5">
							<?php if( $notification->icon ){ ?>
							<i class="icon-es-games icon-tb-notice pull-left"></i>
							<?php } else { ?>
							<i class="ies-earth ies-small"></i>
							<?php } ?>
							<small><?php echo $notification->since; ?></small>
						</div>
					</div>

				</div>
			</a>
		</div>

	</li>
	<?php } ?>
<?php } else { ?>
	<li class="requestItem empty center">
		<div class="mt-20 pt-20 small">
			<i class="ies-info ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_NOTIFICATIONS_NO_NOTIFICATIONS_CURRENTLY' ); ?>
		</div>
	</li>
<?php } ?>
