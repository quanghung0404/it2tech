<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-photo-info class="es-media-info es-photo-info">
    <div data-photo-title class="es-media-title es-photo-title">
        <a data-photo-title-link href="<?php echo $photo->getPermalink();?>"><?php echo $photo->get('title'); ?></a>
    </div>

    <div data-photo-caption class="es-media-caption es-photo-caption">
        <?php echo $this->html('string.truncater', $photo->get('caption'), 250); ?>
        <span data-photoinfo-tag-list-item-group>
        <?php if (!empty($tags)) { ?>
            <br />
            <?php echo JText::_("COM_EASYSOCIAL_PHOTOS_IN_THIS_PHOTO");?>
            -
            <?php $totalTags = count($tags); ?>
            <?php for ($i = 0; $i < $totalTags; $i++) { ?>
                <?php echo $this->includeTemplate('site/photos/info.taglist.item', array('tag' => $tags[$i], 'comma' => ($i !== 0) )); ?>
            <?php } ?>
        <?php } ?>
        </span>
    </div>

    <small>
        <span data-photo-date class="es-photo-date"><?php echo $this->html('string.date', $photo->getAssignedDate() , 'COM_EASYSOCIAL_PHOTOS_DATE_FORMAT', $photo->hasAssignedDate() ? false : true); ?></span>
        <?php $location = $photo->getLocation();
        if (!empty($location)) {
        $address = $location->getAddress();
        if (!empty($address)) { ?>
            <span data-photo-location class="es-photo-location"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_TAKEN_AT"); ?> <u data-popbox="module://easysocial/locations/popbox" data-lat="<?php echo $location->latitude; ?>" data-lng="<?php echo $location->longitude; ?>">
            <a href="//maps.google.com/?q=<?php echo $location->latitude; ?>,<?php echo $location->longitude; ?>" target="_blank"><?php echo $address; ?></a></u></span>.
        <?php } ?>
        <?php } ?>
    </small>
</div>
