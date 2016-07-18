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
<div data-albums class="es-albums<?php echo (empty($albums)) ? '' : ' has-albums'; ?> is-<?php echo $lib->type;?>">

	<?php echo $this->render( 'module' , 'es-albums-between-months' ); ?>

	<?php if( !empty( $data ) ){ ?>
		<?php foreach( $data as $groupDate => $albums ) { ?>
			<div class="es-snackbar"><i class="fa fa-calendar mr-5"></i> <?php echo $groupDate;?></div>
			<?php foreach( $albums as $album ){ ?>

				<?php echo $lib->renderItem( array(
					'layout' => 'row',
					'album'	=> $album,
					'limit' => 6,
					'canUpload'    => false,
					'showToolbar'  => false,
					'showInfo'     => true,
					'showStats'    => true,
					'showPhotos'   => true,
					'showResponse' => false,
					'showTags'     => true,
					'showForm'     => false,
					'showLoadMore' => false,
					'showViewButton' => true,
					'photoItem'    => array(
						'showForm' => false,
						'showInfo' => false,
						'showStats' => false,
						'showToolbar' => false,
						'openInPopup' => $this->config->get('photos.popup.default')
					)
				)); ?>
			<?php } ?>
		<?php } ?>

		<?php if (isset($pagination)) { ?>
		<div class="mt-20 text-center es-pagination">
			<?php echo $pagination->getListFooter( 'site' );?>
		</div>
		<?php } ?>

	<?php } ?>

	<?php if( !$data ){ ?>
	<div class="content-hint no-albums-hint">
		<?php echo JText::_("COM_EASYSOCIAL_NO_ALBUM_AVAILABLE"); ?>

		<?php if( $lib->canCreateAlbums() ){ ?>
		<div>
			<a class="btn btn-es-primary btn-large" href="<?php echo FRoute::albums( array( 'layout' => 'form' , 'uid' => $lib->uid , 'type' => $lib->type ) );?>"><?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_CREATE_ALBUM'); ?></a>
		</div>
		<?php } ?>
	</div>
	<?php } ?>

</div>
