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
<div class="es-album-photos">

	<div class="es-photo-item-group no-transition" data-photo-item-group>

		<div class="es-media-item es-photo-item layout-item grid-sizer">
			<div><div></div></div>
		</div>

		<?php if (!empty($photos)) { ?>
			<?php foreach ($photos as $photo) { ?>
				<?php echo FD::photo($photo->uid, $photo->type, $photo)->renderItem($options['photoItem']); ?>
			<?php } ?>
		<?php } ?>
	</div>

	<div class="no-photos-hint content-hint">
		<?php echo JText::_("COM_EASYSOCIAL_NO_PHOTOS_AVAILABLE"); ?>
	</div>

	<div class="drop-photo-hint content-hint">
		<?php echo JText::_("COM_EASYSOCIAL_DROP_A_FILE_TO_UPLOAD"); ?>
	</div>

	<?php if ($options['showLoadMore']) { ?>
		<?php if (isset($nextStart) && $nextStart >= 0) { ?>
			<button data-album-more-button type="button" class="btn btn-block es-album-more-button">
				<i class="loading-indicator"></i>
				<span class="loadmore-text">
					<i class="fa fa-refresh "></i> <?php echo JText::_("COM_EASYSOCIAL_LOAD_MORE"); ?>
				</span>
			</button>
		<?php } ?>
	<?php } ?>

	<?php if ($options['showViewButton']) { ?>
	<a data-album-view-button class="btn btn-sm btn-es-primary es-album-view-button" href="<?php echo $album->getPermalink(); ?>">
		<?php echo JText::_('COM_EASYSOCIAL_ALBUMS_VIEW_ALBUM'); ?> <i class="fa fa-chevron-right ml-5"></i>
	</a>
	<?php } ?>
</div>
