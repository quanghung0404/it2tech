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

$showAvatar = $user->getAvatarPhoto() && $this->my->getPrivacy()->validate('photos.view', $user->getAvatarPhoto()->id, SOCIAL_TYPE_PHOTO, $user->id);
?>
<div class="es-profile-header-avatar es-flyout" data-profile-avatar
	<?php if ($showAvatar) { ?>
	data-es-photo-group="album:<?php echo $user->getAvatarPhoto()->album_id;?>"
	<?php } ?>
>
	<a href="<?php echo $user->getAvatarPhoto() ? 'javascript:void(0);' : $user->getPermalink();?>" class="es-avatar"
		<?php if ($showAvatar) { ?>
		data-es-photo="<?php echo $user->getAvatarPhoto()->id;?>"
		<?php } ?>
	>
		<img data-avatar-image src="<?php echo $user->getAvatar( SOCIAL_AVATAR_SQUARE );?>" alt="<?php echo $this->html( 'string.escape' , $user->getName() );?>">
	</a>

	<?php if( $user->id == $this->my->id ){ ?>
	<div class="es-flyout-content">
		<div class="dropdown_ es-avatar-menu" data-avatar-menu>
			<a href="javascript:void(0);"
			   class="es-flyout-button dropdown-toggle_"
			   data-bs-toggle="dropdown"><i class="fa fa-cog"></i><?php echo JText::_( 'COM_EASYSOCIAL_PHOTOS_EDIT_AVATAR' );?></a>
			<ul class="dropdown-menu">

				<li data-avatar-upload-button>
					<a href="javascript:void(0);"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_UPLOAD_AVATAR"); ?></a>
				</li>

				<li data-avatar-select-button>
					<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_PHOTOS_SELECT_AVATAR'); ?></a>
				</li>

				<?php if ($this->config->get('users.avatarWebcam')) { ?>
				<li class="divider"></li>
				<li data-avatar-webcam>
					<a href="javascript:void(0);"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_TAKE_PHOTO"); ?></a>
				</li>
				<?php } ?>

				<?php if ($user->hasAvatar()) { ?>
				<li class="divider"></li>
				<li data-avatar-remove-button>
					<a href="javascript:void(0);">
						<?php echo JText::_("COM_EASYSOCIAL_PHOTOS_REMOVE_AVATAR"); ?>
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<?php } ?>

	<?php echo $this->loadTemplate( 'site/utilities/user.online.state' , array( 'online' => $user->isOnline() ) ); ?>

	<?php echo $this->render( 'module' , 'es-profile-avatar' ); ?>
</div>
