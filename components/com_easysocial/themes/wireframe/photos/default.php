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
<?php if( !isset( $heading ) ){ ?>
	<?php echo $lib->heading(); ?>
<?php } ?>

<div
	data-photo-browser="<?php echo $uuid; ?>"
	data-album-id="<?php echo $album->id; ?>"
	class="es-container es-photo-browser es-media-browser">

	<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
		<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
	</a>

	<div data-photo-browser-sidebar class="es-sidebar" data-sidebar>

		<?php echo $this->render( 'module' , 'es-photos-sidebar-top' ); ?>

		<div class="es-widget es-widget-borderless">
			<div class="es-widget-head">
				<div
					data-photo-back-button
					class="btn btn-es-primary btn-media">
					<a
						data-photo-back-button-link
						href="<?php echo $lib->getAlbumLink(); ?>"><i class="fa fa-arrow-left"></i> <?php echo JText::_('COM_EASYSOCIAL_PHOTOS_BACK_TO_ALBUM'); ?></a>
				</div>
			</div>

			<div class="es-widget-body">
				<ul data-photo-list-item-group
					class="fd-nav fd-nav-stacked fd-nav-thumbs">
						<li class="es-thumb grid-sizer"><a></a></li>
					<?php foreach($photos as $photo) { ?>
						<li class="es-thumb<?php echo $photo->id == $id ? ' active' : '';?><?php echo $photo->isFeatured() ? ' featured' : '';?>"
							data-photo-list-item
							data-photo-id="<?php echo $photo->id; ?>"
						>
							<a href="<?php echo $photo->getPermalink();?>" title="<?php echo $this->html( 'string.escape' , $photo->title ); ?>">
								<i data-photo-list-item-image style="background-image: url(<?php echo $photo->getSource('square'); ?>);"></i>
								<img data-photo-list-item-cover src="<?php echo $photo->getSource('square'); ?>" />
								<span data-photo-list-item-title><?php echo $photo->title; ?></span>
							</a>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>

		<?php echo $this->render( 'module' , 'es-photos-sidebar-bottom' ); ?>
	</div>
	<div data-photo-browser-content class="es-content">
		<?php echo $this->render( 'module' , 'es-photos-before-contents' ); ?>
		<?php echo $content; ?>
		<?php echo $this->render( 'module' , 'es-photos-after-contents' ); ?>
	</div>

	<i class="loading-indicator fd-small"></i>
</div>
