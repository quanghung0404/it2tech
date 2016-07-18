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
<div data-album-info class="es-media-info es-album-info">

	<div data-album-title class="es-media-title es-album-title">
		<a href="<?php echo $album->getPermalink();?>"><?php echo $album->get('title'); ?></a>
	</div>

	<?php if( $options[ 'view' ] != 'all' ){ ?>
	<div data-album-caption class="es-media-caption es-album-caption">
		<?php echo $this->html( 'string.truncater' , $album->get( 'caption' ) , 250 ); ?>
	</div>
	<?php } ?>

	<small>
		<?php if( $options[ 'view' ] == 'all' ){ ?>
			<span class="es-album-author mr-5"><i class="fa fa-user"></i> <?php echo JText::sprintf('COM_EASYSOCIAL_ALBUMS_CREATED_BY' , $this->html( 'html.user' , $album->user_id , true ) ); ?></span>
		<?php } ?>

		<?php if ($album->hasDate()) { ?>
			<span data-album-date class="es-album-date"><i class="fa fa-calendar"></i> <?php echo $this->html( 'string.date' , $album->getAssignedDate() , "COM_EASYSOCIAL_ALBUMS_DATE_FORMAT",  $album->hasAssignedDate() ? false : true); ?></span>
			<?php
				$location = $album->getLocation();
				if ($location) {
			?>
				<span data-album-location class="es-album-location"><?php echo JText::_("COM_EASYSOCIAL_ALBUMS_TAKEN_AT"); ?> <u data-popbox="module://easysocial/locations/popbox" data-lat="<?php echo $location->latitude; ?>" data-lng="<?php echo $location->longitude; ?>"><a href="//maps.google.com/?q=<?php echo $location->latitude; ?>,<?php echo $location->longitude; ?>" target="_blank"><?php echo $location->getAddress(); ?></a></u></span>
			<?php } ?>
		<?php } ?>
	</small>

	<i data-album-cover class="es-album-cover" <?php if ($album->hasCover()) { ?>style="background-image: url(<?php echo $album->getCover( 'large' ); ?>);"<?php } ?>><b></b><b></b></i>

</div>
