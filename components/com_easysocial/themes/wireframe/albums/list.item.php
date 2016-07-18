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
<div data-album data-album-id="<?php echo $album->id; ?>" class="es-album es-media-item">

	<div class="es-album-header">
		<div data-album-title class="es-album-title">
			<a data-album-link href="<?php echo FRoute::albums( array( 'id' => $album->getAlias() ) );?>">
				<?php if( $album->title ){ ?>
					<?php echo $album->get( 'title' ); ?>
				<?php } else { ?>
					<?php echo JText::_('COM_EASYSOCIAL_ALBUMS_UNTITLED_ALBUM'); ?>
				<?php } ?>
			</a>
		</div>

		<div data-album-count class="es-album-count"><?php echo JText::sprintf("COM_EASYSOCIAL_ALBUMS_COUNT", $album->getTotalPhotos() ); ?></div>

		<div data-album-privacy><?php echo FD::privacy()->form( $album->id , SOCIAL_TYPE_ALBUM , $album->uid, 'albums.view' );?></div>
	</div>

	<div class="es-album-content">

		<div data-album-cover class="es-album-cover <?php echo ($album->hasCover()) ? '' : 'no-cover'; ?>">
				<i data-album-cover-image class="es-album-image"
					 <?php if( $album->hasCover() ) { ?>
				     style="background-image: url('<?php echo $album->getCover( 'thumbnail' ); ?>');"
				     <?php } ?>
				>
				</i>
		</div>

		<div data-album-menu class="es-media-item-menu btn-group show-on-hover">
			<div class="es-media-item-menu-item btn btn-media dropdown_" data-item-actions-menu>
				<a href="javascript:void(0);" data-bs-toggle="dropdown" class="dropdown-toggle_"><i class="fa fa-angle-down"></i></a>
				<ul class="dropdown-menu">
					<li>
						<?php echo FD::get( 'Sharing' , array( 'url' => FRoute::albums( array( 'id' => $album->getAlias() , 'layout' => 'item' ) ) , 'text' => JText::_( 'COM_EASYSOCIAL_SHARE_ALBUM' ) ) )->getHTML(); ?>
					</li>
					<li data-album-follow-button>
						<a href="javascript:void(0);"><?php echo JText::_("COM_EASYSOCIAL_FOLLOW_ALBUM"); ?></a>
					</li>
					<li class="divider"></li>
					<li data-album-report-button>
						<?php echo FD::reports()->getForm( 'com_easysocial' , SOCIAL_TYPE_ALBUM , $album->id , $album->get( 'title' ) , JText::_( 'COM_EASYSOCIAL_REPORT_ALBUM' ) , $album->getPermalink( true , true ) , JText::_( 'COM_EASYSOCIAL_REPORT_ALBUM_DESC' ) ); ?>
					</li>

					<?php if( $album->editable() || $album->deleteable() ){ ?>
					<li class="divider"></li>
					<?php } ?>

					<?php if( $album->editable() ){ ?>
					<li data-album-report-button>
						<a href="<?php echo FRoute::albums( array( 'id' => $album->getAlias() , 'layout' => 'form' ) );?>"><?php echo JText::_( 'COM_EASYSOCIAL_EDIT_ALBUM' ); ?></a>
					</li>
					<?php } ?>

					<?php if( $album->deleteable() ){ ?>
					<li data-album-delete-button>
						<a href="javascript:void(0);"><?php echo JText::_("COM_EASYSOCIAL_DELETE_ALBUM"); ?></a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>

		<div data-item-actions class="es-item-actions">
			<span data-album-like-button class="btn-like<?php echo FD::likes()->hasLiked( $album->id , SOCIAL_TYPE_ALBUM, 'create', SOCIAL_APPS_GROUP_USER ) ? ' liked' : '';?>">
				<span class="like-text"><?php echo JText::_("COM_EASYSOCIAL_LIKES_LIKE"); ?></span>
				<span class="unlike-text"><?php echo JText::_("COM_EASYSOCIAL_LIKES_UNLIKE"); ?></span>
			</span>
			<b>&bull;</b>
			<span data-album-comment-button class="btn-comment" data-bs-toggle="dropdown"><?php echo JText::_("COM_EASYSOCIAL_COMMENT"); ?></span>
			<span data-album-counts-button class="btn-counts" data-bs-toggle="dropdown">
				<span>
					<i class="fa fa-heart"></i>
					<span data-album-like-count><?php echo $album->getLikesCount();?></span>
				</span>
				<span>
					<i class="fa fa-comments"></i>
					<span data-album-comment-count><?php echo $album->getCommentsCount();?></span>
				</span>
			</span>
			<div data-item-action-content class="es-item-action-content dropdown-menu dropdown-static dropdown-arrow-topcenter scrollbar-wrap loading">
				<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
				<div class="viewport">
					<div class="overview">
						<i class="loading-indicator fd-small"></i>
						<div data-album-response-holder></div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
