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
<div data-photo-menu class="es-media-item-menu es-photo-menu-form">

	<div class="btn-group btn-group-xs">
		<div data-photo-cancel-button class="btn btn-media btn-es">
			<a href="javascript:void(0);"><i class="fa fa-remove"></i> <?php echo JText::_( 'COM_EASYSOCIAL_CANCEL_BUTTON' );?></a>
		</div>

		<div data-photo-done-button class="btn btn-media btn-es-primary">
			<a href="<?php echo $photo->getPermalink();?>" title="<?php echo $this->html( 'string.escape' , $photo->get( 'title' ) );?>"><i class="fa fa-check"></i> <?php echo JText::_( 'COM_EASYSOCIAL_DONE_BUTTON' );?></a>
		</div>
	</div>

	<div class="btn-group btn-group-xs">
		<div class="es-media-item-menu-item btn btn-media btn-es dropdown_" data-item-actions-menu>
			<a href="javascript: void(0);" data-bs-toggle="dropdown"><i class="fa fa-angle-down"></i> <span><?php echo JText::_( 'COM_EASYSOCIAL_PHOTOS_EDIT' ); ?></span></a>
			<ul class="dropdown-menu">

				<?php if ( $lib->editable() ){ ?>
				<li data-photo-cover-button>
					<a href="javascript: void(0);"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_SET_AS_ALBUM_COVER"); ?></a>
				</li>
				<?php } ?>

				<?php if( $lib->downloadable() ){ ?>
				<li data-photo-download-button>
					<a href="<?php echo FRoute::photos( array( 'layout' => 'download' , 'id' => $photo->getAlias() ) );?>">
						<?php echo JText::_("COM_EASYSOCIAL_DOWNLOAD_PHOTO"); ?>
					</a>
				</li>
				<?php } ?>

				<li class="divider"></li>

				<?php if( $lib->moveable() ){ ?>
				<li data-photo-move-button>
					<a href="javascript: void(0);"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_MOVE_PHOTO_TO_ANOTHER_ALBUM"); ?></a>
				</li>
				<?php } ?>

				<?php if( $lib->deleteable() ){ ?>
				<li data-photo-delete-button>
					<a href="javascript: void(0);"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_DELETE_PHOTO"); ?></a>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>

	<?php if ( $lib->canRotatePhoto() ){ ?>
	<div class="btn-group btn-group-xs">
		<div class="btn btn-media btn-es" data-photo-rotateLeft-button>
			<a href="javascript: void(0);"><i class="fa fa-rotate-left"></i></a>
		</div>

		<div class="btn btn-media btn-es" data-photo-rotateRight-button>
			<a href="javascript: void(0);"><i class="fa fa-rotate-right"></i></a>
		</div>
	</div>
	<?php } ?>

	<div class="btn-group btn-group-xs pull-left">
		<div data-popup-close-button class="btn btn-es btn-media">
			<a href="javascript: void(0);"><i class="fa fa-remove"></i></a>
		</div>
	</div>
</div>
