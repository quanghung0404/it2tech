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
<div class="media">
	<div class="media-object pull-left">
		<div class="es-avatar es-avatar-fd-reset-list es-inset"><img src="<?php echo $creator->getAvatar(); ?>" /></div>
	</div>
	<div class="media-body">
		<div><a href="<?php echo $creator->getPermalink(); ?>"><?php echo $creator->getName(); ?></a></div>
		<?php // This is from the time it was uploaded.
		      // TODO: Also show assigned date somewhere ?>
		<div data-photo-date class="es-photo-date small"><?php echo FD::date( $photo->created )->toLapsed(); ?></div>
		<?php if( $photo->getLocation() ) { ?>
		<div data-photo-location class="es-photo-location small">
			<i class="fa fa-map-marker "></i> <?php echo $photo->getLocation()->get( 'address' ); ?>
		</div>
		<?php } ?>
	</div>
</div>

<div data-photo-title class="es-photo-title"><?php echo $photo->title; ?></div>
<div data-photo-caption class="es-photo-caption"><?php echo $photo->caption; ?></div>
<div data-photo-album
	 class="es-photo-album">
	 <?php echo JText::_( 'COM_EASYSOCIAL_FROM_ALBUM' );?> <a href="<?php echo $album->getPermalink(); ?>"><?php echo $album->get( 'title' ); ?></a>
</div>

<div class="es-item-action-buttons">
	<span data-photo-like-button class="btn-like<?php echo FD::likes()->hasLiked( $photo->id , SOCIAL_TYPE_PHOTO, 'upload', SOCIAL_APPS_GROUP_USER ) ? ' liked' : '';?>">
		<span class="like-text"><?php echo JText::_("COM_EASYSOCIAL_LIKES_LIKE"); ?></span>
		<span class="unlike-text"><?php echo JText::_("COM_EASYSOCIAL_LIKES_UNLIKE"); ?></span>
	</span>
	<b>&bull;</b>
	<span data-photo-comment-button class="btn-comment" data-bs-toggle="dropdown"><?php echo JText::_("COM_EASYSOCIAL_COMMENT"); ?></span>
</div>
<div data-photo-likes-holder class="es-item-likes">
	<?php echo FD::likes( $photo->id , SOCIAL_TYPE_PHOTO, 'upload', SOCIAL_APPS_GROUP_USER )->toString(); ?>
</div>
