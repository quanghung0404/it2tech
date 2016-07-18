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
<div class="es-album-form">

	<input
		data-album-title-field
		class="es-album-title-field"
		type="text"
		value="<?php echo $this->html('string.escape', $album->title); ?>"
		placeholder="<?php echo JText::_("COM_EASYSOCIAL_ALBUMS_ENTER_ALBUM_TITLE"); ?>"
		autocomplete="off"
	/>

	<textarea
		data-album-caption-field
		class="es-album-caption-field"
		placeholder="<?php echo JText::_("COM_EASYSOCIAL_ALBUMS_ENTER_ALBUM_DESCRIPTION"); ?>"
	><?php echo $this->html('string.escape', $album->caption); ?></textarea>

	<div
		data-album-cover-field
		<?php if ($album->hasCover()) { ?>
		class="es-album-cover-field"
		style="background-image: url(<?php echo $album->getCover( 'thumbnail' ); ?>);"
		<?php } else { ?>
		class="es-album-cover-field no-cover"
		<?php } ?>
		>
		<i class="fa fa-image"></i>
	</div>

	<div class="sentence">
		<div data-album-location class="es-album-location words">
			<i class="fa fa-map-marker"></i>
			<span
				data-album-location-caption
				data-bs-toggle="dropdown"
				class="with-data <?php if ($album->getLocation()) { echo 'has-data'; }?>">
				<?php if ($album->getLocation()) { ?>
					<?php echo $album->getLocation()->getAddress(); ?>
				<?php } ?>
			</span>
			<span data-album-addLocation-button
				  data-bs-toggle="dropdown"
			      class="without-data">
			    <?php echo JText::_("COM_EASYSOCIAL_ADD_LOCATION"); ?>
			</span>
			<div data-album-location-form
			     class="es-album-location-form dropdown-menu dropdown-static dropdown-arrow-topleft">
				<?php echo $this->html( 'grid.location', $album->getLocation() ); ?>
			</div>
		</div>

		<?php if ($album->hasDate()) { ?>
		<div data-album-date class="es-album-date words has-data">
			<i class="fa fa-clock-o"></i>
			<span data-album-date-caption
			      data-bs-toggle="dropdown"
			      class="with-data">
				<?php echo $this->html('string.date', $album->getCreationDate() , "COM_EASYSOCIAL_ALBUMS_DATE_FORMAT", $album->hasAssignedDate() ? false : true); ?>
			</span>
			<span data-album-addDate-button
			      data-bs-toggle="dropdown"
			      class="without-data">
			    <?php echo JText::_("COM_EASYSOCIAL_ADD_DATE"); ?>
			</span>
			<div class="es-album-date-form dropdown-menu dropdown-static dropdown-arrow-topleft">
				<?php echo $this->html( 'grid.dateform' , 'date-form', $album->getAssignedDate(), '', '', $album->hasAssignedDate() ? false : true); ?>
			</div>
		</div>
		<?php } ?>

		<?php
			$isHtml = ( $album->id ) ? false : true;
			echo $privacy->form( $album->id, SOCIAL_TYPE_ALBUM, $album->uid, 'albums.view', $isHtml );
		?>

	</div>
</div>
