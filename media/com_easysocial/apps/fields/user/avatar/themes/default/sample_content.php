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
<ul class="input-vertical list-unstyled">
	<li data-avatar-upload <?php if( !$params->get( 'upload' ) ) { ?>style="display: none;"<?php } ?>>
		<input type="file" id="<?php echo $inputName; ?>" class="input" name="<?php echo $inputName; ?>" />
	</li>

	<li class="mt-20" <?php if( !$params->get( 'gallery' ) ) { ?>style="display: none;"<?php } ?> data-avatar-gallery>
		<a class="mls btn btn-es-inverse" href="javascript:void(0);" <?php if( !$params->get( 'use_gallery_button' ) ) { ?>style="display: none;"<?php } ?> data-avatar-gallery-button><i class="icon-es-photos mr-5"></i> <?php echo JText::_( 'PLG_FIELDS_AVATAR_SELECT_AVATAR_BUTTON' ); ?></a>
	</li>

	<li <?php if( !$params->get( 'gallery' ) ) { ?>style="display: none;"<?php } ?> data-avatar-gallery>
		<h3 <?php if( $params->get( 'use_gallery_button' ) ) { ?>style="display: none;"<?php } ?> data-avatar-gallery-title><?php echo JText::_( 'PLG_FIELDS_AVATAR_GALLERY_SELECTION' ); ?></h3>
	</li>

	<li <?php if( !$params->get( 'gallery' ) ) { ?>style="display: none;"<?php } ?> data-avatar-gallery>
		<ul class="es-avatar-list list-unstyled" <?php if( $params->get( 'use_gallery_button' ) ) { ?>style="display: none;"<?php } ?> data-avatar-gallery-selection>
		<?php foreach( $avatars as $avatar ){ ?>
		<li class="avatarItem" data-field-avatar-gallery-item data-id="<?php echo $avatar->id;?>">
			<a class="es-avatar es-avatar-md pull-left mr-10" href="javascript:void(0);">
				<img src="<?php echo $avatar->getSource( SOCIAL_AVATAR_MEDIUM );?>" />
			</a>
		</li>
		<?php } ?>
		</ul>
	</li>
</ul>
