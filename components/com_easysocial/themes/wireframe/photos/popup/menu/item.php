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
<div data-photo-menu class="es-media-item-menu es-photo-menu-item">

	<div class="btn-group btn-group-sm">
		<?php if( $lib->featureable() ){ ?>
		<div data-photo-feature-button class="btn btn-es btn-es-featured btn-default<?php echo $photo->featured ? ' btn-es-primary' : '';?>">
			<a href="javascript: void(0);"><i class="fa fa-star"></i></a>
		</div>
		<?php } ?>
		<div class="es-media-item-menu-item btn btn-default btn-es dropdown_" data-item-actions-menu>
			<a href="javascript: void(0);" data-bs-toggle="dropdown">
				<i class="fa fa-bars"></i>
			</a>
			<ul class="dropdown-menu">

				<?php if( $lib->editable() ){ ?>
				<li data-photo-edit-button>
					<a href="<?php echo $photo->getEditPermalink();?>" title="<?php echo $lib->getPageTitle('item');?>"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_EDIT_PHOTO"); ?></a>
				</li>
				<?php } ?>

				<?php if( $lib->shareable() ){ ?>
				<li data-photo-share-button>
					<?php echo FD::get( 'Sharing' , array( 'url' => $photo->getPermalink( true , true ) , 'text' => JText::_( 'COM_EASYSOCIAL_SHARE_PHOTO' ) ) )->getHTML(); ?>
				</li>
				<li class="divider"></li>
				<?php } ?>

				<?php if ($this->config->get('photos.original')) { ?>
				<li data-photo-original-button>
					<a href="<?php echo $photo->getSource( 'original' );?>" target="_blank">
						<?php echo JText::_( 'COM_EASYSOCIAL_PHOTOS_VIEW_ORIGINAL' );?>
					</a>
				</li>
				<?php } ?>

				<?php if( $lib->downloadable() ){ ?>
				<li data-photo-download-button>
					<a href="<?php echo FRoute::photos( array( 'id' => $photo->getAlias() , 'layout' => 'download' ) );?>">
						<?php echo JText::_("COM_EASYSOCIAL_DOWNLOAD_PHOTO"); ?>
					</a>
				</li>
				<?php } ?>

				<?php if( $lib->canSetProfilePicture() ){ ?>
				<li data-photo-profileAvatar-button>
					<a href="javascript:void(0);">
						<?php echo JText::_("COM_EASYSOCIAL_USE_AS_PROFILE_AVATAR"); ?>
					</a>
				</li>
				<?php } ?>

				<?php if( $lib->canSetProfileCover() ){ ?>
				<li data-photo-profileCover-button>
					<a href="<?php echo FRoute::profile( array( 'id' => $this->my->getAlias() , 'cover_id' => $photo->id ) );?>">
						<?php echo JText::_( 'COM_EASYSOCIAL_USE_AS_PROFILE_COVER' ); ?>
					</a>
				</li>
				<li class="divider"></li>
				<?php } ?>


				<?php if ( $lib->albumLib->editable() ){ ?>
				<li data-photo-cover-button>
					<a href="javascript: void(0);"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_SET_AS_ALBUM_COVER"); ?></a>
				</li>
				<?php } ?>

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

	<?php if ($options['showTags']) { ?>
		<?php echo $this->includeTemplate('site/photos/popup/taglist'); ?>
	<?php } ?>

	<div class="btn-group btn-group-sm">
		<?php if( $lib->shareable() ){ ?>
		<div class="btn btn-default btn-es" data-photo-share-button>
			<?php echo FD::get( 'Sharing' , array( 'url' => $photo->getPermalink( true , true ) , 'text' => JText::_( 'COM_EASYSOCIAL_PHOTOS_SHARE' ) ) )->getHTML(true); ?>
		</div>
		<?php } ?>

		<div class="btn btn-default btn-es" data-photo-report-button>
			<?php echo FD::reports()->getForm( 'com_easysocial' , SOCIAL_TYPE_PHOTO , $photo->id , $photo->get( 'title' ) , JText::_( 'COM_EASYSOCIAL_PHOTOS_REPORT' ) , JText::_( 'COM_EASYSOCIAL_PHOTOS_REPORT_PHOTO_TITLE' ) , JText::_( 'COM_EASYSOCIAL_PHOTOS_REPORT_DESC' ) , $photo->getPermalink(true, true), true ); ?>
		</div>
	</div>

	<div class="btn-group btn-group-sm pull-left">
		<div data-popup-close-button class="btn btn-es btn-media">
			<a href="javascript: void(0);"><i class="fa fa-remove"></i></a>
		</div>
	</div>
</div>
