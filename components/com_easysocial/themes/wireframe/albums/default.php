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
<?php echo $lib->heading(); ?>

<div class="es-container es-media-browser layout-album"
	data-layout="album"
	data-album-browser="<?php echo $uuid; ?>">

	<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
		<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
	</a>

	<div data-album-browser-sidebar class="es-sidebar" data-sidebar>

		<?php echo $this->render( 'module' , 'es-albums-sidebar-top' ); ?>
		<div class="es-widget">
			<?php if ($lib->canCreateAlbums()) { ?>
				<div class="es-widget-create mr-10">
					<a href="<?php echo $lib->getCreateLink();?>" class="btn btn-sm btn-es-primary btn-block"><?php echo JText::_("COM_EASYSOCIAL_ALBUMS_CREATE_ALBUM"); ?></a>
				</div>
				<hr class="es-hr mt-15 mb-10" />
			<?php } ?>

			<ul class="es-widget-filter fd-reset-list" data-album-list-item-group="core">
				<li data-album-all-button>
					<a href="<?php echo $lib->getViewAlbumsLink();?>"><?php echo JText::_("COM_EASYSOCIAL_ALBUMS_VIEW_ALL_ALBUMS"); ?></a>
				</li>
				<?php if( $coreAlbums ){ ?>
					<?php foreach( $coreAlbums as $album ){ ?>
						<li data-album-list-item data-album-id="<?php echo $album->id; ?>" class="filter-item<?php if ($album->id==$id) { ?> active<?php } ?>">
							<a href="<?php echo $album->getPermalink();?>" title="<?php echo $album->get('title'); ?>">
								<!-- <i data-album-list-item-cover style="background-image: url(<?php echo $album->getCover(); ?>);"></i>  -->
								<span data-album-list-item-title><?php echo $album->get( 'title' ); ?></span> 
								<b data-album-list-item-count><?php echo $album->getTotalPhotos(); ?></b>
							</a>
						</li>
					<?php } ?>
				<?php } ?>
			</ul>

			<ul class="es-widget-filter fd-reset-list" data-album-list-item-group="regular">
				<?php if ( $layout=="form" && empty($id) ) { ?>
					<li data-album-list-item class="active new">
						<a href="javascript: void(0);"><i data-album-list-item-cover></i> <span data-album-list-item-title><?php echo JText::_('COM_EASYSOCIAL_ALBUMS_NEW_ALBUM'); ?></span> <b data-album-list-item-count>0</b></a>
					</li>
				<?php } ?>
				<?php if( $albums ){ ?>
					<?php foreach( $albums as $album ){ ?>
					<li data-album-list-item
					    data-album-id="<?php echo $album->id; ?>"
					    class="<?php if ($album->id==$id) { ?>active<?php } ?>">
						<a href="<?php echo $album->getPermalink(); ?>" title="<?php echo $this->html( 'string.escape' , $album->get('title') ); ?>"><i data-album-list-item-cover style="background-image: url(<?php echo $album->getCover(); ?>);"></i> <span data-album-list-item-title><?php echo $album->get( 'title' ); ?></span> <b data-album-list-item-count><?php echo $album->getTotalPhotos(); ?></b></a>
					</li>
					<?php } ?>
				<?php } ?>
			</ul>

			<?php echo $this->render( 'module' , 'es-albums-sidebar-bottom' ); ?>
		</div>
	</div>

	<div data-album-browser-content class="es-content">
		<?php echo $this->render( 'module' , 'es-albums-before-contents' ); ?>
		<?php echo $content; ?>
		<?php echo $this->render( 'module' , 'es-albums-after-contents' ); ?>
	</div>

	<i class="loading-indicator fd-small"></i>
</div>
